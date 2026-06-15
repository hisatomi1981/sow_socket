//クライアントで受信した時
function get_html_data(st){
	//console.log(st);
	var m_data = st.split( ',' );//分解
	var data = m_data[7].split( '/' );//分解
	//グローバルIPが一致しなかったら
	if (m_data[1] != myIP){return ""}
	//グローバルIPが一致したら
	else {
		//受信プログラムをデータに変換
		changeRecieveData();
		
		recieve_font_size = "";
		recieve_font_color = "";
		recieve_back_color = "";
		recieve_disp_color = "";
		recieve_sound = "";//着信音
		//受信プログラムを実行
		RecieveDataplay(st);
		const div = document.getElementById("messages");
		var myNo  = document.getElementById('myno').value;
		var myName  = document.getElementById('myname').value;
		var addst ="";
		//生徒番号がなかったら
		if (myNo == ""){return;}
		
		//受信者が複数あったら（グループ送信時）
		var comm_data = m_data[5].split('/');//分解
		
		//送信者でも受信者でもない場合
		if (myNo != m_data[3] && comm_data.indexOf(myNo) == -1){return;}
		
		if (m_data[0] == "ERR"){
				//console.log(st);
			//自分が送信者だったらエラー受信表示
			if (myNo == m_data[3]){
				addst = '<div class="mode_recieve">';
				addst += m_data[2] + "：　" +m_data[4] + "から受信<br>";
				
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
				addst += data[11];
				//終了タグ
				addst += '</p></div></div>';
				//console.log(addst);				
				
				if (addst != ""){
					allMessage += addst;
				}		
				div.innerHTML = allMessage;				
				//1秒後にstartCdS実行
				delayedCall(100,function(){   
				  //スクロールを一番下へ
				   scroll_change();
				});
				
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
			}
		}
		else if (m_data[0] == "CLIENT_A"){
			//時間、あて先表示
			//自分が送信者だったら
			if (myNo == m_data[3]){	
				addst = '<div class="mode_send">';		   
				addst += m_data[2] + "：　" +m_data[5] + "へ送信<br>";
				//画面背景色が指定されていれば
				if (data[10] != ""){
					changeBoxColor( data[10] );
				}
				else{
					changeBoxColor( "#fafaa2" );
				}
			}
			//自分が受信者だったら
			else {
				//サーバがいない場合（2人通信）
				if (m_data[4] == ""){
					addst = '<div class="mode_recieve">';
					if (m_data[6] == ""){
						addst += m_data[2] + "：　" + m_data[3] + "から受信<br>";	
					}
					else{
						addst += m_data[2] + "：　" + m_data[6] + "(" + m_data[3] + ") から受信<br>";	
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
				}
				else{return;}
			}
			//メッセージ
			if (data[0] == "文字" || data[0] == "プログラム" ) {
				//送信者なら
				if (myNo ==m_data[3]){	
					//メッセージ背景色
					if (data[9] != ""){	
						addst += '<div class="fuki-right" style="background-color:'+ data[9] +'">';
					}
					else{
						addst += '<div class="fuki-right">';
					}
					//文字の大きさ
					if (data[7] != ""){
						//文字色
						if (data[8] != ""){addst += '<p style="font-size: '+ data[7] + 'pt;color:' + data[8] + '">';}
						else{ addst += '<p style="font-size: '+ data[7]+'pt">';}
					}
					else{
						if (data[8] != ""){addst += '<p style="color:' + data[8] + '">';}
						else {addst += '<p>';}
					}					
				}
				//受信者なら
				else{
					//メッセージ背景色
					if (recieve_back_color != ""){	
						addst += '<div class="fuki-left" style="background-color:'+ recieve_back_color +'">';
					}
					else{
						addst += '<div class="fuki-left">';
					}
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
				}
				if (data[0] == "文字"){
					//メッセージ
					addst += data[11];
				}
				else{
					var pr_data = data[11].split( '=' );//分解
					addst += "データ：" + escapeHtml(pr_data[0]);
					//受信者なら
					if (myNo !=m_data[3]){
						//オーロラへ転送
						if (pr_data[1] != ""){
							pro_senddata_ipad(pr_data[1]);
						}
					}
				}
				//終了タグ
				addst += '</p></div></div>';
			}
			else if (data[0] == "スタンプ") {
				addst += '<img src="img/illust/'+data[11]+'" width="79" height="79" alt=""/>';
				addst += '</div>';
			}
			else if (data[0] == "写真") {
				addst += '<img src="https://www.hisatomi-kk.com/app/upload/'+data[9]+'" alt=""/>';
				addst += '</div>';
			}
			
			if (addst != ""){
				allMessage += addst;
			}
			div.innerHTML = allMessage;		
			//console.log(allMessage);
			//1秒後にstartCdS実行
			delayedCall(100,function(){   
			  //スクロールを一番下へ
			   scroll_change();
			});
		}		
		else if (m_data[0] == "CLIENT_B"){
			//時間、あて先表示
			//自分が受信者だったら
			if (comm_data.indexOf(myNo) != -1){
				//サーバがいる場合（3人通信）
				if (m_data[4] != ""){
					addst = '<div class="mode_recieve">';
					if (m_data[6] == ""){
						addst += m_data[2] + "：　" + m_data[3] + "から受信<br>";	
					}
					else{
						addst += m_data[2] + "：　" + m_data[6] + "(" + m_data[3] + ") から受信<br>";	
					}
				}
				else{return;}
				
				//メッセージ
				if (data[0] == "文字" || data[0] == "プログラム") {
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
					if (data[0] == "文字"){
						//メッセージ
						addst += data[11];
					}
					else{
						var pr_data = data[11].split( '=' );//分解
						addst += "データ：" + escapeHtml(pr_data[0]);
						//受信者なら
						if (myNo !=m_data[3]){
							//オーロラへ転送
							if (pr_data[1] != ""){
								pro_senddata_ipad(pr_data[1]);
							}
						}
					}
					//終了タグ
					addst += '</p></div></div>';				
				}
				else if (data[0] == "スタンプ") {
					addst += '<img src="img/illust/'+data[11]+'" width="79" height="79" alt=""/>';
					addst += '</div>';
				}
				else if (data[0] == "写真") {				
					addst += '<img src="https://www.hisatomi-kk.com/app/upload/'+data[9]+'" alt=""/>';
					addst += '</div>';
				}
				//console.log(addst);
		
				if (addst != ""){
					allMessage += addst;
				}		
				div.innerHTML = allMessage;				
				//1秒後にstartCdS実行
				delayedCall(100,function(){   
				  //スクロールを一番下へ
				   scroll_change();
				});
				
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
			}
			else{
				return;
			}
		}
		else if (m_data[0] == "SERVER"){
			//サーバから受信
			if (myNo == m_data[5]){
				addst = '<div class="mode_recieve">';
				addst += m_data[2] + "：　サーバ" + m_data[5] + "から受信<br>";
				addst += '<div class="fuki-left">';
				addst += '<p>';
				//メッセージ
				addst += data[11];
				
				//終了タグ
				addst += '</p></div></div>';
			}
			else{return;}
			
			if (addst != ""){
				allMessage += addst;
			}
			div.innerHTML = allMessage;		
			//console.log(allMessage);
			//1秒後にstartCdS実行
			delayedCall(100,function(){   
			  //スクロールを一番下へ
			   scroll_change();
			});
		}		
	}
}