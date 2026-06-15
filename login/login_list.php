<?php
if (!isset($_POST['serial_number_kanri'])) {
	require_once("function/errorhtml.php");
	get_direct_access_error_html();
	exit;
} else {
	$id_number_kanri = $_POST['id_number_kanri'];
	$serial_number_kanri = $_POST['serial_number_kanri'];
	$offset = $_POST['offset'];
	require_once("function/function.php");
	require_once("../function/database.php");
	/** @var PDO $pdo */
	//ログイン履歴一覧に1度に表示する数
	define('loginlistcnt', 50);
}
?>
<html>
<head>
<meta charset="utf-8"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="content-language" content="ja">
<title>
双方向通信ログイン情報
</title>
	<link href="css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
	<link href="css/logindesign.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container-fluid">
<div class="row" style="height: 30px"></div>
<div class="row justify-content-center">
    <div class="col-md-4 t_center">
		<h3>ログイン履歴一覧</h3>
	</div>
</div>
<?php
//print_r($_POST);
//リストに表示する区分（全員、教員、生徒）
	if(!isset($_POST['sort_order'])) {
		$sort_order ="0";
	}
	else{
		$sort_order = $_POST['sort_order'];
	}
	//全員なら
	if ($sort_order == "0") {
		$sortSt = "WHERE ";
	} 
	//教員なら
	else if ($sort_order == "1") {
		$sortSt = "WHERE login_kubun='0' and ";
	} 
	else {
		$sortSt = "WHERE login_kubun='1' and ";
	}
//SQLのbetween部分
	$datebetween = "";
	$search_start = ""; //検索開始のdate
	$search_end = "";
	$date_check_st = "";//日付チェックしその返り値
	if (isset($_POST['start_date_search']) || isset($_POST['end_date_search'])) {
		$search_start = $_POST['start_date_search'];
		$search_end = $_POST['end_date_search'];

		$datetime1 = new DateTime($search_start);
		$datetime2 = new DateTime($search_end);

		//start_date_searchとend_date_searchの関係
		$diff = $datetime1->diff($datetime2);

		//検索開始と終了の計算が-なら範囲エラー、＋なら正常
		if ($diff->format('%R') == '-') {
			//エラー時に自動で開始日、終了日を入れるときはコメントを外す
			$datebetween = "login_time BETWEEN current_timestamp - interval '1 day' AND current_timestamp";
			$date_check_st = "終了日を開始日より前に指定することはできません";
		}
		else{
			$datebetween = "login_time BETWEEN '" . $search_start . " 00:00:00' AND '" . $search_end . " 23:59:59'";
		}
	}
	//指定がない場合は過去1日
	else {
		$search_start = date("Y-m-d", strtotime('-1 day'));
		$search_end = date("Y-m-d");
		$datetime1 = new DateTime($search_start);
		$datetime2 = new DateTime($search_end);
		$datebetween = "login_time BETWEEN '" . $search_start . " 00:00:00' AND '" . $search_end . " 23:59:59'";
	}

//テキスト検索
	if(!isset($_POST['text_search']) || $_POST['text_search'] == "") {
		$text_search = "";
		$text_sql = "";
	}
	else{
		$text_search = $_POST['text_search'];
		$text_sql = " AND login_id_number LIKE '".$text_search."%'";
	}
//リストに表示するオフセット取得
	if(!isset($_POST['offset'])) {
		$offsetcnt = "0";
		$offset_sqlSt = " LIMIT ".loginlistcnt." OFFSET 0;";
	}
	else{
		$offsetcnt = $_POST['offset'];
		$offset_sqlSt = " LIMIT ".loginlistcnt." OFFSET ".((int)$offsetcnt * loginlistcnt).";";
	}

//PHPのタイムゾーンの変更
	date_default_timezone_set('Asia/Tokyo');

//エラーなら
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
/*print_r($datetime1);
print_r("<br>");
print_r($datetime2);
print_r("<br>");
print_r($datebetween);
print_r("<br>");*/

$sqlst = "select * from login_info ".$sortSt . $datebetween . $text_sql ." order by serial_number_login DESC";
	
//print_r($sqlst);
//print_r("<br>");
?>
<!--検索-->
<form action="#" method=POST>
	<input type="hidden" name="mode" value="list">
	<input type="hidden" name="offset" value="0">
	<input type="hidden" name="id_number_kanri" value="<?php echo $id_number_kanri;?>">
	<input type="hidden" name="serial_number_kanri" value="<?php echo $serial_number_kanri;?>">
	<div class="row" style="height: 20px"></div>
	<!--区分-->
	<div class="row justify-content-center">
		<select name="sort_order" class="select_order" style="width: 150px" onchange="submit(this.form)">
			<?php
			//                    0      1     2 
			$sort_array = array("全員","教員","生徒");
			for($i = 0; $i < count($sort_array); $i++){
				if ($sort_order == (string) $i){		
					echo "<option value=\"".$i."\" selected>".$sort_array[$i]."</option>\n";
				}
				else{
					echo "<option value=\"".$i."\">".$sort_array[$i]."</option>\n";
				}	
			}
			?>
			</select>
	</div>
	<div class="row" style="height: 20px"></div>
	<!--テキスト-->
	<div class="row justify-content-center">
		IDで絞り込み
		<?php
		if ($text_search == "") {
			echo "<input type=text name=\"text_search\" size=10 value=\"\">\n";
		} else {
			echo "<input type=text name=\"text_search\" size=10 value=\"" . $text_search . "\">\n";
		}
		?>
	</div>
	<div class="row" style="height: 20px"></div>
	<!--日付-->
	<div class="row justify-content-center t_center">		
		検索開始日<input type="date" name="start_date_search" value="<?php echo $search_start ?>">　
		検索終了日<input type="date" name="end_date_search" value="<?php echo $search_end ?>">　
	</div>
	<!--検索ボタン-->
	<div class="row" style="height: 20px"></div>
	<div class="row justify-content-center t_center">
		<input type=submit class="searchbtn" value="検索">		
	</div>
</form>
<?php
//10件ずつ表示
$sqall = $sqlst . $offset_sqlSt;
//print_r($sqall);
//print_r("<br>");
	$stmt = $pdo->query($sqall);
	//$stmt = $pdo->query('SELECT * FROM question');
	if (!$stmt) {
		$info = $pdo->errorInfo();
		exit($info[2]);
	}
	//FETCH_ASSOC：カラム名をキーとする連想配列で取得する
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$rows = $stmt->fetchAll();	//fetchAll：全件 //fetch:1件
	//print_r($sqall);
	//print_r("<br><br>");
	//print_r($rows);
echo "<div class=\"row\" style=\"height: 10px\"></div>\n";

if (!empty($rows)){
	echo "<div class=\"col-md-12 t_center list\">\n";
	echo "<table border=1>\n";
	echo "<tr>\n";
	echo "<th>ログイン日時</th>\n";
	echo "<th>ID</th>\n";
	echo "<th>名前</th>\n";
	echo "<th>区分</th>\n";
	echo "</tr>\n";
	foreach($rows as $val) {
		echo "<tr>\n";
		//日時
			echo "<td>".date("y/m/d H:i", strtotime($val['login_time']))."</td>\n";
		//ID
			echo "<td>".$val['login_id_number']."</td>\n";
		//名前
			echo "<td>".$val['school_name']."</td>\n";
		//区分
			if ($val['login_kubun'] == "0"){
				echo "<td>教員</td>\n";
			}
			else {
				echo "<td>生徒</td>\n";
			}
		echo "</tr>\n";
	}		
	echo "</table>\n";
	echo "</div>\n";
}
else{
	//データがなかったらここで終了
	echo "データはまだありません。";
}

//print_r($offsetcnt);

//全体の件数取得
$stmt = $pdo->query($sqlst);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$column_cnt = $stmt -> rowCount();
echo "<div class=\"row\" style=\"height: 10px\"></div>\n";

echo "<div class=\"row justify-content-center\">\n";
if ($column_cnt > ((int)$offsetcnt + 1) * loginlistcnt){
	echo "全".$column_cnt."件　".(((int)$offsetcnt * loginlistcnt) + 1)."-".(((int)$offsetcnt + 1) * loginlistcnt)."件を表示";
}
else{
	echo "全".$column_cnt."件　".(((int)$offsetcnt * loginlistcnt) + 1)."-".$column_cnt."件を表示";
}

echo "</div>\n";

echo "<div class=\"row\" style=\"height: 10px\"></div>\n";
echo "<div class=\"row justify-content-center\">\n";
echo "<div class=\"col-md-2\">\n";
if ($column_cnt > loginlistcnt && (int)$offsetcnt > 0){
	//前の10件ボタン
	echo "<form method=\"post\" action=\"#\">\n";
	echo "<input type=\"hidden\" name=\"mode\" value=\"list\">\n";
	echo "<input type=\"hidden\" name=\"offset\" value=\"".((int)$offsetcnt - 1)."\">\n";
	echo "<input type=\"hidden\" name=\"serial_number_kanri\" value=\"".$serial_number_kanri."\">\n";
	echo "<input type=\"hidden\" name=\"id_number_kanri\" value=\"".$id_number_kanri."\">\n";
	echo "<input type=\"hidden\" name=\"login_teacher_id\" value=\"".$id_number_kanri."\">\n";
	echo "<input type=\"hidden\" name=\"start_date_search\" value=\"".$search_start."\">\n";
	echo "<input type=\"hidden\" name=\"end_date_search\" value=\"".$search_end."\">\n";
	echo "<input type=\"hidden\" name=\"sort_order\" value=\"".$sort_order."\">\n";
	echo "<input type=\"submit\" value=\"前の".loginlistcnt."件\"  class=\"nextbtn\">\n";
	echo "</form>\n";
}
echo "</div>\n";
echo "<div class=\"col-md-2\">\n";
if ($column_cnt > loginlistcnt && floor($column_cnt / loginlistcnt) > (int)$offsetcnt ){
	//次の10件ボタン
	echo "<form method=\"post\" action=\"#\">\n";
	echo "<input type=\"hidden\" name=\"mode\" value=\"list\">\n";
	echo "<input type=\"hidden\" name=\"offset\" value=\"".((int)$offsetcnt + 1)."\">\n";	
	echo "<input type=\"hidden\" name=\"serial_number_kanri\" value=\"".$serial_number_kanri."\">\n";
	echo "<input type=\"hidden\" name=\"id_number_kanri\" value=\"".$id_number_kanri."\">\n";
	echo "<input type=\"hidden\" name=\"login_teacher_id\" value=\"".$id_number_kanri."\">\n";
	echo "<input type=\"hidden\" name=\"start_date_search\" value=\"".$search_start."\">\n";
	echo "<input type=\"hidden\" name=\"end_date_search\" value=\"".$search_end."\">\n";
	echo "<input type=\"hidden\" name=\"sort_order\" value=\"".$sort_order."\">\n";
	echo "<input type=\"submit\" value=\"次の".loginlistcnt."件\"  class=\"nextbtn\">\n";
	echo "</form>\n";
}
echo "</div>\n";
echo "</div>\n";

$pdo = null;

?>
<?php
get_navi_html();
?>
</div>
</body>
</html>
