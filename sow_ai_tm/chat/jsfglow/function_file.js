//ローカルストレージへプログラムの自動保存
function client_autosave(){
	//var savedPrefix = 'saved.hisatomi-network-flow-server.blk.';    //ローカルストレージに保存する時のキー
	//name = savedPrefix + name;
        
	document.getElementById("mySavedModel").value = myDiagram.model.toJson();
	myDiagram.isModified = false; 
	//console.log( document.getElementById("mySavedModel").value);
	window.localStorage.setItem("flow_client_mainpro2", document.getElementById("mySavedModel").value);
}
function client_autosavefile_read(){
	if (window.localStorage["flow_client_mainpro2"]) {
		myDiagram.model = go.Model.fromJson(window.localStorage["flow_client_mainpro2"]);
	} 
}
//ローカルストレージへプログラムの自動保存
function server_autosave(){
	//var savedPrefix = 'saved.hisatomi-network-flow-server.blk.';    //ローカルストレージに保存する時のキー
	//name = savedPrefix + name;
        
	document.getElementById("mySavedModel").value = myDiagram.model.toJson();
	myDiagram.isModified = false; 
	//console.log( document.getElementById("mySavedModel").value);
	window.localStorage.setItem("flow_server_mainpro", document.getElementById("mySavedModel").value);
}
function server_autosavefile_read(){
	if (window.localStorage["flow_server_mainpro"]) {
		myDiagram.model = go.Model.fromJson(window.localStorage["flow_server_mainpro"]);
	} 
}
//ログの保存
function savelog(){
	//改行コードを\nに統一
	var text = document.getElementById("messages").value.replace(/\r\n|\r/g, "\n");
    var lines = text.split( '\n' );
	var alllog = "";
	//暗号化にチェック入っていたら
	if (document.getElementById("log_enc").checked){
		alllog = Encrypt("通信ログ(" + get_time() + ")") + "\n";
	}
	else{
		alllog = "通信ログ(" + get_time() + ")\n";
	}	
    for  (var i = 0; i < lines.length; i++) {
		if (lines[i].indexOf('<div class=') != -1 || lines[i].indexOf('</div') != -1){
			continue;
		}
		else if (lines[i].indexOf('<br>') != -1) {
			alllog += lines[i].replace('<br>', "\n");
		}
		else {
			alllog += lines[i].replace(lines[i], lines[i] + "\n");
		}
	}
	
	let blob = new Blob([alllog],{type:"text/plan"});
	let link = document.createElement('a');
	link.href = URL.createObjectURL(blob);
	link.download = '通信ログ.txt';
	link.click();
}
//ログの保存(iPad用)
function savelog_ipad(){
	//改行コードを\nに統一
	var text = document.getElementById("messages").value.replace(/\r\n|\r/g, "\n");
    var lines = text.split( '\n' );
	var alllog = "";
	//暗号化にチェック入っていたら
	if (document.getElementById("log_enc").checked){
		alllog = Encrypt("通信ログ(" + get_time() + ")") + "\n";
	}
	else{
		alllog = "通信ログ(" + get_time() + ")\n";
	}	
    for  (var i = 0; i < lines.length; i++) {
		if (lines[i].indexOf('<div class=') != -1 || lines[i].indexOf('</div') != -1){
			continue;
		}
		else if (lines[i].indexOf('<br>') != -1) {
			alllog += lines[i].replace('<br>', "\n");
		}
		else {
			alllog += lines[i].replace(lines[i], lines[i] + "\n");
		}
	}
	
	var result = prompt("ファイル名を入力してください");
	if (result == null){return;}

	// 隠しフォーム生成
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "../../../common/download_ipad.php";

    // 保存内容
    form.appendChild(Object.assign(document.createElement("input"), {
        type: "hidden", name: "filename", value: result + "(通信ログ).txt"
    }));
    form.appendChild(Object.assign(document.createElement("input"), {
        type: "hidden", name: "content", value: alllog
    }));

    // ★★ 呼び出し元の必須 POST を再送信 ★★
    form.appendChild(Object.assign(document.createElement("input"), {
        type: "hidden", name: "school_id_userid", value: "<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES); ?>"
    }));
    form.appendChild(Object.assign(document.createElement("input"), {
        type: "hidden", name: "school_name_userid", value: "<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES); ?>"
    }));

    document.body.appendChild(form);
    form.submit();
}
//ログの読み込み
function read_log(datast){
	var addst = "";
	var text = datast.replace(/\r\n|\r/g, "\n");
	var lines = text.split( '\n' );	
	for  (var i = 1; i < lines.length; i++) {
		if (lines[i] == ""){continue;}
		else {
			if (i == 1){addst += lines[i];}
			else{addst += "\n" + lines[i];}	
		}
	}
	if (lines[0].startsWith('通信ログ') == false){
		document.getElementById("log_enc").checked = true;
	}
	document.getElementById("messages").value = addst + "\n";	
	
}
//ログの読み込み(iPad)
function read_log_ipad(datast){
	var addst = "";
	var text = datast.replace(/\r\n|\r/g, "\n");
	var lines = text.split( '\n' );	
	
	for  (var i = 1; i < lines.length; i++) {
		if (lines[i] == ""){continue;}
		else {
			if (i == 1){addst += lines[i];}
			else{addst += "\n" + lines[i];}	
		}
	}
	if (lines[0].startsWith('通信ログ') == false){
		document.getElementById("log_enc").checked = true;
	}
	document.getElementById("messages").value = addst + "\n";	
}
//ファイルへ保存
function downloadCode() {
	document.getElementById(
	  "mySavedModel"
	).value = myDiagram.model.toJson();
	myDiagram.isModified = false; 
	  
	var textToWrite = document.getElementById("mySavedModel").innerHTML;
	var textFileAsBlob = new Blob([myDiagram.model.toJson()], {
	  type: "text/plain",
	});
	
	var fileNameToSaveAs = prompt("ファイル名を入力してください");
	if (fileNameToSaveAs == null){return;}

	var downloadLink = document.createElement("a");
	downloadLink.download = fileNameToSaveAs + "(クライアント).wanetf";
	downloadLink.innerHTML = "Download File";
	if (window.webkitURL != null) {
	  // Chrome allows the link to be clicked without actually adding it to the DOM.
	  downloadLink.href = window.webkitURL.createObjectURL(textFileAsBlob);
	} else {
	  // Firefox requires the link to be added to the DOM before it can be clicked.
	  downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
	  downloadLink.onclick = destroyClickedElement;
	  downloadLink.style.display = "none";
	  document.body.appendChild(downloadLink);
	}

	downloadLink.click();
}
//ファイルへ保存(iPad用)
function downloadCode_ipad() {
	document.getElementById(
	  "mySavedModel"
	).value = myDiagram.model.toJson();
	myDiagram.isModified = false; 
	  
	var textToWrite = document.getElementById("mySavedModel").innerHTML;
	var text = myDiagram.model.toJson();
	
	var result = prompt("ファイル名を入力してください");
	if (result == null){return;}

	// 隠しフォーム生成
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "../../../common/download_ipad.php";

    // 保存内容
    form.appendChild(Object.assign(document.createElement("input"), {
        type: "hidden", name: "filename", value: result + "(クライアント).wanet"
    }));
    form.appendChild(Object.assign(document.createElement("input"), {
        type: "hidden", name: "content", value: text
    }));

    // ★★ 呼び出し元の必須 POST を再送信 ★★
    form.appendChild(Object.assign(document.createElement("input"), {
        type: "hidden", name: "school_id_userid", value: "<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES); ?>"
    }));
    form.appendChild(Object.assign(document.createElement("input"), {
        type: "hidden", name: "school_name_userid", value: "<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES); ?>"
    }));

    document.body.appendChild(form);
    form.submit();
}
//ファイルへ保存
function downloadCode_s() {
	document.getElementById(
	  "mySavedModel"
	).value = myDiagram.model.toJson();
	myDiagram.isModified = false; 
	  
	var textToWrite = document.getElementById("mySavedModel").innerHTML;
	var textFileAsBlob = new Blob([myDiagram.model.toJson()], {
	  type: "text/plain",
	});
	
	var fileNameToSaveAs = prompt("ファイル名を入力してください");
	if (fileNameToSaveAs == null){return;}

	var downloadLink = document.createElement("a");
	downloadLink.download = fileNameToSaveAs + "(サーバ).wanetf";
	downloadLink.innerHTML = "Download File";
	if (window.webkitURL != null) {
	  // Chrome allows the link to be clicked without actually adding it to the DOM.
	  downloadLink.href = window.webkitURL.createObjectURL(textFileAsBlob);
	} else {
	  // Firefox requires the link to be added to the DOM before it can be clicked.
	  downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
	  downloadLink.onclick = destroyClickedElement;
	  downloadLink.style.display = "none";
	  document.body.appendChild(downloadLink);
	}

	downloadLink.click();
}
//ファイルへ保存(iPad用)
function downloadCode_ipad_s() {
	document.getElementById(
	  "mySavedModel"
	).value = myDiagram.model.toJson();
	myDiagram.isModified = false; 
	  
	var textToWrite = document.getElementById("mySavedModel").innerHTML;
	var text = myDiagram.model.toJson();
	
	var result = prompt("ファイル名を入力してください");
	if (result == null){return;}

	// 隠しフォーム生成
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "../../../common/download_ipad.php";

    // 保存内容
    form.appendChild(Object.assign(document.createElement("input"), {
        type: "hidden", name: "filename", value: result + "(サーバ).wanet"
    }));
    form.appendChild(Object.assign(document.createElement("input"), {
        type: "hidden", name: "content", value: text
    }));

    // ★★ 呼び出し元の必須 POST を再送信 ★★
    form.appendChild(Object.assign(document.createElement("input"), {
        type: "hidden", name: "school_id_userid", value: "<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES); ?>"
    }));
    form.appendChild(Object.assign(document.createElement("input"), {
        type: "hidden", name: "school_name_userid", value: "<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES); ?>"
    }));

    document.body.appendChild(form);
    form.submit();
}
//ファイルから読み込み
function uploadCode(datast) {
	myDiagram.model = go.Model.fromJson(datast);
}
//ストレージへ保存
function saveCode() {
	var savedPrefix = 'saved.hisatomi-network2-flow-client.blk.';    //ローカルストレージに保存する時のキー
	if ('localStorage' in window) {
        var name = null;
        while (!name) {
          name = window.prompt('プログラム名を入力してください');
          if (!name) { return; } // ignore if empty
          if (window.localStorage[savedPrefix + name]) {
            if (! window.confirm(name + ' は存在します。上書きしますか?')) {
              name = null;
            }
          }
        }
        name = savedPrefix + name;
        
		document.getElementById("mySavedModel").value = myDiagram.model.toJson();
		myDiagram.isModified = false; 
		//console.log( document.getElementById("mySavedModel").value);
        window.localStorage.setItem(name, document.getElementById("mySavedModel").value);
      }
}
//ストレージへ保存されたファイルの表示処理
function restoreBlocks() {
	var savedBlockPrefix = 'saved.hisatomi-network2-flow-client.blk.';
	if ('localStorage' in window) {
	  var modal = document.getElementById('restoreModal');
	  var list  = document.getElementById('restoreList');
	  var items = [];
	  for (var key in window.localStorage) {
		if (key.startsWith(savedBlockPrefix)) {
		  var keyBody = key.substr(savedBlockPrefix.length);
		  items.push(keyBody);
		}
	  }
	if (items.length == 0) {
		window.alert('保存されているプログラムはありません');
		return;
	}
	items.sort();
	var itemsHtml = '';
	for (var i = 0; i < items.length; i++) {
		itemsHtml += '<li><a onclick="restoreBlocksFrom(\'' +
					 items[i] + '\')">' + items[i] + '</a></li>';
		}
		list.innerHTML = itemsHtml;
		modal.style.display = 'block';
	}
}
//ストレージから読み込み
function restoreBlocksFrom(name) {
	var savedBlockPrefix = 'saved.hisatomi-network2-flow-client.blk.';
	var modal = document.getElementById('restoreModal');
	modal.style.display = 'none';
	if (!name) { return; } // ignore if empty
	if (window.localStorage[savedBlockPrefix + name]) {
		name = savedBlockPrefix + name;	
		//console.log(window.localStorage[name]);
		myDiagram.model = go.Model.fromJson(window.localStorage[name]);
	} else {
		window.alert('Error: ' + name + ' がありません');
	}
}
//ストレージへ保存
function saveCode_s() {
	var savedPrefix = 'saved.hisatomi-network-flow-server.blk.';    //ローカルストレージに保存する時のキー
	if ('localStorage' in window) {
        var name = null;
        while (!name) {
          name = window.prompt('プログラム名を入力してください');
          if (!name) { return; } // ignore if empty
          if (window.localStorage[savedPrefix + name]) {
            if (! window.confirm(name + ' は存在します。上書きしますか?')) {
              name = null;
            }
          }
        }
        name = savedPrefix + name;
        
		document.getElementById("mySavedModel").value = myDiagram.model.toJson();
		myDiagram.isModified = false; 
		//console.log( document.getElementById("mySavedModel").value);
        window.localStorage.setItem(name, document.getElementById("mySavedModel").value);
      }
}
//ストレージへ保存されたファイルの表示処理
function restoreBlocks_s() {
	var savedBlockPrefix = 'saved.hisatomi-network-flow-server.blk.';
	if ('localStorage' in window) {
	  var modal = document.getElementById('restoreModal');
	  var list  = document.getElementById('restoreList');
	  var items = [];
	  for (var key in window.localStorage) {
		if (key.startsWith(savedBlockPrefix)) {
		  var keyBody = key.substr(savedBlockPrefix.length);
		  items.push(keyBody);
		}
	  }
	if (items.length == 0) {
		window.alert('保存されているプログラムはありません');
		return;
	}
	items.sort();
	var itemsHtml = '';
	for (var i = 0; i < items.length; i++) {
		itemsHtml += '<li><a onclick="restoreBlocksFrom(\'' +
					 items[i] + '\')">' + items[i] + '</a></li>';
		}
		list.innerHTML = itemsHtml;
		modal.style.display = 'block';
	}
}
//ストレージから読み込み
function restoreBlocksFrom_s(name) {
	var savedBlockPrefix = 'saved.hisatomi-network-flow-server.blk.';
	var modal = document.getElementById('restoreModal');
	modal.style.display = 'none';
	if (!name) { return; } // ignore if empty
	if (window.localStorage[savedBlockPrefix + name]) {
		name = savedBlockPrefix + name;	
		//console.log(window.localStorage[name]);
		myDiagram.model = go.Model.fromJson(window.localStorage[name]);
	} else {
		window.alert('Error: ' + name + ' がありません');
	}
}
function cancelRestoreBlocks() {
	var modal = document.getElementById('restoreModal');
	modal.style.display = 'none';
}
function pressCancelRestoreBlocks(event) {
	var modal = document.getElementById('restoreModal');
	if (event.target == modal) {
		cancelRestoreBlocks();
	}
}
//全てのキャッシュをクリア
function cash_clear_all(){
	localStorage.clear();
}