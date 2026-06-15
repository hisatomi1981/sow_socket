<?php
//------------------------------------------------------
//　ログイン後のメニュー画面
//------------------------------------------------------
function app_menu_header($kubun_userid, $kengen_userid){
	$seigyo_arr = array(
		array("No" => "1", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "2", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "5", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "6", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "11", "key" => "uc9", "kataban" => "UC-9", "name" => "オーロラキュート(UC-9/10)"),
		array("No" => "12", "key" => "uc9", "kataban" => "UC-9", "name" => "オーロラキュート(UC-9/10)"),
		array("No" => "31", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "32", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "3", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)"),
		array("No" => "4", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)"),
		array("No" => "7", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)"),
		array("No" => "8", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)"),
		array("No" => "9", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)ver3"),
		array("No" => "10", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)ver3"),
		array("No" => "33", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)"),
		array("No" => "34", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)"),
		array("No" => "35", "key" => "am1", "kataban" => "AM-1", "name" => "AM-1/2　オーロラミニライト"),
		array("No" => "36", "key" => "am1", "kataban" => "AM-1", "name" => "AM-1/2　オーロラミニライト"),
		array("No" => "51", "key" => "sow", "kataban" => "SOW-5", "name" => "双方向ネットワークアプリ(SOW-5)"),
		array("No" => "61", "key" => "sow", "kataban" => "SOW-5", "name" => "双方向ネットワークアプリ(SOW-5)"),
	);

	$modelkey ="";
	$modelKataban ="";
	$modelName ="";
	foreach ($seigyo_arr as $item) {
		if ($item["No"] == $kengen_userid){
			$modelkey = $item["key"];
			$modelKataban = $item["kataban"];
			$modelName =$item["name"];
		}
	}

	echo "<div class=\"seihinwaku\">\n";
	echo "<div class=\"seihintitle\"><strong>HISATOMIアプリ</strong></div>\n";
	echo "<div class=\"seihinkataban\"><strong>WAシリーズ</strong></div>\n";
	echo "</div>\n";
	echo "<div class=\"row\" style=\"height: 20px\"></div>\n";
	//タイトル
		echo "<div class=\"watitle\">{$modelName}　\n";
		echo "</div>";
		echo "<div class=\"row\" style=\"height: 20px\"></div>\n";
	//商品画像
		echo "<div class=\"row justify-content-center\">\n";    
			if ($kengen_userid <> "51" && $kengen_userid <> "61"){
				echo "<img src=\"https://www.hisatomi-kk.com/app/{$modelkey}/Img/{$modelkey}.png\" height=\"150\" alt=\"\"/>\n"; 
			}
			else{
				echo "<h5 class=\"text-primary\">Chromebook、Windows、iPadに対応しております。</h5>\n"; 
			}
		echo "</div>\n";	
		echo "<div class=\"row\" style=\"height: 20px\"></div>\n";
	//資料
		if ($kengen_userid <> "51" && $kengen_userid <> "61"){
			echo "<div class=\"row justify-content-center\">\n";
				if ($kubun_userid == "0"){
					echo "<div class=\"col-auto t_center btn-menu-radius-document_t font_size_15\"><strong>\n";
					echo "<a href=\"https://www.hisatomi-kk.com/document/joho/{$modelKataban}/{$modelKataban}.html\" target=\"_blank\"><span class=\"font_color_white\">（教員用、解答あり）取扱説明書、動画解説、ワークなどの関連資料はこちら</span></a>\n";
					echo "</strong></div>\n";
				}
				else{
					echo "<div class=\"col-auto t_center btn-menu-radius-document font_size_15\"><strong>\n";
					echo "<a href=\"https://www.hisatomi-kk.com/document/student/infomaterials/{$modelKataban}/{$modelKataban}.html\" target=\"_blank\"><span class=\"font_color_white\">取扱説明書、動画解説、ワークなどの関連資料はこちら</span></a>\n";
					echo "</strong></div>\n";
				}
			echo "</div>\n";
			echo "<div class=\"row\" style=\"height: 20px\"></div>\n";
		}
}
function sow_menu_header($kubun_userid, $kengen_userid){
	$seigyo_arr = array(
		array("No" => "51", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "61", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
	);

	$modelkey ="";
	$modelKataban ="";
	$modelName ="";
	foreach ($seigyo_arr as $item) {
		if ($item["No"] == $kengen_userid){
			$modelkey = $item["key"];
			$modelKataban = $item["kataban"];
			$modelName =$item["name"];
		}
	}

	echo "<div class=\"seihinwaku\">\n";
	echo "<div class=\"seihintitle\"><strong>HISATOMIアプリ</strong></div>\n";
	echo "<div class=\"seihinkataban\"><strong>WAシリーズ</strong></div>\n";
	echo "</div>\n";
	echo "<div class=\"row\" style=\"height: 20px\"></div>\n";
	//タイトル
		echo "<div class=\"watitle\">{$modelName}　\n";
		echo "</div>";
		echo "<div class=\"row\" style=\"height: 20px\"></div>\n";
	//商品画像
		echo "<div class=\"row justify-content-center\">\n";    
			echo "<img src=\"https://www.hisatomi-kk.com/app/{$modelkey}/Img/{$modelkey}.png\" height=\"150\" alt=\"\"/>\n"; 
		echo "</div>\n";	
		echo "<div class=\"row\" style=\"height: 20px\"></div>\n";
	//資料
		echo "<div class=\"row justify-content-center\">\n";
			if ($kubun_userid == "0"){
				echo "<div class=\"col-auto t_center btn-menu-radius-document_t font_size_15\"><strong>\n";
				echo "<a href=\"https://www.hisatomi-kk.com/document/joho/{$modelKataban}/{$modelKataban}.html\" target=\"_blank\"><span class=\"font_color_white\">（教員用、解答あり）取扱説明書、動画解説、ワークなどの関連資料はこちら</span></a>\n";
				echo "</strong></div>\n";
			}
			else{
				echo "<div class=\"col-auto t_center btn-menu-radius-document font_size_15\"><strong>\n";
				echo "<a href=\"https://www.hisatomi-kk.com/document/student/infomaterials/{$modelKataban}/{$modelKataban}.html\" target=\"_blank\"><span class=\"font_color_white\">取扱説明書、動画解説、ワークなどの関連資料はこちら</span></a>\n";
				echo "</strong></div>\n";
			}
		echo "</div>\n";
	echo "<div class=\"row\" style=\"height: 20px\"></div>\n";
}
function app_menu($school_id_userid,$school_name_userid,$kubun_userid,$kengen_userid,$id_number_userid){	
	$kengen_userid_next = (int)$kengen_userid + 1;

	echo <<<HTML
		<div class="row justify-content-center">    
			<h5>ご使用の端末を選択して下さい。 </h5>   
		</div>
		<div class="row justify-content-center ">
			<div class="col-md-2">
			</div>
			<div class="col-md-4 t_center">
				<form action="index.php" method="POST">
					<input type="hidden" name="mode" value="menu">
					<input type="hidden" name="school_id_userid" value="{$school_id_userid}">
					<input type="hidden" name="school_name_userid" value="{$school_name_userid}">
					<input type="hidden" name="kengen_userid" value="{$kengen_userid}">
					<input type="hidden" name="kubun_userid" value="{$kubun_userid}">
					<input type="hidden" name="id_number_userid" value="{$id_number_userid}">
					<button type="submit" class="btn-menu-radius">
						<img src="https://www.hisatomi-kk.com/app/ud1ver2/Img/note.png" width="100" height="74" alt=""/><br>Chromebook<br>Windows
					</button>
				</form>
			</div>
			<div class="col-md-4 t_center">
				<form action="index.php" method="POST">
					<input type="hidden" name="mode" value="menu">
					<input type="hidden" name="school_id_userid" value="{$school_id_userid}">
					<input type="hidden" name="school_name_userid" value="{$school_name_userid}">
					<input type="hidden" name="kengen_userid" value="{$kengen_userid_next}">
					<input type="hidden" name="kubun_userid" value="{$kubun_userid}">
					<input type="hidden" name="id_number_userid" value="{$id_number_userid}">
					<button type="submit" class="btn-menu-radius">
						<img src="https://www.hisatomi-kk.com/app/ud1ver2/Img/ipad.png" width="67" height="100" alt=""/><br>iPad<br>
					</button>
				</form>
			</div> 
			<div class="col-md-2">
			</div>
		</div>
	HTML;
}

//------------------------------------------------------
//　教員なら 双方向管理システム
//------------------------------------------------------
function teacher_btn($school_id_userid,$school_name_userid,$id_number_userid){
	echo "<div class=\"row\" style=\"height: 30px\"></div>\n";
	echo "<h3>双方向通信管理システム</h3>\n";
	echo "<div class=\"row\" style=\"height: 1px\"></div>\n";
	echo "<div class=\"row justify-content-center\">\n";
		echo "<form action=\"licence.php\" method=\"POST\" target=\"_blank\">\n";
		echo "<input type=\"hidden\" name=\"mode\" value=\"list\">\n";
		echo "<input type=\"hidden\" name=\"school_id_userid\" value=\"" . htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8') . "\">\n";
		echo "<input type=\"hidden\" name=\"school_name_userid\" value=\"" . htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8') . "\">\n";
		echo "<input type=\"hidden\" name=\"id_number_userid\" value=\"" . htmlspecialchars($id_number_userid, ENT_QUOTES, 'UTF-8') . "\">\n";
		echo "<input type=\"hidden\" name=\"offset\" value=\"0\">\n";
		echo "<input type=\"submit\" value=\"ユーザ情報\" class=\"btn_menu_manage\">\n";
		echo "</form>\n";
	echo "</div>\n";

	echo "<div class=\"row\" style=\"height: 1px\"></div>\n";
	echo "<div class=\"row justify-content-center\">\n";
		echo "<div class=\"col-md-4 t_center\">\n";
			echo "<form action=\"chat_list.php\" method=\"POST\" target=\"_blank\">\n";
			echo "<input type=\"hidden\" name=\"mode\" value=\"list\">\n";
			echo "<input type=\"hidden\" name=\"school_id_userid\" value=\"" . htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8') . "\">\n";
			echo "<input type=\"hidden\" name=\"school_name_userid\" value=\"" . htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8') . "\">\n";
			echo "<input type=\"hidden\" name=\"id_number_userid\" value=\"" . htmlspecialchars($id_number_userid, ENT_QUOTES, 'UTF-8') . "\">\n";
			echo "<input type=\"hidden\" name=\"offset\" value=\"0\">\n";
			echo "<input type=\"submit\" value=\"チャットデータ\" class=\"btn_menu_manage\">\n";
			echo "</form>\n";
		echo "</div>\n";
		echo "<div class=\"col-md-4 t_center\">\n";
			echo "<form action=\"report/report_list.php\" method=\"POST\" target=\"_blank\">\n";
			echo "<input type=\"hidden\" name=\"mode\" value=\"list\">\n";
			echo "<input type=\"hidden\" name=\"school_id_userid\" value=\"" . htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8') . "\">\n";
			echo "<input type=\"hidden\" name=\"school_name_userid\" value=\"" . htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8') . "\">\n";
			echo "<input type=\"hidden\" name=\"id_number_userid\" value=\"" . htmlspecialchars($id_number_userid, ENT_QUOTES, 'UTF-8') . "\">\n";
			echo "<input type=\"hidden\" name=\"offset\" value=\"0\">\n";
			echo "<input type=\"submit\" value=\"レポートデータ(ログ)\" class=\"btn_menu_manage\">\n";
			echo "</form>\n";
		echo "</div>\n";
	echo "</div>\n";
}

//------------------------------------------------------
//　レポート閲覧
//------------------------------------------------------
function report_list($school_id_userid,$school_name_userid,$id_number_userid){	
	echo "<div class=\"row\" style=\"height: 30px\"></div>\n";
	echo "<h3>レポート</h3>\n";
	echo "<div class=\"row\" style=\"height: 1px\"></div>\n";
	echo "<div class=\"row justify-content-center\">\n";
		echo "<form action=\"report/myReport_view_list.php\" method=\"POST\" target=\"_blank\">\n";
			echo "<input type=\"hidden\" name=\"mode\" value=\"new\">\n";
			echo "<input type=\"hidden\" name=\"school_id_userid\" value=\"" . htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8') . "\">\n";
			echo "<input type=\"hidden\" name=\"school_name_userid\" value=\"" . htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8') . "\">\n";
			echo "<input type=\"hidden\" name=\"id_number_userid\" value=\"" . htmlspecialchars($id_number_userid, ENT_QUOTES, 'UTF-8') . "\">\n";
			echo "<input type=\"hidden\" name=\"offset\" value=\"0\">\n";
			echo "<input type=\"submit\" value=\"レポートデータ\" class=\"btn_menu_manage\">\n";
		echo "</form>\n";
	echo "</div>\n";
}

//------------------------------------------------------
//　時刻設定
//------------------------------------------------------
function time_uc($model){
	$filename = "";
	if ($model == "win"){
		$filename = "index";
	}
	else{
		$filename = "ipad";
	}
	echo "<div class=\"row\" style=\"height: 20px\"></div>\n";
	echo "<h3>■時刻設定 </h3>\n";
	echo "<div class=\"row justify-content-center \">\n";
	echo "  <div class=\"t_center\"><a href=\"<?php echo SEIGYO_URL; ?>app/uc7settime/{$filename}.php\" target=\"_blank\" class=\"btn-gradient-radius btn-gradient-radius-word\"><br>時刻・アラーム設定<br><br></a></div>\n";
	echo "</div>\n";
	echo "<div class=\"row justify-content-center \">\n";
	echo "  <a href=\"https://www.hisatomi-kk.com/Information/news/clocksetup.html\">オーロラクロック / キュートを目覚まし時計として使う方法</a>\n";
	echo "</div>\n";
}
function time_ud($model){
	$filename = "";
	if ($model == "win"){
		$filename = "index";
	}
	else{
		$filename = "ipad";
	}
	echo "<div class=\"row\" style=\"height: 30px\"></div>\n";
	echo "<h3>■時刻設定 </h3>\n";
	echo "<div class=\"row justify-content-center \">\n";
	echo "  <div class=\"t_center\"><a href=\"<?php echo SEIGYO_URL; ?>app/ud1settime/{$filename}.php\" target=\"_blank\" class=\"btn-gradient-radius btn-gradient-radius-word\"><br>時刻・アラーム設定<br><br></a></div>\n";
	echo "</div>\n";
}

//------------------------------------------------------
//　タイトル部
//------------------------------------------------------
function app_header($kubun_userid, $kengen_userid){
	$seigyo_arr = array(
		array("No" => "1", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "2", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "5", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "6", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "31", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "32", "key" => "uc7", "kataban" => "UC-7", "name" => "オーロラクロック2N(UC-7/8)"),
		array("No" => "3", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)"),
		array("No" => "4", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)"),
		array("No" => "7", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)"),
		array("No" => "8", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)"),
		array("No" => "9", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)ver3"),
		array("No" => "10", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)ver3"),
		array("No" => "33", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)"),
		array("No" => "34", "key" => "ud1", "kataban" => "UD-1", "name" => "オーロラクロック3(UD-1/2)"),
		array("No" => "35", "key" => "am1", "kataban" => "AM-1", "name" => "AM-1/2　オーロラミニライト"),
		array("No" => "36", "key" => "am1", "kataban" => "AM-1", "name" => "AM-1/2　オーロラミニライト"),
		array("No" => "37", "key" => "at2", "kataban" => "AT-2", "name" => "AT-2　オーロラトーチ2"),
		array("No" => "38", "key" => "hr1", "kataban" => "HR-1", "name" => "HR-1　アクティくん"),
		array("No" => "39", "key" => "lc12", "kataban" => "LC-12", "name" => "LC-12/24　オーロラスタンド"),
		array("No" => "11", "key" => "uc9", "kataban" => "UC-9", "name" => "オーロラクキュート(UC-9/10)"),
		array("No" => "12", "key" => "uc9", "kataban" => "UC-9", "name" => "オーロラクキュート(UC-9/10)"),
	);

	$modelkey ="";
	$modelKataban ="";
	$modelName ="";
	foreach ($seigyo_arr as $item) {
		if ($item["No"] == $kengen_userid){
			$modelkey = $item["key"];
			$modelKataban = $item["kataban"];
			$modelName =$item["name"];
		}
	}

	echo "<div class=\"seihinwaku\">\n";
	echo "<div class=\"seihintitle\"><strong>HISATOMIアプリ</strong></div>\n";
	echo "<div class=\"seihinkataban\"><strong>WAシリーズ</strong></div>\n";
	echo "</div>\n";
	echo "<div class=\"row\" style=\"height: 10px\"></div>\n";
	//タイトル
		echo "<div class=\"watitle\">{$modelName}　\n";
			if ($kengen_userid == "2" || $kengen_userid == "4" || 
				$kengen_userid == "6" || $kengen_userid == "8" ||  
				$kengen_userid == "10" || $kengen_userid == "32" ||  
				$kengen_userid == "34" || $kengen_userid == "36" || 
				$kengen_userid == "12"){
				echo "iPad用";
			}
			else{
				echo "Chromebook、Windows用";
			}
		echo "</div>";
		echo "<div class=\"row\" style=\"height: 10px\"></div>\n";
	//商品画像
		echo "<div class=\"row justify-content-center\">\n";    
			echo "<img src=\"https://www.hisatomi-kk.com/app/{$modelkey}/Img/{$modelkey}.png\" height=\"150\" alt=\"\"/>\n"; 
		echo "</div>\n";	
	//対応機種
		echo "<div class=\"row justify-content-center\">\n";
			echo "<div class=\"col-md-4 t_center btn-menu-radius font_size_15\"><strong>【対応機種】</strong><br>\n";
			if ($kengen_userid == "2" || $kengen_userid == "4" || 
				$kengen_userid == "6" || $kengen_userid == "8" ||  
				$kengen_userid == "10" || $kengen_userid == "32" ||  
				$kengen_userid == "34" || $kengen_userid == "36" || 
				$kengen_userid == "12"){
				echo "iPad\n";
			}
			else{
				echo "Chromebook<br>\n";
				echo "Windows\n";
			}
			echo "</div>\n";  
		echo "</div>\n";
	//6つの設定
		if ($kengen_userid == "2" || $kengen_userid == "4" || 
			$kengen_userid == "6" || $kengen_userid == "8" ||  
			$kengen_userid == "10" || $kengen_userid == "32" ||  
			$kengen_userid == "34" || $kengen_userid == "36" || 
			$kengen_userid == "12"){
			echo <<<HTML
			<div class="row justify-content-center">  
				<div class="col-md-8 t_center btn-menu-radius-setup font_size_15">
					<strong>
						<a href="https://www.hisatomi-kk.com/app/Img/confirm.pdf" target="_blank">
							<span class="font_color_white">データ転送前の６つの設定</span>
						</a>
					</strong>
					<a href="https://www.hisatomi-kk.com/app/Img/confirm.pdf"><br>
						<span class="font_color_white">（iPad本体の設定を確認して下さい）</span>
					</a>
				</div>   
			</div>
			HTML;
		}
	//資料
		echo "<div class=\"row justify-content-center\">\n";
			if ($kubun_userid == "0"){
				echo "<div class=\"col-auto t_center btn-menu-radius-document_t font_size_15\"><strong>\n";
				echo "<a href=\"https://www.hisatomi-kk.com/document/joho/{$modelKataban}/{$modelKataban}.html\" target=\"_blank\"><span class=\"font_color_white\">（教員用、解答あり）取扱説明書、動画解説、ワークなどの関連資料はこちら</span></a>\n";
				echo "</strong></div>\n";
			}
			else{
				echo "<div class=\"col-auto t_center btn-menu-radius-document font_size_15\"><strong>\n";
				echo "<a href=\"https://www.hisatomi-kk.com/document/student/infomaterials/{$modelKataban}/{$modelKataban}.html\" target=\"_blank\"><span class=\"font_color_white\">取扱説明書、動画解説、ワークなどの関連資料はこちら</span></a>\n";
				echo "</strong></div>\n";
			}
		echo "</div>\n";
	echo "<div class=\"row\" style=\"height: 20px\"></div>\n";
}

function sow_header($kengen_userid){//1:Chromebook、Windows用 2:ipad用
	echo "<div class=\"seihinwaku\">\n";
	echo "<div class=\"seihintitle\"><strong>HISATOMIアプリ</strong></div>\n";
	echo "<div class=\"seihinkataban\"><strong>WAシリーズ</strong></div>\n";
	echo "</div>\n";
	echo "<div class=\"row\" style=\"height: 10px\"></div>\n";
	echo "<div class=\"watitle\">双方向ネットワークアプリ(SOW-5)\n";
		if ($kengen_userid == "61" || $kengen_userid == "62"){
			echo "　Chromebook、Windows用";
		}
		if ($kengen_userid == "52" || $kengen_userid == "62"){
			echo "　iPad用";
		}
		else{
			echo "　Chromebook、Windows用";
		}
	echo "</div>";
	echo "<div class=\"row\" style=\"height: 10px\"></div>\n";
	echo "<div class=\"row justify-content-center\">\n";
		echo "<div class=\"col-md-4 t_center btn-menu-radius font_size_15\"><strong>【対応機種】</strong><br>\n";
		if ($kengen_userid == "52" || $kengen_userid == "62"){
				echo "iPad\n";
			}
			else{
				echo "Chromebook<br>\n";
				echo "Windows\n";
			}
		echo "</div>\n";  
	echo "</div>\n";
}
//------------------------------------------------------
//　制御部
//------------------------------------------------------
define('SEIGYO_URL', 'https://app.hisatomi.net/');
define('SOW_URL', '');
//define('SEIGYO_URL', '');
//define('SOW_URL', 'https://sow.hisatomi.net/');

function uc_ud_seigyo($kengen_userid,$school_id_userid, $school_name_userid){
	$model_name = "";
	$sound_name = "";
	if ($kengen_userid == "1" || $kengen_userid == "2" ||
		$kengen_userid == "31" || $kengen_userid == "32"){
		$model_name = "uc7";
		$sound_name = "sounduc";
	}
	else if ($kengen_userid == "5" || $kengen_userid == "6"){
		$model_name = "uc7_ai";
		$sound_name = "sounduc";
	}
	else if ($kengen_userid == "11" || $kengen_userid == "12"){
		$model_name = "uc9";
		$sound_name = "sounduc";
	}
	else if ($kengen_userid == "7" || $kengen_userid == "8"){
		$model_name = "ud1_ai";
		$sound_name = "soundud";
	}
	else if ($kengen_userid == "9" || $kengen_userid == "10"){
		$model_name = "ud1_ai_ver3";
		$sound_name = "soundud";
	}
	else{
		$model_name = "ud1";
		$sound_name = "soundud";
	}
	$filename = "";
	if ($kengen_userid == "1" || $kengen_userid == "5" ||
		$kengen_userid == "3" || $kengen_userid == "7" || $kengen_userid == "9" || 
		$kengen_userid == "11" || $kengen_userid == "31" || $kengen_userid == "33"){
		$filename = "index";
	}
	else{
		$filename = "ipad";
	}
	?>
	<h3>■計測制御 </h3>
	<?php
	if ($kengen_userid != "11" & $kengen_userid != "12"){
		?>
		<div class="row justify-content-center">
			<div class="col-md-8 t_center btn-menu-radius-preview">
				<div class="font_size_15 font_color_red">
				<strong><span>★シミュレーション機能の追加★</span></strong>
				</div>
				<div>
				<strong>
					作成したプログラムを事前にチェック<br>
					情報技術との関わりの強化やシステムを構想する際ご活用ください。
				</strong>
				</div>
			</div>
		</div>
		<?php
	}
	?>
	<div class="row justify-content-center">
		<a href="https://www.hisatomi-kk.com/document/joho/UC-7/document/check.pdf" target="_blank">
			組立後は動作確認行って下さい。正しく動作しない場合はこちらを参考にして動作確認を行ってください。
		</a>
	</div>
	<div class="row" style="height: 20px"></div>
	<?php
	if ($kengen_userid == "1" || $kengen_userid == "2" || $kengen_userid == "5" || $kengen_userid == "6" || 
		$kengen_userid == "7" || $kengen_userid == "8" || $kengen_userid == "9" || $kengen_userid == "10"){
		?>
		<div class="row justify-content-center ">
			<div class="col-md-4 t_center">
				<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $model_name; ?>/icon/<?php echo $filename; ?>.php" target="_blank" method="POST">
					<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<button type="submit" class="btn-gradient-radius">
						<img src="https://www.hisatomi-kk.com/app/uc7/Img/icon.png" width="92" height="66"><br>
						アイコン
					</button>
				</form>
			</div>
			<div class="col-md-4 t_center">
				<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $model_name; ?>/block/<?php echo $filename; ?>.php" target="_blank" method="POST">
					<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<button type="submit" class="btn-gradient-radius">
						<img src="https://www.hisatomi-kk.com/app/uc7/Img/block.png" width="92" height="66"><br>
						ブロック
					</button>
				</form>
			</div>
			<div class="col-md-4 t_center">
				<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $model_name; ?>/flow/<?php echo $filename; ?>.php" target="_blank" method="POST">
					<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<button type="submit" class="btn-gradient-radius">
						<img src="https://www.hisatomi-kk.com/app/uc7/Img/flow.png" width="43" height="67"><br>
						フローチャート
					</button>
				</form>
			</div>
		</div>
		<div class="row justify-content-center ">
			<div class="col-md-4 t_center">
				<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $model_name; ?>/word/<?php echo $filename; ?>.php" target="_blank" method="POST">
					<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<button type="submit" class="btn-gradient-radius">
						<img src="https://www.hisatomi-kk.com/app/uc7/Img/word.png" width="50" height="69"><br>
						文字
					</button>
				</form>
			</div>
			<div class="col-md-4 t_center">
				<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $sound_name; ?>/block/<?php echo $filename; ?>.php" target="_blank" method="POST">
					<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<button type="submit" class="btn-gradient-radius">
						<br>
						<img src="https://www.hisatomi-kk.com/app/uc7/Img/sound.png" width="155" height="25"><br>
						<br>メロディ
					</button>
				</form>
			</div>
			<div class="col-md-4 t_center">
			</div>
		</div>
		<?php
	}
	else{
		?>
		<div class="row justify-content-center ">
			<div class="col-md-4 t_center">
				<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $model_name; ?>/block/<?php echo $filename; ?>.php" target="_blank" method="POST">
					<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<button type="submit" class="btn-gradient-radius">
						<img src="https://www.hisatomi-kk.com/app/uc7/Img/block.png" width="92" height="66"><br>
						ブロック
					</button>
				</form>
			</div>
			<div class="col-md-4 t_center">
				<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $model_name; ?>/flow/<?php echo $filename; ?>.php" target="_blank" method="POST">
					<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<button type="submit" class="btn-gradient-radius">
						<img src="https://www.hisatomi-kk.com/app/uc7/Img/flow.png" width="43" height="67"><br>
						フローチャート
					</button>
				</form>
			</div>
			<div class="col-md-4 t_center">
				<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $model_name; ?>/word/<?php echo $filename; ?>.php" target="_blank" method="POST">
					<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<button type="submit" class="btn-gradient-radius">
						<img src="https://www.hisatomi-kk.com/app/uc7/Img/word.png" width="50" height="69"><br>
						文字
					</button>
				</form>
			</div>
		</div>
		<div class="row justify-content-center ">
			<div class="col-md-4 t_center">
				<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $sound_name; ?>/block/<?php echo $filename; ?>.php" target="_blank" method="POST">
					<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<button type="submit" class="btn-gradient-radius">
						<img src="https://www.hisatomi-kk.com/app/uc7/Img/sound.png" width="155" height="25"><br>
						<br>メロディ
					</button>
				</form>
			</div>
			<div class="col-md-4 t_center">
			</div>
			<div class="col-md-4 t_center">
			</div>
		</div>
		<?php
	}
}

function etc_seigyo($kengen_userid,$school_id_userid, $school_name_userid){
	$devicename = "";
	if ($kengen_userid == "35" || $kengen_userid == "36"){
		$devicename = "am1";
	}
	else if ($kengen_userid == "37"){
		$devicename = "at2";
	}
	else if ($kengen_userid == "38"){
		$devicename = "hr1";
	}
	else if ($kengen_userid == "39"){
		$devicename = "lc12";
	}

	$filename = "";
	if ($kengen_userid == "36"){
		$filename = "ipad";
	}
	else{
		$filename = "index";
	}
	?>
	<h3>■計測制御 </h3>
	<div class="row justify-content-center ">
		<div class="col-md-4 t_center">
			<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $devicename; ?>/block/<?php echo $filename; ?>.php" target="_blank" method="POST">
				<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
				<button type="submit" class="btn-gradient-radius">
					<img src="https://www.hisatomi-kk.com/app/uc7/Img/block.png" width="92" height="66"><br>
					ブロック
				</button>
			</form>
		</div>
		<div class="col-md-4 t_center">			
			<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $devicename; ?>/flow/<?php echo $filename; ?>.php" target="_blank" method="POST">
				<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
				<button type="submit" class="btn-gradient-radius">
					<img src="https://www.hisatomi-kk.com/app/uc7/Img/flow.png" width="43" height="67"><br>
					フローチャート
				</button>
			</form>
		</div>
	</div>
	<?php 
	//AM、LCならメロディ表示
	if ($kengen_userid == "35" || $kengen_userid == "36" || $kengen_userid == "39"){
		$foldername = "";
		if ($kengen_userid == "35" || $kengen_userid == "36"){
			$foldername = "soundud";
		}
		else{
			$foldername = "sounduc";
		}
		?>
		<div class="row justify-content-center ">
			<div class="col-md-4 t_center">
				<form action="<?php echo SEIGYO_URL; ?>app/<?php echo $foldername; ?>/block/<?php echo $filename; ?>.php" target="_blank" method="POST">
					<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
					<button type="submit" class="btn-gradient-radius">
						<img src="https://www.hisatomi-kk.com/app/uc7/Img/sound.png" width="155" height="25"><br>
						<br>メロディ
					</button>
				</form>
			</div>
			<div class="col-md-4 t_center">
			</div>
		</div>
		<?php 
	}
	?>
	<?php
}

function sensor_data($kengen_userid,$school_id_userid, $school_name_userid){
	?>
	<div class="row" style="height: 20px"></div>
	<h3>センサ計測 </h3>
	<div class="row justify-content-center ">
		<div class="col-md-4 t_center">
			<form action="<?php echo SEIGYO_URL; ?>app/sensor_Measurement/upload.php" target="_blank" method="POST">
				<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
				<button type="submit" class="btn-gradient-radius btn-gradient-radius-melody">									
					<br>センサで計測<br><br>
				</button>
			</form>
		</div>
		<div class="col-md-4 t_center">
			<form action="<?php echo SEIGYO_URL; ?>app/sensor_Measurement/view.php" target="_blank" method="POST">
				<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="kengen_userid" value="<?php echo htmlspecialchars($kengen_userid, ENT_QUOTES, 'UTF-8'); ?>">
				<button type="submit" class="btn-gradient-radius btn-gradient-radius-melody">									
					<br>計測値をみる<br><br>
				</button>
			</form>
		</div>
	</div>
	<?php 
}

//------------------------------------------------------
//SOW、オーロラクロック2、3のネットワーク
//------------------------------------------------------
function aurora_sow_network($kengen_userid, $school_id_userid, $school_name_userid){
	$devicename = "";
	$cli_filename = "";
	$ser_filename = "";
	$folder_name = "";
	//TeachableMachineの時
	if ($kengen_userid == "5" || $kengen_userid == "6" || $kengen_userid == "7" || $kengen_userid == "8" || 
		$kengen_userid == "9" || $kengen_userid == "10" || $kengen_userid == "61" || $kengen_userid == "62"){
		$folder_name = "sow_ai_tm";
	}
	//SOWの時
	else{
		$folder_name = "sow";
	}		

	if ($kengen_userid == "51" || $kengen_userid == "61"){
		$devicename =  "SOW";
		$cli_filename =  "sow";
		$ser_filename =  "sow";
	}
	else if ($kengen_userid == "52" || $kengen_userid == "62"){
		$devicename =  "SOW(iPad)";
		$cli_filename =  "sow_ipad";
		$ser_filename =  "ipad";
	}
	else if ($kengen_userid == "1" || $kengen_userid == "5" || $kengen_userid == "31"){
		$devicename =  "UC-7/8";
		$cli_filename =  "uc";
		$ser_filename =  "sow";
	}
	else if ($kengen_userid == "2" || $kengen_userid == "6" || $kengen_userid == "32"){
		$devicename =  "UC-7/8(iPad)";
		$cli_filename =  "uc_ipad";
		$ser_filename =  "ipad";
	}
	else if ($kengen_userid == "11"){
		$devicename =  "UC-9/10";
		$cli_filename =  "uc";
		$ser_filename =  "sow";
	}
	else if ($kengen_userid == "12"){
		$devicename =  "UC-9/10(iPad)";
		$cli_filename =  "uc_ipad";
		$ser_filename =  "ipad";
	}
	else if ($kengen_userid == "3" || $kengen_userid == "7" || $kengen_userid == "9" || $kengen_userid == "33"){
		$devicename =  "UD-1/2";
		$cli_filename =  "ud";
		$ser_filename =  "sow";
	}
	else if ($kengen_userid == "4" || $kengen_userid == "8" || $kengen_userid == "10" || $kengen_userid == "34"){
		$devicename =  "UD-1/2(iPad)";
		$cli_filename =  "uc_ipad";
		$ser_filename =  "ipad";
	}
	?>
	<div class="row" style="height: 20px"></div>
	<h3>双方向通信アプリ　<?php echo $devicename; ?></h3>
	<div><h4>２人でメッセージ通信</h4></div>
	<div class="row justify-content-center">
		<img src="https://www.hisatomi-kk.com/app/Img/2p.png" width="391" height="94">
	</div>
	<div class="row">
		<div class="tantou_title_client">クライアント担当</div>
	</div>
	<div class="row justify-content-center">
		<div class="col-md-2 t_center">
		</div>
		<div class="col-md-4 t_center">
			<form action="<?php echo SOW_URL.$folder_name; ?>/chat/block_client_<?php echo $cli_filename; ?>.php" target="_blank" method=POST>
			<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8') ?>">
			<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8') ?>">
			<button type="submit" class="btn_menu_client">
			<img src="https://www.hisatomi-kk.com/app/uc7/Img/block.png" width="92" height="66">
			<br>
			クライアント(ブロック)
			</button>
			</form>
		</div>
		<div class="col-md-4 t_center">
			<form action="<?php echo SOW_URL.$folder_name; ?>/chat/flow_client_<?php echo $cli_filename; ?>.php" target="_blank" method=POST>
			<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8') ?>">
			<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8') ?>">
			<button type="submit" class="btn_menu_client">
			<img src="https://www.hisatomi-kk.com/app/uc7/Img/flow.png" width="43" height="67">
			<br>
			クライアント(フロー)
			</button>
			</form>
		</div>
		<div class="col-md-2 t_center">
		</div>
	</div>
	<div class="row" style="height: 20px"></div>
	<div><h4>３人以上でメッセージ通信</h4></div>
	<div class="row justify-content-center">
		<img src="https://www.hisatomi-kk.com/app/Img/3p.png" width="399" height="127">
	</div>
	<div class="row">
		<div class="tantou_title_client">クライアント担当</div>
	</div>
	<div class="row justify-content-center">
		<div class="col-md-2 t_center">
		</div>
		<div class="col-md-4 t_center">
			<form action="<?php echo SOW_URL.$folder_name; ?>/chat/block_client_<?php echo $cli_filename; ?>.php" target="_blank" method=POST>
			<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8') ?>">
			<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8') ?>">
			<button type="submit" class="btn_menu_client">
			<img src="https://www.hisatomi-kk.com/app/uc7/Img/block.png" width="92" height="66">
			<br>
			クライアント(ブロック)
			</button>
			</form>
		</div>
		<div class="col-md-4 t_center">
			<form action="<?php echo SOW_URL.$folder_name; ?>/chat/flow_client_<?php echo $cli_filename; ?>.php" target="_blank" method=POST>
			<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8') ?>">
			<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8') ?>">
			<button type="submit" class="btn_menu_client">
			<img src="https://www.hisatomi-kk.com/app/uc7/Img/flow.png" width="43" height="67">
			<br>
			クライアント(フロー)
			</button>
			</form>
		</div>
		<div class="col-md-2 t_center">
		</div>
	</div>
	<div class="row">
		<div class="tantou_title_server">　サーバ担当　</div>
	</div>
	<div class="row justify-content-center">
		<div class="col-md-2 t_center">
		</div>
		<div class="col-md-4 t_center">
			<form action="<?php echo SOW_URL.$folder_name; ?>/chat/block_server_<?php echo $ser_filename; ?>.php" target="_blank" method=POST>
			<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8') ?>">
			<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8') ?>">
			<button type="submit" class="btn_menu_server">
			<img src="https://www.hisatomi-kk.com/app/uc7/Img/block.png" width="92" height="66">
			<br>
			サーバ(ブロック)
			</button>
			</form>
		</div>
		<div class="col-md-4 t_center">
			<form action="<?php echo SOW_URL.$folder_name; ?>/chat/flow_server_<?php echo $ser_filename; ?>.php" target="_blank" method=POST>
			<input type="hidden" name="school_id_userid" value="<?php echo htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8') ?>">
			<input type="hidden" name="school_name_userid" value="<?php echo htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8') ?>">
			<button type="submit" class="btn_menu_server">
			<img src="https://www.hisatomi-kk.com/app/uc7/Img/flow.png" width="43" height="67">
			<br>
			サーバ(フロー)
			</button>
			</form>
		</div>
		<div class="col-md-2 t_center">
		</div>
	</div>
	<?php
}

?>