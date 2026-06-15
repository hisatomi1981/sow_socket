<?php
function renderGestureUI() {
    echo '
    <div id="gesture">
        <div class="row justify-content-center align-items-center">
            <div class="col-auto d-flex flex-column justify-content-center align-items-center">
                <div style="margin-top:5px">
                    <button id="btnStart" class="formbutton camerabutton">①カメラ開始</button>
                </div>
                <div style="margin-top:12px">
                    <!-- カメラ映像は非表示（学習・推論は内部で利用） 
                    <video id="cam" playsinline autoplay muted></video>-->
                    <video id="cam" style="width:1px; height:1px; opacity:0;" muted autoplay></video>
                    <canvas id="overlay" width="320" height="240"></canvas>
                </div>
            </div>
            <div class="col-auto align-self-start">
                <div style="margin-top:5px">
                    <input id="className" type="text" placeholder="②新規クラス名を入力" />
                    <button id="btnAddClass" class="formbutton etcbuttonColor">③クラスを作成</button>
                </div>
                <div style="margin-top:8px">
                    <label for="classSelect" class="label">クラスの選択：</label>
                    <select id="classSelect" style="min-width:200px">
                        <option value="" selected>④（未選択）</option>
                    </select>
                </div>
                <div style="margin-top:5px">
                    <span>クラスは2つ以上で各クラスに3つ以上学習させてください</span>
                </div>	
                <div style="margin-top:5px">
                    <span id="result"></span>
                </div>
                <div style="margin-top:5px">
                    <span id="dispstatus"></span>
                </div>		
                <div>
                    <span id="liveClass" style="margin-top:5px" class="result-text margin10 font_red fontbold"></span>
                </div>
            </div>
            <div class="col-auto d-flex align-items-center">
                <div class="train-buttons">
                    <button id="btnTrainSelected" class="formbutton studybuttonColor studybutton">⑤学習<br>(選択したクラス)</button>
                    <button id="btnTrainStart" class="formbutton etcbuttonColor studystartbutton">⑥学習終了<br>(認識へ進みます)</button>
                </div>
            </div>
            <div class="col-auto d-flex align-items-center">
                <div class="train-buttons">
                    <button id="btnRecStart" class="formbutton etcbuttonColor2 studybutton" disabled>⑦認識開始</button>
                    <button id="btnRecStop"  class="formbutton etcbuttonColor2 studybutton" disabled>⑧認識終了</button>
                    <div class="label"></div>
                </div>
            </div>
        </div>
        <div class="row" style="height: 5px"></div>
        <div class="row justify-content-center">
            <div class="col-auto">
                <button type="button" id="tmbtn1" class="printbtn" onclick="gestureNoDisplay()">閉じる</button>
            </div>
        </div>
    </div>
    ';
}

function renderTeachableMachineUI() {
    echo '
    <div id="teachablemachine">
        <div class="row justify-content-center">
            <input type="text" id="model-url" placeholder="Teachable MachineでエクスポートしたモデルのURL" style="width: 500px;">
            <div class="col-auto">
                <button type="button" onclick="init_camera()" class="formbutton camerabutton">画像を認識する</button>
            </div>          
        </div>
        <div class="row" style="height: 5px"></div>
        <div class="t_v_center t_center d-flex justify-content-center align-items-center gap-3">
            <span id="webcam-container" class="margin10"></span>
            <span>
                <div class="mb-2"></div>
                <div>
                    <span id="class_label" class="result-text margin10 font_red fontbold"></span>
                </div>
            </span>
        </div>
        <div class="row" style="height: 5px"></div>
        <div class="row justify-content-center">
            <div class="col-auto">
                <button type="button" id="tmbtn1" class="printbtn" onclick="teachablemachineNoDisplay()">閉じる</button>
            </div>
        </div>
    </div>
    ';
}

function renderTeachableMachinePoseUI() {
    echo '
    <div id="teachablemachine_pose">
        <div class="row justify-content-center">
            <input type="text" id="model-url_pose" placeholder="Teachable MachineでエクスポートしたモデルのURL" style="width: 500px;">
            <div class="col-auto">
                <button type="button" onclick="init_pose()" class="formbutton camerabutton">ポーズを認識する</button>
            </div>   
        </div>
        <div class="row" style="height: 5px"></div>
        <div class="t_v_center t_center d-flex justify-content-center align-items-center gap-3">
            <div><canvas id="canvas_pose"></canvas></div>
            <span id="webcam-container_pose" class="margin10"></span>
            <div class="d-flex flex-column align-items-center">
                <div class="mb-2"></div>
                <span id="class_label_pose" class="result-text margin10 font_red fontbold"></span>
            </div>
        </div>
        <div class="row" style="height: 5px"></div>
        <div class="row justify-content-center">
            <div class="col-auto">
                <button type="button" id="tmbtn2" class="printbtn" onclick="teachablemachineNoDisplay_pose()">閉じる</button>
            </div>
        </div>
    </div>
    ';
}

function renderTeachableMachinePresetUI() {
    echo '
    <div id="teachablemachine_preset">
        <div class="row justify-content-center">
            <div class="col-auto">
                <button type="button" onclick="init_preset()" class="formbutton camerabutton">
                    カメラ起動し、手の動きを検知する
                </button>
            </div>
        </div>
        <div class="row" style="height: 5px"></div>
        <div class="t_v_center t_center d-flex justify-content-center align-items-center gap-3">
            <div><canvas id="canvas_preset"></canvas></div>
            <span id="webcam-container_preset" class="margin10"></span>
            <div class="d-flex flex-column align-items-center">
                <div class="mb-2"></div>
                <span id="class_label_preset" class="result-text margin10 font_red fontbold"></span>
            </div>
        </div>
        <div class="row" style="height: 5px"></div>
        <div class="row justify-content-center">
            <div class="col-auto">
                <button type="button" id="tmbtn3" class="printbtn" onclick="teachablemachineNoDisplay_preset()">閉じる</button>
            </div>
        </div>
    </div>
    ';
}
?>
