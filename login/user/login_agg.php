<?php
if (!isset($_REQUEST['serial_number_kanri'])) {
	require_once("../function/errorhtml.php");
	get_direct_access_error_html();
	exit;
} else {
	$id_number_userid = $_POST['id_number_userid'];
	$offset = $_POST['offset'];
	require_once("../function/function.php");
	require_once("../../function/database.php");
	/** @var PDO $pdo */
}
//print_r($_POST);
try {
	$search_start = (date('m') >= 4 ? date('Y') : date('Y') - 1) . "-04-01";
	$search_end   = date("Y-m-d"); // 画面表示用（当日）

	if (isset($_POST['start_date_search']) && $_POST['start_date_search'] !== '') {
		$search_start = $_POST['start_date_search'];
	}
	if (isset($_POST['end_date_search']) && $_POST['end_date_search'] !== '') {
		$search_end = $_POST['end_date_search'];
	}

	// ★ SQL用：終了日は翌日にする（当日分を含めるため）
	$search_end_sql = date('Y-m-d', strtotime($search_end . ' +1 day'));


	//総カウント取得
	$sql = "
		SELECT
		login_time::date AS login_date,
		COUNT(*)::int    AS login_count
		FROM login_info
		WHERE login_id_number = :id_number_userid
		AND login_time >= :search_start
		AND login_time <  :search_end
		GROUP BY login_time::date
		ORDER BY login_date DESC
		";

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':id_number_userid', $id_number_userid, PDO::PARAM_STR);
	$stmt->bindValue(':search_start',       $search_start,       PDO::PARAM_STR);
	$stmt->bindValue(':search_end',         $search_end_sql,         PDO::PARAM_STR);
	$stmt->execute();

	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$column_cnt = count($rows);

	//offset分取得
	$sql = "
		SELECT
		login_time::date AS login_date,
		COUNT(*)::int    AS login_count
		FROM login_info
		WHERE login_id_number = :id_number_userid
		AND login_time >= :search_start
		AND login_time <  :search_end
		GROUP BY login_time::date
		ORDER BY login_date DESC
		LIMIT 10 OFFSET ($offset * 10)
		";

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':id_number_userid', $id_number_userid, PDO::PARAM_STR);
	$stmt->bindValue(':search_start',       $search_start,       PDO::PARAM_STR);
	$stmt->bindValue(':search_end',         $search_end_sql,         PDO::PARAM_STR);
	$stmt->execute();

	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// ① DB結果を「日付 => 件数」の連想配列へ
	$countsByDate = []; // 例: ['2025-12-10' => 3, ...]
	foreach ($rows as $r) {
		$d = $r['login_date'];     // 'YYYY-MM-DD'
		$c = (int)$r['login_count'];
		$countsByDate[$d] = $c;
	}
	var_dump($id_number_userid, $search_start, $search_end, $search_end_sql);

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
		ログイン集計
	</title>
	<link href="../css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
	<link href="../css/logindesign.css" rel="stylesheet" type="text/css">
</head>
<body>
	<div class="container-fluid">
		<div class="row" style="height: 70px"></div>
		<form action="#" method="POST">
			<input type="hidden" name="mode" value="list">
			<input type="hidden" name="serial_number_kanri" value="<?php htmlspecialchars($serial_number_kanri, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="id_number_userid" value="<?php echo $id_number_userid ?>">
			<input type="hidden" name="offset" value="0">
			<div class="row justify-content-center">
				<div class="col-md-6">
					日付で検索<br>
					　　検索開始日<input type="date" class="textarea_ex" name="start_date_search" id="startdate" value="<?php echo $search_start ?>">　
					検索終了日<input type="date" class="textarea_ex" name="end_date_search" id="enddate" value="<?php echo $search_end ?>">　
				</div>
			</div>
			<div class="row" style="height: 10px"></div>
			<div class="row justify-content-center t_center">
				<input type=submit class="searchbtn" value="検索">
			</div>
		</form>

		<div class="row" style="height: 10px"></div>

		<div class="row justify-content-center">
		<div class="col t_center list">
			<table border="1" class="font_size_12">
				<thead>
					<tr>
						<th>日付</th>
						<th>ログイン数</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($countsByDate as $login_date => $login_count): ?>
					<tr>
						<td><?= htmlspecialchars($login_date, ENT_QUOTES, 'UTF-8') ?></td>
						<td><?= (int)$login_count ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		</div>

		<div class="row justify-content-center">
		<?php
		if ($column_cnt > ($offset + 1) * 10) {
			echo "全" . $column_cnt . "件　" . (((int)$offset * 10) + 1) . "-" . (($offset + 1) * 10) . "件を表示";
		} else {
			echo "全" . $column_cnt . "件　" . (($offset * 10) + 1) . "-" . $column_cnt . "件を表示";
		}
		?>
		</div>

		<div class="row" style="height: 10px"></div>

		<div class="row justify-content-center">

			<div class="col-md-2">
				<?php if ($column_cnt > 10 && $offset > 0): ?>
					<!-- 前の10件 -->
					<form method="post" action="#">
						<input type="hidden" name="mode" value="list">
						<input type="hidden" name="id_number_userid" value="<?= htmlspecialchars($id_number_userid, ENT_QUOTES, 'UTF-8'); ?>">
						<input type="hidden" name="serial_number_kanri" value="<?= htmlspecialchars($serial_number_kanri, ENT_QUOTES, 'UTF-8'); ?>">
						<input type="hidden" name="offset" value="<?= $offset - 1; ?>">
						<input type="submit" value="前の10件" class="nextbtn">
					</form>
				<?php endif; ?>
			</div>

			<div class="col-md-2">
				<?php if ($column_cnt > 10 && floor($column_cnt / 10) > $offset): ?>
					<!-- 次の10件 -->
					<form method="post" action="#">
						<input type="hidden" name="mode" value="list">
						<input type="hidden" name="id_number_userid" value="<?= htmlspecialchars($id_number_userid, ENT_QUOTES, 'UTF-8'); ?>">
						<input type="hidden" name="serial_number_kanri" value="<?= htmlspecialchars($serial_number_kanri, ENT_QUOTES, 'UTF-8'); ?>">
						<input type="hidden" name="offset" value="<?= $offset + 1; ?>">
						<input type="submit" value="次の10件" class="nextbtn">
					</form>
				<?php endif; ?>
			</div>

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