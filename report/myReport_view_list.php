<?php
if (!isset($_REQUEST['mode'])) {
	require_once("../login/function/errorhtml.php");
	get_direct_access_error_html();
	exit;
} else {
	$school_id_userid = $_POST['school_id_userid'];	
	$offset = $_POST['offset'];
	$id_number_userid = $_POST['id_number_userid'];
	require_once("../login/function/function.php");
	require_once("../function/database.php");
}
try {
	//レポート抽出
	if ($_POST['mode'] == 'list') {
		$sql = "SELECT * FROM report 
						WHERE school_id_report = :school_id 
						AND gakunen_report = :nen 
						AND kumi_report = :kumi 
						AND seitono_report = :ban 
						AND pass_report = :pass";

			$stmt = $pdo->prepare($sql);
			$stmt->execute([
				':school_id' => $school_id_userid,
				':nen'       => $_POST['nen'],
				':kumi'      => $_POST['kumi'],
				':ban'       => $_POST['ban'],
				':pass'      => $_POST['pass']
			]);
			if (!$stmt) {
				$info = $pdo->errorInfo();
				exit($info[2]);
			}
			// FETCH_ASSOC：カラム名をキーとする連想配列で取得
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	}
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
		レポートデータ表示
	</title>
	<link href="../login/css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
	<link href="../login/css/logindesign.css" rel="stylesheet" type="text/css">
	<link href="../chat/css/design.css" rel="stylesheet" type="text/css">
</head>
<body>
	<div class="container-fluid">
		<div class="row" style="height: 100px"></div>
		<div class="row justify-content-center">
			<div class="col-md-4 t_center">
				<h3>レポートデータ</h3>
			</div>
		</div>
		<div class="row" style="height: 30px"></div>
		<?php
		//パスワード入力
		if ($_POST['mode'] == 'new') {		
			?>
			<div class="row justify-content-center">
				<div class="col-md t_center">
					<form action=myReport_view_list.php method=POST>
						<input type=hidden name="mode" value="list">
						<input type=hidden name="school_id_userid" value="<?php echo $school_id_userid; ?>">
						<input type=hidden name="id_number_userid" value="<?php echo $id_number_userid; ?>">
						<div class="col t_center edit">
							<table>
								<tr>
									<th>年</th>
									<th>組</th>
									<th>番号</th>
									<th>パスワード</th>
								</tr>
								<tr>
									<td><input type=text name="nen" size=5 required></td>
									<td><input type=text name="kumi" size=5 required></td>
									<td><input type=text name="ban" size=5 required></td>
									<td><input type=text name="pass" size=10 required></td>
								</tr>
							</table>
						</div>
						<div style="height: 30px"></div>
						<div class="row justify-content-center">
							<div class="col-md-6 t_center">
								<input type=submit name="form1" value="表示" class="inputbtn" onSubmit="return input_check()">
							</div>
						</div>
					</form>
				</div>
			</div>
			<?php
		}
		//レポート表示
		else{
			if (!empty($rows)) {
				echo "<div class=\"col-md-12 t_center list\">\n";
				echo "<table border=1>\n";
				echo "<tr>\n";
				echo "<th>学年</th>\n";
				echo "<th>組</th>\n";
				echo "<th>番</th>\n";
				echo "<th>名前</th>\n";
				echo "<th>タイトル</th>\n";
				echo "<th>レポート画像</th>\n";
				echo "<th>保存日時</th>\n";
				echo "<th>メッセージ</th>\n";
				echo "<th>メッセージ内容</th>\n";
				echo "</tr>\n";
				foreach ($rows as $val) {
					echo "<tr>\n";
					//学年
						echo "<td>" . $val['gakunen_report'] . "</td>\n";
					//組
						echo "<td>" . $val['kumi_report'] . "</td>\n";
					//番
						echo "<td>" . $val['seitono_report'] . "</td>\n";
					//名前
						echo "<td>" . $val['name_report'] . "</td>\n";
					//タイトル
						echo "<td>" . $val['title_report'] . "</td>\n";
					//画像
						//echo "<td>" . "<a href=\"chat/reportImg/".$val['img_file_name_report']."\" target=\"_blank\">表示</a>" . "</td>\n";
						echo "<td>" . "<a href=\"https://www.hisatomi-kk.com/app/upload_report/".$val['img_file_name_report']."\" target=\"_blank\"><img src=\"https://www.hisatomi-kk.com/app/upload_report/".$val['img_file_name_report']."\" height=\"100\"></a></td>\n";
					
					//保存日時
						echo "<td>" . date("y/m/d H:i:s", strtotime($val['created_time_report'])) . "</td>\n";
					//メッセージ
						echo "<td>\n";
							echo "<form action=\"myReport_view_detail.php\" method=POST>\n";
							echo "<input type=hidden name=mode value=list>\n";
							echo "<input type=hidden name=serial_number_report value=\"" . $val['serial_number_report'] . "\">\n";
							echo "<input type=hidden name=img_file_name_report value=\"" . $val['img_file_name_report'] . "\">\n";
							echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
							echo "<input type=hidden name=id_number_userid value=\"" . $id_number_userid . "\">\n";
							echo "<input type=submit value=\"メッセージ\" class=\"publicbtn\">\n";
							echo "</form>\n";
						echo "</td>\n";
					//メッセージ内容
						echo "<td align=\"left\">" . get_disp_text($val['message_report'], 20) . "</td>\n";
	
					echo "</tr>\n";
				}
				echo "</table>\n";
				echo "</div>\n";
			} else {
				//データがなかったらここで終了
				echo "レポートはありません。<br>前画面に戻りパスワード等正しく入力して下さい。";
			}
		}
		?>

		<div style="height: 50px"></div>

	</div>
	<?php
	get_navi_html();
	?>
</body>

</html>