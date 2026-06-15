<?php
require_once("function/function.php");
require_once("../function/database.php");
/** @var PDO $pdo */
try {
	//認証の場合
	if ($_REQUEST['mode'] == 'certification') {
		//print_r($_POST);
		//ユーザ名をチェック
		$sql = "select * from userid_kanri where id_number_kanri = '" . $_POST['id_number_kanri'] . "';";
		$stmt = $pdo->query($sql);
		if (!$stmt) {
			$info = $pdo->errorInfo();
			header('Content-Type: text/plain; charset=UTF-8');
			exit($info[2]);
		}
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$user_kanri_data = $stmt->fetch();	//fetchAll：全件 //fetch:1件
		//重複していないか
		$login_cnt = $stmt->rowCount();
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
		双方向通信ID登録
	</title>
	<link href="css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
	<link href="css/logindesign.css" rel="stylesheet" type="text/css">
</head>
<body>
	<div class="container-fluid">
		<div class="row" style="height: 100px"></div>
		<div class="row justify-content-center">
			<h3>双方向通信ID登録</h3>
		</div>
		<?php
		if (!isset($_REQUEST['mode'])) {
			get_idpass_form_html_administrator();
		} else {
			//認証の場合
			if ($_REQUEST['mode'] == 'certification') {
				//idが違ったら
				if (empty($user_kanri_data)) {
					echo "<div class=\"row\" style=\"height: 50px\"></div>";
					echo "<h3>ユーザ名が違います。</h3>";
					//id passの入力フォーム
					get_idpass_form_html_administrator();
					echo "</div>";
					echo "</body>";
					echo "</html>";
					exit;
				}

				//ユーザidが複数あったら
				if ($login_cnt > 1) {
					echo "<div class=\"row\" style=\"height: 50px\"></div>";
					echo "<h3>ユーザ名が重複しています。管理者にお問い合わせください</h3>";
					//id passの入力フォーム
					get_idpass_form_html_administrator();
					echo "</div>";
					echo "</body>";
					echo "</html>";
					exit;
				}
				
				//パスワードが違ったら
				if ($user_kanri_data['pass_sha1_kanri'] != sha1($_POST['pass_kanri'])) {
					echo "<div class=\"row\" style=\"height: 50px\"></div>";
					echo "<h3>パスワードが違います。<br>パスワードを忘れた場合は管理者にお問い合わせください。</h3>";
					//id passの入力フォーム
					get_idpass_form_html_administrator();
					echo "</div>";
					echo "</body>";
					echo "</html>";
					exit;
				}

				echo "<div class=\"row\" style=\"height: 30px\"></div>\n";
				echo "<div class=\"row justify-content-center\">\n";
					echo "<form action=\"user/user_list.php\" method=POST>\n";
						echo "<input type=hidden name=mode value=list>\n";
						echo "<input type=hidden name=id_number_kanri value=\"" . $_POST['id_number_kanri'] . "\">\n";
						echo "<input type=hidden name=serial_number_kanri value=\"" . $user_kanri_data['serial_number_kanri'] . "\">\n";
						echo "<input type=hidden name=searchmode value=\"0\">\n";
						echo "<input type=hidden name=refine_search value=\"0\">\n";
						echo "<input type=hidden name=model_search value=\"0\">\n";
						echo "<input type=hidden name=offset value=0>\n";
						echo "<input type=submit value=\"ID登録、一覧表\"  class=\"inputbtn\">\n";
					echo "</form>\n";
				echo "</div>\n";

				echo "<div class=\"row\" style=\"height: 30px\"></div>\n";
				echo "<div class=\"row justify-content-center\">\n";
					echo "<form action=\"login_list.php\" method=POST>\n";
						echo "<input type=hidden name=mode value=list>\n";
						echo "<input type=hidden name=id_number_kanri value=\"" . $_POST['id_number_kanri'] . "\">\n";
						echo "<input type=hidden name=serial_number_kanri value=\"" . $user_kanri_data['serial_number_kanri'] . "\">\n";
						echo "<input type=hidden name=searchmode value=\"0\">\n";
						echo "<input type=hidden name=refine_search value=\"0\">\n";
						echo "<input type=hidden name=model_search value=\"0\">\n";
						echo "<input type=hidden name=offset value=0>\n";
						echo "<input type=submit value=\"ログイン情報\"  class=\"inputbtn\">\n";
					echo "</form>\n";
				echo "</div>\n";
			}
		}
		?>
	</div>
	<?php
	get_navi_html();
	?>
</body>

</html>