//送信データに変換
function Create_Data_List(){
	set_comand_array();
	set_send_Data_Array();
	
	var Send_Data = new Array();
	var totalcount = 0;
	var searchNo = "0";
	for ( var i = 0; i < sendData_Array.length; i++ ) {
		//console.log(command_Array);
		var comm_data = sendData_Array[i].split( ',' );
		var ComNo = Get_ComSendNo(comm_data[1],comm_data[7]);
		//console.log(ComNo);
		if (ComNo == ""){continue;}
		//命令番号を追加（LED点灯以外）
		Send_Data.push( ComNo + "\n"); 
		
		var paracount = comm_data[7].split('-');
		
		//引数１個
		if (ComNo == "620" || ComNo == "622" || ComNo == "623" || ComNo == "626" || ComNo == "627" || ComNo == "628" || 
			ComNo == "640" || ComNo == "641" || ComNo == "650" || ComNo == "660" || ComNo == "661" || 
			ComNo == "662" || ComNo == "663" || ComNo == "670" || ComNo == "680" || ComNo == "681" || ComNo == "682" || ComNo == "683" || ComNo == "684" || 
			ComNo == "662" || ComNo == "663" || ComNo == "670" || 
			ComNo == "420" || ComNo == "422" || ComNo == "423" || ComNo == "426" || 
			ComNo == "696" || ComNo == "697" || ComNo == "698" || ComNo == "699") {
			//グループなら
			if (ComNo == "626" || ComNo == "627" || ComNo == "628" || ComNo == "451"){
				//グループ１なら
				if (paracount[1] == "グループ1"){
					sendnoarraay = groupArray1;
				}
				//グループ２なら
				else if  (paracount[1] == "グループ2"){
					sendnoarraay = groupArray2;
				}
				//グループ３なら
				else{
					sendnoarraay = groupArray3;
				}
				var allst = "";
				for ( var j = 0; j < sendnoarraay.length; j++ ) {
					if (j != 0){allst += "/";}
					allst += sendnoarraay[j];
				}			
				Send_Data.push( allst + "\n");
				Send_Data.push(paracount[2] + "\n");
			}
			else{
				Send_Data.push(paracount[1] + "\n");
			}
			
			if (ComNo == "620" || ComNo == "626"){
				Send_Data.push(paracount[2] + "\n");
			}
			else if (ComNo == "622" || ComNo == "627"){				
				//スタンプ番号が選択されていれば
				if (document.getElementById('m_para_stampno').selectedIndex != 0){
					Send_Data.push(document.getElementById('m_para_stampno').selectedIndex + "\n");
				}
				//何も送信するものがなかったら
				else{
					alert("スタンプが選択されていません。");
					return;
				}
			}
			else if (ComNo == "623" || ComNo == "628"){				
				//写真が選択されていれば
				if (document.getElementById('img_file').value != ""){
					Send_Data.push(document.getElementById('img_file').value + "\n");
				}
				//何も送信するものがなかったら
				else{
					alert("写真が選択されていません。");
					return;
				}
			}
		}
		//AI(プロンプト)
		else if (ComNo == "633"){
			//プロンプトに入力があれば
			if (document.getElementById('prompttext').value != ""){
				var promt_text = document.getElementById('prompttext').value;
				var promt_selectvalue = document.getElementById('promptSelect').value;
				Send_Data.push(document.getElementById('mymessage').value + "\n");
			}
			//何も送信するものがなかったら
			else{
				alert("プロンプトが設定されていません。");
				return;
			}
		}
		//引数２個　時刻待ち　分岐（時刻）
		else if (ComNo == "421" || ComNo == "425" || ComNo == "450" || ComNo == "451" || ComNo == "632" || ComNo == "690" || ComNo == "691" || 
				 ComNo == "694" || ComNo == "695") {
			//AI変換なら
			if (ComNo == "632"){
				Send_Data.push(paracount[1] + "\n");
				Send_Data.push(document.getElementById('mymessage').value + "\n");
			}
			else if (ComNo == "450" || ComNo == "451"){
				Send_Data.push(paracount[1] + "\n");
				Send_Data.push("file" + paracount[2] + "\n");
			}
			else{
				Send_Data.push(paracount[1] + "\n");
				Send_Data.push(paracount[2] + "\n");
			}
		}
		//console.log(comm_data[1]);
		//次の番地追加
		if (comm_data[1] != "End"){
			//console.log(comm_data[5]);
			//Next_Noがあれば
			if (comm_data[5] != ""){
				//console.log(comm_data[5]);
				Send_Data.push(Get_Byte_Adrress(sendData_Array, comm_data[5]).toString() + "\n");
			}
			else{
				Send_Data.push("-1\n");
			}
		}
		//条件分岐なら偽の番地も追加
		if (comm_data[1] == "Conditional"){
			//Next_No2があれば
			if (comm_data[6] != ""){
				Send_Data.push(Get_Byte_Adrress(sendData_Array, comm_data[6]).toString() + "\n");
			}
			else{
				Send_Data.push("-1\n");
			}
		}
		//console.log(Send_Data);
	}	
    //Send_Data.push( "250\n"); 
	document.form3.textarea2.value=Send_Data.join('');
}

//繰り返し中にAIがあるかどうか（あればfalse）
function repeat_check_ai(lines){
	for ( var i = 0; i < lines.length; i++ ) {
		var repeat_level = 0;//繰り返しの階層
		//繰り返し開始なら
		if (lines[i] == "650"){
			repeat_level++;
		}
		//繰り返し終了なら
		else if (lines[i] == "651"){
			repeat_level--;
		}
		//Aiなら
		else if (lines[i] == "630" || lines[i] == "631" || lines[i] == "632"){
			if (repeat_level != 0){
				return false;
			}
		}
	}
	return true;
}

//AIが複数あったらエラーに（あればfalse）
function ai_multi(lines){
	for ( var i = 0; i < lines.length; i++ ) {
		var aicnt = false;
		if (lines[i] == "535" || lines[i] == "536" || lines[i] == "630" || lines[i] == "631" || lines[i] == "632"){
			if (aicnt){
				return false;
			}
			else{
				aicnt = true;
			}
		}
	}
	return true;
}

//受信データに変換
function changeRecieveData(){
	set_comand_array();
	set_recieve_Data_Array();
	var Recieve_Data = new Array();
	for ( var i = 0; i < recieveData_Array.length; i++ ) {
		var comm_data = recieveData_Array[i].split( ',' );
		var ComNo = Get_ComRecieveNo(comm_data[1],comm_data[7]);
		if (ComNo == ""){continue;}
		//命令番号を追加（LED点灯以外）
		Recieve_Data.push( ComNo + "\n"); 
		
		var paracount = comm_data[7].split('-');
		
		//引数１個
		if (ComNo == "950" || ComNo == "960" || ComNo == "961" ||
			 ComNo == "962" || ComNo == "963" || ComNo == "990" || ComNo == "991" ||
			 ComNo == "996" || ComNo == "997") {
			Recieve_Data.push(paracount[1] + "\n");
		}
		//次の番地追加
		if (comm_data[1] != "RecieveEnd"){
			//Next_Noがあれば
			if (comm_data[5] != ""){
				Recieve_Data.push(Get_Byte_Adrress(recieveData_Array, comm_data[5]).toString() + "\n");
			}
		}
		//条件分岐なら偽の番地も追加
		if (comm_data[1] == "Recieve_Conditional"){
			//Next_No2があれば
			if (comm_data[6] != ""){
				Recieve_Data.push(Get_Byte_Adrress(recieveData_Array, comm_data[6]).toString() + "\n");
			}
			else{
				Recieve_Data.push("0\n");
			}
		}
	}
	document.form4.textarea3.value=Recieve_Data.join('');

 }

//命令名、パラメータから転送番号を取得
function Get_ComRecieveNo(ComName, para){
	var para_data = para.split('-');
	var komoku = para_data[0];
	if (ComName == "RecieveStart") {return "900";}
	else if (ComName == "RecieveEnd") {return "901";}
	else if (ComName == "Sound") {return "950";}
	else if (ComName == "Font") {//フォント
		if (komoku == "0") { return "960"; }
		else if (komoku == "1") { return "961"; }
		else if (komoku == "2") { return "962"; }
		else { return "963"; }
	}
	else if (ComName == "Recieve_Conditional") { //受信の条件分岐
		if (komoku == "0") { return "990"; }
		else if (komoku == "1") { return "991"; }
		else if (komoku == "2") { return "992"; }
		else if (komoku == "3") { return "993"; }
		else if (komoku == "4") { return "996"; }
		else if (komoku == "5") { return "997"; }
		else if (komoku == "6") { return "998"; }
		else { return "999"; }
	}
	else {return "";}
}

//受信プログラムを実行
function RecieveDataplay(st){	
    recieve_font_size = "";
	recieve_font_color = "";
	recieve_back_color = "";
	recieve_disp_color = "";
	recieve_sound = "";
	var m_data = st.split( ',' );//分解
	var data = m_data[7].split( '/' );//分解
	var text  = document.getElementById('recieveText').value.replace(/\r\n|\r/g, "\n");
    var lines = text.split( '\n' );	
	let proflag = true;
	//処理番号
	cnt =0;
	while (proflag){
		if (lines[cnt] == "900"){
			cnt += 2;
			continue;
		}
		//音
		else if (lines[cnt] == "950"){
			recieve_sound = lines[cnt + 1];
			cnt = Number(lines[cnt + 2]);
		}
		//フォントの大きさ
		else if (lines[cnt] == "960"){	
			recieve_font_size = lines[cnt + 1];
			cnt = Number(lines[cnt + 2]);
		}
		//フォントの色
		else if (lines[cnt] == "961"){
			recieve_font_color = lines[cnt + 1];
			cnt = Number(lines[cnt + 2]);
		}
		//メッセージ背景色
		else if (lines[cnt] == "962"){
			recieve_back_color = lines[cnt + 1];
			cnt = Number(lines[cnt + 2]);
		}
		//画面背景色
		else if (lines[cnt] == "963"){
			recieve_disp_color = lines[cnt + 1];
			cnt = Number(lines[cnt + 2]);
		}
		//if 時刻前なら
		else if (lines[cnt] == "990"){			
			var date1 = new Date();
			var ji = date1.getHours();
			//前なら 
			if (Number(lines[cnt + 1]) > ji){ 
				cnt = Number(lines[cnt + 2]);
			}
			//後なら
			else{				
				cnt = Number(lines[cnt + 3]);
			}
		}
		//if 時刻後なら
		else if (lines[cnt] == "991"){			
			var date1 = new Date();
			var ji = date1.getHours();
			//前なら 
			if (Number(lines[cnt + 1]) > ji){ 
				cnt = Number(lines[cnt + 3]);
			}
			//後なら
			else{				
				cnt = Number(lines[cnt + 2]);
			}
		}
		//重要なら
		else if (lines[cnt] == "992"){	
			if (data[1] == "true"){ 
				cnt = Number(lines[cnt + 1]);
			}
			else{
				cnt = Number(lines[cnt + 2]);
			}
		}
		//重要でないなら
		else if (lines[cnt] == "993"){	
			if (data[1] == true){ 
				cnt = Number(lines[cnt + 2]);
			}
			else{				
				cnt = Number(lines[cnt + 1]);
			}
		}
		//指定した生徒番号なら
		else if (lines[cnt] == "996"){	
			if (lines[cnt + 1] == m_data[3]){ 
				cnt = Number(lines[cnt + 2]);
			}
			else{
				cnt = Number(lines[cnt + 3]);
			}
		}	
		//指定した生徒番号なら
		else if (lines[cnt] == "997"){	
			if (lines[cnt + 1] != m_data[3]){ 
				cnt = Number(lines[cnt + 2]);
			}
			else{
				cnt = Number(lines[cnt + 3]);
			}
		}	
		//エラーなら
		else if (lines[cnt] == "998"){	
			if (m_data[0] == "ERR"){ 
				cnt = Number(lines[cnt + 1]);
			}
			else{				
				cnt = Number(lines[cnt + 2]);
			}
		}	
		//エラーでないなら
		else if (lines[cnt] == "999"){	
			if (m_data[0] == "ERR"){ 
				cnt = Number(lines[cnt + 2]);
			}
			else{				
				cnt = Number(lines[cnt + 1]);
			}
		}		
		
		else{break;}
		
		if (cnt>=lines.length - 1 || lines[cnt] == "901"){
			break;
		}
	}	 
	
}

//転送データに変換
function Create_Data_List_s(){
	set_comand_array();
	var Send_Data = new Array();
	for ( var i = 0; i < command_Array.length; i++ ) {
		//console.log(command_Array);
		var comm_data = command_Array[i].split( ',' );
		var ComNo = Get_ComSendNo(comm_data[1],comm_data[7]);
		//console.log(ComNo);
		//命令番号を追加（LED点灯以外）
		Send_Data.push( ComNo + "\n"); 
		
		var paracount = comm_data[7].split('-');
		
		//引数１個
		if (ComNo == "521" || ComNo == "522" || ComNo == "523" || ComNo == "530" || ComNo == "535" || ComNo == "550" || ComNo == "551" ||
			 ComNo == "554" || ComNo == "555" || ComNo == "558" || ComNo == "559" || 
			 ComNo == "562" || ComNo == "563" || ComNo == "564" || ComNo == "580" || ComNo == "581" || ComNo == "582" || ComNo == "583" || ComNo == "584") {
			Send_Data.push(paracount[1] + "\n");
		}
		
		//引数１個（プログラム）
		else if (ComNo == "527") {
			if (paracount[1] == "ファイル1"){
				if (profile_data1 == ""){
					alert("ファイル１が選択されていません");
					return "";
				}
				Send_Data.push( profile_data_filename1 + "=" + profile_data1);
			}
			else if (paracount[1] == "ファイル2"){
				if (profile_data2 == ""){
					alert("ファイル２が選択されていません");
					return "";
				}
				Send_Data.push( profile_data_filename2 + "=" + profile_data2);
			}
			else{
				if (profile_data3 == ""){
					alert("ファイル３が選択されていません");
					return "";
				}
				Send_Data.push( profile_data_filename3 + "=" + profile_data3);
			}			
		}
		//引数２個（プログラム）
		else if (ComNo == "525" || ComNo == "526") {
			if (paracount[1] == "ファイル1"){
				if (profile_data1 == ""){
					alert("ファイル１が選択されていません");
					return "";
				}
				Send_Data.push( profile_data_filename1 + "=" + profile_data1);
			}
			else if (paracount[1] == "ファイル2"){
				if (profile_data2 == ""){
					alert("ファイル２が選択されていません");
					return "";
				}
				Send_Data.push( profile_data_filename2 + "=" + profile_data2);
			}
			else{
				if (profile_data3 == ""){
					alert("ファイル３が選択されていません");
					return "";
				}
				Send_Data.push( profile_data_filename3 + "=" + profile_data3);
			}
			//指定番号に送るには
			if (ComNo == "525"){
				Send_Data.push(paracount[2] + "\n");
			}
			//グループに送る
			else{
				//グループ１なら
				if (paracount[2] == "グループ1"){
					sendnoarraay = groupArray1;
				}
				//グループ２なら
				else if  (paracount[2] == "グループ2"){
					sendnoarraay = groupArray2;
				}
				//グループ３なら
				else{
					sendnoarraay = groupArray3;
				}
				var allst = "";
				for ( var j = 0; j < sendnoarraay.length; j++ ) {
					if (j != 0){allst += "/";}
					allst += sendnoarraay[j];
				}			
				Send_Data.push( allst + "\n");
			}
			
		}
		//引数２個
		else if (ComNo == "620" || ComNo == "621") {
			Send_Data.push(paracount[1] + "\n");
			Send_Data.push(paracount[2] + "\n");
		}
		
		//console.log(comm_data[1]);
		//次の番地追加
		if (comm_data[1] != "End"){
			//console.log(comm_data[5]);
			//Next_Noがあれば
			if (comm_data[5] != ""){
				//console.log(comm_data[5]);
				//console.log(Get_Byte_Adrress(comm_data[5]));
				Send_Data.push(Get_Byte_Adrress(comm_data[5]).toString() + "\n");
			}
			else{
				Send_Data.push("0\n");
			}
		}
		//条件分岐なら偽の番地も追加
		if (comm_data[1] == "server_Conditional"){
			//Next_No2があれば
			if (comm_data[6] != ""){
				Send_Data.push(Get_Byte_Adrress(comm_data[6]).toString() + "\n");
			}
			else{
				Send_Data.push("0\n");
			}
		}
		//console.log(Send_Data);
	}	
    //Send_Data.push( "250\n"); 
	document.form3.textarea2.value=Send_Data.join('');
}

//実データから配列にセットする
function set_comand_array(){		
	document.getElementById("mySavedModel").value = myDiagram.model.toJson();
	//JSON形式でブロック個々のデータを取得  
	var obj =JSON.parse(document.getElementById("mySavedModel").value);
	//console.log(obj.nodeDataArray.length);
	//obj.nodeDataArray→{"category":"Led", "fill":"#f15959", "stroke":"#000000", "text":"LED", "key":-2, "loc":"-333 -112"},
	//obj.linkDataArray→{"from":-1, "to":-4, "fromPort":"下", "toPort":"上", "points":[-331,-343.25,-331,-333.25,-331,-332.5,-325,-332.5,-325,-331.75,-325,-321.75]},
	//配列にデータを追加
	for ( var i = 0; i < obj.nodeDataArray.length; i++ ) {
		//No
		var c_no = obj.nodeDataArray[i].key.toString()
		//Command_Name
		var c_name = obj.nodeDataArray[i].category;
		//Next_No Next_No2
		var c_next = "";
		var c_next2 = "";
		//console.log(obj.linkDataArray);
		for ( var j = 0; j < obj.linkDataArray.length; j++ ) {
			if (c_no == obj.linkDataArray[j].from){
				//console.log(obj.linkDataArray[j].from);
				if (obj.linkDataArray[j].fromPort == "下"){
					c_next = obj.linkDataArray[j].to;
				}
				else if (obj.linkDataArray[j].fromPort == "右"){
					c_next2 = obj.linkDataArray[j].to;
				}
			}
		}
		//Before_No
		var c_before = "";
		for ( var j = 0; j < obj.linkDataArray.length; j++ ) {
			if (c_no == obj.linkDataArray[j].to){
				if (c_before != ""){c_before += "/";}
					c_before += obj.linkDataArray[j].from;
			}
		}
		//para			
		//console.log(obj.nodeDataArray[i]);
		var c_para = obj.nodeDataArray[i].parameter;			

		command_Array[i] = c_no + "," + c_name + ",,," + c_before + "," + c_next + "," + c_next2 + ","+ c_para;
	}
	command_Array.sort();
	//console.log(command_Array);
}