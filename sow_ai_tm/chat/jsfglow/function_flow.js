var command_Array = new Array();
var sendData_Array = new Array();
var recieveData_Array = new Array();

//全部の配列から送信だけの配列を作成　sendData_Arrayにセット
function set_send_Data_Array(){
	//Next_NoとNext_No2の数字の命令番号を保持
	var nextData_Array = new Array();
	//検索するリスト
	var serchList = new Array();
	//最初の検索リストに追加
	serchList.push("-1");
	//送信命令の命令番号をserchListに追加
	while ( serchList.length > 0 ) {
		//プログラムがない場合は終わる
		if (command_Array.length == 0){return;}
		//配列から検索番号を代入
		var searchNo = serchList[0];
		//配列を検索し
		for ( var i = 0; i < command_Array.length; i++ ) {
			//各命令の要素をカンマで分解("-1,Start,,,,-3,,0-0-0-0-0-0")
			var comm_data = command_Array[i].split( ',' );
			//送信プログラムがなくて受信のみの場合は無限ループになるので抜ける
			if (i == 0 && comm_data[0] == "-11"){return;}
			if (comm_data[0] == searchNo){
				//命令番号を配列に代入
				nextData_Array.push(comm_data[0]);
				//Next_Noを検索配列に追加
				if (comm_data[5] != ""){
					//nextData_Arrayになければ追加
					if (bool_searched(nextData_Array, comm_data[5]) ==false){
						serchList.push(comm_data[5]);
					}
				}
				//Next_No2があれば配列の追加
				if (comm_data[6] != ""){
					//nextData_Arrayになければ追加
					if (bool_searched(nextData_Array, comm_data[6]) ==false){
						serchList.push(comm_data[6]);
					}
				}
				//検索リストの先頭を削除
				serchList.shift();
				break;
			}
		}
	}
	sendData_Array.length =0;
	//nextData_Arrayの命令番号だけをsendData_Arrayに抽出
	for ( var i = 0; i < nextData_Array.length; i++ ) {
		for ( var j = 0; j < command_Array.length; j++ ) {
			var comm_data = command_Array[j].split( ',' );
			if (nextData_Array[i] == comm_data[0]){
				sendData_Array.push(command_Array[j]);
				break;
			}
		}
	}
}
//全部の配列から受信だけの配列を作成　recieveData_Arrayにセット
function set_recieve_Data_Array(){
	//Next_NoとNext_No2の数字の命令番号を保持
	var nextData_Array = new Array();
	//検索するリスト
	var serchList = new Array();
	//受信開始命令の位置を取得
	for ( var i = 0; i < command_Array.length; i++ ){
		var comm_data = command_Array[i].split( ',' );
		if (comm_data[1] == "RecieveStart"){
			serchList.push(comm_data[0]);
		}		
	}
	//送信命令の命令番号をserchListに追加
	while ( serchList.length > 0 ) {
		//配列から検索番号を代入
		var searchNo = serchList[0];
		//配列を検索し
		for ( var i = 0; i < command_Array.length; i++ ) {
			//各命令の要素をカンマで分解("-1,Start,,,,-3,,0-0-0-0-0-0")
			var comm_data = command_Array[i].split( ',' );
			if (comm_data[0] == searchNo){
				//命令番号を配列に代入
				nextData_Array.push(comm_data[0]);
				//Next_Noを検索配列に追加
				if (comm_data[5] != ""){
					//nextData_Arrayになければ追加
					if (bool_searched(nextData_Array, comm_data[5]) ==false){
						serchList.push(comm_data[5]);
					}
				}
				//Next_No2があれば配列の追加
				if (comm_data[6] != ""){
					//nextData_Arrayになければ追加
					if (bool_searched(nextData_Array, comm_data[6]) ==false){
						serchList.push(comm_data[6]);
					}
				}
				//検索リストの先頭を削除
				serchList.shift();
				break;
			}
		}
	}
	recieveData_Array.length =0;
	//nextData_Arrayの命令番号だけをrecieveData_Arrayに抽出
	for ( var i = 0; i < nextData_Array.length; i++ ) {
		for ( var j = 0; j < command_Array.length; j++ ) {
			var comm_data = command_Array[j].split( ',' );
			if (nextData_Array[i] == comm_data[0]){
				recieveData_Array.push(command_Array[j]);
				break;
			}
		}
	}
}
//noの番号がdataarrayにすでにあるかどうか
function bool_searched(dataarray, no){
	for ( var i = 0; i < dataarray.length; i++ ) {
		if (dataarray[i] == no){
			return true;
		}
	}
	return false;
}
//命令名、パラメータから転送番号を取得
function Get_ComSendNo(ComName, para){
	var para_data = para.split('-');
	var komoku = para_data[0];
	if (ComName == "Start") {return "600";}
	else if (ComName == "End") {return "601";}
	else if (ComName == "Message") {//メッセージ
		if (komoku == "0") { return "620"; }
		else if (komoku == "1") { return "626"; }
		else if (komoku == "2") { return "622"; }
		else if (komoku == "3") { return "627"; }
		else if (komoku == "4") { return "623"; }
		else if (komoku == "5") { return "628"; }
		else if (komoku == "6") { return "450"; }
		else { return "451"; }
	}
	else if (ComName == "AI") {//AI
		if (komoku == "2") { return "632"; }
		else { return "633"; }
	}
	else if (ComName == "Sound") {//音
		return "670";
	}
	else if (ComName == "Font") {//フォント
		if (komoku == "0") { return "660"; }
		else if (komoku == "1") { return "661"; }
		else if (komoku == "2") { return "662"; }
		else { return "663"; }
	}
	else if (ComName == "Variable") {//変数
		if (komoku == "0") { return "680"; }
		else if (komoku == "1") { return "681"; }
		else if (komoku == "2") { return "682"; }
		else if (komoku == "3") { return "683"; }
		else { return "684"; }
	}
	else if (ComName == "Setup") { //設定
		if (komoku == "0") { return "641"; }
		else if (komoku == "1") { return "640"; }
		else if (komoku == "1") { return "642"; }
		else { return "643"; }
	}
	else if (ComName == "Sensor") {//信号待ち
		if (komoku == "0") { return "420"; }
		else if (komoku == "1") { return "421"; }
		else if (komoku == "2") { return "422"; }
		else if (komoku == "3") { return "423"; }
		else if (komoku == "4") { return "424"; }
		else if (komoku == "5") { return "425"; }
		else { return "426"; }
	}
	else if (ComName == "Conditional") { //条件分岐
		if (komoku == "0") { return "690"; }
		else if (komoku == "1") { return "691"; }
		else if (komoku == "2") { return "692"; }
		else if (komoku == "3") { return "693"; }
		else if (komoku == "4") { return "694"; }
		else if (komoku == "5") { return "695"; }
		else if (komoku == "6") { return "696"; }
		else if (komoku == "7") { return "697"; }
		else if (komoku == "8") { return "698"; }
		else if (komoku == "9") { return "699"; }
		else if (komoku == "10") { return "701"; }
		else { return "702"; }
	}
	else if (ComName == "Repeat1") {//繰り返し
		return "650";
	}
	else if (ComName == "Repeat2") {//繰り返し
		return "651";
	}
}
//No(命令番号)が何バイト目にあるか　（送信データ用）
function Get_Byte_Adrress(dataarray, no){	
	//console.log(no);
	var totaladdr = 0;
	for ( var i = 0; i < dataarray.length; i++ ) {
		//命令があれば
		var comm_data = dataarray[i].split(',');
		
		if (comm_data[0] == no){return totaladdr;}
		
		if (comm_data[1] != ""){
			totaladdr += Get_Command_Byte(comm_data[1],comm_data[7]);
			//console.log(totaladdr);
		}
	}
	return totaladdr;
}
//命令ごとの必要バイト数
function Get_Command_Byte(comname, para){	
	var yoso = para.split('-');
	//メッセージ
	if (comname == "Message") {
		if (yoso[0] == "0" || yoso[0] == "1" || yoso[0] == "2" || yoso[0] == "3" || yoso[0] == "6" || yoso[0] == "7"){return 4;}
		else {return 3;}
	}
	//AI
	else if (comname == "AI") {
		if (yoso[0] == "2"){return 4;}
		else {return 3;}
	}
	//音
	else if (comname == "Sound") {
		return 3;		
	}
	//フォント
	else if (comname == "Font") {
		return 3;
	}
	//変数
	else if (comname == "Variable") {
		return 3;
	}
	//設定
	else if (comname == "Setup") {
		if (yoso[0] == "0"){return 3;}
		else if (yoso[0] == "1"){return 3;}
		else if (yoso[0] == "2"){return 2;}
		else {return 2;}
	}
	//信号待ち
	else if (comname == "Sensor") {
		if (yoso[0] == "6"){return 2;}//信号待ち
		if (yoso[0] == "0" || yoso[0] == "1"){return 4;}//時刻待ち　teachableMachine待ち
		else {return 3;}
	}
	//条件分岐
	else if (comname == "Conditional") {
		if (yoso[0] == "0" || yoso[0] == "1" || yoso[0] == "4" || yoso[0] == "5"){
			return 5;
		}
		else if (yoso[0] == "6" || yoso[0] == "7" || yoso[0] == "8" || yoso[0] == "9") {
			return 4;
		}
		else {return 3;}
	}
	//繰り返し
	else if (comname == "Repeat1") {
		return 3;
	}
	//繰り返し終了
	else if (comname == "Repeat2") {
		return 2;
	}
	//start
	else if (comname == "Start") { 
		return 2;
	}
	//end
	else if (comname == "End") { 
		return 1;
	}
	//受信start
	else if (comname == "RecieveStart") { 
		return 2;
	}
	//受信end
	else if (comname == "RecieveEnd") { 
		return 1;
	}
	//受信条件分岐
	else if (comname == "Recieve_Conditional") {
		if (yoso[0] == "0" || yoso[0] == "1" || yoso[0] == "4" || yoso[0] == "5") {
			return 4;
		}
		else {return 3;}
	}
	//
	else{
		return 1;
	}
}
var datastkv = "";
var myDiagram;
//コマンドがクリックされたらパラメータをtextariaに表示
function set_para(data, mD){
	datastkv = data;
	myDiagram = mD;
	//ドラッグ元がクリックされたらパラメータを非表示
	if (strSplit(datastkv.loc, 0, " ") == "0"){		
		//パラメータ設定をすべて非表示に
		para_label_notdisplay();
		return;
	}
	//パラメータを分解
	var eachdata = datastkv.parameter.split( '-' );
	//パラメータラベルを表示
	para_label_display(datastkv.category, datastkv.text);	
	//ラベルに代入
	if (datastkv.category == "Message"){
		//メッセージの場合
		if (eachdata[0] == "0" || eachdata[0] == "1"){
			if (eachdata[1].indexOf('グループ') != -1){
				document.getElementById('m_para_sendno').value = "";
			}
			else{
				document.getElementById('m_para_sendno').value = eachdata[1];
			}
			//メッセージ
			if (eachdata[2] == "入力メッセージ"){
				document.getElementById('m_para_mess').selectedIndex = 0;
			}
			else if (eachdata[2] == "変数1"){
				document.getElementById('m_para_mess').selectedIndex = 1;
			}
			else if (eachdata[2] == "変数2"){
				document.getElementById('m_para_mess').selectedIndex = 2;
			}
			else if (eachdata[2] == "変数3"){
				document.getElementById('m_para_mess').selectedIndex = 3;
			}
			else if (eachdata[2] == "変数4"){
				document.getElementById('m_para_mess').selectedIndex = 4;
			}
			else {
				document.getElementById('m_para_mess').selectedIndex = 5;
			}
		}
		else if (eachdata[0] == "2" || eachdata[0] == "3"){
			if (eachdata[1].indexOf('グループ') != -1){
				document.getElementById('m_para_sendno').value = "";
			}
			else{
				document.getElementById('m_para_sendno').value = eachdata[1];
			}
		}
		else if (eachdata[0] == "4" || eachdata[0] == "5"){
			if (eachdata[1].indexOf('グループ') != -1){
				document.getElementById('m_para_sendno').value = "";
			}
			else{
				document.getElementById('m_para_sendno').value = eachdata[1];
			}
		}
		else{
			if (eachdata[1].indexOf('グループ') != -1){
				document.getElementById('m_para_sendno').value = "";
			}
			else{
				document.getElementById('m_para_sendno').value = eachdata[1];
				document.getElementById('m_para_file').selectedIndex = Number(eachdata[2]) - 1;
			}
		}
	}
	//AI
	else if (datastkv.category == "AI"){
		//AIで変換なら
		if (eachdata[0] == "2"){
			document.getElementById('ai_change_st').value = eachdata[1];
		}
	}
	else if (datastkv.category == "Sound"){
		document.getElementById('sound_para_sound').selectedIndex = Number(eachdata[1]);	
	}
	else if (datastkv.category == "Font"){
		//フォントサイズ
		if (eachdata[0] == "0"){
			document.getElementById('disp_para_fontsize').selectedIndex = Number(eachdata[1]) - 8;	
		}
		//文字色
		else if (eachdata[0] == "1"){
			document.getElementById('disp_para_fontcolor').value = eachdata[1];			
		}
		//メッセージ背景色
		else if (eachdata[0] == "2"){
			document.getElementById('disp_para_messagebackcolor').value = eachdata[1];			
		}
		//画面背景色
		else {
			document.getElementById('disp_para_dispbackcolor').value = eachdata[1];	
		}
	}
	else if (datastkv.category == "Variable"){
		document.getElementById('variable_value').value = eachdata[1];
	}
	else if (datastkv.category == "Setup"){
		//サーバ設定
		if (eachdata[0] == "0"){
			document.getElementById('setup_para_server').value = eachdata[1];
		}
		//パスワード
		else if (eachdata[0] == "1"){
			document.getElementById('setup_para_pass').value = eachdata[1];
		}
	}
	else if (datastkv.category == "Sensor"){
		if (datastkv.text.indexOf('明') != -1 || datastkv.text.indexOf('暗') != -1 || datastkv.text.indexOf('度') != -1){
			document.getElementById('sensor_paralight').value = eachdata[1];	
		}
		else if (datastkv.text.indexOf('時') != -1){
			document.getElementById('sensor_para_ji').selectedIndex = Number(eachdata[1]);
			document.getElementById('sensor_para_hun').selectedIndex = Number(eachdata[2]);
		}
		//TeachableMachine
		else{
			document.getElementById('sensor_paralight').value = eachdata[1];
			document.getElementById('sensor_paravalue').value = eachdata[2];
		}
	}
	else if (datastkv.category == "Repeat1"){
			document.getElementById('repeat_paracnt').value = eachdata[1];		
	}
	else if (datastkv.category == "Conditional"){
		if (eachdata[0] == "0" || eachdata[0] == "1"){
			document.getElementById('if_para_ji').selectedIndex = Number(eachdata[1]);
			document.getElementById('if_para_hun').selectedIndex = Number(eachdata[2]);
		}
		//TeachableMachine
		else if (eachdata[0] == "4" || eachdata[0] == "5"){
			document.getElementById('if_paraclass').value = eachdata[1];
			document.getElementById('if_paravalue').value = eachdata[2];
		}
	}
	else if (datastkv.category == "Recieve_Conditional"){
		if (eachdata[0] == "0" || eachdata[0] == "1"){
			document.getElementById('if_para_r_ji').selectedIndex = Number(eachdata[1]) - 1;
		}
		else if (eachdata[0] == "4" || eachdata[0] == "5"){
			document.getElementById('if_para_r_seitono').selectedIndex = eachdata[1];
		}	
	}
}
//パラメータラベルをすべて非表示
function para_label_notdisplay(){
	document.getElementById("para_message").style.display ="none";
	document.getElementById("para_aichange").style.display ="none";
	document.getElementById("para_sound").style.display ="none";
	document.getElementById("para_variable").style.display ="none";
	document.getElementById("para_setup").style.display ="none";
	document.getElementById("para_display").style.display ="none";
	document.getElementById("para_sensor").style.display ="none";
	document.getElementById("para_repeat").style.display ="none";
	document.getElementById("para_if").style.display ="none";
	document.getElementById("para_if_r").style.display ="none";
	document.getElementById("para_btn").style.display ="none";	
}
//パラメータラベルを表示
function para_label_display(cate,textst){
	//パラメータ設定をすべて非表示に
	para_label_notdisplay();
	
	//更新ボタンの表示
	if (cate != "Start" && cate != "End" && cate != "Repeat2" && cate != "RecieveStart" && 
		cate != "RecieveEnd" && cate != "RecieveEnd" && cate != "Repeat2"){
		document.getElementById("para_btn").style.display ="block";
	}
	if (cate == "Message"){
		//設定画面を表示
		document.getElementById("para_message").style.display ="block";
		document.getElementById("paramessage").style.display ="block";
		//メッセージ
		if (textst.indexOf('\"を') != -1 || textst.indexOf('メッセージ') != -1 || textst.indexOf('変数') != -1){
			document.getElementById("parablock_file").style.display ="none";
			document.getElementById("parablock_mess").style.display ="block";
			if (textst.indexOf('グループ') != -1){
				//document.getElementById("m_para_sendno").style.display ="none";
				document.getElementById('paramessage').selectedIndex  = 1;
				document.getElementById("parablock_sendno").style.display ="none";
				document.getElementById("parablock_group").style.display ="block";
			}
			else{
				document.getElementById('paramessage').selectedIndex  = 0;
				document.getElementById("parablock_sendno").style.display ="block";
				document.getElementById("parablock_group").style.display ="none";
			}
		}
		else if (textst.indexOf('スタンプ') != -1){
			document.getElementById("parablock_file").style.display ="none";
			document.getElementById("parablock_mess").style.display ="none";
			if (textst.indexOf('グループ') != -1){
				document.getElementById('paramessage').selectedIndex  = 3;
				document.getElementById("parablock_sendno").style.display ="none";
				document.getElementById("parablock_group").style.display ="block";
			}
			else{
				document.getElementById('paramessage').selectedIndex  = 2;
				document.getElementById("parablock_sendno").style.display ="block";
				document.getElementById("parablock_group").style.display ="none";
			}
		}
		else if (textst.indexOf('写真') != -1){
			document.getElementById("parablock_file").style.display ="none";
			document.getElementById("parablock_mess").style.display ="none";
			if (textst.indexOf('グループ') != -1){
				document.getElementById('paramessage').selectedIndex  = 5;
				document.getElementById("parablock_sendno").style.display ="none";
				document.getElementById("parablock_group").style.display ="block";
			}
			else{
				document.getElementById('paramessage').selectedIndex  = 4;
				document.getElementById("parablock_sendno").style.display ="block";
				document.getElementById("parablock_group").style.display ="none";
			}
		}
		else {
			if (textst.indexOf('グループ') != -1){
				document.getElementById('paramessage').selectedIndex  = 7;
				document.getElementById("parablock_sendno").style.display ="none";
				document.getElementById("parablock_group").style.display ="block";
				document.getElementById("parablock_file").style.display ="block";
				document.getElementById("parablock_mess").style.display ="none";
			}
			else {
				document.getElementById('paramessage').selectedIndex  = 6;
				document.getElementById("parablock_sendno").style.display ="block";
				document.getElementById("parablock_group").style.display ="none";
				document.getElementById("parablock_file").style.display ="block";
				document.getElementById("parablock_mess").style.display ="none";
			}
		}
	}
	else if (cate == "AI"){
		document.getElementById("para_aichange").style.display ="block";
		if (textst.indexOf('に変換') != -1){
			document.getElementById('paraaichange').selectedIndex  = 0;
			document.getElementById("parablock_aichange").style.display ="block";			
			document.getElementById('ai_change_st').value = "";
		}
		else{
			document.getElementById('paraaichange').selectedIndex  = 1;
			document.getElementById("parablock_aichange").style.display ="none";	
		}
	}
	else if (cate == "Sound"){
		document.getElementById("para_sound").style.display ="block";
		document.getElementById("parablock_soundno").style.display ="block";
		document.getElementById('parasound').selectedIndex  = 0;	
	}
	else if (cate == "Variable"){
		document.getElementById("para_variable").style.display ="block";
		document.getElementById("parablock_variable").style.display ="block";
		if (textst.indexOf('変数1') != -1){
			document.getElementById('paravariable').selectedIndex  = 0;
		}
		else if (textst.indexOf('変数2') != -1){
			document.getElementById('paravariable').selectedIndex  = 1;
		}
		else if (textst.indexOf('変数3') != -1){
			document.getElementById('paravariable').selectedIndex  = 2;
		}
		else if (textst.indexOf('変数4') != -1){
			document.getElementById('paravariable').selectedIndex  = 3;
		}
		else{
			document.getElementById('paravariable').selectedIndex  = 4;
		}
		document.getElementById('variable_value').value = "";
	}
	else if (cate == "Setup"){
		document.getElementById("para_setup").style.display ="block";
		document.getElementById("parasetup").style.display ="block";
		if (textst.indexOf('サーバ') != -1){
			document.getElementById('parasetup').selectedIndex  = 0;	
			document.getElementById("parablock_server").style.display ="block";
			document.getElementById("parablock_pass").style.display ="none";
		}
		else if (textst.indexOf('ﾊﾟｽﾜｰﾄﾞ') != -1){
			document.getElementById('parasetup').selectedIndex  = 1;
			document.getElementById("parablock_pass").style.display ="block";			
			document.getElementById('setup_para_pass').value = "";
			document.getElementById("parablock_server").style.display ="none";
		}
		else if (textst.indexOf('重要') != -1){
			document.getElementById('parasetup').selectedIndex  = 2;
			document.getElementById("parablock_server").style.display ="none";
			document.getElementById("parablock_pass").style.display ="none";
		}
		else{
			document.getElementById('parasetup').selectedIndex  = 3;
			document.getElementById("parablock_server").style.display ="none";
			document.getElementById("parablock_pass").style.display ="none";
		}
	}
	else if (cate == "Font"){
		//設定画面を表示
		document.getElementById("para_display").style.display ="block";
		document.getElementById("paradisplay").style.display ="block";
		if (textst.indexOf('ﾌｫﾝﾄ') != -1){
			document.getElementById('paradisplay').selectedIndex  = 0;	
			document.getElementById("parablock_fontsize").style.display ="block";				
			document.getElementById('disp_para_fontsize').selectedIndex  = 11;
			document.getElementById("parablock_fontcolor").style.display ="none";
			document.getElementById("parablock_messagebackcolor").style.display ="none";
			document.getElementById("parablock_dispbackcolor").style.display ="none";
		}
		//文字色
		else if (textst.indexOf('の色') != -1){
			document.getElementById('paradisplay').selectedIndex  = 1;	
			document.getElementById("parablock_fontsize").style.display ="none";
			document.getElementById("parablock_fontcolor").style.display ="block";
			document.getElementById("parablock_messagebackcolor").style.display ="none";
			document.getElementById("parablock_dispbackcolor").style.display ="none";
		}
		else if (textst.indexOf('メッセージ背景色') != -1){
			document.getElementById('paradisplay').selectedIndex  = 2;	
			document.getElementById("parablock_fontsize").style.display ="none";
			document.getElementById("parablock_fontcolor").style.display ="none";
			document.getElementById("parablock_messagebackcolor").style.display ="block";
			document.getElementById("parablock_dispbackcolor").style.display ="none";
		}
		else{
			document.getElementById('paradisplay').selectedIndex  = 3;	
			document.getElementById("parablock_fontsize").style.display ="none";
			document.getElementById("parablock_fontcolor").style.display ="none";
			document.getElementById("parablock_messagebackcolor").style.display ="none";
			document.getElementById("parablock_dispbackcolor").style.display ="block";
		}		
	}
	else if (cate == "Sensor"){
		document.getElementById("para_sensor").style.display ="block";
		document.getElementById("parasensor").style.display ="block";	
		//時刻待ち	
		if (textst.indexOf('明') != -1 || textst.indexOf('暗') != -1 || textst.indexOf('度') != -1){
			document.getElementById("parablock_sensor").style.display ="block";
			document.getElementById("parablock_sensor_time").style.display ="none";
			if (textst.indexOf('明') != -1 || textst.indexOf('暗') != -1){
				document.getElementById("para_sensor_st1").innerHTML = "明るさ：";				
				document.getElementById('sensor_paralight').value = "50";
				if (textst.indexOf('明') != -1){
					document.getElementById('parasensor').selectedIndex  = 2;
				}
				else{
					document.getElementById('parasensor').selectedIndex  = 3;
				}
			}
			else{
				document.getElementById("para_sensor_st1").innerHTML = "温度：";				
				document.getElementById('sensor_paralight').value = "25";
				if (textst.indexOf('以上') != -1){
					document.getElementById('parasensor').selectedIndex  = 4;
				}
				else{
					document.getElementById('parasensor').selectedIndex  = 5;
				}
			}
		}
		else if (textst.indexOf('機械学習') != -1){
			document.getElementById("parablock_sensor").style.display ="none";
			document.getElementById("parablock_sensor_time").style.display ="none";
			document.getElementById("parablock_sensor_teachable").style.display ="block";
			document.getElementById('sensor_paralight').value = "";
			document.getElementById('sensor_paravalue').value = "";
			document.getElementById('parasensor').selectedIndex  = 1;
		}
		//時間待ち
		else if (textst.indexOf('時') != -1){
			document.getElementById("parablock_sensor").style.display ="none";
			document.getElementById("parablock_sensor_time").style.display ="block";
			document.getElementById("parablock_sensor_teachable").style.display ="none";
			document.getElementById('sensor_para_ji').selectedIndex = 11;
			document.getElementById('sensor_para_hun').selectedIndex = 0;
			document.getElementById('parasensor').selectedIndex  = 0;
		}
		//信号入力
		else {			
			document.getElementById("parablock_sensor").style.display ="none";
			document.getElementById("parablock_sensor_time").style.display ="none";
			document.getElementById("parablock_sensor_teachable").style.display ="none";
			document.getElementById('parasensor').selectedIndex  = 6;
		}
	}
	else if (cate == "Repeat1"){
		document.getElementById("para_repeat").style.display ="block";
		document.getElementById("pararepeat").style.display ="block";
		document.getElementById("parablock_repeat").style.display ="block";
	}
	else if (cate == "Repeat2"){
		
	}
	else if (cate == "Conditional"){
		document.getElementById("para_if").style.display ="block";
		document.getElementById("if_paraif").style.display ="block";
		if (textst.indexOf('より前') != -1 || textst.indexOf('より後') != -1){
			document.getElementById("para_if_time").style.display ="block";
			document.getElementById("parablock_if_teachable").style.display ="none";
			document.getElementById("parablock_if").style.display ="none";
			//document.getElementById("if_para_ji").selectedIndex = 11;
			if (textst.indexOf('より前') != -1) {
				document.getElementById('if_paraif').selectedIndex  = 0;
			}
			else{
				document.getElementById('if_paraif').selectedIndex  = 1;
			}
		}
		//重要
		else if (textst.indexOf('重要') != -1){
			document.getElementById("para_if_time").style.display ="none";
			document.getElementById("parablock_if_teachable").style.display ="none";
			document.getElementById("parablock_if").style.display ="none";
			if (textst.indexOf('でない') != -1) {
				document.getElementById('if_paraif').selectedIndex  = 3;
			}
			else{
				document.getElementById('if_paraif').selectedIndex  = 2;
			}
		}
		//機械学習
		else if (textst.indexOf('機械学習') != -1){
			document.getElementById("para_if_time").style.display ="none";
			document.getElementById("parablock_if_teachable").style.display ="block";
			document.getElementById("parablock_if").style.display ="none";
			if (textst.indexOf('>') != -1) {
				document.getElementById('if_paraif').selectedIndex  = 4;
			}
			else{
				document.getElementById('if_paraif').selectedIndex  = 5;
			}
		}
		else if (textst.indexOf('明') != -1 || textst.indexOf('暗') != -1){
			document.getElementById("para_if_time").style.display ="none";
			document.getElementById("parablock_if_teachable").style.display ="none";
			document.getElementById("parablock_if").style.display ="block";
			document.getElementById("para_if_st3").innerHTML = "明るさ：";	
			if (textst.indexOf('>') != -1) {
				document.getElementById('if_paraif').selectedIndex  = 6;
			}
			else{
				document.getElementById('if_paraif').selectedIndex  = 7;
			}
		}
		else if (textst.indexOf('度') != -1){
			document.getElementById("para_if_time").style.display ="none";
			document.getElementById("parablock_if_teachable").style.display ="none";
			document.getElementById("parablock_if").style.display ="block";
			document.getElementById("para_if_st3").innerHTML = "温度：";	
			if (textst.indexOf('>') != -1) {
				document.getElementById('if_paraif').selectedIndex  = 8;
			}
			else{
				document.getElementById('if_paraif').selectedIndex  = 9;
			}
		}
		else{
			document.getElementById("parablock_if").style.display ="none";
			document.getElementById("para_if_time").style.display ="none";
			document.getElementById("parablock_if_teachable").style.display ="none";
			if (textst.indexOf('ON') != -1) {
				document.getElementById('if_paraif').selectedIndex  = 10;
			}
			else{
				document.getElementById('if_paraif').selectedIndex  = 11;
			}
		}
	}
	else if (cate == "Recieve_Conditional"){
		document.getElementById("para_if_r").style.display ="block";
		document.getElementById("if_paraif_r").style.display ="block";
		if (textst.indexOf('より前') != -1 || textst.indexOf('より後') != -1){
			document.getElementById("parablock_r_ifseitono").style.display ="none";
			document.getElementById("parablock_if_r").style.display ="block";
			document.getElementById("parablock_if_r").selectedIndex = 11;
			if (textst.indexOf('より前') != -1) {
				document.getElementById('if_paraif_r').selectedIndex  = 0;
			}
			else{
				document.getElementById('if_paraif_r').selectedIndex  = 1;
			}
		}
		//重要
		else if (textst.indexOf('重要') != -1){
			document.getElementById("parablock_if_r").style.display ="none";
			document.getElementById("parablock_r_ifseitono").style.display ="none";
			if (textst.indexOf('でない') != -1) {
				document.getElementById('if_paraif_r').selectedIndex  = 3;
			}
			else{
				document.getElementById('if_paraif_r').selectedIndex  = 2;
			}
		}
		//生徒番号
		else if (textst.indexOf('生徒') != -1){
			document.getElementById("parablock_if_r").style.display ="none";
			document.getElementById("parablock_r_ifseitono").style.display ="block";
			if (textst.indexOf('でない') != -1) {
				document.getElementById('if_paraif_r').selectedIndex  = 5;
			}
			else{
				document.getElementById('if_paraif_r').selectedIndex  = 4;
			}
		}
		else {
			document.getElementById("parablock_if_r").style.display ="none";
			document.getElementById("parablock_r_ifseitono").style.display ="none";
			if (textst.indexOf('でない') != -1) {
				document.getElementById('if_paraif_r').selectedIndex  = 7;
			}
			else{
				document.getElementById('if_paraif_r').selectedIndex  = 6;
			}
		}
	}
	else{
		
	}	
}
//プルダウンが変更されたら
function change_pulldown(selectid){
	//console.log(selectid);
	var pulldown = document.getElementById(selectid.id);
	//console.log(pulldown.value);
	para_label_display(datastkv.category, pulldown.value);
}
//paraデータ更新ボタン
function kousin(){
	//console.log(myDiagram);
	//console.log(datastkv);
	var parast="";
	var dispst="";
	if (datastkv.category == "Message"){
		var komoku = document.getElementById('paramessage').value;
		
		var mess = document.getElementById('m_para_mess').value;
		var sendno = "";
		if (komoku.indexOf('グループ') != -1){
			sendno = document.getElementById('m_para_group').value;
		}
		else{
			sendno = document.getElementById('m_para_sendno').value;
			sendno = sendno.replace(/[\s　]+/g, '');
		}

		if (komoku.indexOf('メッセージ') != -1){
			if (komoku.indexOf('グループ') != -1){
				parast = "1-"+ sendno + "-" + mess + "-0-0-0";
			}
			else{
				parast = "0-"+ sendno + "-" + mess + "-0-0-0";
			}
			dispst = sendno + "番に"+ mess +"を送信";
		}
		//スタンプの場合
		else if (komoku.indexOf('スタンプ') != -1){
			if (komoku.indexOf('グループ') != -1){
				parast = "3-"+ sendno + "-" + "" + "-0-0-0";
			}
			else{
				parast = "2-"+ sendno + "-" + "" + "-0-0-0";
			}
			
			dispst = "スタンプを"+ sendno +"番に送信";
		}
		//写真の場合
		else if (komoku.indexOf('写真') != -1){
			if (komoku.indexOf('グループ') != -1){
				parast = "5-" + sendno + "-0-0-0-0";
			}
			else{
				parast = "4-" + sendno + "-0-0-0-0";
			}
			
			dispst = "写真を"+ sendno +"に送信";
		}
		//プログラムの場合
		else{
			var fileno = document.getElementById('m_para_file').value;
			if (komoku.indexOf('グループ') != -1){
				parast = "7-" + sendno + "-" + fileno +"-0-0-0";
			}
			else{
				parast = "6-" + sendno + "-" + fileno +"-0-0-0";
			}
			
			dispst = "ﾌｧｲﾙ "+ fileno +" を"+ sendno +"に送信";
		}
	}
	else if (datastkv.category == "AI"){
		var komoku = document.getElementById('paraaichange').value;
		if (komoku.indexOf('メッセージをAIに変換') != -1){
			var strresu="";
			var para1 = document.getElementById('ai_change_st').value;
			parast = "2-"+ para1 +"-0-0-0-0";
			if (para1.length > 5) {
				strresu = para1.substr(0,5);
				strresu += "...";
			}
			else{
				strresu = para1;
			}
			dispst = "AIで"+strresu+"に変換";
		}
		else{
			dispst = "プロンプトでAI変換";
		}
	}
	else if (datastkv.category == "Sound"){
		var para1 = document.getElementById('sound_para_sound').selectedIndex;
		parast = "0-" + para1.toString()+ "-0-0-0-0";
		dispst = "音"+　(para1 + 1).toString() + "を鳴らす";
	}
	else if (datastkv.category == "Font"){
		var komoku = document.getElementById('paradisplay').value;
		if (komoku.indexOf('ﾌｫﾝﾄｻｲｽﾞ') != -1){	
			var para1 = document.getElementById('disp_para_fontsize').value;
			parast = "0-" + para1 + "-0-0-0-0";
			dispst = "ﾌｫﾝﾄｻｲｽﾞ " + para1;		
			myDiagram.model.setDataProperty(datastkv, "fill", "#fff3c7");		
		}
		else if (komoku.indexOf('の色') != -1){	
			var para1 = document.getElementById('disp_para_fontcolor').value;
			parast = "1-" + para1 + "-0-0-0-0";
			dispst = "文字の色";
			myDiagram.model.setDataProperty(datastkv, "fill", para1);		
		}
		else if (komoku.indexOf('メッセージ背景色') != -1){	
			var para1 = document.getElementById('disp_para_messagebackcolor').value;
			parast = "2-" + para1 + "-0-0-0-0";
			dispst = "メッセージ背景色";
			myDiagram.model.setDataProperty(datastkv, "fill", para1);		
		}
		else{
			var para1 = document.getElementById('disp_para_dispbackcolor').value;
			parast = "3-" + para1 + "-0-0-0-0";
			dispst = "画面背景色";
			myDiagram.model.setDataProperty(datastkv, "fill", para1);		
		}
	}
	else if (datastkv.category == "Variable"){
		var komoku = document.getElementById('paravariable').value;
		var para1 = document.getElementById('variable_value').value;
		if (komoku.indexOf('1') != -1){
			parast = "0-" + para1 + "-0-0-0-0";
			variable_St1 = para1;
		}
		else if (komoku.indexOf('2') != -1){
			parast = "1-" + para1 + "-0-0-0-0";	
			variable_St2 = para1;
		}
		else if (komoku.indexOf('3') != -1){
			parast = "2-" + para1 + "-0-0-0-0";
			variable_St3 = para1;
		}
		else if (komoku.indexOf('4') != -1){
			parast = "3-" + para1 + "-0-0-0-0";
			variable_St4 = para1;
		}
		else{
			parast = "4-" + para1 + "-0-0-0-0";
			variable_St5 = para1;
		}
		dispst = komoku + "に\"" + para1 + "\"を設定";
	}
	else if (datastkv.category == "Setup"){
		var komoku = document.getElementById('parasetup').value;
		if (komoku.indexOf('サーバ') != -1){
			var para1 = document.getElementById('setup_para_server').value;
			para1 = para1.replace(/[\s　]+/g, '');
			parast = "0-"+ para1 +"-0-0-0-0";
			dispst = "サーバを"+ para1 + "に設定";
		}
		else if (komoku.indexOf('ﾊﾟｽﾜｰﾄﾞ') != -1){
			var para1 = document.getElementById('setup_para_pass').value;
			parast = "1-"+ para1 +"-0-0-0-0";
			dispst = "ﾊﾟｽﾜｰﾄﾞ"+ para1;
		}
		else if (komoku.indexOf('重要') != -1){
			parast = "2-0-0-0-0-0";
			dispst = "重要なﾒｯｾｰｼﾞ";
		}
		else{
			parast = "3-0-0-0-0-0";
			dispst = "確認画面を表示";
		}
	}
	else if (datastkv.category == "Sensor"){
		var komoku = document.getElementById('parasensor').value;
		if (komoku.indexOf('時刻') != -1){
			var para1 = document.getElementById('sensor_para_ji').value;
			var para2 = document.getElementById('sensor_para_hun').value;
			parast = "0-"+ (Number(para1) - 1).toString() + "-" + para2 + "-0-0-0";
			dispst = para1 +"時" + para2 + "分まで待つ";
		}
		else if (komoku.indexOf('機械学習') != -1){				
			var classname = document.getElementById('sensor_paraclass').value;
			var value = document.getElementById('sensor_paravalue').value;
			parast = "1-" + classname + "-" + value + "-0-0-0";
			dispst = "機械学習\"" + classname + "\"が\"" +  value + "\"待ち";
		}
		else if (komoku.indexOf('明') != -1){				
			var lightvalue = document.getElementById('sensor_paralight').value;
			if (isNumber(lightvalue) == false || isHani(Number(lightvalue), 0, 100) == false) {
				alert("0から100の整数を入力して下さい。");
				return;
			}
			parast = "2-" + lightvalue + "-0-0-0-0";
			dispst = "明るさ " + lightvalue + "待ち";
		}
		else if (komoku.indexOf('暗') != -1){	
			var lightvalue = document.getElementById('sensor_paralight').value;
			if (isNumber(lightvalue) == false || isHani(Number(lightvalue), 0, 100) == false) {
				alert("0から100の整数を入力して下さい。");
				return;
			}
			parast = "3-" + lightvalue + "-0-0-0-0";
			dispst = "暗さ " + lightvalue + "待ち";
		}
		else if (komoku.indexOf('以上') != -1){	
			var lightvalue = document.getElementById('sensor_paralight').value;
			if (isNumber(lightvalue) == false || isHani(Number(lightvalue), 0, 50) == false) {
				alert("0から50の整数を入力して下さい。");
				return;
			}
			parast = "4-" + lightvalue + "-0-0-0-0";
			dispst = lightvalue + "度以上待ち";
		}
		else if (komoku.indexOf('以下') != -1){	
			var lightvalue = document.getElementById('sensor_paralight').value;
			if (isNumber(lightvalue) == false || isHani(Number(lightvalue), 0, 50) == false) {
				alert("0から50の整数を入力して下さい。");
				return;
			}
			parast = "5-" + lightvalue + "-0-0-0-0";
			dispst = lightvalue + "度以下待ち";
		}
		else{	
			parast = "6-0-0-0-0-0";
			dispst = "信号待ち";
		}
	}
	else if (datastkv.category == "Repeat1"){		
		var repcnt = document.getElementById('repeat_paracnt').value;
		if (isNumber(repcnt) == false || isHani(Number(repcnt), 1, 5) == false) {
			alert("1から5の整数を入力して下さい。");
			return;
		}
		parast = "0-" + repcnt + "-0-0-0-0";
		dispst = "繰り返し" + repcnt + "回";
	}
	else if (datastkv.category == "Repeat2"){
		parast = "1-0-0-0-0-0";
		dispst = "繰り返し終了";		
	}
	else if (datastkv.category == "Conditional"){
		var komoku = document.getElementById('if_paraif').value;
		if (komoku.indexOf('前') != -1 || komoku.indexOf('後') != -1 ){
			var para1 = document.getElementById('if_para_ji').value;
			var para2 = document.getElementById('if_para_hun').value;
			if (komoku.indexOf('前') != -1){
				parast = "0-"+ (Number(para1) - 1).toString() + "-" + para2 + "-0-0-0";
				dispst = para1 + "時" + para2 + "分より前?";
			}
			else {
				parast = "1-"+ (Number(para1) - 1).toString() + "-" + para2 + "-0-0-0";
				dispst = para1 + "時" + para2 + "分より後?";
			}
		}
		else if (komoku.indexOf('重要') != -1){	
			if (komoku.indexOf('でない') != -1){
				parast = "3-0-0-0-0-0";
				dispst = "重要でない?";
			}
			else{
				parast = "2-0-0-0-0-0";
				dispst = "重要?";
			}
		}
		else if (komoku.indexOf('明') != -1 || komoku.indexOf('暗') != -1 ){
			var para1 = document.getElementById('if_paralight').value;
			if (isNumber(para1) == false || isHani(Number(para1), 0, 100) == false) {
				alert("0から100の整数を入力して下さい。");
				return;
			}
			if (komoku.indexOf('>') != -1){
				parast = "6-"+ para1 + "-0-0-0-0";
				dispst = "明るさ > " + para1 + "?";
			}
			else {
				parast = "7-"+ para1 + "-0-0-0-0";
				dispst = "明るさ < " + para1 + "?";
			}
		}
		else if (komoku.indexOf('機械') != -1){
			var para1 = document.getElementById('if_paraclass').value;
			var para2 = document.getElementById('if_paravalue').value;
			if (komoku.indexOf('>なら') != -1){
				parast = "4-"+ para1 + "-" + para2 + "-0-0-0";
				dispst = "機械学習:" + para1 + ">=" + para2 + "%?";
			}
			else {
				parast = "5-"+ para1 + "-0-0-0-0";
				dispst = "機械学習:" + para1 + "<=" + para2 + "%?";
			}
		}
		else if (komoku.indexOf('度') != -1){
			var para1 = document.getElementById('if_paralight').value;
			if (isNumber(para1) == false || isHani(Number(para1), 0, 50) == false) {
				alert("0から50の整数を入力して下さい。");
				return;
			}
			if (komoku.indexOf('>') != -1){
				parast = "8-"+ para1 + "-0-0-0-0";
				dispst = "温度 > " + para1 + "?";
			}
			else {
				parast = "9-"+ para1 + "-0-0-0-0";
				dispst = "温度 < " + para1 + "?";
			}
		}
		else {
			if (komoku.indexOf('ON') != -1){
				parast = "10-0-0-0-0-0";
				dispst = "外部信号ON?";
			}
			else {
				parast = "11-0-0-0-0-0";
				dispst = "外部信号OFF?";
			}
		}
	}
	else if (datastkv.category == "Recieve_Conditional"){
		var komoku = document.getElementById('if_paraif_r').value;
		if (komoku.indexOf('前') != -1 || komoku.indexOf('後') != -1 ){
			var para1 = document.getElementById('if_para_r_ji').value;
			if (komoku.indexOf('前') != -1){
				parast = "0-"+ para1 + "-0-0-0-0";
				dispst = para1 +"時より前?";
			}
			else {
				parast = "1-"+ para1 + "-0-0-0-0";
				dispst = para1 +"時より後?";
			}
		}
		else if (komoku.indexOf('重要') != -1){	
			if (komoku.indexOf('でない') != -1){
				parast = "3-0-0-0-0-0";
				dispst = "重要でない?";
			}
			else {
				parast = "2-0-0-0-0-0";
				dispst = "重要?";
			}			
		}
		else if (komoku.indexOf('生徒') != -1){
			var para1 = document.getElementById('if_para_r_seitono').value;
			if (komoku.indexOf('でない') != -1){
				parast = "5-"+ para1 + "-0-0-0-0";
				dispst = "生徒番号が" + para1 + "でない?";
			}
			else {
				parast = "4-"+ para1 + "-0-0-0-0";
				dispst = "生徒番号が" + para1 + "?";
			}
		}		
		else{			
			if (komoku.indexOf('でない') != -1){
				parast = "7-0-0-0-0-0";
				dispst = "エラーでない?";
			}
			else {
				parast = "6-0-0-0-0-0";
				dispst = "エラー?";
			}			
		}
	}
	else{				
		parast = "0-0-0-0-0-0";
		dispst = " ";
	}
	
	//データ更新    name:変更するkey(category,fill,parameter) value:変更後の値
	myDiagram.model.setDataProperty(datastkv, "parameter", parast);
	myDiagram.model.setDataProperty(datastkv, "text", dispst);
	//console.log(parast);
	//console.log(datastkv.text);
	para_label_notdisplay();
}

//全削除
function allDelete() {
	myDiagram.model = go.Model.fromJson("{ \"class\": \"go.GraphLinksModel\", \"linkFromPortIdProperty\": \"fromPort\", \"linkToPortIdProperty\": \"toPort\", \"nodeDataArray\":[],\"linkDataArray\": []}");
}
