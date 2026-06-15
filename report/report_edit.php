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
	$start_date_search = $_POST['start_date_search'];
	$end_date_search = $_POST['end_date_search'];
	$sort_order = $_POST['sort_order'];
	$filepass = "https://www.hisatomi-kk.com/app/upload_report/".$img_file_name_report;
	require_once("../login/function/function.php");
	require_once("../function/database.php");
	/** @var PDO $pdo */
}
//print_r($_POST);
try {
	$sql = "select * from report where serial_number_report =".$serial_number_report.";";
	$stmt = $pdo->query($sql);
		//$stmt = $pdo->query('SELECT * FROM question');
		if (!$stmt) {
			$info = $pdo->errorInfo();
			exit($info[2]);
		}
		//FETCH_ASSOC：カラム名をキーとする連想配列で取得する
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$row = $stmt->fetch();	//fetchAll：全件 //fetch:1件

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
	</head>
	<body>
		<div class="container-fluid">
			<div class="row" style="height: 70px"></div>
			<?php			
			//新規作成処理
			if ($_REQUEST['mode'] == 'update') {
				//教員編集登録
				$data = ['message_report' => $_POST['message']];
				// SQL の SET 部分を動的に生成
				$setClauses = [];
				$params = [];
				foreach ($data as $key => $val) {
					$setClauses[] = "\"{$key}\" = :{$key}";
					$params[$key] = $val;
				}

				$sql = "UPDATE report SET " . implode(", ", $setClauses) . " WHERE serial_number_report = :serial_number_report";
				$params['serial_number_report'] = $serial_number_report;

				$stmt = $pdo->prepare($sql);
				$result = $stmt->execute($params);

				if (!$result) {
					$info = $stmt->errorInfo();
					header('Content-Type: text/plain; charset=UTF-8');
					exit($info[2]);
				}
			}

			//新規作成後、編集後
			if (isset($_REQUEST['mode']) && ($_REQUEST['mode'] == 'update')) {
				echo "<div class=\"row\" style=\"height: 30px\"></div>\n";
				echo "<div class=\"row justify-content-center\">\n";
					echo "<div class=\"col-md-6 t_center\">\n";
					switch ($_REQUEST['mode']) {
						case "input":
							echo "<h3>以下を登録しました</h3><br>\n";
							break;
						case "update":
							echo "<h3>以下を更新しました</h3><br>\n";
							break;
					}
					echo "</div>\n";
				echo "</div>\n";
				echo "<div class=\"row justify-content-center\">\n";
					echo "<div class=\"col-md-6 t_center\">\n";
						echo "<form action=\"report_list.php\" method=POST>\n";
						echo "<input type=hidden name=mode value=list>\n";
						echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
						echo "<input type=hidden name=id_number_userid value=\"" . $id_number_userid . "\">\n";
						echo "<input type=hidden name=start_date_search value=\"" . $start_date_search . "\">\n";
						echo "<input type=hidden name=end_date_search value=\"" . $end_date_search . "\">\n";
						echo "<input type=hidden name=sort_order value=\"" . $sort_order . "\">\n";
						echo "<input type=hidden name=offset value=\"0\">\n";
						echo "<input type=hidden name=searchmode value=\"0\">\n";
						echo "<input type=submit value=\"一覧に戻る\"  class=\"listbackbtn\">\n";
						echo "</form>\n";
					echo "</div>\n";
				echo "</div>\n";
			}
			//新規作成
			else {
				?>
				<div class="row" style="height: 30px"></div>
				<div class="row justify-content-center">
					<div class="col-md-4 t_center">
						<h3>メッセージ登録</h3>
					</div>
				</div>
				<div class="row" style="height: 30px"></div>
				<div class="row justify-content-center">
				<a href="https://www.hisatomi-kk.com/app/upload_report/<?php echo $row['img_file_name_report']; ?>" target="_blank"><img src="https://www.hisatomi-kk.com/app/upload_report/<?php echo $row['img_file_name_report']; ?>" height="500px"></a>
				</div>
				
				<div class="row" style="height: 10px"></div>
					<form action=report_edit.php method=POST>
						<input type=hidden name="mode" value="update">
						<input type=hidden name="serial_number_report" value="<?php echo $serial_number_report; ?>">
						<input type=hidden name="img_file_name_report" value="<?php echo $img_file_name_report; ?>">
						<input type=hidden name="school_id_userid" value="<?php echo $school_id_userid; ?>">
						<input type=hidden name="id_number_userid" value="<?php echo $id_number_userid; ?>">
						<input type=hidden name="start_date_search" value="<?php echo $start_date_search; ?>">
						<input type=hidden name="end_date_search" value="<?php echo $end_date_search; ?>">
						<input type=hidden name="sort_order" value="<?php echo $sort_order; ?>">
						<div class="row justify-content-center">
							<?php
							if (empty($row['message_report'])) {
								echo "<input type=\"text\" size=\"50\" name=\"message\" placeholder=\"メッセージ\">";
							}
							else{
								echo "<input type=\"text\" size=\"50\" name=\"message\" value=\"".$row['message_report']."\">";
							}
							?>
						</div>
						<div class="row" style="height: 10px"></div>
						<div class="row justify-content-center">
							<input type=submit name="form1" value="登録" class="inputbtn" onSubmit="return input_check()">
						</div>
					</form>
				</div>
				<?php
			}
			?>
			<div style="height: 50px"></div>
			<div class="bottom_area"></div>
			<div style="height: 30px"></div>
		</div>
		<?php
		get_navi_html();
		?>
	</body>
	</html>
<?php
} catch (PDOException $e) {
	die('Connection failed: ' . $e->getMessage());
}finally {
	$pdo = null;
}
?>