// ============================================================
// Supabase Realtime Broadcast 方式
// ============================================================

var supabaseClient = window.supabase.createClient(
  'https://spdodhlxnklompnlwfgy.supabase.co',   // ← Supabase の Project URL
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InNwZG9kaGx4bmtsb21wbmx3Zmd5Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3Mzk0MTcwMTAsImV4cCI6MjA1NDk5MzAxMH0.mzB0VSVguF19uAEfwr4QLGn6RkV3xO284d6bfs0aAZ4'        // ← Supabase の anon public key
);

var realtimeChannel = null;
var myRole = 'client'; // 'client' or 'server'

// ============================================================
// チャンネル接続
// ============================================================
function wsConnect(role) {
  myRole = role || 'client';

  if (realtimeChannel) {
    supabaseClient.removeChannel(realtimeChannel);
  }

  // school_id と chat_group でチャンネルを分離
  var channelName = 'chat:' + school_id + ':' + chat_group_data;

  realtimeChannel = supabaseClient.channel(channelName, {
    config: { broadcast: { self: false } } // 自分自身には届かない
  })
    // Client A がメッセージ送信 → サーバー or Client B が受信
    .on('broadcast', { event: 'client_send' }, function(payload) {
      handleClientSend(payload.payload);
    })
    // サーバーが処理完了 → Client B が受信
    .on('broadcast', { event: 'server_response' }, function(payload) {
      handleServerResponse(payload.payload);
    })
    // サーバーが Client A/B へ直接送信
    .on('broadcast', { event: 'server_to_client' }, function(payload) {
      handleServerToClient(payload.payload);
    })
    // エラー通知 → Client A が受信
    .on('broadcast', { event: 'error_notify' }, function(payload) {
      handleErrorNotify(payload.payload);
    })
    .subscribe(function(status) {
      var el = document.getElementById('dispstate');
      if (el) el.innerHTML = status === 'SUBSCRIBED' ? '通信中(RT)' : '接続中...';
    });
}

// ============================================================
// Broadcast 受信ハンドラ
// ============================================================

// Client A のメッセージ受信（サーバー役 or Client B が処理）
function handleClientSend(data) {
  var myNo = document.getElementById('myno') ? document.getElementById('myno').value : '';
  if (!myNo) return;

  var serverNo = data.server_no;
  var clientB  = data.client_b;

  // サーバー役の場合：自分宛なら処理
  if (myRole === 'server' && serverNo === myNo) {
    server_up_action(data.row); // DBのrowオブジェクトを渡す
    return;
  }

  // クライアントB（サーバーなし直接通信）の場合
  if (myRole !== 'server' && serverNo === '') {
    var recipients = clientB ? clientB.match(/\d+/g) || [] : [];
    if (recipients.indexOf(myNo) !== -1 || clientB === '') {
      recieve_up_action(data.sendst);
    }
  }
}

// サーバーの処理結果受信 → Client B が表示
function handleServerResponse(data) {
  var myNo = document.getElementById('myno') ? document.getElementById('myno').value : '';
  if (!myNo || myRole === 'server') return;

  var recipients = data.client_b ? data.client_b.match(/\d+/g) || [] : [];
  if (recipients.indexOf(myNo) === -1 && data.client_b !== '') return;

  recieve_up_action(data.sendst);
}

// サーバーからクライアントへ直接送信
function handleServerToClient(data) {
  var myNo = document.getElementById('myno') ? document.getElementById('myno').value : '';
  if (!myNo || myRole === 'server') return;

  var recipients = data.client_b ? data.client_b.match(/\d+/g) || [] : [];
  if (recipients.length > 0 && recipients.indexOf(myNo) === -1) return;

  document.getElementById('messages').innerHTML += message_left(data.message, '');
  delayedCall(100, function() { scroll_change(); });
}

// エラー通知 → Client A が表示
function handleErrorNotify(data) {
  var myNo = document.getElementById('myno') ? document.getElementById('myno').value : '';
  if (!myNo || myRole === 'server') return;
  if (data.client_a !== myNo) return;

  send_error_upload(data.sendst, data.status);
}

// ============================================================
// ポーリング互換関数（既存コードから呼ばれる）
// ============================================================
var pollingTimer        = null;
var pollingInterval     = 2000;
var POLL_MIN            = 2000;
var POLL_MAX            = 5000;
var serverPollingTimer  = null;
var serverPollingInterval = 2000;
var SERVER_POLL_MIN     = 2000;
var SERVER_POLL_MAX     = 3000;

function startPolling()       { wsConnect('client'); }
function startServerPolling() { wsConnect('server'); }
function stopPolling()        { if (realtimeChannel) supabaseClient.removeChannel(realtimeChannel); }
function stopServerPolling()  { stopPolling(); }

// タブ復帰時（未読を Ajax で補完）
function recieve_check_action() {
  var myNo = document.getElementById('myno') ? document.getElementById('myno').value : '';
  if (!myNo) return;
  if (myRole !== 'server') {
    $.ajax({
      type: 'POST', url: './php/client_b_check.php',
      data: { clientb: myNo, school_id: school_id, chat_group: chat_group_data },
      cache: false, dataType: 'json',
      success: function(newData) {
        if (!Array.isArray(newData) || newData.length === 0) return;
        for (var i = 0; i < newData.length; i++) {
          var d = newData[i];
          var disp_message = (d.server_clienta === '' || d.server_clienta == null)
            ? d.message_st_clienta : d.message_st_server;
          var sendst = d.serial_number_clienta + ',' + d.time_clienta + ',' +
                       d.client_a_name_clienta + ',' + d.client_a_clienta + ',' +
                       d.server_clienta + ',' + d.data_kind_clienta + ',' +
                       d.juyo_clienta + ',' + d.repeat_cnt_clienta + ',' +
                       d.password_clienta + ',' + d.sound_clienta + ',' +
                       d.save_file_clienta + ',' + disp_message;
          recieve_up_action(sendst);
        }
      }
    });
  }
}

// ============================================================
// 送信関数
// ============================================================

// Client A がメッセージ送信
function send_action(sendSt) {
  // ① DB保存（PHP）
  $.ajax({
    type: 'POST', url: './php/client_a_upload.php',
    data: { alldata: sendSt, status: 'アップ' },
    cache: false, dataType: 'json',
    success: function(newData) {
      // 自分の画面に送信バブルを表示
      document.getElementById('messages').innerHTML += message_right(newData.ans);
      delayedCall(100, function() { scroll_change(); });

      // ② Broadcast 送信（他クライアントへ通知）
      var parts    = sendSt.split(',');
      var serverNo = parts[5] || '';
      var clientB  = parts[6] || '';
      realtimeChannel.send({
        type:    'broadcast',
        event:   'client_send',
        payload: {
          sendst:    sendSt,
          server_no: serverNo,
          client_b:  clientB,
          row:       newData.row || null, // PHP側でrowを返す場合
        }
      });
    }
  });
}

// エラーアップ（表示 + DB保存）
function send_error_upload(sendSt, status_st) {
  $.ajax({
    type: 'POST', url: './php/client_a_error_upload.php',
    data: { alldata: sendSt, status: status_st, school_id: school_id, chat_group: chat_group_data },
    cache: false, dataType: 'json',
    success: function(newData) {
      for (var i in newData) {
        document.getElementById('messages').innerHTML += message_left(newData['ans'], status_st);
      }
    }
  });
}

// Client B 受信確認をDBに記録
function recieve_up_action(st) {
  var clientno = document.getElementById('myno').value;
  var rdata = '';
  if (recieve_font_size !== '' || recieve_font_color !== '' ||
      recieve_back_color !== '' || recieve_disp_color !== '' || recieve_sound !== '') {
    rdata = recieve_sound + ',' + recieve_font_size + ',' + recieve_font_color + ',' +
            recieve_back_color + ',' + recieve_disp_color;
  }
  $.ajax({
    type: 'POST', url: './php/client_b_upload.php',
    data: { updata: st, clientb: clientno },
    cache: false, dataType: 'json',
    success: function(newData) {
      for (var i in newData) {
        document.getElementById('messages').innerHTML += message_left(newData[i], '');
      }
      if (rdata) { recieve_pro_up_action(st); }
    }
  });
}

function recieve_pro_up_action(st) {
  var r_st = recieve_sound + ',' + recieve_font_size + ',' + recieve_font_color + ',' +
             recieve_back_color + ',' + recieve_disp_color;
  $.ajax({
    type: 'POST', url: './php/client_b_update.php',
    data: { updata: st, recievedata: r_st },
    cache: false, dataType: 'json'
  });
}

// ============================================================
// サーバー側の送受信関数
// ============================================================

// サーバーがメッセージを処理してDBに保存
function server_up_action(st) {
  var up_st = get_serverupload_data(st);
  if (up_st === '') { set_Interval(); return; }
  if (ai_mode === false) { server_up_process(up_st); }
}

// サーバー処理結果をDB保存 + Broadcast
function server_up_process(up_st) {
  // ① DB保存（PHP）
  $.ajax({
    type: 'POST', url: './php/server_upload.php',
    data: { updata: up_st },
    cache: false, dataType: 'json',
    success: function(newData) {
      for (var i in newData) { message_server(newData[i]); }

      // ② Broadcast 送信
      var parts   = up_st.split(',');
      var status  = parts[1]; // OK or NG
      var clientA = parts[4];
      var clientB = parts[5];

      if (status === 'OK') {
        // Client B へ受信データを通知
        realtimeChannel.send({
          type:    'broadcast',
          event:   'server_response',
          payload: { sendst: up_st, client_b: clientB }
        });
      } else {
        // Client A へエラーを通知
        realtimeChannel.send({
          type:    'broadcast',
          event:   'error_notify',
          payload: { sendst: up_st, client_a: clientA, status: status }
        });
      }
    }
  });
}

// サーバーから Client B へ直接送信
function server_send_action(sendSt) {
  // ① DB保存（PHP）
  $.ajax({
    type: 'POST', url: './php/server_send_upload.php',
    data: { alldata: sendSt, school_id: school_id, chat_group: chat_group_data },
    cache: false, dataType: 'json',
    success: function(newData) {
      for (var i in newData) { message_server(newData[i]); }

      // ② Broadcast 送信
      var parts   = sendSt.split(',');
      var clientB = parts[6] || '';
      realtimeChannel.send({
        type:    'broadcast',
        event:   'server_to_client',
        payload: { message: newData, client_b: clientB }
      });
    }
  });
}

// ============================================================
// AI関連（引き続き Ajax）
// ============================================================
function send_ai(sendSt, messageSt) {
  $.ajax({
    type: 'POST', url: './php/gemini.php',
    data: { alldata: sendSt, request: messageSt },
    cache: false,
    success: function(data) {
      var newsendSt = sendSt.replace(messageSt, data);
      send_action(newsendSt);
    }
  });
}

function change_ai(messageSt) {
  $.ajax({
    type: 'POST', url: './php/gemini.php',
    data: { alldata: '', request: messageSt },
    cache: false,
    success: function(data) {
      data = data.replace(/&quot;/g, '');
      data = extractQuotedText(data);
      if (data.indexOf('ません') !== -1) { data = '取得できませんでした'; }
      ai_returnData = data;
    }
  });
}

// ============================================================
// ページ終了時のクリーンアップ
// ============================================================
window.addEventListener('beforeunload', function() {
  if (realtimeChannel) supabaseClient.removeChannel(realtimeChannel);
});
