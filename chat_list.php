<?php
if (!isset($_POST['mode'])) {
	require_once("login/function/errorhtml.php");
	get_direct_access_error_html();
	exit;
} else {
	$school_id_userid = $_POST['school_id_userid'];	
	$offset = $_POST['offset'];
	$id_number_userid = $_POST['id_number_userid'];
	require_once("login/function/function.php");
	require_once("function/database.php");
	/** @var PDO $pdo */
}
try {
	//SQLのbetween部分
		$datebetween = "";
		$search_start = ""; //検索開始のdate
		$search_end = "";
		if (isset($_POST['start_date_search']) || isset($_POST['end_date_search'])) {
			$search_start = $_POST['start_date_search'];
			$search_end = $_POST['end_date_search'];

			$datetime1 = new DateTime($search_start);
			$datetime2 = new DateTime($search_end);
			$diff = $datetime1->diff($datetime2);
			//検索開始と終了の計算が-なら範囲エラー、＋なら正常
			if ($diff->format('%R') != '-') {
				$datebetween = " and time_clienta BETWEEN '" . $search_start . "' and '" . $search_end . "'";
			}
			//print_r($datebetween);
		}
		//指定がない場合は過去1時間
		else {
			//PHPのタイムゾーンの変更
			//date_default_timezone_set('Asia/Tokyo');
			$search_start = date("Y-m-d\TH:i", strtotime('-1 hour'));
			$search_end = date("Y-m-d\TH:i", strtotime("+1 minute"));
			$datebetween = " and time_clienta BETWEEN '" . $search_start . "' and '" . $search_end . "'";
		}

	//100件取得
	$sqlst = "select * from clientadata where school_id_clienta = '" . $school_id_userid . "'" . $datebetween . " ";
	//リストに表示する並び順取得
		if (!isset($_POST['sort_order'])) {
			$sort_order = "0";
		} else {
			$sort_order = $_POST['sort_order'];
		}
	//並び順
		//新しい順
		if ($sort_order == "0") {
			$sqlst .= "order by serial_number_clienta DESC";
		}
		//古い順
		elseif ($sort_order == "1") {
			$sqlst .= "order by serial_number_clienta ASC";
		}
		//送信番号　昇順
		elseif ($sort_order == "2") {
			$sqlst .= "order by client_a_clienta ASC";
		}
		//送信番号　降順
		elseif ($sort_order == "3") {
			$sqlst .= "order by client_a_clienta DESC";
		}
		//ネットワーク番号　昇順
		elseif ($sort_order == "4") {
			$sqlst .= "order by chat_group_clienta ASC";
		}
		//ネットワーク番号　降順
		elseif ($sort_order == "5") {
			$sqlst .= "order by chat_group_clienta DESC";
		} 
		else {
			$sqlst .= "order by serial_number_clienta DESC";
		}

	//100件ずつ表示
		$sqall = $sqlst . " LIMIT 100 OFFSET " . ($offset * 100) . ";";
		$stmt = $pdo->query($sqall);
		//$stmt = $pdo->query('SELECT * FROM question');
		if (!$stmt) {
			$info = $pdo->errorInfo();
			exit($info[2]);
		}
		//FETCH_ASSOC：カラム名をキーとする連想配列で取得する
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $stmt->fetchAll();	//fetchAll：全件 //fetch:1件

	//全体の件数取得
		$sqlst .= ";";
		$stmt = $pdo->query($sqlst);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$column_cnt = $stmt->rowCount();
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
		双方向通信管理システム(データ表示)
	</title>
	<link href="login/css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
	<link href="login/css/logindesign.css" rel="stylesheet" type="text/css">
	<link href="login/css/seihin.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div class="container-fluid">
		<div class="row" style="height: 70px"></div>
		<div class="row justify-content-center">
			<div class="col-md-4 t_center">
				<h4>チャットデータ</h4>
			</div>
		</div>
		<div class="row" style="height: 30px"></div>
		<div class="row justify-content-center t_center">
			<form action="#" method=POST>
				<?php
				echo "<input type=hidden name=mode value=list>\n";
				echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
				echo "<input type=hidden name=id_number_userid value=\"" . $id_number_userid . "\">\n";
				echo "<input type=hidden name=offset value=" . $offset . ">\n";
				?>
				検索開始日<input type="datetime-local" class="textarea_ex" name="start_date_search" id="startdate" value="<?php echo $search_start ?>">　
				検索終了日<input type="datetime-local" class="textarea_ex" name="end_date_search" id="enddate" value="<?php echo $search_end ?>">　
				<input type=submit class="searchbtn" value="検索">
			</form>
		</div>
		<?php
		//print_r($_POST);
		echo "<div class=\"row justify-content-center t_center\">\n";
			echo "<form method=\"post\" action=\"#\">\n";
				echo "<input type=hidden name=mode value=list>\n";
				echo "<input type=hidden name=offset value=0>\n";
				echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
				echo "<input type=hidden name=start_date_search value=\"" . $search_start . "\">\n";
				echo "<input type=hidden name=end_date_search value=\"" . $search_end . "\">\n";
				echo "<input type=hidden name=sort_order value=\"" . $sort_order . "\">\n";
				echo "<select name=\"sort_order\" class=\"textarea_ex\" style=\"width: 250px\" onchange=\"submit(this.form)\">\n";
				$sort_array = array("新しい順", "古い順", "送信番号昇順", "送信番号降順", "ネットワーク番号昇順", "ネットワーク番号降順");
				for ($i = 0; $i < count($sort_array); $i++) {
					if ($_POST['sort_order'] == (string) $i) {
						echo "<option value=\"" . $i . "\" selected>" . $sort_array[$i] . "</option>\n";
					} else {
						echo "<option value=\"" . $i . "\">" . $sort_array[$i] . "</option>\n";
					}
				}
				echo "</select>\n";
			echo "</form>\n";
		echo "</div>\n";
		echo "<div class=\"row\" style=\"height: 10px\"></div>\n";

		//ダウンロード
		echo "<div class=\"row justify-content-center\">\n";
			echo "<div class=\"col-md t_center\">\n";
				echo "<form id=\"form10\" action=\"https://sow.hisatomi.net/chat_download.php\" method=POST>\n";
				echo "<input type=hidden name=mode value=csvdl>\n";
				echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
				echo "<input type=hidden name=start_date_search value=\"" . $search_start . "\">\n";
				echo "<input type=hidden name=end_date_search value=\"" . $search_end . "\">\n";
				echo "<input type=hidden name=sort_order value=\"" . $sort_order . "\">\n";
				echo "<input type=submit class=\"btn_etc\" value=\"チャットデータダウンロード\" class=\"updatebtn\">\n";
				echo "</form>\n";
			echo "</div>\n";
		echo "</div>\n";

		if (!empty($rows)) {
			echo "<div class=\"col-md-12 t_center list\">\n";
			echo "<table border=1>\n";
			echo "<tr>\n";
			echo "<th>ネットワーク番号</th>\n";
			echo "<th>送信時間</th>\n";
			echo "<th>送信者名</th>\n";
			echo "<th>送信番号</th>\n";
			echo "<th>サーバ番号</th>\n";
			echo "<th>受信番号</th>\n";
			echo "<th>グループ</th>\n";
			echo "<th>データの種類</th>\n";
			echo "<th>重要</th>\n";
			echo "<th>繰返回数</th>\n";
			echo "<th>パスワード</th>\n";
			echo "<th width=\"250\">送信内容</th>\n";
			echo "<th width=\"250\">変換前</th>\n";
			echo "<th width=\"250\">プロンプト</th>\n";
			echo "</tr>\n";
			foreach ($rows as $val) {
				echo "<tr>\n";
				//ネットワーク番号
				echo "<td>" . $val['chat_group_clienta'] . "</td>\n";
				//送信時間
				echo "<td>" . date("y/m/d H:i:s", strtotime($val['time_clienta'])) . "</td>\n";
				//送信者名
				echo "<td>" . $val['client_a_name_clienta'] . "</td>\n";
				//送信番号
				echo "<td>" . $val['client_a_clienta'] . "</td>\n";
				//サーバ番号
				echo "<td>" . $val['server_clienta'] . "</td>\n";
				//受信番号
				echo "<td>" . $val['client_b_clienta'] . "</td>\n";
				//グループ
				echo "<td>" . $val['group_clienta'] . "</td>\n";
				//データの種類
				echo "<td>" . $val['data_kind_clienta'] . "</td>\n";
				//重要
				if ($val['juyo_clienta'] == "false") {
					echo "<td></td>\n";
				} else {
					echo "<td>〇</td>\n";
				}
				//繰返回数
				echo "<td>" . $val['repeat_cnt_clienta'] . "</td>\n";
				//パスワード
				echo "<td>" . $val['password_clienta'] . "</td>\n";
				//内容
				if ($val['data_kind_clienta'] == "プログラム") {
					$medata = explode("=", $val['message_st_clienta']); //分解
					echo "<td>" . $medata[0] . "</td>\n";
				} else if ($val['data_kind_clienta'] == "スタンプ") {
					$medata = explode(".", $val['message_st_clienta']); //分解
					echo "<td>スタンプ番号 " . $medata[0] . "</td>\n";
				} else {
					echo "<td>" . $val['message_st_clienta'] . "</td>\n";
				}
				//変換前
				echo "<td>" . $val['ai_before_st_clienta'] . "</td>\n";
				//プロンプト
				echo "<td>" . $val['ai_prompt_clienta'] . "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
			echo "</div>\n";
		} else {
			//データがなかったらここで終了
			echo "過去1時間の通信データはありません。";
		}

		echo "<div class=\"row\" style=\"height: 10px\"></div>\n";

		echo "<div class=\"row justify-content-center\">\n";
		if ($column_cnt > ($offset + 1) * 100) {
			echo "全" . $column_cnt . "件　" . (((int)$offset * 100) + 1) . "-" . (($offset + 1) * 100) . "件を表示";
		} else {
			echo "全" . $column_cnt . "件　" . (($offset * 100) + 1) . "-" . $column_cnt . "件を表示";
		}

		echo "</div>\n";

		echo "<div class=\"row\" style=\"height: 10px\"></div>\n";
		echo "<div class=\"row justify-content-center\">\n";
		echo "<div class=\"col-md-2\">\n";
		if ($column_cnt > 100 && $offset > 0) {
			//前の10件ボタン
			echo "<form method=\"post\" action=\"#\">\n";
			echo "<input type=hidden name=mode value=list>\n";
			echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
			echo "<input type=hidden name=start_date_search value=\"" . $search_start . "\">\n";
			echo "<input type=hidden name=end_date_search value=\"" . $search_end . "\">\n";			
			echo "<input type=hidden name=sort_order value=\"" . $sort_order . "\">\n";
			echo "<input type=hidden name=offset value=" . ($offset - 1) . ">\n";
			echo "<input type=submit value=\"前の100件\"  class=\"nextbtn\">\n";
			echo "</form>\n";
		}
		echo "</div>\n";
		echo "<div class=\"col-md-2\">\n";
		if ($column_cnt > 100 && floor($column_cnt / 100) > $offset) {
			//次の10件ボタン
			echo "<form method=\"post\" action=\"#\">\n";
			echo "<input type=hidden name=mode value=list>\n";
			echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
			echo "<input type=hidden name=start_date_search value=\"" . $search_start . "\">\n";
			echo "<input type=hidden name=end_date_search value=\"" . $search_end . "\">\n";			
			echo "<input type=hidden name=sort_order value=\"" . $sort_order . "\">\n";
			echo "<input type=hidden name=offset value=" . ($offset + 1) . ">\n";
			echo "<input type=submit value=\"次の100件\"  class=\"nextbtn\">\n";
			echo "</form>\n";
		}
		echo "</div>\n";
		echo "</div>\n";

		$pdo = null;
		?>
		
		<div style="height: 50px"></div>

	</div>
	<?php
	get_navi_html();
	?>
</body>

</html>