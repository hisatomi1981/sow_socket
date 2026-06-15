<?php
if (!isset($_REQUEST['mode'])) {
	require_once("../login/function/errorhtml.php");
	get_direct_access_error_html();
	exit;
} else {
	$serial_number_report = $_POST['serial_number_report'];
	$img_file_name_report = $_POST['img_file_name_report'];
	$school_id_userid = $_POST['school_id_userid'];
	$id_number_userid = $_POST['id_number_userid'];
	$filepass = "https://www.hisatomi-kk.com/app/upload_report/".$img_file_name_report;
	require_once("../login/function/function.php");
	require_once("../function/database.php");
}
//print_r($_POST);
try {
	$sql = "SELECT * FROM report WHERE serial_number_report = :serial";
		$stmt = $pdo->prepare($sql); // プリペアドステートメントを使ってSQLを準備
		//$stmt->bindParam(':serial', $serial_number_report, PDO::PARAM_INT); // パラメータを参照しINTで処理バインド（整数として）
		$stmt->bindValue(':serial', (int)$serial_number_report, PDO::PARAM_INT);//bindValue() は「値そのもの」を渡します（参照ではなく）
		$stmt->execute(); // SQLを実行

		$stmt->setFetchMode(PDO::FETCH_ASSOC); // 結果を連想配列で取得
		$row = $stmt->fetch(); // 1件だけ取得

} catch (PDOException $e) {
	die('Connection failed: ' . $e->getMessage());
}finally {
	$pdo = null;
}
?>
	<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="content-language" content="ja">
		<title>
			レポート
		</title>
		<link href="../login/css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
		<link href="../login/css/logindesign.css" rel="stylesheet" type="text/css">
		<link href="../chat/css/design.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="container-fluid">
			<div class="row" style="height: 70px"></div>
				<div class="row justify-content-center">
					<div class="col-md-4 t_center">
						<h3>レポート</h3>
					</div>
				</div>
				<div class="row" style="height: 30px"></div>
				<div class="row justify-content-center">
					<img src="https://www.hisatomi-kk.com/app/upload_report/<?php echo $row['img_file_name_report']; ?>" height="500px">
				</div>
				
				<div class="row" style="height: 10px"></div>
				<div class="row justify-content-center">
					<h3>メッセージ</h3>
				</div>				
				<div class="row" style="height: 10px"></div>				
				<div class="row justify-content-center">
					<div class="col-md-6">
						<?php echo $row['message_report']; ?>
					</div>
				</div>
			<div style="height: 30px"></div>
			
			<div class="row justify-content-center">
				<button class="publicbtn" onclick="window.history.back()">一覧に戻る</button>
			</div>
			<div style="height: 50px"></div>
			<div class="bottom_area"></div>
			<div style="height: 30px"></div>
		</div>
		<?php
		get_navi_html();
		?>
	</body>
	</html>