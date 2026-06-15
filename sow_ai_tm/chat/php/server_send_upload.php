<?php
require_once("../../../function/database.php");

// JSONとして返すヘッダを設定（推奨）
header('Content-Type: application/json; charset=utf-8');

// POSTに 'alldata' が存在するかチェック（必要に応じて追加チェック）
if (!isset($_POST['alldata'], $_POST['school_id'], $_POST['chat_group'])) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Missing parameters: alldata, school_id or chat_group']);
    exit;
}

try {
    // カンマ区切りの文字列を配列に変換
    // 例: id,時間,名前,clientA,server,種類,重要,繰り返し回数,パスワード,音,保存ファイル名,メッセージ ...
    $alldata = explode(",", $_POST['alldata']);

    // clientadata に登録するための配列を組み立て
    // カラム名 => 値 の連想配列 (キーがテーブルのカラム名)
    $data = [
        'status_clienta'       => "サーバ",
        'school_id_clienta'    => $_POST['school_id'],
        'chat_group_clienta'   => $_POST['chat_group'],
        'time_clienta'         => $alldata[2],
        'client_a_name_clienta'=> $alldata[3],
        'client_a_clienta'     => $alldata[4],
        'server_clienta'       => $alldata[5],
        'client_b_clienta'     => $alldata[6],
        'data_kind_clienta'    => $alldata[7],
        'juyo_clienta'         => $alldata[8],
        'read_clienta'         => $alldata[9],
        'repeat_cnt_clienta'   => $alldata[10],
        'password_clienta'     => $alldata[11],
        'sound_clienta'        => $alldata[12],
        'font_siza_clienta'    => $alldata[13],
        'font_color_clienta'   => $alldata[14],
        'back_color_clienta'   => $alldata[15],
        'disp_color_clienta'   => $alldata[16],
        'save_file_clienta'    => $alldata[17],
        'message_st_clienta'   => $alldata[18],
    ];

    // --------------------------------------
    // 1) clientadata に INSERT
    // --------------------------------------
    // カラム名とプレースホルダを作成
    $columns = [];
    $placeholders = [];
    $params = []; // bindValue 用の連想配列

    $i = 1;
    foreach ($data as $key => $val) {
        // PostgreSQL でカラム名をダブルクォートしている場合
        $columns[] = "\"$key\"";
        // :param1, :param2,... のようなプレースホルダを作成
        $ph = ":param{$i}";
        $placeholders[] = $ph;
        $params[$ph] = $val;
        $i++;
    }

    // SQL 文を組み立て
    $sqlInsertClient = "
        INSERT INTO clientadata (" . implode(',', $columns) . ")
        VALUES (" . implode(',', $placeholders) . ")
        RETURNING serial_number_clienta
    ";

    // プリペアドステートメントを準備
    $stmt = $pdo->prepare($sqlInsertClient);

    // パラメータをバインド
    // （型は実際のカラムに合わせて PDO::PARAM_STR や PDO::PARAM_INT を使い分けてください）
    foreach ($params as $ph => $value) {
        $stmt->bindValue($ph, $value, PDO::PARAM_STR);
    }

    // 実行
    $stmt->execute();

    // serial_number_clienta を取得
    $serino = $stmt->fetchColumn();
    // --------------------------------------
    // 2) シリアル（主キー）の取得
    //    currval('clientadata_serial_number_clienta_seq') をSELECT
    // --------------------------------------
    // ユーザー入力なしなのでプリペアドステートメントでなくとも安全ですが、
    // 一貫性のため prepare() を利用しても構いません。
    /*$sqlSerial = "SELECT currval('clientadata_serial_number_clienta_seq') AS currval;";
    $stmt = $pdo->query($sqlSerial);
    if (!$stmt) {
        // 取得に失敗した場合のエラーハンドリング
        $info = $pdo->errorInfo();
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => $info[2]]);
        exit;
    }
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $rows = $stmt->fetch();
    $serino = $rows['currval'];*/

    // --------------------------------------
    // 3) serverdata に INSERT
    // --------------------------------------
    $data2 = [
        'source_id_server' => $serino,
        'status_server'    => "クライアント",
        'message_st_server'=> $alldata[18]
    ];

    $columns2 = [];
    $placeholders2 = [];
    $params2 = [];

    $j = 1;
    foreach ($data2 as $key => $val) {
        $columns2[] = "\"$key\"";
        $ph2 = ":param_s{$j}";
        $placeholders2[] = $ph2;
        $params2[$ph2] = $val;
        $j++;
    }

    $sqlInsertServer = "
        INSERT INTO serverdata (" . implode(',', $columns2) . ")
        VALUES (" . implode(',', $placeholders2) . ")
    ";
    $stmt = $pdo->prepare($sqlInsertServer);

    foreach ($params2 as $ph => $value) {
        $stmt->bindValue($ph, $value, PDO::PARAM_STR);
    }

    $stmt->execute();

} catch (PDOException $e) {
    // データベースエラー時はステータスコードを 500 にするなど
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    exit;
} finally {
    // 接続解放
    $pdo = null;
}

// --------------------------------------
// 4) レスポンス（JSON）を返す
// --------------------------------------
$newdata = $alldata[0] . "," . $alldata[1] . "," . $alldata[2] . "," . $alldata[3] . "," . $alldata[4] . "," .
           $alldata[6] . "," . $alldata[7] . "," . $alldata[8] . "," . $alldata[9] . "," . $alldata[10] . "," .
           $alldata[11] . "," . $alldata[12] . "," . $alldata[13] . "," . $alldata[14] . "," . $alldata[15] . "," .
           $alldata[16] . "," . $alldata[17] . "," . $alldata[18];

$returnData = ["ans" => $newdata];
echo json_encode($returnData);
exit;
