// ================================================================
// 0. DOM 参照
// ================================================================
const video = document.getElementById("cam");
const overlay = document.getElementById("overlay");
const ctx2 = overlay.getContext("2d");

const classNameInput = document.getElementById("className");
const classSelect = document.getElementById("classSelect");
const liveClass = document.getElementById("liveClass"); // 推論表示
const resultEl = document.getElementById("result");      // サンプル状況表示
const dispstatusEl = document.getElementById("dispstatus");

const btnStart = document.getElementById("btnStart");
const btnAddClass = document.getElementById("btnAddClass");

// ⑤（ここは「サンプル追加」用途）
const btnTrainSelected = document.getElementById("btnTrainSelected");

// 学習開始ボタン
const btnTrainStart = document.getElementById("btnTrainStart");

// 認識開始/終了ボタン
const btnRecStart = document.getElementById("btnRecStart");
const btnRecStop  = document.getElementById("btnRecStop");

// ================================================================
// 設定値
// ================================================================
const MAX_CLASSES = 5;
const MIN_SAMPLES_PER_CLASS = 3;

const EPOCHS = 20;
const BATCH_SIZE = 16;

// 推論間隔（ms）※重い場合は 300〜500 にするとさらに軽くなります
const PREDICT_INTERVAL_MS = 250;

// ================================================================
// 状態変数
// ================================================================
let classData = {};              // { className: Tensor[] }
let featureExtractor = null;     // MobileNet中間層
let classifier = null;           // 追加分類器
let isModelReady = false;
let isTraining = false;
let isRecognizing = false;
let classLabels = [];
let predictTimerId = null;

// ================================================================
// UIフェーズ制御（表示/非表示）
//  - train: 学習系ボタン表示、認識系ボタン非表示
//  - rec  : 認識系ボタン表示、学習系ボタン非表示
// ================================================================
function setUiPhase(mode) {
  const showTrain = (mode === "train");
  const showRec   = (mode === "rec");

  // 学習系
  if (btnTrainSelected) btnTrainSelected.style.display = showTrain ? "inline-block" : "none";
  if (btnTrainStart)    btnTrainStart.style.display    = showTrain ? "inline-block" : "none";

  // 認識系
  if (btnRecStart) btnRecStart.style.display = showRec ? "inline-block" : "none";
  if (btnRecStop)  btnRecStop.style.display  = showRec ? "inline-block" : "none";

  // 認識系の初期disabled
  if (showRec) {
    if (btnRecStart) btnRecStart.disabled = false; // 認識開始は押せる状態に
    if (btnRecStop)  btnRecStop.disabled  = true;  // 認識終了は認識中のみ有効
  }
}

// ================================================================
// 初期UI状態
// ================================================================
setUiPhase("train"); // ★初期は学習ボタンだけ表示
if (btnRecStop) btnRecStop.disabled = true;
if (btnRecStart) btnRecStart.disabled = true; // 表示されないが念のため
if (btnTrainStart) btnTrainStart.disabled = false;

// ================================================================
// イベント設定
// ================================================================
btnStart.addEventListener("click", startCamera);
btnAddClass.addEventListener("click", addClass);
btnTrainSelected.addEventListener("click", addSampleToSelectedClass);

if (btnTrainStart) btnTrainStart.addEventListener("click", startTrainingByButton);
if (btnRecStart) btnRecStart.addEventListener("click", startRecognitionByButton);
if (btnRecStop) btnRecStop.addEventListener("click", stopRecognitionByButton);

classSelect.addEventListener("change", onClassChanged);

// ================================================================
// 1. カメラ起動
// ================================================================
async function startCamera() {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: { width: 320, height: 240 }
    });

    video.srcObject = stream;
    video.onloadedmetadata = () => {
      video.play();

      // canvasサイズの明示（将来ズレ防止）
      overlay.width = 320;
      overlay.height = 240;

      drawLoop();
    };

    dispstatusEl.textContent = "カメラ起動完了";
  } catch (e) {
    alert("カメラにアクセスできません。");
    console.error(e);
  }
}

function drawLoop() {
  if (video.readyState >= 2) {
    ctx2.drawImage(video, 0, 0, overlay.width, overlay.height);
  }
  requestAnimationFrame(drawLoop);
}

// ================================================================
// 2. クラス追加
// ================================================================
function addClass() {
  if (isRecognizing) {
    alert("認識中はクラス作成できません。認識を終了してください。");
    return;
  }

  if (Object.keys(classData).length >= MAX_CLASSES) {
    alert(`クラスは最大${MAX_CLASSES}つまでです。`);
    return;
  }

  const name = classNameInput.value.trim();
  if (!name) {
    alert("クラス名を入力してください。");
    return;
  }

  if (classData[name]) {
    alert("同名のクラスがあります。");
    return;
  }

  classData[name] = [];

  const option = document.createElement("option");
  option.value = name;
  option.textContent = name;
  classSelect.appendChild(option);

  classNameInput.value = "";

  resultEl.textContent = `クラス「${name}」を作成しました。サンプルを追加してください。`;
  dispstatusEl.textContent = "クラス作成完了";
}

// ================================================================
// 3. MobileNet 読み込み
// ================================================================
async function loadBaseModelIfNeeded() {
  if (isModelReady) return;

  dispstatusEl.textContent = "モデル準備中...";

  const baseModel = await tf.loadLayersModel(
    "https://storage.googleapis.com/tfjs-models/tfjs/mobilenet_v1_0.25_224/model.json"
  );

  const layer = baseModel.getLayer("conv_pw_13_relu");
  featureExtractor = tf.model({
    inputs: baseModel.inputs,
    outputs: layer.output
  });

  isModelReady = true;
  dispstatusEl.textContent = "モデル準備完了";
}

// ================================================================
// 4. カメラ画像 → Tensor
// ================================================================
function captureTensorFromVideo() {
  if (!video || video.readyState < 2) {
    console.warn("video not ready");
    return null;
  }

  return tf.tidy(() => {
    const img = tf.browser.fromPixels(video);
    return img
      .resizeNearestNeighbor([224, 224])
      .toFloat()
      .div(255.0)
      .expandDims();
  });
}

// ================================================================
// 5. サンプル追加（⑤）
// ================================================================
function addSampleToSelectedClass() {
  if (isRecognizing) {
    alert("認識中はサンプル追加できません。認識を終了してください。");
    return;
  }

  const selectedClass = classSelect.value;
  if (!selectedClass) {
    alert("クラスを選択してください。");
    return;
  }

  if (!classData[selectedClass]) {
    alert("クラスが存在しません。");
    return;
  }

  const tensor = captureTensorFromVideo();
  if (!tensor) {
    alert("カメラ準備中です。");
    return;
  }

  classData[selectedClass].push(tensor);

  updateSampleStatusText(selectedClass);
  dispstatusEl.textContent = "サンプル収集中";
}

function updateSampleStatusText(selectedClass) {
  let msg = `クラス「${selectedClass}」 サンプル数：${classData[selectedClass].length} 枚`;
  resultEl.textContent = msg;
}

// ================================================================
// 現在のサンプル状況
// ================================================================
function getSampleStatus() {
  const names = Object.keys(classData);
  let nonEmpty = 0;
  let minSamples = Infinity;

  names.forEach(name => {
    const len = classData[name].length;
    if (len > 0) {
      nonEmpty++;
      minSamples = Math.min(minSamples, len);
    }
  });

  if (minSamples === Infinity) minSamples = 0;

  return {
    classCount: names.length,
    nonEmptyClassCount: nonEmpty,
    minSamples
  };
}

// ================================================================
// 6. 学習開始（ボタン制御）
// ================================================================
async function startTrainingByButton() {
  if (isRecognizing) {
    alert("認識中は学習できません。認識を終了してください。");
    return;
  }
  if (isTraining) return;

  const info = getSampleStatus();
  if (info.nonEmptyClassCount < 2 || info.minSamples < MIN_SAMPLES_PER_CLASS) {
    alert(`学習条件：クラス2つ以上、各クラス${MIN_SAMPLES_PER_CLASS}枚以上の学習が必要です。`);
    return;
  }

  await trainModelAsync();
}

async function trainModelAsync() {
  if (isTraining) return;
  isTraining = true;

  setUiModeTraining(true);

  try {
    await trainModel();
  } catch (e) {
    console.error(e);
    alert("学習中にエラーが発生しました。");
  } finally {
    isTraining = false;
    setUiModeTraining(false);
  }
}

function setUiModeTraining(on) {
  btnAddClass.disabled = on;
  btnTrainSelected.disabled = on;
  classSelect.disabled = on;
  classNameInput.disabled = on;

  if (btnTrainStart) btnTrainStart.disabled = on;

  // ★学習中は認識操作は不可（ただし表示/非表示は setUiPhase が担当）
  if (btnRecStart) btnRecStart.disabled = on;
  if (btnRecStop)  btnRecStop.disabled  = true;
}

// ================================================================
// 7. モデル学習
// ================================================================
async function trainModel() {
  dispstatusEl.textContent = "学習準備中...";

  await loadBaseModelIfNeeded();

  // サンプルがあるクラスのみ対象
  const names = Object.keys(classData).filter(n => classData[n].length > 0);
  classLabels = names;

  // 既存classifierがあれば破棄（学習し直し）
  if (classifier) {
    try { classifier.dispose(); } catch (_) {}
    classifier = null;
  }

  let xsList = [];
  let ysList = [];

  names.forEach((name, idx) => {
    classData[name].forEach(t => {
      const feat = featureExtractor.predict(t);
      xsList.push(feat);

      const y = tf.tensor2d([names.map((_, i) => (i === idx ? 1 : 0))]);
      ysList.push(y);
    });
  });

  const xs = tf.concat(xsList);
  const ys = tf.concat(ysList);

  classifier = tf.sequential();
  classifier.add(tf.layers.flatten({ inputShape: [7, 7, 256] }));
  classifier.add(tf.layers.dense({ units: 80, activation: "relu" }));
  classifier.add(tf.layers.dense({ units: names.length, activation: "softmax" }));

  classifier.compile({
    optimizer: tf.train.adam(0.0001),
    loss: "categoricalCrossentropy",
    metrics: ["accuracy"]
  });

  await classifier.fit(xs, ys, {
    epochs: EPOCHS,
    batchSize: BATCH_SIZE,
    shuffle: true,
    callbacks: {
      onEpochEnd: (epoch, logs) => {
        dispstatusEl.textContent = `学習中... (${epoch + 1}/${EPOCHS})`;
      }
    }
  });

  // ★学習完了後：認識フェーズへ切り替え（表示/非表示）
  dispstatusEl.textContent = "学習完了！『認識開始』が可能です。";
  initLiveContainer();
  setUiPhase("rec"); // ← 要件どおり、学習ボタン非表示・認識ボタン表示にする

  // 後処理（メモリ解放）
  xs.dispose();
  ys.dispose();
  xsList.forEach(t => t.dispose()); // dispose?. ではなく確実に
  ysList.forEach(t => t.dispose());
}

// ================================================================
// 8. 推論表示領域の初期化（行構築）
// ================================================================
function initLiveContainer() {
  liveClass.innerHTML = "";

  const header = document.createElement("div");
  header.classList.add("header-row");
  header.innerHTML = "<span>クラス名</span><span>確率</span>";
  liveClass.appendChild(header);

  classLabels.forEach(label => {
    const row = document.createElement("div");
    row.classList.add("result-row");

    const nameSpan = document.createElement("span");
    nameSpan.textContent = label + ":";

    const valueSpan = document.createElement("span");
    valueSpan.classList.add("prob-value");
    valueSpan.id = `prob-${label}`;
    valueSpan.textContent = "---";

    row.appendChild(nameSpan);
    row.appendChild(valueSpan);

    liveClass.appendChild(row);
  });
}

// ================================================================
// 9. 認識開始/終了（ボタン制御）
// ================================================================
async function startRecognitionByButton() {
  if (isRecognizing) return;

  if (!classifier || !featureExtractor || classLabels.length < 2) {
    alert("先に学習を完了してください。");
    return;
  }

  if (!video || video.readyState < 2) {
    alert("先にカメラを開始してください。");
    return;
  }

  setUiModeRecognizing(true);
  initLiveContainer();

  isRecognizing = true;
  dispstatusEl.textContent = "認識中...";

  predictTimerId = setInterval(() => {
    tf.tidy(() => {
      if (!video || video.readyState < 2) return;

      const img = tf.browser.fromPixels(video)
        .resizeNearestNeighbor([224, 224])
        .toFloat()
        .div(255.0)
        .expandDims();

      const feat = featureExtractor.predict(img);
      const pred = classifier.predict(feat);
      const data = pred.dataSync();

      for (let i = 0; i < classLabels.length; i++) {
        const probSpan = document.getElementById(`prob-${classLabels[i]}`);
        if (probSpan) probSpan.textContent = `${(data[i] * 100).toFixed(1)}%`;
      }
    });
  }, PREDICT_INTERVAL_MS);
}

function stopRecognitionByButton() {
  if (!isRecognizing) return;

  if (predictTimerId) {
    clearInterval(predictTimerId);
    predictTimerId = null;
  }

  isRecognizing = false;
  dispstatusEl.textContent = "認識停止";

  setUiModeRecognizing(false);
  setUiPhase("train");
}

// 認識中のdisabled切り替えのみ担当（表示/非表示は setUiPhase）
function setUiModeRecognizing(on) {
  // 認識中はサンプル追加・クラス編集不可
  btnAddClass.disabled = on;
  btnTrainSelected.disabled = on;
  classSelect.disabled = on;
  classNameInput.disabled = on;

  // 学習開始も不可（ただし学習フェーズでは非表示）
  if (btnTrainStart) btnTrainStart.disabled = on;

  // 認識開始/終了
  if (btnRecStart) btnRecStart.disabled = on;
  if (btnRecStop)  btnRecStop.disabled  = !on;
}

// ================================================================
// 10. クラス変更時
// ================================================================
function onClassChanged() {
  const selected = classSelect.value;

  if (!selected) {
    resultEl.textContent = "クラスが選択されていません。";
    return;
  }

  if (!classData[selected]) {
    resultEl.textContent = `クラス「${selected}」のデータはありません。`;
    return;
  }

  updateSampleStatusText(selected);
}
