var command_Array = new Array();

//サーバのデータベースにアップするデータの取得 st:chatdataのオブジェクト
function get_serverupload_data(st){
	serverpwst = "";//パスワード
	ai_returnData = "";//aiで取得した文字列
	ai_mode = false;//AIへ問い合わせ中かどうか
	server_up_message = "";//データベースへアップするメッセージ
	transferSt = "";//サーバーから送信先へ送るメッセージ
	returnSt = "";
	returnSt1 = "";//変数１をリセット
	returnSt2 = "";//変数２をリセット
	returnSt3 = "";//変数３をリセット
	returnSt4 = "";//変数４をリセット
	returnSt5 = "";//変数５をリセット

	var text  = document.getElementById('sendText').value.replace(/\r\n|\r/g, "\n");
	if (text =="転送データ"){return "";}
	var lines = text.split( '\n' );    
	play_serverProgram(lines,0,st);
	if(server_up_message !=""){
		return server_up_message;
	}
	//server_up_messageに何もない場合（送る、返信など何もない場合）
	else{
		return st['serial_number_clienta'] + ",-," + st['time_clienta'] + "," + st['client_a_name_clienta'] + "," + 
				   "-" + "," + "-" + "," + "-" + "," + 
				   st['juyo_clienta'] + "," + st['repeat_cnt_clienta'] + "," + st['password_clienta'] + "," + 
				   st['sound_clienta'] + "," + st['save_file_clienta'] + "," + st['message_st_clienta'] + "," + transferSt;
	}
}
function play_serverProgram(lines, playno,st){
	var proflag = true;
	//処理番号
	s_cnt =playno;
	
	while(proflag){
		if (lines[s_cnt] == "600"){
			s_cnt = Number(lines[s_cnt + 1]);
			continue;
		}
		//メッセージを送る
		else if (lines[s_cnt] == "520"){
			if (ai_returnData == ""){
				transferSt = st['message_st_clienta'];	
			}
			else{
				transferSt = ai_returnData;
			}
			server_up_message = st['serial_number_clienta'] + ",OK," + st['time_clienta'] + "," + st['client_a_name_clienta'] + "," + 
				   st['client_a_clienta'] + "," + st['client_b_clienta'] + "," + st['data_kind_clienta'] + "," + 
				   st['juyo_clienta'] + "," + st['repeat_cnt_clienta'] + "," + st['password_clienta'] + "," + 
				   st['sound_clienta'] + "," + st['save_file_clienta'] + "," + st['message_st_clienta'] + "," + transferSt;//エラー時とデータ数を合わせるためにmessage_st_clientaは2回
			s_cnt = Number(lines[s_cnt + 1]);
		}
		//エラーメッセージを返信
		else if (lines[s_cnt] == "521"){
			server_up_message = st['serial_number_clienta'] + ",NG," + st['time_clienta'] + "," + st['client_a_name_clienta'] + "," + 
				   st['client_a_clienta'] + "," + st['client_b_clienta'] + "," + st['data_kind_clienta'] + "," + 
				   st['juyo_clienta'] + "," + st['repeat_cnt_clienta'] + "," + st['password_clienta'] + "," + 
				   st['sound_clienta'] + "," + st['save_file_clienta'] + "," + st['message_st_clienta'] + "," + 
				   lines[Number(s_cnt) + 1];
			s_cnt = Number(lines[s_cnt + 2]);
		}
		//変数メッセージを返信
		else if (lines[s_cnt] == "523"){
			var r_st = "";
			//変数１なら
			if (lines[s_cnt + 1] == "変数1"){
				r_st = returnSt1;
			}
			//変数２なら
			else if (lines[s_cnt + 1] == "変数2"){
				r_st = returnSt2;
			}
			//変数３なら
			else if (lines[s_cnt + 1] == "変数3"){
				r_st = returnSt3;
			}
			//変数４なら
			else if (lines[s_cnt + 1] == "変数4"){
				r_st = returnSt4;
			}
			//変数５なら
			else if (lines[s_cnt + 1] == "変数5"){
				r_st = returnSt5;
			}
			//ランダムなら
			else{
				var strings = ["returnSt1", "returnSt2", "returnSt3", "returnSt4", "returnSt5"];// 文字列の配列を作成
				var randomIndex = Math.floor(Math.random() * strings.length);// ランダムなインデックスを生成
				r_st = strings[randomIndex];
			}
			server_up_message = st['serial_number_clienta'] + ",RET," + st['time_clienta'] + "," + st['client_a_name_clienta'] + "," + 
								st['client_a_clienta'] + "," + st['client_b_clienta'] + "," + st['data_kind_clienta'] + "," + 
								st['juyo_clienta'] + "," + st['repeat_cnt_clienta'] + "," + st['password_clienta'] + "," + 
								st['sound_clienta'] + "," + st['save_file_clienta'] + "," + st['message_st_clienta'] + "," + r_st;
			s_cnt = Number(lines[s_cnt + 2]);
		}
		//AIで変換したメッセージを返信
		else if (lines[s_cnt] == "524"){
			if (st['data_kind_clienta'] != "文字"){
				//文字以外のスタンプ、写真はこのコマンドはスルーする
			}
			else{
				if (ai_returnData == ""){
					transferSt = st['message_st_clienta'];	
				}
				else{
					transferSt = ai_returnData;
				}
				server_up_message = st['serial_number_clienta'] + ",RET," + st['time_clienta'] + "," + st['client_a_name_clienta'] + "," + 
					st['client_a_clienta'] + "," + st['client_b_clienta'] + "," + st['data_kind_clienta'] + "," + 
					st['juyo_clienta'] + "," + st['repeat_cnt_clienta'] + "," + st['password_clienta'] + "," + 
					st['sound_clienta'] + "," + st['save_file_clienta'] + "," + st['message_st_clienta'] + "," + ai_returnData;//エラー時とデータ数を合わせるためにmessage_st_clientaは2回
			}
			s_cnt = Number(lines[s_cnt + 1]);
		}		
		//サーバーからプログラムをクライアントに送る（グループ含む）
		else if (lines[s_cnt] == "525" || lines[s_cnt] == "526"){
			transferSt = lines[s_cnt + 1];
			server_up_message = st['serial_number_clienta'] + ",OK," + st['time_clienta'] + "," + st['client_a_name_clienta'] + "," + 
				   st['client_a_clienta'] + "," + lines[s_cnt + 2] + ",サーバプログラム," + 
				   st['juyo_clienta'] + "," + st['repeat_cnt_clienta'] + "," + st['password_clienta'] + "," + 
				   st['sound_clienta'] + "," + st['save_file_clienta'] + "," + st['message_st_clienta'] + "," + transferSt;//エラー時とデータ数を合わせるためにmessage_st_clientaは2回
			s_cnt = Number(lines[s_cnt + 3]);			
		}
		//サーバーから送信元にプログラムを返す
		else if (lines[s_cnt] == "527"){
			server_up_message = st['serial_number_clienta'] + ",NG," + st['time_clienta'] + "," + st['client_a_name_clienta'] + "," + 
				   st['client_a_clienta'] + "," + st['client_b_clienta'] + ",サーバプログラム," + 
				   st['juyo_clienta'] + "," + st['repeat_cnt_clienta'] + "," + st['password_clienta'] + "," + 
				   st['sound_clienta'] + "," + st['save_file_clienta'] + "," + st['message_st_clienta'] + "," + 
				   lines[Number(s_cnt) + 1];
			s_cnt = Number(lines[s_cnt + 2]);
		}
		//パスワード設定
		else if (lines[s_cnt] == "530"){
			serverpwst = lines[s_cnt + 1];
			//console.log(serverpwst);
			s_cnt = Number(lines[s_cnt + 2]);
		}
		//変数1設定
		else if (lines[s_cnt] == "580"){
			returnSt1 = lines[s_cnt + 1];
			//console.log(serverpwst);
			s_cnt = Number(lines[s_cnt + 2]);
		}
		//変数2設定
		else if (lines[s_cnt] == "581"){
			returnSt2 = lines[s_cnt + 1];
			//console.log(serverpwst);
			s_cnt = Number(lines[s_cnt + 2]);
		}
		//変数3設定
		else if (lines[s_cnt] == "582"){
			returnSt3 = lines[s_cnt + 1];
			//console.log(serverpwst);
			s_cnt = Number(lines[s_cnt + 2]);
		}
		//変数4設定
		else if (lines[s_cnt] == "583"){
			returnSt4 = lines[s_cnt + 1];
			//console.log(serverpwst);
			s_cnt = Number(lines[s_cnt + 2]);
		}
		//変数5設定
		else if (lines[s_cnt] == "584"){
			returnSt5 = lines[s_cnt + 1];
			//console.log(serverpwst);
			s_cnt = Number(lines[s_cnt + 2]);
		}
		//AIでメッセージを変換
		else if (lines[s_cnt] == "535"){
			if (st['data_kind_clienta'] == "文字"){
				//ai_returnDataが空なら
				if (ai_returnData == ""){
					//command_waitで0.5秒ごとにaiからの応答があるか確認。その間はbreakでwhile文はとめる
					command_wait("7" , lines , st);//para1:aiで取得 para2:転送データ para3:データベースのデータ
					messageSt = "「" + st['message_st_clienta'] + "」を" + lines[s_cnt + 1] + "にしてその結果だけを返して";
					//console.log("4messageSt " + messageSt);
					//AIに問い合わせモードをtrueに
					ai_mode = true;
					//AIで処理
					change_ai(messageSt);
					break;
				}
				else{
					ai_mode = false;
					s_cnt = Number(lines[s_cnt + 2]);
				}
			}
			//文字以外なら
			else{
				s_cnt = Number(lines[s_cnt + 2]);
			}
		}
		//AIで質問を回答する
		else if (lines[s_cnt] == "536"){
			if (st['data_kind_clienta'] == "文字"){
				//ai_returnDataが空なら
				if (ai_returnData == ""){
					//command_waitで0.5秒ごとにaiからの応答があるか確認。その間はbreakでwhile文はとめる
					command_wait("7" , lines , st);//para1:aiで取得 para2:転送データ para3:データベースのデータ
					messageSt = "「" + st['message_st_clienta'] + "」を20文字程度で答えて";
					//console.log("4messageSt " + messageSt);
					//AIに問い合わせモードをtrueに
					ai_mode = true;
					//AIで処理
					change_ai(messageSt);
					break;
				}
				else{
					ai_mode = false;
					s_cnt = Number(lines[s_cnt + 1]);
				}
			}
			//文字以外なら
			else{
				s_cnt = Number(lines[s_cnt + 1]);
			}
		}
		//AIで指定文字列であれば
		else if (lines[s_cnt] == "564"){
			if (st['data_kind_clienta'] == "文字"){
				//ai_returnDataが空なら
				if (ai_returnData == ""){
					//command_waitで0.5秒ごとにaiからの応答があるか確認。その間はbreakでwhile文はとめる
					command_wait("7" , lines , st);//para1:aiで取得 para2:転送データ para3:データベースのデータ
					messageSt = "「" + st['message_st_clienta'] + "」の文脈が" + lines[s_cnt + 1] + "するような文脈であれば「YES」、そうでなければ「NO」で答えて";
					//console.log("4messageSt " + messageSt);
					//AIに問い合わせモードをtrueに
					ai_mode = true;
					//AIで処理
					change_ai(messageSt);
					break;
				}
				else{
					ai_mode = false;
					console.log("5ai_returnData " + ai_returnData);
					if (ai_returnData.indexOf('YES') != -1){
						s_cnt = Number(lines[s_cnt + 2]);
					}
					else{
						s_cnt = Number(lines[s_cnt + 3]);
						ai_returnData = "";
					}
				}
			}
			//文字以外なら
			else{
				s_cnt = Number(lines[s_cnt + 3]);
			}
		}
		//禁止ワードがあれば
		else if (lines[s_cnt] == "540"){
			var kinsitext  = document.getElementById('kinsiText').value.replace(/\r\n|\r/g, "\n");
			var kinsilines = kinsitext.split( '\n' );  
			var indexbool = false;
			for  (var i = 0; i < kinsilines.length; i++) {
				if (st['message_st_clienta'].indexOf(kinsilines[i]) != -1 && kinsilines[i] != ""){
					indexbool =true;
					break;
				}
			}				
			if (indexbool == true){
				s_cnt = Number(lines[s_cnt + 1]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 2]);
			}
		}
		//禁止ワードがなければ
		else if (lines[s_cnt] == "541"){	
			var kinsitext  = document.getElementById('kinsiText').value.replace(/\r\n|\r/g, "\n");
			var kinsilines = kinsitext.split( '\n' );  
			var indexbool = false;
			for  (var i = 0; i < kinsilines.length; i++) {
				if (st['message_st_clienta'].indexOf(kinsilines[i]) != -1 && kinsilines[i] != ""){
					indexbool =true;
					break;
				}
			}				
			if (indexbool == true){
				s_cnt = Number(lines[s_cnt + 2]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 1]);
			}					
		}
		//登録ワードがあれば
		else if (lines[s_cnt] == "542"){	
			var torokutext  = document.getElementById('torokuText').value.replace(/\r\n|\r/g, "\n");
			var torokulines = torokutext.split( '\n' );  
			var indexbool = false;
			for  (var i = 0; i < torokulines.length; i++) {
				if (st['message_st_clienta'].indexOf(torokulines[i]) != -1 && torokulines[i] != ""){
					indexbool =true;
					break;
				}
			}				
			if (indexbool == true){
				s_cnt = Number(lines[s_cnt + 1]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 2]);
			}					
		}
		//登録ワードがなければ
		else if (lines[s_cnt] == "543"){	
			var torokutext  = document.getElementById('torokuText').value.replace(/\r\n|\r/g, "\n");
			var torokulines = torokutext.split( '\n' );  
			var indexbool = false;
			for  (var i = 0; i < torokulines.length; i++) {
				if (st['message_st_clienta'].indexOf(torokulines[i]) != -1 && torokulines[i] != ""){
					indexbool =true;
					break;
				}
			}				
			if (indexbool == true){
				s_cnt = Number(lines[s_cnt + 2]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 1]);
			}					
		}
		//パスワード一致なら
		else if (lines[s_cnt] == "546"){
			if (serverpwst != ""){
				if (serverpwst == st['password_clienta']){
					s_cnt = Number(lines[s_cnt + 1]);
				}
				else{
					s_cnt = Number(lines[s_cnt + 2]);
				}
			}
			else{
				s_cnt = Number(lines[s_cnt + 2]);
			}
		}
		//パスワード不一致なら
		else if (lines[s_cnt] == "547"){	
			if (serverpwst != ""){
				if (serverpwst == st['password_clienta']){
					s_cnt = Number(lines[s_cnt + 2]);
				}
				else{
					s_cnt = Number(lines[s_cnt + 1]);
				}
			}
			else{
				s_cnt = Number(lines[s_cnt + 1]);
			}
		}
		//繰り返し回数が以上なら
		else if (lines[s_cnt] == "550"){
			//繰り返しでない場合
			if (st['repeat_cnt_clienta'] == "1"){
				s_cnt = Number(lines[s_cnt + 3]);
			}
			//繰り返しの場合
			else{
				if (Number(lines[s_cnt + 1]) <= Number(st['repeat_cnt_clienta'])) {
					s_cnt = Number(lines[s_cnt + 2]);
				}
				else{
					s_cnt = Number(lines[s_cnt + 3]);
				}
			}
		}
		//繰り返し回数が以下なら
		else if (lines[s_cnt] == "551"){	
			//繰り返しでない場合
			if (st['repeat_cnt_clienta'] == "1"){
				s_cnt = Number(lines[s_cnt + 3]);
			}
			//繰り返しの場合
			else{
				if (Number(lines[s_cnt + 1]) >= Number(st['repeat_cnt_clienta'])) {
					s_cnt = Number(lines[s_cnt + 2]);
				}
				else{
					s_cnt = Number(lines[s_cnt + 3]);
				}
			}
		}
		//重要なら
		else if (lines[s_cnt] == "552"){
			//console.log("1");
			if (st['juyo_clienta'] == "true"){
				s_cnt = Number(lines[s_cnt + 1]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 2]);
			}
		}
		//重要でないなら
		else if (lines[s_cnt] == "553"){					
			if (st['juyo_clienta'] == "true"){
				s_cnt = Number(lines[s_cnt + 2]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 1]);
			}
		}
		//出席番号が一致なら
		else if (lines[s_cnt] == "554"){		
			if (st['client_a_clienta'] == lines[Number(s_cnt) + 1]){
				s_cnt = Number(lines[s_cnt + 2]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 3]);
			}	
		}
		//出席番号が不一致なら
		else if (lines[s_cnt] == "555"){		
			if (st['client_a_clienta'] == lines[Number(s_cnt) + 1]){
				s_cnt = Number(lines[s_cnt + 3]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 2]);
			}	
		}
		//文字数が以上なら
		else if (lines[s_cnt] == "558"){		
			if (st['message_st_clienta'].length >= lines[Number(s_cnt) + 1]){
				s_cnt = Number(lines[s_cnt + 2]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 3]);
			}
			//console.log(s_cnt);		
		}
		//文字数が以上なら
		else if (lines[s_cnt] == "559"){		
			if (st['message_st_clienta'].length <= lines[Number(s_cnt) + 1]){
				s_cnt = Number(lines[s_cnt + 2]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 3]);
			}	
		}
		//名前があるなら
		else if (lines[s_cnt] == "560"){
			if (st['client_a_name_clienta'] != ""){
				s_cnt = Number(lines[s_cnt + 1]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 2]);
			}	
		}
		//名前がないなら
		else if (lines[s_cnt] == "561"){		
			if (st['client_a_name_clienta'] != ""){
				s_cnt = Number(lines[s_cnt + 2]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 1]);
			}	
		}
		//名前があるなら
		else if (lines[s_cnt] == "560"){
			if (st['client_a_name_clienta'] != ""){
				s_cnt = Number(lines[s_cnt + 1]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 2]);
			}	
		}
		//グループなら
		else if (lines[s_cnt] == "562"){
			//処理用配列
			var sendnoarraay = new Array();
			//グループ１なら
			if (lines[s_cnt + 1] == "1"){
				sendnoarraay = groupArray1;
			}
			//グループ２なら
			else if  (lines[s_cnt + 1] == "2"){
				sendnoarraay = groupArray2;
			}
			//グループ３なら
			else{
				sendnoarraay = groupArray3;
			}
			var ittibool = false;
			for ( var j = 0; j < sendnoarraay.length; j++ ) {
				//送信者と一致するかどうか
				if (sendnoarraay[j] == st['client_a_clienta']){
					ittibool = true;
					break;
				}
			}
			if (ittibool){
				s_cnt = Number(lines[s_cnt + 2]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 3]);
			}
		}
		//グループでないなら
		else if (lines[s_cnt] == "563"){		
			//処理用配列
			var sendnoarraay = new Array();
			//グループ１なら
			if (lines[s_cnt + 1] == "1"){
				sendnoarraay = groupArray1;
			}
			//グループ２なら
			else if  (lines[s_cnt + 1] == "2"){
				sendnoarraay = groupArray2;
			}
			//グループ３なら
			else{
				sendnoarraay = groupArray3;
			}
			var ittibool = false;
			for ( var j = 0; j < sendnoarraay.length; j++ ) {
				//送信者と一致するかどうか
				if (sendnoarraay[j] == st['client_a_clienta']){
					ittibool = true;
					break;
				}
			}
			if (ittibool){
				s_cnt = Number(lines[s_cnt + 3]);
			}
			else{
				s_cnt = Number(lines[s_cnt + 2]);
			}
		}
		
		else{
			//console.log("set_Interval():play_serverProgram");
			//set_Interval();
			break;
		}
		
		if (s_cnt>=lines.length - 1 || lines[s_cnt] == "601"){
			//console.log("set_Interval():play_serverProgram2");
			//set_Interval();
			break;
		}		
		
	}
}
//命令名、パラメータから転送番号を取得
function Get_ComSendNo(ComName, para){
	var para_data = para.split('-');
	var komoku = para_data[0];
	if (ComName == "Start") {return "600";}
	else if (ComName == "End") {return "601";}
	else if (ComName == "server_Message") {//メッセージ
		if (komoku == "0") { return "520"; }
		else if (komoku == "5") { return "525"; }
		else { return "526"; }
	}
	else if (ComName == "server_Error") {//エラー
		if (komoku == "1") { return "521"; }
		else if (komoku == "3") { return "523"; }
		else { return "527"; }
	}
	else if (ComName == "server_AI"){//AI
		if (komoku == "0") { return "535"; }
		else { return "536"; }
	}
	else if (ComName == "server_Variable"){//変数
		if (komoku == "0") { return "580"; }
		else if (komoku == "1") { return "581"; }
		else if (komoku == "2") { return "582"; }
		else if (komoku == "3") { return "583"; }
		else if (komoku == "4") { return "584"; }
		else {
			var strings = ["580", "581", "582", "583", "584"];// 文字列の配列を作成
			var randomIndex = Math.floor(Math.random() * strings.length);// ランダムなインデックスを生成
			return strings[randomIndex];
		}
	}
	else if (ComName == "server_Setup") {//パスワード
		return "530";
	}
	else if (ComName == "server_Conditional") { //条件分岐
		if (komoku == "0") { return "540"; }
		else if (komoku == "1") { return "541"; }
		else if (komoku == "2") { return "542"; }
		else if (komoku == "3") { return "543"; }
		else if (komoku == "4") { return "546"; }
		else if (komoku == "5") { return "547"; }
		else if (komoku == "6") { return "550"; }
		else if (komoku == "7") { return "551"; }
		else if (komoku == "8") { return "552"; }
		else if (komoku == "9") { return "553"; }
		else if (komoku == "10") { return "554"; }
		else if (komoku == "11") { return "555"; }
		else if (komoku == "12") { return "558"; }
		else if (komoku == "13") { return "559"; }
		else if (komoku == "14") { return "560"; }
		else if (komoku == "15") { return "561"; }
		else if (komoku == "16") { return "562"; }
		else if (komoku == "17") { return "563"; }
		else { return "564"; }
	}
}
//No(命令番号)が何バイト目にあるか　（送信データ用）
function Get_Byte_Adrress(no){	
	//console.log(no);
	var totaladdr = 0;
	for ( var i = 0; i < command_Array.length; i++ ) {
		//命令があれば
		var comm_data = command_Array[i].split(',');
		
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
	//start
	if (comname == "Start") { 
		return 2;
	}
	//end
	else if (comname == "End") { 
		return 1;
	}
	//サーバメッセージ送る
	else if (comname == "server_Message") {
		if (yoso[0] == "0"){
			return 2;
		}
		else{
			return 4;
		}
	}
	//サーバエラー返信
	else if (comname == "server_Error") {
		if (yoso[0] == "4"){
			return 2;
		}
		else{
			return 3;
		}
	}
	//AI
	else if (comname == "server_AI"){
		if (yoso[0] == "0"){
			return 3;
		}
		else{
			return 2;
		}
	}
	//変数
	else if (comname == "server_Variable"){
		return 3;
	}
	//サーバパスワード
	else if (comname == "server_Setup") {
		return 3;
	}
	//サーバ条件分岐
	else if (comname == "server_Conditional") {
		if (yoso[0] == "6" || yoso[0] == "7" || yoso[0] == "10" || yoso[0] == "11" || yoso[0] == "12" || 
			yoso[0] == "13" || yoso[0] == "16" || yoso[0] == "17" || yoso[0] == "18") {
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
	//メッセージ
	if (datastkv.category == "server_Message"){
		//プログラムを送る
		if (eachdata[0] == "5"){
			document.getElementById('para_server_fileprogram').value = eachdata[1];
			document.getElementById('para_server_sendno').value = eachdata[2];	
		}
		//グループにプログラムを送る
		else if (eachdata[0] == "6"){
			document.getElementById('para_server_fileprogram').value = eachdata[1];
			document.getElementById('para_server_group').value = eachdata[2];	
		}
	}
	//エラー返信
	else if (datastkv.category == "server_Error"){
		//エラーを返す
		if (eachdata[0] == "1"){
			document.getElementById('server_m_para_error').value = eachdata[1];	
		}
		//変数を返す
		else if (eachdata[0] == "3"){
			document.getElementById('paraserverrvari').value = eachdata[1];	
		}
		//プログラムを返す
		else if (eachdata[0] == "7"){
			document.getElementById('paraserverrprogram').value = eachdata[1];
		}
	}
	//AI変換
	else if (datastkv.category == "server_AI"){
		document.getElementById('server_ai_change_st').value = eachdata[1];
	}
	//変数
	else if (datastkv.category == "server_Variable"){
		document.getElementById('variable_para_server_st').value = eachdata[1];
	}
	else if (datastkv.category == "server_Setup"){
		//パスワード
		document.getElementById('setup_para_server_pass').value = eachdata[1];
	}
	else if (datastkv.category == "server_Conditional"){
		//回数設定
		if (eachdata[0] == "6" || eachdata[0] == "7"){
			document.getElementById('if_para_server_strepeatcnt').value = eachdata[1];
		}
		//生徒番号
		else if (eachdata[0] == "10" || eachdata[0] == "11"){
			document.getElementById('if_para_server_seitono').value = eachdata[1];
		}
		//文字数
		else if (eachdata[0] == "12" || eachdata[0] == "13"){
			document.getElementById('if_para_server_stlength').value = eachdata[1];
		}
		//グループ
		else if (eachdata[0] == "16" || eachdata[0] == "17"){
			document.getElementById('if_para_server_group').selectedIndex = Number(eachdata[1]);
		}
		//AI
		else if (eachdata[0] == "18"){
			document.getElementById('if_para_server_aijudgment').value = eachdata[1];
		}
	}
}
//パラメータラベルをすべて非表示
function para_label_notdisplay(){
	document.getElementById("server_Message").style.display ="none";
	document.getElementById("para_server_error").style.display ="none";
	document.getElementById("para_server_aichange").style.display ="none";
	document.getElementById("para_server_variable").style.display ="none";
	document.getElementById("para_server_setup").style.display ="none";
	document.getElementById("para_server_if").style.display ="none";
	document.getElementById("para_btn").style.display ="none";	
}
//パラメータラベルを表示
function para_label_display(cate,textst){
	//パラメータ設定をすべて非表示に
	para_label_notdisplay();
	
	if (cate == "server_Message"){
		document.getElementById("server_Message").style.display ="block";
		document.getElementById("paraservermessage").style.display ="block";
		if (textst.indexOf('メッセージ') != -1){
			document.getElementById("parablock_server_program").style.display ="none";
			document.getElementById("parablock_server_sendno").style.display ="none";
			document.getElementById("parablock_server_sendgroup").style.display ="none";
			document.getElementById('paraservermessage').selectedIndex  = 0;
		}
		else if (textst.indexOf('に送る') != -1){
			document.getElementById("parablock_server_program").style.display ="block";
			document.getElementById("parablock_server_sendno").style.display ="block";
			document.getElementById("parablock_server_sendgroup").style.display ="none";
			document.getElementById('paraservermessage').selectedIndex  = 1;
		}
		else{
			document.getElementById("parablock_server_program").style.display ="block";
			document.getElementById("parablock_server_sendno").style.display ="none";
			document.getElementById("parablock_server_sendgroup").style.display ="block";
			document.getElementById('paraservermessage').selectedIndex  = 2;
		}
	}
	else if (cate == "server_Error"){
		//エラー画面を表示
		document.getElementById("para_server_error").style.display ="block";
		document.getElementById("paraservererror").style.display ="block";
		if (textst.indexOf('エラー') != -1){
			document.getElementById("parablock_server_error").style.display ="block";
			document.getElementById("parablock_server_rvari").style.display ="none";
			document.getElementById("parablock_server_rprogram").style.display ="none";
			document.getElementById('paraservererror').selectedIndex  = 0;
		}
		else if (textst.indexOf('変数') != -1){
			document.getElementById("parablock_server_error").style.display ="none";
			document.getElementById("parablock_server_rvari").style.display ="block";
			document.getElementById("parablock_server_rprogram").style.display ="none";
			document.getElementById('paraservererror').selectedIndex  = 1;
		}
		else{
			document.getElementById("parablock_server_error").style.display ="none";
			document.getElementById("parablock_server_rvari").style.display ="none";
			document.getElementById("parablock_server_rprogram").style.display ="block";
			document.getElementById('paraservererror').selectedIndex  = 2;
		}
	}
	else if (cate == "server_AI"){
		document.getElementById("para_server_aichange").style.display ="block";
		document.getElementById("paraserveraichange").style.display ="block";
		if (textst.indexOf('変換') != -1){
			document.getElementById("parablock_server_aichange").style.display ="block";
			document.getElementById("server_ai_change_st").style.display ="inline";			
			document.getElementById('server_ai_change_st').value = "";
			document.getElementById('paraserveraichange').selectedIndex  = 0;
		}
		else{
			document.getElementById("parablock_server_aichange").style.display ="none";
			document.getElementById('paraserveraichange').selectedIndex  = 1;
		}
	}
	//変数
	else if (cate == "server_Variable"){
		document.getElementById("para_server_variable").style.display ="block";
		document.getElementById("paraservervariable").style.display ="block";
		document.getElementById("parablock_server_variable").style.display ="block";
		document.getElementById("variable_para_server_st").style.display ="inline";			
		document.getElementById('variable_para_server_st').value = "";
		if (textst.indexOf('1') != -1){
			document.getElementById('paraservererror').selectedIndex  = 0;
		}
		else if (textst.indexOf('2') != -1){
			document.getElementById('paraservererror').selectedIndex  = 1;
		}
		else if (textst.indexOf('3') != -1){
			document.getElementById('paraservererror').selectedIndex  = 2;
		}
		else if (textst.indexOf('4') != -1){
			document.getElementById('paraservererror').selectedIndex  = 3;
		}
		else if (textst.indexOf('5') != -1){
			document.getElementById('paraservererror').selectedIndex  = 4;
		}
	}
	//パスワード
	else if (cate == "server_Setup"){
		document.getElementById("para_server_setup").style.display ="block";
		document.getElementById("paraserversetup").style.display ="block";
		document.getElementById("parablock_server_pass").style.display ="block";
		document.getElementById("setup_para_server_pass").style.display ="inline";			
		document.getElementById('setup_para_server_pass').value = "";
	}
	else if (cate == "server_Conditional"){
		
		document.getElementById("parablock_server_ifseitono").style.display ="none";
		document.getElementById("parablock_server_ifstlength").style.display ="none";
		document.getElementById("parablock_server_ifrepeatcnt").style.display ="none";
		document.getElementById("parablock_server_group").style.display ="none";
		document.getElementById("parablock_server_ifaijudgment").style.display ="none";

		document.getElementById("para_server_if").style.display ="block";
		document.getElementById("paraserverif").style.display ="block";
		if (textst.indexOf('禁止ワードがある') != -1){
			document.getElementById('paraserverif').selectedIndex  = 0;
		}
		else if (textst.indexOf('禁止ワードがない') != -1){
			document.getElementById('paraserverif').selectedIndex  = 1;
		}
		else if (textst.indexOf('登録ワードがある') != -1){
			document.getElementById('paraserverif').selectedIndex  = 2;
		}
		else if (textst.indexOf('登録ワードがない') != -1){
			document.getElementById('paraserverif').selectedIndex  = 3;
		}
		else if (textst.indexOf('パスワードが一致') != -1){
			document.getElementById('paraserverif').selectedIndex  = 4;
		}
		else if (textst.indexOf('パスワードが不一致') != -1){
			document.getElementById('paraserverif').selectedIndex  = 5;
		}
		//繰り返し
		else if (textst.indexOf('回以上') != -1 || textst.indexOf('回以下') != -1){
			document.getElementById("parablock_server_ifrepeatcnt").style.display ="block";
			if (textst.indexOf('回以上') != -1) {
				document.getElementById('paraserverif').selectedIndex  = 6;
			}
			else{
				document.getElementById('paraserverif').selectedIndex  = 7;
			}
		}
		else if (textst.indexOf('重要') != -1){
			if (textst.indexOf('でない') != -1) {
				document.getElementById('paraserverif').selectedIndex  = 9;
			}
			else{
				document.getElementById('paraserverif').selectedIndex  = 8;
			}
		}
		//生徒番号
		else if (textst.indexOf('生徒番号') != -1){
			document.getElementById("parablock_server_ifseitono").style.display ="block";
			if (textst.indexOf('でない') != -1) {
				document.getElementById('paraserverif').selectedIndex  = 11;
			}
			else{
				document.getElementById('paraserverif').selectedIndex  = 10;
			}
		}
		//文字数
		else if (textst.indexOf('文字以上') != -1 || textst.indexOf('文字以下') != -1){
			document.getElementById("parablock_server_ifstlength").style.display ="block";
			if (textst.indexOf('以上') != -1) {
				document.getElementById('paraserverif').selectedIndex  = 12;
			}
			else{
				document.getElementById('paraserverif').selectedIndex  = 13;
			}
		}
		else if (textst.indexOf('送信者の名前') != -1){
			if (textst.indexOf('ある') != -1) {
				document.getElementById('paraserverif').selectedIndex  = 14;
			}
			else{
				document.getElementById('paraserverif').selectedIndex  = 15;
			}
		}
		else if (textst.indexOf('グループ') != -1){			
			document.getElementById("parablock_server_group").style.display ="block";
			if (textst.indexOf('ある') != -1) {
				document.getElementById('paraserverif').selectedIndex  = 16;
			}
			else{
				document.getElementById('paraserverif').selectedIndex  = 17;
			}
		}
		//AI
		else if (textst.indexOf('AIが') != -1 ){
			document.getElementById("parablock_server_ifaijudgment").style.display ="block";
			document.getElementById('paraserverif').selectedIndex  = 18;
		}
	}
	else{
		
	}	
	//更新ボタンの表示
	if (cate != "Start" && cate != "End"){
		document.getElementById("para_btn").style.display ="block";
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
	if (datastkv.category == "server_Message"){
		var komoku = document.getElementById('paraservermessage').value;
		var strresu="";
		if (komoku.indexOf('メッセージ') != -1){
			parast = "0-0-0-0-0-0";
			dispst = "メッセージを送る";
		}
		else if (komoku.indexOf('に送る') != -1){
			var mst = document.getElementById('para_server_fileprogram').value;
			var nost = document.getElementById('para_server_sendno').value;
			parast = "5-"+ mst + "-" + nost + "-0-0-0-0";
			dispst = "\"" + mst + "\"を" + nost + "番に送る";
		}
		else{
			var mst = document.getElementById('para_server_fileprogram').value;
			var nost = document.getElementById('para_server_group').value;
			parast = "6-"+ mst + "-" + nost + "-0-0-0-0";
			dispst = "\"" + mst + "\"を" + nost + "へ送る";
		}
	}
	else if (datastkv.category == "server_Error"){
		var komoku = document.getElementById('paraservererror').value;
		var strresu="";
		if (komoku.indexOf('エラー') != -1){
			var mst = document.getElementById('server_m_para_error').value;
			parast = "1-"+ mst + "-0-0-0-0";
			if (mst.length > 5) {
				strresu = mst.substr(0,5);
				strresu += "...";
			}
			else{
				strresu = mst;
			}
			dispst = "\"" + strresu + "\"を返信";
		}
		else if (komoku.indexOf('変数') != -1){
			var mst = document.getElementById('paraserverrvari').value;
			parast = "3-"+ mst + "-0-0-0-0";
			dispst = mst + "を返信";
		}
		else{
			var mst = document.getElementById('paraserverrprogram').value;
			parast = "7-"+ mst + "-0-0-0-0";
			dispst = mst + "を返信";
		}		
	}
	else if (datastkv.category == "server_AI"){
		var komoku = document.getElementById('paraserveraichange').value;
		if (komoku.indexOf('変換') != -1){
			var strresu="";
			var para1 = document.getElementById('server_ai_change_st').value;
			parast = "0-"+ para1 +"-0-0-0-0";
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
			parast = "1-0-0-0-0-0";
			dispst = "ﾒｯｾｰｼﾞの質問をAIで取得";
		}
	}
	else if (datastkv.category == "server_Variable"){
		var komoku = document.getElementById('paraservervariable').value;
		var strresu="";
		var para1 = document.getElementById('variable_para_server_st').value;
		if (para1.length > 5) {
			strresu = para1.substr(0,5);
			strresu += "...";
		}
		else{
			strresu = para1;
		}

		if (komoku.indexOf('1') != -1){
			parast = "0-"+ para1 +"-0-0-0-0";
			dispst = "変数1:\""+strresu+"\"";
		}
		else if (komoku.indexOf('2') != -1){
			parast = "1-"+ para1 +"-0-0-0-0";
			dispst = "変数2:\""+strresu+"\"";
		}
		else if (komoku.indexOf('3') != -1){
			parast = "2-"+ para1 +"-0-0-0-0";
			dispst = "変数3:\""+strresu+"\"";
		}
		else if (komoku.indexOf('4') != -1){
			parast = "3-"+ para1 +"-0-0-0-0";
			dispst = "変数4:\""+strresu+"\"";
		}
		else {
			parast = "4-"+ para1 +"-0-0-0-0";
			dispst = "変数5:\""+strresu+"\"";
		}
	}
	else if (datastkv.category == "server_Setup"){
		var para1 = document.getElementById('setup_para_server_pass').value;
		parast = "0-"+ para1 +"-0-0-0-0";
		dispst = "ﾊﾟｽﾜｰﾄﾞ"+ para1;
	}
	else if (datastkv.category == "server_Conditional"){
		var komoku = document.getElementById('paraserverif').value;
		if (komoku.indexOf('禁止ワードがある') != -1 || komoku.indexOf('禁止ワードがない') != -1 ){
			if (komoku.indexOf('ある') != -1){
				parast = "0-0-0-0-0-0";
				dispst = "禁止ワードがある?";
			}
			else {
				parast = "1-0-0-0-0-0";
				dispst = "禁止ワードがない?";
			}
		}
		else if (komoku.indexOf('登録ワードがある') != -1 || komoku.indexOf('登録ワードがない') != -1 ){
			if (komoku.indexOf('ある') != -1){
				parast = "2-0-0-0-0-0";
				dispst = "登録ワードがある?";
			}
			else {
				parast = "3-0-0-0-0-0";
				dispst = "登録ワードがない?";
			}
		}
		else if (komoku.indexOf('一致') != -1){	
			if (komoku.indexOf('不一致') != -1){
				parast = "5-0-0-0-0-0";
				dispst = "パスワード不一致?";
			}
			else {
				parast = "4-0-0-0-0-0";
				dispst = "パスワード一致?";
			}
		}
		else if (komoku.indexOf('回以上') != -1 || komoku.indexOf('回以下') != -1){	
			var komoku = document.getElementById('paraserverif').value;	
			var recnt = document.getElementById('if_para_server_strepeatcnt').value;
			if (komoku.indexOf('以上') != -1){
				parast = "6-" + recnt + "-0-0-0-0";
				dispst = "繰返し" + recnt + "回以上?";
			}
			else {
				parast = "7-" + recnt + "-0-0-0-0";
				dispst = "繰返し" + recnt + "回以下?";
			}
		}
		else if (komoku.indexOf('重要なら') != -1 || komoku.indexOf('重要でない') != -1 ){	
			if (komoku.indexOf('重要なら') != -1){
				parast = "8-0-0-0-0-0";
				dispst = "重要?";
			}
			else {
				parast = "9-0-0-0-0-0";
				dispst = "重要でない?";
			}
		}
		else if (komoku.indexOf('生徒番号') != -1){	
			var sno = document.getElementById('if_para_server_seitono').value;
			if (komoku.indexOf('でない') != -1){
				parast = "11-" + sno + "-0-0-0-0";
				dispst = "生徒番号が" + sno + "でない?";
			}
			else {
				parast = "10-" + sno + "-0-0-0-0";
				dispst = "生徒番号が" + sno + "?";
			}
		}
		else if (komoku.indexOf('文字数') != -1){	
			var svalu = document.getElementById('if_para_server_stlength').value;
			if (komoku.indexOf('以上') != -1){
				parast = "12-" + svalu + "-0-0-0-0";
				dispst = "文字数が" + svalu + "文字以上?";
			}
			else {
				parast = "13-" + svalu + "-0-0-0-0";
				dispst = "文字数が" + svalu + "文字以下?";
			}
		}
		else if (komoku.indexOf('送信者の名前') != -1){	
			if (komoku.indexOf('ある') != -1){
				parast = "14-0-0-0-0-0";
				dispst = "送信者の名前ある?";
			}
			else {
				parast = "15-0-0-0-0-0";
				dispst = "送信者の名前ない?";
			}
		}
		else if (komoku.indexOf('グループ') != -1){	
			var svalu = document.getElementById('if_para_server_group').selectedIndex + 1;
			if (komoku.indexOf('ある') != -1){
				parast = "16-" + svalu + "-0-0-0-0";
				dispst = "送信者がグループ" + svalu + "\nにいる?";
			}
			else {
				parast = "17-" + svalu + "-0-0-0-0";
				dispst = "送信者がグループ" + svalu + "\nにいない?";
			}
		}
		else if (komoku.indexOf('AIが判断') != -1){	
			var judgeSt = document.getElementById('if_para_server_aijudgment').value;
			var strresu="";
			if (judgeSt.length > 5) {
				strresu = judgeSt.substr(0,5);
				strresu += "...";
			}
			else{
				strresu = judgeSt;
			}
			parast = "18-" + judgeSt + "-0-0-0-0";
			dispst = "AIが\"" + strresu + "\"と判断?";
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