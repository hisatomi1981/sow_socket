<?php
// JSONを返すので、ヘッダをJSONに統一する
header('Content-Type: application/json; charset=utf-8');

require_once("../../../function/database.php");

try {
    // 必要なPOSTパラメータをチェック
    if (!isset($_POST['clientb'], $_POST['school_id'], $_POST['chat_group'])) {
        // 必要があればここでエラーとして終了
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Missing parameters.']);
        exit;
    }

    // POST値を変数に格納 (安全のために trim なども適宜)
    $clientb    = $_POST['clientb'];
    $school_id  = $_POST['school_id'];
    $chat_group = $_POST['chat_group'];

    // ----------------------------------------------------------------------
    // 1) clientbが自分で "サーバーがある" 場合のデータ取得
    // ----------------------------------------------------------------------
    // 元のコードで使っていた正規表現:
    //    clientbdata.recieve_no_clientb !~ '(^|\D)clientb(\D|$)'
    //    clientadata.client_b_clienta ~ '(^|\D)clientb(\D|$)'
    //
    // ユーザー入力を正規表現として使う際には、preg_quote()でエスケープしておくと安全
    $clientbRegex = '(^|\D)' . preg_quote($clientb, '/') . '(\D|$)';

    $sqlst = "SELECT * FROM clientadata
     LEFT JOIN serverdata
            ON clientadata.serial_number_clienta = CAST(serverdata.source_id_server AS INTEGER)
     LEFT JOIN clientbdata
            ON clientadata.serial_number_clienta = CAST(clientbdata.source_id_clientb AS INTEGER)
         WHERE (
                 clientbdata.source_id_clientb IS NULL
                 OR clientbdata.recieve_no_clientb !~ :clientbRegex1
               )
           AND serverdata.source_id_server IS NOT NULL
           AND serverdata.status_server = 'OK'
           AND clientadata.server_clienta != ''
           AND clientadata.status_clienta = 'アップ'
           AND clientadata.client_b_clienta ~ :clientbRegex2
           AND clientadata.school_id_clienta = :school_id
           AND clientadata.chat_group_clienta = :chat_group
           AND clientadata.time_clienta >= (NOW() AT TIME ZONE 'Asia/Tokyo') - INTERVAL '1 minute'
         ORDER BY clientadata.serial_number_clienta ASC
    ";

    $stmt = $pdo->prepare($sqlst);

    // バインド (同じ正規表現を !~ と ~ で使うため、2つのプレースホルダに同じ値をバインド)
    $stmt->bindValue(':clientbRegex1', $clientbRegex,  PDO::PARAM_STR);
    $stmt->bindValue(':clientbRegex2', $clientbRegex,  PDO::PARAM_STR);
    $stmt->bindValue(':school_id',     $school_id,     PDO::PARAM_STR);
    $stmt->bindValue(':chat_group',    $chat_group,    PDO::PARAM_STR);

    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ----------------------------------------------------------------------
    // 2) clientbが自分で "サーバーがない" 場合のデータ取得
    // ----------------------------------------------------------------------
    $sqlst2 = "SELECT * FROM clientadata
     LEFT JOIN clientbdata
            ON clientadata.serial_number_clienta = CAST(clientbdata.source_id_clientb AS INTEGER)
         WHERE (
                 clientbdata.source_id_clientb IS NULL
                 OR clientbdata.recieve_no_clientb !~ :clientbRegex1
               )
           AND clientadata.server_clienta = ''
           AND clientadata.status_clienta = 'アップ'
           AND clientadata.client_b_clienta ~ :clientbRegex2
           AND clientadata.school_id_clienta = :school_id
           AND clientadata.chat_group_clienta = :chat_group
           AND clientadata.time_clienta >= (NOW() AT TIME ZONE 'Asia/Tokyo') - INTERVAL '1 minute'
         ORDER BY clientadata.serial_number_clienta ASC
    ";

    $stmt2 = $pdo->prepare($sqlst2);

    $stmt2->bindValue(':clientbRegex1', $clientbRegex, PDO::PARAM_STR);
    $stmt2->bindValue(':clientbRegex2', $clientbRegex, PDO::PARAM_STR);
    $stmt2->bindValue(':school_id',     $school_id,    PDO::PARAM_STR);
    $stmt2->bindValue(':chat_group',    $chat_group,   PDO::PARAM_STR);

    $stmt2->execute();
    $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // 3) array_merge で結合
    $rowsall = array_merge($rows, $rows2);

} catch (PDOException $e) {
    // DBエラー時など
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit;
} finally {
    $pdo = null;
}

// JSONとして返す
echo json_encode($rowsall);
exit;
