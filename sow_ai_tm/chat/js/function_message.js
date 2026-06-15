//送信者の時の吹き出し
function message_right(dataSt){
	var strArray = dataSt.split(',');//カンマ区切りを分解
	var timearray = strArray[2].split( ' ' );//時間を分解
	addst = '<div class="mode_send">';
	if (strArray[7] == ""){
		addst += timearray[1] + "：　" +strArray[6] + "へ送信<br>";
	}
	else{
		addst += timearray[1] + "：　" +strArray[7] + "へ送信<br>";
	}
	addst += '</div>';
	//画面背景色が指定されていれば
	if (strArray[17] != ""){
		changeBoxColor( strArray[17] );
	}
	else{
		changeBoxColor( "#fafaa2" );
	}			
	//メッセージ
	if (strArray[8] == "文字" || strArray[8] == "プログラム" ) {
		//繰り返し回数分だけ繰り返し
		for ( var i = 0; i < Number(strArray[11]); i++ ) {
			addst += '<div class="mode_send">';
			//メッセージ背景色
			if (strArray[16] != ""){
				addst += '<div class="fuki-right" style="background-color:'+ strArray[16] +'">';
			}
			else{
				addst += '<div class="fuki-right">';
			}
			//文字の大きさ
			if (strArray[14] != ""){
				//文字色
				if (strArray[15] != ""){addst += '<p style="font-size: '+ strArray[14] + 'pt;color:' + strArray[15] + '">';}
				else{ addst += '<p style="font-size: '+ strArray[14]+'pt">';}
			}
			else{
				if (strArray[15] != ""){addst += '<p style="color:' + strArray[15] + '">';}
				else {addst += '<p>';}
			}
			if (strArray[8] == "文字"){
				//メッセージ
				addst += escapeHtml(strArray[19]);
			}
			else{
				var pr_data = strArray[19].split( '=' );//分解
				addst += "データ：" + escapeHtml(pr_data[0]);
			}
			//終了タグ
			addst += '</p></div></div>';
		}
	}
	else if (strArray[8] == "スタンプ") {
		//繰り返し回数分だけ繰り返し
		for ( var i = 0; i < Number(strArray[11]); i++ ) {
			addst += '<div class="mode_send">';
			addst += '<img src="img/illust/'+strArray[19]+'" width="79" height="79" alt=""/>';
			addst += '</div>';
		}
	}
	else if (strArray[8] == "写真") {
		//繰り返し回数分だけ繰り返し
		for ( var i = 0; i < Number(strArray[11]); i++ ) {
			addst += '<div class="mode_send">';
			addst += '<img src="https://www.hisatomi-kk.com/app/upload/'+strArray[18]+'" alt=""/>';
			addst += '</div>';
		}
	}

	if (addst != ""){
		allMessage = addst;
	}

	delayedCall(100,function(){   
        //スクロールを一番下へ
        scroll_change();
    });

	return allMessage;
}
//受信者の時の吹き出し datast: id,時間,名前,clientA,server,種類,重要,繰り返し回数,パスワード,音,保存ファイル名,メッセージ
function message_left(dataSt, status_st){
	//受信プログラムをデータに変換
	changeRecieveData();
	//受信プログラムを実行
	RecieveDataplay(dataSt);

	var strArray = dataSt.split(',');//カンマ区切りを分解
	//タイムスタンプ
		var timearray = strArray[1].split( ' ' );//時間を分解
		var reSt = "";
		addst = '<div class="mode_recieve">' + timearray[1] + "：　";
		//OKの場合
		if ( status_st != "NG"){
			if (strArray[5] == "サーバプログラム"){
				addst +=  "サーバ";	
			}else{
				addst +=  strArray[3];
			}			
			reSt = "から受信<br>";
		}
		//エラーの場合
		else{
			addst +=  strArray[4];	
			reSt = "から返信<br>";
		}
		//名前がないもしくはエラーの場合 「時間」:「送信番号」から受信
		if ( strArray[2] == "" || status_st == "NG"){
			addst += reSt;	
		}
		else{
			addst += "(" + strArray[2] + ")" + reSt;	
		}
		addst += '</div>';	
	//メッセージ
	if (strArray[5] == "文字" || strArray[5] == "プログラム" || strArray[5] == "サーバプログラム") {
		//繰り返し回数分だけ繰り返し
		for ( var i = 0; i < Number(strArray[7]); i++ ) {
			addst += '<div class="mode_recieve">';
			//メッセージ背景色
			if (recieve_back_color != ""){	
				addst += '<div class="fuki-left" style="background-color:'+ recieve_back_color +'">';
			}
			else{addst += '<div class="fuki-left">';}
			//文字の大きさ
			if (recieve_font_size != ""){
				//文字色
				if (recieve_font_color != ""){addst += '<p style="font-size: '+ recieve_font_size + 'pt;color:' + recieve_font_color + '">';}
				else{ addst += '<p style="font-size: '+ recieve_font_size +'pt">';}
			}
			else{
				if (recieve_font_color != ""){addst += '<p style="color:' + recieve_font_color + '">';}
				else {addst += '<p>';}
			}
			if (strArray[5] == "文字" && (strArray[11].indexOf('ud1') == -1 && strArray[11].indexOf('uc7') == -1)){
				//メッセージ
				addst += escapeHtml(strArray[11]);
			}
			else{
				//プログラムを送信し、エラーを受信した際（データ：がない場合）
				if (strArray[11].indexOf('=') == -1){
					//メッセージ
					addst += escapeHtml(strArray[11]);
				}
				else{
					var pr_data = strArray[11].split( '=' );//分解
					addst += "データ：" + escapeHtml(pr_data[0]);
					//オーロラへ転送
					if (pr_data[1] != ""){
						//pro_senddata_ipad(pr_data[1]);
						pro_senddataHID(pr_data[1]);
					}
				}
			}
			//終了タグ
			addst += '</p></div></div>';
		}
	}
	else if (strArray[5] == "スタンプ") {
		//繰り返し回数分だけ繰り返し
		for ( var i = 0; i < Number(strArray[7]); i++ ) {
			//スタンプを送信し、エラーを受信した際（.pngがない場合）
			if (strArray[11].indexOf('.png') == -1){
				addst += '<div class="mode_recieve">';
				//メッセージ背景色
				if (recieve_back_color != ""){	
					addst += '<div class="fuki-left" style="background-color:'+ recieve_back_color +'">';
				}
				else{addst += '<div class="fuki-left">';}
				//文字の大きさ
				if (recieve_font_size != ""){
					//文字色
					if (recieve_font_color != ""){addst += '<p style="font-size: '+ recieve_font_size + 'pt;color:' + recieve_font_color + '">';}
					else{ addst += '<p style="font-size: '+ recieve_font_size +'pt">';}
				}
				else{
					if (recieve_font_color != ""){addst += '<p style="color:' + recieve_font_color + '">';}
					else {addst += '<p>';}
				}
				//メッセージ
				addst += escapeHtml(strArray[11]);
				//終了タグ
				addst += '</p></div></div>';
			}
			else{
				addst += '<div class="mode_recieve">';
				addst += '<img src="img/illust/'+strArray[11]+'" width="79" height="79" alt=""/>';
				addst += '</div>';
			}
		}
	}
	else if (strArray[5] == "写真") {
		//繰り返し回数分だけ繰り返し
		for ( var i = 0; i < Number(strArray[7]); i++ ) {
			//写真を送信し、エラーを受信した際（.png .jpgがない場合）
			if (strArray[11].indexOf('.png') == -1 && strArray[11].indexOf('.jpg') == -1 && strArray[11].indexOf('.jpeg') == -1){
				addst += '<div class="mode_recieve">';
				//メッセージ背景色
				if (recieve_back_color != ""){	
					addst += '<div class="fuki-left" style="background-color:'+ recieve_back_color +'">';
				}
				else{addst += '<div class="fuki-left">';}
				//文字の大きさ
				if (recieve_font_size != ""){
					//文字色
					if (recieve_font_color != ""){addst += '<p style="font-size: '+ recieve_font_size + 'pt;color:' + recieve_font_color + '">';}
					else{ addst += '<p style="font-size: '+ recieve_font_size +'pt">';}
				}
				else{
					if (recieve_font_color != ""){addst += '<p style="color:' + recieve_font_color + '">';}
					else {addst += '<p>';}
				}
				//メッセージ
				addst += escapeHtml(strArray[11]);
				//終了タグ
				addst += '</p></div></div>';
			}
			else{
				addst += '<div class="mode_recieve">';
				addst += '<img src="https://www.hisatomi-kk.com/app/upload/'+strArray[10]+'" alt=""/>';
				addst += '</div>';
			}
		}
	}
	else if (strArray[5] == "サーバ"){
		//繰り返し回数分だけ繰り返し
		for ( var i = 0; i < Number(strArray[7]); i++ ) {
			addst += '<div class="mode_recieve">';
			//メッセージ背景色
			if (recieve_back_color != ""){	
				addst += '<div class="fuki-left" style="background-color:'+ recieve_back_color +'">';
			}
			else{addst += '<div class="fuki-left">';}
			//文字の大きさ
			if (recieve_font_size != ""){
				//文字色
				if (recieve_font_color != ""){addst += '<p style="font-size: '+ recieve_font_size + 'pt;color:' + recieve_font_color + '">';}
				else{ addst += '<p style="font-size: '+ recieve_font_size +'pt">';}
			}
			else{
				if (recieve_font_color != ""){addst += '<p style="color:' + recieve_font_color + '">';}
				else {addst += '<p>';}
			}
			//メッセージ
			addst += escapeHtml(strArray[11]);
			//終了タグ
			addst += '</p></div></div>';
		}
	}

	//画面背景色が指定されていれば
	if (recieve_disp_color != ""){
		changeBoxColor( recieve_disp_color );
	}
	else{
		changeBoxColor( "#fafaa2" );
	}
	//着信音があれば
	if (recieve_sound != ""){
		wav(recieve_sound);
	}

	if (addst != ""){
		allMessage = addst;
	}

	delayedCall(100,function(){   
        //スクロールを一番下へ
        scroll_change();
    });

	return allMessage;

}
//サーバのログ表示
function message_server(datast){//datast: id,OK(NG),時間,名前,client_A,client_B,種類,重要,繰り返し回数,パスワード,音,保存ファイル名,元メッセージ,送信するメッセージ
	var pdata = datast.split(',');//分解
	var timearray = pdata[2].split( ' ' );//時間を分解
	//var time = new Date(pdata[2]);
	//var disp_time = ("0"+(time.getHours() + 1)).slice(-2) + ":" + ("0"+(time.getMinutes() + 1)).slice(-2) + ":" + ("0"+(time.getSeconds() + 1)).slice(-2);
	//送信先を取得（グループかどうか）
	var c_b_no = "";
	var groupArray = pdata[5].split('=');//=区切りを分解 （group= 送信先） group= 30/31
	if (groupArray.length > 2){
		c_b_no = "グループ";
	}
	else{
		c_b_no = pdata[5];
	}
	var addst = "";
	var clientAname = "";
	//名前があれば
	if (pdata[3] != ""){
		clientAname = "("+pdata[3] + ")";
	}
	if (pdata[6] == "文字" || pdata[6] == "写真"){
		addst =  timearray[1] + "：　" + pdata[4] + clientAname +" から「" + pdata[12] + "」を受信\n";
		if (pdata[1] == "OK"){
			addst += timearray[1] + "：　" + c_b_no + " へ「" + escapeHtml(pdata[13]) + "」を送信\n";
		}
		else{
			addst += timearray[1] + "：　" + pdata[4] + " へ「" + pdata[13] + "」を返信\n";
		}
	}
	else if (pdata[6] == "スタンプ"){
		var filenamedata = pdata[12].substring(0, pdata[12].indexOf(".png"));	
		addst += timearray[1] + "：　" + pdata[4] + clientAname + "から 「スタンプ " + filenamedata + " 」を受信\n";
		if (pdata[1] == "OK"){
			addst += timearray[1] + "：　" + c_b_no + "へ 「スタンプ " + filenamedata + " 」を送信\n";
		}
		else{
			addst += timearray[1] + "：　" + pdata[4] + " へ「" + pdata[13] + "」を返信\n";
		}
	}	
	else if (pdata[6] == "プログラム") {
		var filenamedata = pdata[12].substring(0, pdata[12].indexOf("="));
		addst += timearray[1] + "：　" + pdata[4] + clientAname + "から 「" + filenamedata + " 」を受信\n";
		if (pdata[1] == "OK"){
			addst += timearray[1] + "：　" + c_b_no + "へ 「" + filenamedata + " 」を送信\n";
		}
		else{
			addst += timearray[1] + "：　" + pdata[4] + " へ「" + pdata[13] + "」を返信\n";
		}
	}
	else if (pdata[6] == "サーバプログラム") {
		var filenamedata = pdata[13].substring(0, pdata[13].indexOf("="));
		addst += timearray[1] + "：　" + pdata[4] + clientAname + "から 「" + pdata[12] + " 」を受信\n";
		if (pdata[1] == "OK"){
			addst += timearray[1] + "：　" + c_b_no + "へ 「" + filenamedata + " 」を送信\n";
		}
		else{
			addst += timearray[1] + "：　" + pdata[4] + " へ「" + filenamedata + "」を返信\n";
		}
	}
	else if (pdata[6] == "サーバ"){
		addst =  timearray[1] + "：　" + "クライアント" + c_b_no + "へ「"+ pdata[17] +"」を送信\n";
	}

	var allmessage = document.getElementById('messages').value;//通信ログ
	if (document.getElementById("log_enc").checked){
		var newstdec = Decrypt(allmessage) + addst;	
		allmessage = Encrypt(newstdec);
	}
	else{						
		allmessage += addst;
	}

	document.form6.logtextarea.value = allmessage;
	
	delayedCall(100,function(){   
        //スクロールを一番下へ
        scroll_change();
    });
}

//送信処理(mode→1:webUSB 2:HIDUSB 3:SOW 4:サンプル)
function sendMessage(mode,schoolid) {
	try{
		//送受信処理中なら何もしない
		if (bool_sending || bool_recieving){return;}
		
		//プログラムの自動保存
		client_autosave();

		if (chat_group_data == ""){
			alert("「利用開始」されていません。");
			return;
		}
		
		if (document.getElementById('play_set').innerHTML.indexOf('実行') == -1){
			alert("現在処理中の為実行できません。");
			return;
		}

		if (mode == 1){
			if (usb_info() == false){		
				alert("オーロラ(CUC)本体が接続されておりません");
				return;
			}
		}
		else if (mode == 2){
			if (usb_info_HID() == false){		
			alert("オーロラ(UC)本体が接続されておりません");
			return;
			}
		}
		school_id = schoolid;
		//文字プログラムに変換
		outputProData();
		//転送データに変換
		changeSendData();
		
		//改行コードを\nに統一
		var text  = document.getElementById('proText').value.replace(/\r\n|\r/g, "\n");
		var lines = text.split( '\n' );
		for  (var i = 0; i < lines.length; i++) { 
			if (lines[i] == "start" || lines[i] == "server_start"){
				break;
			}
			if (i == lines.length - 1){
				alert("開始命令がありません。");
				return;
			}
		}
			
		var text  = document.getElementById('sendText').value.replace(/\r\n|\r/g, "\n");
		if (text == ""){return;}
		var lines = text.split( '\n' );    
		if (lines.length > 120){ 
			alert("データ転送量が超えています。");
			return;
		}

		//AIコマンドが繰り返しの中にあればエラー
		if (!repeat_check_ai(lines)) {
			alert("AI機能を繰り返し命令の中に入れることはできません");
			return;
		}
		//AIが複数あったらエラーに
		if (!ai_multi(lines)) {
			alert("AI機能を複数プログラムすることはできません");
			return;
		}

  		//送信フラグをたてる
		bool_sending = true;

		//自分の番号
		clientA = document.getElementById('myno').value;
		//自分の名前
		myName = document.getElementById('myname').value;
		
		//信号待ちに変わっていたら元に戻す
		document.getElementById('play_set').innerHTML = "　実行　";
		if (timerid != null){
			clearInterval(timerid);
		}	
		//console.log(timerid);		
		
		serverNo = "";//サーバ設定
		pwst = "";//パスワード
		juyo = "false";//重要かどうか
		disp_confirm = "false";//確認画面を出すかどうか
		fsize = "";//フォントサイズ
		fcolor = "";//フォント色
		backcolor = "";//背景色
		dispback = "";//ディスプレイ背景色
		repeat_kaisu = 1;//繰り返し回数
		ai_returnData = "";//aiで取得した文字列
		ai_prompt = "";//aiのプロンプト
		ai_Before_st = "";//aiで変換する前の送信メッセージ

		variable_St1 = "";//クライアントの変数１
		variable_St2 = "";//クライアントの変数２
		variable_St3 = "";//クライアントの変数３
		variable_St4 = "";//クライアントの変数４
		variable_St5 = "";//クライアントの変数５

		//先頭から処理するので 0
		play_sendProgram(lines,0,mode);

		groupname = "";//グループはsendSt作成してからリセット
		
		//送信フラグをおろす
		bool_sending = false;

	}catch(e){}
}
//送信プログラムの実行(lines:データ、playno:処理番号、mode:sowかサンプル版かオーロラ版か)
function play_sendProgram(lines, playno, mode){
	try{
		let proflag = true;
		//処理番号
		cnt =playno;
		while (proflag){
			if (cnt == -1){break;}
			if (lines[cnt] == "600"){
				cnt += Number(lines[cnt + 1]);
				continue;
			}
			//メッセージを送るなら
			else if (lines[cnt] == "620" || lines[cnt] == "626"){
				clientB = lines[cnt + 1];
				//送信先がなかったら（グループ設定がない場合等）
				if (clientB == ""){
					alert("送信先が設定されていません");
					return;
				}

				var s_message_st = "";
				//入力メッセージを送るなら
				if (lines[cnt + 2] == "input_message"){
					s_message_st = document.getElementById('mymessage').value;
				}
				//変数１を送るなら
				else if (lines[cnt + 2] == "variable1"){
					s_message_st = variable_St1;
				}
				else if (lines[cnt + 2] == "variable2"){
					s_message_st = variable_St2;
				}
				else if (lines[cnt + 2] == "variable3"){
					s_message_st = variable_St3;
				}
				else if (lines[cnt + 2] == "variable4"){
					s_message_st = variable_St4;		
				}
				else{
					s_message_st = variable_St5;
				}

				if (ai_returnData == "" && s_message_st == ""){
					alert("メッセージもしくは変数が入力されておりません。");
					return;
				}
				if (s_message_st.length >= 20) {
					alert("20文字以上入力できません");
					return; // 処理を終了
				}
				//サンプル版の場合
				if (mode == 4){
					messageSt = "サンプル文です";	
				}
				else{
					if (ai_returnData == ""){
						messageSt = s_message_st;
					}
					else{
						messageSt = ai_returnData;
					}
				}
				var messageSt_disp = escapeHtml(messageSt);//特殊文字を置換(messageStに代入してはダメ。表示用に変換)
				messageSt = messageSt.replace(",", ".");//カンマがあったら.に置換
				//確認画面表示なら
				if (disp_confirm == "true"){
					// 「OK」時の処理開始 ＋ 確認ダイアログの表示
					if(window.confirm('「' + messageSt_disp + '」を送ります。よろしいですか？')){

					}
					//キャンセルが押されtら
					else{
						//set_Interval();
						break;
					}
				}
				var nowtime = get_time();	
				var sendSt = school_id + "," + chat_group_data + "," + nowtime + "," + myName + "," + 
							clientA + "," + serverNo + "," + clientB + "," + groupname + "," + 
							"文字" + "," + juyo +  ",," + repeat_kaisu +  "," + pwst +  "," + sound +  "," + fsize +  "," + 
							fcolor +  "," + backcolor +  "," + dispback +  ",," + messageSt +  "," + ai_Before_st + "," + ai_prompt;
				document.getElementById('mymessage').value ="";
				send_action(sendSt);
				cnt = Number(lines[cnt + 3]);
			}
			//写真を送るなら
			else if (lines[cnt] == "623" || lines[cnt] == "628"){
				//サンプル版の場合
				if (mode == 4){
					alert('サンプル版の為画像は送れません。');
					return;
				}
				if (document.getElementById('img_file').value == ""){
					alert("写真が選択されていません。");
					return;
				}
				let picture_filename = document.getElementById('img_file').value;
				if (picture_filename != ""){
					clientB = lines[cnt + 1];
					//送信先がなかったら（グループ設定がない場合等）
					if (clientB == ""){
						alert("送信先が設定されていません");
						return;
					}
					//確認画面表示なら
					if (disp_confirm == "true"){
						// 「OK」時の処理開始 ＋ 確認ダイアログの表示
						if(window.confirm('「' + picture_filename + '」を送ります。よろしいですか？')){
		
						}
						//キャンセルが押されtら
						else{
							//set_Interval();
							break;
						}
					}

					var nowtime = get_time();
					//拡張子取得
					var extst = get_extension(picture_filename);
					if (extst == "jpeg"){extst = "jpg";}
					
					var sendSt = school_id + "," + chat_group_data + "," + nowtime + "," + myName + "," + 
							clientA + "," + serverNo + "," + clientB + "," + groupname + "," + 
							"写真" + "," + juyo +  ",," + repeat_kaisu +  "," + pwst +  "," + sound +  "," + fsize +  "," + 
							fcolor +  "," + backcolor +  "," + dispback +  "," + up_filename + "." + extst +  "," + picture_filename.split('\\').pop().split('/').pop() +  ",,";

					send_action(sendSt);
				}
				cnt = Number(lines[cnt + 2]);
			}
			//スタンプを送るなら
			else if (lines[cnt] == "622" || lines[cnt] == "627"){
				clientB = lines[cnt + 1];	
				//送信先がなかったら（グループ設定がない場合等）
				if (clientB == ""){
					alert("送信先が設定されていません");
					return;
				}
				if (document.getElementById('m_para_stampno').selectedIndex == 0){
					alert("スタンプが選択されていません。");
					return;
				}
				//サンプル版の場合
				if (mode == 4){
					messageSt = "0.png";	
				}
				else{
					messageSt = lines[cnt + 2] + ".png";
				}
				//確認画面表示なら
				if (disp_confirm == "true"){
					// 「OK」時の処理開始 ＋ 確認ダイアログの表示
					if(window.confirm('スタンプを送ります。よろしいですか？')){

					}
					//キャンセルが押されtら
					else{
						//set_Interval();
						break;
					}
				}

				var nowtime = get_time();	
				var sendSt = school_id + "," + chat_group_data + "," + nowtime + "," + myName + "," + 
							clientA + "," + serverNo + "," + clientB + "," + groupname + "," + 
							"スタンプ" + "," + juyo +  ",," + repeat_kaisu +  "," + pwst +  "," + sound +  "," + fsize +  "," + 
							fcolor +  "," + backcolor +  "," + dispback +  ",," + messageSt +  ",,";

				send_action(sendSt);
				cnt = Number(lines[cnt + 3]);
			}
			//プログラムデータを送るなら
			else if (lines[cnt] == "450" || lines[cnt] == "451"){
				clientB = lines[cnt + 1];	
				//送信先がなかったら（グループ設定がない場合等）
				if (clientB == ""){
					alert("送信先が設定されていません");
					return;
				}

				//送信データをファイル名
				var dataSt = "";
				var profile_data_filename = "";
				if (lines[cnt + 2] == "file1"){
					dataSt = profile_data1;
					profile_data_filename = profile_data_filename1;
				}
				else if (lines[cnt + 2] == "file2"){
					dataSt = profile_data2;
					profile_data_filename = profile_data_filename2;
				}
				else{
					dataSt = profile_data3;
					profile_data_filename = profile_data_filename3;
				}
				if (dataSt == ""){				
					cnt = Number(lines[cnt + 3]);
					continue;
				}

				//確認画面表示なら
				if (disp_confirm == "true"){
					// 「OK」時の処理開始 ＋ 確認ダイアログの表示
					if(window.confirm('プログラムを送ります。よろしいですか？')){

					}
					//キャンセルが押されtら
					else{
						//set_Interval();
						break;
					}
				}

				var nowtime = get_time();	
				var sendSt = school_id + "," + chat_group_data + "," + nowtime + "," + myName + "," + 
							clientA + "," + serverNo + "," + clientB + "," + groupname + "," + 
							"プログラム" + "," + juyo +  ",," + repeat_kaisu +  "," + pwst +  "," + sound +  "," + fsize +  "," + 
							fcolor +  "," + backcolor +  "," + dispback +  ",," + profile_data_filename + "=" + dataSt +  ",,";

				send_action(sendSt);
				cnt = Number(lines[cnt + 2]);
			}
			//AIで送るなら
			else if (lines[cnt] == "630"){
				clientB = lines[cnt + 1];
				//サンプル版の場合
				if (mode == 4){
					alert("サンプル版はAIの利用はできません");
					return;
				}
				//送信先がなかったら（グループ設定がない場合等）
				if (clientB == ""){
					alert("送信先が設定されていません");
					return;
				}
				if (document.getElementById('mymessage').value == ""){
					alert("メッセージが入力されておりません。");
					return;
				}
				
				messageSt = lines[cnt + 2];

				var nowtime = get_time();	
				var sendSt = school_id + "," + chat_group_data + "," + nowtime + "," + myName + "," + 
							clientA + "," + serverNo + "," + clientB + "," + groupname + "," + 
							"文字" + "," + juyo +  ",," + repeat_kaisu +  "," + pwst +  "," + sound +  "," + fsize +  "," + 
							fcolor +  "," + backcolor +  "," + dispback +  ",," + messageSt +  ",,";
				
				//AIで処理
				send_ai(sendSt, messageSt);
				cnt = Number(lines[cnt + 3]);
			}
			//AIで処理する
			else if (lines[cnt] == "631"){
				//サンプル版の場合
				if (mode == 4){
					alert("サンプル版はAIの利用はできません");
					return;
				}
				if (document.getElementById('mymessage').value == ""){
					alert("メッセージが入力されておりません。");
					return;
				}
				
				//console.log(data);
				//ai_returnDataが空なら
				if (ai_returnData == ""){
					document.getElementById('play_set').innerHTML = "AI処理待ち";
					//command_waitで0.5秒ごとにaiからの応答があるか確認。その間はbreakでwhile文はとめる
					command_wait("6" , lines , "");//para1:aiで取得 para2:転送データ para3:しきい値はなし
					messageSt = "「" + lines[cnt + 2] + "」の質問を" + lines[cnt + 1] + "文字以内で結果だけを返して";
					console.log(messageSt);
					//AIで処理
					change_ai(messageSt);
					cnt = Number(lines[cnt + 3]);
					break;
				}
				else{				
					cnt = Number(lines[cnt + 3]);
				}
			}
			//AIで処理する(パラメータ付き)
			else if (lines[cnt] == "632"){
				//サンプル版の場合
				if (mode == 4){
					alert("サンプル版はAIの利用はできません");
					return;
				}
				if (document.getElementById('mymessage').value == ""){
					alert("メッセージが入力されておりません。");
					return;
				}
				
				//console.log(data);
				//ai_returnDataが空なら
				if (ai_returnData == ""){
					document.getElementById('play_set').innerHTML = "AI処理待ち";
					//command_waitで0.5秒ごとにaiからの応答があるか確認。その間はbreakでwhile文はとめる
					command_wait("6" , lines , "");//para1:aiで取得 para2:転送データ para3:しきい値はなし
					messageSt = "「" + lines[cnt + 2] + "」を" + lines[cnt + 1] + "にしてその結果だけを返して";
					ai_Before_st = lines[cnt + 2];
					ai_prompt = lines[cnt + 1];
					console.log(messageSt);
					//AIで処理
					change_ai(messageSt);
					cnt = Number(lines[cnt + 3]);
					break;
				}
				else{				
					cnt = Number(lines[cnt + 3]);
				}
			}
			//AIで処理する(プロンプト)
			else if (lines[cnt] == "633"){
				//サンプル版の場合
				if (mode == 4){
					alert("サンプル版はAIの利用はできません");
					return;
				}
				if (document.getElementById('mymessage').value == ""){
					alert("メッセージが入力されておりません。");
					return;
				}
				
				//console.log(data);
				//ai_returnDataが空なら
				if (ai_returnData == ""){
					document.getElementById('play_set').innerHTML = "AI処理待ち";
					var promt_text = document.getElementById('prompttext').value;
					var promt_selectvalue = document.getElementById('promptSelect').value;
					//command_waitで0.5秒ごとにaiからの応答があるか確認。その間はbreakでwhile文はとめる
					command_wait("6" , lines , "");//para1:aiで取得 para2:転送データ para3:しきい値はなし
					messageSt = "「" + lines[cnt + 1] + "」" + promt_text + promt_selectvalue;
					ai_Before_st = lines[cnt + 1];
					ai_prompt = promt_text;
					console.log(messageSt);
					//AIで処理
					change_ai(messageSt);
					cnt = Number(lines[cnt + 2]);
					break;
				}
				else{				
					cnt = Number(lines[cnt + 2]);
				}
			}
			//パスワード設定
			else if (lines[cnt] == "640"){
				pwst = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
			}
			//サーバ設定
			else if (lines[cnt] == "641"){
				serverNo = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
			}
			//重要設定
			else if (lines[cnt] == "642"){
				juyo = "true";
				cnt = Number(lines[cnt + 1]);
			}
			//確認画面
			else if (lines[cnt] == "643"){
				disp_confirm = "true";
				cnt = Number(lines[cnt + 1]);
			}
			//フォント大きさ
			else if (lines[cnt] == "660"){
				fsize = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
			}
			//フォント色
			else if (lines[cnt] == "661"){
				fcolor = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
			}
			//背景色
			else if (lines[cnt] == "662"){
				backcolor = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
			}
			//ディスプレイ背景色
			else if (lines[cnt] == "663"){
				dispback = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
			}
			//時刻まで待つ
			else if (lines[cnt] == "420"){
				var date1 = new Date();
				var ji = date1.getHours();
				var hun = date1.getMinutes();
				//現在時 < 設定時なら、それ以外はスルー
				if (Number(ji) < Number(lines[cnt + 1] + 1)){
					document.getElementById('play_set').innerHTML = "時刻待ち";
					command_wait("8" , lines , lines[cnt + 1] + "-" + lines[cnt + 2]);//para1:暗くなるまで para2:転送データ para3:明るさ（温度）数値
					cnt = Number(lines[cnt + 3]);
					break;
				}
				else if (Number(ji) == Number(lines[cnt + 1] + 1) || Number(hun) < Number(lines[cnt + 2])){
					command_wait("8" , lines , lines[cnt + 1] + "-" + lines[cnt + 2]);//para1:暗くなるまで para2:転送データ para3:明るさ（温度）数値
					cnt = Number(lines[cnt + 3]);
					break;
				}
				else{				
					cnt = Number(lines[cnt + 3]);
				}
			}
			//TeachableMachineが〇〇まで停止
			else if (lines[cnt] == "421"){
				//%取得
				if (doesLabelExist(lines[cnt + 1])<= lines[cnt + 2]){
					document.getElementById('play_set').innerHTML = "\"" + lines[cnt + 1] + "\"待ち";
					command_wait("9" , lines , lines[cnt + 1]+":"+lines[cnt + 2]);//para1:暗くなるまで para2:転送データ para3:クラス:数値
					cnt = Number(lines[cnt + 3]);
					break;
				}
				else if (doesLabelExist(lines[cnt + 1]) == null){					
					set_Interval();
					break;
				}
				else{
					cnt = Number(lines[cnt + 3]);
				}
			}
			//明るくなるまで待つ
			else if (lines[cnt] == "422"){
				var data  = document.getElementById('cdSData').innerHTML;
				//console.log(data);
				//実測値 < 設定値なら、それ以外はスルー
				if (Number(data) < Number(lines[cnt + 1])){
					document.getElementById('play_set').innerHTML = "明るさ待ち";
					command_wait("1" , lines , lines[cnt + 1]);//para1:暗くなるまで para2:転送データ para3:明るさ（温度）数値
					cnt = Number(lines[cnt + 2]);
					break;
				}
				else{				
					cnt = Number(lines[cnt + 2]);
				}
			}
			//暗くなるまで待つ
			else if (lines[cnt] == "423"){
				var data  = document.getElementById('cdSData').innerHTML;
				//実測値 > 設定値なら、それ以外はスルー
				if (Number(data) > Number(lines[cnt + 1])){
					document.getElementById('play_set').innerHTML = "明るさ待ち";
					command_wait("2" , lines , lines[cnt + 1]);//para1:暗くなるまで para2:転送データ para3:明るさ（温度）数値
					cnt = Number(lines[cnt + 2]);
					break;
				}
				else{				
					cnt = Number(lines[cnt + 2]);
				}
			}
			//度以上になるまで待つ
			else if (lines[cnt] == "424"){
				var data  = document.getElementById('ondoData').innerHTML;
				//実測値 < 設定値なら、それ以外はスルー
				if (Number(data) < Number(lines[cnt + 1])){
					document.getElementById('play_set').innerHTML = "温度待ち";
					command_wait("3" , lines , lines[cnt + 1]);//para1:暗くなるまで para2:転送データ para3:明るさ（温度）数値
					cnt = Number(lines[cnt + 2]);
					break;
				}
				else{				
					cnt = Number(lines[cnt + 2]);
				}
			}
			//度以下になるまで待つ
			else if (lines[cnt] == "425"){
				var data  = document.getElementById('ondoData').innerHTML;
				//実測値 < 設定値なら、それ以外はスルー
				if (Number(data) > Number(lines[cnt + 1])){
					document.getElementById('play_set').innerHTML = "温度待ち";
					command_wait("4" , lines , lines[cnt + 1]);//para1:暗くなるまで para2:転送データ para3:明るさ（温度）数値
					cnt = Number(lines[cnt + 2]);
					break;
				}
				else{				
					cnt = Number(lines[cnt + 2]);
				}
			}
			//信号入力があるまで待つ
			else if (lines[cnt] == "426"){
				var data  = document.getElementById('gaibuData').innerHTML;
				//OFFなら、それ以外はスルー
				if (data == "OFF"){
					document.getElementById('play_set').innerHTML = "信号入力待ち";
					command_wait("5" , lines , lines[cnt + 1]);//para1:信号入力 para2:転送データ para3:明るさ（温度）数値
					cnt = Number(lines[cnt + 1]);
					break;
				}
				else{				
					cnt = Number(lines[cnt + 1]);
				}
			}
			
			//繰り返し
			else if (lines[cnt] == "650"){
				repeat_kaisu = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
			}
			//繰り返し終了
			else if (lines[cnt] == "651"){
				cnt = Number(lines[cnt + 1]);
			}		
			//音
			else if (lines[cnt] == "670"){
				sno = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
				wav(sno);
			}
			//変数1
			else if (lines[cnt] == "680"){
				variable_St1 = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
			}
			//変数2
			else if (lines[cnt] == "681"){
				variable_St2 = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
			}
			//変数3
			else if (lines[cnt] == "682"){
				variable_St3 = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
			}
			//変数4
			else if (lines[cnt] == "683"){
				variable_St4 = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
			}
			//変数5
			else if (lines[cnt] == "684"){
				variable_St5 = lines[cnt + 1];
				cnt = Number(lines[cnt + 2]);
			}
			//if 時刻前なら
			else if (lines[cnt] == "690"){			
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
			else if (lines[cnt] == "691"){			
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
			else if (lines[cnt] == "692"){	
				if (juyo == "true"){ 
					cnt = Number(lines[cnt + 1]);
				}
				else{				
					cnt = Number(lines[cnt + 2]);
				}
			}
			//重要でないなら
			else if (lines[cnt] == "693"){	
				if (juyo == "true"){ 
					cnt = Number(lines[cnt + 2]);
				}
				else{				
					cnt = Number(lines[cnt + 1]);
				}
			}
			//TeachableMachineで指定したクラスが数値以上なら
			else if (lines[cnt] == "694"){	
				//クラスがなかったら
				if (doesLabelExist(lines[cnt + 1]) == null){
					set_Interval();
					break;
				}
				//%取得
				if (doesLabelExist(lines[cnt + 1]) >= lines[cnt + 2]){
					cnt = Number(lines[cnt + 3]);
				}
				else{				
					cnt = Number(lines[cnt + 4]);
				}
			}
			//TeachableMachineで指定したクラスが数値以下なら
			else if (lines[cnt] == "695"){	
				//クラスがなかったら
				if (doesLabelExist(lines[cnt + 1]) == null){
					set_Interval();
					break;
				}
				//%取得
				if (doesLabelExist(lines[cnt + 1]) <= lines[cnt + 2]){
					cnt = Number(lines[cnt + 3]);
				}
				else{				
					cnt = Number(lines[cnt + 4]);
				}
			}
			//明るければなら
			else if (lines[cnt] == "696"){	
				var data  = document.getElementById('cdSData').innerHTML;
				//lines[cnt + 1]:しきい値　data:実測値
				if (Number(lines[cnt + 1]) < Number(data)){
					cnt = Number(lines[cnt + 2]);
				}
				else{				
					cnt = Number(lines[cnt + 3]);
				}
			}
			//暗ければなら
			else if (lines[cnt] == "697"){	
				var data  = document.getElementById('cdSData').innerHTML;
				//lines[cnt + 1]:しきい値　data:実測値
				if (Number(lines[cnt + 1]) > Number(data)){
					cnt = Number(lines[cnt + 2]);
				}
				else{				
					cnt = Number(lines[cnt + 3]);
				}
			}
			//設定温度より高ければ
			else if (lines[cnt] == "698"){	
				var data  = document.getElementById('ondoData').innerHTML;
				//lines[cnt + 1]:しきい値　data:実測値
				if (Number(lines[cnt + 1]) < Number(data)){
					cnt = Number(lines[cnt + 2]);
				}
				else{				
					cnt = Number(lines[cnt + 3]);
				}
			}
			//設定温度より低ければ
			else if (lines[cnt] == "699"){	
				var data  = document.getElementById('ondoData').innerHTML;
				//lines[cnt + 1]:しきい値　data:実測値
				if (Number(lines[cnt + 1]) > Number(data)){
					cnt = Number(lines[cnt + 2]);
				}
				else{				
					cnt = Number(lines[cnt + 3]);
				}
			}
			//外部信号がONなら
			else if (lines[cnt] == "701"){	
				var data  = document.getElementById('gaibuData').innerHTML;
				if (data.indexOf('ON') != -1){
					cnt = Number(lines[cnt + 1]);
				}
				else{				
					cnt = Number(lines[cnt + 2]);
				}
			}
			//外部信号がOFFなら
			else if (lines[cnt] == "702"){	
				var data  = document.getElementById('gaibuData').innerHTML;
				if (data.indexOf('OFF') != -1){
					cnt = Number(lines[cnt + 1]);
				}
				else{				
					cnt = Number(lines[cnt + 2]);
				}
			}

			else{
				set_Interval();
				break;
			}
			
			if (cnt>=lines.length - 1 || lines[cnt] == "601"){
				set_Interval();
				break;
			}
		}
		//document.getElementById('mymessage').value ="";
		if (!document.getElementById('play_set').textContent.includes('待ち')) {
			if (document.getElementById('m_para_stampno').selectedIndex != 0){
				document.getElementById('m_para_stampno').selectedIndex = 0;
			}
			if (document.getElementById('img_file').files.length > 0){
				document.getElementById('img_file').value = ''; // 入力値を空に設定
				document.getElementById('uppicturearea').innerHTML = '写真は選択されていません';
			}
		}
	}catch(e){}
}
//クラス名があるかどうか
function doesLabelExist(className) {
	const containerIds = [
        "liveClass",//独自の画像認識
        "class_label",//TeachableMachjineの画像
        "class_label_pose",//TeachableMachjineのポーズ
        "class_label_preset",//TeachableMachjineのプリセット
        "class_label_face",//顔認証
		"fg_liveClass"//表情
    ];

	for (const id of containerIds) {
		const container = document.getElementById(id);
		if (!container) continue;

		const divs = container.querySelectorAll("div");
		for (const div of divs) {
			const text = div.textContent.trim();
			if (text.startsWith(className + ":")) {
				const match = text.match(/: *([\d.]+)%/);
				if (match) {
					return parseFloat(match[1]);
				}
			}
		}
	}

	return null; // 見つからなかった場合
}

var timerid = null;
let stopRequested = false;  // 停止要求フラグ
//信号待ちの場合に実行(mode 1:明るくなるまで停止　2:暗くなるまで停止 3:度になるまで lines:データ para:しきい値)
function command_wait(mode, lines, para){
	timerid = setInterval(function(){
		// 停止要求があればタイマーを止めて終了
		if (stopRequested){
			clearInterval(timerid);
			timerid = null;
			return;
		}
		//明るくなるまで停止
		if (mode =="1"){
			var data  = document.getElementById('cdSData').innerHTML;
			//para:しきい値　data:実測値
			if (Number(para) <= Number(data)){
				document.getElementById('play_set').innerHTML = "　実行　";
				clearInterval(timerid);
				timerid = null;
				stopRequested = false;  // フラグリセット
				play_sendProgram(lines , cnt);
			}
		}
		//暗くなるまで停止
		else if (mode == "2"){
			var data  = document.getElementById('cdSData').innerHTML;
			//para:しきい値　data:実測値
			if (Number(para) > Number(data)){
				document.getElementById('play_set').innerHTML = "　実行　";
				clearInterval(timerid);
				timerid = null;
				stopRequested = false;  // フラグリセット
				play_sendProgram(lines , cnt);
			}
		}
		//度になるまで
		else if (mode == "3"){
			var data  = document.getElementById('ondoData').innerHTML;
			//para:しきい値　data:実測値
			if (Number(para) <= Number(data)){
				document.getElementById('play_set').innerHTML = "　実行　";
				clearInterval(timerid);
				timerid = null;
				stopRequested = false;  // フラグリセット
				play_sendProgram(lines , cnt);
			}
		}
		//度になるまで
		else if (mode == "4"){
			var data  = document.getElementById('ondoData').innerHTML;
			//para:しきい値　data:実測値
			if (Number(para) > Number(data)){
				document.getElementById('play_set').innerHTML = "　実行　";
				clearInterval(timerid);
				timerid = null;
				stopRequested = false;  // フラグリセット
				play_sendProgram(lines , cnt);
			}
		}
		//信号入力があるまで
		else if (mode == "5"){
			var data  = document.getElementById('gaibuData').innerHTML;
			if (data.indexOf('ON') != -1){				
				document.getElementById('play_set').innerHTML = "　実行　";
				clearInterval(timerid);
				timerid = null;
				stopRequested = false;  // フラグリセット
				play_sendProgram(lines , cnt);
			}
		}
		//AIの処理が終わるまで（クライアント）
		else if (mode == "6"){
			if (ai_returnData != ""){				
				document.getElementById('play_set').innerHTML = "　実行　";
				clearInterval(timerid);
				timerid = null;
				stopRequested = false;  // フラグリセット
				play_sendProgram(lines , cnt);
			}
		}
		//AIの処理が終わるまで（サーバー）
		else if (mode == "7"){
			if (ai_returnData != ""){
				clearInterval(timerid);
				timerid = null;
				stopRequested = false;  // フラグリセット
				play_serverProgram(lines , s_cnt, para);
				if(server_up_message !=""){
					server_up_process(server_up_message);
				}
			}
		}
		else if (mode == "8"){
			var date1 = new Date();
			var ji = date1.getHours();
			var hun = date1.getMinutes();
			var time_data = para.split('-');//分解

			//para:しきい値　data:実測値
			if ((Number(time_data[0]) + 1) < Number(ji)){
				document.getElementById('play_set').innerHTML = "　実行　";
				clearInterval(timerid);
				timerid = null;
				stopRequested = false;  // フラグリセット
				play_sendProgram(lines , cnt);
			}
			else if ((Number(time_data[0]) + 1) == Number(ji)){
				if (Number(time_data[1]) <= Number(hun)){
					document.getElementById('play_set').innerHTML = "　実行　";
					clearInterval(timerid);
					timerid = null;
					stopRequested = false;  // フラグリセット
					play_sendProgram(lines , cnt);
				}
			}
		}
		//TeachableMachineが一致するまで
		else if (mode == "9"){
			var paraclass = strSplit(para, 0, ":");
			var paravalue = strSplit(para, 1, ":");
			//%取得
			if (doesLabelExist(paraclass)>= paravalue){
				document.getElementById('play_set').innerHTML = "　実行　";
				clearInterval(timerid);
				timerid = null;
				stopRequested = false;  // フラグリセット
				play_sendProgram(lines , cnt);
			}
		}
	}, 500);
}
//サーバからクライアントへ送信
function client_send_message(){	//自分の番号
	//送受信処理中なら何もしない
	if (bool_server_sending ||bool_server_recieving){return;}
    //送信フラグをたてる
	bool_server_sending = true;

	clientA = document.getElementById('myno').value;
	clientB = document.getElementById('client_sendno').value;
	juyo = document.getElementById('s_c_juyo').checked;
	messageSt = document.getElementById('client_sendst').value;
	var nowtime = get_time();	
	
	var sendSt = school_id + "," + chat_group_data + "," + nowtime + ",サーバ," + clientA + ",," + clientB + "," + 
						 "サーバ" + "," + juyo +  ",未読,1,,,,,,,," + messageSt + ",,";

	server_send_action(sendSt);

	document.getElementById('s_c_juyo').checked = false;
	document.getElementById('client_sendno').value = "";
	document.getElementById('client_sendst').value = "";
	document.getElementById("cli_send").style.display ="none";

    //送信フラグをおろす
	bool_server_sending = false;
}

// 停止ボタンから呼ぶ関数
function stopTeachablePrograms(){
	stopRequested = true;

	// setIntervalタイマーを停止
	if (timerid){
		clearInterval(timerid);
		timerid = null;
	}
	
	// 表示をクリア
	document.getElementById('play_set').innerHTML = "　実行　";
}