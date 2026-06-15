//禁止ワード登録
function setkinsiword() {
    document.form4.textarea3.value += document.getElementById('kinsiword').value + "\n";  
	document.getElementById('kinsiword').value = "";
	localStorage.setItem("kinsiword",  document.getElementById('kinsiText').value);
}
function delete_kinsi() {
	document.getElementById('kinsiText').value ="";
}
//登録ワード登録
function settorokuword() {
    document.form5.textarea4.value += document.getElementById('torokuword').value + "\n";  
	document.getElementById('torokuword').value = "";
	localStorage.setItem("torokuword",  document.getElementById('torokuText').value);	
}
function delete_toroku() {
	document.getElementById('torokuText').value ="";
}