<?php
if (!isset($_POST['school_id_userid'])) {
	require_once("../login/function/errorhtml.php");
	get_direct_access_error_html();
	exit;
} else {
	$school_id_userid = $_POST['school_id_userid'];
	$school_name_userid = $_POST['school_name_userid'];
	$login_time = isset($_POST['login_time']) ? (int)$_POST['login_time'] : time();//ログイン時刻
	require_once("common/machine_learning.php");
	require_once("common/common.php");
	require_once '../../function/feature_const.php';
	$disabled_features = features_from_post();
}
//print_r($_POST);
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
	<title>読み込み中（クライアント）</title>
	<link href="css/bootstrap-5.3.3.css" rel="stylesheet" type="text/css">
	<link href="css/design.css" rel="stylesheet" type="text/css">
    <link href="css/gesture.css" rel="stylesheet" type="text/css">

    <!-- 機能設定をjsで使用できるように -->
	<script>
        window.DISABLED_FEATURES = <?php echo json_encode($disabled_features); ?>;
		// JSの中では　if (!DISABLED_FEATURES.includes(1))　で使用できる
		// JSの中では　if (ENABLED_FEATURES.includes(1)) {　で使用できる　1があるか
		// PHPでは　if (show_feature(1)) {
    </script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="js/popper.min.js" type="text/javascript"></script><!--bootstrapとセット（先に読み込む）-->
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
	<script src="js/function_message_ipad.js"></script>
	<script src="js/function_picture.js"></script>
	<script src="js/function_senddata_uc.js"></script>
	<script src="js/sound.js"></script>
	<?php require_once('common/head_scripts.php'); ?>
	<script src="js/main.js"></script>
	<script src="js/sendSound_o.js"></script>
	<script src="js/save_report.js"></script>
	<script src="js/html2canvas.js"></script>
    <?php if (show_feature(64) || show_feature(65)) { //画像認識、teachablemachine?>
   		<script src="js/TeachableMachine.js"></script>
	<?php }	?>
	<!-- 顔分析（face-api.js） -->
	<?php if (show_feature(68)) { //顔分析?>
		<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.15/dist/face-api.js"></script>
		<script src="js/function_face_auth.js"></script>
	<?php }	?>
</head>

<body style="padding-top: 60px" oncontextmenu="return false;">
	<!-- ワークスペースのエリア -->
	<div class="container-fluid" id="droppable">
		<!-- 印刷、機械学習 -->
			<?php 
				renderPrintHeadUI('blocklyDiv', $school_id_userid);
				if (show_feature(64)) { //画像認識　レジシステム
					renderGestureUI(); 
				}
			?>
		<!-- プロンプト、グループ、ネットワーク番号 -->
			<?php 
				renderSetpromptHeadUI();
				renderSetGroupHeadUI();
				renderSetupNetworkNoHeadUI();
			?>
		<!-- 顔認証、表情認証 -->
			<?php 
				if (show_feature(68)) { //顔分析
					renderFaceAuthUI();
				}
				if (show_feature(69)) { //表情
					renderFaceGestureUI();
				}
			?>
		<!-- ネットワーク番号表示 -->
			<div class="row" id="head_area">
				<div class="col-sm-2 tanto_title"><span>　　クライアント　　</span></div>
				<div class="col-sm-10"> <a class="riyokaisi text-decoration-none" href="#" onClick="startchat('<?php echo addslashes(htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8')) ?>', '<?php echo addslashes(htmlspecialchars($school_name_userid, ENT_QUOTES, 'UTF-8')) ?>')" id="systemstart">利用開始</a>
					<div class="dispinfo"><span id="netnoid">ネットワーク番号</span></div>

					名前：<input class="textarea_seitoname" type="text" id="myname" size="13" onBlur="leavename()">　　
					<span id="seitonotext">生徒番号：
						<input class="textarea_seitono" type="text" id="myno" size="4" onBlur="leaveno()">
						<span class="font16 fontbold" id="studentno"></span>
					</span>
				</div>
			</div>
		<!-- 接続方法表示 -->
			<div class="row align-items-center">
				<!-- 左側：中央寄せ -->
				<div class="col-sm d-flex justify-content-center">
					<span class="text-center">
					<img class="img-fluid" src="img/sw1.png" alt=""/>を押しながら
					<img class="img-fluid" src="img/sw2.png" alt=""/>にする
					<img class="img-fluid" src="img/sw3.png" alt=""/>を離す
					<img class="img-fluid" src="img/sw4.png" alt=""/>「接続処理」をする
					</span>
				</div>
			</div>
		<div class="row justify-content-right">
			<div class="col" id="blocklyDiv" style="height: 450px; width: 100%;"></div>
			<div class="col-sm-4">
				<div class="backcolor-default  m_area" id="messages" style="height: 350px;"></div>
				<div class="backcolor-message">
					<table width="100%" border="0">
						<tbody>
							<tr>
								<td>
									<input class="textarea_message" type="text" id="mymessage" size="20" onBlur="leavemessage()" placeholder="メッセージ入力">
								</td>
								<td rowspan="2">
									<a class="sendbtn text-decoration-none" href="#" onclick="sendMessage('3','<?php echo addslashes(htmlspecialchars($school_id_userid, ENT_QUOTES, 'UTF-8')) ?>')" id="play_set">　実行　</a>
								</td>
							</tr>
							<tr>
								<td>
									<span id="parablock_stamp"></span>
									スタンプ番号：
									<select name="select" id="m_para_stampno" class="selectpara">
										<option hidden></option>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
										<option value="9">9</option>
										<option value="10">10</option>
										<option value="11">11</option>
										<option value="12">12</option>
										<option value="13">13</option>
										<option value="14">14</option>
										<option value="15">15</option>
										<option value="16">16</option>
										<option value="17">17</option>
										<option value="18">18</option>
										<option value="19">19</option>
										<option value="20">20</option>
										<option value="21">21</option>
										<option value="22">22</option>
										<option value="23">23</option>
										<option value="24">24</option>
										<option value="25">25</option>
										<option value="26">26</option>
										<option value="27">27</option>
										<option value="28">28</option>
										<option value="29">29</option>
										<option value="30">30</option>
										<option value="31">31</option>
										<option value="32">32</option>
										<option value="33">33</option>
										<option value="34">34</option>
										<option value="35">35</option>
									</select>
									<img src="img/stp.png" class="ImgBox-Img" width="30px" height="30px">
									</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="row justify-content-right">
			<!--画像アップロード用 -->
			<div class="col-auto">
				<!--画像アップロード用 -->
				<div class="upload_back" id="photarea">
					写真を送る場合のファイルを選択します
					<form id="img_form" method="post" enctype="multipart/form-data">
						<input type="file" name="img" accept="image/png,image/jpeg" id="img_file">
					</form>
					<div id="uppicturearea">写真は選択されていません</div>
				</div>
			</div>
			<div class="col-auto">
				<div class="illust_back" id="illustarea">
					<!-- 1〜18段目 -->
					<div class="row g-1 text-center">
						<div class="col-auto">
							<div>1</div>
							<div class="ImgBox"><img src="img/illust/1.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>2</div>
							<div class="ImgBox"><img src="img/illust/2.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>3</div>
							<div class="ImgBox"><img src="img/illust/3.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>4</div>
							<div class="ImgBox"><img src="img/illust/4.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>5</div>
							<div class="ImgBox"><img src="img/illust/5.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>6</div>
							<div class="ImgBox"><img src="img/illust/6.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>7</div>
							<div class="ImgBox"><img src="img/illust/7.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>8</div>
							<div class="ImgBox"><img src="img/illust/8.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>9</div>
							<div class="ImgBox"><img src="img/illust/9.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>10</div>
							<div class="ImgBox"><img src="img/illust/10.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>11</div>
							<div class="ImgBox"><img src="img/illust/11.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>12</div>
							<div class="ImgBox"><img src="img/illust/12.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>13</div>
							<div class="ImgBox"><img src="img/illust/13.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>14</div>
							<div class="ImgBox"><img src="img/illust/14.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>15</div>
							<div class="ImgBox"><img src="img/illust/15.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>16</div>
							<div class="ImgBox"><img src="img/illust/16.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>17</div>
							<div class="ImgBox"><img src="img/illust/17.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>18</div>
							<div class="ImgBox"><img src="img/illust/18.png" class="ImgBox-Img illust-img"></div>
						</div>
					</div>
					<!-- 19〜35段目 -->
					<div class="row g-1 text-center">
						<div class="col-auto">
							<div>19</div>
							<div class="ImgBox"><img src="img/illust/19.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>20</div>
							<div class="ImgBox"><img src="img/illust/20.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>21</div>
							<div class="ImgBox"><img src="img/illust/21.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>22</div>
							<div class="ImgBox"><img src="img/illust/22.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>23</div>
							<div class="ImgBox"><img src="img/illust/23.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>24</div>
							<div class="ImgBox"><img src="img/illust/24.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>25</div>
							<div class="ImgBox"><img src="img/illust/25.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>26</div>
							<div class="ImgBox"><img src="img/illust/26.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>27</div>
							<div class="ImgBox"><img src="img/illust/27.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>28</div>
							<div class="ImgBox"><img src="img/illust/28.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>29</div>
							<div class="ImgBox"><img src="img/illust/29.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>30</div>
							<div class="ImgBox"><img src="img/illust/30.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>31</div>
							<div class="ImgBox"><img src="img/illust/31.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>32</div>
							<div class="ImgBox"><img src="img/illust/32.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>33</div>
							<div class="ImgBox"><img src="img/illust/33.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>34</div>
							<div class="ImgBox"><img src="img/illust/34.png" class="ImgBox-Img illust-img"></div>
						</div>
						<div class="col-auto">
							<div>35</div>
							<div class="ImgBox"><img src="img/illust/35.png" class="ImgBox-Img illust-img"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row justify-content-right">
			<div class="col-auto upload_back" id="programarea">
				プログラムデータを送る場合のファイルを選択します<br>
				事前に作成したファイル(LED・メロディ)を選択して下さい。<br>
				ファイル１：<input type="file" name="img" accept=".cuc7,.cuc7f,.cuc7w,.cuc9,.cuc9f,.cuc9w,.ud1,.ud1f,.ud1w,.udsd,.ucsd" id="pro_file1"><br>
				ファイル２：<input type="file" name="img" accept=".cuc7,.cuc7f,.cuc7w,.cuc9,.cuc9f,.cuc9w,.ud1,.ud1f,.ud1w,.udsd,.ucsd" id="pro_file2"><br>
				ファイル３：<input type="file" name="img" accept=".cuc7,.cuc7f,.cuc7w,.cuc9,.cuc9f,.cuc9w,.ud1,.ud1f,.ud1w,.udsd,.ucsd" id="pro_file3"><br>
			</div>
		</div>
		<div class="row justify-content-right">
			<!-- <form name="form2"><textarea style="display:none;" name='textarea1' id='proText'>プログラム</textarea> -->
			<form name="form2"><textarea style="display:none;" name='textarea1' id='proText'>プログラム</textarea></form>
			<form name="form3"><textarea style="display:none;" name='textarea2' id='sendText'>転送データ</textarea></form>
			<form name="form4"><textarea style="display:none;" name='textarea3' id='recieveText'>受信プログラム</textarea></form>
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
			<block type="start"></block>
			<block type="start_r"></block>
			<block type="loop"></block>
			<block type="if_yes"></block>
			<block type="if_else"></block>
			<block type="if_block_beforetime">
				<field name="settime">12</field>
			</block>
			<block type="if_block_aftertime">
				<field name="settime">12</field>
			</block>
			<block type="if_block_impo"></block>
			<block type="if_block_notimpo"></block>
			<block type="if_block_seitono"></block>
			<block type="if_block_notseitono"></block>
			<block type="if_block_error"></block>
			<block type="if_block_noterror"></block>
			<?php if (show_feature(64)) { ?>
				<block type="if_block_teachablemachine"></block>
				<block type="if_block_notteachablemachine"></block>
			<?php }	?>
		</category>
		<category name="送信" colour="#00aaff">
			<block type="m_send"></block>
			<block type="stamp_send"></block>
			<block type="picturem_send"></block>
			<block type="program_send"></block>
			<block type="m_group_send"></block>
			<block type="stamp_group_send"></block>
			<block type="picture_group_send"></block>
			<block type="program_group_send"></block>
		</category>
		<category name="AI" colour="#506373">
			<!-- //このブロックはいらない<block type="ai_send"></block>
			//このブロックはいらない<block type="ai_change"></block>-->
			<block type="ai_para_change"></block>
			<!--<block type="ai_prompt_change"></block>-->
		</category>
		<category name="音" colour="#ff8c1a">
			<block type="play_sound">
				<field name="soundno">0</field>
			</block>
		</category>
		<category name="信号待ち" colour="#8a97b8">
			<block type="wait_time"></block>
			<?php if (show_feature(64)) { ?>
				<block type="wait_teachablemachine"></block>
			<?php }	?>
		</category>
		<category name="変数" colour="#9e9065">
			<block type="client_variable_1"></block>
			<block type="client_variable_2"></block>
			<block type="client_variable_3"></block>
			<block type="client_variable_4"></block>
			<block type="client_variable_5"></block>
		</category>
		<category name="設定" colour="#92117D">
			<block type="set_server">
				<field name="server_add">00</field>
			</block>
			<block type="set_pass">
				<field name="passst">1234</field>
			</block>
			<block type="set_impo"></block>
			<block type="disp_confirm"></block>
		</category>
		<category name="フォント・画面" colour="#00ff80">
			<block type="font_size">
				<field name="f_size">12</field>
			</block>
			<block type="font_color">
				<field name="f_color">#ff0000</field>
			</block>
			<block type="font_backcolor">
				<field name="f_backcolor">#ffe2ba</field>
			</block>
			<block type="disp_backcolor">
				<field name="d_backcolor">#def5ff</field>
			</block>
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
		
		const hideIds = [
			"gesture","teachablemachine_warning","teachablemachine","teachablemachine_pose","teachablemachine_preset","setprompt","setgroup","face_auth","face_gesture",
			"printhead","setup_networkno","print-koso","print-siyo","print-kuhu","print-kanso"
		];
		hideIds.forEach(id => { const el = document.getElementById(id); if (el) el.style.display = "none"; });

		document.getElementById('myno').value = localStorage.getItem("seitono");
		document.getElementById('myname').value = localStorage.getItem("seitoname");
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
	</script>

	<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light">
		<span id="schooid">双方向ネットワークアプリ(UC iPad)</span> ：
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

			<!-- ファイル -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownFile" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				ファイル
				</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownFile">
				<li><a class="dropdown-item aback" onclick="saveCode()" href="#">ブラウザへ保存</a></li>
				<li><a class="dropdown-item aback" onclick="restoreBlocks()" href="#">ブラウザから読み込み</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item aback" onclick="downloadCode_ipad()" href="#" id="filesave">ファイルへ保存</a></li>

				<li>
					<label class="dropdown-item read_label">
					<form name="test">ファイルから読み込み
						<input type="file" id="readfile" accept=".wanet" hidden>
					</form>
					</label>
				</li>
				</ul>
			</li>

			<!-- 接続処理 -->
			<li class="nav-item">
				<a class="nav-link aback" ontouchstart="" href="#" onclick="connect_iPad()">接続処理</a>
			</li>

			<!-- グループ -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownGroup" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				グループ
				</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownGroup">
				<li><a class="dropdown-item aback" onclick="groupDisplay()" href="#">グループ設定</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item aback" href="help/group.pdf" target="_blank">グループ送信手順</a></li>
				</ul>
			</li>

			<!-- その他 -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownOther" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				その他
				</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownOther">
				<?php if (show_feature(64)) { ?>
					<li><a class="dropdown-item aback" onclick="gestureDisplay()" href="#">機械学習（画像分析）</a></li>
					<li><a class="dropdown-item aback" href="https://www.hisatomi-kk.com/document/joho/UD-1/guide/machinelearning.pdf" target="_blank">画像分析について(PDF)</a></li>
				<?php }	?>
				<?php if (show_feature(68)) { ?>
					<li><hr class="dropdown-divider"></li>				
					<li><a class="dropdown-item aback" onclick="faceAuthDisplay()" href="#">機械学習（顔分析）</a></li>
				<?php }	?>
				<?php if (show_feature(69)) { ?>
					<li><hr class="dropdown-divider"></li>				
					<li><a class="dropdown-item aback" onclick="faceGestureDisplay()" href="#">機械学習（表情分析）</a></li>
				<?php }	?>
				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item aback" onclick="printDisplay()" href="#">レポート作成</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item aback" onclick="change_network_no_Display()" href="#">設定</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item aback" onclick="display_net_no()" href="#">利用中のネットワーク番号</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item aback" onclick="cash_clear_all()" href="#">すべてのキャッシュをクリア</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item aback" href="help/client.pdf" target="_blank">ヘルプ</a></li>
				<li><a class="dropdown-item aback" href="help/procedure_b_c.pdf" target="_blank">操作手順</a></li>
				</ul>
			</li>

			<!-- 動画 -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownMovie" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">
				動画
				</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownMovie">
				<li><a class="dropdown-item aback" href="https://youtu.be/o8IumegiS-o" target="_blank">ネットワーク番号について</a></li>
				<li><a class="dropdown-item aback" href="https://youtu.be/y_1tNZrrVtM" target="_blank">2人で通信</a></li>
				<li><a class="dropdown-item aback" href="https://youtu.be/hXgj2OgqTBs" target="_blank">3人で通信</a></li>
				<li><a class="dropdown-item aback" href="https://youtu.be/uhk4r2AFrMQ" target="_blank">3人で通信（禁止ワード編）</a></li>
				<li><a class="dropdown-item aback" href="https://youtu.be/0l2Hp59nM6A" target="_blank">3人で通信（パスワード編）</a></li>
				</ul>
			</li>

			</ul>
		</div>
	</nav>

  	<script src="js/bootstrap-5.3.3.js" type="text/javascript"></script>
	<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.17.0"></script><!--独自-->
	<?php if (show_feature(64)) { //画像認識?>
		<script src="js/gesture_tenso.js"></script>	
	<?php }	?>
	<?php if (show_feature(69)) { //表情認証?>
		<script src="js/gesture_face.js"></script>
	<?php }	?>

	<script>
		
		//利用開始がクリックされたら
		systemstart.addEventListener('click', function() {
			startPolling();
		});
		
		function set_Interval(){
			startPolling();
		}

		//自動保存したプログラムの呼び出し
		client_autosavefile_read();

		//画像アップロード（ファイル選択が変更されたら）
		var input_file = document.getElementById("img_file");
		input_file.onchange = function() {
			//生徒番号が入力されていなかったら
			var snovalue = document.getElementById('myno').value;
			if (snovalue == "") {
				input_file.value = "";
				alert("生徒番号が入力されおりません。生徒番号を入力して下さい。");
				return;
			}
			if (input_file.value) {
				//写真アップロード
				picture_upload();
				//input_file.value = "";
			}
		}

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
			pollingInterval = POLL_MIN;
			recieve_check_action();
		}
		});
		
		document.title = "読み込み完了（クライアント）";
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