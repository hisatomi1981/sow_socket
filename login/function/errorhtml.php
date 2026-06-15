<?php

//------------------------------------------------------
//　直接ページを開こうとした時のエラー
//------------------------------------------------------
function get_direct_access_error_html(){
	//ログイン画面に誘導
	header('Location: ../../../index.php');
	/*
	echo "<html>";
	echo "<head>";
	echo "<meta charset=\"utf-8\"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">";
	echo "<meta http-equiv=\"content-language\" content=\"ja\">";
	echo "<title>";
	echo "双方向通信管理システム";
	echo "</title>";
	echo "</head>";
	echo "<body>";
	echo "直接のアクセスは禁止されております。<br>";
	echo "Topページからお入りください。";
	echo "</body>";
	echo "</html>";
	*/
}

?>