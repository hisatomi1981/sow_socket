Blockly.Blocks['start'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("送信プログラム");
    this.setNextStatement(true, null);
    this.setColour("#d99102");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_start'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("サーバプログラムスタート");
    this.setNextStatement(true, null);
    this.setColour("#d99102");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['start_r'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("受信プログラム");
    this.setNextStatement(true, null);
    this.setColour("#d99102");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_yes'] = {
  init: function() {
    this.appendValueInput("if_jeken")
        .setCheck(null)
        .appendField("もし ～なら");
    this.appendStatementInput("yes")
        .setCheck(null)
        .appendField("YES");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#ffab19");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_else'] = {
  init: function() {
    this.appendValueInput("if_jeken")
        .setCheck(null)
        .appendField("もし　～なら");
    this.appendStatementInput("yes")
        .setCheck(null)
        .appendField("YES");
    this.appendStatementInput("no")
        .setCheck(null)
        .appendField("No");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#ffab19");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_beforetime'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("時刻が")
	    .appendField(new Blockly.FieldNumber(0, 0, 24, 1), "settime")
        .appendField("時より前");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_aftertime'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("時刻が")
	    .appendField(new Blockly.FieldNumber(0, 0, 24, 1), "settime")
        .appendField("時より後");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_impo'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("重要？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_notimpo'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("重要でない？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_light'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("明るさ >=")
        .appendField(new Blockly.FieldNumber(50, 1, 100, 1), "if_light");
    this.setOutput(true, null);
    this.setColour("#74b598");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_dark'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("明るさ <")
        .appendField(new Blockly.FieldNumber(50, 1, 100, 1), "if_light");
    this.setOutput(true, null);
    this.setColour("#74b598");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_tempup'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("温度 >")
        .appendField(new Blockly.FieldNumber(25, 1, 50, 1), "if_light");
    this.setOutput(true, null);
    this.setColour("#74b598");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_tempdown'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("温度 <")
        .appendField(new Blockly.FieldNumber(25, 1, 50, 1), "if_light");
    this.setOutput(true, null);
    this.setColour("#74b598");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_signal'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("外部信号=ON？");
    this.setOutput(true, null);
    this.setColour("#74b598");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_notsignal'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("外部信号=OFF？");
    this.setOutput(true, null);
    this.setColour("#74b598");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_teachablemachine'] = {
  init: function() {
    this.appendDummyInput()
        .appendField(new Blockly.FieldTextInput("機械学習したclass名"), "if_teachablemachine")  
        .appendField("が")
        .appendField(new Blockly.FieldNumber(80, 0, 100, 1), "paravalue")      
        .appendField("％以上なら");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_notteachablemachine'] = {
  init: function() {
    this.appendDummyInput()
        .appendField(new Blockly.FieldTextInput("機械学習したclass名"), "if_teachablemachine")
        .appendField("が")
        .appendField(new Blockly.FieldNumber(80, 0, 100, 1), "paravalue")     
        .appendField("％以下なら");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};

Blockly.Blocks['if_block_seitono'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("生徒番号が")
	    .appendField(new Blockly.FieldNumber(0, 0, 50, 1), "sno")
        .appendField("番？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_notseitono'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("生徒番号が")
	    .appendField(new Blockly.FieldNumber(0, 0, 50, 1), "sno")
        .appendField("番でない？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_error'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("エラー？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['if_block_noterror'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("エラーでない？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['loop'] = {
  init: function() {
    this.appendDummyInput()
        .appendField(new Blockly.FieldNumber(3, 1, 5, 1), "times")
        .appendField("回繰り返す");
    this.appendStatementInput("loop_play")
        .setCheck(null)
        .appendField("実行");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#428a43");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['m_send'] = {
  init: function() {
    this.appendDummyInput()
        .appendField(new Blockly.FieldTextInput("0"), "destination")
        .appendField("番に")
       .appendField(new Blockly.FieldDropdown([["入力メッセージ","input_message"], ["変数１","variable1"], ["変数２","variable2"], ["変数３","variable3"], ["変数４","variable4"], ["変数５","variable5"]]), "messagedata")
       .appendField("を送信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour(230);
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['stamp_send'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("スタンプを")
        .appendField(new Blockly.FieldTextInput("0"), "destination")
        .appendField("番に送信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour(230);
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['picturem_send'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("写真（画面下で選択）を")
        .appendField(new Blockly.FieldTextInput("0"), "destination")
        .appendField("に送信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour(230);
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['program_send'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("プログラムデータ（画面下で選択）")
        .appendField(new Blockly.FieldDropdown([["ファイル１","file1"], ["ファイル２","file2"], ["ファイル３","file3"]]), "file_no")
        .appendField("を")
        .appendField(new Blockly.FieldTextInput("0"), "destination")
        .appendField("に送信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour(230);
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['m_group_send'] = {
  init: function() {
    this.appendDummyInput()
        .appendField(new Blockly.FieldDropdown([["グループ１","1"], ["グループ２","2"], ["グループ３","3"]]), "m_group")
        .appendField("に")
        .appendField(new Blockly.FieldDropdown([["入力メッセージ","input_message"], ["変数１","variable1"], ["変数２","variable2"], ["変数３","variable3"], ["変数４","variable4"], ["変数５","variable5"]]), "messagedata")
        .appendField("を送信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour(230);
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['stamp_group_send'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("スタンプを")
        .appendField(new Blockly.FieldDropdown([["グループ１","1"], ["グループ２","2"], ["グループ３","3"]]), "m_group")
        .appendField("に送信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour(230);
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['picture_group_send'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("写真（画面下で選択）を")
        .appendField(new Blockly.FieldDropdown([["グループ１","1"], ["グループ２","2"], ["グループ３","3"]]), "m_group")
        .appendField("に送信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour(230);
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['program_group_send'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("プログラムデータ（画面下で選択）")
        .appendField(new Blockly.FieldDropdown([["ファイル１","file1"], ["ファイル２","file2"], ["ファイル３","file3"]]), "file_no")
        .appendField("を")
        .appendField(new Blockly.FieldDropdown([["グループ１","1"], ["グループ２","2"], ["グループ３","3"]]), "m_group")
        .appendField("に送信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour(230);
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['ai_send'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("メッセージをAIで処理し")
        .appendField(new Blockly.FieldTextInput("0"), "destination")
        .appendField("番に送信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#506373");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['ai_change'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("メッセージをAIで")
        .appendField(new Blockly.FieldDropdown([["10","10"], ["20","20"], ["30","30"]]), "maxstlength")
        .appendField("文字以内で取得する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#506373");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['ai_para_change'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("メッセージをAIで")
        .appendField(new Blockly.FieldTextInput("英語"), "param")
        .appendField("に変換する(送信用)");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#506373");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['ai_prompt_change'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("メッセージを設定したAIプロンプトで変換する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#506373");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['set_server'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("サーバを")
        .appendField(new Blockly.FieldTextInput(""), "server_add")
        .appendField("に設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#ff8c1a");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['set_pass'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("パスワードを")
        .appendField(new Blockly.FieldTextInput(""), "passst")
        .appendField("に設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#ff8c1a");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['set_impo'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("重要なメッセージである");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#ff8c1a");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['disp_confirm'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("確認画面を表示");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#ff8c1a");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['play_sound'] = {
  init: function() {
    this.appendDummyInput()
         .appendField("音")
        .appendField(new Blockly.FieldDropdown([["1","0"], ["2","1"], ["3","2"], ["4","3"], ["5","4"], ["6","5"], ["7","6"], ["8","7"], ["9","8"], ["10","9"]]), "soundno")
        .appendField("を鳴らす");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#cf63cf");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['client_variable_1'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("変数１に")
        .appendField(new Blockly.FieldTextInput(""), "variablest")
        .appendField("を設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#9e9065");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['client_variable_2'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("変数２に")
        .appendField(new Blockly.FieldTextInput(""), "variablest")
        .appendField("を設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#9e9065");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['client_variable_3'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("変数３に")
        .appendField(new Blockly.FieldTextInput(""), "variablest")
        .appendField("を設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#9e9065");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['client_variable_4'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("変数４に")
        .appendField(new Blockly.FieldTextInput(""), "variablest")
        .appendField("を設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#9e9065");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['client_variable_5'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("変数５に")
        .appendField(new Blockly.FieldTextInput(""), "variablest")
        .appendField("を設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#9e9065");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['font_size'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("フォントサイズを")
        .appendField(new Blockly.FieldNumber(12, 5, 20, 1), "f_size")
        .appendField("にする");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5cd6d6");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['font_color'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("メッセージを")
	    .appendField(new Blockly.FieldColour("#ff0000"), "f_color")
        .appendField("色にする");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5cd6d6");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['font_backcolor'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("メッセージ背景色を")
	    .appendField(new Blockly.FieldColour("#ff0000"), "f_backcolor")
        .appendField("にする");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5cd6d6");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['disp_backcolor'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("画面背景色を")
	    .appendField(new Blockly.FieldColour("#ff0000"), "d_backcolor")
        .appendField("にする");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5cd6d6");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['wait_light'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("明るくなるまで待つ")
        .appendField(new Blockly.FieldNumber(50, 1, 100, 1), "light");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5ed6a0");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['wait_dark'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("暗くなるまで待つ")
        .appendField(new Blockly.FieldNumber(50, 1, 100, 1), "light");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5ed6a0");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['wait_tempup'] = {
  init: function() {
    this.appendDummyInput()
        .appendField(new Blockly.FieldNumber(25, 1, 50, 1), "temp")
        .appendField("度以上になるまで待つ");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5ed6a0");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['wait_tempdown'] = {
  init: function() {
    this.appendDummyInput()
        .appendField(new Blockly.FieldNumber(25, 1, 50, 1), "temp")
        .appendField("度以下になるまで待つ");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5ed6a0");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['wait_signal'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("信号入力があるまで待つ");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5ed6a0");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['wait_time'] = {
  init: function() {
    this.appendDummyInput()
        .appendField(new Blockly.FieldNumber(12, 0, 23, 1), "time_ji")
        .appendField("時")
        .appendField(new Blockly.FieldNumber(0, 0, 59, 1), "time_hun")
        .appendField("分まで待つ");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5ed6a0");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['wait_teachablemachine'] = {
  init: function() {
    this.appendDummyInput()
        .appendField(new Blockly.FieldTextInput("機械学習したclass名"), "teachablemachine")
        .appendField("が")
        .appendField(new Blockly.FieldNumber(80, 0, 100, 1), "paravalue")
        .appendField("％以上になるまで待つ");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5ed6a0");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};

Blockly.Blocks['server_send'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("メッセージを送る");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5cb1d6");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_error'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("送信元に「")
        .appendField(new Blockly.FieldTextInput("エラー"), "st")
        .appendField("」を返信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#f27e91");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_return'] = {
  init: function() {
    this.appendDummyInput()
      .appendField("送信元に「")
      .appendField(new Blockly.FieldDropdown([["変数１","variable1"], ["変数２","variable2"], ["変数３","variable3"], ["変数４","variable4"], ["変数５","variable5"], ["変数(ランダム)","random"]]), "variable")
      .appendField("」を返信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#f27e91");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_changeai_return'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("AIで取得したメッセージを返信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#f27e91");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_programsend'] = {
  init: function() {
    this.appendDummyInput()
        .appendField(new Blockly.FieldTextInput("0"), "destination")
        .appendField("番に")
        .appendField(new Blockly.FieldDropdown([["ファイル１","file1"], ["ファイル２","file2"], ["ファイル３","file3"]]), "file_no")
        .appendField("を送る");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5cb1d6");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_group_programsend'] = {
  init: function() {
    this.appendDummyInput()
        .appendField(new Blockly.FieldDropdown([["グループ１","1"], ["グループ２","2"], ["グループ３","3"]]), "p_group")
        .appendField("に")
        .appendField(new Blockly.FieldDropdown([["ファイル１","file1"], ["ファイル２","file2"], ["ファイル３","file3"]]), "file_no")
        .appendField("を送る");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#5cb1d6");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_programreturn'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("送信元に")
        .appendField(new Blockly.FieldDropdown([["ファイル１","file1"], ["ファイル２","file2"], ["ファイル３","file3"]]), "file_no")
        .appendField("を返信する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#f27e91");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_pass_set'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("パスワードを")
        .appendField(new Blockly.FieldTextInput(""), "passst")
        .appendField("に設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#ff8c1a");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_ai_message_change'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("メッセージをAIで")
        .appendField(new Blockly.FieldTextInput(""), "changest")
        .appendField("に変換する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#506373");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_ai_question_anser'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("メッセージの質問をAIで取得する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#506373");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_variable_1'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("変数１に")
        .appendField(new Blockly.FieldTextInput(""), "variablest")
        .appendField("を設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#9e9065");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_variable_2'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("変数２に")
        .appendField(new Blockly.FieldTextInput(""), "variablest")
        .appendField("を設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#9e9065");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_variable_3'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("変数３に")
        .appendField(new Blockly.FieldTextInput(""), "variablest")
        .appendField("を設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#9e9065");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_variable_4'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("変数４に")
        .appendField(new Blockly.FieldTextInput(""), "variablest")
        .appendField("を設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#9e9065");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_variable_5'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("変数５に")
        .appendField(new Blockly.FieldTextInput(""), "variablest")
        .appendField("を設定する");
    this.setPreviousStatement(true, null);
    this.setNextStatement(true, null);
    this.setColour("#9e9065");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_ai_kinsi'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("AIが")
        .appendField(new Blockly.FieldTextInput("誹謗中傷"), "param")
        .appendField("と判断した？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_kinsi'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("禁止ワードがある？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_notkinsi'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("禁止ワードがない？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_toroku'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("登録ワードがある？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_nottoroku'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("登録ワードがない？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_juyo'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("重要？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_notjuyo'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("重要でない？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_pass'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("パスワードが一致？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_notpass'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("パスワードが不一致？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_perno'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("生徒番号が")
	    .appendField(new Blockly.FieldNumber(1, 1, 40, 1), "perno")
        .appendField("なら");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_notperno'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("生徒番号が")
	    .appendField(new Blockly.FieldNumber(1, 1, 40, 1), "perno")
        .appendField("でないなら");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_stlength'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("文字数が")
	    .appendField(new Blockly.FieldNumber(1, 1, 20, 1), "perno")
        .appendField("文字以上？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_notstlength'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("文字数が")
	    .appendField(new Blockly.FieldNumber(1, 1, 20, 1), "perno")
        .appendField("文字以下？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_myname'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("送信者の名前がある？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_notmyname'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("送信者の名前がない？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_loopcnt'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("繰り返し送信：")
	    .appendField(new Blockly.FieldNumber(1, 1, 5, 1), "sno")
        .appendField("回以上？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_notloopcnt'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("繰り返し送信：")
	    .appendField(new Blockly.FieldNumber(1, 1, 5, 1), "sno")
        .appendField("回以下？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_group'] = {
  init: function() {
    this.appendDummyInput()    
        .appendField("送信者が")
	      .appendField(new Blockly.FieldDropdown([["グループ１","group1"], ["グループ２","group2"], ["グループ３","group3"]]), "group")
        .appendField("にいる？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};
Blockly.Blocks['server_if_block_notgroup'] = {
  init: function() {
    this.appendDummyInput()
        .appendField("送信者が")
	      .appendField(new Blockly.FieldDropdown([["グループ１","group1"], ["グループ２","group2"], ["グループ３","group3"]]), "group")
        .appendField("にいない？");
    this.setOutput(true, null);
    this.setColour("#59c059");
 this.setTooltip("");
 this.setHelpUrl("");
  }
};