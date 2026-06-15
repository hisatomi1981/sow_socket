<?php

//------------------------------------------------------
//　id passの入力フォーム
//------------------------------------------------------
//　管理者用
function get_idpass_form_html_administrator()
{
	echo "<div class=\"row\" style=\"height: 50px\"></div>\n";
	echo "<div class=\"col t_center list\">\n";
	echo "<form action=index_administrator.php method=POST>";
	echo "<input type=hidden name=\"mode\" value=\"certification\">";
	echo "<table border=1>\n";
	echo "<tr>\n";
	echo "<th>ID番号</th>\n";
	echo "<td class=\"backgroundcolor_white\"><input type=text name=\"id_number_kanri\" size=30></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<th>パスワード</th>\n";
	echo "<td class=\"backgroundcolor_white\"><input type=password name=\"pass_kanri\" size=30></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan=\"2\" class=\"backgroundcolor_white\"><input type=submit value=\"ログイン\"></td>\n";
	echo "</tr>\n";
	echo "</table>";
	echo "</form>";
	echo "</div>";
	echo "<div class=\"row justify-content-center\">";
	echo "<div class=\"col t_center\">\n";
	echo "ID番号、パスワードを入力し「ログイン」ボタンを押して下さい。";
	echo "</div>";
	echo "</div>";
	echo "<br>";
	echo "</div>\n";
}

//　教員、生徒用
function get_idpass_form_html_user(){
    echo <<<HTML
	<div style="
		background-color: #fff9e6;       /* 薄い黄色（注意喚起） */
		border: 4px solid #ff9800;       /* 太めのオレンジ色（目立たせる） */
		border-radius: 8px;              /* 角を少し丸く */
		padding: 12px 15px;              /* 内側の余白 */
		margin: 15px auto;               /* 上下に余白を作り、左右中央寄せ */
		max-width: 550px;                /* 幅をスマホサイズに合わせて小さく設定 */
		text-align: center;              /* 文字を中央寄せ */
		font-family: sans-serif;         /* 見やすいゴシック体 */
		box-shadow: 0 2px 6px rgba(0,0,0,0.15); /* 少し影をつけて浮かせる */
		">
		<div style="
			font-weight: bold;
			color: #e65100;
			margin-bottom: 8px;
			font-size: 15px;
		">
			⚠️ ご案内
		</div>
		<p style="
			margin: 0;
			font-size: 13px;
			color: #333;
			line-height: 1.6;
		">
			サンプルなどのお試しや個人での利用は
			<a href="https://www.hisatomi-kk.com/app/" style="
			color: #007bff;
			font-weight: bold;
			text-decoration: underline;
			font-size: 14px;
			">
			「お試し版・個人利用」
			</a>をご利用ください。
		</p>
	</div>

	<div class="row" style="height: 0px"></div>
	<h2 class="login-title">ログイン</h2>
	<div class="row justify-content-center">
		<div class="col t_center">
			アプリをご利用になる方はこちらからログインしてください。
		</div>
	</div>
	
	<div class="row" style="height: 5px"></div>

	<div class="col t_center list">
		<form action="index.php" method="POST">
			<input type="hidden" name="mode" value="certification">

			<table>
				<tr>
					<th>ID番号</th>
					<td>
						<input type="text" name="id_number_userid" size="30" required autocomplete="ID">
					</td>
				</tr>
				<tr>
					<th>パスワード</th>
					<td>
						<input type="password" name="pass_userid" size="30" required autocomplete="パスワード">
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" value="ログイン">
					</td>
				</tr>
			</table>
		</form>
	</div>

	<div class="row justify-content-center">
		<div class="col t_center">
			ID番号、パスワードを入力し「ログイン」ボタンを押してください。
		</div>
	</div>
	HTML;
}

//------------------------------------------------------
//　ナビゲーション
//------------------------------------------------------
function get_navi_html()
{
	echo "<nav class=\"navbar fixed-top navbar-expand-lg navbar-light bg-light\"> <a class=\"navbar-brand\" href=\"https://www.hisatomi-kk.com\"><img src=\"https://www.hisatomi-kk.com/topImage/templete/logo.png\" width=\"170\" height=\"43\" alt=\"\"/></a>\n";
	echo "<button class=\"navbar-toggler\" type=\"button\" data-toggle=\"collapse\" data-target=\"#navbarSupportedContent1\" aria-controls=\"navbarSupportedContent1\" aria-expanded=\"false\" aria-label=\"Toggle navigation\"> <span class=\"navbar-toggler-icon\"></span> </button>\n";
	echo "<div class=\"collapse navbar-collapse\" id=\"navbarSupportedContent1\">\n";
	echo "<ul class=\"navbar-nav mr-auto\">\n";
	echo "<li class=\"nav-item dropdown\"> <a class=\"nav-link dropdown-toggle\" href=\"https://www.hisatomi-kk.com/seihin/submenu/product/Produt.htm\" id=\"navbarDropdown1\" role=\"button\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\"> 製品情報 </a>\n";
	echo "<div class=\"dropdown-menu\" aria-labelledby=\"navbarDropdown1\">\n";
	echo "<a class=\"dropdown-item\" href=\"https://www.hisatomi-kk.com/seihin/submenu/johoetc/joho.html\">情報教材</a>\n";
	echo "<div class=\"dropdown-divider\"></div>\n";
	echo "<a class=\"dropdown-item\" href=\"https://www.hisatomi-kk.com/seihin/submenu/generation/generation.html\">エネルギー変換(ダイナモ)</a> \n";
	echo "<a class=\"dropdown-item\" href=\"https://www.hisatomi-kk.com/seihin/submenu/dc/denti.html\">エネルギー変換(DC)</a>\n";
	echo "<div class=\"dropdown-divider\"></div>\n";
	echo "<a class=\"dropdown-item\" href=\"https://www.hisatomi-kk.com/SET-1/index.html\">アプリケーション</a>\n";
	echo "<div class=\"dropdown-divider\"></div>\n";
	echo "<a class=\"dropdown-item\" href=\"https://www.hisatomi-kk.com/seihin/submenu/tap/tap.html\">テーブルタップ</a>\n";
	echo "<div class=\"dropdown-divider\"></div>\n";
	echo "<a class=\"dropdown-item\" href=\"https://www.hisatomi-kk.com/seihin/submenu/kote/kote.html\">半田ごて</a>\n";
	echo "</div>\n";
	echo "</li>\n";
	echo "<li class=\"nav-item\"> <a class=\"nav-link\" href=\"https://www.hisatomi-kk.com/DownLoad/index.html\">ダウンロード</a> </li>\n";
	echo "<li class=\"nav-item\"> <a class=\"nav-link\" href=\"https://www.hisatomi-kk.com/Information/kiyaku.html\">ご注文</a> </li>\n";
	echo "<li class=\"nav-item\"> <a class=\"nav-link\" href=\"https://www.hisatomi-kk.com/Movie/index.html\">動画</a> </li>\n";
	echo "<li class=\"nav-item\"> <a class=\"nav-link\" href=\"https://www.hisatomi-kk.com/Information/kaishaannai/kaishaannai.html\">会社案内</a> </li>\n";
	echo "<li class=\"nav-item\"> <a class=\"nav-link\" href=\"https://www.hisatomi-kk.com/Information/otioawase.html\">お問い合わせ</a> </li>\n";
	echo "</ul>\n";
	echo "</div>\n";
	echo "</nav>\n";
}

//------------------------------------------------------
//　指定文字を超えたら...で表示する文字列取得 $st:元の文字列　$cnt:バイト数(日本語は3バイト)
//------------------------------------------------------
/*function get_disp_text($st, $cnt){
	if (strlen($st) > $cnt) {
		return substr($st, 0, $cnt) . "...";
	} else {
		return $st;
	}
}*/
function get_disp_text($st, $cnt){
    if (mb_strlen($st) > $cnt){
        return mb_substr($st, 0, $cnt)."...";
    }
    else{return $st; }
}

//------------------------------------------------------
//　文字列を$cnt文字ずつ配列に入れる(配列の要素数は2個まで)レーダーチャートの項目名用 $st:元の文字列　$cnt:1行にいれるバイト数(日本語は3バイト) 
//　$gyou：何個の配列にするか（行数）
//　$blankno：指定の行数に満たなかった時、空白をどこに入れるか 0:先頭　1:最後
//------------------------------------------------------
function split_string($str, $cnt, $gyou, $blank)
{
	$result = array();
	$length = strlen($str);
	$start = 0;
	while ($start < $length && count($result) < $gyou) {
		$chunk = substr($str, $start, $cnt);
		$result[] = $chunk;
		$start += $cnt;
	}

	if (count($result) < $gyou) {
		for ($i = 0; $i < $gyou - count($result); $i++) {
			if ($blank == "0") {
				array_unshift($result, " ");
			} else {
				array_push($result, " ");
			}
		}
	}
	//文字数が配列数$gyouにおさまりきれなかったら...をつける
	if ($length > ($cnt * $gyou) && count($result) == $gyou) {
		$result[count($result) - 1] = substr($result[count($result) - 1], 0, $cnt - 3) . "...";
	}
	return $result;
}

//------------------------------------------------------
//　// 禁止期間内かどうかを判断する関数
//　$start_hour：禁止開始時間
//　$end_hour：禁止終了時間
//　$current_hour：現在の"時"
//------------------------------------------------------
function isTimeWithinBanPeriod($start_hour, $end_hour, $current_hour) {
	// ★ 明示的に整数化
	$start_hour   = (int)$start_hour;
	$end_hour     = (int)$end_hour;
	$current_hour = (int)$current_hour;

	// 禁止時間が日付を跨ぐ場合
	if ($start_hour > $end_hour) {
		return $current_hour >= $start_hour || $current_hour < $end_hour;
	} else {
		// 禁止時間が同じ日に含まれる場合
		return $current_hour >= $start_hour && $current_hour < $end_hour;
	}
}

//------------------------------------------------------
//　ファイルのダウンロード
//------------------------------------------------------
function download_file($path_file){
	/* ファイルの存在確認 */
	if (!file_exists($path_file)) {
        die("Error: File(".$path_file.") does not exist");
    }
    /* オープンできるか確認 */
    if (!($fp = fopen($path_file, "r"))) {
        die("Error: Cannot open the file(".$path_file.")");
    }
    fclose($fp);
    /* ファイルサイズの確認 */
    if (($content_length = filesize($path_file)) == 0) {
        die("Error: File size is 0.(".$path_file.")");
    }
    /* ダウンロード用のHTTPヘッダー送信 */
    header("Cache-Control: private");
    header("Pragma: private");
    header('Content-Description: File Transfer');
    header("Content-Disposition: inline; filename=\"".basename($path_file)."\"");
    header("Content-Length: ".$content_length);
    header("Content-Type: application/octet-stream");
    header('Content-Transfer-Encoding: binary');
    /* ファイルを読んで出力 */
	if (!readfile($path_file)) {
		die("Cannot read the file(".$path_file.")");
	}
}