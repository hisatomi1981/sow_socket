<?php
if (!isset($_POST['school_id_userid'])) {
	require_once("../login/function/errorhtml.php");
	get_direct_access_error_html();
	exit;
} else {
	$school_id_userid = $_POST['school_id_userid'];
	$school_name_userid = $_POST['school_name_userid'];
	$login_time = isset($_POST['login_time']) ? (int)$_POST['login_time'] : time();//ログイン時刻
	require_once("common/common.php");
}
?>
<!DOCTYPE html>
<html>

<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-YSL4X6DJY0"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());

		gtag('config', 'G-YSL4X6DJY0');
	</script>
	<title>読み込み中（サーバ）</title>
	<link href="css/bootstrap-5.3.3.css" rel="stylesheet" type="text/css">
	<link href="css/design.css" rel="stylesheet" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="js/popper.min.js" type="text/javascript"></script><!--bootstrapとセット（先に読み込む）-->
  	<script src="js/bootstrap-5.3.3.js" type="text/javascript"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/pbkdf2.js"></script>
	<!-- Blocklyのライブラリ -->
	<script src="js/blockly_compressed.js"></script>
	<script src="js/blocks_compressed.js"></script>
	<script src="js/javascript_compressed.js"></script>
	<script src="msg/js/ja.js"></script>
	<!-- 自作ブロックの定義 -->
	<script src="js/myblock_Stub.js"></script>
	<script src="js/myblock_Definition.js"></script>
	<script src="js/workspace.js" defer></script>
	<script src="js/function.js"></script>
	<script src="js/function_contents.js"></script>
	<script src="js/function_data.js"></script>
	<script src="js/function_display.js"></script>
	<script src="js/function_file.js"></script>
	<script src="js/function_server.js"></script>
	<script src="js/function_message_ipad.js"></script>
	<script src="js/sound.js"></script>
	<?php require_once('common/head_scripts.php'); ?>
	<script src="js/main.js"></script>
	<script src="js/save_report.js"></script>
	<script src="js/html2canvas.js"></script>
</head>

<body style="padding-top: 70px" oncontextmenu="return false;">
	<!-- ワークスペースのエリア -->
	<div class="container-fluid">		
		<div id="cli_send">
			<div class="row justify-content-center">
				<div class="col-sm-8">
					クライアント<input class="s_c_textarea_seitono" type="text" id="client_sendno" size="6">　番に
					メッセージ　<input class="s_c_textarea" type="text" id="client_sendst" size="16">　を送る　
					<input type="checkbox" name="juyocheckbox" id="s_c_juyo" value="重要">重要
					<a class="s_c_send" href="#" onClick="client_send_message()">送信</a>
					<a class="s_c_cancel" href="#" onClick="printNo_cli_send()">キャンセル</a>
				</div>
			</div><br>
		</div>		
		<?php 
			renderPrintHeadUI('blocklyDiv', $school_id_userid);
			renderSetGroupHeadUI();
			renderSetupNetworkNoHeadUI();
		?>

		<div class="row justify-content-right" id="head_area">
			<div class="col-md-2 tanto_title">　　　サーバ　　　</div>
			<div class="col-md-10"> <a class="riyokaisi text-decoration-none" href="#" onClick="startchat('<?php echo addslashes(htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8')) ?>', '<?php echo addslashes(htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8')) ?>')" id="systemstart">利用開始</a>
				<div class="dispinfo"><span id="netnoid">ネットワーク番号</span></div>
				<span id="seitonotext">生徒番号：
					<input class="textarea_seitono" type="text" id="myno" size="4" onBlur="leaveno()">
					<span class="font16 fontbold" id="studentno"></span>
				</span>
				<a class="sendbtn text-decoration-none" href="#" onclick="set_program()" id="play_set">プログラムセット</a>：<span id="settime"></span>
			</div>
		</div>
		<div class="row" style="height: 5px"></div>
		<div class="row justify-content-right">
			<div class="col-sm-6" id="blocklyDiv" style="height: 450px;"></div>
			<div class="col-sm-4 m_area">
				<form name="form6"><textarea readonly class="logarea" name='logtextarea' id='messages' placeholder="通信ログ："></textarea></form>
			</div>
			<div class="col-sm-2">
				禁止ワード<br><input type="text" id="kinsiword" size="10"><button onClick="setkinsiword()">登録</button>
				<form name="form4"><textarea readonly class="textarea_kinsi" name='textarea3' id='kinsiText'></textarea></form>
				登録ワード<br><input type="text" id="torokuword" size="10"><button onClick="settorokuword()">登録</button>
				<form name="form5"><textarea readonly class="textarea_toroku" name='textarea4' id='torokuText'></textarea></form>
			</div>
		</div>
		<div class="row">
			<div class="col-auto upload_back" id="programarea">
				プログラムデータを送る場合のファイルを選択します<br>
				事前に作成したファイル(LED・メロディ)を選択して下さい。<br>
				ファイル１：<input type="file" name="img" accept=".cuc7,.cuc7f,.cuc7w,.cuc9,.cuc9f,.cuc9w,.ucsd,.ud1,.ud1f,.ud1w,.udsd" id="pro_file1"><br>
				ファイル２：<input type="file" name="img" accept=".cuc7,.cuc7f,.cuc7w,.cuc9,.cuc9f,.cuc9w,.ucsd,.ud1,.ud1f,.ud1w,.udsd" id="pro_file2"><br>
				ファイル３：<input type="file" name="img" accept=".cuc7,.cuc7f,.cuc7w,.cuc9,.cuc9f,.cuc9w,.ucsd,.ud1,.ud1f,.ud1w,.udsd" id="pro_file3"><br>
			</div>
		</div>
		<div class="row justify-content-right">
			<!-- <form name="form2"><textarea style="display:none;" name='textarea1' id='proText'>プログラム</textarea> -->
			<form name="form2"><textarea style="display:none;" name='textarea1' id='proText'>プログラム</textarea></form>
			<!-- <img src="img/uc7.png" width="175" height="189" alt=""/> -->
			<form name="form3"><textarea style="display:none;" name='textarea2' id='sendText'>転送データ</textarea></form>
		</div>
		<div class="row justify-content-right">
			<canvas id="saveboard" width="800px" style="display: none;"></canvas>
		</div>
	</div>
	<div id="restoreModal" class="modal" onclick="pressCancelRestoreBlocks(event)">
		<div class="modalWindow" style="width: 480px">
			<div class="modalHeader">
				開きたいプログラムを選択してください。
			</div>
			<ul id="restoreList">
			</ul>
			<div class="modalFooter">
				<a onclick="cancelRestoreBlocks()">キャンセル</a>
			</div>
		</div>
	</div>

	<!-- ツールボックスの定義 -->
	<xml xmlns="https://developers.google.com/blockly/xml" id="toolbox" style="display: none">
		<category name="制御" colour="#ffbf00">
			<block type="server_start"></block>
			<block type="if_yes"></block>
			<block type="if_else"></block>
			<block type="server_if_block_kinsi"></block>
			<block type="server_if_block_notkinsi"></block>
			<block type="server_if_block_toroku"></block>
			<block type="server_if_block_nottoroku"></block>
			<block type="server_if_block_juyo"></block>
			<block type="server_if_block_notjuyo"></block>
			<block type="server_if_block_pass"></block>
			<block type="server_if_block_notpass"></block>
			<block type="server_if_block_perno">
				<field name="perno">1</field>
			</block>
			<block type="server_if_block_notperno">
				<field name="perno">1</field>
			</block>
			<block type="server_if_block_group"></block>
			<block type="server_if_block_notgroup"></block>
			<block type="server_if_block_stlength">
				<field name="perno">10</field>
			</block>
			<block type="server_if_block_notstlength">
				<field name="perno">10</field>
			</block>
			<block type="server_if_block_myname"></block>
			<block type="server_if_block_notmyname"></block>
			<block type="server_if_block_loopcnt"></block>
			<block type="server_if_block_notloopcnt"></block>
		</category>
		<category name="送信" colour="#00aaff">
			<block type="server_send"></block>
			<block type="server_error"></block>
			<block type="server_return"></block>
			<block type="server_programsend"></block>
			<block type="server_group_programsend"></block>
			<block type="server_programreturn"></block>
			<!--<block type="server_changeai_return"></block>-->
		</category>
		<!--<category name="AI" colour="#506373">
			<block type="server_ai_message_change">
				<field name="changest"></field>
			</block>			
			<block type="server_ai_question_anser"></block>
		</category>-->
		<category name="設定" colour="#92117D">
			<block type="server_pass_set">
				<field name="passst">1234</field>
			</block>
		</category>
		<category name="変数" colour="#9e9065">
			<block type="server_variable_1"></block>
			<block type="server_variable_2"></block>
			<block type="server_variable_3"></block>
			<block type="server_variable_4"></block>
			<block type="server_variable_5"></block>
		</category>
	</xml>
	<script>
		//Blocklyをdivにはめ込み
		var toolbox = document.getElementById("toolbox");
		var options = {
			toolbox: toolbox,
			maxBlocks: Infinity,
			css: true,
			sounds: false,
			zoom : { 
				wheel : true, 
				startScale : 1, 
				maxScale : 3, 
				minScale : 0.3, 
				scaleSpeed : 1.2
			}
		};
		var workspace = Blockly.inject('blocklyDiv', options);

		document.getElementById("cli_send").style.display = "none";
		document.getElementById("setgroup").style.display = "none";
		document.getElementById("printhead").style.display = "none";
		document.getElementById("setup_networkno").style.display = "none";
		document.getElementById("print-koso").style.display = "none";
		document.getElementById("print-siyo").style.display = "none";
		document.getElementById("print-kuhu").style.display = "none";
		document.getElementById("print-kanso").style.display = "none";

		document.getElementById('myno').value = localStorage.getItem("seitono");
		document.getElementById('kinsiText').value = localStorage.getItem("kinsiword");
		document.getElementById('torokuText').value = localStorage.getItem("torokuword");
		document.getElementById('network_no1').value = localStorage.getItem("networkno1");
		document.getElementById('network_no2').value = localStorage.getItem("networkno2");
		document.getElementById('network_no3').value = localStorage.getItem("networkno3");
		document.getElementById('network_no4').value = localStorage.getItem("networkno4");
		if (document.getElementById('network_no1').value == "") {
			document.getElementById('network_no1').value = "192";
		}
		if (document.getElementById('network_no2').value == "") {
			document.getElementById('network_no2').value = "168";
		}

		//ファイル読み込み用
		window.addEventListener('DOMContentLoaded', function(){
	    var ele = document.getElementById("readfile");
	    ele.addEventListener('change', function(ev){
			var file = ev.target.files[0];
			var filename = file.name.split('.');
			var reader = new FileReader();
			if (filename[1] == "wanet"){
			  reader.readAsText(file, 'UTF-8');
			}
			reader.onload = function(e){				
				if (reader.result.substr(0,4) == "<xml" ){
					uploadCode(reader.result);				  
				}		  
				document.getElementById("readfile").value = "";
			}
	    }, false);
		});
	  //ログ読み込み用
		window.addEventListener('DOMContentLoaded', function(){
	    var ele = document.getElementById("readlog");
	    ele.addEventListener('change', function(ev){
			var file = ev.target.files[0];
			var filename = file.name.split('.');
			var reader = new FileReader();
			if (filename[1] == "log"){
			  reader.readAsText(file, 'UTF-8');
			}
			reader.onload = function(e){
				read_log_ipad(reader.result);	
				document.getElementById("readlog").value = "";
			}
	    }, false);
		});
	</script>

	<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light">
		<span id="schooid">双方向ネットワークアプリ(iPad)</span> ：
		<span id="dispstate">停止</span>

		<button class="navbar-toggler" type="button"
				data-bs-toggle="collapse"
				data-bs-target="#navbarSupportedContent1"
				aria-controls="navbarSupportedContent1"
				aria-expanded="false"
				aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent1">
			<ul class="navbar-nav mx-auto">

			<!-- ▼ ファイル -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownFile" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				ファイル
				</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownFile">
				<li><a class="dropdown-item" onclick="saveCode()" href="#">ブラウザへ保存</a></li>
				<li><a class="dropdown-item" onclick="restoreBlocks()" href="#">ブラウザから読み込み</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" onclick="downloadCode_ipad_s()" href="#" id="filesave">ファイルへ保存</a></li>

				<li>
					<label class="dropdown-item read_label">
					<form name="test">ファイルから読み込み
						<input type="file" id="readfile" hidden>
					</form>
					</label>
				</li>
				</ul>
			</li>

			<!-- ▼ グループ -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownGroup" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				グループ
				</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownGroup">
				<li><a class="dropdown-item" onclick="groupDisplay()" href="#">グループ設定</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" href="help/group.pdf" target="_blank">グループ送信手順</a></li>
				</ul>
			</li>

			<!-- ▼ 編集 -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownEdit" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				編集
				</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownEdit">
				<li><a class="dropdown-item" onclick="delete_kinsi()" href="#">禁止ワード削除</a></li>
				<li><a class="dropdown-item" onclick="delete_toroku()" href="#">登録ワード削除</a></li>
				</ul>
			</li>

			<!-- ▼ 通信ログ -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownLog" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				通信ログ
				</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownLog">
				<li class="px-3 py-1">
					<input type="checkbox" id="log_enc" value="2" onchange="check_Encrypt()"> ログの暗号化
				</li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" onclick="savelog_ipad()" href="#" id="logsave">ログの保存</a></li>

				<li>
					<label class="dropdown-item read_label">
					<form name="test1">ログの読み込み
						<input type="file" id="readlog" accept=".txt" hidden>
					</form>
					</label>
				</li>
				</ul>
			</li>

			<!-- ▼ その他 -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownOther" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				その他
				</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownOther">
				<li><a class="dropdown-item" onclick="print_cli_send()" href="#">クライアントへ送信</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" onclick="printDisplay()" href="#">レポート作成</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" onclick="change_network_no_Display()" href="#">設定</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" onclick="display_net_no()" href="#">利用中のネットワーク番号</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" onclick="cash_clear_all()" href="#">すべてのキャッシュをクリア</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" href="help/server.pdf" target="_blank">ヘルプ</a></li>
				<li><a class="dropdown-item" href="help/procedure_b_s.pdf" target="_blank">操作手順</a></li>
				</ul>
			</li>

			<!-- ▼ 動画 -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownMovie" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				動画
				</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownMovie">
				<li><a class="dropdown-item" href="https://youtu.be/o8IumegiS-o" target="_blank">ネットワーク番号について</a></li>
				<li><a class="dropdown-item" href="https://youtu.be/hXgj2OgqTBs" target="_blank">3人で通信</a></li>
				<li><a class="dropdown-item" href="https://youtu.be/uhk4r2AFrMQ" target="_blank">3人で通信（禁止ワード編）</a></li>
				<li><a class="dropdown-item" href="https://youtu.be/0l2Hp59nM6A" target="_blank">3人で通信（パスワード編）</a></li>
				</ul>
			</li>

			</ul>
		</div>
	</nav>

	<script>
		// set_Interval() 互換ラッパ
		function set_Interval() { startServerPolling(); }

		systemstart.addEventListener('click', function() {
			startServerPolling();
		});

		//自動保存したプログラムの呼び出し
		server_autosavefile_read();

		//プログラムデータを送るのファイルが変更されたら
			var profile_data1 = "";
			var profile_data2 = "";
			var profile_data3 = "";
			var profile_data_filename1 = "";
			var profile_data_filename2 = "";
			var profile_data_filename3 = "";

			/**
			 * 指定されたファイルの内容を読み込み、必要なデータ部分を抽出後、
			 * ファイルインデックスに応じた変数に格納する関数
			 * @param {File} file - 読み込む対象のFileオブジェクト
			 * @param {number} index - 対象のファイル入力インデックス（1～3）
			 */
			function processFile(file, index) {
				var reader = new FileReader();
				reader.readAsText(file);  // ファイルをテキスト形式として読み込む

				// 読み込み完了後の処理
				reader.onload = function(ev) {
					var content = ev.target.result;  // ファイル全体の内容
					var sendPos = content.indexOf("SEND=");
					var startPos = content.indexOf("START");
					var dataPart = "";

					if (sendPos !== -1) {  // 「SEND=」が見つかった場合のみ処理
						if (startPos === -1) {
							// 「START」がない場合、SEND=からファイル末尾までを抽出（末尾から1文字分を除く）
							dataPart = content.substring(sendPos, content.length - 1);
						} else {
							// 「SEND=」から「START」直前までを抽出
							dataPart = content.substring(sendPos, startPos);
						}
						// 「SEND=」を除去して実データ部分のみを抽出
						dataPart = dataPart.replace("SEND=", "");
					} else {
						console.warn("選択されたファイル " + file.name + " に「SEND=」が存在しません。");
					}

					// 各ファイルインデックスに応じた変数へ格納
					switch (index) {
						case 1:
							profile_data1 = dataPart;
							profile_data_filename1 = file.name;
							break;
						case 2:
							profile_data2 = dataPart;
							profile_data_filename2 = file.name;
							break;
						case 3:
							profile_data3 = dataPart;
							profile_data_filename3 = file.name;
							break;
					}
					// デバッグ用：処理完了の確認用コンソール出力
					//console.log("Processed file " + index + ": " + file.name);
					//console.log("Extracted data:", dataPart);
				};
			}

			// 各ファイル入力要素に対してイベントリスナーを設定
			document.getElementById("pro_file1").addEventListener("change", function(evt) {
				var files = evt.target.files;
				if (files.length > 0) {
					processFile(files[0], 1);
				}
			}, false);

			document.getElementById("pro_file2").addEventListener("change", function(evt) {
				var files = evt.target.files;
				if (files.length > 0) {
					processFile(files[0], 2);
				}
			}, false);

			document.getElementById("pro_file3").addEventListener("change", function(evt) {
				var files = evt.target.files;
				if (files.length > 0) {
					processFile(files[0], 3);
				}
			}, false);
			
		// ===== タブ非表示で自動停止 =====
		document.addEventListener('visibilitychange', function() {
		if (document.hidden) {
		} else {
			startServerPolling();
		}
		});
		
		document.title = "読み込み完了（サーバ）";
	</script>
	<!-- ─── 自動ログアウト用カウントダウン ──────────────────────── -->
	<script>
		(function() {
			var loginTime = <?php echo $login_time; ?>;
			var timeout   = 7200; // 2時間（秒）
			var warned10  = false;

			// 1秒ごとではなく「現在時刻」で判定する
			setInterval(function() {
				var now       = Math.floor(Date.now() / 1000);
				var remaining = timeout - (now - loginTime);

				if (remaining <= 0) {
					alert('セッションの有効期限が切れました。再度ログインしてください。');
					location.href = '../../index.php';
					return;
				}
				if (!warned10 && remaining <= 600) {
					warned10 = true;
					alert('ログインから有効期限まで残り10分です。');
				}
			}, 30000); // 30秒ごとにチェック（スロットリングされても確実）
		})();
	</script>
</body>

</html>