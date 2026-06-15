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

function renderTeachableMachineWarning() {
    echo '
    <div id="teachablemachine_warning">
        <div class="row justify-content-center">
            TeachableMachineのプログラムを動作させるには「転送」ボタンで動作実行を行います。<br>
            「実行」ボタンは押さないでください。
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

/**
 * ============================================================
 *  顔認証UIレンダ関数（machine_learning.php に追記）
 *  既存の renderTeachableMachinePresetUI() などと同じ流儀
 *  
 *  使い方： machine_learning.php の末尾（最後の閉じタグの前）に
 *           この関数定義をそのまま貼り付けてください。
 *           index.php / ipad.php で renderFaceAuthUI() を呼ぶこと
 *           で UI が描画されます。
 *
 *  出力要素ID（JS 側で参照）：
 *    - face_auth                 ：トップレベル div（表示/非表示の対象）
 *    - face_video                ：カメラ映像
 *    - face_overlay              ：顔枠描画用キャンバス
 *    - face_status               ：認証ステータス文言
 *    - face_threshold_value      ：閾値の数値表示
 *    - face_register_btn         ：顔登録ボタン
 *    - face_register_status      ：登録ステータス文言
 *    - class_label_face          ：認識結果出力コンテナ
 *                                  → ここに「faceclass:XX.X%」を吐き出すので
 *                                    function_teachablemachine.js の
 *                                    doesLabelExist("faceclass") が動く
 * ============================================================
 */
function renderFaceAuthUI() {
    echo '
    <div id="face_auth">
        <div class="row justify-content-center align-items-center">

            <!-- 左：カメラとカメラ開始ボタン -->
            <div class="col-auto d-flex flex-column justify-content-center align-items-center">
                <div style="margin-top:5px">
                    <button id="face_btnStart" class="formbutton camerabutton">①カメラ開始</button>
                </div>
                <div style="margin-top:12px; position:relative; width:320px; height:240px;">
                    <video id="face_video" autoplay muted playsinline
                           style="width:320px; height:240px; background:#000;
                                  border:1px solid #ccc; border-radius:8px; display:block;"></video>
                    <canvas id="face_overlay"
                            style="position:absolute; top:0; left:0;
                                   width:320px; height:240px; pointer-events:none;"></canvas>
                </div>
                <div id="face_camera_status" style="font-size:0.85em; color:#666; margin-top:4px;">
                    カメラ未起動
                </div>
            </div>

            <!-- 中央：クラス操作＋認識結果％ -->
            <div class="col-auto align-self-start">
                <div style="margin-top:5px">
                    <input id="face_className" type="text" placeholder="②新規クラス名を入力" />
                    <button id="face_btnAddClass" class="formbutton etcbuttonColor">③クラスを作成</button>
                </div>
                <div style="margin-top:8px">
                    <label for="face_classSelect" class="label">クラスの選択：</label>
                    <select id="face_classSelect" style="min-width:200px">
                        <option value="" selected>④（未選択）</option>
                    </select>
                </div>
                <div style="margin-top:5px">
                    <span>各クラスに 1 枚以上の顔を登録してください（複数枚推奨）</span>
                </div>
                <div style="margin-top:5px">
                    <span id="face_result"></span>
                </div>
                <div style="margin-top:5px">
                    <span id="face_dispstatus">face-api.js モデル読み込み中...</span>
                </div>

                <!-- ★認識結果％（旧：認証閾値スライダーの場所） -->
                <div style="margin-top:8px;">
                    <span id="class_label_face" class="result-text margin10 font_red fontbold"></span>
                </div>
            </div>

            <!-- 右：⑤⑥⑦ボタン縦並び -->
            <div class="col-auto align-self-start">
                <div class="d-flex flex-column" style="gap:8px; margin-top:5px;">
                    <button id="face_btnRegister" class="formbutton studybuttonColor studybutton" disabled>
                        ⑤顔を追加登録<br>(選択したクラス)
                    </button>
                    <button id="face_btnRecStart" class="formbutton etcbuttonColor2 studybutton" disabled>
                        ⑥認識開始
                    </button>
                    <button id="face_btnRecStop"  class="formbutton etcbuttonColor2 studybutton" disabled>
                        ⑦認識終了
                    </button>
                </div>
            </div>
        </div>

        <div class="row" style="height: 5px"></div>

        <div class="row justify-content-center">
            <div class="col-3 text-center">
            </div>
            <div class="col-auto">
                <button type="button" id="tmbtn4" class="printbtn" onclick="faceAuthNoDisplay()">閉じる</button>
            </div>
            <div class="col-3 text-center">
                <button type="button" id="btn-update" class="printbtn" onclick="stopTeachablePrograms()">プログラムの停止</button>
            </div>
        </div>
    </div>
    ';
}
// ================================================================
//  表情認識UI（feature 9）
//  gesture_tenso.js / function_face_auth.js と同じレイアウト規則
// ================================================================
function renderFaceGestureUI() {
    echo '
    <div id="face_gesture" style="display:none;">
        <div class="row justify-content-center align-items-center">

            <!-- 左：カメラ映像 -->
            <div class="col-auto d-flex flex-column justify-content-center align-items-center">
                <div style="margin-top:5px">
                    <button id="fg_btnStart" class="formbutton camerabutton">①カメラ開始</button>
                </div>
                <div style="margin-top:12px; position:relative; width:320px; height:240px;">
                    <video id="fg_cam" autoplay muted playsinline
                           style="width:320px; height:240px; background:#000;
                                  border:1px solid #ccc; border-radius:8px; display:none;"></video>
                    <canvas id="fg_overlay"
                            style="position:absolute; top:0; left:0;
                                   width:320px; height:240px;
                                   border:1px solid #ccc; border-radius:8px;"></canvas>
                </div>
                <div id="fg_dispstatus" style="font-size:0.85em; color:#666; margin-top:4px;">
                    カメラ未起動
                </div>
            </div>

            <!-- 中央：クラス操作＋確率表示 -->
            <div class="col-auto align-self-start">
                <div style="margin-top:5px">
                    <input id="fg_className" type="text" placeholder="②表情クラス名を入力" />
                    <button id="fg_btnAddClass" class="formbutton etcbuttonColor">③クラスを作成</button>
                </div>
                <div style="margin-top:8px">
                    <label for="fg_classSelect" class="label">クラスの選択：</label>
                    <select id="fg_classSelect" style="min-width:200px">
                        <option value="" selected>④（未選択）</option>
                    </select>
                </div>
                <div style="margin-top:5px">
                    <span>表情をキープしたままサンプル追加→各クラス5枚以上推奨</span>
                </div>
                <div style="margin-top:5px">
                    <span id="fg_result"></span>
                </div>
                <div style="margin-top:5px">
                    <span id="fg_dispstatus2"></span>
                </div>
                <!-- 確率表示 -->
                <div style="margin-top:8px;">
                    <div id="fg_liveClass" class="result-text margin10 font_red fontbold"></div>
                </div>
            </div>

            <!-- 右：ボタン縦並び -->
            <div class="col-auto align-self-start">
                <div class="d-flex flex-column" style="gap:8px; margin-top:5px;">
                    <button id="fg_btnTrainSelected" class="formbutton studybuttonColor studybutton">
                        ⑤サンプル追加<br>(選択したクラス)
                    </button>
                    <button id="fg_btnTrainStart" class="formbutton studybuttonColor studybutton">
                        ⑥学習する
                    </button>
                    <button id="fg_btnRecStart" class="formbutton etcbuttonColor2 studybutton" disabled>
                        ⑦認識開始
                    </button>
                    <button id="fg_btnRecStop"  class="formbutton etcbuttonColor2 studybutton" disabled>
                        ⑧認識終了
                    </button>
                </div>
            </div>

        </div>

        <div class="row" style="height: 5px"></div>

        <div class="row justify-content-center">
            <div class="col-3 text-center"></div>
            <div class="col-auto">
                <button type="button" class="printbtn" onclick="faceGestureNoDisplay()">閉じる</button>
            </div>
            <div class="col-3 text-center">
                <button type="button" class="printbtn" onclick="stopTeachablePrograms()">プログラムの停止</button>
            </div>
        </div>
    </div>
    ';
}
?>
