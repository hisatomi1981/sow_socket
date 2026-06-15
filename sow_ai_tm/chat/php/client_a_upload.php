<?php
// JSONを返す場合は、Content-Typeヘッダを設定しておくとクライアント側での扱いがスムーズ
header('Content-Type: application/json; charset=utf-8');

require_once("../../../function/database.php");

try {
    // 必要なパラメータが存在するかチェック（必要に応じて追加）
    if (!isset($_POST['alldata'], $_POST['status'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => true, 'message' => 'Missing parameters: alldata or status']);
        exit;
    }

    // カンマ区切りの文字列
    $alldata = $_POST['alldata'];
    // 配列に分解
    $chatdata = explode(",", $alldata);

    // 取り出す要素の数が合っているかどうかのチェック（必要に応じて）
    // 例: 22個必要なら
    if (count($chatdata) < 22) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Insufficient data in alldata']);
        exit;
    }

    // clientadataに登録するデータを連想配列化
    // （キー = テーブルカラム名, 値 = 実際にINSERTする値）
    $data = [
        'status_clienta'         => $_POST['status'],  // POSTパラメータ "status"
        'school_id_clienta'      => $chatdata[0],
        'chat_group_clienta'     => $chatdata[1],
        'time_clienta'           => $chatdata[2],
        'client_a_name_clienta'  => $chatdata[3],
        'client_a_clienta'       => $chatdata[4],
        'server_clienta'         => $chatdata[5],
        'client_b_clienta'       => $chatdata[6],
        'group_clienta'          => $chatdata[7],
        'data_kind_clienta'      => $chatdata[8],
        'juyo_clienta'           => $chatdata[9],
        'read_clienta'           => $chatdata[10],
        'repeat_cnt_clienta'     => $chatdata[11],
        'password_clienta'       => $chatdata[12],
        'sound_clienta'          => $chatdata[13],
        'font_siza_clienta'      => $chatdata[14],
        'font_color_clienta'     => $chatdata[15],
        'back_color_clienta'     => $chatdata[16],
        'disp_color_clienta'     => $chatdata[17],
        'save_file_clienta'      => $chatdata[18],
        'message_st_clienta'     => $chatdata[19],
        'ai_before_st_clienta'   => $chatdata[20],
        'ai_prompt_clienta'      => $chatdata[21],
    ];

    // --------------------------------------------------
    // プリペアドステートメント用にカラム名 & プレースホルダを生成
    // --------------------------------------------------
    $columns = [];
    $placeholders = [];
    $bindParams = [];  // bindValue 用の配列
    $i = 1;

    foreach ($data as $key => $val) {
        // PostgreSQL でカラム名を大文字やアンダースコア含めて定義している場合はダブルクォートが必要
        $columns[] = "\"$key\"";

        // :p1, :p2,...のようなプレースホルダを作成
        $ph = ":p{$i}";
        $placeholders[] = $ph;

        // 後で bindValue() するために配列に詰める
        $bindParams[$ph] = $val;
        $i++;
    }

    // INSERT文を組み立て
    $sql = "
        INSERT INTO clientadata (".implode(',', $columns).")
        VALUES (".implode(',', $placeholders).")
    ";

    // --------------------------------------------------
    // プリペアドステートメントを準備して実行
    // --------------------------------------------------
    $stmt = $pdo->prepare($sql);

    // bindValue() でパラメータをセット
    // （実際に数値カラムの場合は PDO::PARAM_INT にする等、要件に応じて変更）
    foreach ($bindParams as $ph => $val) {
        $stmt->bindValue($ph, $val, PDO::PARAM_STR);
    }

    $stmt->execute();

    // 正常終了
    $returnData = [
        "ans" => $alldata // もとのコードのように alldata を返す
    ];

} catch (PDOException $e) {
    // DBエラー時 (SQL失敗・接続失敗など)
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    exit;
} finally {
    // 接続をクローズ
    $pdo = null;
}

// 成功時のレスポンスをJSONで返す
echo json_encode($returnData);
exit;
