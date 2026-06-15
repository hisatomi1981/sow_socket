//リアルタイム実行の転送処理(ipad)
function pro_senddata_ipad(data){
	var commdata = data.split("-");
	
	var ledsoundmode = 0;
	if (commdata[0] == "230"){
		ledsoundmode = 0;//LEDプログラムなら
	}
	else{
		ledsoundmode = 1;//音プログラムなら
	}
	
	var startArray = new Array(); 
	for  (var i = 0; i < commdata.length; i++) {
		if (commdata[i] == ""){break;}
		if (commdata[i] == "\n"){continue;}
		
		startArray.push(commdata[i]);		
	}
	//startArray.unshift("251");
	
	//LEDプログラムは250を追加。音プログラムは250があるので何もしない
	if (startArray[startArray.length - 1] != "250"){
		startArray.push("250");
	}
	
	var blcnt = Math.ceil(startArray.length / 16);//32バイトずつ転送するので何ブロックあるか
    for  (var i = 0; i < blcnt; i++) {
		var sendArray = new Array(19);   
    	sendArray.fill(0);
		sendArray[0] = 253;
		//LEDプログラムなら
		if (ledsoundmode == 0){
			sendArray[1] = 1;// 1:転送
		}
		//音プログラムなら
		else{
			sendArray[1] = 3;// 3:音転送
		}	
		sendArray[2] = i + 9;
		for (var j = 0; j < 16; j++) {
			if ((i * 16 + j) > startArray.length -1){break;}
        	sendArray[j + 3] = Number(startArray[i * 16 + j]);			
		}
		sendDataBySound(sendArray);  				
    	sleep(500);   		  
	}
	var playArray = new Array(19);   
	playArray.fill(0);
	playArray[0] = 253;
	//LEDプログラムなら
	if (ledsoundmode == 0){
		playArray[1] = 2;// 1:転送 2:実行
	}
	//音プログラムなら
	else{
		playArray[1] = 4;// 3:音転送 4:音実行
	}	
	sendDataBySound(playArray);
	
}
//リアルタイム実行の転送処理(HID)
function pro_senddataHID(data){
	var commdata = data.split("-");
	
	var startArray = new Array(); 
	
	if (commdata[0] == "230"){
		startArray.push("248");
		startArray.push("240");
	}
	else{
		startArray.push("251");
		startArray.push("242");
		
	}
	for  (var i = 0; i < commdata.length; i++) {
		if (commdata[i] == ""){break;}
		if (commdata[i] == "\n"){continue;}
		
		startArray.push(commdata[i]);		
	}
	//LEDプログラムは250を追加。音プログラムは250があるので何もしない
	if (startArray[startArray.length - 1] != "250"){
		startArray.push("250");
	}
	transferHID(startArray);	
	
}
//リアルタイム実行の転送処理
function pro_senddata(data){
	//console.log(data);
	var commdata = data.split("-");	  
    var blcnt = Math.ceil(commdata.length / 64);
	
	for  (var i = 0; i < blcnt; i++) {
		var startArray = new Array(64); 
    	startArray.fill("255");
		if (commdata[0] == "230"){
			startArray[0] = "240";
		}
		else{
			startArray[0] = "242";		
		}
		
		if (i == 0){startArray[1] = "1";}
		else{startArray[1] = "2";}	
		
		//console.log(startArray);	
    	transferUSB4(startArray);  		
    	sleep(300); 
		
		var sendArray = new Array(64);   
    	sendArray.fill("255");
		for (var j = 0; j < 64; j++) {
			if ((i * 64 + j) > commdata.length -1){break;}
        	sendArray[j] = Number(commdata[i * 64 + j]);			
		}		
		//console.log(sendArray);	
		transferUSB4(sendArray);  				
    	sleep(300);   		  
		  
	}
	
	var endArray = new Array(64); 
	endArray.fill("255");
	if (commdata[0] == "230"){
		endArray[0] = "241";
	}
	else{
		endArray[0] = "243";		
	}
	//console.log(endArray);	
	transferUSB4(endArray);  	
}