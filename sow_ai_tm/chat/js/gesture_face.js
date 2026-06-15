/**
 * gesture_face.js  $2014 表情認識 → Aurora Clock LED制御
 *
 * gesture_tenso.js と同じ操作フロー・DOM ID 規則に従う。
 *
 * 【重要】イベント登録は faceGestureDisplay() が呼ばれたタイミングで行う。
 *         スクリプト読み込み時点では DOM が存在しない場合があるため。
 *
 * 依存（index.php / ipad.php の feature 9 ブロックで読み込む）:
 *   <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.17.0"></script>
 *   ※ MediaPipe は face_loadLandmarkerIfNeeded() 内で動的 import する
 */

'use strict';

// ================================================================
// 設定値
// ================================================================
const FACE_MAX_CLASSES      = 5;
const FACE_MIN_SAMPLES      = 3;
const FACE_EPOCHS           = 40;
const FACE_BATCH_SIZE       = 16;
const FACE_PREDICT_INTERVAL = 300;   // ms
const FACE_BLEND_DIM        = 52;    // MediaPipe blendShapes 次元数

// ================================================================
// 状態変数
// ================================================================
let face_classData     = {};
let face_landmarker    = null;
let face_classifier    = null;
let face_classLabels   = [];
let face_isModelReady  = false;
let face_isTraining    = false;
let face_isRecognizing = false;
let face_predictTimer  = null;
let face_animFrame     = null;
let face_uiInited      = false;   // イベント登録済みフラグ

// ================================================================
// DOM 取得ヘルパー（毎回 getElementById で取得）
// ================================================================
function fgEl(id) { return document.getElementById(id); }

// ================================================================
// UI フェーズ制御
// ================================================================
function face_setUiPhase(mode) {
    const showTrain = (mode === 'train');
    const showRec   = (mode === 'rec');
    ['fg_btnTrainSelected', 'fg_btnTrainStart'].forEach(id => {
        const el = fgEl(id);
        if (el) el.style.display = showTrain ? 'inline-block' : 'none';
    });
    ['fg_btnRecStart', 'fg_btnRecStop'].forEach(id => {
        const el = fgEl(id);
        if (el) el.style.display = showRec ? 'inline-block' : 'none';
    });
    if (showRec) {
        const s = fgEl('fg_btnRecStart'); if (s) s.disabled = false;
        const e = fgEl('fg_btnRecStop');  if (e) e.disabled = true;
    }
}

// ================================================================
// UI 初期化（faceGestureDisplay() から呼ぶ）
// ================================================================
function face_setupUI() {
    if (face_uiInited) return;
    face_uiInited = true;

    const bind = (id, fn) => {
        const el = fgEl(id);
        if (el) el.addEventListener('click', fn);
    };
    bind('fg_btnStart',         face_startCamera);
    bind('fg_btnAddClass',       face_addClass);
    bind('fg_btnTrainSelected',  face_addSample);
    bind('fg_btnTrainStart',     face_startTraining);
    bind('fg_btnRecStart',      face_startRecognition);
    bind('fg_btnRecStop',       face_stopRecognition);

    const sel = fgEl('fg_classSelect');
    if (sel) sel.addEventListener('change', face_onClassChanged);

    face_setUiPhase('train');
    const recStop = fgEl('fg_btnRecStop');   if (recStop)  recStop.disabled  = true;
    const recStart = fgEl('fg_btnRecStart'); if (recStart) recStart.disabled = true;
}

// ================================================================
// 1. カメラ起動
// ================================================================
async function face_startCamera() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { width: 320, height: 240, facingMode: 'user' }
        });
        const video = fgEl('fg_cam');
        const canvas = fgEl('fg_overlay');
        if (!video || !canvas) { alert('カメラ要素が見つかりません。'); return; }

        video.srcObject = stream;
        video.onloadedmetadata = () => {
            video.play();
            canvas.width  = 320;
            canvas.height = 240;
            face_drawLoop();
        };
        face_setStatus('カメラ起動完了');
    } catch (e) {
        alert('カメラにアクセスできません。\n' + e.message);
        console.error('[gesture_face] カメラエラー:', e);
    }
}

function face_drawLoop() {
    const video  = fgEl('fg_cam');
    const canvas = fgEl('fg_overlay');
    if (!video || !canvas) return;
    const ctx = canvas.getContext('2d');
    if (video.readyState >= 2) {
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    }
    face_animFrame = requestAnimationFrame(face_drawLoop);
}

// ================================================================
// 2. クラス追加
// ================================================================
function face_addClass() {
    if (face_isRecognizing) {
        alert('認識中はクラス作成できません。認識を終了してください。');
        return;
    }
    if (Object.keys(face_classData).length >= FACE_MAX_CLASSES) {
        alert('クラスは最大' + FACE_MAX_CLASSES + 'つまでです。');
        return;
    }
    const input = fgEl('fg_className');
    const name  = input ? input.value.trim() : '';
    if (!name) { alert('クラス名を入力してください。'); return; }
    if (face_classData[name]) { alert('同名のクラスがあります。'); return; }

    face_classData[name] = [];

    const sel = fgEl('fg_classSelect');
    if (sel) {
        const opt = document.createElement('option');
        opt.value = name;
        opt.textContent = name;
        sel.appendChild(opt);
    }
    if (input) input.value = '';
    face_setResult('クラス「' + name + '」を作成しました。サンプルを追加してください。');
    face_setStatus('クラス作成完了');
}

// ================================================================
// 3. FaceLandmarker 初期化（動的 import）
// ================================================================
async function face_loadLandmarkerIfNeeded() {
    if (face_isModelReady) return;
    face_setStatus('MediaPipe モデル読み込み中...');

    try {
        const vision = await import(
            'https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision/vision_bundle.mjs'
        );
        const { FaceLandmarker, FilesetResolver } = vision;

        const resolver = await FilesetResolver.forVisionTasks(
            'https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision/wasm'
        );

        // ローカルキャッシュ優先、なければ CDN フォールバック
        let modelPath = 'gesture/face_landmarker.task';
        try {
            const res = await fetch(modelPath, { method: 'HEAD' });
            if (!res.ok) throw new Error('local not found');
        } catch (_) {
            modelPath = 'https://storage.googleapis.com/mediapipe-models/face_landmarker/face_landmarker/float16/1/face_landmarker.task';
        }

        face_landmarker = await FaceLandmarker.createFromOptions(resolver, {
            baseOptions: { modelAssetPath: modelPath, delegate: 'GPU' },
            outputFaceBlendshapes: true,
            runningMode: 'VIDEO',
            numFaces: 1,
        });

        face_isModelReady = true;
        face_setStatus('モデル準備完了');
    } catch (e) {
        face_setStatus('モデル読み込み失敗: ' + e.message);
        console.error('[gesture_face] FaceLandmarker 初期化失敗:', e);
        throw e;
    }
}

// ================================================================
// 4. blendShapes → 52次元配列
// ================================================================
function face_captureBlendShapes() {
    const video = fgEl('fg_cam');
    if (!face_landmarker || !video || video.readyState < 2) return null;
    const result = face_landmarker.detectForVideo(video, performance.now());
    if (!result.faceBlendshapes || result.faceBlendshapes.length === 0) return null;
    const cats = result.faceBlendshapes[0].categories;
    const vec = new Array(FACE_BLEND_DIM).fill(0);
    for (let i = 0; i < Math.min(cats.length, FACE_BLEND_DIM); i++) {
        vec[i] = cats[i].score;
    }
    return vec;
}

// ================================================================
// 5. サンプル収集
// ================================================================
async function face_addSample() {
    if (face_isRecognizing) {
        alert('認識中はサンプル追加できません。認識を終了してください。');
        return;
    }
    const sel = fgEl('fg_classSelect');
    const selected = sel ? sel.value : '';
    if (!selected) { alert('クラスを選択してください。'); return; }
    if (!face_classData[selected]) { alert('クラスが存在しません。'); return; }

    if (!face_isModelReady) {
        try { await face_loadLandmarkerIfNeeded(); }
        catch (e) { alert('モデル読み込み失敗: ' + e.message); return; }
    }

    const video = fgEl('fg_cam');
    if (!video || video.readyState < 2) {
        alert('先にカメラを起動してください。');
        return;
    }

    const vec = face_captureBlendShapes();
    if (!vec) {
        face_setStatus('顔が検出されません。正面を向いてください。');
        return;
    }

    face_classData[selected].push(vec);
    const count = face_classData[selected].length;
    face_setResult('「' + selected + '」 サンプル数：' + count + ' 件');
    face_setStatus('サンプル収集中');
}

// ================================================================
// 6. 学習開始
// ================================================================
async function face_startTraining() {
    if (face_isRecognizing) { alert('認識中は学習できません。'); return; }
    if (face_isTraining) return;

    const valid = Object.keys(face_classData).filter(
        n => face_classData[n].length >= FACE_MIN_SAMPLES
    );
    if (valid.length < 2) {
        alert('学習条件：クラス2つ以上、各クラス' + FACE_MIN_SAMPLES + '件以上のサンプルが必要です。');
        return;
    }

    face_isTraining = true;
    face_setUiDisabled(true);
    try {
        await face_trainModel();
    } catch (e) {
        console.error('[gesture_face] 学習エラー:', e);
        alert('学習中にエラーが発生しました。\n' + e.message);
    } finally {
        face_isTraining = false;
        face_setUiDisabled(false);
    }
}

// ================================================================
// 7. TF.js モデル学習
// ================================================================
async function face_trainModel() {
    face_setStatus('学習準備中...');

    const labels = Object.keys(face_classData).filter(
        n => face_classData[n].length >= FACE_MIN_SAMPLES
    );
    face_classLabels = labels;

    if (face_classifier) {
        try { face_classifier.dispose(); } catch (_) {}
        face_classifier = null;
    }

    const xs = [], ys = [];
    labels.forEach((name, idx) => {
        for (const vec of face_classData[name]) {
            xs.push(vec);
            ys.push(labels.map((_, i) => i === idx ? 1 : 0));
        }
    });

    const xT = tf.tensor2d(xs, [xs.length, FACE_BLEND_DIM]);
    const yT = tf.tensor2d(ys, [ys.length, labels.length]);

    face_classifier = tf.sequential();
    face_classifier.add(tf.layers.dense({ inputShape: [FACE_BLEND_DIM], units: 64, activation: 'relu' }));
    face_classifier.add(tf.layers.dropout({ rate: 0.3 }));
    face_classifier.add(tf.layers.dense({ units: 32, activation: 'relu' }));
    face_classifier.add(tf.layers.dense({ units: labels.length, activation: 'softmax' }));
    face_classifier.compile({
        optimizer: tf.train.adam(0.001),
        loss: 'categoricalCrossentropy',
        metrics: ['accuracy'],
    });

    await face_classifier.fit(xT, yT, {
        epochs: FACE_EPOCHS,
        batchSize: FACE_BATCH_SIZE,
        shuffle: true,
        callbacks: {
            onEpochEnd: (epoch) => {
                face_setStatus('学習中... (' + (epoch + 1) + '/' + FACE_EPOCHS + ')');
            }
        }
    });

    xT.dispose();
    yT.dispose();

    face_setStatus("学習完了！『認識開始』が可能です。");
    face_initLiveContainer();
    face_setUiPhase('rec');
}

// ================================================================
// 8. 確率表示エリア初期化
// ================================================================
function face_initLiveContainer() {
    const lc = fgEl('fg_liveClass');
    if (!lc) return;
    lc.innerHTML = '';
    const header = document.createElement('div');
    header.classList.add('header-row');
    header.innerHTML = '<span>表情</span><span>確率</span>';
    lc.appendChild(header);
    face_classLabels.forEach(label => {
        const row = document.createElement('div');
        row.classList.add('result-row');
        const ns = document.createElement('span'); ns.textContent = label + ':';
        const vs = document.createElement('span');
        vs.classList.add('prob-value');
        vs.id = 'face_prob-' + label;
        vs.textContent = '---';
        row.appendChild(ns);
        row.appendChild(vs);
        lc.appendChild(row);
    });
}

// ================================================================
// 9. 認識開始 / 終了
// ================================================================
async function face_startRecognition() {
    if (face_isRecognizing) return;
    if (!face_classifier || face_classLabels.length < 2) {
        alert('先に学習を完了してください。');
        return;
    }
    const video = fgEl('fg_cam');
    if (!video || video.readyState < 2) {
        alert('先にカメラを起動してください。');
        return;
    }

    face_setUiModeRecognizing(true);
    face_initLiveContainer();
    face_isRecognizing = true;
    face_setStatus('認識中...');

    face_predictTimer = setInterval(() => {
        if (!face_isRecognizing) return;
        const vec = face_captureBlendShapes();
        if (!vec) return;

        tf.tidy(() => {
            const input = tf.tensor2d([vec], [1, FACE_BLEND_DIM]);
            const probs = face_classifier.predict(input).dataSync();

            for (let i = 0; i < face_classLabels.length; i++) {
                const el = fgEl('face_prob-' + face_classLabels[i]);
                if (el) el.textContent = (probs[i] * 100).toFixed(1) + '%';
            }

        });
    }, FACE_PREDICT_INTERVAL);
}

function face_stopRecognition() {
    if (!face_isRecognizing) return;
    if (face_predictTimer) { clearInterval(face_predictTimer); face_predictTimer = null; }
    face_isRecognizing = false;
    face_setStatus('認識停止');
    face_setUiModeRecognizing(false);
    face_setUiPhase('train');
}

// ================================================================
// UI 補助
// ================================================================
function face_setStatus(msg) {
    const el = fgEl('fg_dispstatus2');
    if (el) el.textContent = msg;
    // カメラ未起動エリアも更新
    const el2 = fgEl('fg_dispstatus');
    if (el2) el2.textContent = msg;
}
function face_setResult(msg) {
    const el = fgEl('fg_result');
    if (el) el.textContent = msg;
}
function face_onClassChanged() {
    const sel = fgEl('fg_classSelect');
    const name = sel ? sel.value : '';
    if (!name) { face_setResult('クラス未選択'); return; }
    const count = face_classData[name] ? face_classData[name].length : 0;
    face_setResult('「' + name + '」 サンプル数：' + count + ' 件');
}
function face_setUiDisabled(on) {
    ['fg_btnAddClass','fg_btnTrainSelected','fg_classSelect',
     'fg_className','fg_btnTrainStart','fg_btnRecStart'].forEach(id => {
        const el = fgEl(id); if (el) el.disabled = on;
    });
    const stop = fgEl('fg_btnRecStop'); if (stop) stop.disabled = true;
}
function face_setUiModeRecognizing(on) {
    ['fg_btnAddClass','fg_btnTrainSelected','fg_classSelect',
     'fg_className','fg_btnTrainStart','fg_btnRecStart'].forEach(id => {
        const el = fgEl(id); if (el) el.disabled = on;
    });
    const stop  = fgEl('fg_btnRecStop');  if (stop)  stop.disabled  = !on;
}