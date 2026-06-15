<?php
// JSONで返すヘッダ
header('Content-Type: application/json; charset=utf-8');

require_once("../../../function/database.php");

try {
    // updata, recievedata がPOSTに存在するか確認
    if (!isset($_POST['updata']) || !isset($_POST['recievedata'])) {
        // 必要なパラメータがない場合はエラー
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Missing parameters (updata or recievedata)']);
        exit;
    }

    // -------------------------------------------------
    // 1) パラメータ取得・分解
    // -------------------------------------------------
    $s_st = explode(',', $_POST['updata']);        // id,時間,名前,clientA,種類,重要,繰り返し回数,パスワード,音,保存ファイル名,メッセージ
    // 例えば $s_st[0] = id（source_id_clientbに使う想定）

    $recieve_st = explode(',', $_POST['recievedata']); // 着信音,フォントサイズ,色,背景色,画面背景色
    // 例えば $recieve_st[0] = 着信音, $recieve_st[1] = フォントサイズ, ...など

    // 更新したいデータを連想配列にまとめる
    $data = [
        'sound_clientb'       => $recieve_st[0],
        'font_siza_clientb'   => $recieve_st[1],
        'font_color_clientb'  => $recieve_st[2],
        'back_color_clientb'  => $recieve_st[3],
        'disp_color_clientb'  => $recieve_st[4],
    ];

    // -------------------------------------------------
    // 2) UPDATE文を動的に組み立てる (SET 句)
    // -------------------------------------------------
    $sets = [];
    $bindParams = [];
    $i = 1;
    foreach ($data as $key => $val) {
        // PostgreSQL でカラム名を大文字や特殊文字を含めて定義しているならダブルクォートが必要
        $ph = ":param$i";
        $sets[] = "\"$key\" = $ph";
        $bindParams[$ph] = $val;
        $i++;
    }

    // where句で source_id_clientb を指定 (ユーザー入力はプレースホルダ使用)
    $sql = "
        UPDATE clientbdata
           SET " . implode(', ', $sets) . "
         WHERE source_id_clientb = :source_id
    ";

    // プリペアドステートメントを準備
    $stmt = $pdo->prepare($sql);

    // SET句のバインド
    foreach ($bindParams as $ph => $val) {
        $stmt->bindValue($ph, $val, PDO::PARAM_STR);
    }

    // WHERE 句のバインド (source_id_clientb)
    // $s_st[0] が id（ソースID）として使われている想定
    $stmt->bindValue(':source_id', $s_st[0], PDO::PARAM_STR);

    // 実行
    $stmt->execute();

    // 正常終了データ
    $returnData = ["ans" => "OK"];

} catch (PDOException $e) {
    // DBエラーなどの場合に 500 ステータス + JSONエラー情報を返すのが望ましい
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    exit;
} finally {
    $pdo = null;  // DB 接続をクローズ
}

// 正常終了時のレスポンス
echo json_encode($returnData);
exit;
