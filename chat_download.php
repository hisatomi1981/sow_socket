<?php
if (!isset($_POST['mode'])) {
	require_once("login/function/errorhtml.php");
	get_direct_access_error_html();
	exit;
} else {
	$school_id_userid = $_POST['school_id_userid'];
	$sort_order = $_POST['sort_order'];
	require_once("login/function/function.php");
	require_once("function/database.php");
	/** @var PDO $pdo */
}

try {
	//SQLのbetween部分
		$search_start = $_POST['start_date_search'] ?? '';
		$search_end   = $_POST['end_date_search'] ?? '';
		$datebetween = '';
		
		if ($search_start && $search_end) {
			try {
				$datetime1 = new DateTime($search_start);
				$datetime2 = new DateTime($search_end);
				$diff = $datetime1->diff($datetime2);
				if ($diff->format('%R') != '-') {
					$datebetween = " AND time_clienta BETWEEN :start_date AND :end_date";
				}
			} catch (Exception $e) {
				exit('Invalid date format');
			}
		}
		
	$sql = "SELECT * FROM clientadata WHERE school_id_clienta = :school_id" . $datebetween;
		
		switch ($sort_order) {
			case "1": $sql .= " ORDER BY serial_number_clienta ASC"; break;
			case "2": $sql .= " ORDER BY client_a_clienta ASC"; break;
			case "3": $sql .= " ORDER BY client_a_clienta DESC"; break;
			case "4": $sql .= " ORDER BY chat_group_clienta ASC"; break;
			case "5": $sql .= " ORDER BY chat_group_clienta DESC"; break;
			default:  $sql .= " ORDER BY serial_number_clienta DESC"; break;
		}
		
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':school_id', $school_id_userid, PDO::PARAM_STR);
		if ($datebetween) {
			$stmt->bindValue(':start_date', $search_start);
			$stmt->bindValue(':end_date', $search_end);
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
	die('Connection failed: ' . $e->getMessage());
}finally {
    $pdo = null;
}

//ファイル名
	$filename = "sow/chat/download/chatdata-".$school_id_userid.".csv";
	if (!$fp = fopen($filename, 'w')) {
		echo "Cannot open file ($filename)";
		exit;
	}
try{
	$head = 'ネットワーク番号,送信時間,送信者名,送信番号,サーバ番号,受信番号,グループ,データの種類,重要,繰返回数,パスワード,内容';
	// head書き込み
	fwrite($fp, mb_convert_encoding($head . "\n", "SJIS"));
	foreach ($rows as $val) {
		// 出力用
		$output_text  = '';
		//ネットワーク番号
		$output_text .= $val['chat_group_clienta'];
		//送信時間
		$output_text .= ',' . date("y/m/d H:i:s", strtotime($val['time_clienta']));
		//送信者名
		$output_text .= ',' . $val['client_a_name_clienta'];
		//送信番号
		$output_text .= ',' . $val['client_a_clienta'];
		//サーバ番号
		$output_text .= ',' . $val['server_clienta'];
		//受信番号
		$output_text .= ',' . $val['client_b_clienta'];
		//グループ
		$output_text .= ',' . $val['group_clienta'];
		//データの種類
		$output_text .= ',' . $val['data_kind_clienta'];
		//重要
		if ($val['juyo_clienta'] == "false") {
			$output_text .= ',' . "";
		} else {
			$output_text .= ',' . "〇";
		}
		//繰返回数
		$output_text .= ',' . $val['repeat_cnt_clienta'];
		//パスワード
		$output_text .= ',' . $val['password_clienta'];
		//内容
		if ($val['data_kind_clienta'] == "プログラム") {
			$medata = explode("=", $val['message_st_clienta']); //分解
			$output_text .= ',' . $medata[0];
		} else if ($val['data_kind_clienta'] == "スタンプ") {
			$medata = explode(".", $val['message_st_clienta']); //分解
			$output_text .= ',' . "スタンプ番号 " . $medata[0];
		} else {
			$output_text .= ',' . $val['message_st_clienta'];
		}
		$output_text .= '';
		$output_text .= "\n";

		if (fwrite($fp, mb_convert_encoding($output_text, "SJIS")) === FALSE) {
			break;
		}
	}

}catch (PDOException $e) {
    print "[ERROR] {{$e->getMessage()}}\n";
    die();
}
fclose($fp);
  /* download_file関数実行 */
  download_file($filename);

//ファイルをダ削除
	if (file_exists($filename)) {
		// ダウンロード後にZIPファイルを削除する場合
		unlink($filename);
		exit;
	} else {
		echo 'ファイルが存在しません。';
	}
?>