//プログラムの印刷
function printBlock() {
	//idを取得
	let textkoso = document.getElementById('print-text-koso').value.replace(/\r\n|\r|\n/g, '<br>');
	if (textkoso == ""){textkoso = "<br><br><br><br><br>"}
	let koso = document.getElementById('print-koso');
	koso.innerHTML = textkoso;
	let textsiyo = document.getElementById('print-text-siyo').value.replace(/\r\n|\r|\n/g, '<br>');
	if (textsiyo == ""){textsiyo = "<br><br><br><br><br>"}
	let siyo = document.getElementById('print-siyo');
	siyo.innerHTML = textsiyo;
	let textkuhu = document.getElementById('print-text-kuhu').value.replace(/\r\n|\r|\n/g, '<br>');
	if (textkuhu == ""){textkuhu = "<br><br><br><br><br>"}
	let kuhu = document.getElementById('print-kuhu');
	kuhu.innerHTML = textkuhu;
	let textkanso = document.getElementById('print-text-kanso').value.replace(/\r\n|\r|\n/g, '<br>');
	if (textkanso == ""){textkanso = "<br><br><br><br><br>"}
	let kanso = document.getElementById('print-kanso');
	kanso.innerHTML = textkanso;
	//表示非表示を反転
	document.getElementById("print-koso").style.display ="block";
	document.getElementById("print-siyo").style.display ="block";
	document.getElementById("print-kuhu").style.display ="block";
	document.getElementById("print-kanso").style.display ="block";
	document.getElementById("print-text-koso").style.display ="none";
	document.getElementById("print-text-siyo").style.display ="none";
	document.getElementById("print-text-kuhu").style.display ="none";
	document.getElementById("print-text-kanso").style.display ="none";
	window.print();	//window全体を印刷
	//表示に戻す
	document.getElementById("print-koso").style.display ="none";
	document.getElementById("print-siyo").style.display ="none";
	document.getElementById("print-kuhu").style.display ="none";
	document.getElementById("print-kanso").style.display ="none";
	document.getElementById("print-text-koso").style.display ="block";
	document.getElementById("print-text-siyo").style.display ="block";
	document.getElementById("print-text-kuhu").style.display ="block";
	document.getElementById("print-text-kanso").style.display ="block";
	printNoDisplay();
}
//プロンプト入力エリアの表示
function promptDisplay() {
	document.getElementById("setprompt").style.display ="block";
}
//プロンプト入力の非表示
function promptNoDisplay() {
	document.getElementById("setprompt").style.display ="none";
}
//グループ追加エリアの表示
function groupDisplay() {
	document.getElementById("setgroup").style.display ="block";
}
//グループ追加の非表示
function groupNoDisplay() {
	document.getElementById("setgroup").style.display ="none";
}
//画像認識エリアの表示
function gestureDisplay() {
	teachablemachineNoDisplay();
	teachablemachineNoDisplay_pose();
	teachablemachineNoDisplay_preset();
	document.getElementById("gesture").style.display ="block";
}
///画像認識エリアの非表示
function gestureNoDisplay() {
	const el = document.getElementById("gesture");
	if (el) {
	el.style.display = "none";
	}
}
//表情認識エリアの表示
function faceGestureDisplay() {
    if (typeof gestureNoDisplay === 'function')                  gestureNoDisplay();
    if (typeof teachablemachineNoDisplay === 'function')         teachablemachineNoDisplay();
    if (typeof teachablemachineNoDisplay_pose === 'function')    teachablemachineNoDisplay_pose();
    if (typeof teachablemachineNoDisplay_preset === 'function')  teachablemachineNoDisplay_preset();
    if (typeof faceAuthNoDisplay === 'function')                 faceAuthNoDisplay();
    const warn = document.getElementById('teachablemachine_warning');
    if (warn) warn.style.display = 'block';
    const el = document.getElementById('face_gesture');
    if (el) el.style.display = 'block';
	face_setupUI();
}
//表情認識エリアの非表示
function faceGestureNoDisplay() {
    const el = document.getElementById('face_gesture');
    if (el) el.style.display = 'none';
    if (typeof face_stopRecognition === 'function') face_stopRecognition();
	document.getElementById("teachablemachine_warning").style.display ="none";
}
//TeachableMachine（画像）エリアの表示
function teachablemachineDisplay() {
	gestureNoDisplay();
	teachablemachineNoDisplay_pose();
	teachablemachineNoDisplay_preset();
	document.getElementById("teachablemachine").style.display ="block";
}
//TeachableMachine（画像）エリアの非表示
function teachablemachineNoDisplay() {
	const el = document.getElementById("teachablemachine");
	if (el) {
	el.style.display = "none";
	}
}
//TeachableMachine（ポーズ）エリアの表示
function teachablemachineDisplay_pose() {
	gestureNoDisplay();
	teachablemachineNoDisplay();
	teachablemachineNoDisplay_preset();
	document.getElementById("teachablemachine_pose").style.display ="block";
}
//TeachableMachine（ポーズ）エリアの非表示
function teachablemachineNoDisplay_pose() {
	const el = document.getElementById("teachablemachine_pose");
	if (el) {
	el.style.display = "none";
	}
}
//TeachableMachine（プリセット）エリアの表示
function teachablemachineDisplay_preset() {
	gestureNoDisplay();
	teachablemachineNoDisplay();
	teachablemachineNoDisplay_pose();
	document.getElementById("teachablemachine_preset").style.display ="block";
}
//TeachableMachine（プリセット）エリアの非表示
function teachablemachineNoDisplay_preset() {
	const el = document.getElementById("teachablemachine_preset");
	if (el) {
	el.style.display = "none";
	}
}
//印刷エリアの表示
function printDisplay() {
	document.getElementById("printhead").style.display ="block";
}
//印刷エリアの非表示
function printNoDisplay() {
	document.getElementById("printhead").style.display ="none";
}
//設定エリア(ネットワーク番号)の非表示
function change_network_no_notDisplay() {
	document.getElementById("setup_networkno").style.display ="none";
}
//設定エリア(ネットワーク番号)の表示
function change_network_no_Display() {
	document.getElementById("setup_networkno").style.display ="block";
}
//サーバからクライアントへ送るエリアの表示
function print_cli_send() {
	document.getElementById("cli_send").style.display ="block";
}
//サーバからクライアントへ送るエリアの非表示
function printNo_cli_send() {
	document.getElementById("cli_send").style.display ="none";
}
//ネットワーク番号の初期化
function network_no_default() {
	document.getElementById('network_no1').value = "192";
	document.getElementById('network_no2').value = "168";
	document.getElementById('network_no3').value = "";
	document.getElementById('network_no4').value = "";
}
//生徒番号からカーソルが離れるとき
function leaveno(){
	var str = document.getElementById('myno').value;
	// 半角英数字チェック
	if (str.match(/^[a-zA-Z0-9!-/:-@¥[-`{-~]*$/)) { 
	    localStorage.setItem("seitono",  document.getElementById('myno').value);
	} else {
		document.getElementById('myno').value = "";
		alert('半角数字で入力して下さい');
	}
}
//名前からカーソルが離れるとき
function leavename(){
	localStorage.setItem("seitoname",  document.getElementById('myname').value);
}

//メッセージエリアの背景色
function changeBoxColor( newColor ) {
    document.getElementById('messages').style.backgroundColor = newColor;
}