let model, webcam, ctx, labelContainer, maxPredictions;

// Load the image model and setup the webcam
async function init_camera() {
    //const inputURL = "https://teachablemachine.withgoogle.com/models/5OXjNY4X4/"; // グーチョキパー
    //const inputURL = "https://teachablemachine.withgoogle.com/models/rScScq_K8/"; // UC UD
    const inputURL = document.getElementById("model-url").value;
    if (!inputURL) {
        alert("Teachable MachineでエクスポートしたモデルのURLを入力してください。");
        return;
    }

    const modelURL = inputURL + "model.json";
    const metadataURL = inputURL + "metadata.json";

    const response = await fetch(metadataURL);
    const metadata = await response.json();

    if (metadata.modelName != "tm-my-image-model") {
        alert("このモデルは画像分類モデルではありません。画像モデル専用のURLを指定してください。");
        return;
    }

    try {
        // load the model and metadata
        model = await tmImage.load(modelURL, metadataURL);
        maxPredictions = model.getTotalClasses();

        // Setup the webcam
        const flip = true; // whether to flip the webcam
        webcam = new tmImage.Webcam(200, 200, flip); // width, height, flip
        await webcam.setup(); // request access to the webcam
        await webcam.play();
        window.requestAnimationFrame(loop);

        // append elements to the DOM
        document.getElementById("webcam-container").innerHTML = ""; // Clear previous webcam if any
        document.getElementById("webcam-container").appendChild(webcam.canvas);
        labelContainer = document.getElementById("class_label");
        labelContainer.innerHTML = ""; // Clear previous labels if any
        for (let i = 0; i < maxPredictions; i++) { // and class labels
            labelContainer.appendChild(document.createElement("div"));
        }

        // Start prediction after initialization
        setInterval(predict, 500);
    } catch (error) {
        alert("モデルの読み込みに失敗しました。正しいURLを入力して下さい。");
        console.error(error);
    }
}

async function loop() {
    if (webcam) {
        webcam.update(); // update the webcam frame
    }
    window.requestAnimationFrame(loop);
}

let currentPrediction = []; // Store the latest predictions
async function predict() {
    if (model && webcam) {
        // １．予測結果を取得
        const prediction = await model.predict(webcam.canvas);

        // ２．既存の出力をクリア
        labelContainer.innerHTML = "";

        // ３．見出し行を作成（1行目）
        const header = document.createElement("div");
        header.classList.add("header-row");
        // 左（クラス名）用
        const headerName = document.createElement("span");
        headerName.textContent = "クラス名:";
        // 右（値）用
        const headerValue = document.createElement("span");
        headerValue.textContent = "値";
        header.append(headerName, headerValue);
        labelContainer.appendChild(header);

        // ４．各クラスの予測値を順次追加（2行目以降）
        prediction.forEach(p => {
            const row = document.createElement("div");
            row.classList.add("result-row");
            // 左：クラス名
            const nameSpan = document.createElement("span");
            nameSpan.textContent = p.className + ":";
            // 右：確率
            const valueSpan = document.createElement("span");
            valueSpan.textContent = (p.probability * 100).toFixed(2) + "%";
            row.append(nameSpan, valueSpan);
            labelContainer.appendChild(row);
        });
    }
}

// グローバル変数（ポーズ分類用）
let model_pose, webcam_pose, ctx_pose, labelContainer_pose, maxPredictions_pose;

// 1. 初期化：モデルロード＋カメラセットアップ＋ループ開始
async function init_pose() {
    const inputURL_pose = document.getElementById("model-url_pose").value;
    if (!inputURL_pose) {
        alert("Teachable MachineでエクスポートしたモデルのURLを入力してください。");
        return;
    }

    const modelURL_pose = inputURL_pose + "model.json";
    const metadataURL_pose = inputURL_pose + "metadata.json";
    const response_pose = await fetch(metadataURL_pose);
    const metadata_pose = await response_pose.json();

    if (metadata_pose.modelName !== "my-pose-model") {
        alert("このモデルはポーズ分類モデルではありません。正しいURLを指定してください。");
        return;
    }

    // モデルロード
    model_pose = await tmPose.load(modelURL_pose, metadataURL_pose);
    maxPredictions_pose = model_pose.getTotalClasses();

    // カメラセットアップ
    const size_pose = 200;
    const flip_pose = true;
    webcam_pose = new tmPose.Webcam(size_pose, size_pose, flip_pose);
    await webcam_pose.setup();
    await webcam_pose.play();

    // 描画コンテキスト取得（別名）
    const canvas = document.getElementById("canvas_pose");
    canvas.width = size_pose;
    canvas.height = size_pose;
    ctx_pose = canvas.getContext("2d");

    // ラベル表示用コンテナ取得
    labelContainer_pose = document.getElementById("class_label_pose");

    // 描画＋分類スロットルループ開始
    window.requestAnimationFrame(loop_pose);
}

// 2. スロットル制御用タイムスタンプ
let lastPredictTime = 0;
const PREDICT_INTERVAL = 500; // ms

// 3. メインループ：描画は毎フレーム、分類は500ms毎
async function loop_pose(timestamp) {
    // --- 描画更新（常時計算） ---
    webcam_pose.update();
    const { pose, posenetOutput } = await model_pose.estimatePose(webcam_pose.canvas);
    drawPose(pose);

    // --- 分類更新（500msごと） ---
    if (timestamp - lastPredictTime > PREDICT_INTERVAL) {
        await updateLabels(posenetOutput);
        lastPredictTime = timestamp;
    }

    window.requestAnimationFrame(loop_pose);
}

// 4. ラベル更新：ヘッダー＋左右分割で表示
async function updateLabels(posenetOutput) {
    const prediction_pose = await model_pose.predict(posenetOutput);

    // 既存出力をクリア
    labelContainer_pose.innerHTML = "";

    // 見出し行
    const header = document.createElement("div");
    header.classList.add("header-row");
    const headerName  = document.createElement("span");
    headerName.textContent  = "クラス名:";
    const headerValue = document.createElement("span");
    headerValue.textContent = "値";
    header.append(headerName, headerValue);
    labelContainer_pose.appendChild(header);

    // 各クラス結果
    prediction_pose.forEach(p => {
        const row = document.createElement("div");
        row.classList.add("result-row");
        const nameSpan  = document.createElement("span");
        nameSpan.textContent = p.className + ":";
        const valueSpan = document.createElement("span");
        valueSpan.textContent = (p.probability * 100).toFixed(2) + "%";
        row.append(nameSpan, valueSpan);
        labelContainer_pose.appendChild(row);
    });
}

// 5. スケルトン描画（毎フレーム呼び出し）
function drawPose(pose) {
    // クリアしてから描画
    ctx_pose.clearRect(0, 0, ctx_pose.canvas.width, ctx_pose.canvas.height);
    ctx_pose.drawImage(webcam_pose.canvas, 0, 0);
    if (pose) {
        const minPartConfidence = 0.5;
        tmPose.drawKeypoints(pose.keypoints, minPartConfidence, ctx_pose);
        tmPose.drawSkeleton(pose.keypoints, minPartConfidence, ctx_pose);
    }
}