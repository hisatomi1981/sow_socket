/**
 * WebSocket Server for Chat Application
 *
 * ポーリング方式をWebSocket方式に変換するブリッジサーバー。
 * PHP/PostgreSQL のバックエンドはそのまま維持し、
 * クライアント間のリアルタイム通信をWebSocketで実現する。
 *
 * 起動方法: node websocket_server.js
 * 環境変数:
 *   WS_PORT      WebSocketのポート番号 (デフォルト: 8080)
 *   PHP_BASE_URL PHPエンドポイントのベースURL (デフォルト: http://localhost/sow_socket/chat/php)
 */

'use strict';

const WebSocket = require('ws');
const http = require('http');
const { URLSearchParams } = require('url');

const WS_PORT = parseInt(process.env.WS_PORT) || 8080;
const PHP_BASE = (process.env.PHP_BASE_URL || 'http://localhost/sow_ai_tm/chat/php').replace(/\/$/, '');

const wss = new WebSocket.Server({ port: WS_PORT });

// 接続クライアント管理
// key: "school_id:chat_group:clientno" → { ws, school_id, chat_group, clientno, role }
const registry = new Map();

// ============================================================
// ユーティリティ関数
// ============================================================

/** WebSocketメッセージを安全に送信 */
function wsSend(ws, msg) {
  if (ws && ws.readyState === WebSocket.OPEN) {
    ws.send(JSON.stringify(msg));
  }
}

/** 特定クライアントへプッシュ */
function pushToClient(school_id, chat_group, clientno, msg) {
  const key = `${school_id}:${chat_group}:${clientno}`;
  const entry = registry.get(key);
  if (entry) wsSend(entry.ws, msg);
}

/** 同一school/chat_groupの全クライアント（サーバ以外）へプッシュ */
function pushToAllClients(school_id, chat_group, msg) {
  for (const entry of registry.values()) {
    if (entry.school_id === school_id &&
        entry.chat_group === chat_group &&
        entry.role !== 'server') {
      wsSend(entry.ws, msg);
    }
  }
}

/** 同一school/chat_groupの全サーバーへプッシュ */
function pushToAllServers(school_id, chat_group, msg) {
  for (const entry of registry.values()) {
    if (entry.school_id === school_id &&
        entry.chat_group === chat_group &&
        entry.role === 'server') {
      wsSend(entry.ws, msg);
    }
  }
}

/** PHPエンドポイントへPOSTリクエスト */
function phpPost(endpoint, params) {
  return new Promise((resolve) => {
    const body = new URLSearchParams(params).toString();
    //console.log('DEBUG body:', body);
    
    const urlStr = `${PHP_BASE}/${endpoint}`;

    let urlObj;
    try { urlObj = new URL(urlStr); } catch(e) { resolve(null); return; }

    const options = {
      hostname: urlObj.hostname,
      port: parseInt(urlObj.port) || 80,
      path: urlObj.pathname,
      method: 'POST',
      headers: {
	'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
    'Content-Length': Buffer.byteLength(body, 'utf8'),
      },
    };

    const req = http.request(options, (res) => {
      let data = '';
      res.on('data', (chunk) => { data += chunk; });
      res.on('end', () => {
        try { resolve(JSON.parse(data)); }
        catch (e) { resolve(null); }
      });
    });

    req.on('error', (err) => {
      console.error(`phpPost error [${endpoint}]:`, err.message);
      resolve(null);
    });

    req.setTimeout(10000, () => {
      req.destroy();
      resolve(null);
    });

    req.write(body);
    req.end();
  });
}

// ============================================================
// 接続後の初期データ配信（未読メッセージのキャッチアップ）
// ============================================================
async function deliverInitialMessages(ws, school_id, chat_group, clientno, role) {
  try {
    if (role !== 'server') {
      // クライアント: 3種類のチェックを並行実行
      const [d_cb, d_err, d_ca] = await Promise.all([
        phpPost('client_b_check.php',      { clientb: clientno, school_id, chat_group }),
        phpPost('client_a_error_check.php', { clienta: clientno, school_id, chat_group }),
        phpPost('client_a_check.php',       { clienta: clientno, school_id, chat_group }),
      ]);

      if (Array.isArray(d_cb)  && d_cb.length  > 0) wsSend(ws, { type: 'initial_client_b', data: d_cb  });
      if (Array.isArray(d_err) && d_err.length > 0) wsSend(ws, { type: 'initial_error',    data: d_err });
      if (Array.isArray(d_ca)  && d_ca.length  > 0) wsSend(ws, { type: 'initial_client_a', data: d_ca  });
    } else {
      // サーバー: 未処理メッセージをチェック
      const d_sv = await phpPost('server_check.php', { server: clientno, school_id, chat_group });
      if (Array.isArray(d_sv) && d_sv.length > 0) {
        wsSend(ws, { type: 'initial_server', data: d_sv });
      }
    }
  } catch (e) {
    console.error('deliverInitialMessages error:', e);
  }
}
// ============================================================
// 宛先を解析して必要なクライアントだけに未読をプッシュ（並列実行）
//
// clientBStr 例:
//   "30"          → クライアント30だけ
//   "30/31/32"    → クライアント30,31,32（グループ）
//   "group=30/31" → 同上（グループ名付き）
//   ""            → 全員チェック（フォールバック）
// ============================================================
async function pushToRecipients(school_id, chat_group, clientBStr) {
  // 文字列から数字を全て抽出（グループ形式にも対応）
  const numbers = clientBStr ? clientBStr.match(/\d+/g) : null;

  if (!numbers || numbers.length === 0) {
    // 宛先不明の場合は全員を並列チェック（フォールバック）
    await pushPendingToAllClients(school_id, chat_group);
    return;
  }

  // 重複除去してから並列実行
  const recipients = [...new Set(numbers)];
  await Promise.all(recipients.map(async (clientno) => {
    const key   = `${school_id}:${chat_group}:${clientno}`;
    const entry = registry.get(key);
    if (!entry) return; // 未接続なら送信不要（DBには残るのでログイン時に取得）
    try {
      const rows = await phpPost('client_b_check.php', {
        clientb: clientno, school_id, chat_group
      });
      if (Array.isArray(rows) && rows.length > 0) {
        wsSend(entry.ws, { type: 'initial_client_b', data: rows });
      }
    } catch (e) {
      console.error('pushToRecipients error:', e);
    }
  }));
}

// ============================================================
// 全クライアントへ未読をプッシュ（並列実行・フォールバック用）
// ============================================================
async function pushPendingToAllClients(school_id, chat_group) {
  const entries = [];
  for (const entry of registry.values()) {
    if (entry.school_id === school_id &&
        entry.chat_group === chat_group &&
        entry.role !== 'server') {
      entries.push(entry);
    }
  }
  // 全員分を並列実行（awaitを直列に積まない）
  await Promise.all(entries.map(async (entry) => {
    try {
      const rows = await phpPost('client_b_check.php', {
        clientb: entry.clientno, school_id, chat_group
      });
      if (Array.isArray(rows) && rows.length > 0) {
        wsSend(entry.ws, { type: 'initial_client_b', data: rows });
      }
    } catch (e) {
      console.error('pushPendingToAllClients error:', e);
    }
  }));
}

// ============================================================
// メインのWebSocket接続ハンドラ
// ============================================================
wss.on('connection', (ws) => {
  let myKey  = null;
  let myInfo = null;

  ws.on('message', async (rawData) => {
    let msg;
    try { msg = JSON.parse(rawData); }
    catch (e) { console.error('JSON parse error:', e); return; }

    const { type } = msg;

    // ── register ─────────────────────────────────────────────
    if (type === 'register') {
      const { school_id, chat_group, clientno, role = 'client' } = msg;

      // 既存エントリがあれば上書き
      if (myKey) registry.delete(myKey);

      myKey  = `${school_id}:${chat_group}:${clientno}`;
      myInfo = { ws, school_id, chat_group, clientno, role };
      registry.set(myKey, myInfo);

      console.log(`[connect] ${myKey} (${role}) — total: ${registry.size}`);

      // 未読メッセージを配信
      await deliverInitialMessages(ws, school_id, chat_group, clientno, role);
      return;
    }

    // register前のメッセージは無視
    if (!myInfo) return;

    // ── client_send ──────────────────────────────────────────
    // クライアントAがメッセージ送信
    if (type === 'client_send') {
      const result = await phpPost('client_a_upload.php', {
        alldata: msg.alldata,
        status:  'アップ',
      });

      if (!result || result.error) {
        console.error('client_a_upload failed:', result);
        return;
      }

      // 送信者へ確認を返す
      wsSend(ws, { type: 'send_confirm', ans: result.ans });

      // alldataを解析して宛先を判断
      const parts    = msg.alldata.split(',');
      const serverNo = parts[5];  // サーバ番号
      const clientB  = parts[6];  // 送信先クライアント

      if (serverNo && serverNo !== '') {
        // サーバ経由: サーバに新メッセージを通知
        const serverRows = await phpPost('server_check.php', {
          server:     serverNo,
          school_id:  myInfo.school_id,
          chat_group: myInfo.chat_group,
        });
        if (Array.isArray(serverRows) && serverRows.length > 0) {
          pushToAllServers(myInfo.school_id, myInfo.chat_group, {
            type: 'initial_server',
            data: serverRows,
          });
        }
      } else if (clientB && clientB !== '') {
        // 直接配信: 宛先を特定してプッシュ（グループも対応）
        await pushToRecipients(myInfo.school_id, myInfo.chat_group, clientB);
      }
      return;
    }

    // ── client_b_received ────────────────────────────────────
    // クライアントBが受信を確認・DBに記録
    if (type === 'client_b_received') {
      const result = await phpPost('client_b_upload.php', {
        updata:  msg.updata,
        clientb: msg.clientb,
      });
      if (result && result.ans) {
        wsSend(ws, { type: 'client_b_confirm', ans: result.ans });
      }
      // スタイル情報があれば更新
      if (msg.recievedata) {
        await phpPost('client_b_update.php', {
          updata:      msg.updata,
          recievedata: msg.recievedata,
        });
      }
      return;
    }

    // ── error_upload ─────────────────────────────────────────
    // エラー情報をDBに記録して表示
    if (type === 'error_upload') {
      const result = await phpPost('client_a_error_upload.php', {
        alldata:    msg.alldata,
        status:     msg.status,
        school_id:  msg.school_id,
        chat_group: msg.chat_group,
      });
      if (result && result.ans) {
        wsSend(ws, { type: 'error_confirm', ans: result.ans, status: msg.status });
      }
      return;
    }

    // ── server_upload ─────────────────────────────────────────
    // サーバが処理結果(OK/NG)をDBに保存
    if (type === 'server_upload') {
      const result = await phpPost('server_upload.php', { updata: msg.updata });

      if (result && result.ans) {
        // サーバへログ用に返す
        wsSend(ws, { type: 'server_message', data: result.ans });

        // updata: serial,OK/NG,time,name,clientA,clientB,...
        const parts  = msg.updata.split(',');
        const status = parts[1]; // OK or NG

        if (status === 'OK') {
          // 宛先クライアントへ未読メッセージをプッシュ
          // updata: serial,OK,time,name,clientA,clientB,...
          const clientB = parts[5];
          await pushToRecipients(myInfo.school_id, myInfo.chat_group, clientB);
        } else {
          // NG/RET: ClientAへエラーをプッシュ
          const clientA = parts[4];
          const errRows = await phpPost('client_a_error_check.php', {
            clienta:    clientA,
            school_id:  myInfo.school_id,
            chat_group: myInfo.chat_group,
          });
          if (Array.isArray(errRows) && errRows.length > 0) {
            pushToClient(myInfo.school_id, myInfo.chat_group, clientA, {
              type: 'initial_error',
              data: errRows,
            });
          }
        }
      }
      return;
    }

    // ── server_send ───────────────────────────────────────────
    // サーバからクライアントへ直接メッセージ送信
    if (type === 'server_send') {
      const result = await phpPost('server_send_upload.php', {
        alldata:    msg.alldata,
        school_id:  msg.school_id,
        chat_group: msg.chat_group,
      });

      if (result && result.ans) {
        // サーバへログ用に返す
        wsSend(ws, { type: 'server_message', data: result.ans });

        // 宛先クライアントへ client_a_check (サーバ種別メッセージ) をプッシュ
        // alldata: [0]=school_id,[1]=chat_group,...,[6]=clientB
        const sendParts = msg.alldata.split(',');
        const targetB   = sendParts[6]; // 送信先クライアント番号
        const numbers   = targetB ? targetB.match(/\d+/g) : null;
        const targets   = numbers
          ? [...new Set(numbers)]
          : (() => {
              // 宛先不明時は全員（並列）
              const all = [];
              for (const e of registry.values()) {
                if (e.school_id === msg.school_id &&
                    e.chat_group === msg.chat_group &&
                    e.role !== 'server') all.push(e.clientno);
              }
              return all;
            })();

        await Promise.all(targets.map(async (clientno) => {
          const key   = `${msg.school_id}:${msg.chat_group}:${clientno}`;
          const entry = registry.get(key);
          if (!entry) return;
          const caRows = await phpPost('client_a_check.php', {
            clienta:    clientno,
            school_id:  msg.school_id,
            chat_group: msg.chat_group,
          });
          if (Array.isArray(caRows) && caRows.length > 0) {
            wsSend(entry.ws, { type: 'initial_client_a', data: caRows });
          }
        }));
      }
      return;
    }

    console.warn('Unknown message type:', type);
  });

  ws.on('close', () => {
    if (myKey) {
      registry.delete(myKey);
      console.log(`[disconnect] ${myKey} — total: ${registry.size}`);
    }
  });

  ws.on('error', (err) => {
    console.error('WebSocket client error:', err.message);
    if (myKey) registry.delete(myKey);
  });
});

wss.on('error', (err) => {
  console.error('WebSocket server error:', err);
});

console.log(`WebSocket server started on ws://localhost:${WS_PORT}`);
console.log(`PHP base URL: ${PHP_BASE}`);
