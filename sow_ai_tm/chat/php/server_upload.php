<?php
require_once("../../../function/database.php");

// JSONとして返すことを明示
header('Content-Type: application/json; charset=utf-8');

// もしPOSTに'updata'が存在しない場合はエラーとして終了（必要に応じて実装）
if (!isset($_POST['updata'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => true, 'message' => 'Missing parameter: updata']);
    exit;
}

try {
    // updata から配列を取得
    // 例: id,OK(NG),時間,名前,client_A,client_B,種類,重要,繰り返し回数,パスワード,音,保存ファイル名,元メッセージ,送信メッセージ
    $s_st = explode(',', $_POST['updata']);

    // 0番目が source_id_server に相当
    $sourceId = $s_st[0];

    // 1番目が status_server に相当
    $status   = $s_st[1];

    // 13番目が message_st_server に相当
    // 配列範囲外にならないかチェックが必要かもしれません。必要ならif文等で保護
    $message  = $s_st[13];

    // まず、すでに同じ番号で登録があれば何もしない
    // SQLインジェクション対策としてプリペアドステートメントを使用
    $sqlCheck = "SELECT * FROM serverdata WHERE source_id_server = :source_id";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->bindValue(':source_id', $sourceId, PDO::PARAM_STR);
    $stmtCheck->execute();

    // データが1件でも存在するなら何もしないで終了
    if ($stmtCheck->rowCount() > 0) {
        $pdo = null;
        exit;
    }

    // 挿入するデータを連想配列で用意
    $data = [
        'source_id_server' => $sourceId,
        'status_server'    => $status,
        'message_st_server'=> $message
    ];

    // serverdata テーブルの各カラムに対応するプレースホルダとバインドするための準備
    $columns = [];
    $placeholders = [];
    $params = [];  // bindValue用にパラメータをまとめる

    // 連想配列をループしてカラム名と値を構築
    // カラム名は PostgreSQL の場合ダブルクォーテーションで囲む必要があるケースがあるため注意。
    // （実際に DB でカラム名を小文字固定にしているなら必ずしも必要ではありません）
    $count = 1;
    foreach ($data as $key => $val) {
        $columns[] = "\"$key\"";
        // :param1, :param2,...のようなプレースホルダーを作成
        $ph = ":param{$count}";
        $placeholders[] = $ph;
        $params[$ph] = $val;
        $count++;
    }

    // implodeで文字列化
    $colStr = implode(', ', $columns);
    $phStr  = implode(', ', $placeholders);

    // INSERT文を作成
    $sqlInsert = "INSERT INTO serverdata ($colStr) VALUES ($phStr)";

    // プリペアドステートメントで実行
    $stmtInsert = $pdo->prepare($sqlInsert);

    // パラメータバインド
    foreach ($params as $ph => $val) {
        // カラム型に合わせて PDO::PARAM_STR, PDO::PARAM_INT などを適宜使い分け
        $stmtInsert->bindValue($ph, $val, PDO::PARAM_STR);
    }

    $stmtInsert->execute();

} catch (PDOException $e) {
    // DBエラー時は500ステータスでJSONを返す (要件次第で変える)
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
    ]);
    exit;
} finally {
    $pdo = null;
}

// 正常終了時のレスポンス
$returnData = [
    "ans" => $_POST['updata']
];
echo json_encode($returnData);
exit;
