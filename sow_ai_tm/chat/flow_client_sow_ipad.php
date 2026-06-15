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
	<title>読み込み中（クライアント）</title>
	<link href="css/bootstrap-5.3.3.css" rel="stylesheet" type="text/css">
	<link href="css/design.css" rel="stylesheet" type="text/css">
    <link href="css/gesture.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="css/jquery-ui.min.css" />
	<link rel="stylesheet" href="css/style.css" />

    <!-- 機能設定をjsで使用できるように -->
	<script>
        window.DISABLED_FEATURES = <?php echo json_encode($disabled_features); ?>;
		// JSの中では　if (!DISABLED_FEATURES.includes(1))　で使用できる
		// JSの中では　if (ENABLED_FEATURES.includes(1)) {　で使用できる　1があるか
		// PHPでは　if (show_feature(1)) {
    </script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="js/popper.min.js" type="text/javascript"></script><!--bootstrapとセット（先に読み込む）-->

	<!-- Copyright 1998-2021 by Northwoods Software Corporation. -->
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<script src="jsfglow/jquery.min.js"></script>
	<script src="jsfglow/jquery-ui.min.js"></script>
	<script src="jsfglow/Define_client.js"></script>
	<script src="jsfglow/flowchart.js"></script>
	<script src="jsfglow/function.js"></script>
	<script src="jsfglow/function_contents.js"></script>
	<script src="jsfglow/function_data.js"></script>
	<script src="jsfglow/function_display.js"></script>
	<script src="jsfglow/function_file.js"></script>
	<script src="jsfglow/function_flow.js"></script>
	<script src="jsfglow/function_message_ipad.js"></script>
	<script src="jsfglow/function_picture.js"></script>
	<script src="jsfglow/function_recieve.js"></script>
	<script src="jsfglow/function_share.js"></script>
	<script src="js/sound.js"></script>
	<script src="jsfglow/DataInspector.js"></script>
    <script src="jsfglow/shortcut.js"></script>
	<?php require_once('common/head_scripts.php'); ?>
	<script src="js/main.js"></script>
	<script src="js/save_report.js"></script>
	<script src="js/html2canvas.js"></script>
	<?php if (show_feature(94) || show_feature(95)) { //画像認識、teachablemachine?>
   		<script src="js/TeachableMachine.js"></script>
	<?php }	?>
	<!-- 顔分析（face-api.js） -->
	<?php if (show_feature(98)) { //顔分析?>
		<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.15/dist/face-api.js"></script>
		<script src="js/function_face_auth.js"></script>
	<?php }	?>
</head>

<body onload="init()" style="padding-top: 70px">
	<div class="container-fluid" id="droppable">
		<!-- 印刷、機械学習 -->
			<?php 
				renderPrintHeadUI('myDiagramDiv', $school_id_userid);
				if (show_feature(94)) { //画像認識
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
				if (show_feature(98)) { //顔分析
					renderFaceAuthUI();
				}
				if (show_feature(99)) { //表情
					renderFaceGestureUI();
				}
			?>
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
		<div class="row" style="height: 5px"></div>
		<div id="sample">
			<div class="row justify-content-center">
				<div class="col-md-8">
					<div style="width: 100%; display: flex; justify-content: space-between">
						<div id="paletteDraggable" style="width: 200px; height: 345px; margin-right: 2px; background-color: #f8f8f8">
							<div id="paletteDraggableHandle"></div>
							<div id="myPaletteDiv"></div>
							<div style="height: 5px;"></div>
							<div style="height: 150px; background-color: #FFDDC6">
								<a href="#" class="updatebtn" id="para_btn" onClick="kousin()" style="display:none;">更新</a>
								<div id="para_message" style="display:none;">
									<select name="select" id="paramessage" class="select" onchange="change_pulldown(paramessage)">
										<option value="メッセージを送信">メッセージを送信</option>
										<option value="メッセージをグループに送信">メッセージをグループに送信</option>
										<option value="スタンプを送信">スタンプを送信</option>
										<option value="スタンプをグループに送信">スタンプをグループに送信</option>
										<option value="写真を送信">写真を送信</option>
										<option value="写真をグループに送信">写真をグループに送信</option>
									</select>
									<span id="parablock_sendno">
										送信先番号：<input type="text" id="m_para_sendno" size="5">
									</span>
									<span id="parablock_group">
										グループ：
										<select name="select" id="m_para_group" class="selectgroup">
											<option value="グループ1">グループ１</option>
											<option value="グループ2">グループ２</option>
											<option value="グループ3">グループ３</option>
										</select>
									</span>
									<span id="parablock_file">
										ファイル：
										<select name="select" id="m_para_file" class="selectgroup">
											<option value="1">ファイル１</option>
											<option value="2">ファイル２</option>
											<option value="3">ファイル３</option>
										</select>
									</span>
									<span id="parablock_mess">
										送信内容：
										<select name="select" id="m_para_mess" class="selectgroup">
											<option value="入力メッセージ">入力メッセージ</option>
											<option value="変数1">変数１</option>
											<option value="変数2">変数２</option>
											<option value="変数3">変数３</option>
											<option value="変数4">変数４</option>
											<option value="変数5">変数５</option>
										</select>
									</span>
								</div>
								<div id="para_aichange" style="display:none;">
									<select name="select" id="paraaichange" class="select" onchange="change_pulldown(paraaichange)">
										<option value="メッセージをAIに変換">AIでメッセージを変換</option>
										<!--<option value="メッセージをプロンプトでAI変換">AIでプロンプト変換</option>-->
									</select>
									<span id="parablock_aichange">
										メッセージを
										<input type="text" id="ai_change_st" size="10" placeholder="">
										に変換
									</span>
								</div>
								<div id="para_sound" style="display:none;">
									<select name="select" id="parasound" class="select" onchange="change_pulldown(parasound)">
										<option value="音を鳴らす">音を鳴らす</option>
									</select>
									<span id="parablock_soundno">
										音番号：
										<select name="select" id="sound_para_sound" class="selectpara">
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
										</select>
									</span>
								</div>
								<div id="para_display" style="display:none;">
									<select name="select" id="paradisplay" class="select" onchange="change_pulldown(paradisplay)">
										<option value="ﾌｫﾝﾄｻｲｽﾞ">文字の大きさ</option>
										<option value="文字の色">文字の色</option>
										<option value="メッセージ背景色">メッセージ背景色</option>
										<option value="画面背景色">画面背景色</option>
									</select>
									<span id="parablock_fontsize">
										文字の大きさ：
										<select name="select" id="disp_para_fontsize" class="selectpara">
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
										</select>
									</span>
									<span id="parablock_fontcolor">
										色：<input type="color" id="disp_para_fontcolor" value="#ff0000">
									</span>
									<span id="parablock_messagebackcolor">
										色：<input type="color" id="disp_para_messagebackcolor" value="#dcf7fa">
									</span>
									<span id="parablock_dispbackcolor">
										色：<input type="color" id="disp_para_dispbackcolor" value="#e5ffdb">
									</span>
								</div>
								<div id="para_variable" style="display:none;">
									<select name="select" id="paravariable" class="select" onchange="change_pulldown(paravariable)">
										<option value="変数1">変数1</option>
										<option value="変数2">変数2</option>
										<option value="変数3">変数3</option>
										<option value="変数4">変数4</option>
										<option value="変数5">変数5</option>
									</select>
									<span id="parablock_variable">
										変数：<input type="text" id="variable_value" size="6">
									</span>
								</div>
								<div id="para_setup" style="display:none;">
									<select name="select" id="parasetup" class="select" onchange="change_pulldown(parasetup)">
										<option value="サーバを設定する">サーバを設定する</option>
										<option value="ﾊﾟｽﾜｰﾄﾞを設定する">パスワードを設定する</option>
										<option value="重要なメッセージ">重要なメッセージ</option>
										<option value="確認画面を表示">確認画面を表示</option>
									</select>
									<span id="parablock_server">
										サーバの番号：<input type="text" id="setup_para_server" size="3">
									</span>
									<span id="parablock_pass">
										パスワード：<input type="text" id="setup_para_pass" size="6">
									</span>
								</div>
								<div id="para_sensor" style="display:none;">
									<select name="select" id="parasensor" class="select" onchange="change_pulldown(parasensor)">
										<option value="時刻待ち">設定時刻になるまで停止</option>
										<?php if (show_feature(94)) { ?>
											<option value="機械学習まで">機械学習でクラスが設定値になるまで停止</option>
										<?php }	?>
									</select>
									<span id="parablock_sensor">
										<div><span id="para_sensor_st1">明るさ：</span><input type="text" id="sensor_paralight" size="3"></div>									
									</span>
									<span id="parablock_sensor_teachable">
										<div><span id="para_sensor_st2">クラス：</span><input type="text" id="sensor_paraclass" size="5"></div>
										<div><span id="para_sensor_st3">値：</span><input type="text" id="sensor_paravalue" size="3">％</div>										
									</span>
									<span id="parablock_sensor_time">
										<span id="para_sensor_st4">時刻：</span>
										<select name="select" id="sensor_para_ji" class="selectpara">
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
										</select>時
										<select name="select" id="sensor_para_hun" class="selectpara">
											<option value="0">00</option>
											<option value="1">01</option>
											<option value="2">02</option>
											<option value="3">03</option>
											<option value="4">04</option>
											<option value="5">05</option>
											<option value="6">06</option>
											<option value="7">07</option>
											<option value="8">08</option>
											<option value="9">09</option>
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
											<option value="36">36</option>
											<option value="37">37</option>
											<option value="28">38</option>
											<option value="39">39</option>
											<option value="40">40</option>
											<option value="41">41</option>
											<option value="42">42</option>
											<option value="43">43</option>
											<option value="44">44</option>
											<option value="45">45</option>
											<option value="46">46</option>
											<option value="47">47</option>
											<option value="48">48</option>
											<option value="49">49</option>
											<option value="50">50</option>
											<option value="51">51</option>
											<option value="52">52</option>
											<option value="53">53</option>
											<option value="54">54</option>
											<option value="55">55</option>
											<option value="56">56</option>
											<option value="57">57</option>
											<option value="58">58</option>
											<option value="59">59</option>
										</select>分
									</span>
								</div>
								<div id="para_repeat" style="display:none;">
									<select name="select" id="pararepeat" class="select" onchange="change_pulldown(pararepeat)">
										<option value="繰り返し開始">繰り返し開始</option>
									</select>
									<span id="parablock_repeat">
										繰り返し回数：<input type="text" id="repeat_paracnt" size="2">
									</span>
								</div>
								<div id="para_if" style="display:none;">
									<select name="select" id="if_paraif" class="select" onchange="change_pulldown(if_paraif)">
										<option value="設定時刻より前">設定時刻より前なら</option>
										<option value="設定時刻より後">設定時刻より後なら</option>
										<option value="重要なら">重要なら</option>
										<option value="重要でないなら">重要でないなら</option>
										<?php if (show_feature(94)) { ?>
											<option value="機械学習以上>なら">機械学習でクラスが設定値以上なら</option>
											<option value="機械学習以下<なら">機械学習でクラスが設定値以下なら</option>
										<?php }	?>
									</select>
									<span id="para_if_time">時刻：
										<select name="select" id="if_para_ji" class="selectpara">
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
										</select>時
										<select name="select" id="if_para_hun" class="selectpara">
											<option value="0">00</option>
											<option value="1">01</option>
											<option value="2">02</option>
											<option value="3">03</option>
											<option value="4">04</option>
											<option value="5">05</option>
											<option value="6">06</option>
											<option value="7">07</option>
											<option value="8">08</option>
											<option value="9">09</option>
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
											<option value="36">36</option>
											<option value="37">37</option>
											<option value="28">38</option>
											<option value="39">39</option>
											<option value="40">40</option>
											<option value="41">41</option>
											<option value="42">42</option>
											<option value="43">43</option>
											<option value="44">44</option>
											<option value="45">45</option>
											<option value="46">46</option>
											<option value="47">47</option>
											<option value="48">48</option>
											<option value="49">49</option>
											<option value="50">50</option>
											<option value="51">51</option>
											<option value="52">52</option>
											<option value="53">53</option>
											<option value="54">54</option>
											<option value="55">55</option>
											<option value="56">56</option>
											<option value="57">57</option>
											<option value="58">58</option>
											<option value="59">59</option>
										</select>分
									</span>
									<span id="parablock_if_teachable">
										<div><span id="para_if_st1">クラス：</span><input type="text" id="if_paraclass" size="5"></div>
										<div><span id="para_if_st2">値：</span><input type="text" id="if_paravalue" size="3">％</div>										
									</span>
									<span id="parablock_if">
										<div><span id="para_if_st3">明るさ：</span><input type="text" id="if_paralight" size="3"></div>
									</span>
								</div>
								<div id="para_if_r" style="display:none;">
									<select name="select" id="if_paraif_r" class="select" onchange="change_pulldown(if_paraif_r)">
										<option value="設定時刻より前">設定時刻より前なら</option>
										<option value="設定時刻より後">設定時刻より後なら</option>
										<option value="重要なら">重要なら</option>
										<option value="重要でないなら">重要でないなら</option>
										<option value="特定の生徒番号?">特定の生徒番号?</option>
										<option value="特定の生徒番号でない?">特定の生徒番号でない?</option>
										<option value="エラーなら">エラーなら</option>
										<option value="エラーでないなら">エラーでないなら</option>
									</select>
									<span id="parablock_if_r">
										<span id="para_if_r_st1">時刻：
											<select name="select" id="if_para_r_ji" class="selectpara">
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
											</select>時
										</span>
									</span>
									<span id="parablock_r_ifseitono">
										生徒番号：<input type="text" id="if_para_r_seitono" size="4">
									</span>
								</div>
							</div>
						</div>
						<div style="height: 5px;"></div>
						<div id="myDiagramDiv" style="flex-grow: 1; height: 500px; background-color: #ededed"></div>

						<div id="infoDraggable">
							<div id="myInfo"></div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="backcolor-default m_area" id="messages" style="height: 400px;"></div>
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
			<textarea id="mySavedModel" style="width: 100%; height: 300px; display: none">
                { "class": "go.GraphLinksModel", "linkFromPortIdProperty": "fromPort", "linkToPortIdProperty": "toPort"}
			</textarea>
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
			<!--  style="display:none;" -->
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

	<script>
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
			if (filename[1] == "wanetf"){
			  reader.readAsText(file, 'UTF-8');
			}
			reader.onload = function(e){				
				if (reader.result.substr(0,2) == "{ " ){
					uploadCode(reader.result);				  
				}		  
				document.getElementById("readfile").value = "";
			}
	    }, false);
		});
	</script>

	<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light">
		<span id="schooid">双方向ネットワークアプリ</span> ：
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
				data-bs-toggle="dropdown" aria-expanded="false">ファイル</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownFile">
				<li><a class="dropdown-item aback" onclick="saveCode()" href="#">ブラウザへ保存</a></li>
				<li><a class="dropdown-item aback" onclick="restoreBlocks()" href="#">ブラウザから読み込み</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item aback" onclick="downloadCode_ipad()" href="#">ファイルへ保存</a></li>

				<li>
					<label class="dropdown-item read_label">
					<form name="test">ファイルから読み込み
						<input type="file" id="readfile" accept=".wanetf" hidden>
					</form>
					</label>
				</li>
				</ul>
			</li>

			<!-- グループ -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownGroup" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">グループ</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownGroup">
				<li><a class="dropdown-item aback" onclick="groupDisplay()" href="#">グループ設定</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item aback" href="help/group.pdf" target="_blank">グループ送信手順</a></li>
				</ul>
			</li>

			<!-- 操作 -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownOp" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">操作</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownOp">
				<li><a class="dropdown-item aback" ontouchstart="" onclick="allselectcommanad()" href="#">全選択</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item aback" ontouchstart="" onclick="deletecommanad()" href="#">削除</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item aback" ontouchstart="" onclick="undocommanad()" href="#">元に戻す</a></li>

				<li><hr class="dropdown-divider"></li>

				<li><a class="dropdown-item aback" ontouchstart="" onclick="redocommanad()" href="#">やり直す</a></li>
				</ul>
			</li>

			<!-- その他 -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownOther" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">その他</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownOther">
				<li><a class="dropdown-item aback" onclick="allDelete()" href="#">プログラム削除</a></li>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item aback" onclick="printDisplay()" href="#">レポート作成</a></li>
				<?php if (show_feature(94)) { ?>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item aback" onclick="gestureDisplay()" href="#">機械学習（画像分析）</a></li>
				<li><a class="dropdown-item aback" href="https://www.hisatomi-kk.com/document/joho/UD-1/guide/machinelearning.pdf" target="_blank">画像分析ついて(PDF)</a></li>
				<?php }	?>
				<?php if (show_feature(98)) { ?>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item aback" href="#" onclick="faceAuthDisplay(); return false;">機械学習（顔分析）</a></li>
				<?php }	?>
				<?php if (show_feature(99)) { ?>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item aback" href="#" onclick="faceGestureDisplay(); return false;">機械学習（表情分析）</a></li>
				<?php }	?>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item aback" onclick="change_network_no_Display()" href="#">設定</a></li>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item aback" onclick="display_net_no()" href="#">利用中のネットワーク番号</a></li>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item aback" onclick="cash_clear_all()" href="#">すべてのキャッシュをクリア</a></li>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item aback" href="help/client_flow.pdf" target="_blank">ヘルプ</a></li>
				<li><a class="dropdown-item aback" href="help/procedure_f_c.pdf" target="_blank">操作手順</a></li>
				</ul>
			</li>

			<!-- 動画 -->
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdownMovie" role="button"
				data-bs-toggle="dropdown" aria-expanded="false">動画</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownMovie">
				<li><a class="dropdown-item aback" href="https://youtu.be/o8IumegiS-o" target="_blank">ネットワーク番号について</a></li>
				<li><a class="dropdown-item aback" href="https://youtu.be/LgdRVmeN8fc" target="_blank">2人で通信</a></li>
				<li><a class="dropdown-item aback" href="https://youtu.be/MrmThZ3aELo" target="_blank">3人で通信</a></li>
				<li><a class="dropdown-item aback" href="https://youtu.be/mXYWFWx0wIU" target="_blank">3人で通信（禁止ワード編）</a></li>
				<li><a class="dropdown-item aback" href="https://youtu.be/S5WC9F7rxnI" target="_blank">3人で通信（パスワード編）</a></li>
				</ul>
			</li>

			</ul>
		</div>
	</nav>

  	<script src="js/bootstrap-5.3.3.js" type="text/javascript"></script>
	<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.17.0"></script><!--独自-->
	<?php if (show_feature(94)) { //画像認識?>
		<script src="js/gesture_tenso.js"></script>	
	<?php }	?>
	<?php if (show_feature(99)) { //表情認証?>
		<script src="js/gesture_face.js"></script>
	<?php }	?>

	<script>
		
		systemstart.addEventListener('click', function() {
			startPolling();
		});
		
		function set_Interval(){
			startPolling();
		}

		//自動保存したプログラムの呼び出し
		delayedCall(1000, function() {
			client_autosavefile_read();
		});

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