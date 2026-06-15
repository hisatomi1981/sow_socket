<?php
// JSONとして返す場合は、Content-Typeヘッダを設定
header('Content-Type: application/json; charset=utf-8');

// DB接続
require_once("../../../function/database.php");

try {
    // 必要なPOSTパラメータがあるかチェック
    if (!isset($_POST['alldata'], $_POST['status'], $_POST['school_id'], $_POST['chat_group'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => true, 'message' => 'Missing parameters']);
        exit;
    }

    // 変数に格納
    $alldata    = $_POST['alldata'];    // カンマ区切り文字列
    $status     = $_POST['status'];     // "NG" かどうか
    $school_id  = $_POST['school_id'];
    $chat_group = $_POST['chat_group'];

    // $errordata: カンマ区切りを配列に変換 (id,時間,名前,clientA,server,種類,重要,繰り返し回数,パスワード,音,保存ファイル名,メッセージ, etc...)
    $errordata = explode(",", $alldata);

    // 必要要素数が足りない場合はエラーにするなど
    if (count($errordata) < 13) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Not enough elements in alldata']);
        exit;
    }

    // ---------------------------------------
    // 1) clientadata にINSERT
    // ---------------------------------------
    // $status_w の設定
    if ($status === "NG") {
        $status_w = "エラー";
    } else {
        $status_w = "返信";
    }

    // INSERT するデータ
    // (キー => テーブルのカラム名, 値 => 実際にINSERTする値)
    $data1 = [
        'status_clienta'       => $status_w,
        'school_id_clienta'    => $school_id,
        'chat_group_clienta'   => $chat_group,
        'time_clienta'         => $errordata[1],
        'client_a_name_clienta'=> $errordata[2],
        'client_a_clienta'     => $errordata[3],
        'server_clienta'       => $errordata[4],
        'client_b_clienta'     => '',
        'data_kind_clienta'    => $status_w,  // エラー or 返信
        'juyo_clienta'         => $errordata[6],
        'read_clienta'         => '',
        'repeat_cnt_clienta'   => $errordata[7],
        'password_clienta'     => $errordata[8],
        'sound_clienta'        => $errordata[9],
        'font_siza_clienta'    => '',
        'font_color_clienta'   => '',
        'back_color_clienta'   => '',
        'disp_color_clienta'   => '',
        'save_file_clienta'    => $errordata[10],
        'message_st_clienta'   => $errordata[11],
    ];

    // 動的にINSERT文作成
    $columns1      = [];
    $placeholders1 = [];
    $bindParams1   = [];
    $i = 1;
    foreach ($data1 as $key => $val) {
        $columns1[]       = "\"$key\"";       // PostgreSQLでカラム名をダブルクォート
        $ph = ":p{$i}";
        $placeholders1[]  = $ph;
        $bindParams1[$ph] = $val;
        $i++;
    }
    $sqlInsert1 = "
        INSERT INTO clientadata (" . implode(',', $columns1) . ")
        VALUES (" . implode(',', $placeholders1) . ")
        RETURNING serial_number_clienta
    ";

    // 実行
    $stmt = $pdo->prepare($sqlInsert1);
    foreach ($bindParams1 as $ph => $val) {
        $stmt->bindValue($ph, $val, PDO::PARAM_STR);
    }
    $stmt->execute();

    // serial_number_clienta を取得
    $serino = $stmt->fetchColumn();

    // ---------------------------------------
    // 2) 直近で挿入した clientadata のシリアル取得
    // ---------------------------------------
    // currval('clientadata_serial_number_clienta_seq')
    /*$sqlCurrval = "SELECT currval('clientadata_serial_number_clienta_seq') AS currval";
    $stmt = $pdo->query($sqlCurrval);
    if (!$stmt) {
        $info = $pdo->errorInfo();
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => $info[2]]);
        exit;
    }
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $row   = $stmt->fetch();
    $serino = $row['currval'];*/

    // ---------------------------------------
    // 3) 同じ番号が serverdata に登録済みか確認
    // ---------------------------------------
    $sqlCheckServer = "
        SELECT *
          FROM serverdata
         WHERE source_id_server = :serino
    ";
    $stmt = $pdo->prepare($sqlCheckServer);
    $stmt->bindValue(':serino', $serino, PDO::PARAM_STR);
    $stmt->execute();
    $column_cnt = $stmt->rowCount();
    // もし1件以上あれば処理終了
    if ($column_cnt > 0) {
        $pdo = null;
        echo json_encode(['ans' => $alldata, 'info' => 'Already exists in serverdata']);
        exit;
    }

    // ---------------------------------------
    // 4) serverdata にINSERT
    // ---------------------------------------
    $data2 = [
        'source_id_server' => (string)$serino,
        // "NG" なら "エラー受信 XX"
        // そうでなければ "返信受信 XX"
        'status_server'    => ($status === "NG") ? "エラー受信 ".$errordata[0] : "返信受信 ".$errordata[0],
        'message_st_server'=> $errordata[11],
    ];
    $columns2      = [];
    $placeholders2 = [];
    $bindParams2   = [];
    $j = 1;
    foreach ($data2 as $key => $val) {
        $columns2[]       = "\"$key\"";
        $ph = ":q{$j}";
        $placeholders2[]  = $ph;
        $bindParams2[$ph] = $val;
        $j++;
    }
    $sqlInsert2 = "
        INSERT INTO serverdata (" . implode(',', $columns2) . ")
        VALUES (" . implode(',', $placeholders2) . ")
    ";
    $stmt = $pdo->prepare($sqlInsert2);
    foreach ($bindParams2 as $ph => $val) {
        $stmt->bindValue($ph, $val, PDO::PARAM_STR);
    }
    $stmt->execute();

    // ---------------------------------------
    // 5) serverdata の別レコードを UPDATE
    //   (直前のデータではなく、errordata[0] の行)
    // ---------------------------------------
    // "NG" なら "エラー受信 XX"、でなければ "返信受信 XX" に更新
    $data3 = [
        'status_server'    => ($status === "NG") ? "エラー受信 ".$errordata[0] : "返信受信 ".$errordata[0],
        'message_st_server'=> $errordata[12],
    ];

    // 動的にUPDATE文作成
    $sets3       = [];
    $bindParams3 = [];
    $k = 1;
    foreach ($data3 as $key => $val) {
        $ph = ":r{$k}";
        $sets3[]        = "\"$key\" = $ph";
        $bindParams3[$ph] = $val;
        $k++;
    }
    // "WHERE source_id_server = errordata[0]"
    $sqlUpdate3 = "
        UPDATE serverdata
           SET " . implode(',', $sets3) . "
         WHERE source_id_server = :prev_id
    ";
    $stmt = $pdo->prepare($sqlUpdate3);

    // バインド
    foreach ($bindParams3 as $ph => $val) {
        $stmt->bindValue($ph, $val, PDO::PARAM_STR);
    }
    // WHERE 条件
    $stmt->bindValue(':prev_id', $errordata[0], PDO::PARAM_STR); // id は数値想定なら PARAM_INT

    $stmt->execute();

    // ---------------------------------------
    // 正常終了レスポンス
    // ---------------------------------------
    $returnData = ["ans" => $alldata];

} catch (PDOException $e) {
    // DBエラー時
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit;
} finally {
    $pdo = null;
}

// 成功時レスポンス
echo json_encode($returnData);
exit;
