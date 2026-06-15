<?php
// JSONを返す場合はヘッダを設定しておくと安全
header('Content-Type: application/json; charset=utf-8');

require_once("../../../function/database.php");

try {
    // 必要なパラメータがあるかを簡易チェック（必要に応じて強化）
    if (!isset($_POST['clienta'], $_POST['school_id'], $_POST['chat_group'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => true, 'message' => 'Missing parameters']);
        exit;
    }

    // プリペアドステートメントで安全にパラメータを注入
    $sqlst = "SELECT * FROM clientadata
     LEFT JOIN serverdata
            ON clientadata.serial_number_clienta = CAST(serverdata.source_id_server AS INTEGER)
         WHERE serverdata.source_id_server IS NOT NULL
           AND (serverdata.status_server = 'NG' OR serverdata.status_server = 'RET')
           AND clientadata.server_clienta != ''
           AND clientadata.client_a_clienta    = :clienta
           AND clientadata.school_id_clienta   = :school_id
           AND clientadata.chat_group_clienta  = :chat_group
           AND clientadata.time_clienta >= (NOW() AT TIME ZONE 'Asia/Tokyo') - INTERVAL '1 minute'
         ORDER BY clientadata.serial_number_clienta ASC
    ";

    // 準備
    $stmt = $pdo->prepare($sqlst);

    // バインド
    $stmt->bindValue(':clienta',    $_POST['clienta'],    PDO::PARAM_STR);
    $stmt->bindValue(':school_id',  $_POST['school_id'],  PDO::PARAM_STR);
    $stmt->bindValue(':chat_group', $_POST['chat_group'], PDO::PARAM_STR);

    // 実行
    $stmt->execute();

    // FETCH_ASSOC：カラム名をキーとする連想配列
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // DBエラー等
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit;
} finally {
    // DB接続を解放
    $pdo = null;
}

// JSON 形式で結果を返す
echo json_encode($rows);
exit;
