var print_y = 20;
//非表示のCanvasにレポート内容をコピー
function saveReport(divname,school_id_userid){
	var pass = document.getElementById("pass").value;
    // 4桁以上の数字のみ許可
    if (!/^\d{4,}$/.test(pass)) {
        alert("パスワードは4桁以上の数字で入力してください。");
        return false;
    }

	print_y = 20;
	let nen = document.getElementById('print_nen').value;
	let kumi = document.getElementById('print_kumi').value;
	let ban = document.getElementById('print_ban').value;
	let name = document.getElementById('print_name').value;
	let title = document.getElementById('print_title').value;
	let textkoso = document.getElementById('print-text-koso').value;
	let textsiyo = document.getElementById('print-text-siyo').value;
	let textkuhu = document.getElementById('print-text-kuhu').value;
	let textkanso = document.getElementById('print-text-kanso').value;

	//HTMLのcanvas情報をcanvasへ関連付け
	var canvas_copy = document.getElementById('saveboard');
	//コンテクストを取得
	var ctx_copy = canvas_copy.getContext('2d');
	
	//canvasの高さを計算
	//年行、名前行、タイトル行、構想：仕様：工夫：感想：30px × 3, 20px × 4, 10px × 4 + 余白20px
	var total = 210 + 20;
	total += text_height(textkoso);
	total += text_height(textsiyo);
	total += text_height(textkuhu);
	total += text_height(textkanso);
	canvas_copy.height = total+ 500;
	//フォントと文字サイズを決める(heightを更新した後でないと反映されない)
	ctx_copy.font = "18px Arial";

	ctx_copy.fillStyle = '#FFFFFF';
	ctx_copy.fillRect(0, 0, canvas_copy.width, canvas_copy.height);
	//塗りつぶしの色を決める（青）
	ctx_copy.fillStyle = "rgb(0, 0, 0)";

	//文字を描くfillText()
	ctx_copy.fillText(nen + "年 " + kumi + "組 " + ban + "番", 100, print_y);
	print_y += 30;
	ctx_copy.fillText("名前：" + name , 100, print_y);
	print_y += 30;
	ctx_copy.fillText("タイトル：" + title , 20, print_y);
	print_y += 30;
	ctx_copy.fillText("構想：", 20, print_y);
	print_y += 20;
	get_print_text(ctx_copy,textkoso,40);
	print_y += 10;
	ctx_copy.fillText("仕様：", 20, print_y);
	print_y += 20;
	get_print_text(ctx_copy,textsiyo,40);
	print_y += 10;
	ctx_copy.fillText("工夫：", 20, print_y);
	print_y += 20;
	get_print_text(ctx_copy,textkuhu,40);
	print_y += 10;
	ctx_copy.fillText("感想：", 20, print_y);
	print_y += 20;
	get_print_text(ctx_copy,textkanso,40);
	print_y += 10;

	//現在の日時を取得
	var now = new Date();
	var Year = now.getFullYear();
	var Month = now.getMonth()+1;
	var Hiniti = now.getDate();
	var Hour = now.getHours();
	var Min = now.getMinutes();
	var Sec = now.getSeconds();
	var filename = Year + "_" + Month + "_" + Hiniti + "_" + Hour + "_" + Min + "_" + Sec + "_" + school_id_userid + "_"
				 + nen + "_" + kumi + "_"+ ban;

	//Blocklyの画面を描画
	//html2canvas(document.getElementById(divname)).then(canvas => {
	//ここから修正
	html2canvas(document.getElementById(divname), {
			scale: 1
			}).then(canvas => {//ここまで修正
		// 画像を読み込む
		const img = new Image();
		img.onload = () => {
		  // 画像をcanvasに描画
		  ctx_copy.drawImage(img, 0, print_y);		  
	 	  save(filename, title, name, pass);
		};
		img.src = canvas.toDataURL();
 	});
	//印刷画面の非表示
	printNoDisplay();
}

//Canvas1を保存
function save(filename, title, name, pass){
	jQuery(function($) {
	    // body部パラメーター
	    var data = {};
	    // Canvasのデータをbase64でエンコードした文字列を取得
	    var canvasData = document.getElementById('saveboard').toDataURL();

	    // 不要な情報を取り除く
	    canvasData = canvasData.replace(/^data:image\/png;base64,/, '');

	    data.image = canvasData;

	    $.ajax({
	        url: 'report_img_save.php',
	        type: 'POST',
			contentType: 'application/json',  // Add this line
	        success: function() {
		    	//console.log('成功');
	            // 成功時の処理
	        },
	        error(jqXHR, textStatus, errorThrown) {
		    	//console.log('saverr');
		    	//console.log(jqXHR);
		    	//console.log(textStatus);
	            // 失敗時の処理
	        },
	        //data: data,
			data: JSON.stringify({ "image": data.image, "filename": filename, "title": title, "name": name, "pass": pass }),
	        dataType: 'json'
	    });

	});
}
//textareaの高さ
function text_height(outputSt){	
	var total = 0;
	var canvas_copy = document.getElementById('saveboard');
	var lineHeight = 20; // 行の高さ
	var lines = outputSt.split('\n');
	for (var i = 0; i < lines.length; i++) {
		var line = lines[i];
		var text_arr = splitText(line, canvas_copy.width - 50);
		for (var j = 0; j < text_arr.length; j++) {			
			total += lineHeight;
		}
	}
	return total;
}
function get_print_text(ctx_copy,outputSt,x){
	var canvas_copy = document.getElementById('saveboard');
	var lineHeight = 20; // 行の高さ
	var lines = outputSt.split('\n');
	for (var i = 0; i < lines.length; i++) {
		var line = lines[i];
		var text_arr = splitText(line, canvas_copy.width - 50);
		for (var j = 0; j < text_arr.length; j++) {
			ctx_copy.fillText(text_arr[j], x, print_y);
			print_y += lineHeight;
		}
	}
}
//長い文字列を分解（maxSize:canvasの幅）
function splitText(text, maxSize) {
	const fontSize = 18; // フォントサイズ（18px）
	const maxLength = Math.floor(maxSize / fontSize); // 1行の最大文字数
  
	if (text.length <= maxLength) {
	  // テキストの長さが最大文字数以下の場合、そのまま出力
	  return [text];
	} else {
	  // テキストを最大文字数ごとに分割して配列にする
	  const chunks = [];
	  let startIndex = 0;
	  let endIndex = maxLength;
  
	  while (startIndex < text.length) {
		chunks.push(text.slice(startIndex, endIndex));
		startIndex = endIndex;
		endIndex += maxLength;
	  }
  
	  return chunks;
	}
}