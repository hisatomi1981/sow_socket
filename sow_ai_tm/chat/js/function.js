var bool_sending = false;//送信処理中かどうか
var bool_recieving = false;//受信処理中かどうか
var bool_server_sending = false;//サーバーが送信処理中かどうか
var bool_server_recieving = false;//サーバーが受信処理中かどうか

var groupArray1 = new Array();//グループ1の配列
var groupArray2 = new Array();//グループ2の配列
var groupArray3 = new Array();//グループ3の配列

//送信変数
var roomid = "";
var school_id = "";
var chat_group_data = "";
var clientA = "";//自分の番号
var serverNo = "";//サーバーの番号
var clientB = "";//送信先の番号
var groupname = "";//グループ名
var juyo = "false";//重要かどうか
var disp_confirm = "false";//確認画面を出すかどうか
var repeat_kaisu = 1;//繰り返しがあった時の回数
var pwst = "";//パスワード
var sound = "";
var fsize = "";//フォントサイズ
var fcolor = "";//フォント色
var backcolor = "";//背景色
var dispback = "";//ディスプレイ背景色
var ai_returnData = "";//aiで取得した文字列

var cnt =0;//送信プログラムの何番目を処理しているか
var myName = "";//自分の名前
var messageSt = "";//メッセージ内容
var profile_data = "";//プログラムデータを送信する場合のデータ
var profile_data_filename = "";//プログラムデータを送信する場合のファイル名

//受信変数
var allMessage ="";
var recieve_font_size = "";//受信フォントサイズ
var recieve_font_color = "";//受信フォント色
var recieve_back_color = "";//受信背景色
var recieve_disp_color = "";//受信画面色
var recieve_sound = "";//着信音

var s_cnt =0;//サーバプログラムの何番目を処理しているか
var serverpwst = "";//パスワード
var ai_mode = false;//AIへ問い合わせ中かどうか
var server_up_message//データベースへアップするメッセージ（エラーなのかAIで処理したものなのか）
var transferSt = "";//サーバーから送信先へ送るメッセージ
var returnSt1 = "";//サーバーから送信先へ返信するメッセージ１
var returnSt2 = "";//サーバーから送信先へ返信するメッセージ２
var returnSt3 = "";//サーバーから送信先へ返信するメッセージ３
var returnSt4 = "";//サーバーから送信先へ返信するメッセージ４
var returnSt5 = "";//サーバーから送信先へ返信するメッセージ５

var variable_St1 = "";//クライアントの変数１
var variable_St2 = "";//クライアントの変数２
var variable_St3 = "";//クライアントの変数３
var variable_St4 = "";//クライアントの変数４
var variable_St5 = "";//クライアントの変数５

//特殊文字を直す
function escapeHtml(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

//文字列を分解（str：文字列 , no：何番目を取得するか）
function strSplit(str, no, kugiri){
    var strArray = str.split(kugiri);
    return strArray[no];
}
//textareaの内容を配列にセット
function splitByLine() {
    //改行コードを\nに統一
    var text  = document.getElementById('proText').value.replace(/\r\n|\r/g, "\n");
    var lines = text.split( '\n' );
    var outArray = new Array();

    for ( var i = 0; i < lines.length; i++ ) {
        // 空行があれば終了
        if ( lines[i] == '' ) {
            break;
        }
        //lines[i] = lines[i].replace(",","");
        outArray.push( lines[i] +"\n");
    }
    return outArray;
}

//プログラムセット
function set_program(){
	//プログラムの自動保存
	server_autosave();
	//文字プログラムに変換
    outputProData();
    //転送データに変換
    changeSendData();

	var text  = document.getElementById('sendText').value.replace(/\r\n|\r/g, "\n");
	if (text == ""){return;}
	var lines = text.split( '\n' );    
	if (lines.length > 120){ 
		alert("データ転送量が超えています。");
		return;
	}
	//AIが複数あったらエラーに
	if (!ai_multi(lines)) {
		alert("AI機能を複数プログラムすることはできません");
		return;
	}
	
	//改行コードを\nに統一
	var text  = document.getElementById('proText').value.replace(/\r\n|\r/g, "\n");
    var lines = text.split( '\n' );
    for  (var i = 0; i < lines.length; i++) { 
        if (lines[i] == "start" || lines[i] == "server_start"){
            break;
        }
        if (i == lines.length - 1){
            alert("開始命令がありません。");
            throw new Error("エラーメッセージ");
        }
    }	
	
	const div = document.getElementById("settime");	
	div.innerHTML = get_time();	
	
	var addst = document.getElementById('messages').value;//通信ログ
	var timearray = get_time().split( ' ' );//時間を分解	
	var addmessage = timearray[1] + "：プログラムセット\n";
	if (document.getElementById("log_enc").checked){
		var newstdec = Decrypt(addst) + addmessage;	
		addst = Encrypt(newstdec);
	}
	else{						
		addst += addmessage;
	}
	document.form6.logtextarea.value = addst;

	//自動保存
	server_autosave();
}

// 数秒後に実行メソッド
function delayedCall(second, callBack){
  setTimeout(callBack, second);
}
//スクロール位置を一番下へ（クライアント）
function scroll_change(){	
	const div = document.getElementById("messages");
	//div.scrollTop = div.scrollHeight;
	if (div.scrollTop > (div.scrollHeight - div.offsetHeight - 100)) {
  		div.scrollTop = div.scrollHeight;
	}
}

//IPアドレスを取得し、myIPへ格納
//var myIP ="192.168.144.1";
//get_ip();
function get_ip(){
	var globalip = "";
	globalip = localStorage.getItem("globalip");
	if (globalip == null){
		fetch('https://ipinfo.io/json?token=8688571a27554c')
			.then(res => res.json())
			.then(json => myIP = json.ip)
	}
}
//現在時刻を取得
function get_time() {
	const date1 = new Date();
	return date1.getFullYear() + "/" + ("0"+(date1.getMonth() + 1)).slice(-2) + "/" + ("0"+date1.getDate()).slice(-2) + " " + 
			("0"+date1.getHours()).slice(-2) + ":" + ("0"+date1.getMinutes()).slice(-2) + ":" + ("0"+date1.getSeconds() ).slice(-2);
}
//現在時刻を数字だけで取得
function get_time_f() {
	const date1 = new Date();
	let rtime = date1.getHours().toString(10) + date1.getMinutes().toString(10) + date1.getSeconds().toString(10);
	return rtime;
}
//ビジーwaitを使う方法
function sleep(waitMsec) {
  var startMsec = new Date();
 
  // 指定ミリ秒間だけループさせる（CPUは常にビジー状態）
  while (new Date() - startMsec < waitMsec);
}
//メッセージにカンマがあれば全角に
function leavemessage(){
	var str = document.getElementById('mymessage').value;
	// 半角英数字チェック
	if (str.match(/[,]/)) {
	    document.getElementById('mymessage').value = str.replace(',','，');
	}
}
//開始時
function startchat(schoolid,schoolname){
	if (document.getElementById('myno').value == ""){
		alert("生徒番号が入力されていません。");
		return;
	}
	//ネットワーク番号が指定されているとき
	if (document.getElementById('network_no1').value != "" && document.getElementById('network_no2').value != "" && 			
	    document.getElementById('network_no3').value != "" && document.getElementById('network_no4').value != "") {
		const regex = /^((25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.){3}(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])$/;
		var ipn = document.getElementById('network_no1').value +"."+ document.getElementById('network_no2').value +"."+ 
			      document.getElementById('network_no3').value +"."+ document.getElementById('network_no4').value;
		//IPアドレスの形なら
		if (regex.test(ipn)){
			roomid = ipn.split('.').join('');
			//console.log(roomid + "で参加");			
			//ストレージにネットワーク番号を保存
			localStorage.setItem("networkno1",  document.getElementById('network_no1').value);
			localStorage.setItem("networkno2",  document.getElementById('network_no2').value);
			localStorage.setItem("networkno3",  document.getElementById('network_no3').value);
			localStorage.setItem("networkno4",  document.getElementById('network_no4').value);
			//ネットワーク番号設定画面を非表示
			document.getElementById("setup_networkno").style.display ="none";
			//return;
		}
		else{
			return;
		}
	}
	else{
		//ネットワーク番号が指定されていない
		roomid = schoolid;
	}
	console.log(roomid + "で参加");
	//document.title = roomid + "で利用中";
	document.getElementById('dispstate').innerHTML = "通信開始";
	document.getElementById("systemstart").style.display ="none";
	document.getElementById('netnoid').innerHTML = "ネットワーク番号：" + roomid;

	//生徒番号
	var stno = document.getElementById("myno").value;
	document.getElementById("myno").style.display ="none";
	document.getElementById('studentno').innerHTML = stno;
	
	localStorage.setItem("globalip",  roomid);

	//送信時使用
	school_id = schoolid;
	chat_group_data = roomid;
	
	if (schoolname == ""){
		document.getElementById('schooid').innerHTML = "双方向ネットワークアプリ";
	}
	else{
		document.getElementById('schooid').innerHTML = schoolname;
	}
}
//暗号化チェック
function check_Encrypt(){
	let alllog = "";
	var text = document.getElementById("messages").value;
	if (document.getElementById("log_enc").checked){
		document.form6.logtextarea.value = Encrypt(text);
	}
	else{
		document.form6.logtextarea.value = Decrypt(text);
	}	
}
//暗号化
function Encrypt(value){
	return CryptoJS.AES.encrypt(value, "hisatomi").toString();
}
function Decrypt(value){
	return CryptoJS.AES.decrypt(value, "hisatomi").toString(CryptoJS.enc.Utf8);
} 

//利用中のルーム番号を表示
function display_net_no(){
	//myIP.split('.').join('');
	alert('利用中のネットワーク：' + roomid);
}

//文字列から二重引用符（「」）で囲まれた部分だけを取り出す
function extractQuotedText(str) {
    // 正規表現を使って、二重引用符で囲まれたテキストを探す
    const match = str.match(/「(.*?)」/);

    // マッチした場合、その中身（キャプチャグループ）を返す
    if (match) {
        return match[1];
    }

    // マッチしなかった場合、元の文字列を返す
    return str;
}