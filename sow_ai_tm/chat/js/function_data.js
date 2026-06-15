//プログラム出力
function outputProData() {
	Blockly.JavaScript.INFINITE_LOOP_TRAP = null;
	let code = Blockly.JavaScript.workspaceToCode(workspace);
	//プログラムスタートからの命令だけにする
	var pro_text  = code.replace(/\r\n|\r/g, "\n");
    var lines = pro_text.split( '\n' );
	var text = "";
	var bool_start = false;
	for ( var i = 0; i < lines.length; i++ ) {
		if (lines[i] == "start" || lines[i] == "start_r" || lines[i] == "server_start"){
			bool_start = true;
			if (text != ""){text += "\n";}
			text += lines[i];
			continue;
		}
		if (bool_start){
			if (lines[i] == ""){
				bool_start = false;
				text += "\n";
				continue;
			}
			if (text != ""){text += "\n";}
			text += lines[i];
		}
	}	
	document.form2.textarea1.value = text;
}
//転送データ出力
function changeSendData() {
    document.form3.textarea2.value=getSenddata();    
}
//転送データに変換
function getSenddata(){
    //改行コードを\nに統一
    var text  = document.getElementById('proText').value.replace(/\r\n|\r/g, "\n");
    var lines = text.split( '\n' );
	var if_list = new Array();//ifレベルを保持
    var com_head_address = new Array();//各コマンドのアドレスを保持
    var outArray = new Array();
	var in_pro = false;//start命令でtrue 空白行でfalse
		
    var LevelCount = 0;
	//ifレベルだけを保持
    for ( var i = 0; i < lines.length; i++ ) {
		if (lines[i].indexOf('doIf') != -1){
			LevelCount++;
		}
		else if (lines[i].indexOf('else') != -1){
            LevelCount++; 
		}
		else if (lines[i].indexOf('endif') != -1){
			LevelCount -= 2;
		}
		if_list.push(LevelCount.toString());
	}
	
	//各コマンドの開始アドレスを保持
	com_head_address = get_head_address(lines);

    for ( var i = 0; i < lines.length; i++ ) {
        lines[i] = lines[i].trim();	
        // 空行があれば終了
        if (in_pro == true){
		  if ( lines[i] == '' || i == lines.length - 1) {
              outArray.push( "601\n");  
              break;
		  }
        }
		//start命令でフラグをたて分析開始
		if (in_pro == false){
			if (lines[i] == "start" || lines[i] == "server_start"){
				in_pro = true;				
			}
			else{
				continue;
			}
		}
        if (lines[i] == "start" || lines[i] == "server_start"){
            outArray.push( "600\n");          
        }
        else if (lines[i] == "server_start"){
            outArray.push( "600\n");          
        }
        else if (lines[i].indexOf('message_send') != -1){
            var parano = strSplit(lines[i], 1, " ");
			var parast = strSplit(lines[i], 2, " ");
            outArray.push( "620\n");
            outArray.push( parano + "\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('stamp_send') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            var bango = document.getElementById('m_para_stampno').selectedIndex; 
            outArray.push( "622\n");
            outArray.push( parano + "\n");
            outArray.push( bango + "\n");
        }
        else if (lines[i].indexOf('picturem_send') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "623\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('program_send') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            var fileno = strSplit(lines[i], 2, " "); 
            outArray.push( "450\n");
            outArray.push( parano + "\n");
            outArray.push( fileno + "\n");
		}
        else if (lines[i].indexOf('message_group_send') != -1){
            var parano = strSplit(lines[i], 1, " "); //グループ番号
			var parast = document.getElementById('mymessage').value;
            outArray.push( "626\n");
			//処理用配列
			var sendnoarraay = new Array();
			//グループ１なら
			if (parano == 1){
				sendnoarraay = groupArray1;
			}
			//グループ２なら
			else if  (parano == 2){
				sendnoarraay = groupArray2;
			}
			//グループ３なら
			else{
				sendnoarraay = groupArray3;
			}
			groupname = "グループ" + String(parano);
			var allst = "";
			for ( var j = 0; j < sendnoarraay.length; j++ ) {
				if (j != 0){allst += "/";}
				allst += sendnoarraay[j];
			}
            outArray.push( allst + "\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('stamp_group_send') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            var bango = document.getElementById('m_para_stampno').selectedIndex; 
            outArray.push( "627\n");
			//処理用配列
			var sendnoarraay = new Array();
			//グループ１なら
			if (parano == 1){
				sendnoarraay = groupArray1;
			}
			//グループ２なら
			else if  (parano == 2){
				sendnoarraay = groupArray2;
			}
			//グループ３なら
			else{
				sendnoarraay = groupArray3;
			}
			groupname = "グループ" + String(parano);
			var allst = "";
			for ( var j = 0; j < sendnoarraay.length; j++ ) {
				if (j != 0){allst += "/";}
				allst += sendnoarraay[j];
			}
            outArray.push( allst + "\n");
            outArray.push( bango + "\n");
        }
        else if (lines[i].indexOf('picture_group_send') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "628\n");
			//処理用配列
			var sendnoarraay = new Array();
			//グループ１なら
			if (parano == 1){
				sendnoarraay = groupArray1;
			}
			//グループ２なら
			else if  (parano == 2){
				sendnoarraay = groupArray2;
			}
			//グループ３なら
			else{
				sendnoarraay = groupArray3;
			}
			groupname = "グループ" + String(parano);
			var allst = "";
			for ( var j = 0; j < sendnoarraay.length; j++ ) {
				if (j != 0){allst += "/";}
				allst += sendnoarraay[j];
			}
            outArray.push( allst + "\n");
        }
        else if (lines[i].indexOf('program_group_send') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            var fileno = strSplit(lines[i], 2, " "); 
            outArray.push( "451\n");
			//処理用配列
			var sendnoarraay = new Array();
			//グループ１なら
			if (parano == 1){
				sendnoarraay = groupArray1;
			}
			//グループ２なら
			else if  (parano == 2){
				sendnoarraay = groupArray2;
			}
			//グループ３なら
			else{
				sendnoarraay = groupArray3;
			}
			groupname = "グループ" + String(parano);
			var allst = "";
			for ( var j = 0; j < sendnoarraay.length; j++ ) {
				if (j != 0){allst += "/";}
				allst += sendnoarraay[j];
			}
            outArray.push( allst + "\n");
            outArray.push( fileno + "\n");
		}
        else if (lines[i].indexOf('ai_send') != -1){
            var parano = strSplit(lines[i], 1, " ");
			var parast = document.getElementById('mymessage').value;
            outArray.push( "630\n");
            outArray.push( parano + "\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('ai_change') != -1){
            var maxlength = strSplit(lines[i], 1, " ");
			var parast = document.getElementById('mymessage').value;
            outArray.push( "631\n");
            outArray.push( maxlength + "\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('ai_para_change') != -1){
            var param = strSplit(lines[i], 1, " ");
			var parast = document.getElementById('mymessage').value;
            outArray.push( "632\n");
            outArray.push( param + "\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('ai_prompt_change') != -1){
			var parast = document.getElementById('mymessage').value;
            outArray.push( "633\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('set_pass') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "640\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('set_server') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "641\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('set_impo') != -1){
            outArray.push( "642\n");
        }
        else if (lines[i].indexOf('disp_confirm') != -1){
            outArray.push( "643\n");
        }
        else if (lines[i].indexOf('play_sound') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "670\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('client_variable_1') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "680\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('client_variable_2') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "681\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('client_variable_3') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "682\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('client_variable_4') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "683\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('client_variable_5') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "684\n");
            outArray.push( parano + "\n");
        }

        else if (lines[i].indexOf('font_size') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "660\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('font_color') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "661\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('font_backcolor') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "662\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('disp_backcolor') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "663\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('wait_time') != -1){
            var para_ji = strSplit(lines[i], 1, " "); 
            var para_hun = strSplit(lines[i], 2, " "); 
            outArray.push( "420\n");
            outArray.push( para_ji + "\n");
            outArray.push( para_hun + "\n");
        }
        else if (lines[i].indexOf('wait_teachablemachine') != -1){
			var paraclass = strSplit(lines[i], 1, " ");
			var paravalue = strSplit(lines[i], 2, " ");
            outArray.push( "421\n");
            outArray.push( paraclass + "\n");
            outArray.push( paravalue + "\n");
        }
        else if (lines[i].indexOf('wait_light') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "422\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('wait_dark') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "423\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('wait_tempup') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "424\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('wait_tempdown') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "425\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('wait_signal') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "426\n");
        }
		
        else if (lines[i].indexOf('server_send') != -1){
            outArray.push( "520\n");
        }
        else if (lines[i].indexOf('server_error') != -1){
            var parast = strSplit(lines[i], 1, " "); 
            outArray.push( "521\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('server_return') != -1){
            var parast = strSplit(lines[i], 1, " "); 
            outArray.push( "523\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('server_changeai_return') != -1){
            outArray.push( "524\n");
        }
        else if (lines[i].indexOf('server_programsend') != -1){
            outArray.push( "525\n");

			var parast = strSplit(lines[i], 2, " "); 
			if (parast == "file1"){
				if (profile_data1 == ""){
					alert("ファイル１が選択されていません");
					return "";
				}
				outArray.push( profile_data_filename1 + "=" + profile_data1);
			}
			else if (parast == "file2"){
				if (profile_data2 == ""){
					alert("ファイル２が選択されていません");
					return "";
				}
				outArray.push( profile_data_filename2 + "=" + profile_data2);
			}
			else{
				if (profile_data3 == ""){
					alert("ファイル３が選択されていません");
					return "";
				}
				outArray.push( profile_data_filename3 + "=" + profile_data3);
			}

			var parano = strSplit(lines[i], 1, " "); 
			outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('server_group_programsend') != -1){
            outArray.push( "526\n");

			var parast = strSplit(lines[i], 2, " "); 
			if (parast == "file1"){
				if (profile_data1 == ""){
					alert("ファイル１が選択されていません");
					return "";
				}
				outArray.push( profile_data_filename1 + "=" + profile_data1);
			}
			else if (parast == "file2"){
				if (profile_data2 == ""){
					alert("ファイル２が選択されていません");
					return "";
				}
				outArray.push( profile_data_filename2 + "=" + profile_data2);
			}
			else{
				if (profile_data3 == ""){
					alert("ファイル３が選択されていません");
					return "";
				}
				outArray.push( profile_data_filename3 + "=" + profile_data3);
			}

            var groupno = strSplit(lines[i], 1, " ");
			//処理用配列
			var sendnoarraay = new Array();
			//グループ１なら
			if (groupno == "1"){
				sendnoarraay = groupArray1;
			}
			//グループ２なら
			else if  (groupno == "2"){
				sendnoarraay = groupArray2;
			}
			//グループ３なら
			else{
				sendnoarraay = groupArray3;
			}
			groupname = "グループ" + groupno;
			var allst = "";
			for ( var j = 0; j < sendnoarraay.length; j++ ) {
				if (j != 0){allst += "/";}
				allst += sendnoarraay[j];
			}
            outArray.push( allst + "\n");
		}
        else if (lines[i].indexOf('server_programreturn') != -1){
            outArray.push( "527\n");

			var parast = strSplit(lines[i], 1, " "); 
			if (parast == "file1"){
				if (profile_data1 == ""){
					alert("ファイル１が選択されていません");
					return "";
				}
				outArray.push( profile_data_filename1 + "=" + profile_data1);
			}
			else if (parast == "file2"){
				if (profile_data2 == ""){
					alert("ファイル２が選択されていません");
					return "";
				}
				outArray.push( profile_data_filename2 + "=" + profile_data2);
			}
			else{
				if (profile_data3 == ""){
					alert("ファイル３が選択されていません");
					return "";
				}
				outArray.push( profile_data_filename3 + "=" + profile_data3);
			}
        }
        else if (lines[i].indexOf('server_pass_set') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "530\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('server_variable_1') != -1){
            var parast = strSplit(lines[i], 1, " "); 
            outArray.push( "580\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('server_variable_2') != -1){
            var parast = strSplit(lines[i], 1, " "); 
            outArray.push( "581\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('server_variable_3') != -1){
            var parast = strSplit(lines[i], 1, " "); 
            outArray.push( "582\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('server_variable_4') != -1){
            var parast = strSplit(lines[i], 1, " "); 
            outArray.push( "583\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('server_variable_5') != -1){
            var parast = strSplit(lines[i], 1, " "); 
            outArray.push( "584\n");
            outArray.push( parast + "\n");
        }
        else if (lines[i].indexOf('server_ai_message_change') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "535\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('server_ai_question_anser') != -1){
            outArray.push( "536\n");
        }
		
        else if (lines[i].indexOf('doRepeat') != -1){
            outArray.push( "650\n");
            var para1 = strSplit(lines[i], 1, " ");
            outArray.push( para1 + "\n");
        }
        else if (lines[i].indexOf('endloop') != -1){
            outArray.push( "651\n");
        }
        else if (lines[i].indexOf('doIf') != -1){
            var para1 = strSplit(lines[i], 1, " ");	 
            if (para1 == "time<"){				
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "690\n");
                outArray.push( parapara + "\n");
            }
            else if (para1 == "time>="){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "691\n");
                outArray.push( parapara + "\n");
            }
            else if (para1 == "if_block_impo"){
                outArray.push( "692\n");
            }
            else if (para1 == "teachablemachine>"){
                var paraclass = strSplit(lines[i], 2, " ");
                var paravalue = strSplit(lines[i], 3, " ");
                outArray.push( "694\n");
                outArray.push( paraclass + "\n");
                outArray.push( paravalue + "\n");
            }
            else if (para1 == "teachablemachine<"){
                var paraclass = strSplit(lines[i], 2, " ");
                var paravalue = strSplit(lines[i], 3, " ");
                outArray.push( "695\n");
                outArray.push( paraclass + "\n");
                outArray.push( paravalue + "\n");
            }
            else if (para1.indexOf('light>=') != -1){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "696\n");                    
                outArray.push( parapara + "\n");
            }
            else if (para1.indexOf('light<') != -1){
                var parapara = strSplit(lines[i], 2, " ");      
                outArray.push( "697\n");                    
                outArray.push( parapara + "\n");
            }
            else if (para1.indexOf('temp>') != -1){
                var parapara = strSplit(lines[i], 2, " ");      
                outArray.push( "698\n");                    
                outArray.push( parapara + "\n");
            }
            else if (para1.indexOf('temp<') != -1){
                var parapara = strSplit(lines[i], 2, " ");      
                outArray.push( "699\n");                    
                outArray.push( parapara + "\n");
            }
            else if (para1.indexOf('if_block_signal') != -1){
                outArray.push( "701\n");
            }
            else if (para1.indexOf('if_block_notsignal') != -1){
                outArray.push( "702\n");
            }
			else if (para1 == "aiparam="){			
				var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "564\n");  
                outArray.push( parapara + "\n");
            }
			else if (para1 == "kinsi"){			
                outArray.push( "540\n"); 
            }
			else if (para1 == "notkinsi"){			
                outArray.push( "541\n");
            }
			else if (para1 == "toroku"){			
                outArray.push( "542\n");
            }
			else if (para1 == "nottoroku"){			
                outArray.push( "543\n");
            }
			else if (para1 == "pass"){			
                outArray.push( "546\n");
            }
			else if (para1 == "notpass"){			
                outArray.push( "547\n");
            }
            else if (para1 == "loopcnt>="){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "550\n");
                outArray.push( parapara + "\n");    
            }
            else if (para1 == "loopcnt<="){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "551\n");
                outArray.push( parapara + "\n");
            }
			else if (para1 == "juyo"){			
                outArray.push( "552\n");
            }
			else if (para1 == "notjuyo"){			
                outArray.push( "553\n");
            }
            else if (para1 == "personalno="){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "554\n");
                outArray.push( parapara + "\n");
            }
            else if (para1 == "personalno!="){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "555\n");
                outArray.push( parapara + "\n");
            }
            else if (para1 == "groupno="){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "562\n");
				if (parapara == "group1"){
					outArray.push( "1\n");
				}
				else if (parapara == "group2"){
					outArray.push( "2\n");
				}
				else{
					outArray.push( "3\n");
				}
            }
            else if (para1 == "groupno!="){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "563\n");
				if (parapara == "group1"){
					outArray.push( "1\n");
				}
				else if (parapara == "group2"){
					outArray.push( "2\n");
				}
				else{
					outArray.push( "3\n");
				}
            }
            else if (para1 == "stlength>="){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "558\n");
                outArray.push( parapara + "\n");
            }
            else if (para1 == "stlength<="){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "559\n");
                outArray.push( parapara + "\n");
            }
			else if (para1 == "myname"){			
                outArray.push( "560\n");
            }
			else if (para1 == "notmyname"){			
                outArray.push( "561\n");
            }
            else {
                outArray.push( "693\n");
            }
        }
        else if (lines[i].indexOf('else') != -1){
            
        }
        //elseのないendif
        else if (lines[i].indexOf('endif1') != -1){
            
        }
        //elseのあるendif
        else if (lines[i].indexOf('endif2') != -1){
            
        }
        
        //次の番地　elseの番地
		if (lines[i].indexOf('doIf') != -1){
			//次の番地			
            outArray.push(get_add(i + 1, Number(if_list[i]), com_head_address, if_list) + "\n");
			//elseの番地			
            outArray.push(get_else_add(i + 1, Number(if_list[i]), com_head_address, if_list, lines) + "\n");
		}
		else if (lines[i].indexOf('endif') != -1){
            
		}
		else if (lines[i].indexOf('else') != -1){
            
		}
		//次の番地
		else{
			//次の命令がelseなら
			if (lines[i + 1].indexOf('else') != -1){
				outArray.push(get_next_else_add(i + 1, Number(if_list[i]) - 1, com_head_address, if_list) + "\n");
			}
			//次の命令がelseのないendifなら
			else if (lines[i + 1].indexOf('endif1') != -1){
				outArray.push(get_next_else_add(i + 1, Number(if_list[i]), com_head_address, if_list) + "\n");
			}
			//次の命令がelseのあるendifなら
			else if (lines[i + 1].indexOf('endif2') != -1){
				outArray.push(get_next_else_add(i + 1, Number(if_list[i]) - 2, com_head_address, if_list) + "\n");
			}
			else{
            	outArray.push(get_add(i + 1, Number(if_list[i]), com_head_address, if_list) + "\n");
			}
		}
    }
    return outArray.join('');
}

//各コマンドの開始アドレスを保持
function get_head_address(dataarray){
	var add_list = new Array();
	var totalcnt=0;
	for ( var i = 0; i < dataarray.length; i++ ) {
        dataarray[i] = dataarray[i].trim();		
        if (dataarray[i].indexOf('else') != -1 || dataarray[i].indexOf('endif') != -1){
			add_list.push("");
			continue;
        }
		
		add_list.push( totalcnt.toString());
		
		if (dataarray[i] == "start" || dataarray[i] == "server_start" || dataarray[i] == "start_r" || 
			dataarray[i].indexOf('set_impo') != -1 || dataarray[i].indexOf('wait_signal') != -1 || 
			dataarray[i].indexOf('server_send') != -1 || dataarray[i].indexOf('endloop') != -1 || 
			dataarray[i].indexOf('disp_confirm') != -1 || dataarray[i].indexOf('server_changeai_return') != -1 || 
			dataarray[i].indexOf('server_ai_question_anser') != -1){			
            totalcnt += 2;
		}
        else if (dataarray[i].indexOf('message_send') != -1 || dataarray[i].indexOf('stamp_send') != -1 || 
				 dataarray[i].indexOf('program_send') != -1 || dataarray[i].indexOf('program_group_send') != -1 || 
				 dataarray[i].indexOf('message_group_send') != -1 || dataarray[i].indexOf('stamp_group_send') != -1 ||
				 dataarray[i].indexOf('ai_send') != -1 || dataarray[i].indexOf('ai_change') != -1 ||
				 dataarray[i].indexOf('ai_para_change') != -1 || dataarray[i].indexOf('wait_time') != -1 ||
				 dataarray[i].indexOf('wait_teachablemachine') != -1 || dataarray[i].indexOf('server_programsend') != -1 ||
				 dataarray[i].indexOf('server_group_programsend') != -1){
            totalcnt += 4;
        }
        else if (dataarray[i].indexOf('picturem_send') != -1 || dataarray[i].indexOf('picture_group_send') != -1 || 
				 dataarray[i].indexOf('server_set_pass') != -1 || dataarray[i].indexOf('set_pass') != -1 || 
				 dataarray[i].indexOf('set_server') != -1 || dataarray[i].indexOf('play_sound') != -1 || 
				 dataarray[i].indexOf('font_size') != -1 || dataarray[i].indexOf('font_color') != -1 || 
				 dataarray[i].indexOf('font_backcolor') != -1 || dataarray[i].indexOf('disp_backcolor') != -1 || 
				 dataarray[i].indexOf('wait_light') != -1 || dataarray[i].indexOf('wait_dark') != -1 || 
				 dataarray[i].indexOf('wait_tempup') != -1 || dataarray[i].indexOf('wait_tempdown') != -1 || 
				 dataarray[i].indexOf('server_send_no') != -1 || dataarray[i].indexOf('server_error') != -1 || 
				 dataarray[i].indexOf('server_light_send') != -1 || dataarray[i].indexOf('set_server_pass') != -1 || 
				 dataarray[i].indexOf('doRepeat') != -1 || dataarray[i].indexOf('ai_prompt_change') != -1 || 
				 dataarray[i].indexOf('client_variable_1') != -1 || dataarray[i].indexOf('client_variable_2') != -1 || 
				 dataarray[i].indexOf('client_variable_3') != -1 || dataarray[i].indexOf('client_variable_4') != -1 || 
				 dataarray[i].indexOf('client_variable_5') != -1 || dataarray[i].indexOf('server_return') != -1 || 
				 dataarray[i].indexOf('server_pass_set') != -1 || dataarray[i].indexOf('server_variable_1') != -1 || 
				 dataarray[i].indexOf('server_variable_2') != -1 || dataarray[i].indexOf('server_variable_3') != -1 || 
				 dataarray[i].indexOf('server_variable_4') != -1 || dataarray[i].indexOf('server_variable_5') != -1 || 
				 dataarray[i].indexOf('server_ai_message_change') != -1 || dataarray[i].indexOf('server_programreturn') != -1){
			totalcnt += 3;
		}		
        else if (dataarray[i].indexOf('doIf') != -1){
            var para1 = strSplit(dataarray[i], 1, " ");	
            if (para1 == "time<" || para1 == "time>=" || para1 == "light>=" || 
				para1 == "light<" || para1 == "temp>" || para1 == "temp<" || 
				para1 == "temp=" || para1 == "include_st=" || para1 == "include_st!=" || 
				para1 == "loopcnt>=" || para1 == "loopcnt<=" || para1 == "personalno=" || 
				para1 == "personalno!=" || para1 == "stlength>=" || para1 == "stlength<=" || 
				para1 == "aiparam=" || para1 == "groupno=" || para1 == "groupno!="){       
                totalcnt += 4;
            }
			else if (para1 == "teachablemachine>" || para1 == "teachablemachine<"){
				totalcnt += 5;
			}
            else {
                totalcnt += 3;
            }
        }
    }
	return add_list;
}

//cnt以降の次の番地取得
function get_add(cnt, iflevel, addarray ,ifarray){
	for ( var i = cnt; i < addarray.length; i++ ) {
		if (addarray[i] != ""){
			if (ifarray[i] == iflevel.toString() || ifarray[i] == (iflevel + 1).toString() || ifarray[i] == (iflevel - 1).toString()){
				return addarray[i];
			}
		}
	}	
	return addarray[addarray.length - 1];
}
//ifのelseの番地取得 cnt:検索開始位置　iflevel:検索開始位置のifレベル　addarrar;コマンドの開始アドレス　
function get_else_add(cnt, iflevel, addarray ,ifarray, allarray){	
	var doifcnt = 0;//ifが存在する個数
	for ( var i = cnt; i < addarray.length; i++ ) {
		//elseのあるifの場合
		if (doifcnt == 1){
			if (iflevel < Number(ifarray[i])){			
				if (addarray[i] != "" && i > cnt + 1){
					return addarray[i];
				}
			}
		}
		//elseのないifの場合
		if (doifcnt == -2){
			if (iflevel > Number(ifarray[i])){			
				if (addarray[i] != "" && i > cnt + 1){
					return addarray[i];
				}
			}
		}
		//if文があれば+1
		if (allarray[i].indexOf('doIf') != -1){
			doifcnt++;
		}
		else if (allarray[i].indexOf('else') != -1){
			doifcnt++;
		}
		else if (allarray[i].indexOf('endif') != -1){
			doifcnt -=2;
		}
	}
	return addarray[addarray.length - 1];
}
//次のコマンドがelseなら
function get_next_else_add(cnt, iflevel, addarray ,ifarray){
	//繰り返しの中に条件分岐が複数続いたときに２個目の条件分岐が実行されないので対象範囲を抽出
	var target_addarray = new Array();//addarrayから対象の範囲だけ抽出
	var target_ifarray = new Array();//ifarrayから対象の範囲だけ抽出
	for ( var i = cnt; i < addarray.length; i++ ) {
		if (ifarray[i] == "0"){
			target_addarray.push(addarray[i]);
			target_addarray.push(addarray[i + 1]);
			target_ifarray.push(ifarray[i]);
			target_ifarray.push(ifarray[i + 1]);
			break;
		}
		else{
			target_addarray.push(addarray[i]);
			target_ifarray.push(ifarray[i]);
		}
	}	
	
	for ( var i = 0; i < target_addarray.length; i++ ) {
		if (Number(target_ifarray[i]) == iflevel -2 ){
			if (target_addarray[i] != ""){return target_addarray[i];}
		}
	}
	//合流部の直前にendloopがあれば（通常は合流部の直前は何も無いがendloopでアドレスがあれば）繰り返しの中に条件分岐の場合
	if (target_addarray[target_addarray.length - 2] != ""){
		return target_addarray[target_addarray.length - 2];
	}
	else{
		return target_addarray[target_addarray.length - 1];
	}
}

//繰り返し中にAIがあるかどうか（あればfalse）
function repeat_check_ai(lines){
	var repeat_level = 0;//繰り返しの階層
	for ( var i = 0; i < lines.length; i++ ) {
		//繰り返し開始なら
		if (lines[i] == "650"){
			repeat_level++;
		}
		//繰り返し終了なら
		else if (lines[i] == "651"){
			repeat_level--;
		}
		//Aiなら
		else if (lines[i] == "630" || lines[i] == "631" || lines[i] == "632" || lines[i] == "633"){
			if (repeat_level != 0){
				return false;
			}
		}
	}
	return true;
}

//AIが複数あったらエラーに（あればfalse）
function ai_multi(lines){
	var aicnt = false;
	for ( var i = 0; i < lines.length; i++ ) {
		if (lines[i] == "535" || lines[i] == "536" || lines[i] == "630" || lines[i] == "631" || lines[i] == "632" || lines[i] == "633"){
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
			if (lines[s_cnt + 1] == "variable1"){
				r_st = returnSt1;
			}
			//変数２なら
			else if (lines[s_cnt + 1] == "variable2"){
				r_st = returnSt2;
			}
			//変数３なら
			else if (lines[s_cnt + 1] == "variable3"){
				r_st = returnSt3;
			}
			//変数４なら
			else if (lines[s_cnt + 1] == "variable4"){
				r_st = returnSt4;
			}
			//変数５なら
			else if (lines[s_cnt + 1] == "variable5"){
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
					//console.log("5ai_returnData " + ai_returnData);
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
//受信データ出力
function changeRecieveData() {
	//文字プログラムに変換
    outputProData();
    document.form4.textarea3.value=getRecievedata();    
}
//受信データに変換
function getRecievedata(){
    recieve_font_size = "";
	recieve_font_color = "";
	recieve_back_color = "";
	recieve_disp_color = "";
	recieve_sound = "";
    //改行コードを\nに統一
    var text  = document.getElementById('proText').value.replace(/\r\n|\r/g, "\n");
    var lines = text.split( '\n' );
	var if_list = new Array();//ifレベルを保持
    var com_head_address = new Array();//各コマンドのアドレスを保持
    var outArray = new Array();
	var in_pro = false;//start命令でtrue 空白行でfalse
	
	//start_rの位置を取得
	var r_start_pos = lines.indexOf('start_r');

	if (r_start_pos !== -1) {
		// start_r 以降を切り出して lines に再代入
		lines = lines.slice(r_start_pos);
	} 
	else {
		// 見つからなければ空配列に
		lines = [];
	}

    var LevelCount = 0;
	//ifレベルだけを保持
    for ( var i = 0; i < lines.length; i++ ) {
		if (lines[i].indexOf('doIf') != -1){
			LevelCount++;
		}
		else if (lines[i].indexOf('else') != -1){
            LevelCount++; 
		}
		else if (lines[i].indexOf('endif') != -1){
			LevelCount -= 2;
		}
		if_list.push(LevelCount.toString());
	}
	
	//各コマンドの開始アドレスを保持
	com_head_address = get_head_address(lines);
	
    for ( var i = 0; i < lines.length; i++ ) {
        lines[i] = lines[i].trim();
        // 空行があれば終了
        if (in_pro == true){
		  if ( lines[i] == '' || i == lines.length - 1) {
              break;
		  }
        }
		//start命令でフラグをたて分析開始
		if (in_pro == false){
			if (lines[i] == "start_r"){
				in_pro = true;				
			}
			else{
				continue;
			}
		}
        if (lines[i] == "start_r"){
            outArray.push( "900\n");         
        }
        else if (lines[i].indexOf('play_sound') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "950\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('font_size') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "960\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('font_color') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "961\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('font_backcolor') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "962\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('disp_backcolor') != -1){
            var parano = strSplit(lines[i], 1, " "); 
            outArray.push( "963\n");
            outArray.push( parano + "\n");
        }
        else if (lines[i].indexOf('doRepeat') != -1){
            outArray.push( "650\n");
            var para1 = strSplit(lines[i], 1, " ");
            outArray.push( para1 + "\n");
        }
        else if (lines[i].indexOf('endloop') != -1){
            outArray.push( "651\n");
        }
        else if (lines[i].indexOf('doIf') != -1){
            var para1 = strSplit(lines[i], 1, " ");	 
            if (para1 == "time<"){				
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "990\n");
                outArray.push( parapara + "\n");
            }
            else if (para1 == "time>="){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "991\n");
                outArray.push( parapara + "\n");
            }
            else if (para1 == "if_block_impo"){
                outArray.push( "992\n");
            }
            else if (para1 == "personalno="){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "996\n");
                outArray.push( parapara + "\n"); 
            }
            else if (para1 == "personalno!="){
                var parapara = strSplit(lines[i], 2, " ");
                outArray.push( "997\n");
                outArray.push( parapara + "\n");
            }
            else if (para1 == "if_block_notimpo"){
                outArray.push( "993\n");
            }
            else if (para1 == "if_block_error"){
                outArray.push( "998\n");
            }
            else if (para1 == "if_block_noterror"){
                outArray.push( "999\n");
            }
            else {
                
            }
        }
        else if (lines[i].indexOf('else') != -1){
            
        }
        //elseのないendif
        else if (lines[i].indexOf('endif1') != -1){
           
        }
        //elseのあるendif
        else if (lines[i].indexOf('endif2') != -1){
            
        }
        
        //次の番地　elseの番地
		if (lines[i].indexOf('doIf') != -1){
			//次の番地			
            outArray.push(get_add(i + 1, Number(if_list[i]), com_head_address, if_list) + "\n");
			//elseの番地			
            outArray.push(get_else_add(i + 1, Number(if_list[i]), com_head_address, if_list, lines) + "\n");
		}
		else if (lines[i].indexOf('endif') != -1){
            
		}
		else if (lines[i].indexOf('else') != -1){
            
		}
		//次の番地
		else{
			//次の命令がelseなら
			if (lines[i + 1].indexOf('else') != -1){
				outArray.push(get_next_else_add(i + 1, Number(if_list[i]) - 1, com_head_address, if_list) + "\n");
			}
			//次の命令がelseのないendifなら
			else if (lines[i + 1].indexOf('endif1') != -1){
				outArray.push(get_next_else_add(i + 1, Number(if_list[i]), com_head_address, if_list) + "\n");
			}
			//次の命令がelseのあるendifなら
			else if (lines[i + 1].indexOf('endif2') != -1){
				outArray.push(get_next_else_add(i + 1, Number(if_list[i]) - 2, com_head_address, if_list) + "\n");
			}
			else{
            	outArray.push(get_add(i + 1, Number(if_list[i]), com_head_address, if_list) + "\n");
			}
		}
    }
	outArray.push( "901\n");
    return outArray.join('');
 }
//受信プログラムを実行
function RecieveDataplay(dataSt){
	var m_data = dataSt.split( ',' );//分解
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
			if (m_data[6] == "true"){ 
				cnt = Number(lines[cnt + 1]);
			}
			else{
				cnt = Number(lines[cnt + 2]);
			}
		}
		//重要でないなら
		else if (lines[cnt] == "993"){	
			if (m_data[6] == "true"){ 
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