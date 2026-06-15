/**
 * ============================================================
 *  function_face_auth.js（複数クラス対応 / 認証閾値削除版）
 *
 *  仕様：
 *   - クラスを複数作成でき、各クラスに顔を複数枚登録可能
 *   - 検出顔と各クラスの登録顔を総当たりで比較、最小距離 → 一致率(%)
 *   - 全クラスの%を class_label_face に出力
 *   - 一致率 70% 以上で表示をハイライト
 *   - 認識開始ボタンを押した時にクラス全体で1枚も登録されていなければ警告
 *   - 認識中は「顔を追加登録」を無効化、認識終了で再度有効化
 *
 *  保存（localStorage）：
 *    キー: "ud1_face_classes_v2"
 *    値: { className: [ [128次元], [128次元], ... ], ... }
 * ============================================================
 */

(function () {
    "use strict";

    // ─── 定数 ────────────────────────────────────────────────
    const FACE_MODEL_URL =
        "https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights";
    const FACE_STORAGE_KEY = "ud1_face_classes_v2";
    const FACE_DETECT_INTERVAL_MS  = 500;
    const FACE_INPUT_SIZE_DETECT   = 320;
    const FACE_INPUT_SIZE_REGISTER = 416;
    const FACE_SCORE_THRESHOLD     = 0.5;
    // 認識成功とみなす一致率（旧スライダー値、固定 70%）
    const FACE_MATCH_THRESHOLD_PCT = 70;

    // ─── 状態 ────────────────────────────────────────────────
    let faceClasses = {};        // { className: [Float32Array(128), ...] }
    let face_modelsReady   = false;
    let face_cameraReady   = false;
    let face_isRecognizing = false;
    let face_detectTimerId = null;

    let face_video      = null;
    let face_overlay    = null;
    let face_overlayCtx = null;

    // ─── 表示／非表示 ────────────────────────────────────────
    window.faceAuthDisplay = function () {
        if (typeof gestureNoDisplay === "function")                  gestureNoDisplay();
        if (typeof teachablemachineNoDisplay === "function")         teachablemachineNoDisplay();
        if (typeof teachablemachineNoDisplay_pose === "function")    teachablemachineNoDisplay_pose();
        if (typeof teachablemachineNoDisplay_preset === "function")  teachablemachineNoDisplay_preset();
        const warn = document.getElementById("teachablemachine_warning");
        if (warn) warn.style.display = "block";
        const root = document.getElementById("face_auth");
        if (root) root.style.display = "block";

        faceAuthInit();
    };

    window.faceAuthNoDisplay = function () {
        const root = document.getElementById("face_auth");
        if (root) root.style.display = "none";
        stopRecognition();
    };

    // ─── 初期化 ──────────────────────────────────────────────
    let face_initStarted = false;
    async function faceAuthInit() {
        if (face_initStarted) return;
        face_initStarted = true;

        face_video   = document.getElementById("face_video");
        face_overlay = document.getElementById("face_overlay");
        if (face_overlay) face_overlayCtx = face_overlay.getContext("2d");

        if (typeof faceapi === "undefined") {
            setStatus("face-api.js が読み込まれていません。");
            face_initStarted = false;
            return;
        }

        setupUI();
        loadFromStorage();
        updateClassSelectOptions();
        updateResultText();

        try {
            await loadFaceModels();
        } catch (e) {
            console.error("[face_auth] モデル読み込み失敗:", e);
            setStatus("モデル読み込み失敗: " + e.message);
        }
    }

    // ─── モデル読み込み ──────────────────────────────────────
    async function loadFaceModels() {
        if (face_modelsReady) return;
        setStatus("モデル読み込み中: TinyFaceDetector");
        await faceapi.nets.tinyFaceDetector.loadFromUri(FACE_MODEL_URL);
        setStatus("モデル読み込み中: FaceLandmark68TinyNet");
        await faceapi.nets.faceLandmark68TinyNet.loadFromUri(FACE_MODEL_URL);
        setStatus("モデル読み込み中: FaceRecognitionNet");
        await faceapi.nets.faceRecognitionNet.loadFromUri(FACE_MODEL_URL);

        face_modelsReady = true;
        setStatus("モデル準備完了。カメラを開始してください。");
        updateButtonsState();
    }

    // ─── カメラ起動 ──────────────────────────────────────────
    async function startCamera() {
        if (face_cameraReady) return;
        const statusEl = document.getElementById("face_camera_status");
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 320 }, height: { ideal: 240 }, facingMode: "user" },
                audio: false
            });
            face_video.srcObject = stream;
            await new Promise(resolve => { face_video.onloadedmetadata = resolve; });
            await face_video.play();
            face_overlay.width  = face_video.videoWidth  || 320;
            face_overlay.height = face_video.videoHeight || 240;
            face_cameraReady = true;
            if (statusEl) statusEl.textContent = "カメラ起動完了";
            setStatus("カメラ起動完了。クラスを作成して顔を登録してください。");
            updateButtonsState();
        } catch (err) {
            if (statusEl) statusEl.textContent = "カメラエラー: " + err.message;
            console.error("[face_auth] カメラ起動失敗:", err);
            setStatus("カメラ起動失敗: " + err.message);
        }
    }

    // ─── クラス作成 ──────────────────────────────────────────
    function addClass() {
        if (face_isRecognizing) {
            alert("認識中はクラス作成できません。認識を終了してください。");
            return;
        }
        const input = document.getElementById("face_className");
        const name  = (input.value || "").trim();
        if (!name) {
            alert("クラス名を入力してください。");
            return;
        }
        if (faceClasses[name]) {
            alert("同名のクラスがあります。");
            return;
        }
        faceClasses[name] = [];
        input.value = "";
        //saveToStorage();
        updateClassSelectOptions(name);
        updateResultText();
        setStatus('クラス「' + name + '」を作成しました。顔を登録してください。');
    }

    // ─── 顔の追加登録 ────────────────────────────────────────
    async function addFaceToSelectedClass() {
        if (face_isRecognizing) {
            alert("認識中は登録できません。認識を終了してください。");
            return;
        }
        if (!face_cameraReady) {
            alert("先にカメラを開始してください。");
            return;
        }
        const sel = document.getElementById("face_classSelect");
        const className = sel.value;
        if (!className) {
            alert("クラスを選択してください。");
            return;
        }
        if (!faceClasses[className]) {
            alert("クラスが存在しません。");
            return;
        }

        setStatus("顔を解析中...");
        const options = new faceapi.TinyFaceDetectorOptions({
            inputSize: FACE_INPUT_SIZE_REGISTER,
            scoreThreshold: FACE_SCORE_THRESHOLD
        });
        let result;
        try {
            result = await faceapi
                .detectSingleFace(face_video, options)
                .withFaceLandmarks(true)
                .withFaceDescriptor();
        } catch (e) {
            console.warn("[face_auth] 登録時検出失敗:", e);
        }
        if (!result) {
            setStatus("顔が検出できませんでした。正面を向いて再試行してください。");
            return;
        }

        faceClasses[className].push(Array.from(result.descriptor));
        //saveToStorage();
        updateClassSelectOptions(className);  // セレクトの枚数表示を更新
        updateResultText();
        setStatus('クラス「' + className + '」に顔を1枚追加しました。');
    }

    // ─── 認識開始 ────────────────────────────────────────────
    function startRecognition() {
        if (face_isRecognizing) return;
        if (!face_modelsReady) {
            alert("モデルの準備が完了していません。");
            return;
        }
        if (!face_cameraReady) {
            alert("先にカメラを開始してください。");
            return;
        }

        // ★ 全クラスを通して 1枚も顔が登録されていなければ警告
        const totalSamples = Object.keys(faceClasses)
            .reduce((sum, k) => sum + faceClasses[k].length, 0);
        if (totalSamples === 0) {
            alert("顔が登録されていません。\n「⑤顔を追加登録」で1枚以上の顔を登録してから認識を開始してください。");
            return;
        }

        face_isRecognizing = true;
        setStatus("認識中...");

        const options = new faceapi.TinyFaceDetectorOptions({
            inputSize: FACE_INPUT_SIZE_DETECT,
            scoreThreshold: FACE_SCORE_THRESHOLD
        });
        face_detectTimerId = setInterval(async () => {
            if (!face_isRecognizing) return;
            if (face_video.paused || face_video.ended) return;

            let result;
            try {
                result = await faceapi
                    .detectSingleFace(face_video, options)
                    .withFaceLandmarks(true)
                    .withFaceDescriptor();
            } catch (e) {
                return;
            }

            if (face_overlayCtx) {
                face_overlayCtx.clearRect(0, 0, face_overlay.width, face_overlay.height);
            }
            if (result) {
                drawFaceBox(result.detection);
                renderProbabilities(computeAllPercentages(result.descriptor));
            } else {
                renderProbabilities(computeAllPercentages(null));
            }
        }, FACE_DETECT_INTERVAL_MS);

        updateButtonsState();   // ★ 登録ボタン無効化／終了ボタン有効化
    }

    // ─── 認識終了 ────────────────────────────────────────────
    function stopRecognition() {
        if (!face_isRecognizing) return;
        face_isRecognizing = false;
        if (face_detectTimerId !== null) {
            clearInterval(face_detectTimerId);
            face_detectTimerId = null;
        }
        // 結果をクリア（doesLabelExist が 0% を返すように 0% で再描画）
        renderProbabilities(computeAllPercentages(null));
        if (face_overlayCtx) {
            face_overlayCtx.clearRect(0, 0, face_overlay.width, face_overlay.height);
        }
        setStatus("認識停止");
        updateButtonsState();   // ★ 登録ボタン再有効化
    }

    // ─── 一致率計算（全クラス） ──────────────────────────────
    function computeAllPercentages(descriptor) {
        const out = {};
        Object.keys(faceClasses).forEach(name => {
            const samples = faceClasses[name];
            if (!descriptor || !samples || samples.length === 0) {
                out[name] = 0;
                return;
            }
            let minDist = Infinity;
            for (let i = 0; i < samples.length; i++) {
                const d = euclideanDistance(descriptor, samples[i]);
                if (d < minDist) minDist = d;
            }
            // 距離→一致率(%)　小数点1桁
            const pct = Math.max(0, Math.min(100, Math.round((1 - minDist) * 1000) / 10));
            out[name] = pct;
        });
        return out;
    }

    function euclideanDistance(a, b) {
        if (typeof faceapi !== "undefined" && faceapi.euclideanDistance) {
            return faceapi.euclideanDistance(a, b);
        }
        let s = 0;
        for (let i = 0; i < a.length; i++) {
            const d = a[i] - b[i];
            s += d * d;
        }
        return Math.sqrt(s);
    }

    // ─── 結果描画 ───────────────────────────────────────────
    // doesLabelExist は <div> の textContent から「クラス名:数値%」を
    // 正規表現で拾うので、必ずヘッダ行＋各クラス行の構造で書き出す
    function renderProbabilities(pctMap) {
        const container = document.getElementById("class_label_face");
        if (!container) return;
        container.innerHTML = "";

        const names = Object.keys(pctMap);
        if (names.length === 0) {
            const div = document.createElement("div");
            div.textContent = "（クラス未登録）";
            container.appendChild(div);
            return;
        }

        // ヘッダー行（他のUIと同じ流儀）
        const header = document.createElement("div");
        header.classList.add("header-row");
        const hName  = document.createElement("span");
        hName.textContent  = "クラス名:";
        const hValue = document.createElement("span");
        hValue.textContent = "値";
        hValue.style.marginLeft = "8px";
        header.append(hName, hValue);
        container.appendChild(header);

        names.forEach(name => {
            const pct = pctMap[name];
            const row = document.createElement("div");
            row.classList.add("result-row");
            const nameSpan = document.createElement("span");
            nameSpan.textContent = name + ":";
            const valueSpan = document.createElement("span");
            valueSpan.textContent = pct.toFixed(1) + "%";
            valueSpan.style.marginLeft = "8px";
            if (pct >= FACE_MATCH_THRESHOLD_PCT) {
                valueSpan.style.color = "#00b050";
                valueSpan.style.fontWeight = "bold";
            }
            row.append(nameSpan, valueSpan);
            container.appendChild(row);
        });
    }

    function drawFaceBox(detection) {
        if (!face_overlayCtx) return;
        const box = detection.box;
        face_overlayCtx.strokeStyle = "#0070c0";
        face_overlayCtx.lineWidth = 3;
        face_overlayCtx.strokeRect(box.x, box.y, box.width, box.height);
    }

    // ─── localStorage ───────────────────────────────────────
    function saveToStorage() {
        try {
            localStorage.setItem(FACE_STORAGE_KEY, JSON.stringify(faceClasses));
        } catch (e) {
            console.warn("[face_auth] localStorage 保存失敗:", e);
        }
    }
    function loadFromStorage() {
        try {
            const raw = localStorage.getItem(FACE_STORAGE_KEY);
            if (!raw) return;
            const obj = JSON.parse(raw);
            if (obj && typeof obj === "object") {
                faceClasses = {};
                Object.keys(obj).forEach(k => {
                    if (Array.isArray(obj[k])) {
                        faceClasses[k] = obj[k].filter(v => Array.isArray(v));
                    }
                });
            }
        } catch (e) {
            console.warn("[face_auth] 保存データ読み込み失敗:", e);
            faceClasses = {};
        }
    }

    // ─── UI 補助 ────────────────────────────────────────────
    function updateClassSelectOptions(selectName) {
        const sel = document.getElementById("face_classSelect");
        if (!sel) return;
        const current = selectName || sel.value;
        sel.innerHTML = "";
        const placeholder = document.createElement("option");
        placeholder.value = "";
        placeholder.textContent = "④（未選択）";
        sel.appendChild(placeholder);
        Object.keys(faceClasses).forEach(name => {
            const opt = document.createElement("option");
            opt.value = name;
            opt.textContent = name + "（" + faceClasses[name].length + "枚）";
            sel.appendChild(opt);
        });
        if (current && faceClasses[current]) {
            sel.value = current;
        }
        updateButtonsState();
    }

    function updateResultText() {
        const el = document.getElementById("face_result");
        if (!el) return;
        const names = Object.keys(faceClasses);
        if (names.length === 0) {
            el.textContent = "クラス未作成";
            return;
        }
        const parts = names.map(n => n + ":" + faceClasses[n].length + "枚");
        el.textContent = parts.join(" / ");
    }

    function setStatus(msg) {
        const el = document.getElementById("face_dispstatus");
        if (el) el.textContent = msg;
    }

    // ★ ボタンの活性／非活性を一括制御
    //   - 認識中は ⑤登録ボタンを必ず無効化、⑦終了のみ有効
    //   - 認識停止中は条件次第で ⑤⑥ を有効化、⑦は無効
    function updateButtonsState() {
        const btnRegister = document.getElementById("face_btnRegister");
        const btnRecStart = document.getElementById("face_btnRecStart");
        const btnRecStop  = document.getElementById("face_btnRecStop");
        const sel         = document.getElementById("face_classSelect");

        const hasClassSelected = sel && sel.value && faceClasses[sel.value];
        const anyClass = Object.keys(faceClasses).length > 0;
        const ready    = face_modelsReady && face_cameraReady;

        if (face_isRecognizing) {
            // 認識中：登録ボタン無効、開始無効、終了のみ有効
            if (btnRegister) btnRegister.disabled = true;
            if (btnRecStart) btnRecStart.disabled = true;
            if (btnRecStop)  btnRecStop.disabled  = false;
        } else {
            // 認識停止中
            if (btnRegister) btnRegister.disabled = !(ready && hasClassSelected);
            if (btnRecStart) btnRecStart.disabled = !(ready && anyClass);
            if (btnRecStop)  btnRecStop.disabled  = true;
        }
    }

    // ─── イベントセットアップ ───────────────────────────────
    let face_uiInited = false;
    function setupUI() {
        if (face_uiInited) return;
        face_uiInited = true;

        document.getElementById("face_btnStart")
            .addEventListener("click", startCamera);
        document.getElementById("face_btnAddClass")
            .addEventListener("click", addClass);
        document.getElementById("face_btnRegister")
            .addEventListener("click", addFaceToSelectedClass);
        document.getElementById("face_btnRecStart")
            .addEventListener("click", startRecognition);
        document.getElementById("face_btnRecStop")
            .addEventListener("click", stopRecognition);

        const sel = document.getElementById("face_classSelect");
        if (sel) sel.addEventListener("change", updateButtonsState);
    }
})();