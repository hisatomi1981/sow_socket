<?php
if (!isset($_REQUEST['mode'])) {
	require_once("login/function/errorhtml.php");
	get_direct_access_error_html();
	exit;
} else {
	$school_id_userid = $_POST['school_id_userid'];
	$id_number_userid = $_POST['id_number_userid'];
	require_once("login/function/function.php");
	require_once("function/database.php");
}
//print_r($_POST);
//print_r("<br><br>");
try {
	//変更であればデータベース更新
	if(isset($_POST['mode']) && $_POST['mode'] == "load") {
		if(isset($_POST['ban'])){
			$data['ban_userid'] = $_POST['ban'];
			$data['ban_start_time_userid'] = $_POST['ban_start_time'];
			$data['ban_end_time_userid'] = $_POST['ban_end_time'];
		}
		else{
			$data['ban_userid'] = "no";
			$data['ban_start_time_userid'] = "";
			$data['ban_end_time_userid'] = "";
		}
		

		//教員のデータベース更新
		$sql = "";
			foreach($data as $key=>$val) {
				if($sql == "") {
					$sql = "update userid set ";
				}else{
					$sql .= ",";
				}
				$sql .= "\"".$key."\" = '".$val."'";
			}
			
			$sql .= " where school_id_userid='".$school_id_userid."' and kubun_userid='0';";
				//クエリ実行
				$stmt = $pdo->query($sql);
				if (!$stmt) {
					$info = $pdo->errorInfo();
					header('Content-Type: text/plain; charset=UTF-8');
					exit($info[2]);
				}
		//生徒のデータベース更新
		$sql = "";
			foreach($data as $key=>$val) {
				if($sql == "") {
					$sql = "update userid set ";
				}else{
					$sql .= ",";
				}
				$sql .= "\"".$key."\" = '".$val."'";
			}
			
			$sql .= " where school_id_userid='".$school_id_userid."' and kubun_userid='1';";
				//クエリ実行
				$stmt = $pdo->query($sql);
				if (!$stmt) {
					$info = $pdo->errorInfo();
					header('Content-Type: text/plain; charset=UTF-8');
					exit($info[2]);
				}
	}

	$sqlst = "select * from userid where school_id_userid = '" . $school_id_userid . "';";
		$stmt = $pdo->query($sqlst);
		//$stmt = $pdo->query('SELECT * FROM question');
		if (!$stmt) {
			$info = $pdo->errorInfo();
			exit($info[2]);
		}
		//FETCH_ASSOC：カラム名をキーとする連想配列で取得する
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$userinfo = $stmt->fetch();	//fetchAll：全件 //fetch:1件
		
	//print_r($userinfo);
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
		双方向通信管理システム(ユーザ情報)
	</title>
	<link href="login/css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
	<link href="login/css/logindesign.css" rel="stylesheet" type="text/css">
</head>
<body>
	<div class="container-fluid">
		<div class="row" style="height: 100px"></div>
		<div class="row" style="height: 30px"></div>
		<div class="row justify-content-center">
			<div class="col-md-4 t_center">
				<h3>ユーザ情報</h3>
			</div>
		</div>
		<div class="row" style="height: 30px"></div>
		<?php
		if(isset($_POST['mode'])) {
			?>
			<div class="row justify-content-center">
			<div class="col-md-4 t_center">
				<h5>更新しました</h5>
			</div>
			</div>
			<div class="row" style="height: 30px"></div>
			<?php
		}
		echo "<div class=\"row justify-content-center t_center\">\n";
		echo "<div class=\"col-md-12 t_center list\">\n";
		echo "<table border=1>\n";
			echo "<tr>\n";
			echo "<th>学校ID</th>\n";			
			echo "<td>".$userinfo['school_id_userid']."</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<th>学校名</th>\n";			
			echo "<td>".$userinfo['school_name_userid']."</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<th>アプリ使用期限</th>\n";
			$date = new DateTime($userinfo['kigen_userid']);			
			echo "<td>".$date->format('Y年m月d日')."</td>\n";
			echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";
		echo "</div>\n";

		?>

		<div class="row" style="height: 30px"></div>
		
		<div class="row justify-content-center">
			<div class="col t_center">
				アプリの使用を禁止する時間を設定
			</div>
		</div>
		<div class="row justify-content-center">
			<form action="licence.php" method="post">
			<input type=hidden name=mode value="load">
			<input type=hidden name=school_id_userid value='<?php echo $school_id_userid ?>'>
			<input type=hidden name=offset value=0>
			<div class="col-md-12 waku_black">
				アプリの使用を禁止する時間を設定する事が出来ます。<br>
				①設定する場合は「アプリ使用禁止を設定する」にチェックする<br>
				②禁止する開始時間と終了時間を設定する<br>
				（開始時間と終了時間を同時刻に設定するとすべての時間において使用不可になります）<br>
				③「設定」ボタンを押してください。
				<div class="row" style="height: 5px"></div>
				<hr size=”2”>
				<div class="row" style="height: 5px"></div>
				<label>
					<input type="checkbox" name="ban" value="yes" <?php if ($userinfo['ban_userid'] == "yes"){echo "checked=\"checked\"";} ?>> アプリ使用禁止を設定する
				</label>
				<div class="row justify-content-center">
					<div class="t_center">
						<label>禁止する時間：</label>
						<select name="ban_start_time" id="banstarttime">
							<?php 
							if ($userinfo['ban_userid'] == "no"){
								for ($i = 0; $i < 24; $i++) {
									echo "<option value=\"".str_pad($i, 2, '0', STR_PAD_LEFT)."\">".str_pad($i, 2, '0', STR_PAD_LEFT).":00</option>";
								}
							}
							else{
								for ($i = 0; $i < 24; $i++) {
									if (intval($userinfo['ban_start_time_userid'] == intval($i))){
										echo "<option value=\"".str_pad($i, 2, '0', STR_PAD_LEFT)."\" selected>".str_pad($i, 2, '0', STR_PAD_LEFT).":00</option>";
									}
									else{
										echo "<option value=\"".str_pad($i, 2, '0', STR_PAD_LEFT)."\">".str_pad($i, 2, '0', STR_PAD_LEFT).":00</option>";
									}
								}
							}
							?>
						</select>
						<label>時から</label>
						<select name="ban_end_time" id="banendtime">
							<?php 
							if ($userinfo['ban_userid'] == "no"){
								for ($i = 0; $i < 24; $i++) {
									echo "<option value=\"".str_pad($i, 2, '0', STR_PAD_LEFT)."\">".str_pad($i, 2, '0', STR_PAD_LEFT).":00</option>";
								}
							}
							else{
								for ($i = 0; $i < 24; $i++) {
									if (intval($userinfo['ban_end_time_userid'] == intval($i))){
										echo "<option value=\"".str_pad($i, 2, '0', STR_PAD_LEFT)."\" selected>".str_pad($i, 2, '0', STR_PAD_LEFT).":00</option>";
									}
									else{
										echo "<option value=\"".str_pad($i, 2, '0', STR_PAD_LEFT)."\">".str_pad($i, 2, '0', STR_PAD_LEFT).":00</option>";
									}
								}
							}
							?>
						</select>
						<label>時までアプリの使用を禁止する</label>
					</div>
				</div>
				<div class="row justify-content-center">
					<div class="t_center">
						<input type="submit" value="設定">
					</div>
				</div>
			</div>
			</form>
		</div>
		
		<div style="height: 50px"></div>
		<div class="row justify-content-center">
			<button onclick="window.history.back()">トップページに戻る</button>
		</div>
		<div style="height: 50px"></div>

	</div>
	<?php
	get_navi_html();
	?>
</body>

</html>