//共通リストからプログラムへ
function sharelist_to_program(text){
	var retxt = text.replace(/\r\n|\r/g, "\n");
	var textarray = retxt.split( '\n' );
	//共通データでなかったら終了
	if (textarray[1].indexOf('START') == -1){return;}
	var lines = new Array();
	for ( var i = 1; i < textarray.length; i++ ) {
		if (textarray[i] != ""){
			lines.push(textarray[i]);
		}
		if (textarray[i].indexOf('END') != -1){
			break;
		}
	}
	var all_list = new Array();
	all_list = get_if_level_pro(lines);	
	
	var loadtxt = all_list.join('\n');
	myDiagram.model = go.Model.fromJson(loadtxt);
}
//共通データからプログラムを取得
function get_if_level_pro(sharelist){
	var if_level = 0;
	var if_list = new Array();		
	//座標を取得するための基準となる配列に格納
	for ( var i = 0; i < sharelist.length; i++ ) {
		var dataArray = sharelist[i].split(',');
		//条件分岐なら
		if (dataArray[0] == "78" || dataArray[0] == "88"){
			if_level ++;
		}
		else if (dataArray[0] == "79" || dataArray[0] == "89"){
			if_level --;
		}
		if_list.push(if_level.toString());
	}
	//x座標を配列に格納
	var zahyo_x_list = new Array();
	var lebelno = 0;
	var x_zahyo = 100;
	for ( var i = 0; i < if_list.length; i++ ) {		
		//ifレベルが1つ上がったら(else)
		if (lebelno + 1 == Number(if_list[i])){
			lebelno++;
			x_zahyo += 150;
			zahyo_x_list.push("");
		}
		//ifレベルが1つ下がったら(endif)
		else if (lebelno - 1 == Number(if_list[i])){
			lebelno --;
			x_zahyo -= 150;
			zahyo_x_list.push("");				 
		}
		else{
			zahyo_x_list.push(x_zahyo.toString());
		}
	}
	
	//y座標を配列に格納
	var zahyo_y_list = new Array(sharelist.length);
	zahyo_y_list.fill("");
	var y_zahyo = 50;	
	var lebelcnt = 0;
	var if_pool = new Array();
	while(true){
		for ( var i = 0; i < if_list.length; i++ ) {
			var dataArray = sharelist[i].split(',');
			//ifレベルが一致していたら
			if (lebelcnt.toString() == if_list[i]){
				if (dataArray[0] != "78" && dataArray[0] != "79" && dataArray[0] != "88" && dataArray[0] != "89"){
					zahyo_y_list[i] = y_zahyo;
					y_zahyo += 100;
				}
				if (dataArray[0] == "54" || dataArray[0] == "69" || dataArray[0] == "99" || dataArray[0] == "74" || dataArray[0] == "84"){
					if_pool.push(i.toString());
				}
			}
		}
		if (if_pool.length > 0){
			lebelcnt++;
			y_zahyo = zahyo_y_list[Number(if_pool[if_pool.length - 1])] + 100;
			if_pool.pop();
		}
		else {
			break;
		}
		
	}
	
	if_list.length = 0;
	if_level =0;
	//if_levelだけを配列に格納
	for ( var i = 0; i < sharelist.length; i++ ) {
		var dataArray = sharelist[i].split(',');
		//条件分岐なら
		if (dataArray[0] == "54" || dataArray[0] == "69" || dataArray[0] == "99" || 
			dataArray[0] == "74" || dataArray[0] == "84" || dataArray[0] == "78" || dataArray[0] == "88"){
			if_level ++;
		}
		else if (dataArray[0] == "79" || dataArray[0] == "89"){
			if_level -= 2;
		}
		if_list.push(if_level.toString());
	}
	
	var node_data_array = new Array();//category、fill、stroke等
	var node_data_base = "{\"category\":\"c_cate\", \"fill\":\"c_fill\", \"stroke\":\"c_stroke\", \"text\":\"c_text\", \"parameter\":\"c_para\", \"key\":c_key, \"loc\":\"c_x c_y\"}";
	var start_key = 3;
	var key_list = new Array();
	//コマンド情報をJSON式に変換　共通リストを順番に処理
	for ( var i = 0; i < sharelist.length; i++ ) {
		var dataArray = sharelist[i].split(',');//共通リストを分解
		var comm_cate = "";		var comm_fill = "";		var comm_stroke = "";		var comm_text = "";
		var comm_para = "";		var comm_key = "";		var comm_x = "";		var comm_y = "";
		var after_node_data = node_data_base;
		//START
		if (dataArray[0] == "START"){
			comm_cate = "Start";
			comm_fill = "#FFFFFF";
			comm_text = "開始";
			comm_para = "0-0-0-0-0-0";
			comm_key = "-1";
		}
		//END
		else if (dataArray[0] == "END"){			
			comm_cate = "End";
			comm_fill = "#FFFFFF";
			comm_text = "終了";
			comm_para = "0-0-0-0-0-0";
			comm_key = "-2";
		}
		//else endif
		else if (dataArray[0] == "78" || dataArray[0] == "79" || dataArray[0] == "88" || dataArray[0] == "89"){	
			if (dataArray[0] == "78" || dataArray[0] == "88"){comm_key = "else";}
			else{comm_key = "endif";}			
			key_list.push(comm_key); 
			continue;
		}
		else{
			//メッセージ（サーバ）
			if (dataArray[0] == "52"){
				if (dataArray[1] == "0"){
					comm_cate = "server_Message";
					comm_fill = "#afd9f0";
					comm_text = "メッセージを送る";
					comm_para = "0-0-0-0-0-0";
				}
				else{
					comm_cate = "server_Error";
					comm_fill = "#fadcfa";
					comm_text = "\"" + dataArray[2] + "\"を返信";
					comm_para = "0-" + dataArray[2] + "-0-0-0-0";
				}
			}	
			//パスワード（サーバ）
			else if (dataArray[0] == "53"){
				comm_cate = "server_Setup";
				comm_fill = "#e3e3e3";
				comm_text = "ﾊﾟｽﾜｰﾄﾞ" + dataArray[2];
				comm_para = "0-" + dataArray[2] + "-0-0-0-0";
			}
			//条件分岐（サーバ）
			else if (dataArray[0] == "54"){
				comm_cate = "server_Conditional";
				comm_fill = "#ffd6b0";
				if (dataArray[1] == "0"){
					comm_text = "禁止ワードがある?";
					comm_para = "0-0-0-0-0-0";
				}
				else if (dataArray[1] == "1"){
					comm_text = "禁止ワードがない?";
					comm_para = "1-0-0-0-0-0";
				}
				else if (dataArray[1] == "2"){
					comm_text = "登録ワードがある?";
					comm_para = "2-0-0-0-0-0";				
				}
				else if (dataArray[1] == "3"){
					comm_text = "登録ワードがない?";
					comm_para = "3-0-0-0-0-0";	
				}
				else if (dataArray[1] == "6"){
					comm_text = "パスワードが一致?";
					comm_para = "4-0-0-0-0-0";
				}
				else if (dataArray[1] == "7"){
					comm_text = "パスワードが不一致?";
					comm_para = "5-0-0-0-0-0";				
				}
				else if (dataArray[1] == "10"){
					comm_text = "繰返回数が設定値以上?";
					comm_para = "6-" + dataArray[2] + "-0-0-0-0";				
				}
				else if (dataArray[1] == "11"){
					comm_text = "繰返回数が設定値以下?";
					comm_para = "7-" + dataArray[2] + "-0-0-0-0";				
				}
				else if (dataArray[1] == "12"){
					comm_text = "重要なら";
					comm_para = "8-0-0-0-0-0";
				}
				else if (dataArray[1] == "13"){
					comm_text = "重要でないなら";
					comm_para = "9-0-0-0-0-0";
				}
				else if (dataArray[1] == "14"){
					comm_text = "特定の生徒番号?";
					comm_para = "10-" + dataArray[2] + "-0-0-0-0";		
				}
				else if (dataArray[1] == "15"){
					comm_text = "特定の生徒番号でない?";
					comm_para = "11-" + dataArray[2] + "-0-0-0-0";		
				}
				else if (dataArray[1] == "18"){
					comm_text = "文字数が設定値以上?";
					comm_para = "12-" + dataArray[2] + "-0-0-0-0";		
				}
				else if (dataArray[1] == "19"){
					comm_text = "文字数が設定値以下?";
					comm_para = "13-" + dataArray[2] + "-0-0-0-0";		
				}
				else if (dataArray[1] == "20"){
					comm_text = "送信者の名前がある?";
					comm_para = "14-" + dataArray[2] + "-0-0-0-0";		
				}
				else if (dataArray[1] == "21"){
					comm_text = "送信者の名前がない?";
					comm_para = "15-" + dataArray[2] + "-0-0-0-0";		
				}
				else {
					comm_text = "";
					comm_para = "";
				}
			}
						
			//メッセージ（クライアント）
			else if (dataArray[0] == "62"){
				if (dataArray[1] == "0"){
					comm_cate = "Message";
					comm_fill = "#afd9f0";
					comm_text = "\\\"メッセージ\\\"を0に送信";
					comm_para = "0-0-メッセージ-0-0-0";
				}
				//音連動はないのでスルー
				else{
					continue;
				}
			}
			//メッセージ（クライアント）
			else if (dataArray[0] == "63"){
				if (dataArray[1] == "0"){
					comm_cate = "Message";
					comm_fill = "#afd9f0";
					comm_text = "\\\"" + dataArray[3] + "\\\"を" + dataArray[2] + "に送信";
					comm_para = "0-" + dataArray[2] + "-" + dataArray[3] + "-0-0-0";
				}
				//音連動、グループはないのでスルー
				else{
					continue;
				}
			}
			//機能（クライアント）
			else if (dataArray[0] == "64"){
				if (dataArray[1] == "0"){
					comm_cate = "Setup";
					comm_fill = "#e3e3e3";
					comm_text = "ﾊﾟｽﾜｰﾄﾞ" + dataArray[2];
					comm_para = "0-" + dataArray[2] + "-0-0-0-0";
				}
				else if (dataArray[1] == "1"){
					comm_cate = "Setup";
					comm_fill = "#e3e3e3";
					comm_text = "重要!!";
					comm_para = "2-0-0-0-0-0";
				}
				//音連動、グループはないのでスルー
				else{
					continue;
				}
			}	
			//繰り返し（クライアント）
			else if (dataArray[0] == "65"){
				comm_fill = "#fffebf";
				if (dataArray[1] == "0"){
					comm_cate = "Repeat1";
					comm_text = "繰り返し" + dataArray[2] + "回";
					comm_para = "0-" + dataArray[2] + "-0-0-0-0";		
				}
				else {
					comm_cate = "Repeat2";
					comm_text = "繰り返し終了";
					comm_para = "1-0-0-0-0-0";
				}
			}	
			//フォント（クライアント）
			else if (dataArray[0] == "66"){
				comm_cate = "Font";
				comm_fill = "#fffebf";
				if (dataArray[1] == "0"){
					comm_text = "ﾌｫﾝﾄｻｲｽﾞ " + dataArray[2];
					comm_para = "0-" + dataArray[2] + "-0-0-0-0";		
				}
				else if (dataArray[1] == "1"){
					comm_text = "文字の色";
					comm_para = "1-" + dataArray[2] + "-0-0-0-0";		
				}
				else if (dataArray[1] == "2"){
					comm_text = "メッセージ背景色";
					comm_para = "2-" + dataArray[2] + "-0-0-0-0";
				}
				else {
					comm_text = "画面背景色";
					comm_para = "3-" + dataArray[2] + "-0-0-0-0";
				}
			}
			//信号待ち（クライアント）
			else if (dataArray[0] == "67"){
				comm_cate = "Sensor";
				comm_fill = "#e3ffe5";
				if (dataArray[1] == "1"){
					comm_text = "明るさ " + dataArray[2] +  "待ち";
					comm_para = "0-" + dataArray[2] + "-0-0-0-0";		
				}
				else if (dataArray[1] == "2"){
					comm_text = "暗さ " + dataArray[2] +  "待ち";
					comm_para = "1-" + dataArray[2] + "-0-0-0-0";		
				}
				else if (dataArray[1] == "2"){
					comm_text = dataArray[2] + "度以上待ち";
					comm_para = "2-" + dataArray[2] + "-0-0-0-0";
				}
				else if (dataArray[1] == "3"){
					comm_text = dataArray[2] + "度以下待ち";
					comm_para = "3-" + dataArray[2] + "-0-0-0-0";
				}
				else if (dataArray[1] == "4"){
					comm_text = "信号待ち";
					comm_para = "4-0-0-0-0-0";
				}
				else{
					
				}
			}			
			//条件分岐（クライアント）
			else if (dataArray[0] == "69"){
				comm_cate = "Conditional";
				comm_fill = "#ffd6b0";
				if (dataArray[1] == "0"){
					comm_text = dataArray[2] + "時より前?";
					comm_para = "0-" + dataArray[2] + "-0-0-0-0";	
				}
				else if (dataArray[1] == "1"){
					comm_text = dataArray[2] + "時より後?";
					comm_para = "1-" + dataArray[2] + "-0-0-0-0";	
				}
				else if (dataArray[1] == "2"){
					comm_text = "重要?";
					comm_para = "2-0-0-0-0-0";				
				}
				else if (dataArray[1] == "3"){
					comm_text = "重要でない?";
					comm_para = "3-0-0-0-0-0";	
				}
				else if (dataArray[1] == "6"){
					comm_text = "明るさ > " + dataArray[2] + "?";
					comm_para = "4-" + dataArray[2] + "-0-0-0-0";	
				}
				else if (dataArray[1] == "7"){
					comm_text = "明るさ < " + dataArray[2] + "?";
					comm_para = "5-" + dataArray[2] + "-0-0-0-0";	
				}
				else if (dataArray[1] == "8"){
					comm_text = "温度 > " + dataArray[2] + "?";
					comm_para = "6-" + dataArray[2] + "-0-0-0-0";	
				}
				else if (dataArray[1] == "9"){
					comm_text = "温度 < " + dataArray[2] + "?";
					comm_para = "7-" + dataArray[2] + "-0-0-0-0";	
				}
				else if (dataArray[1] == "11"){
					comm_text = "外部信号ON?";
					comm_para = "8-0-0-0-0-0";
				}
				else if (dataArray[1] == "12"){
					comm_text = "外部信号OFF?";
					comm_para = "9-0-0-0-0-0";
				}
				else {
					comm_text = "";
					comm_para = "";
				}
			}
			else{
				
			}			
			
			comm_key = "-" + start_key.toString();
			start_key++;
		}
		key_list.push(comm_key);
		comm_stroke = "#000000";
		comm_x = zahyo_x_list[i];
		comm_y = zahyo_y_list[i];
		
		after_node_data = after_node_data.replace("c_cate", comm_cate);
		after_node_data = after_node_data.replace("c_fill", comm_fill);
		after_node_data = after_node_data.replace("c_stroke", comm_stroke);
		after_node_data = after_node_data.replace("c_text", comm_text);
		after_node_data = after_node_data.replace("c_para", comm_para);
		after_node_data = after_node_data.replace("c_key", comm_key);
		after_node_data = after_node_data.replace("c_x", comm_x);
		after_node_data = after_node_data.replace("c_y", comm_y);
		
		node_data_array.push(after_node_data);
	}
		
	var link_data_array = new Array();//from、to等	
	var link_from = "";		var link_to = "";		var link_fromPort = "";		var link_toPort = "";
	
	var if_arrow_list = new Array();
	var arrow_start_x = 0;//矢印開始命令のx座標
	var arrow_start_y = 0;//矢印開始命令のy座標
	var arrow_end_x = 0;//矢印終了命令のx座標
	var arrow_end_y = 0;//矢終了始命令のy座標
	var iflebel =0;//0:本筋　1以上:else
	var arrow_text = "";//矢印に描画するテキスト
	var linecnt = 0;//検索番号
	var base_x_zahyou = 0;//条件分岐など基準となるx座標
	//矢印情報をJSON式に変換　共通リストを順番に処理

	//本筋の矢印
	for ( var i = 0; i < sharelist.length; i++ ) {
		var dataArray = sharelist[i].split(',');//共通リストを分解
		//START
		if (dataArray[0] == "START"){
			link_from = key_list[i];//矢印の始まり番号
			link_fromPort = "下";//矢印の始まり位置
			arrow_start_x = Number(zahyo_x_list[i]);
			arrow_start_y = Number(zahyo_y_list[i]);
			//iflebelはx座標
			base_x_zahyou = arrow_start_x;
		}
		//END
		else if (dataArray[0] == "END"){
			link_to = key_list[i];//矢印の終わり番号
			link_toPort = "上";//矢印の終わり位置			
			arrow_end_x = Number(zahyo_x_list[i]);
			arrow_end_y = Number(zahyo_y_list[i]);

			link_data_array.push(replace_arrowdata(link_from, link_to, link_fromPort, link_toPort, arrow_start_x, arrow_start_y, arrow_end_x, arrow_end_y, arrow_text, dataArray[0]));
			break;
		}
		else if (dataArray[0] == "78" || dataArray[0] == "88"){

		}
		else if (dataArray[0] == "79" || dataArray[0] == "89"){
			
		}
		//else endif以外
		else {				
			if (base_x_zahyou == Number(zahyo_x_list[i])){
				//条件分岐ならリストに追加
				if (dataArray[0] == "54" || dataArray[0] == "69" || dataArray[0] == "99" || 
					dataArray[0] == "74" || dataArray[0] == "84"){
					if_arrow_list.push(i.toString());				
				}
				link_to = key_list[i];//矢印の終わり番号
				link_toPort = "上";//矢印の終わり位置			
				arrow_end_x = Number(zahyo_x_list[i]);
				arrow_end_y = Number(zahyo_y_list[i]);

				link_data_array.push(replace_arrowdata(link_from, link_to, link_fromPort, link_toPort, arrow_start_x, arrow_start_y, arrow_end_x, arrow_end_y, arrow_text, dataArray[0]));

				link_from = key_list[i];//矢印の始まり番号
				link_fromPort = "下";//矢印の始まり位置
				arrow_start_x = Number(zahyo_x_list[i]);
				arrow_start_y = Number(zahyo_y_list[i]);
				//矢印に描画する文字
				if (dataArray[0] == "54" || dataArray[0] == "69" || dataArray[0] == "99" || 
					dataArray[0] == "74" || dataArray[0] == "84"){
					arrow_text = "YES";
				}
				else{
					arrow_text = "";
				}
			}
		}
	}	
	
	//else部
	while(true){
		//ifがあれば
		if (if_arrow_list.length > 0){
			//ifリストにある最後の番号
			linecnt = Number(if_arrow_list[if_arrow_list.length - 1]);
			iflebel = if_list[linecnt];			
			
			link_from = key_list[linecnt];//矢印の始まり番号
			link_fromPort = "右";//矢印の始まり位置
			arrow_text = "NO";
			arrow_start_x = Number(zahyo_x_list[linecnt]);
			arrow_start_y = Number(zahyo_y_list[linecnt]);			
			if_arrow_list.pop();
			linecnt++;
		}
		else{
			break;
		}
		
		var bool_add = false;
		var else_start_x = arrow_start_x;//elseに入る時のifのx座標
		while(true){
			if (linecnt > sharelist.length){break;}
			var dataArray = sharelist[linecnt].split(',');//共通リストを分解
			if (dataArray[0] == "78" || dataArray[0] == "88"){
				//elseに入った時のiflebel+1ならその条件分岐のelse部
				if (Number(if_list[linecnt]) == Number(iflebel) + 1){
					bool_add = true;
					//base_x_zahyouはelseの次のx座標
					base_x_zahyou = else_start_x + 150;
				}
			}
			else if (dataArray[0] == "79" || dataArray[0] == "89"){
				//ifレベルがif -1なら
				if (Number(if_list[linecnt]) == Number(iflebel) - 1){
					bool_add = false;
					base_x_zahyou -= 150;
					//endifの直後にendifならさらにx座標をマイナス
					for ( var i = linecnt + 1; i < sharelist.length; i++ ) {
						var nextarray = sharelist[i].split(',');//共通リストを分解
						if (nextarray[0] == "79" || nextarray[0] == "89"){
							base_x_zahyou -= 150;							
						}
						else{
							break;
						}
					}
				}
			}
			else{
				if (base_x_zahyou == Number(zahyo_x_list[linecnt])){
					if ((dataArray[0] == "54" || dataArray[0] == "69" || dataArray[0] == "99" || 
						 dataArray[0] == "74" || dataArray[0] == "84") && else_start_x != Number(zahyo_x_list[linecnt])){
						if_arrow_list.push(linecnt.toString());
					}
					//elseと同じifレベルなら
					if (bool_add){
							link_to = key_list[linecnt];//矢印の終わり番号
							link_toPort = "上";//矢印の終わり位置			
							arrow_end_x = Number(zahyo_x_list[linecnt]);
							arrow_end_y = Number(zahyo_y_list[linecnt]);

							link_data_array.push(replace_arrowdata(link_from, link_to, link_fromPort, link_toPort, arrow_start_x, arrow_start_y, arrow_end_x, arrow_end_y, arrow_text, dataArray[0]));

							link_from = key_list[linecnt];//矢印の始まり番号
							link_fromPort = "下";//矢印の始まり位置
							arrow_start_x = Number(zahyo_x_list[linecnt]);
							arrow_start_y = Number(zahyo_y_list[linecnt]);
							//矢印に描画する文字
							if (dataArray[0] == "54" || dataArray[0] == "69" || dataArray[0] == "99" || 
								dataArray[0] == "74" || dataArray[0] == "84"){
								arrow_text = "YES";
							}
							else{
								arrow_text = "";
							}
					}
					else{
						if (iflebel > if_list[linecnt]){
							link_to = key_list[linecnt];//矢印の終わり番号
							link_toPort = "上";//矢印の終わり位置			
							arrow_end_x = Number(zahyo_x_list[linecnt]);
							arrow_end_y = Number(zahyo_y_list[linecnt]);

							link_data_array.push(replace_arrowdata(link_from, link_to, link_fromPort, link_toPort, arrow_start_x, arrow_start_y, arrow_end_x, arrow_end_y, arrow_text, dataArray[0]));

							link_from = key_list[linecnt];//矢印の始まり番号
							link_fromPort = "下";//矢印の始まり位置
							arrow_start_x = Number(zahyo_x_list[linecnt]);
							arrow_start_y = Number(zahyo_y_list[linecnt]);
							//矢印に描画する文字
							arrow_text = "";
							break;
						}
					}
				}				
				
			}
			linecnt++;
		}
	}
	
	var all_data = new Array();
	//all_data.push("\{ \"class\": \"go.GraphLinksModel\",  \"linkFromPortIdProperty\": \"fromPort\",  \"linkToPortIdProperty\": \"toPort\",  \"nodeDataArray\": [ ");
	all_data.push("{ \"class\": \"go.GraphLinksModel\",");
	all_data.push("  \"linkFromPortIdProperty\": \"fromPort\",");
	all_data.push("  \"linkToPortIdProperty\": \"toPort\",");
	all_data.push("  \"nodeDataArray\": [ ");
	
	for ( var i = 0; i < node_data_array.length; i++ ) {
		if (i == node_data_array.length - 1){
			all_data.push(node_data_array[i]);
		}
		else{
			all_data.push(node_data_array[i] + ",");
		}
	}
	all_data.push(" ],");
	all_data.push(" \"linkDataArray\": [ ");	
	for ( var i = 0; i < link_data_array.length; i++ ) {
		if (i == link_data_array.length - 1){
			all_data.push(link_data_array[i]);
		}
		else{
			all_data.push(link_data_array[i] + ",");
		}
	}
	all_data.push(" ]\}");
	
	return all_data;
}
//replace_arrowdata(link_from, link_to, link_fromPort, link_toPort, arrow_start_x, arrow_start_y, arrow_end_x, arrow_end_y)
function replace_arrowdata(from, to, fromport, toport, startx, starty, endx, endy, text, comcategory){
	//comcategoryが8なら行き先の命令は条件分岐。textに文字(YES,NO)があれば出先の命令条件分岐
	var after_link_data = "";
	//textがない場合
	if (text == ""){
		after_link_data = "{\"from\":before_no, \"to\":next_no, \"fromPort\":\"arrow_from\", \"toPort\":\"arrow_to\", \"points\":[arrow_x1,arrow_y1,arrow_x2,arrow_y2,arrow_x3,arrow_y3,arrow_x4,arrow_y4,arrow_x5,arrow_y5,arrow_x6,arrow_y6]}";
	}
	//textに"YES""NO"がある場合
	else {
		after_link_data = "{\"from\":before_no, \"to\":next_no, \"fromPort\":\"arrow_from\", \"toPort\":\"arrow_to\", \"visible\":true, \"text\":\""+ text +"\", \"points\":[arrow_x1,arrow_y1,arrow_x2,arrow_y2,arrow_x3,arrow_y3,arrow_x4,arrow_y4,arrow_x5,arrow_y5,arrow_x6,arrow_y6]}";
	}
	
	var link_x1 = "";		var link_y1 = "";		var link_x2 = "";		var link_y2 = "";
	var link_x3 = "";		var link_y3 = "";		var link_x4 = "";		var link_y4 = "";
	var link_x5 = "";		var link_y5 = "";		var link_x6 = "";		var link_y6 = "";
	//矢印の出発座標link_x1,link_y1 link_x2,link_y2
	if (text == "YES"){
		link_x1 = startx;
		link_y1 = starty + 35.75;		
		link_x2 = link_x1;
		link_y2 = link_y1 + 10;
	}
	else if (text == "NO"){
		link_x1 = startx + 75.75;
		link_y1 = starty;	
		link_x2 = link_x1 + 10;
		link_y2 = link_y1;
	}
	else{
		link_x1 = startx;
		link_y1 = starty + 25.75;
		link_x2 = link_x1;
		link_y2 = link_y1 + 10;
	}	
	//矢印の矢印の出発座標link_x5,link_y5 link_x6,link_y6 行き先が条件分岐なら
	if (comcategory == "8"){
		link_x5 = endx;
		link_y5 = endy - 45.75;
		link_x6 = endx;
		link_y6 = link_y5 + 10;
	}
	else{
		link_x5 = endx;
		link_y5 = endy - 35.75;
		link_x6 = endx;
		link_y6 = link_y5 + 10;
	}
	//矢印の中間位置 link_x3,link_y3 link_x4,link_y4
	//真下へ矢印
	if (startx == endx){
		//elseの直後がendなら
		if (text == "NO") {
			link_x3 = link_x2;
			link_y3 = starty + ((endy - starty) / 2);
			link_x4 = link_x5;
			link_y4 = link_y3;
		}
		else{
			link_x3 = link_x2;
			link_y3 = link_y2;
			link_x4 = link_x5;
			link_y4 = link_y5;
		}
	}
	//左上から右下へ矢印（ifのelse）
	else if (startx < endx){
		link_x3 = link_x5;
		link_y3 = link_y1;
		link_x4 = link_x5;
		link_y4 = link_y3 + ((link_y5 - link_y3) / 2);
	}
	else{
		//右下から左上へ矢印
		if (starty >= endy){
			link_x3 = endx + ((startx - endx) / 2);
			link_y3 = link_y2;
			link_x4 = link_x3;
			link_y4 = link_y5;	
		}
		//右上から左下へ矢印
		else{
			link_x3 = link_x2;
			link_y3 = starty + ((endy - starty) / 2);
			link_x4 = link_x5;
			link_y4 = link_y3;	
		}		
	}
	
	after_link_data = after_link_data.replace("before_no", from);
	after_link_data = after_link_data.replace("next_no", to);
	after_link_data = after_link_data.replace("arrow_from", fromport);
	after_link_data = after_link_data.replace("arrow_to", toport);
	after_link_data = after_link_data.replace("arrow_x1", link_x1);
	after_link_data = after_link_data.replace("arrow_y1", link_y1);
	after_link_data = after_link_data.replace("arrow_x2", link_x2);
	after_link_data = after_link_data.replace("arrow_y2", link_y2);
	after_link_data = after_link_data.replace("arrow_x3", link_x3);
	after_link_data = after_link_data.replace("arrow_y3", link_y3);
	after_link_data = after_link_data.replace("arrow_x4", link_x4);
	after_link_data = after_link_data.replace("arrow_y4", link_y4);
	after_link_data = after_link_data.replace("arrow_x5", link_x5);
	after_link_data = after_link_data.replace("arrow_y5", link_y5);
	after_link_data = after_link_data.replace("arrow_x6", link_x6);
	after_link_data = after_link_data.replace("arrow_y6", link_y6);
	return after_link_data;
}
//16進2桁を返す
function cov16(n){
sin = "0123456789ABCDEF";
if(n>255)return 'FF';
if(n<0) return '00';
return sin.charAt(Math.floor(n/16))+sin.charAt(n%16);//16進数2桁を返す
}

///////////////////////////////////////////////////////////////////////////