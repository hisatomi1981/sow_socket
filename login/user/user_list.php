<?php
if (!isset($_REQUEST['serial_number_kanri'])) {
	require_once("../function/errorhtml.php");
	get_direct_access_error_html();
	exit;
} else {
	$id_number_kanri = $_POST['id_number_kanri'];
	$serial_number_kanri = $_POST['serial_number_kanri'];
	$offset = $_POST['offset'];
	require_once("../function/function.php");
	require_once("../../function/database.php");
	/** @var PDO $pdo */
}
//print_r($_POST);
try {
	//SQLのbetween部分
		$datebetween = "";
		$search_start = (date('m') >= 4 ? date('Y') : date('Y') - 1) . "-04-01";
		$search_end = date("Y-m-d");
		$searchtext = "";
		$refinedata = "";
		$modeldata = "";

	if (isset($_POST['searchmode'])) {
		//日時検索の場合
		if ($_POST['searchmode'] == "0") {
			if (isset($_POST['start_date_search']) || isset($_POST['end_date_search'])) {
				$search_start = $_POST['start_date_search'];
				$search_end = $_POST['end_date_search'];

				$datetime1 = new DateTime($search_start);
				$datetime2 = new DateTime($search_end);
				$diff = $datetime1->diff($datetime2);
				//検索開始と終了の計算が-なら範囲エラー、＋なら正常
				if ($diff->format('%R') != '-') {
					$datebetween = " and shipped_date_userid BETWEEN '" . $search_start . "' and '" . $search_end . "'";
				}
				//print_r($datebetween);
			}
		}
		//テキスト検索の場合
		else if ($_POST['searchmode'] == "1") {
			$searchtext = " and school_name_userid LIKE '%" . $_POST['text_search'] . "%' 
							or agency_userid LIKE '%" . $_POST['text_search'] . "%' 
							or school_id_userid LIKE '%" . $_POST['text_search'] . "%'";
		}
		//期限検索の場合
		else if ($_POST['searchmode'] == "2") {
			$now = new DateTime();
			$nowString = $now->format('Y-m-d H:i:s'); // 例えば "2024-01-11 12:34:56" の形式
			$refine_search = $_POST['refine_search'];//期限内か期限切れか全てか
			//期限内のユーザ
			if ($refine_search == "0"){
				$refinedata = " and kigen_userid > '" . $nowString . "'";
			}
			//期限切れ
			else if ($refine_search == "1"){
				$refinedata = " and kigen_userid < '" . $nowString . "'";
			}
			//全てのユーザ
			else{
				//何もしない
			}
			//print_r($refinedata."<br>");
		}
		//機種検索の場合
		else if ($_POST['searchmode'] == "3") {
			$model_search = $_POST['model_search'];//期限内か期限切れか全てか
			$modeldata = " and kengen_userid = '".$model_search."'";
		}
	}

	//print_r($_POST);
	//作成したリストを表示
	$sql = "SELECT * FROM userid where created_userid=" . $serial_number_kanri . " and active_userid = '1'" . $datebetween . $searchtext . $refinedata . $modeldata .
			" order by serial_number_userid DESC";
		$sqast = $sql . " LIMIT 50 OFFSET " . ($offset * 50) . ";";
		//print_r($sqast);
		$stmt = $pdo->query($sqast);
		if (!$stmt) {
			$info = $pdo->errorInfo();
			header('Content-Type: text/plain; charset=UTF-8');
			exit($info[2]);
		}
		//FETCH_ASSOC：カラム名をキーとする連想配列で取得する
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $stmt->fetchAll();	//fetchAll：全件 //fetch:1件

		//全体の件数取得
		$sql .= ";";
		$stmt = $pdo->query($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$column_cnt = $stmt->rowCount();
		//print_r($sql);
} catch (PDOException $e) {
	die('Connection failed: ' . $e->getMessage());
}finally {
	$pdo = null;
}
function getModelName($code) {
    $model_map = [
		"5" => "UC-7/8　AI版",
		"7" => "UD-1/2　AI版",
		"9" => "UD-1/2　AI版_ver3(センサボード付)",

		"35" => "AM-1　計測制御",
		"37" => "AT-2　計測制御",
		"38" => "HR-1　計測制御",
		"39" => "LC-12　計測制御",
		"11" => "UC-9/10",

		"61" => "(特)双方向　SOW5/6　AI版",

		"1" => "(特)UC-7/8",
		"3" => "(特)UD-1/2",

		"31" => "(特)UC7/8　計測制御",
		"33" => "(特)UD-1/2　計測制御",

		"51" => "(特)双方向　SOW5/6",

        "0"  => "全部"
    ];

    return $model_map[$code] ?? "未定義";
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
</head>
<body>
	<div class="container-fluid">
		<div class="row" style="height: 70px"></div>
		<?php
		echo "<div class=\"col t_center\">\n";
		echo "<form action=\"user_edit.php\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name=\"mode\" value=\"new\">\n";
		echo "<input type=\"hidden\" name=\"id_number_kanri\" value=\"" . $id_number_kanri . "\">\n";
		echo "<input type=\"hidden\" name=\"serial_number_kanri\" value=\"" . $serial_number_kanri . "\">\n";
		echo "<input type=\"submit\" value=\"新規作成\" class=\"inputbtn\">\n";
		echo "</form>\n";
		echo "</div>\n";
		?>
		<div class="row" style="height: 10px"></div>
		<form action="#" method="POST">
			<input type="hidden" name="mode" value="list">
			<input type="hidden" name="serial_number_kanri" value="<?php echo $serial_number_kanri ?>">
			<input type="hidden" name="id_number_kanri" value="<?php echo $id_number_kanri ?>">
			<input type="hidden" name="offset" value="0">
			<div class="row justify-content-center">
				<div class="col-md-6">
					<?php
					if ($_POST['searchmode'] == "0") {
						echo "<input type=\"radio\" id=\"sm0\" name=\"searchmode\" value=\"0\" checked>\n";
					} else {
						echo "<input type=\"radio\" id=\"sm0\" name=\"searchmode\" value=\"0\">\n";
					}
					?>
					出荷日で検索<br>
					　　検索開始日<input type="date" class="textarea_ex" name="start_date_search" id="startdate" value="<?php echo $search_start ?>">　
					検索終了日<input type="date" class="textarea_ex" name="end_date_search" id="enddate" value="<?php echo $search_end ?>">　
				</div>
			</div>
			<div class="row" style="height: 10px"></div>
			<div class="row justify-content-center">
				<div class="col-md-6">
					<?php
					if ($_POST['searchmode'] != "1") {
						echo "<input type=\"radio\" id=\"sm1\" name=\"searchmode\" value=\"1\">\n";
						echo "文字で検索<br>　　\n";
						echo "<input type=text name=\"text_search\" size=50 value=\"\">\n";
					} else {
						echo "<input type=\"radio\" id=\"sm1\" name=\"searchmode\" value=\"1\" checked>\n";
						echo "文字で検索<br>　　\n";
						echo "<input type=text name=\"text_search\" size=50 value=\"" . $_POST['text_search'] . "\">\n";
					}
					?>

				</div>
			</div>
			<div class="row" style="height: 10px"></div>
			<div class="row justify-content-center">
				<div class="col-md-6">
					<?php
					$searchMode = $_POST['searchmode'];
					$refineSearch = $_POST['refine_search'];
					$isChecked = $searchMode == "2" ? 'checked' : '';
					$options = [
						"0" => "期限内のユーザ",
						"1" => "期限切れのユーザ",
						"2" => "全てのユーザ"
					];
					echo "<input type=\"radio\" id=\"sm2\" name=\"searchmode\" value=\"2\" $isChecked>\n";
					echo "期限で検索<br>　　\n";
					echo "<select name=\"refine_search\">\n";
					foreach ($options as $value => $text) {
						$isSelected = $value == $refineSearch ? 'selected' : '';
						echo "<option value=\"$value\" $isSelected>$text</option>\n";
					}
					echo "</select>\n";					
					?>
				</div>
			</div>
			<div class="row" style="height: 10px"></div>
			<div class="row justify-content-center">
				<div class="col-md-6">
					<?php
					$searchMode = $_POST['searchmode'];
					$modelSearch = $_POST['model_search'];
					$isChecked = $searchMode == "3" ? 'checked' : '';
					$options = [
						"5"  => "UC-7/8　AI版",
						"7"  => "UD-1/2　AI版",
						"9"  => "UD-1/2　AI版_ver3(センサボード付)",

						"35" => "AM-1　計測制御",
						"37" => "AT-2　計測制御",
						"38" => "HR-1　計測制御",
						"39" => "LC-12　計測制御",

						"61" => "(特)双方向　SOW5/6　AI版",

						"1"  => "(特)UC-7/8",
						"3"  => "(特)UD-1/2",

						"31" => "(特)UC7/8　計測制御",
						"33" => "(特)UD-1/2　計測制御",

						"51" => "双方向　SOW5/6",
					];
					echo "<input type=\"radio\" id=\"sm3\" name=\"searchmode\" value=\"3\" $isChecked>\n";
					echo "機種で検索<br>　　\n";
					echo "<select name=\"model_search\">\n";
					foreach ($options as $value => $text) {
						$isSelected = $value == $modelSearch ? 'selected' : '';
						echo "<option value=\"$value\" $isSelected>$text</option>\n";
					}
					echo "</select>\n";					
					?>
				</div>
			</div>
			<div class="row" style="height: 10px"></div>
			<div class="row justify-content-center t_center">
				<input type=submit class="searchbtn" value="検索">
			</div>
		</form>
		<?php

		echo "<div class=\"row\" style=\"height: 10px\"></div>\n";
		echo "<div class=\"row  justify-content-center\">";
		echo "<div class=\"col t_center list\">\n";
		echo "<table border=1 class=\"font_size_12\">\n";
		echo "<tr>\n";
		echo "<th>学校ID</th>\n";
		echo "<th>教員ID<br>生徒ID</th>\n";
		echo "<th>教員ﾊﾟｽﾜｰﾄﾞ<br>生徒ﾊﾟｽﾜｰﾄﾞ</th>\n";
		echo "<th width=\"150px\">代理店名</th>\n";
		echo "<th>学校名</th>\n";
		//echo "<th>区分</th>\n";
		echo "<th>機種</th>\n";
		echo "<th>作成日<br>期限</th>\n";
		echo "<th>ログイン集計</th>\n";
		echo "<th>状態</th>\n";
		echo "<th>操作</th>\n";
		echo "<th>出力</th>\n";
		echo "<th>ログイン</th>\n";
		echo "<th>出荷</th>\n";
		echo "</tr>\n";

		//作成したIDを順番に繰り返し
		foreach ($rows as $val) {
			//生徒なら
			if ($val['kubun_userid'] == "1") {
				$student_id = $val['id_number_userid'];
				$student_pass = $val['pass_userid'];
			}
			else{
				echo "<tr>";
				echo "<td>" . $val['school_id_userid'] . "</td>\n";
				echo "<td>" . $val['id_number_userid'] . "<br>". $student_id ."</td>\n";
				echo "<td>" . $val['pass_userid'] . "<br>". $student_pass ."</td>\n";
				//名前
				echo "<td>" . get_disp_text($val['agency_userid'], 30) . "</td>\n";
				//学校名
				echo "<td>" . $val['school_name_userid'] . "</td>\n";
				//教師か先生か
				/*
				if ($val['kubun_userid'] == "0") {
					echo "<td>教師</td>\n";
				}
				else if ($val['kubun_userid'] == "99") {
					echo "<td>お試し版</td>\n";
				}
				else if ($val['kubun_userid'] == "9") {
					echo "<td>個人</td>\n";
				}
				else {
					echo "<td></td>\n";
				}*/

				//機種
				echo "<td>".getModelName($val['kengen_userid'])."</td>";

				//作成日
				echo "<td>" . date("y/m/d", strtotime($val['created_time_userid'])) . "<br>". date("y/m/d", strtotime($val['kigen_userid'])) . "</td>\n";
				//期限
				//echo "" . date("y/m/d", strtotime($val['kigen_userid'])) . "</td>\n";
				//ログイン集計
					echo "<td>\n";
					echo "<form action=\"login_agg.php\" method=\"POST\" target=\"_blank\">\n";
					echo "<input type=\"hidden\" name=\"mode\" value=\"list\">\n";
					echo "<input type=\"hidden\" name=\"id_number_kanri\" value=\"" . $id_number_kanri . "\">\n";
					echo "<input type=\"hidden\" name=\"serial_number_kanri\" value=\"" . $serial_number_kanri . "\">\n";
					echo "<input type=\"hidden\" name=\"id_number_userid\" value=\"" . $val['id_number_userid'] . "\">\n";
					echo "<input type=\"hidden\" name=\"offset\" value=\"0\">\n";
					echo "<input type=\"submit\" value=\"ログイン集計\">\n";
					echo "</form>\n";
					echo "</td>\n";
				//状態
				if ($val['active_userid'] == "0"){
					echo "<td>削除</td>\n";
				}
				else{
					echo "<td></td>\n";
				}

				//教員か個人なら
				if ($val['kubun_userid'] == "0" || $val['kubun_userid'] == "9") {
					//操作
					echo "<td>\n";
					echo "<form action=\"user_edit.php\" method=\"POST\" target=\"_blank\">\n";
					echo "<input type=\"hidden\" name=\"mode\" value=\"load\">\n";
					echo "<input type=\"hidden\" name=\"id_number_kanri\" value=\"" . $id_number_kanri . "\">\n";
					echo "<input type=\"hidden\" name=\"serial_number_kanri\" value=\"" . $serial_number_kanri . "\">\n";
					echo "<input type=\"hidden\" name=\"serial_number_userid\" value=\"" . $val['serial_number_userid'] . "\">\n";
					echo "<input type=\"submit\" value=\"編集\">\n";
					echo "</form>\n";
					echo "</td>\n";

					//出力
					echo "<td>\n";
					echo "<form action=\"../pdf/outputpdf.php\" method=\"POST\" target=\"_blank\">\n";
					echo "<input type=\"hidden\" name=\"mode\" value=\"load\">\n";
						echo "<input type=\"hidden\" name=\"kengen_userid\" value=\"" . $val['kengen_userid'] . "\">\n";
					echo "<input type=\"hidden\" name=\"id_number_kanri\" value=\"" . $id_number_kanri . "\">\n";
					echo "<input type=\"hidden\" name=\"serial_number_kanri\" value=\"" . $serial_number_kanri . "\">\n";
					echo "<input type=\"hidden\" name=\"serial_number_userid\" value=\"" . $val['serial_number_userid'] . "\">\n";
					echo "<input type=\"submit\" value=\"PDF出力\">\n";
					echo "</form>\n";

					//ログイン
					echo "<td>\n";
					echo "<form action=\"../../index.php\" method=\"POST\" target=\"_blank\">\n";
					echo "<input type=\"hidden\" name=\"mode\" value=\"certification\">\n";
					echo "<input type=\"hidden\" name=\"id_number_userid\" value=\"" . $val['id_number_userid'] . "\">\n";
					echo "<input type=\"hidden\" name=\"pass_userid\" value=\"" . $val['pass_userid'] . "\">\n";
					echo "<input type=\"submit\" value=\"ログイン\">\n";
					echo "</form>\n";
					echo "</td>\n";
				}
				// 出荷チェックボックスと日付
				$shipped      = $val['shipped_userid'] ? 'checked' : '';
				$shipped_date = $val['shipped_date_userid'] ?? '';
				$sn           = $val['serial_number_userid'];
				$shipped_label = $val['shipped_userid'] ? '&nbsp;<span class="ship-label" style="color:green;font-weight:bold;">出荷済</span>' : '<span class="ship-label"></span>';
				$date_disabled = $val['shipped_userid'] ? '' : 'disabled';
				echo "<td style=\"text-align:center; white-space:nowrap; padding:4px;\">\n";
				echo "  <input type=\"checkbox\" class=\"ship-check\" data-sn=\"{$sn}\" " . $shipped . ">" . $shipped_label . "<br>\n";
				echo "  <input type=\"date\" class=\"ship-date\" data-sn=\"{$sn}\" value=\"" . htmlspecialchars($shipped_date) . "\" style=\"font-size:11px;width:110px;\" " . $date_disabled . ">\n";
				echo "</td>\n";
				echo "</tr>\n";
				$student_id = "";
				$student_pass = "";
			}
		}
		echo "</table>";
		echo "</div>";
		echo "</div>";

		echo "<div class=\"row justify-content-center\">\n";
		if ($column_cnt > ($offset + 1) * 50) {
			echo "全" . $column_cnt . "件　" . (((int)$offset * 50) + 1) . "-" . (($offset + 1) * 50) . "件を表示";
		} else {
			echo "全" . $column_cnt . "件　" . (($offset * 50) + 1) . "-" . $column_cnt . "件を表示";
		}
		echo "</div>\n";

		echo "<div class=\"row\" style=\"height: 10px\"></div>\n";
		echo "<div class=\"row justify-content-center\">\n";
		echo "<div class=\"col-md-2\">\n";
		if ($column_cnt > 50 && $offset > 0) {
			//前の10件ボタン
			echo "<form method=\"post\" action=\"#\">\n";
			echo "<input type=\"hidden\" name=\"mode\" value=\"list\">\n";
			echo "<input type=\"hidden\" name=\"serial_number_kanri\" value=\"" . $serial_number_kanri . "\">\n";
			echo "<input type=\"hidden\" name=\"id_number_kanri\" value=\"" . $id_number_kanri . "\">\n";
			echo "<input type=\"hidden\" name=\"searchmode\" value=\"" . $searchmode . "\">\n";	
			echo "<input type=\"hidden\" name=\"offset\" value=" . ($offset - 1) . ">\n";
			echo "<input type=\"submit\" value=\"前の50件\"  class=\"nextbtn\">\n";
			echo "</form>\n";
		}
		echo "</div>\n";
		echo "<div class=\"col-md-2\">\n";
		if ($column_cnt > 50 && floor($column_cnt / 50) > $offset) {
			//次の10件ボタン
			echo "<form method=\"post\" action=\"#\">\n";
			echo "<input type=\"hidden\" name=\"mode\" value=\"list\">\n";
			echo "<input type=\"hidden\" name=\"serial_number_kanri\" value=\"" . $serial_number_kanri . "\">\n";
			echo "<input type=\"hidden\" name=\"id_number_kanri\" value=\"" . $id_number_kanri . "\">\n";
			echo "<input type=\"hidden\" name=\"searchmode\" value=\"" . $searchmode . "\">\n";	
			echo "<input type=\"hidden\" name=\"offset\" value=" . ($offset + 1) . ">\n";
			echo "<input type=\"submit\" value=\"次の50件\"  class=\"nextbtn\">\n";
			echo "</form>\n";
		}
		echo "</div>\n";
		echo "</div>\n";
		?>
		<div style="height: 50px"></div>
		<div class="bottom_area"></div>
		<div style="height: 30px"></div>
	</div>
	<?php
	get_navi_html();
	?>
	<script>
		(function () {
		const sm0 = document.getElementById('sm0');
		const sm1 = document.getElementById('sm1');
		const sm2 = document.getElementById('sm2');
		const sm3 = document.getElementById('sm3');

		const startDate = document.getElementById('startdate');
		const endDate   = document.getElementById('enddate');

		const textSearch  = document.querySelector('input[name="text_search"]');
		const refineSel   = document.querySelector('select[name="refine_search"]');
		const modelSel    = document.querySelector('select[name="model_search"]');

		// 付随する入力操作があれば該当ラジオにチェック
		const bindRadio = (el, radio) => {
			if (!el || !radio) return;

			// フォーカスした瞬間に切り替え（＝「アクティブになったら」）
			el.addEventListener('focus', () => { radio.checked = true; });

			// 値が変わったら切り替え（PC/モバイル両対応）
			el.addEventListener('input',  () => { radio.checked = true; });
			el.addEventListener('change', () => { radio.checked = true; });
		};

		// 作成日（0）：開始日・終了日どちらを触っても 0
		bindRadio(startDate, sm0);
		bindRadio(endDate,   sm0);

		// 文字検索（1）
		bindRadio(textSearch, sm1);

		// 期限検索（2）
		bindRadio(refineSel, sm2);

		// 機種検索（3）
		bindRadio(modelSel, sm3);
		// 出荷チェックボックス・日付のAJAX保存
		document.querySelectorAll('.ship-check, .ship-date').forEach(function(el) {
			el.addEventListener('change', function() {
				var sn   = this.dataset.sn;
				var row  = document.querySelector('[data-sn="' + sn + '"].ship-check');
				var dateEl = document.querySelector('[data-sn="' + sn + '"].ship-date');
				var shipped   = row ? (row.checked ? '1' : '0') : '0';
				// チェックを外す時は確認ダイアログ＆日付リセット
				if (shipped === '0' && this.classList.contains('ship-check')) {
					if (!confirm('出荷済みを解除します。日付もリセットされますがよろしいですか？')) {
						row.checked = true; // 元に戻す
						return;
					}
					if (dateEl) { dateEl.value = ''; dateEl.disabled = true; }
					// 「出荷済」ラベルを消す
					var label = row.parentNode.querySelector('.ship-label');
					if (label) { label.textContent = ''; label.style.color = ''; }
				}
				// チェックを入れた時、日付を有効化＆空なら本日をセット
				if (shipped === '1') {
					if (dateEl) dateEl.disabled = false;
					if (dateEl && dateEl.value === '') {
						var today = new Date();
						var y = today.getFullYear();
						var m = String(today.getMonth() + 1).padStart(2, '0');
						var d = String(today.getDate()).padStart(2, '0');
						dateEl.value = y + '-' + m + '-' + d;
					}
					// 「出荷済」ラベルを表示
					var label = row.parentNode.querySelector('.ship-label');
					if (label) { label.textContent = '出荷済'; label.style.color = 'green'; label.style.fontWeight = 'bold'; }
				}
				var shipDate  = dateEl ? dateEl.value : '';
				var params = new URLSearchParams({
					serial_number_userid: sn,
					shipped: shipped,
					shipped_date: shipDate,
					serial_number_kanri: '<?php echo $serial_number_kanri; ?>'
				});
				fetch('ship_update.php', {
					method: 'POST',
					headers: {'Content-Type': 'application/x-www-form-urlencoded'},
					body: params.toString()
				})
				.then(function(r){ return r.json(); })
				.then(function(d){
					if(d.result !== 'ok'){
						alert('保存に失敗しました: ' + (d.error || ''));
					}
				})
				.catch(function(){ alert('通信エラーが発生しました'); });
			});
		});
		})();
	</script>
</body>

</html>