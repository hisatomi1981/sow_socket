<?php
// JSONを返すヘッダを設定しておくとクライアントが扱いやすい
header('Content-Type: application/json; charset=utf-8');

require_once("../../../function/database.php");
/** @var PDO $pdo */

try {
    // 必要なパラメータチェック（必要なら厳密に）
    if (!isset($_POST['clienta'], $_POST['school_id'], $_POST['chat_group'])) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Missing parameters']);
        exit;
    }

    // 変数に格納
    $clienta   = $_POST['clienta'];
    $school_id = $_POST['school_id'];
    $chat_group = $_POST['chat_group'];

    // ------------------------------------------------------------------------
    // 1) 未読データを SELECT
    // ------------------------------------------------------------------------
    // 「client_b_clienta = :clienta かつ school_id_clienta = :school_id …」
    // 「time_clienta >= NOW() - INTERVAL '1 minute'」などは固定でOK
    $sqlSelect = "SELECT * FROM clientadata
         WHERE client_b_clienta = :clienta
           AND school_id_clienta = :school_id
           AND chat_group_clienta = :chat_group
           AND data_kind_clienta = 'サーバ'
           AND read_clienta = '未読'
           AND time_clienta >= (NOW() AT TIME ZONE 'Asia/Tokyo') - INTERVAL '1 minute'
      ORDER BY serial_number_clienta ASC
    ";

    // プリペアドステートメントを準備
    $stmt = $pdo->prepare($sqlSelect);

    // バインド
    $stmt->bindValue(':clienta',    $clienta,    PDO::PARAM_STR);
    $stmt->bindValue(':school_id',  $school_id,  PDO::PARAM_STR);
    $stmt->bindValue(':chat_group', $chat_group, PDO::PARAM_STR);

    // 実行
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        // 該当データが無い場合も空配列を返して終了
        echo json_encode([]);
        exit;
    }

    // ------------------------------------------------------------------------
    // 2) 該当レコードを UPDATE ＆ 結果配列に格納
    // ------------------------------------------------------------------------
    $return_array = [];

    // UPDATE 用ステートメントを先に準備
    //   read_clienta を '既読'
    //   password_clienta を ''
    //   WHERE serial_number_clienta = :serial_id
    $sqlUpdate = "
        UPDATE clientadata
           SET read_clienta = '既読',
               password_clienta = ''
         WHERE serial_number_clienta = :serial_id
    ";
    $stmtUpdate = $pdo->prepare($sqlUpdate);

    foreach ($rows as $row_once) {
        // 1レコード分の更新
        $stmtUpdate->bindValue(':serial_id', $row_once['serial_number_clienta'], PDO::PARAM_INT);
        $stmtUpdate->execute();

        // 更新後、返却用のデータを作成
        // newdata = serial_number_clienta, time_clienta, client_a_name_clienta, ...
        $newdata = $row_once['serial_number_clienta'] . "," . 
                   $row_once['time_clienta'] . "," . 
                   $row_once['client_a_name_clienta'] . "," . 
                   $row_once['client_a_clienta'] . "," .
                   $row_once['server_clienta'] . "," . 
                   $row_once['data_kind_clienta'] . "," . 
                   $row_once['juyo_clienta'] . "," .
                   $row_once['repeat_cnt_clienta'] . "," .
                   $row_once['password_clienta'] . "," .
                   $row_once['sound_clienta'] . "," .
                   $row_once['save_file_clienta'] . "," .
                   $row_once['message_st_clienta'];

        $return_array[] = $newdata;
    }

} catch (PDOException $e) {
    // DBエラーなど
    error_log('DB Connection failed: ' . $e->getMessage()); // サーバーログにだけ記録
    http_response_code(500);
    die(json_encode(['error' => 'サーバーエラーが発生しました。管理者にお問い合わせください。']));
} finally {
    $pdo = null;
}

// 3) 最終的に JSON を返す
echo json_encode($return_array);
exit;
