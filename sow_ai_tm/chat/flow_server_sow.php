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
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-EWVD8X8MBK"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());

		gtag('config', 'G-EWVD8X8MBK');
	</script>
	<title>読み込み中（サーバ）</title>
	<link href="css/bootstrap-5.3.3.css" rel="stylesheet" type="text/css">
	<link href="css/design.css" rel="stylesheet" type="text/css">
	<link href="css/style.css" rel="stylesheet" type="text/css">
	<link href="css/jquery-ui.min.css" rel="stylesheet" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="js/popper.min.js" type="text/javascript"></script><!--bootstrapとセット（先に読み込む）-->
  	<script src="js/bootstrap-5.3.3.js" type="text/javascript"></script>

	<!-- Copyright 1998-2021 by Northwoods Software Corporation. -->
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<script src="jsfglow/jquery.min.js"></script>
	<script src="jsfglow/jquery-ui.min.js"></script>
	<script src="jsfglow/Define_server.js"></script>
	<script src="jsfglow/flowchart.js"></script>
	<script src="jsfglow/function.js"></script>
	<script src="jsfglow/function_contents.js"></script>
	<script src="jsfglow/function_data.js"></script>
	<script src="jsfglow/function_display.js"></script>
	<script src="jsfglow/function_file.js"></script>
	<script src="jsfglow/function_flow_server.js"></script>
	<script src="jsfglow/function_message.js"></script>
	<script src="jsfglow/function_server.js"></script>
	<script src="js/sound.js"></script>
	<script src="jsfglow/DataInspector.js"></script>
	<script src="jsfglow/function_share.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script><!--暗号化、復号化のライブラリ-->
	<?php require_once('common/head_scripts.php'); ?>
	<script src="js/main.js"></script>
	<script src="js/save_report.js"></script>
	<script src="js/html2canvas.js"></script>
</head>

<body onload="init()" style="padding-top: 70px">
	<div class="container-fluid" id="droppable">	
		<!-- 印刷、グループ -->
			<?php 
				rendercli_sendHeadUI();
				renderPrintHeadUI('myDiagramDiv',$school_id_userid);
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
				<a class="sendbtn text-decoration-none" href="#" onclick="set_program_flow_server()" id="play_set">プログラムセット</a>
				：<span id="settime"></span>
			</div>
		</div>
		<div class="row" style="height: 5px"></div>
		<div class="row">
			<div class="col-sm-6" style="padding: 0px;">
				<div style="width: 100%; display: flex; justify-content: space-between">
					<div id="paletteDraggable" style="width: 200px; height: 450px; margin-right: 2px; background-color: #f8f8f8">
						<div id="paletteDraggableHandle"></div>
						<div id="myPaletteDiv"></div>
					</div>
					<div id="myDiagramDiv" style="flex-grow: 1; height: 450px; background-color: #ededed"></div>

					<div id="infoDraggable">
						<div id="myInfo"></div>
					</div>
				</div>
			</div>
			<div class="col-sm-4 m_area" style="padding: 0px 10px;">
				<div style="height: 140px; background-color: #FFDDC6">
					<a href="#" class="updatebtn" id="para_btn" onClick="kousin()" style="display:none;">更新</a>
					<div id="server_Message" style="display:none;">
						<select name="select" id="paraservermessage" class="select" onchange="change_pulldown(paraservermessage)">
							<option value="メッセージを送る">メッセージを送る</option>
						</select>
						<span id="parablock_server_program">
							ファイル：
							<select name="select" id="para_server_fileprogram" class="selectgroup">
								<option value="ファイル1">ファイル１</option>
								<option value="ファイル2">ファイル２</option>
								<option value="ファイル3">ファイル３</option>
							</select>
						</span>
						<span id="parablock_server_sendno">
							送信先番号：<input type="text" id="para_server_sendno" size="5">
						</span>
						<span id="parablock_server_sendgroup">
							グループ：
							<select name="select" id="para_server_group" class="selectgroup">
								<option value="グループ1">グループ１</option>
								<option value="グループ2">グループ２</option>
								<option value="グループ3">グループ３</option>
							</select>
						</span>
					</div>
					<div id="para_server_error" style="display:none;">
						<select name="select" id="paraservererror" class="select" onchange="change_pulldown(paraservererror)">
							<option value="エラーを返信">エラーを返信</option>
							<option value="変数ーを返信">変数を返信</option>
							<!--<option value="AIで変換したメッセージを返す">AIで変換したメッセージを返す</option>-->
						</select>
						<span id="parablock_server_error">
							エラー内容：<input type="text" id="server_m_para_error" size="20">
						</span>
						<span id="parablock_server_rvari">
							変数：
							<select name="select" id="paraserverrvari" class="selectgroup">
								<option value="変数1">変数１</option>
								<option value="変数2">変数２</option>
								<option value="変数3">変数３</option>
								<option value="変数4">変数４</option>
								<option value="変数5">変数５</option>
								<option value="変数(ランダム)">変数(ランダム)</option>
							</select>
						</span>
						<span id="parablock_server_rprogram">
							ファイル：
							<select name="select" id="paraserverrprogram" class="selectgroup">
								<option value="ファイル1">ファイル１</option>
								<option value="ファイル2">ファイル２</option>
								<option value="ファイル3">ファイル３</option>
							</select>
						</span>
					</div>
					<div id="para_server_aichange" style="display:none;">
						<select name="select" id="paraserveraichange" class="select" onchange="change_pulldown(paraserveraichange)">
							<option value="AIでメッセージを変換">AIでメッセージを変換</option>
							<option value="AIでメッセージの質問を取得">AIでメッセージの質問を取得</option>
						</select>
						<span id="parablock_server_aichange">
							メッセージを
							<input type="text" id="server_ai_change_st" size="10" placeholder="">
							に変換
						</span>
					</div>
					<div id="para_server_variable" style="display:none;">
						<select name="select" id="paraservervariable" class="select" onchange="change_pulldown(paraservervariable)">
							<option value="変数1">変数１</option>
							<option value="変数2">変数２</option>
							<option value="変数3">変数３</option>
							<option value="変数4">変数４</option>
							<option value="変数5">変数５</option>
						</select>
						<span id="parablock_server_variable">
							変数に入れる文字：<input type="text" id="variable_para_server_st" size="6">
						</span>
					</div>
					<div id="para_server_setup" style="display:none;">
						<select name="select" id="paraserversetup" class="select" onchange="change_pulldown(paraserversetup)">
							<option value="ﾊﾟｽﾜｰﾄﾞを設定する">パスワードを設定する</option>
						</select>
						<span id="parablock_server_pass">
							パスワード：<input type="text" id="setup_para_server_pass" size="6">
						</span>
					</div>
					<div id="para_server_if" style="display:none;">
						<select name="select" id="paraserverif" class="select" onchange="change_pulldown(paraserverif)">
							<option value="禁止ワードがある">禁止ワードがある?</option>
							<option value="禁止ワードがない">禁止ワードがない?</option>
							<option value="登録ワードがある">登録ワードがある?</option>
							<option value="登録ワードがない">登録ワードがない?</option>
							<option value="パスワードが一致?">パスワードが一致?</option>
							<option value="パスワードが不一致?">パスワードが不一致?</option>
							<option value="繰返回数が回以上?">繰返回数が設定値以上?</option>
							<option value="繰返回数が回以下?">繰返回数が設定値以下?</option>
							<option value="重要なら">重要なら</option>
							<option value="重要でないなら">重要でないなら</option>
							<option value="特定の生徒番号?">特定の生徒番号?</option>
							<option value="特定の生徒番号でない?">特定の生徒番号でない?</option>
							<option value="文字数が文字以上?">文字数が設定値以上?</option>
							<option value="文字数が文字以下?">文字数が設定値以下?</option>
							<option value="送信者の名前がある?">送信者の名前がある?</option>
							<option value="送信者の名前がない?">送信者の名前がない?</option>
							<option value="送信者がグループにいる?">送信者がグループにいる?</option>
							<option value="送信者がグループにいない?">送信者がグループにいない?</option>
							<!--<option value="AIが判断?">AIが判断?</option>-->
						</select>
						<span id="parablock_server_ifaijudgment">
							判断：<input type="text" id="if_para_server_aijudgment" size="10">
						</span>
						<span id="parablock_server_ifseitono">
							生徒番号：<input type="text" id="if_para_server_seitono" size="4">
						</span>
						<span id="parablock_server_ifstlength">
							文字数：<input type="text" id="if_para_server_stlength" size="2">
						</span>
						<span id="parablock_server_ifrepeatcnt">
							繰り返し回数：<input type="text" id="if_para_server_strepeatcnt" size="2">
						</span>
						<span id="parablock_server_group">
							グループ：
							<select name="select" id="if_para_server_group" class="selectgroup">
								<option value="グループ1">グループ1</option>
								<option value="グループ2">グループ2</option>
								<option value="グループ3">グループ3</option>
							</select>
						</span>
					</div>
				</div>
				<div style="height: 5px;"></div>
				<form name="form6"><textarea style="height: 305px;" readonly class="logarea" name='logtextarea' id='messages' placeholder="通信ログ："></textarea></form>
			</div>
			<div class="col-sm-2" style="padding: 5px;">
				禁止ワード<br><input type="text" id="kinsiword" size="10"><button onClick="setkinsiword()">登録</button>
				<form name="form4"><textarea readonly class="textarea_kinsi" name='textarea3' id='kinsiText'></textarea></form>
				登録ワード<br><input type="text" id="torokuword" size="10"><button onClick="settorokuword()">登録</button>
				<form name="form5"><textarea readonly class="textarea_toroku" name='textarea4' id='torokuText'></textarea></form>
			</div>
			<textarea id="mySavedModel" style="width: 100%; height: 300px; display: none">
                { "class": "go.GraphLinksModel", "linkFromPortIdProperty": "fromPort", "linkToPortIdProperty": "toPort"}
			</textarea>
		</div>
		<div class="row justify-content-right">
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
	<script>
		document.getElementById("setgroup").style.display = "none";
		document.getElementById("cli_send").style.display = "none";
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
		window.addEventListener('DOMContentLoaded', function() {
			document.getElementById("readfile").addEventListener('change', function() {
				var input = document.getElementById("readfile").files[0];
				var reader = new FileReader();
				reader.addEventListener('load', function() {
					uploadCode(reader.result);
				}, false);
				reader.readAsText(input, 'UTF-8');
			}, false);
			document.getElementById("readlog").addEventListener('change', function() {
				var input = document.getElementById("readlog").files[0];
				var reader = new FileReader();
				reader.addEventListener('load', function() {
					read_log(reader.result);
				}, false);
				reader.readAsText(input, 'UTF-8');
			}, false);
		});

		//保存ファイルのドラッグドロップ処理
		$(function() {
			var droppable = $("#droppable");

			// File API が使用できない場合は諦めます.
			if (!window.FileReader) {
				alert("File API がサポートされていません。");
				return false;
			}

			// イベントをキャンセルするハンドラです.
			var cancelEvent = function(event) {
				event.preventDefault();
				event.stopPropagation();
				return false;
			}

			// dragenter, dragover イベントのデフォルト処理をキャンセルします.
			droppable.bind("dragenter", cancelEvent);
			droppable.bind("dragover", cancelEvent);

			// ドロップ時のイベントハンドラを設定します.
			var handleDroppedFile = function(event) {
				// ファイルは複数ドロップされる可能性がありますが, ここでは 1 つ目のファイルを扱います.
				var file = event.originalEvent.dataTransfer.files[0];
				if (strSplit(file.name, 1, ".") == "wanetf") {
					if (file.name.indexOf('サーバ') == -1) {
						alert("ドロップされたファイルはこのモードで保存されたものではありません。");
						return false;
					}
					// ファイルの内容は FileReader で読み込みます.
					var fileReader = new FileReader();
					fileReader.onload = function(event) {
						// event.target.result に読み込んだファイルの内容が入っています.
						uploadCode(event.target.result);
					}
					fileReader.readAsText(file);

					// デフォルトの処理をキャンセルします.
					cancelEvent(event);
					return false;
				} else if (strSplit(file.name, 1, ".") == "sfnet") {
					// ファイルの内容は FileReader で読み込みます.
					var fileReader = new FileReader();
					fileReader.onload = function(event) {
						// event.target.result に読み込んだファイルの内容が入っています.
						sharelist_to_program(event.target.result);
					}
					fileReader.readAsText(file);

					// デフォルトの処理をキャンセルします.
					cancelEvent(event);
					return false;
				} else {
					alert("ドロップされたファイルはこのモードで保存されたものではありません。");
					return false;
				}
			}
			// ドロップ時のイベントハンドラを設定します.
			droppable.bind("drop", handleDroppedFile);
		});
	</script>

	<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light">
		<span id="schooid">双方向ネットワークアプリ</span>　：　
		<span id="dispstate">停止</span>

		<button class="navbar-toggler" type="button"
				data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent1"
				aria-controls="navbarSupportedContent1" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent1">
			<ul class="navbar-nav mx-auto">

			<!-- ファイル -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownFile" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				ファイル
				</a>
				<ul class="dropdown-menu" aria-labelledby="navbarDropdownFile">
				<li><a class="dropdown-item" href="#" onclick="saveCode_s(); return false;">ブラウザへ保存</a></li>
				<li><a class="dropdown-item" href="#" onclick="restoreBlocks_s(); return false;">ブラウザから読み込み</a></li>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item" href="#" onclick="downloadCode_s(); return false;">ファイルへ保存</a></li>

				<li>
					<label class="dropdown-item read_label mb-0" for="readfile" style="cursor:pointer;">
					ファイルから読み込み
					</label>
					<input type="file" id="readfile" accept=".wanetf" hidden>
				</li>
				</ul>
			</li>

			<!-- グループ -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownGroup" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				グループ
				</a>
				<ul class="dropdown-menu" aria-labelledby="navbarDropdownGroup">
				<li><a class="dropdown-item" href="#" onclick="groupDisplay(); return false;">グループ設定</a></li>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item" href="help/group.pdf" target="_blank" rel="noopener">グループ送信手順</a></li>
				</ul>
			</li>

			<!-- 編集 -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownEdit" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				　編集
				</a>
				<ul class="dropdown-menu" aria-labelledby="navbarDropdownEdit">
				<li><a class="dropdown-item" href="#" onclick="delete_kinsi(); return false;">禁止ワード削除</a></li>
				<li><a class="dropdown-item" href="#" onclick="delete_toroku(); return false;">登録ワード削除</a></li>
				</ul>
			</li>

			<!-- 通信ログ -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLog" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				　通信ログ　
				</a>
				<ul class="dropdown-menu" aria-labelledby="navbarDropdownLog">
				<li class="dropdown-item">
					<div class="form-check m-0">
					<input class="form-check-input" type="checkbox" id="log_enc" value="2" onchange="check_Encrypt()">
					<label class="form-check-label" for="log_enc">ログの暗号化</label>
					</div>
				</li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" href="#" onclick="savelog(); return false;">ログの保存</a></li>

				<li>
					<label class="dropdown-item read_label mb-0" for="readlog" style="cursor:pointer;">
					ログの読み込み
					</label>
					<input type="file" id="readlog" accept=".txt" hidden>
				</li>
				</ul>
			</li>

			<!-- その他 -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownOther" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				　その他
				</a>
				<ul class="dropdown-menu" aria-labelledby="navbarDropdownOther">
				<li><a class="dropdown-item" href="#" onclick="allDelete(); return false;">プログラム削除</a></li>
				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" href="#" onclick="print_cli_send(); return false;">クライアントへ送信</a></li>
				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" href="#" onclick="printDisplay(); return false;">レポート作成</a></li>
				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" href="#" onclick="change_network_no_Display(); return false;">設定</a></li>
				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" href="#" onclick="display_net_no(); return false;">利用中のネットワーク番号</a></li>
				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" href="#" onclick="cash_clear_all(); return false;">すべてのキャッシュをクリア</a></li>
				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item" href="help/server_flow.pdf" target="_blank" rel="noopener">ヘルプ</a></li>
				<li><a class="dropdown-item" href="help/procedure_f_s.pdf" target="_blank" rel="noopener">操作手順</a></li>
				</ul>
			</li>

			<!-- 動画 -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMovie" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				動画
				</a>
				<ul class="dropdown-menu" aria-labelledby="navbarDropdownMovie">
				<li><a class="dropdown-item" href="https://youtu.be/o8IumegiS-o" target="_blank" rel="noopener">ネットワーク番号について</a></li>
				<li><a class="dropdown-item" href="https://youtu.be/MrmThZ3aELo" target="_blank" rel="noopener">3人で通信</a></li>
				<li><a class="dropdown-item" href="https://youtu.be/mXYWFWx0wIU" target="_blank" rel="noopener">3人で通信（禁止ワード編）</a></li>
				<li><a class="dropdown-item" href="https://youtu.be/S5WC9F7rxnI" target="_blank" rel="noopener">3人で通信（パスワード編）</a></li>
				</ul>
			</li>

			</ul>
		</div>
	</nav>

	<script>
		function set_Interval() { startServerPolling(); }

		systemstart.addEventListener('click', function() {
			startServerPolling();
		});

		//自動保存したプログラムの呼び出し
		delayedCall(1000, function() {
			server_autosavefile_read();
		});

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