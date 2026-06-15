<?php
if (!isset($_REQUEST['mode'])) {
	require_once("../function/errorhtml.php");
	get_direct_access_error_html();
	exit;
} else {
	$id_number_kanri = $_POST['id_number_kanri'];
	$serial_number_kanri = $_POST['serial_number_kanri'];
	require_once("../function/function.php");
	require_once("../../function/database.php");
	/** @var PDO $pdo */
}
try {
	function get_password_php($length = 5) {
		$characters = '0123456789';
		$password = '';
		for ($i = 0; $i < $length; $i++) {
			$password .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $password;
	}
?>
	<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="content-language" content="ja">
		<title>
			ID管理
		</title>
		<link href="../css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
		<link href="../css/logindesign.css" rel="stylesheet" type="text/css">
		<script src="../js/dialog.js"></script>
		<script src="../js/function.js"></script>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row" style="height: 70px"></div>
			<?php			
			//新規作成処理
			if ($_REQUEST['mode'] == 'input') {
				//学校コードがすでにあるかどうか
					$school_code = $_POST['school_id_userid'] ?? '';

					// 既存コード取得
					$sql = "
						SELECT school_id_userid
						FROM userid
						WHERE school_id_userid LIKE :school_id_userid
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindValue(':school_id_userid', $school_code . '%', PDO::PARAM_STR);
					$stmt->execute();
					$existing_codes = $stmt->fetchAll(PDO::FETCH_COLUMN);

					// 完全一致が存在する場合のみ連番付与
					if (in_array($school_code, $existing_codes, true)) {
							echo "<div class=\"row\" style=\"height: 50px\"></div>";
							echo "<h3>この学校コードはすでに登録済です。別のIDにしてください</h3>";
							echo "</div>";
							echo "</body>";
							echo "</html>";
							$pdo = null;
							exit;
					}
				
				//教員IDと生徒IDが同一なら
					if ($_POST['id_number_userid_teacher'] ==$_POST['id_number_userid_student']){
						echo "<div class=\"row\" style=\"height: 50px\"></div>";
						echo "<h3>教員IDと生徒IDが同一です。別のIDにしてください</h3>";
						echo "</div>";
						echo "</body>";
						echo "</html>";
						$pdo = null;
						exit;
					}
				//以前に登録があれば
					$sql = "select * from userid where id_number_userid = '" . $_POST['id_number_userid_teacher'] . "' and school_id_userid = '". $_POST['school_id_userid'] ."';";
						$stmt = $pdo->query($sql);
						if (!$stmt) {
							$info = $pdo->errorInfo();
							header('Content-Type: text/plain; charset=UTF-8');
							exit($info[2]);
						}
						$stmt->setFetchMode(PDO::FETCH_ASSOC);			
						//重複していないか
						$login_cnt = $stmt->rowCount();

						//すでに登録があったら
						if ($login_cnt > 0) {
							echo "<div class=\"row\" style=\"height: 50px\"></div>";
							echo "<h3>このIDはすでに登録されています。</h3>";
							echo "</div>";
							echo "</body>";
							echo "</html>";
							$pdo = null;
							exit;
						}

				//教員登録
					$data['source_number_userid'] = "";
					$data['school_id_userid'] = $_POST['school_id_userid'];
					$data['id_number_userid'] = $_POST['id_number_userid_teacher'];
					$data['pass_userid'] = $_POST['pass_userid_teacher'];
					$data['pass_sha1_userid'] = sha1($_POST['pass_userid_teacher']);
					$data['agency_userid'] = $_POST['agency_userid'];
					$data['name_userid'] = "";
					$data['school_name_userid'] = $_POST['school_name_userid'];					
					//個人なら 0：教師 1：生徒 9:個人
					$data['kubun_userid'] = "0";
					$data['active_userid'] = "1"; //使用可は1 削除したら0
					$data['kengen_userid'] = $_POST['kengen_userid'];//0:全部 1：SOW 2：SOW(iPad) 3：UC 4:UC(iPad) 5：UD 6:UD(iPad)
					$data['ban_userid'] = "no";//禁止時間設けない:yes 設ける:no
					$data['ban_start_time_userid'] = "";
					$data['ban_end_time_userid'] = "";
					$data['created_time_userid'] = date("Ymd");
					$data['kigen_userid'] = $_POST['kigen_userid'];
					$data['created_userid'] = $serial_number_kanri;

					$colmn = "";
					$values = "";

					foreach ($data as $key => $val) {
						if ($colmn <> "") {
							$colmn .= ",";
						}
						if ($values <> "") {
							$values .= ",";
						}
						$colmn .= "\"" . $key . "\""; //ダブルクォーテーション
						$values .= "'" . $val . "'"; //シングルクォーテーション
					}

					$sql = "insert into userid ($colmn) values ($values);";
					//クエリ実行
					$stmt = $pdo->query($sql);
					if (!$stmt) {
						$info = $pdo->errorInfo();
						header('Content-Type: text/plain; charset=UTF-8');
						exit($info[2]);
					}

					//最後に取得した番号（シリアル）
					$sql = "select currval('userid_serial_number_userid_seq'); ";
					$stmt = $pdo->query($sql);
					if (!$stmt) {
						$info = $pdo->errorInfo();
						header('Content-Type: text/plain; charset=UTF-8');
						exit($info[2]);
					}
					$stmt->setFetchMode(PDO::FETCH_ASSOC);
					$rows = $stmt->fetch();
					$lastserino =  $rows['currval'];

					//教員用のsource_number_useridをシリアルに変更
					$data_t['source_number_userid'] = $lastserino;
					$sql = "";
					foreach ($data_t as $key => $val) {
						if ($sql == "") {
							$sql = "update userid set ";
						} else {
							$sql .= ",";
						}
						$sql .= "\"" . $key . "\" = '" . $val . "'";
					}

					$sql .= " where serial_number_userid=" . $lastserino . ";";
					//クエリ実行
					$stmt = $pdo->query($sql);
					if (!$stmt) {
						$info = $pdo->errorInfo();
						header('Content-Type: text/plain; charset=UTF-8');
						exit($info[2]);
					}

				//生徒編集登録（個人、お試し版でないなら）
					if ($_POST['kubun_userid'] !="9" && $_POST['kubun_userid'] !="99"){
						$data_s['source_number_userid'] = $lastserino;
						$data_s['school_id_userid'] = $_POST['school_id_userid'];
						$data_s['id_number_userid'] = $_POST['id_number_userid_student'];
						$data_s['pass_userid'] = $_POST['pass_userid_student'];
						$data_s['pass_sha1_userid'] = sha1($_POST['pass_userid_student']);
						$data_s['agency_userid'] = $_POST['agency_userid'];
						$data_s['name_userid'] = "";
						$data_s['school_name_userid'] = $_POST['school_name_userid'];				
						$data_s['kubun_userid'] = "1";//0:教師 1:生徒 9:個人
						$data_s['active_userid'] = "1"; //使用可は1 削除したら0
						$data_s['kengen_userid'] = $_POST['kengen_userid'];//0:全部 1：SOW 2：SOW(iPad) 3：UC 4:UC(iPad) 5：UD 6:UD(iPad)
						$data_s['ban_userid'] = "no";//禁止時間設けない:yes 設ける:no
						$data_s['ban_start_time_userid'] = "";
						$data_s['ban_end_time_userid'] = "";
						$data_s['created_time_userid'] = date("Ymd");
						$data_s['kigen_userid'] = $_POST['kigen_userid'];
						$data_s['created_userid'] = $serial_number_kanri;

						$colmn = "";
						$values = "";

						foreach ($data_s as $key => $val) {
							if ($colmn <> "") {
								$colmn .= ",";
							}
							if ($values <> "") {
								$values .= ",";
							}
							$colmn .= "\"" . $key . "\""; //ダブルクォーテーション
							$values .= "'" . $val . "'"; //シングルクォーテーション
						}

						$sql = "insert into userid ($colmn) values ($values);";
						//クエリ実行
						$stmt = $pdo->query($sql);
						if (!$stmt) {
							$info = $pdo->errorInfo();
							header('Content-Type: text/plain; charset=UTF-8');
							exit($info[2]);
						}
					}
			}
			//編集処理
			elseif ($_REQUEST['mode'] == 'update') {
				//教員編集登録
				$data['school_id_userid'] = $_POST['school_id_userid'];
				$data['id_number_userid'] = $_POST['id_number_userid_teacher'];
				$data['pass_userid'] = $_POST['pass_userid_teacher'];
				$data['pass_sha1_userid'] = sha1($_POST['pass_userid_teacher']);
				$data['agency_userid'] = $_POST['agency_userid'];
				$data['name_userid'] = "";
				$data['school_name_userid'] = $_POST['school_name_userid'];
				$data['kengen_userid'] = $_POST['kengen_userid'];//0:全部 1：SOW 2：SOW(iPad) 3：UC 4:UC(iPad) 5：UD 6:UD(iPad)
				$data['created_time_userid'] = date("Ymd");
				$data['kigen_userid'] = $_POST['kigen_userid'];

				$sql = "";
				foreach ($data as $key => $val) {
					if ($sql == "") {
						$sql = "update userid set ";
					} else {
						$sql .= ",";
					}
					$sql .= "\"" . $key . "\" = '" . $val . "'";
				}

				$sql .= " where serial_number_userid=" . $_POST['serial_number_userid'] . ";";
				//クエリ実行
				$stmt = $pdo->query($sql);
				if (!$stmt) {
					$info = $pdo->errorInfo();
					header('Content-Type: text/plain; charset=UTF-8');
					exit($info[2]);
				}

				$sql = "SELECT * FROM userid where source_number_userid='" . $_POST['serial_number_userid'] . "' and kubun_userid = '1';";
				$stmt = $pdo->query($sql);
				if (!$stmt) {
					$info = $pdo->errorInfo();
					header('Content-Type: text/plain; charset=UTF-8');
					exit($info[2]);
				}
				//FETCH_ASSOC：カラム名をキーとする連想配列で取得する
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				$stu_data = $stmt->fetch();	//fetchAll：全件 //fetch:1件

				//生徒編集登録（個人、お試し版でないなら）
				if ($_POST['kubun_userid'] !="9" && $_POST['kubun_userid'] !="99"){
					$data_s['school_id_userid'] = $_POST['school_id_userid'];
					$data_s['id_number_userid'] = $_POST['id_number_userid_student'];
					$data_s['pass_userid'] = $_POST['pass_userid_student'];
					$data_s['pass_sha1_userid'] = sha1($_POST['pass_userid_student']);
					$data_s['agency_userid'] = $_POST['agency_userid'];
					$data_s['school_name_userid'] = $_POST['school_name_userid'];
					$data_s['kengen_userid'] = $_POST['kengen_userid'];//0:全部 1：SOW 2：SOW(iPad) 3：UC 4:UC(iPad) 5：UD 6:UD(iPad)
					$data_s['kigen_userid'] = $_POST['kigen_userid'];

					$sql = "";
					foreach ($data_s as $key => $val) {
						if ($sql == "") {
							$sql = "update userid set ";
						} else {
							$sql .= ",";
						}
						$sql .= "\"" . $key . "\" = '" . $val . "'";
					}

					$sql .= " where serial_number_userid=" . $stu_data['serial_number_userid'] . ";";
					//クエリ実行
					$stmt = $pdo->query($sql);
					if (!$stmt) {
						$info = $pdo->errorInfo();
						header('Content-Type: text/plain; charset=UTF-8');
						exit($info[2]);
					}
				}
			}
			//削除処理
			elseif ($_REQUEST['mode'] == 'delete') {
				$data['active_userid'] = "0"; //使用可は1 削除したら0
				$sql = "";
				foreach ($data as $key => $val) {
					if ($sql == "") {
						$sql = "update userid set ";
					} else {
						$sql .= ",";
					}
					$sql .= "\"" . $key . "\" = '" . $val . "'";
				}

				$sql .= " where source_number_userid = '" . $_POST['serial_number_userid'] . "';";
				//クエリ実行
				$stmt = $pdo->query($sql);
				if (!$stmt) {
					$info = $pdo->errorInfo();
					header('Content-Type: text/plain; charset=UTF-8');
					exit($info[2]);
				}
			}
			//編集前
			elseif ($_REQUEST['mode'] == 'load') {
				//print_r($_POST);
				//教員、個人の情報
				$sql = "SELECT *
						FROM userid
						WHERE source_number_userid = '" . $_POST['serial_number_userid'] . "'
						AND (kubun_userid = '0' OR kubun_userid = '9' OR kubun_userid = '99')";

					$stmt = $pdo->query($sql);
					if (!$stmt) {
						$info = $pdo->errorInfo();
						header('Content-Type: text/plain; charset=UTF-8');
						exit($info[2]);
					}
					$stmt->setFetchMode(PDO::FETCH_ASSOC);
					$result_teacher = $stmt->fetch();	//fetchAll：全件 //fetch:1件
				//print_r($result);
				//生徒の情報
				$sql = "select * from userid where source_number_userid = '" . $_POST['serial_number_userid'] . "' and kubun_userid = '1';";
					$stmt = $pdo->query($sql);
					if (!$stmt) {
						$info = $pdo->errorInfo();
						header('Content-Type: text/plain; charset=UTF-8');
						exit($info[2]);
					}
					$stmt->setFetchMode(PDO::FETCH_ASSOC);
					$result_student = $stmt->fetch();	//fetchAll：全件 //fetch:1件
				//print_r($result);
			}

			//新規作成後、編集後
			if (isset($_REQUEST['mode']) && ($_REQUEST['mode'] == 'input' || $_REQUEST['mode'] == 'update')) {
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
				echo "<div class=\"col-md-8 t_center list\">\n";
				echo "<table width=\"100%\" border=\"0\">\n";
				echo "<tbody>\n";
				echo "<tr>\n";
				echo "<th width=\"20%\">学校コード : </th>\n";
				echo "<td>" . $data['school_id_userid'] . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<th>学校名 : </th>\n";
				echo "<td>" . $data['school_name_userid'] . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<th width=\"20%\">ID : </th>\n";
				echo "<td>" . $data['id_number_userid'] . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<th>パスワード : </th>\n";
				echo "<td>" . $data['pass_userid'] . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<th width=\"20%\">生徒ID : </th>\n";
				echo "<td>" . $data_s['id_number_userid'] . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<th>生徒パスワード : </th>\n";
				echo "<td>" . $data_s['pass_userid'] . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<th>代理店名 : </th>\n";
				echo "<td>" . $data['agency_userid'] . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<th>ライセンス期限 : </th>\n";
				echo "<td>" . date('Y年n月j日', strtotime($data['kigen_userid'])) . "</td>\n";
				echo "</tr>\n";
				//echo "<tr>\n";
				//echo "<th>区分 : </th>\n";
				//if ($data['kubun_userid'] == 0) {
				//	echo "<td>教師</td>\n";
				//} else {
				//	echo "<td>生徒</td>\n";
				//}
				//echo "</tr>\n";
				echo "</tbody>\n";
				echo "</table>\n";
				echo "</div>\n";
				echo "</div>\n";
				echo "</br></br></br>\n";
				echo "<div class=\"row justify-content-center\">\n";
				echo "<div class=\"col-md-6 t_center\">\n";

				echo "<form action=\"user_list.php\" method=POST>\n";
				echo "<input type=hidden name=mode value=list>\n";
				echo "<input type=hidden name=id_number_kanri value=\"" . $id_number_kanri . "\">\n";
				echo "<input type=hidden name=serial_number_kanri value=\"" . $serial_number_kanri . "\">\n";
				echo "<input type=hidden name=searchmode value=\"0\">\n";
				echo "<input type=hidden name=refine_search value=\"0\">\n";
				echo "<input type=hidden name=model_search value=\"0\">\n";
				echo "<input type=submit value=\"一覧に戻る\"  class=\"listbackbtn\">\n";
				echo "</form>\n";

				echo "</div>\n";
				echo "</div>\n";
			}
			//削除後
			elseif (isset($_REQUEST['mode']) && ($_REQUEST['mode'] == 'delete')) {
				echo "<div class=\"row\" style=\"height: 30px\"></div>\n";
				echo "<div class=\"row justify-content-center\">\n";
				echo "<div class=\"col-md-6 t_center\">\n";
				echo "<h3>以下のユーザーを削除しました</h3><br>\n";
				echo "</div>\n";
				echo "</div>\n";
				
				echo "<div class=\"row justify-content-center\">\n";
				echo "<div class=\"col-md-6 t_center\">\n";
				echo "<form action=\"user_list.php\" method=POST>\n";
				echo "<input type=hidden name=mode value=list>\n";
				echo "<input type=hidden name=id_number_kanri value=\"" . $id_number_kanri . "\">\n";
				echo "<input type=hidden name=serial_number_kanri value=\"" . $serial_number_kanri . "\">\n";
				echo "<input type=hidden name=searchmode value=\"0\">\n";
				echo "<input type=submit value=\"一覧に戻る\"  class=\"listbackbtn\">\n";
				echo "</form>\n";
				echo "</div>\n";
				echo "</div>\n";
			}
			//編集
			elseif (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'load') {
				?>
				<div class="row" style="height: 30px"></div>
				<div class="row justify-content-center">
					<div class="col-md-6 t_center">
						<h3>編集</h3>
					</div>
				</div>
				<div class="row justify-content-center">
					<form action=user_edit.php method=POST>
						<input type=hidden name="mode" value="update">
						<input type=hidden name="id_number_kanri" value="<?php echo $id_number_kanri; ?>">
						<input type=hidden name="serial_number_kanri" value="<?php echo $serial_number_kanri; ?>">
						<input type=hidden name="serial_number_userid" value="<?php echo $result_teacher['serial_number_userid']; ?>">
						<div class="col t_center edit">
							<table>
								<tr>
									<th>学校コード</th>
									<td><input type=text name="school_id_userid" size=20 value="<?php echo $result_teacher['school_id_userid']; ?>"></td>
								</tr>
								<tr>
									<th>学校名</th>
									<td><input type=text name="school_name_userid" size=15 value="<?php echo $result_teacher['school_name_userid']; ?>"></td>
								</tr>
								<!--<tr>
									<th>区分</th>
									<td>
										<label style="margin-right: 16px;">
											<input type="radio"
												name="kubun_userid"
												value="0"
												<?php if ($result_teacher['kubun_userid'] != '9') echo 'checked'; ?>
												required>
												学校
										</label>
										<label>
											<input type="radio"
												name="kubun_userid"
												value="99"
												<?php if ($result_teacher['kubun_userid'] == '99') echo 'checked'; ?>>
												お試し版
										</label>
										<label>
											<input type="radio"
												name="kubun_userid"
												value="9"
												<?php if ($result_teacher['kubun_userid'] == '9') echo 'checked'; ?>>
												個人（5分間に3回以上ログインできない）
										</label>
									</td>
								</tr>-->

								<tr>
									<th id="th-teacher-id">教員ID</th>
									<td><input type=text name="id_number_userid_teacher" size=20 value="<?php echo $result_teacher['id_number_userid']; ?>"></td>
								</tr>
								<tr>
									<th id="th-teacher-pass">教員パスワード</th>
									<td><input type=text name="pass_userid_teacher" size=20 value="<?php echo $result_teacher['pass_userid']; ?>"></td>
								</tr>
								<tr id="row-student-id">
									<th>生徒ID</th>
									<td><input type=text name="id_number_userid_student" size=20 value="<?php echo $result_student['id_number_userid']; ?>"></td>
								</tr>
								<tr id="row-student-pass">
									<th>生徒パスワード</th>
									<td><input type=text name="pass_userid_student" size=20 value="<?php echo $result_student['pass_userid']; ?>"></td>
								</tr>
								<tr>
									<th>代理店名</th>
									<td><input type=text name="agency_userid" size=15 value="<?php echo $result_teacher['agency_userid']; ?>"></td>
								</tr>
								<tr>
									<th>機種</th>
									<td><select name="kengen_userid" id="kengen_select">
											<?php 
												$options = [
													5  => "UC-7/8　AI版",
													7  => "UD-1/2　AI版",
													9  => "UD-1/2　AI版_ver3(センサボード付)",
													
													35 => "AM-1　計測制御",
													37 => "AT-2　計測制御",
													38 => "HR-1　計測制御",
													39 => "LC-12　計測制御",
													11 => "UC-9/10",

													61 => "(特)双方向　SOW5/6　AI版",
													
													1  => "(特)UC-7/8　2025年版",
													3  => "(特)UD-1/2　2025年版",

													31 => "(特)UC7/8　計測制御だけ",
													33 => "(特)UD-1/2　計測制御だけ",

													51 => "(特)双方向　SOW5/6",
												];
												$kengen_userid = $result_teacher['kengen_userid'];
												foreach ($options as $value => $text) {
													$selected = ($value == $kengen_userid) ? 'selected' : '';
													echo "<option value=\"$value\" $selected>$text</option>\n";
												}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<th>ライセンス期限</th>
									<td><input type=date name="kigen_userid" size=80 value="<?php echo date("Y-m-d", strtotime($result_teacher['kigen_userid'])); ?>"></td>
								</tr>
							</table>
						</div>
				</div>
				<br>
				<div class="row justify-content-center">
					<div class="col-md-6 t_center">
						<input type=submit value="更新" class="inputbtn">
					</div>
				</div>
				</form>
				<br>
				<?php
					/*
					<div class="row">
						<div class="col-md-6 t_right">
							<form action=user_edit.php method=POST onsubmit="return check();">
								<input type=hidden name=mode value="delete">
								<input type=hidden name=id_number_kanri value="<?php echo $id_number_kanri; ?>">
								<input type=hidden name=serial_number_kanri value="<?php echo $serial_number_kanri; ?>">
								<input type=hidden name="serial_number_userid" value=<?php echo $result_teacher['serial_number_userid']; ?>>
								<input type=submit value="削除" class="deletebtn">
							</form>
						</div>
					</div>
					*/
			}
			//新規作成
			else {
				$school_search = "";
				$schoo_name = "";
				$teacher_code = "";
				$student_code = "";
				?>
				<div class="row" style="height: 30px"></div>
				<div class="row justify-content-center">
					<div class="col-md-4 t_center">
						<h3>ID登録</h3>
					</div>
				</div>
				<div class="row" style="height: 20px"></div>
				<div class="row justify-content-center">
					まずは学校コード検索から
				</div>
				<div class="row" style="height: 10px"></div>
				<div class="row justify-content-center">
					<form action=user_edit.php method="POST">
						<input type=hidden name="mode" value="search">
						<input type=hidden name="id_number_kanri" value="<?php echo $id_number_kanri; ?>">
						<input type=hidden name="serial_number_kanri" value="<?php echo $serial_number_kanri; ?>">
						学校コード検索　<input type=text name="school_search" size=20 required>
						<input type=submit name="form2" value="検索">
					</form>
				</div>
					<?php
					$school_code = "";
					$schoo_name = "";
					$teacher_code = "";
					$student_code = "";
					if ($_POST['mode'] == 'search') {
						?>						
						<div class="row justify-content-center">
							<label for="school_select">学校を選択:</label>
						</div>
						<?php
						$school_search = $_POST['school_search'];
					
						$sql = "SELECT * FROM school_data 
									WHERE school_code LIKE :search_keyword 
									OR school_name LIKE :search_keyword 
									OR remarks1 LIKE :search_keyword";
							$search_keyword = '%' . $school_search . '%';
							$stmt = $pdo->prepare($sql);
							$stmt->bindParam(':search_keyword', $search_keyword, PDO::PARAM_STR);
							$stmt->execute();
							$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
							//print_r($results);

						//print_r($school_info);
						if (!empty($results)): ?>
							<div class="row justify-content-center">
								<form action=user_edit.php method="post">
									<input type=hidden name="mode" value="selected">
									<input type=hidden name="id_number_kanri" value="<?php echo $id_number_kanri; ?>">
									<input type=hidden name="serial_number_kanri" value="<?php echo $serial_number_kanri; ?>">
									<select id="school_select" name="school_select" size="5">
										<?php foreach ($results as $row): ?>
											<option value="<?= htmlspecialchars($row['school_code'], ENT_QUOTES, 'UTF-8'); ?>">
												<?= htmlspecialchars($row['school_name'], ENT_QUOTES, 'UTF-8'); ?> (<?= htmlspecialchars($row['school_code'], ENT_QUOTES, 'UTF-8'); ?>)
											</option>
										<?php endforeach; ?>
									</select>
									<div style="height: 30px"></div>
									<div class="t_center">
									<button type="submit">決定</button>
									</div>
								</form>
							</div>
						<?php else: ?>
							<p>該当する学校がありません。</p>
						<?php endif;
					}
					else if ($_POST['mode'] == 'selected') {
						$selected_school_code = $_POST['school_select'];
						//選択された学校を抽出
						$sql = "SELECT * FROM school_data WHERE school_code = :school_code";
							$stmt = $pdo->prepare($sql);
							$stmt->bindValue(':school_code', $selected_school_code, PDO::PARAM_STR);
							$stmt->execute();
							$school_details = $stmt->fetch(PDO::FETCH_ASSOC);
							//print_r($school_details);

						$school_code = $school_details['school_code'];
						$schoo_name = $school_details['school_name'];
						$teacher_code = $school_details['teacher_code'];
						$student_code = $school_details['student_code'];


						//同じschool_codeを含む既存のuseridレコードを全取得
						$sql = "SELECT school_id_userid FROM userid WHERE school_id_userid LIKE :school_code_prefix";
							$stmt = $pdo->prepare($sql);
							$stmt->bindValue(':school_code_prefix', $school_code . '%', PDO::PARAM_STR);
							$stmt->execute();
							$existing_codes = $stmt->fetchAll(PDO::FETCH_COLUMN);

						//重複していたら連番を付与
						if (in_array($school_code, $existing_codes)) {
							$max_suffix = 0;

							foreach ($existing_codes as $code) {
								// school_code_数字 の形式をチェック
								if (preg_match('/^' . preg_quote($school_code, '/') . '_(\d+)$/', $code, $matches)) {
									$num = (int)$matches[1];
									if ($num > $max_suffix) {
										$max_suffix = $num;
									}
								}
							}

							// 次の番号を付与
							$school_code = $school_code . '_' . ($max_suffix + 1);
							$teacher_code = "t".get_password_php();
							$student_code = "s".get_password_php();
						}
					}
					?>
					<div class="row" style="height: 30px"></div>
					<div class="row justify-content-center">
						<form action=user_edit.php method=POST>
							<input type=hidden name="mode" value="input">
							<input type=hidden name="id_number_kanri" value="<?php echo $id_number_kanri; ?>">
							<input type=hidden name="serial_number_kanri" value="<?php echo $serial_number_kanri; ?>">
							<div class="col t_center edit">
								<table>
									<tr>
										<th>学校コード</th>
										<td><input type=text name="school_id_userid" size=20 required value="<?php echo $school_code; ?>"><br>
										※お試し版の場合は日付で登録。例：20260401<br>（複数ある場合は_1,_2のように連番付ける）
										</td>
									</tr>
									<tr>
										<th>学校名</th>
										<td><input type=text name="school_name_userid" size=30 value="<?php echo $schoo_name; ?>"></td>
									</tr>
									<!--<tr>
										<th>区分</th>
										<td>
											<label style="margin-right: 16px;">
												<input type="radio" name="kubun_userid" value="0" checked required>
												学校
											</label>
											<label>
												<input type="radio" name="kubun_userid" value="99">
												お試し版
											</label>
											<label>
												<input type="radio" name="kubun_userid" value="9">
												個人（5分間に3回以上ログインできない）
											</label>
										</td>
									</tr>-->
									<tr>
										<th id="th-teacher-id">教員ID</th>
										<td>
											<input type=text name="id_number_userid_teacher" id="teacherid" size=20 required value="<?php echo $teacher_code; ?>">
											<a href="#" onclick="get_id_teacher()">自動生成</a>
										</td>
									</tr>
									<tr>
										<th id="th-teacher-pass">教員パスワード</th>
										<td>
											<input type=text name="pass_userid_teacher" id="teacherpass" size=20 required value="<?php echo get_password_php(); ?>">
											<a href="#" onclick="get_password_teacher()">自動生成</a>
										</td>
									</tr>
									<tr id="row-student-id">
										<th>生徒ID</th>
										<td>
											<input type=text name="id_number_userid_student" id="studentid" size=20 required value="<?php echo $student_code; ?>">
											<a href="#" onclick="get_id_student()">自動生成</a>
										</td>
									</tr>
									<tr id="row-student-pass">
										<th>生徒パスワード</th>
										<td>
											<input type=text name="pass_userid_student" id="studentpass" size=20 required value="<?php echo get_password_php(); ?>">
											<a href="#" onclick="get_password_student()">自動生成</a>
										</td>
									</tr>
									<tr>
										<th>代理店名</th>
										<td><input type=text name="agency_userid" size=15></td>
									</tr>
									<tr>
										<th>機種</th>
										<td><select name="kengen_userid" id="kengen_select" required>
											<option value="" disabled selected>選択してください</option>

											<option value="5">UC-7/8　AI版</option>
											<option value="7">UD-1/2　AI版</option>
											<option value="9">UD-1/2　AI版_ver3(センサボード付)</option>

											<option value="35">AM-1　計測制御</option>
											<option value="37">AT-2　計測制御</option>
											<option value="38">HR-1　計測制御</option>
											<option value="39">LC-12　計測制御</option>
											<option value="11">UC-9/10</option>

											<option value="61">(特)双方向　SOW5/6　AI版</option>

											<option value="1">(特)UC-7/8　2025年版</option>
											<option value="3">(特)UD-1/2　2025年版</option>

											<option value="31">(特)UC7/8　計測制御だけ</option>
											<option value="33">(特)UD-1/2　計測制御だけ</option>
											
											<option value="51">(特)双方向　SOW5/6</option>

											</select>
										</td>
									</tr>
									<tr>
										<th>ライセンス期限</th>
										<td><input type=date name="kigen_userid" size=80 required value="<?php echo date('m') <= 3 ? date('Y') : date('Y') + 1 ?>-03-31"></td>
									</tr>
								</table>
							</div>
							<div style="height: 30px"></div>
							<div class="row justify-content-center">
								<div class="col-md-6 t_center">
									<input type=submit name="form1" value="登録" class="inputbtn" onSubmit="return input_check()">
								</div>
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
		<script>
			function updateKubunUI() {
			const kubun = document.querySelector('input[name="kubun_userid"]:checked')?.value;

			const rowStudentId   = document.getElementById('row-student-id');
			const rowStudentPass = document.getElementById('row-student-pass');

			const thTeacherId   = document.getElementById('th-teacher-id');
			const thTeacherPass = document.getElementById('th-teacher-pass');

			const studentIdInput   = document.getElementById('studentid');
			const studentPassInput = document.getElementById('studentpass');

			// 万一 id が付いていない等のケースを即検知
			if (!thTeacherId || !thTeacherPass) {
				console.log('見出し(th)のidが見つかりません', { thTeacherId, thTeacherPass });
				return;
			}

			if (kubun === '9' || kubun === '99') {
				// 個人
				if (rowStudentId) rowStudentId.style.display = 'none';
				if (rowStudentPass) rowStudentPass.style.display = 'none';

				if (studentIdInput) studentIdInput.required = false;
				if (studentPassInput) studentPassInput.required = false;

				if (kubun === '9') {
					thTeacherId.textContent   = '個人ID';
					thTeacherPass.textContent = '個人パスワード';
				}
				else {
					thTeacherId.textContent   = 'お試し版ID';
					thTeacherPass.textContent = 'お試し版パスワード';
				}
			} else {
				// 学校
				if (rowStudentId) rowStudentId.style.display = '';
				if (rowStudentPass) rowStudentPass.style.display = '';

				if (studentIdInput) studentIdInput.required = true;
				if (studentPassInput) studentPassInput.required = true;

				thTeacherId.textContent   = '教員ID';
				thTeacherPass.textContent = '教員パスワード';
			}
			}

			// change イベントを設定
			document.querySelectorAll('input[name="kubun_userid"]').forEach(r => {
			r.addEventListener('change', updateKubunUI);
			});

			// 初期状態反映
			updateKubunUI();
		</script>

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