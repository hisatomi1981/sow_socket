<?php
// ─── セッション管理（自動ログアウト）────────────────────────────
session_start();
define('SESSION_TIMEOUT', 10800); // 3時間（秒）

// ログイン済みセッションの有効期限チェック
if (isset($_SESSION['login_time'])) {
    if ((time() - $_SESSION['login_time']) > SESSION_TIMEOUT) {
        // 10時間超過 → セッション破棄してログイン画面へ
        session_unset();
        session_destroy();
        echo "<script>alert('ログインの有効期限が切れました。再度ログインしてください。');location.href='index.php';</script>";
        exit;
    }
    // アクティブな場合はタイムスタンプを更新しない（ログイン時刻固定）
}
// ─────────────────────────────────────────────────────────────────

require_once("login/function/function.php");
require_once("function/database.php");
/** @var PDO $pdo */
require_once("function/topBtn.php");
require_once("login/function/errorhtml.php");

try {
	if (isset($_POST['mode'])) {
		//認証の場合
		if ($_REQUEST['mode'] == 'certification') {
			$id_number_userid = $_POST['id_number_userid'];
			//ユーザ名を取得
			//$sql = "select * from userid where id_number_userid = '" . $_POST['id_number_userid'] . "' and school_id_userid = '". $_POST['school_id_userid'] ."';";
			$sql = "select * from userid where id_number_userid = '" . $id_number_userid . "' and active_userid='1';";
				$stmt = $pdo->query($sql);
				if (!$stmt) {
					$info = $pdo->errorInfo();
					header('Content-Type: text/plain; charset=UTF-8');
					exit($info[2]);
				}
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				$user_data = $stmt->fetch();	//fetchAll：全件 //fetch:1件
				//重複していないか
				$login_cnt = $stmt->rowCount();
			//個人なら１分間に5回以上ログインがあったら警告を出す
			$kubun_userid = $user_data['kubun_userid'] ?? null;
			if ($kubun_userid == "9"){
				//1分以内に5回のログインがあれば警告
				$sql = "SELECT COUNT(*) AS login_count
						FROM login_info
						WHERE login_id_number = :login_id
						AND login_time >= (NOW() AT TIME ZONE 'Asia/Tokyo') - INTERVAL '5 minute'";//Supabase
						//AND login_time >= NOW() - INTERVAL '5 minute'";//ローカル
					$stmt = $pdo->prepare($sql);
					if (!$stmt) {
						$info = $pdo->errorInfo();
						header('Content-Type: text/plain; charset=UTF-8');
						exit($info[2]);
					}

					$stmt->execute([
						':login_id' => $id_number_userid,
					]);

					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$logincnt = (int)($row['login_count'] ?? 0);
				if ($logincnt >= 5) {
					echo "<script>
							alert('一定時間にログインできる回数を超過しました。少しお時間をあけてログインしてください。');
							location.href = 'index.php';
						</script>";
					exit;
				}			
			}
			
			$kengen_userid = $user_data['kengen_userid'];
			$kubun_userid = $user_data['kubun_userid'];
			$school_id_userid = $user_data['school_id_userid'];
			$school_name_userid = $user_data['school_name_userid'];
		}	
		//メニューからでkengen_useridが違うのはiPadのためkengen_useridを+1する
		else if ($_REQUEST['mode'] == 'menu') {
			$id_number_userid = $_POST['id_number_userid'];
			$kengen_userid = $_POST['kengen_userid'];
			$kubun_userid = $_POST['kubun_userid'];
			$school_id_userid = $_POST['school_id_userid'];
			$school_name_userid = $_POST['school_name_userid'];
		}
	}
?>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="content-language" content="ja">
	<title>
		久富電機産業株式会社
	</title>
	<link href="login/css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
	<link href="login/css/idpass.css" rel="stylesheet" type="text/css">
	<link href="login/css/logindesign.css" rel="stylesheet" type="text/css">
	<link href="login/css/seihin.css" rel="stylesheet" type="text/css">
</head>
<body>
	<div class="container">
		<div class="row" style="height: 100px"></div>
		<?php
		//print_r($_POST);
		if (!isset($_REQUEST['mode'])) {
			get_idpass_form_html_user();
		} 
		else {
			//認証の場合
			if ($_REQUEST['mode'] == 'certification') {
				//idが違ったら
				if (empty($user_data)) {
					echo "<script>alert('ユーザ名が違います。');location.href = 'index.php';</script>";
					exit;
				}
				//ユーザidが複数あったら
				if ($login_cnt > 1) {
					echo "<script>alert('ユーザ名が重複しています。管理者にお問い合わせください');location.href = 'index.php';</script>";
					exit;
				}
				//パスワードが違ったら
				if ($user_data['pass_sha1_userid'] != sha1($_POST['pass_userid'])) {
					echo "<script>alert('パスワードが違います');location.href = 'index.php';</script>";
					exit;
				}
				//期限が過ぎてていたら
				if ((strtotime('now') - strtotime($user_data['kigen_userid'])) > 0) {					
					echo "<script>alert('有効期限が超過しています。管理者にお問い合わせください。');location.href = 'index.php';</script>";
					exit;
				}

				// 生徒で禁止時間内かどうか
				if ($kubun_userid == "1" && $user_data['ban_userid'] == "yes"){
					if ($user_data['ban_start_time_userid'] == $user_data['ban_end_time_userid']) {
						echo "<script>alert('アクセスは禁止されている為開けません');location.href = 'index.php';</script>";
						exit;
					}
					else{
						// 現在の時を取得（24時間制）
						$current_hour = (int)date('G');
						if (isTimeWithinBanPeriod($user_data['ban_start_time_userid'], $user_data['ban_end_time_userid'], $current_hour)) {
							echo "<script>alert('現在の時間はアクセス禁止時間です');location.href = 'index.php';</script>";
							exit;
						}
					}
				}
				//ログイン情報テーブルに登録
					date_default_timezone_set('Asia/Tokyo');
					$data = [];
					$data['login_id_number'] = $id_number_userid;
					$data['login_kubun']     = $kubun_userid;
					$data['school_name']     = $user_data['school_name_userid'];

					// ★ login_time をPHP側でセット（NOW()は使わない）
					$data['login_time']      = date("Y-m-d H:i:s");

					$columns = [];
					$placeholders = [];
					$params = [];

					// カラムとプレースホルダを生成
					foreach ($data as $key => $val) {
						$columns[] = "\"{$key}\"";
						$placeholders[] = ":{$key}";
						$params[":{$key}"] = $val;
					}

					// INSERT文作成
					$sql = "
						INSERT INTO login_info (" . implode(',', $columns) . ")
						VALUES (" . implode(',', $placeholders) . ")
					";

					$stmt = $pdo->prepare($sql);
					if (!$stmt) {
						$info = $pdo->errorInfo();
						header('Content-Type: text/plain; charset=UTF-8');
						exit($info[2]);
					}

					// 実行（失敗時はエラーを返すと運用が楽です）
					if (!$stmt->execute($params)) {
						$info = $stmt->errorInfo();
						header('Content-Type: text/plain; charset=UTF-8');
						exit($info[2]);
					}
				
				// ─── セッションにログイン時刻を記録 ───────────────────────
					$_SESSION['login_time'] = time();
					$_SESSION['login_id']   = $id_number_userid;
			}			
			else if ($_REQUEST['mode'] == 'menu') {
				
			}
			else{
				exit;
			}

			//認証が通ってUC、UD、AM、SOWならメニュー画面表示
			if ($_REQUEST['mode'] == 'certification' && 
				($kengen_userid == "1" || $kengen_userid == "3" || 
				$kengen_userid == "5" || $kengen_userid == "7" || $kengen_userid == "9" || $kengen_userid == "11" || 
				$kengen_userid == "31" || $kengen_userid == "33" || $kengen_userid == "35" || 
				$kengen_userid == "51" || $kengen_userid == "61" || $kengen_userid == "71" || $kengen_userid == "73")) {
				app_menu_header($kubun_userid, $kengen_userid);
				//個人かあ試しならAI機能はない表示
				if ($kubun_userid == "9" || $kubun_userid == "99") {
					disp_notAI();
				}
				app_menu($school_id_userid,$school_name_userid,$kubun_userid,$kengen_userid,$id_number_userid);
			}
			else{
				//管理者なら
				if ($kengen_userid == "0"){
					echo "<h4>双方向</h4>\n";
						echo "<div class=\"admin_menu_back_network\">\n";
							$sow_arr = array(
								array("No" => "1", "key" => "sow_ai", "folder" => "sow_ai_tm", "client" => "sow", "server" => "sow", "kataban" => "SOW-5 AI版"),
								array("No" => "2", "key" => "sow_ai", "folder" => "sow_ai_tm", "client" => "sow_ipad", "server" => "ipad", "kataban" => "SOW-5(iPad) AI版"),
								array("No" => "3", "key" => "sow", "folder" => "sow", "client" => "sow", "server" => "sow", "kataban" => "SOW-5"),
								array("No" => "4", "key" => "sow", "folder" => "sow", "client" => "sow_ipad", "server" => "ipad", "kataban" => "SOW-5(iPad)"),

								array("No" => "5", "key" => "sow_ai", "folder" => "sow_ai_tm", "client" => "uc", "server" => "ucd", "kataban" => "UC-7/8 AI版"),
								array("No" => "6", "key" => "sow_ai", "folder" => "sow_ai_tm", "client" => "uc_ipad", "server" => "ucd_ipad", "kataban" => "UC-7/8(iPad) AI版"),
								array("No" => "7", "key" => "sow", "folder" => "sow", "client" => "uc", "server" => "sow", "kataban" => "UC-7/8"),
								array("No" => "8", "key" => "sow", "folder" => "sow", "client" => "uc_ipad", "server" => "ipad", "kataban" => "UC-7/8(iPad)"),

								array("No" => "9", "key" => "sow_ai", "folder" => "sow_ai_tm", "client" => "ud", "server" => "ucd", "kataban" => "UD-1/2 AI版"),
								array("No" => "10", "key" => "sow_ai", "folder" => "sow_ai_tm", "client" => "ud_ipad", "server" => "ucd_ipad", "kataban" => "UD-1/2(iPad) AI版"),
								array("No" => "11", "key" => "sow", "folder" => "sow", "client" => "ud", "server" => "sow", "kataban" => "UD-1/2"),
								array("No" => "12", "key" => "sow", "folder" => "sow", "client" => "ud_ipad", "server" => "ipad", "kataban" => "UD-1/2(iPad)"),
							);

							$modelfolder ="";
							$client_filename ="";
							$server_filename ="";
							$modelKataban ="";
							foreach ($sow_arr as $item) {
								$modelfolder = $item["folder"];
								$client_filename = $item["client"];
								$server_filename = $item["server"];
								$modelKataban = $item["kataban"];
								echo "<h5>{$modelKataban}</h5>\n";
								?>
								<div class="row justify-content-center ">
									<div class="col-md-3 t_center">
										<form action="<?php echo SOW_URL.$modelfolder; ?>/chat/block_client_<?php echo $client_filename; ?>.php" target="_blank" method="POST">
											<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<button type="submit" class="btn-gradient-radius-client">
												クライアント（ブロック）
											</button>
										</form>
									</div>
									<div class="col-md-3 t_center">
										<form action="<?php echo SOW_URL.$modelfolder; ?>/chat/block_server_<?php echo $server_filename; ?>.php" target="_blank" method="POST">
											<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<button type="submit" class="btn-gradient-radius-server">
												サーバ（ブロック）
											</button>
										</form>
									</div>
									<div class="col-md-3 t_center">
										<form action="<?php echo SOW_URL.$modelfolder; ?>/chat/flow_client_<?php echo $client_filename; ?>.php" target="_blank" method="POST">
											<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<button type="submit" class="btn-gradient-radius-client">
												クライアント（フロー）
											</button>
										</form>
									</div>
									<div class="col-md-3 t_center">
										<form action="<?php echo SOW_URL.$modelfolder; ?>/chat/flow_server_<?php echo $server_filename; ?>.php" target="_blank" method="POST">
											<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<button type="submit" class="btn-gradient-radius-server">
												サーバ（フロー）
											</button>
										</form>
									</div>
								</div>
								<?php
							}				
						echo "</div>\n";
					echo "<div class=\"row\" style=\"height: 50px\"></div>\n";
					echo "<h4>計測制御</h4>\n";
						echo "<div class=\"admin_menu_back_network\">\n";
							$seigyo_arr = array(
								array("No" => "1", "key" => "uc7", "filename" => "index", "kataban" => "UC-7/8", "name" => "オーロラクロック2N"),
								array("No" => "2", "key" => "uc7", "filename" => "ipad", "kataban" => "UC-7/8(iPad)", "name" => "オーロラクロック2N"),
								
								array("No" => "5", "key" => "uc7_ai", "filename" => "index", "kataban" => "UC-7/8 AI版", "name" => "オーロラクロック2N"),
								array("No" => "6", "key" => "uc7_ai", "filename" => "ipad", "kataban" => "UC-7/8(iPad) AI版", "name" => "オーロラクロック2N"),

								array("No" => "3", "key" => "ud1", "filename" => "index", "kataban" => "UD-1/2", "name" => "オーロラクロック3"),
								array("No" => "4", "key" => "ud1", "filename" => "ipad", "kataban" => "UD-1/2(iPad)", "name" => "オーロラクロック3"),

								array("No" => "7", "key" => "ud1_ai", "filename" => "index", "kataban" => "UD-1/2 AI版 ver2", "name" => "オーロラクロック3"),
								array("No" => "8", "key" => "ud1_ai", "filename" => "ipad", "kataban" => "UD-1/2(iPad) AI版 ver2", "name" => "オーロラクロック3"),
								
								array("No" => "9", "key" => "ud1_ai_ver3", "filename" => "index", "kataban" => "UD-1/2 AI版 ver3", "name" => "オーロラクロック3"),
								array("No" => "10", "key" => "ud1_ai_ver3", "filename" => "ipad", "kataban" => "UD-1/2(iPad) AI版 ver3", "name" => "オーロラクロック3"),

								array("No" => "35", "key" => "am1", "filename" => "index", "kataban" => "AM-1/2", "name" => "オーロラミニライト"),
								array("No" => "36", "key" => "am1", "filename" => "ipad", "kataban" => "AM-1/2(iPad)", "name" => "オーロラミニライト"),

								array("No" => "37", "key" => "at2", "filename" => "index", "kataban" => "AT-2", "name" => "オーロラトーチ2"),
								array("No" => "38", "key" => "hr1", "filename" => "index", "kataban" => "HR-1", "name" => "アクティくん"),
								array("No" => "39", "key" => "lc12", "filename" => "index", "kataban" => "LC-12", "name" => "オーロラスタンド"),

								array("No" => "11", "key" => "uc9", "filename" => "index", "kataban" => "UC-9/10", "name" => "オーロラクキュート"),
								array("No" => "12", "key" => "uc9", "filename" => "ipad", "kataban" => "UC-9/10(iPad)", "name" => "オーロラクキュート"),
								
							);

							$modelkey ="";
							$modelKataban ="";
							$modelName ="";

							// 行番号カウンタ
							$rowNo = 1;

							foreach ($seigyo_arr as $item) {
								$model_name = $item["key"];
								$filename = $item["filename"];
								$modelKataban = $item["kataban"];
								$modelName =$item["name"];
								echo "<h5>{$modelKataban}</h5>\n";
								?>
								<div class="row justify-content-center ">
									<?php
									//アイコン　UC UD
									if ($rowNo == 5 || $rowNo == 6 || $rowNo == 7 || $rowNo == 8 || $rowNo == 9 || $rowNo == 10){
										?>
										<div class="col-md-2 t_center">
											<form action="<?php echo SEIGYO_URL; ?>app/<?php //echo $model_name; ?>ud1_ai_ver3/icon/<?php echo $filename; ?>.php" target="_blank" method="POST">
												<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
												<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
												<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
												<button type="submit" class="btn-gradient-radius btn-gradient-radius-icon">
													アイコン
												</button>
											</form>
										</div>
										<?php
									}
									//UC
									else if ($rowNo == 1 || $rowNo == 2 || $rowNo == 3 || $rowNo == 4){
										?>
										<div class="col-md-2 t_center">
											<form action="<?php echo SEIGYO_URL; ?>app/<?php //echo $model_name; ?>uc7_ai/icon/<?php echo $filename; ?>.php" target="_blank" method="POST">
												<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
												<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
												<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
												<button type="submit" class="btn-gradient-radius btn-gradient-radius-icon">
													アイコン
												</button>
											</form>
										</div>
										<?php
									}
									else{
										?>
										<div class="col-md-2 t_center">
											
										</div>
										<?php
									}
									?>
									<div class="col-md-2 t_center">
										<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $model_name; ?>/block/<?php echo $filename; ?>.php" target="_blank" method="POST">
											<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<button type="submit" class="btn-gradient-radius btn-gradient-radius-block">									
												ブロック
											</button>
										</form>
									</div>
									<div class="col-md-2 t_center">
										<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $model_name; ?>/flow/<?php echo $filename; ?>.php" target="_blank" method="POST">
											<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
											<button type="submit" class="btn-gradient-radius btn-gradient-radius-flow">
												フローチャート
											</button>
										</form>
									</div>
									<?php
									if ($rowNo <= 12 || $rowNo == 16 || $rowNo == 17){
										?>
										<div class="col-md-2 t_center">
											<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $model_name; ?>/word/<?php echo $filename; ?>.php" target="_blank" method="POST">
												<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
												<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
												<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
												<button type="submit" class="btn-gradient-radius btn-gradient-radius-word">
													文字
												</button>
											</form>
										</div>
										<?php
									}
									else{
										?>
										<div class="col-md-2 t_center">
											
										</div>
										<?php
									}
									if ($rowNo == 1 || $rowNo == 2 || $rowNo == 3 || $rowNo == 4 || $rowNo == 5 || 
										$rowNo == 6 || $rowNo == 7 || $rowNo == 8 || $rowNo == 9 || $rowNo == 10 || 
										$rowNo == 11 || $rowNo == 12 || $rowNo == 16 || $rowNo == 17){
										$melody_folder = "";
										//オーロラクロック
										if ($rowNo == 1 || $rowNo == 2 || $rowNo == 3 || $rowNo == 4 || $rowNo == 16 || $rowNo == 17){
											$melody_folder = "sounduc";
										}
										else{
											$melody_folder = "soundud";
										}
										?>
										<div class="col-md-2 t_center">
											<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $melody_folder; ?>/block/<?php echo $filename; ?>.php" target="_blank" method="POST">
												<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
												<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
												<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
												<button type="submit" class="btn-gradient-radius btn-gradient-radius-melody">
													メロディ
												</button>
											</form>
										</div>
										<?php
									}
									else{
										?>
										<div class="col-md-2 t_center">
											
										</div>
										<?php
									}
									?>
								</div>
								<?php						
								$rowNo++;
							}
						echo "</div>\n";
						sensor_data($kengen_userid,$school_id_userid, $school_name_userid);
				}
				//SOWなら
				else if ($kengen_userid == "51" || $kengen_userid == "52" || 
					$kengen_userid == "61" || $kengen_userid == "62") {
					sow_header($kengen_userid);
					aurora_sow_network($kengen_userid, $school_id_userid, $school_name_userid);
				}
				//UCタイプなら(1:計測制御 + 双方向   31:計測制御だけ)
				else if ($kengen_userid == "1" || $kengen_userid == "5" || $kengen_userid == "11" || $kengen_userid == "31" || $kengen_userid == "71") {
					app_header($kubun_userid, $kengen_userid);					
					uc_ud_seigyo($kengen_userid,$school_id_userid, $school_name_userid);
					time_uc("win");
					//UC-9、個人、お試し以外なら
					if ($kengen_userid != "11" && $kubun_userid != "9" && $kubun_userid != "99") {
						sensor_data($kengen_userid,$school_id_userid, $school_name_userid);
					}
					//個人以外は双方向
					if ($kubun_userid != "9" && $kubun_userid != "99") {
						//UC用のSOW
						if ($kengen_userid == "1" || $kengen_userid == "5" || $kengen_userid == "11"){
							aurora_sow_network($kengen_userid, $school_id_userid, $school_name_userid);
						}
					}
				}
				//UC(iPad)タイプなら
				else if ($kengen_userid == "2" || $kengen_userid == "6" || $kengen_userid == "12" || $kengen_userid == "32" || $kengen_userid == "72") {
					app_header($kubun_userid, $kengen_userid);
					uc_ud_seigyo($kengen_userid,$school_id_userid, $school_name_userid);
					time_uc("ipad");
					//個人以外は双方向
					if ($kubun_userid != "9" && $kubun_userid != "99") {
						if ($kengen_userid == "2" || $kengen_userid == "6" || $kengen_userid == "12"){
							aurora_sow_network($kengen_userid, $school_id_userid, $school_name_userid);
						}
					}
				}
				//UDタイプなら
				else if ($kengen_userid == "3" || $kengen_userid == "7" || $kengen_userid == "9" || $kengen_userid == "33" || $kengen_userid == "73") {
					app_header($kubun_userid, $kengen_userid);
					uc_ud_seigyo($kengen_userid,$school_id_userid, $school_name_userid);
					time_ud("win");
					//個人、お試し以外なら
					if ($kubun_userid <> "9" && $kubun_userid <> "99") {
						sensor_data($kengen_userid,$school_id_userid, $school_name_userid);
					}					
					//個人以外は双方向
					if ($kubun_userid != "9" && $kubun_userid != "99") {
						//UD用のSOW
						if ($kengen_userid == "3" || $kengen_userid == "7" || $kengen_userid == "9"){
							aurora_sow_network($kengen_userid, $school_id_userid, $school_name_userid);
						}
					}
				}
				//UD(iPad)タイプなら
				else if ($kengen_userid == "4" || $kengen_userid == "8" || $kengen_userid == "10" || $kengen_userid == "34" || $kengen_userid == "74") {
					app_header($kubun_userid, $kengen_userid);
					uc_ud_seigyo($kengen_userid,$school_id_userid, $school_name_userid);
					time_ud("ipad");
					//個人以外は双方向
					if ($kubun_userid != "9" && $kubun_userid != "99") {
						if ($kengen_userid == "4" || $kengen_userid == "8" || $kengen_userid == "10"){
							aurora_sow_network($kengen_userid, $school_id_userid, $school_name_userid);
						}
					}
				}
				//AMタイプなら
				else if ($kengen_userid == "35" || $kengen_userid == "40") {
					app_header($kubun_userid, $kengen_userid);
					etc_seigyo($kengen_userid,$school_id_userid, $school_name_userid);
					if ($kengen_userid == "40"){
						aurora_sow_network($kengen_userid, $school_id_userid, $school_name_userid);
					}
				}
				//AM(iPad)タイプなら
				else if ($kengen_userid == "36" || $kengen_userid == "41") {
					app_header($kubun_userid, $kengen_userid);
					etc_seigyo($kengen_userid,$school_id_userid, $school_name_userid);
					if ($kengen_userid == "41"){
						aurora_sow_network($kengen_userid, $school_id_userid, $school_name_userid);
					}
				}
				//ATタイプなら
				else if ($kengen_userid == "37" || $kengen_userid == "42") {
					app_header($kubun_userid, $kengen_userid);
					etc_seigyo($kengen_userid,$school_id_userid, $school_name_userid);
					if ($kengen_userid == "42"){
						aurora_sow_network($kengen_userid, $school_id_userid, $school_name_userid);
					}
				}
				//HRタイプなら
				else if ($kengen_userid == "38" || $kengen_userid == "43") {
					app_header($kubun_userid, $kengen_userid);
					etc_seigyo($kengen_userid,$school_id_userid, $school_name_userid);
					if ($kengen_userid == "43"){
						aurora_sow_network($kengen_userid, $school_id_userid, $school_name_userid);
					}
				}
				//LCタイプなら
				else if ($kengen_userid == "39" || $kengen_userid == "44") {
					app_header($kubun_userid, $kengen_userid);
					etc_seigyo($kengen_userid,$school_id_userid, $school_name_userid);
					if ($kengen_userid == "44"){
						aurora_sow_network($kengen_userid, $school_id_userid, $school_name_userid);
					}
				}

				//レポート閲覧
				if ($kubun_userid != "0" && $kengen_userid != "11"){
					report_list($school_id_userid, $school_name_userid, $id_number_userid);
				}

				//管理システム(UC UD、SOWは表示) チャットデータなど
				if ($kengen_userid == "0" || 
					$kengen_userid == "1" || $kengen_userid == "2" || $kengen_userid == "3" || $kengen_userid == "4" || 
					$kengen_userid == "5" || $kengen_userid == "6" || $kengen_userid == "7" || $kengen_userid == "8" || 
					$kengen_userid == "9" || $kengen_userid == "10" ||
					$kengen_userid == "31" || $kengen_userid == "32" || $kengen_userid == "33" || $kengen_userid == "34" || 
					$kengen_userid == "71" || $kengen_userid == "72" || $kengen_userid == "73" || $kengen_userid == "74" || 
					$kengen_userid == "51" || $kengen_userid == "52" ||	$kengen_userid == "61" || $kengen_userid == "62") {
					//先生ならユーザ情報など表示
					if ($kubun_userid == "0") {
						teacher_btn($school_id_userid, $school_name_userid, $id_number_userid);
					}
				}
			}
		}
		?>
	</div>
	<?php
	// ─── セッション残り時間をJSに渡す ────────────────────────────
	if (isset($_SESSION['login_time'])) {
		$elapsed  = time() - $_SESSION['login_time'];
		$remaining = SESSION_TIMEOUT - $elapsed;
		if ($remaining < 0) $remaining = 0;
		$login_time = $_SESSION['login_time'];
		echo "<script>
			(function() {
				var remaining = {$remaining}; // 残り秒数
				var warned10 = (remaining <= 600);
				var timer = setInterval(function() {
					remaining--;
					if (remaining <= 0) {
						clearInterval(timer);
						alert('ログインの有効期限が切れました。再度ログインしてください。');
						location.href = 'index.php';
						return;
					}
					if (!warned10 && remaining <= 600) {
						warned10 = true;
						alert('ログインから有効期限まで残り10分です。');
					}
				}, 1000);
				// ─── アプリページへのPOSTフォームにlogin_timeを全件追加 ───
				var loginTime = {$login_time};
				document.querySelectorAll('form[method=\"POST\"]').forEach(function(form) {
					var input = document.createElement('input');
					input.type  = 'hidden';
					input.name  = 'login_time';
					input.value = loginTime;
					form.appendChild(input);
				});
			})();
		</script>";
	}
	
} catch (PDOException $e) {
	die('Connection failed: ' . $e->getMessage());
}finally {
	$pdo = null;
}
	get_navi_html();
	?>
</body>
</html>