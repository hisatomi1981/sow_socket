//modelsフォルダへの相対パスを指定
const URL = "teachablemachine/models/";
let model_preset, webcam_preset, ctx_preset, labelContainer_preset, maxPredictions_preset;

// ① 定数名をユニークに
const PREDICT_INTERVAL_PRESET = 500;  // 500ms間隔
let lastPredictTimePreset = 0;

// ② init_preset をグローバルに公開
window.init_preset = async function init_preset() {
    const modelURL    = URL + "model.json";
    const metadataURL = URL + "metadata.json";

    model_preset = await tmPose.load(modelURL, metadataURL);
    maxPredictions_preset = model_preset.getTotalClasses();

    const size = 200, flip = true;
    webcam_preset = new tmPose.Webcam(size, size, flip);
    await webcam_preset.setup();
    await webcam_preset.play();

    // キャンバス取得＆コンテキストを別名で保持
    const canvas_preset = document.getElementById("canvas_preset");
    if (!canvas_preset) {
      console.error("canvas_preset が見つかりません");
      return;
    }
    canvas_preset.width  = size;
    canvas_preset.height = size;
    ctx_preset = canvas_preset.getContext("2d");

    labelContainer_preset = document.getElementById("class_label_preset");
    if (!labelContainer_preset) {
      console.error("class_label_preset が見つかりません");
      return;
    }

    // 描画＋分類スロットルループ開始
    window.requestAnimationFrame(loop_preset);
};

async function loop_preset(timestamp) {
    // 毎フレーム：映像更新＋スケルトン描画
    webcam_preset.update();
    const { pose, posenetOutput } = await model_preset.estimatePose(webcam_preset.canvas);
    drawPose_preset(pose);

    // 500ms毎にラベル更新
    if (timestamp - lastPredictTimePreset > PREDICT_INTERVAL_PRESET) {
        await updateLabels_preset(posenetOutput);
        lastPredictTimePreset = timestamp;
    }

    window.requestAnimationFrame(loop_preset);
}

async function updateLabels_preset(posenetOutput) {
    const prediction = await model_preset.predict(posenetOutput);

    // 出力クリア
    labelContainer_preset.innerHTML = "";

    // 見出し行
    const header      = document.createElement("div");
    header.classList.add("header-row");
    const headerName  = document.createElement("span");
    headerName.textContent  = "クラス名:";
    const headerValue = document.createElement("span");
    headerValue.textContent = "値";
    header.append(headerName, headerValue);
    labelContainer_preset.appendChild(header);

    // 各クラス結果
    prediction.forEach(p => {
        const row       = document.createElement("div");
        row.classList.add("result-row");
        const nameSpan  = document.createElement("span");
        nameSpan.textContent  = p.className + ":";
        const valueSpan = document.createElement("span");
        valueSpan.textContent = (p.probability * 100).toFixed(2) + "%";
        row.append(nameSpan, valueSpan);
        labelContainer_preset.appendChild(row);
    });
}

function drawPose_preset(pose) {
    if (!ctx_preset) return;
    ctx_preset.clearRect(0, 0, ctx_preset.canvas.width, ctx_preset.canvas.height);
    ctx_preset.drawImage(webcam_preset.canvas, 0, 0);
    if (pose) {
        const minPartConfidence = 0.5;
        tmPose.drawKeypoints(pose.keypoints, minPartConfidence, ctx_preset);
        tmPose.drawSkeleton(pose.keypoints, minPartConfidence, ctx_preset);
    }
}