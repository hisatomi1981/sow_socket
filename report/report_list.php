<?php
if (!isset($_POST['mode'])) {
	require_once("../login/function/errorhtml.php");
	get_direct_access_error_html();
	exit;
} else {
	$school_id_userid = $_POST['school_id_userid'];	
	$offset = (int)($_POST['offset'] ?? 0);
	if ($offset < 0) $offset = 0;
	$id_number_userid = $_POST['id_number_userid'];
	require_once("../login/function/function.php");
	require_once("../function/database.php");
	/** @var PDO $pdo */
}
try {
	//削除なら
	if ($_POST['mode'] == 'delete') {
		$serial_number_report = $_POST['serial_number_report'];
		$sql = "UPDATE report
				SET active_report = false
				WHERE serial_number_report = :id
				AND school_id_report = :school_id";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':id', (int)$serial_number_report, PDO::PARAM_INT);
		$stmt->bindValue(':school_id', $school_id_userid, PDO::PARAM_STR);
		if (!$stmt->execute()) {
			$info = $stmt->errorInfo();
			exit($info[2]);
		}
	}
	// 一括削除なら（チェックボックスで選択）
	if ($_POST['mode'] == 'bulk_delete') {
		$ids = $_POST['delete_ids'] ?? [];  // delete_ids[] で来る想定

		// 数値のみ許可（安全対策）
		$ids = array_values(array_filter($ids, fn($v) => ctype_digit((string)$v)));

		if (!empty($ids)) {
			// PostgreSQL: ANY + int配列
			$sql = "
				UPDATE report
				SET active_report = false
				WHERE school_id_report = :school_id
				AND serial_number_report = ANY(:ids)
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindValue(':school_id', $school_id_userid, PDO::PARAM_STR);

			// '{1,2,3}' 形式の配列文字列にして渡す
			$pgArray = '{' . implode(',', $ids) . '}';
			$stmt->bindValue(':ids', $pgArray, PDO::PARAM_STR);

			if (!$stmt->execute()) {
				$info = $stmt->errorInfo();
				exit($info[2]);
			}
		}
	}

	// ==========================================================
	// 検索条件 取得＆バリデーション
	// ==========================================================
	$date_check_st   = "";
	$search_start    = "";
	$search_end      = "";
	$search_gakunen  = "";
	$search_kumi     = "";
	$search_name     = "";

	// ▼ 期間（デフォルト：過去1時間〜現在+1分）
		if (!empty($_POST['start_date_search']) && !empty($_POST['end_date_search'])) {
			$search_start = $_POST['start_date_search'];
			$search_end   = $_POST['end_date_search'];

			$datetime1 = new DateTime($search_start);
			$datetime2 = new DateTime($search_end);

			// start_date_search と end_date_search の関係
			$diff = $datetime1->diff($datetime2);

			// 1ヶ月前の日付取得
			$threeMonthsAgo = new DateTime();
			$threeMonthsAgo->modify('-1 month');

			if ($datetime1 < $threeMonthsAgo) {
				$date_check_st = "1ヶ月以上前の日付は選択できません";
			}
			if ($diff->format('%R') == '-') {
				$date_check_st = "終了日を開始日より前に指定することはできません";
			}
		} 
		else {
			// 指定がない場合は過去1時間〜現在+1分
			$search_start = date("Y-m-d\TH:i", strtotime('-1 hour'));
			$search_end   = date("Y-m-d\TH:i", strtotime("+1 minute"));
		}

	// 学年
		if (!empty($_POST['gakunen_search'])) {
			$search_gakunen = (string)$_POST['gakunen_search'];
		}
	// 組
		if (!empty($_POST['kumi_search'])) {
			$search_kumi = (string)$_POST['kumi_search'];
		}
	// 名前
		if (!empty($_POST['name_search'])) {
			$search_name = (string)$_POST['name_search'];
		}

	// ここで日付エラーなら後段のSQL実行はしない（従来の挙動に合わせる）
	if ($date_check_st !== "") {
		// 例：表示部分で $date_check_st を出して終了
		// echo $date_check_st;
		// exit;
	} 
	else {
		// ==========================================================
		// sort_order（許可リストで ORDER BY を固定）
		// ==========================================================
		$sort_order = isset($_POST['sort_order']) ? (string)$_POST['sort_order'] : "0";

		$orderByMap = [
			"0" => "serial_number_report DESC",  // 新しい順
			"1" => "serial_number_report ASC",   // 古い順
			"2" => "seitono_report ASC",         // 番 昇順
			"3" => "seitono_report DESC",        // 番 降順
		];
		$orderBy = $orderByMap[$sort_order] ?? $orderByMap["0"];

		// ==========================================================
		// LIMIT/OFFSET（数値化）
		// ==========================================================
		$limit  = 100;
		$offset = (int)($offset ?? 0); // 既存の$offsetが未定義の場合にも耐える
		if ($offset < 0) $offset = 0;
		$offVal = $offset * $limit;

		// ==========================================================
		// datetime-local を DB 用に整形
		// ==========================================================
		$start_ts = (new DateTime($search_start))->format('Y-m-d H:i:s');
		$end_ts   = (new DateTime($search_end))->format('Y-m-d H:i:s');

		// ==========================================================
		// WHERE 条件を動的に構築（条件がある時だけ追加）
		// ==========================================================
		$where = [];
		$params = [];

		// 必須条件
		$where[] = "school_id_report = :school_id";
		$params[':school_id'] = [$school_id_userid, PDO::PARAM_STR];

		$where[] = "COALESCE(active_report, true) = true";

		$where[] = "created_time_report BETWEEN :start_ts AND :end_ts";
		$params[':start_ts'] = [$start_ts, PDO::PARAM_STR];
		$params[':end_ts']   = [$end_ts,   PDO::PARAM_STR];

		// 任意条件（データがなければSQLに入れない）
		if ($search_gakunen !== "") {
			$where[] = "gakunen_report = :gakunen";
			$params[':gakunen'] = [$search_gakunen, PDO::PARAM_STR];
		}
		if ($search_kumi !== "") {
			$where[] = "kumi_report = :kumi";
			$params[':kumi'] = [$search_kumi, PDO::PARAM_STR];
		}
		if ($search_name !== "") {
			// 完全一致（部分一致にしたい場合は下のコメント参照）
			//$where[] = "name_report = :name";
			//$params[':name'] = [$search_name, PDO::PARAM_STR];

			// 部分一致にしたい場合（PostgreSQL）
			$where[] = "name_report ILIKE :name";
			$params[':name'] = ['%' . $search_name . '%', PDO::PARAM_STR];
		}

		$whereSql = implode("\n            AND ", $where);

		// ==========================================================
		// 1) 一覧取得（prepare + bind）
		// ==========================================================
		$sqlList = "
			SELECT *
			FROM report
			WHERE {$whereSql}
			ORDER BY {$orderBy}
			LIMIT :limit
			OFFSET :offset
		";

		$stmt = $pdo->prepare($sqlList);

		// WHERE の bind（動的）
		foreach ($params as $ph => $arr) {
			$stmt->bindValue($ph, $arr[0], $arr[1]);
		}

		// LIMIT/OFFSET の bind
		$stmt->bindValue(':limit',  (int)$limit,  PDO::PARAM_INT);
		$stmt->bindValue(':offset', (int)$offVal, PDO::PARAM_INT);

		if (!$stmt->execute()) {
			$info = $stmt->errorInfo();
			exit($info[2]);
		}

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// ==========================================================
		// 2) 全体件数取得（SELECT COUNT(*)）
		// ==========================================================
		$sqlCount = "
			SELECT COUNT(*) AS cnt
			FROM report
			WHERE {$whereSql}
		";

		$stmt2 = $pdo->prepare($sqlCount);

		// COUNT 側も同じ bind（動的）
		foreach ($params as $ph => $arr) {
			$stmt2->bindValue($ph, $arr[0], $arr[1]);
		}

		if (!$stmt2->execute()) {
			$info = $stmt2->errorInfo();
			exit($info[2]);
		}

		$column_cnt = (int)$stmt2->fetch(PDO::FETCH_ASSOC)['cnt'];
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
		双方向通信管理システム(レポートデータ)
	</title>
	<link href="../login/css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
	<link href="../login/css/logindesign.css" rel="stylesheet" type="text/css">
	<link href="../login/css/seihin.css" rel="stylesheet" type="text/css">
</head>
<body>
	<div class="container-fluid">
		<div class="row" style="height: 70px"></div>
		<div class="row justify-content-center">
			<div class="col-md-4 t_center">
				<h4>レポートデータ</h4>
			</div>
		</div>
		<div class="row" style="height: 30px"></div>
		<div class="row justify-content-center">
			<div class="col-md t_center">
				<h5>レポートデータは1ヶ月間見ることが出来ます</h5>
			</div>
		</div>
		<div class="row" style="height: 10px"></div>
		<div class="row justify-content-center t_center">
			<form action="#" method=POST>
				<?php
				echo "<input type=hidden name=mode value=list>\n";
				echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
				echo "<input type=hidden name=id_number_userid value=\"" . $id_number_userid . "\">\n";
				echo "<input type=hidden name=offset value=" . $offset . ">\n";
				?>
				<summary class="filter-summary">絞り込み（任意）初期値は直近1時間を表示しております。</summary>
				検索開始日<input type="datetime-local" class="textarea_ex" name="start_date_search" id="startdate" value="<?php echo $search_start ?>">　
				検索終了日<input type="datetime-local" class="textarea_ex" name="end_date_search" id="enddate" value="<?php echo $search_end ?>">　
				<div class="row" style="height: 10px"></div>
				<summary class="filter-summary">学年、組、名前でもそれぞれ絞り込みが可能です。</summary>
				学年
				<input type="text" class="textarea_ex" name="gakunen_search" id="gakunen_search"
					value="<?php echo htmlspecialchars($search_gakunen, ENT_QUOTES, 'UTF-8'); ?>"
					style="width:80px;"placeholder="例：3">　

				組
				<input type="text" class="textarea_ex" name="kumi_search" id="kumi_search"
					value="<?php echo htmlspecialchars($search_kumi, ENT_QUOTES, 'UTF-8'); ?>"
					style="width:80px;"placeholder="例：1">　

				名前
				<input type="text" class="textarea_ex" name="name_search" id="name_search"
					value="<?php echo htmlspecialchars($search_name, ENT_QUOTES, 'UTF-8'); ?>"
					style="width:160px;"placeholder="例：久富　太郎">
				<br><br>
				<input type=submit class="searchbtn" value="検索">
			</form>
		</div>
		<?php
		if ($date_check_st != ""){
			echo "<div class=\"row justify-content-center\">\n";
			echo "<div class=\"col-md t_center\">\n";
			echo "<h4>".$date_check_st."</h4>\n";
			echo "</div>\n";
			echo "</div>\n";
			
			echo "</div>\n";
			echo "</body>\n";
			echo "</html>\n";
			exit;
		} 
		echo "<div class=\"row justify-content-center t_center\">\n";
			echo "<form method=\"post\" action=\"#\">\n";
			echo "<input type=hidden name=mode value=list>\n";
			echo "<input type=hidden name=offset value=0>\n";
			echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
			echo "<input type=hidden name=id_number_userid value=\"" . $id_number_userid . "\">\n";
			echo "<input type=hidden name=start_date_search value=\"" . $search_start . "\">\n";
			echo "<input type=hidden name=end_date_search value=\"" . $search_end . "\">\n";
			echo "<input type=hidden name=sort_order value=\"" . $sort_order . "\">\n";
			echo "<input type=\"hidden\" name=\"gakunen_search\" value=\"" . htmlspecialchars($search_gakunen, ENT_QUOTES, 'UTF-8') . "\">\n";
			echo "<input type=\"hidden\" name=\"kumi_search\" value=\"" . htmlspecialchars($search_kumi, ENT_QUOTES, 'UTF-8') . "\">\n";
			echo "<input type=\"hidden\" name=\"name_search\" value=\"" . htmlspecialchars($search_name, ENT_QUOTES, 'UTF-8') . "\">\n";
			echo "<select name=\"sort_order\" class=\"textarea_ex\" style=\"width: 250px\" onchange=\"submit(this.form)\">\n";
			$sort_array = array("新しい順", "古い順", "番号昇順", "番号降順");
			for ($i = 0; $i < count($sort_array); $i++) {
				if ($sort_order  == (string) $i) {
					echo "<option value=\"" . $i . "\" selected>" . $sort_array[$i] . "</option>\n";
				} else {
					echo "<option value=\"" . $i . "\">" . $sort_array[$i] . "</option>\n";
				}
			}
			echo "</select>\n";
			echo "</form>\n";
		echo "</div>\n";
		echo "<div class=\"row\" style=\"height: 10px\"></div>\n";
		
		if (!empty($rows)) {
			//ダウンロード
				echo "<div class=\"row justify-content-center\">\n";
					echo "<div class=\"col-md t_center\">\n";
						echo "<form id=\"form10\" action=\"report_download.php\" method=POST>\n";
							echo "<input type=hidden name=mode value=zip>\n";
							echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
							echo "<input type=hidden name=start_date_search value=\"" . $search_start . "\">\n";
							echo "<input type=hidden name=end_date_search value=\"" . $search_end . "\">\n";
							echo "<input type=hidden name=sort_order value=\"" . $sort_order . "\">\n";
							echo "<input type=submit value=\"レポートデータダウンロード\" class=\"btn_etc\">\n";
						echo "</form>\n";
					echo "</div>\n";
				echo "</div>\n";
			// ★一括削除フォーム（入れ子回避のためテーブル外に置く）
				echo "<div class=\"row justify-content-center\">\n";
					echo "<div class=\"col-md t_center\">\n";
						echo "<form id=\"bulkForm\" method=\"post\" action=\"#\" onsubmit=\"return confirm('チェックしたデータを削除しますか？');\">\n";
							echo "<input type=hidden name=mode value=bulk_delete>\n";
							echo "<input type=hidden name=offset value=\"" . $offset . "\">\n";
							echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
							echo "<input type=hidden name=id_number_userid value=\"" . $id_number_userid . "\">\n";
							echo "<input type=hidden name=start_date_search value=\"" . $search_start . "\">\n";
							echo "<input type=hidden name=end_date_search value=\"" . $search_end . "\">\n";
							echo "<input type=hidden name=sort_order value=\"" . $sort_order . "\">\n";
							echo "<input type=submit value=\"チェックしたデータを削除\" class=\"btn_etc\">\n";
						echo "</form>\n";
					echo "</div>\n";
				echo "</div>\n";

			echo "<div class=\"col-md-12 t_center list\">\n";
			echo "<table border=1>\n";
			echo "<tr>\n";
			echo "<th>選択</th>\n";
			echo "<th>操作</th>\n";
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
				//チェックボックス（bulkForm に紐付ける：form属性）
				echo "<td>";
					echo "<input type=\"checkbox\" name=\"delete_ids[]\" value=\"" . (int)$val['serial_number_report'] . "\" form=\"bulkForm\">";
				echo "</td>\n";
				//削除
				echo "<td>\n";
					echo "<form method=\"post\" action=\"#\">\n";
						echo "<input type=hidden name=mode value=delete>\n";
						echo "<input type=hidden name=offset value=\"" . $offset . "\">\n";
						echo "<input type=hidden name=serial_number_report value=\"" . $val['serial_number_report'] . "\">\n";
						echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
						echo "<input type=hidden name=id_number_userid value=\"" . $id_number_userid . "\">\n";
						echo "<input type=hidden name=start_date_search value=\"" . $search_start . "\">\n";
						echo "<input type=hidden name=end_date_search value=\"" . $search_end . "\">\n";
						echo "<input type=hidden name=sort_order value=\"" . $sort_order . "\">\n";
						echo "<input type=\"submit\" value=\"削除\" class=\"\" onclick=\"return confirm('削除しますか？');\">\n";
					echo "</form>\n";
				echo "</td>\n";
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
					echo "<form action=\"report_edit.php\" method=POST>\n";
					echo "<input type=hidden name=mode value=new>\n";
					echo "<input type=hidden name=serial_number_report value=\"" . $val['serial_number_report'] . "\">\n";
					echo "<input type=hidden name=img_file_name_report value=\"" . $val['img_file_name_report'] . "\">\n";
					echo "<input type=hidden name=school_id_userid value=\"" . $school_id_userid . "\">\n";
					echo "<input type=hidden name=id_number_userid value=\"" . $id_number_userid . "\">\n";
					echo "<input type=hidden name=start_date_search value=\"" . $search_start . "\">\n";
					echo "<input type=hidden name=end_date_search value=\"" . $search_end . "\">\n";
					echo "<input type=hidden name=sort_order value=\"" . $sort_order . "\">\n";
					echo "<input type=submit value=\"メッセージ\" class=\"updatebtn\">\n";
					echo "</form>\n";
				echo "</td>\n";
				//メッセージ内容
				echo "<td align=\"left\">" . get_disp_text($val['message_report'], 20) . "</td>\n";

				echo "</tr>\n";
			}
			echo "</table>\n";
			echo "</div>\n";
		} 
		else {
			//データがなかったらここで終了
			echo "通信データはありません。";
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
			echo "<input type=hidden name=id_number_userid value=\"" . $id_number_userid . "\">\n";
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
			echo "<input type=hidden name=id_number_userid value=\"" . $id_number_userid . "\">\n";
			echo "<input type=hidden name=start_date_search value=\"" . $search_start . "\">\n";
			echo "<input type=hidden name=end_date_search value=\"" . $search_end . "\">\n";			
			echo "<input type=hidden name=sort_order value=\"" . $sort_order . "\">\n";
			echo "<input type=hidden name=offset value=" . ($offset + 1) . ">\n";
			echo "<input type=submit value=\"次の100件\"  class=\"nextbtn\">\n";
			echo "</form>\n";
		}
		echo "</div>\n";
		echo "</div>\n";
		?>
		
		<div style="height: 50px"></div>

	</div>
	<?php
	get_navi_html();
	?>
</body>

</html>