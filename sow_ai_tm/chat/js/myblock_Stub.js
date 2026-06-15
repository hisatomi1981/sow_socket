Blockly.JavaScript['start'] = function(block) {
  var code = 'start\n';
  return code;
};
Blockly.JavaScript['server_start'] = function(block) {
  var code = 'server_start\n';
  return code;
};
Blockly.JavaScript['start_r'] = function(block) {
  var code = 'start_r\n';
  return code;
};
Blockly.JavaScript['if_yes'] = function(block) {
  var value_if_jeken = Blockly.JavaScript.valueToCode(block, 'if_jeken', Blockly.JavaScript.ORDER_ATOMIC);
  var statements_yes = Blockly.JavaScript.statementToCode(block, 'yes');
  // TODO: Assemble JavaScript into code variable.
  var code = 'doIf' + value_if_jeken + '\n' +statements_yes + 'endif1,\n';
  return code;
};
Blockly.JavaScript['if_else'] = function(block) {
  var value_if_jeken = Blockly.JavaScript.valueToCode(block, 'if_jeken', Blockly.JavaScript.ORDER_ATOMIC);
  var statements_yes = Blockly.JavaScript.statementToCode(block, 'yes');
  var statements_no = Blockly.JavaScript.statementToCode(block, 'no');
  // TODO: Assemble JavaScript into code variable.
  var code = 'doIf' + value_if_jeken + '\n' + statements_yes + 'endif1, else {\n' + statements_no + 'endif2,\n';
  return code;
};
Blockly.JavaScript['if_block_beforetime'] = function(block) {
  var parast = block.getFieldValue('settime');
  var code = ' time< ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_aftertime'] = function(block) {
  var parast = block.getFieldValue('settime');
  var code = ' time>= ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_impo'] = function(block) {
  var code = ' if_block_impo ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_notimpo'] = function(block) {
  var code = ' if_block_notimpo ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_light'] = function(block) {
  var number_if_light = block.getFieldValue('if_light');
  var code = ' light>= ' + number_if_light + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_dark'] = function(block) {
  var number_if_light = block.getFieldValue('if_light');
  var code = ' light< ' + number_if_light + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_tempup'] = function(block) {
  var number_if_light = block.getFieldValue('if_light');
  var code = ' temp> ' + number_if_light + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_tempdown'] = function(block) {
  var number_if_light = block.getFieldValue('if_light');
  var code = ' temp< ' + number_if_light + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_signal'] = function(block) {
  var code = ' if_block_signal ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_notsignal'] = function(block) {
  var code = ' if_block_notsignal ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_teachablemachine'] = function(block) {
  var if_teachablemachine = block.getFieldValue('if_teachablemachine');
  var paravalue = block.getFieldValue('paravalue');
  var code = ' teachablemachine> ' + if_teachablemachine + ' '+ paravalue + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_notteachablemachine'] = function(block) {
  var if_teachablemachine = block.getFieldValue('if_teachablemachine');
  var paravalue = block.getFieldValue('paravalue');
  var code = ' teachablemachine< ' + if_teachablemachine + ' '+ paravalue + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};

Blockly.JavaScript['if_block_seitono'] = function(block) {
  var parast = block.getFieldValue('sno');
  var code = ' personalno= ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_notseitono'] = function(block) {
  var parast = block.getFieldValue('sno');
  var code = ' personalno!= ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_group'] = function(block) {
  var parast = block.getFieldValue('group');
  var code = ' groupno= ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_notgroup'] = function(block) {
  var parast = block.getFieldValue('group');
  var code = ' groupno!= ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_error'] = function(block) {
  var code = ' if_block_error ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['if_block_noterror'] = function(block) {
  var code = ' if_block_noterror ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['loop'] = function(block) {
  var number_times = block.getFieldValue('times');
  var statements_loop_play = Blockly.JavaScript.statementToCode(block, 'loop_play');
  var code = 'doRepeat ' + number_times + '\n' +statements_loop_play + 'endloop,\n';
  return code;
};
Blockly.JavaScript['m_send'] = function(block) { 
  var clientb = block.getFieldValue('destination');
  var messagedata = block.getFieldValue('messagedata');
  var code = 'message_send( ' + clientb + ' ' + messagedata + ' )\n';
  return code;
};
Blockly.JavaScript['stamp_send'] = function(block) {  
  var clientb = block.getFieldValue('destination');
  var code = 'stamp_send( ' + clientb + ' )\n';
  return code;
};
Blockly.JavaScript['picturem_send'] = function(block) {  
  var clientb = block.getFieldValue('destination');
  var code = 'picturem_send( ' + clientb + ' )\n';
  return code;
};
Blockly.JavaScript['program_send'] = function(block) {  
  var clientb = block.getFieldValue('destination');
  var fileno = block.getFieldValue('file_no');
  var code = 'program_send( ' + clientb + ' ' + fileno + ' )\n';
  return code;
};
Blockly.JavaScript['m_group_send'] = function(block) { 
  var clientb = block.getFieldValue('m_group');
  var messagedata = block.getFieldValue('messagedata');
  var code = 'message_group_send( ' + clientb + ' ' + messagedata + ' )\n';
  return code;
};
Blockly.JavaScript['stamp_group_send'] = function(block) {  
  var clientb = block.getFieldValue('m_group');
  var code = 'stamp_group_send( ' + clientb + ' )\n';
  return code;
};
Blockly.JavaScript['picture_group_send'] = function(block) {  
  var clientb = block.getFieldValue('m_group');
  var code = 'picture_group_send( ' + clientb + ' )\n';
  return code;
};
Blockly.JavaScript['program_group_send'] = function(block) {
  var clientb = block.getFieldValue('m_group');
  var fileno = block.getFieldValue('file_no');
  var code = 'program_group_send( ' + clientb + ' ' + fileno + ' )\n';
  return code;
};
Blockly.JavaScript['ai_send'] = function(block) { 
  var clientb = block.getFieldValue('destination');
  var code = 'ai_send( ' + clientb + ' )\n';
  return code;
};
Blockly.JavaScript['ai_change'] = function(block) { 
  var maxstlength = block.getFieldValue('maxstlength');
  var code = 'ai_change( ' + maxstlength + ' )\n';
  return code;
};
Blockly.JavaScript['ai_para_change'] = function(block) { 
  var parast = block.getFieldValue('param');
  var code = 'ai_para_change( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['ai_prompt_change'] = function(block) { 
  var parast = block.getFieldValue('param');
  var code = 'ai_prompt_change()\n';
  return code;
};
Blockly.JavaScript['set_server'] = function(block) {  
  var add = block.getFieldValue('server_add');
  var code = 'set_server( ' + add + ' )\n';
  return code;
};
Blockly.JavaScript['set_pass'] = function(block) {  
  var pass_st = block.getFieldValue('passst');
  var code = 'set_pass( ' + pass_st + ' )\n';
  return code;
};
Blockly.JavaScript['set_impo'] = function(block) {  
  var code = 'set_impo()\n';
  return code;
};
Blockly.JavaScript['disp_confirm'] = function(block) {  
  var code = 'disp_confirm()\n';
  return code;
};
Blockly.JavaScript['play_sound'] = function(block) {  
  var sno = block.getFieldValue('soundno');
  var code = 'play_sound( ' + sno + ' )\n';
  return code;
};
Blockly.JavaScript['client_variable_1'] = function(block) {  
  var parast = block.getFieldValue('variablest');
  var code = 'client_variable_1( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['client_variable_2'] = function(block) {  
  var parast = block.getFieldValue('variablest');
  var code = 'client_variable_2( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['client_variable_3'] = function(block) {  
  var parast = block.getFieldValue('variablest');
  var code = 'client_variable_3( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['client_variable_4'] = function(block) {  
  var parast = block.getFieldValue('variablest');
  var code = 'client_variable_4( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['client_variable_5'] = function(block) {  
  var parast = block.getFieldValue('variablest');
  var code = 'client_variable_5( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['font_size'] = function(block) {  
  var size = block.getFieldValue('f_size');
  var code = 'font_size( ' + size + ' )\n';
  return code;
};
Blockly.JavaScript['font_color'] = function(block) {  
  var parast = block.getFieldValue('f_color');
  var code = 'font_color( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['font_backcolor'] = function(block) {  
  var parast = block.getFieldValue('f_backcolor');
  var code = 'font_backcolor( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['disp_backcolor'] = function(block) {  
  var parast = block.getFieldValue('d_backcolor');
  var code = 'disp_backcolor( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['wait_light'] = function(block) {  
  var parast = block.getFieldValue('light');
  var code = 'wait_light( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['wait_dark'] = function(block) {  
  var parast = block.getFieldValue('light');
  var code = 'wait_dark( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['wait_tempup'] = function(block) {  
  var parast = block.getFieldValue('temp');
  var code = 'wait_tempup( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['wait_tempdown'] = function(block) {  
  var parast = block.getFieldValue('temp');
  var code = 'wait_tempdown( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['wait_signal'] = function(block) {  
  var code = 'wait_signal()\n';
  return code;
};
Blockly.JavaScript['wait_time'] = function(block) { 
  var para_ji = block.getFieldValue('time_ji');
  var para_hun = block.getFieldValue('time_hun'); 
  var code = 'wait_time( ' + para_ji + ' ' + para_hun + ' )\n';
  return code;
};
Blockly.JavaScript['wait_teachablemachine'] = function(block) { 
  var parast = block.getFieldValue('teachablemachine');
  var paravalue = block.getFieldValue('paravalue');
  var code = 'wait_teachablemachine( ' + parast + ' ' + paravalue + ' )\n';
  return code;
};

Blockly.JavaScript['server_send'] = function(block) {
  var code = 'server_send()\n';
  return code;
};
Blockly.JavaScript['server_error'] = function(block) {  
  var parast = block.getFieldValue('st');
  var code = 'server_error( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['server_return'] = function(block) {
  var parast = block.getFieldValue('variable');
  var code = 'server_return( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['server_changeai_return'] = function(block) {
  var code = 'server_changeai_return()\n';
  return code;
};
Blockly.JavaScript['server_programsend'] = function(block) {
  var clientb = block.getFieldValue('destination');
  var fileno = block.getFieldValue('file_no');
  var code = 'server_programsend( ' + clientb + ' ' + fileno + ' )\n';
  return code;
};
Blockly.JavaScript['server_group_programsend'] = function(block) {
  var clientb = block.getFieldValue('p_group');
  var fileno = block.getFieldValue('file_no');
  var code = 'server_group_programsend( ' + clientb + ' ' + fileno + ' )\n';
  return code;
};
Blockly.JavaScript['server_programreturn'] = function(block) {  
  var fileno = block.getFieldValue('file_no');
  var code = 'server_programreturn( ' + fileno + ' )\n';
  return code;
};
Blockly.JavaScript['server_pass_set'] = function(block) {  
  var parast = block.getFieldValue('passst');
  var code = 'server_pass_set( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['server_variable_1'] = function(block) {  
  var parast = block.getFieldValue('variablest');
  var code = 'server_variable_1( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['server_variable_2'] = function(block) {  
  var parast = block.getFieldValue('variablest');
  var code = 'server_variable_2( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['server_variable_3'] = function(block) {  
  var parast = block.getFieldValue('variablest');
  var code = 'server_variable_3( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['server_variable_4'] = function(block) {  
  var parast = block.getFieldValue('variablest');
  var code = 'server_variable_4( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['server_variable_5'] = function(block) {  
  var parast = block.getFieldValue('variablest');
  var code = 'server_variable_5( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['server_ai_message_change'] = function(block) {  
  var parast = block.getFieldValue('changest');
  var code = 'server_ai_message_change( ' + parast + ' )\n';
  return code;
};
Blockly.JavaScript['server_ai_question_anser'] = function(block) {  
  var code = 'server_ai_question_anser()\n';
  return code;
};
Blockly.JavaScript['server_if_block_ai_kinsi'] = function(block) {
  var parast = block.getFieldValue('param');
  var code = ' aiparam= ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_kinsi'] = function(block) {
  var code = ' kinsi ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_notkinsi'] = function(block) {
  var code = ' notkinsi ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_toroku'] = function(block) {
  var code = ' toroku ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_nottoroku'] = function(block) {
  var code = ' nottoroku ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_juyo'] = function(block) {
  var code = ' juyo ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_notjuyo'] = function(block) {
  var code = ' notjuyo ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_pass'] = function(block) {
  var code = ' pass ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_notpass'] = function(block) {
  var code = ' notpass ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_perno'] = function(block) {
  var parast = block.getFieldValue('perno');
  var code = ' personalno= ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_notperno'] = function(block) {
  var parast = block.getFieldValue('perno');
  var code = ' personalno!= ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_stlength'] = function(block) {
  var parast = block.getFieldValue('perno');
  var code = ' stlength>= ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_notstlength'] = function(block) {
  var parast = block.getFieldValue('perno');
  var code = ' stlength<= ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_myname'] = function(block) {
  var code = ' myname ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_notmyname'] = function(block) {
  var code = ' notmyname ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_loopcnt'] = function(block) {
  var parast = block.getFieldValue('sno');
  var code = ' loopcnt>= ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};
Blockly.JavaScript['server_if_block_notloopcnt'] = function(block) {
  var parast = block.getFieldValue('sno');
  var code = ' loopcnt<= ' + parast + ' ';
  return [code, Blockly.JavaScript.ORDER_NONE];
};