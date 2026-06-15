<?php
// JSON形式で返すなら、ヘッダーを設定しておくとよい
header('Content-Type: application/json; charset=utf-8');

// DB接続
require_once("../../../function/database.php");
/** @var PDO $pdo */

try {
    // 1) パラメータの取得と分解
    if (!isset($_POST['updata']) || !isset($_POST['clientb'])) {
        // 必要なパラメータが無ければエラー
        echo json_encode(['error' => true, 'message' => 'Missing parameters']);
        exit;
    }
    
    $s_st = explode(',', $_POST['updata']); 
    // $s_st[0] => id
    // $s_st[11] => メッセージ
    // 他の要素も配列に格納されています（0～...）

    // 2) すでに受信しているかどうかチェック
    //    "SELECT * FROM clientbdata WHERE source_id_clientb = '...'"
    //    ↓ プリペアドステートメントに書き換え
    $sqlCheck = "
        SELECT *
          FROM clientbdata
         WHERE source_id_clientb = :source_id
    ";
    $stmt = $pdo->prepare($sqlCheck);
    $stmt->bindValue(':source_id', $s_st[0], PDO::PARAM_STR);
    $stmt->execute();

    // FETCH_ASSOC：カラム名をキーとする連想配列
    $r_data = $stmt->fetch(PDO::FETCH_ASSOC); // 1件取得
    // rowCount() はSELECTではDBによって動作が異なるため注意。ここではfetch()で結果を見ます。
    // $stmt->rowCount() でもよいが非推奨（PostgreSQLなら動くことが多い）

    // 受信データが見つからなければ INSERT、見つかれば UPDATE 相当の処理
    if (!$r_data) {
        // ---------------------------------------------
        // 3) INSERT 処理
        // ---------------------------------------------
        // 登録データを連想配列でまとめる
        $data = [
            'source_id_clientb'      => $s_st[0],
            'recieve_no_clientb'     => $_POST['clientb'],
            'status_clientb'         => "ダウン",
            'recieve_message_clientb'=> $s_st[11],
        ];

        // プリペアドステートメント用にカラム名・プレースホルダーを組み立て
        $columns = [];
        $placeholders = [];
        $bindParams = [];

        $i = 1;
        foreach ($data as $key => $val) {
            // PostgreSQLでカラム名をダブルクォートしている場合
            $columns[] = "\"$key\"";
            // :p1, :p2 などのプレースホルダーを作成
            $ph = ":p$i";
            $placeholders[] = $ph;
            $bindParams[$ph] = $val;
            $i++;
        }

        $sqlInsert = "
            INSERT INTO clientbdata (".implode(',', $columns).")
            VALUES (".implode(',', $placeholders).")
        ";
        $stmt = $pdo->prepare($sqlInsert);

        // バインド
        foreach ($bindParams as $ph => $val) {
            // 実際のカラムが数値型なら PDO::PARAM_INT に切り替える
            $stmt->bindValue($ph, $val, PDO::PARAM_STR);
        }

        $stmt->execute();

    } else {
        // ---------------------------------------------
        // 4) UPDATE 処理
        // ---------------------------------------------
        // 同じ番号が続かないように
        if ($r_data['recieve_no_clientb'] !== $_POST['clientb']) {
            $newr_no = $r_data['recieve_no_clientb'] . "/" . $_POST['clientb'];
        } else {
            $newr_no = $r_data['recieve_no_clientb'];
        }

        // UPDATE したいカラムと値を用意
        $data = [
            'recieve_no_clientb' => $newr_no,
            // 必要に応じてほかのカラムも追加
            // 例: 'status_clientb' => '更新内容' etc...
        ];

        // 動的に SET 句を生成
        $sets = [];
        $bindParams = [];
        $j = 1;

        foreach ($data as $key => $val) {
            $ph = ":p$j"; 
            $sets[] = "\"$key\" = $ph";
            $bindParams[$ph] = $val;
            $j++;
        }

        // WHERE 条件にもプレースホルダーを利用
        $sqlUpdate = "
            UPDATE clientbdata
               SET ".implode(',', $sets)."
             WHERE serial_number_clientb = :serial_id
        ";
        $stmt = $pdo->prepare($sqlUpdate);

        // もともと取得していた行のシリアルを使う
        $stmt->bindValue(':serial_id', $r_data['serial_number_clientb'], PDO::PARAM_INT);

        // SET句のバインド
        foreach ($bindParams as $ph => $val) {
            $stmt->bindValue($ph, $val, PDO::PARAM_STR);
        }

        $stmt->execute();
    }

    // 5) 戻り値を返す
    //    もとのコードでは $_POST['updata'] をそのまま返している
    $returnData = ["ans" => $_POST['updata']];

} catch (PDOException $e) {
    // PDO でのエラー時
    error_log('DB Connection failed: ' . $e->getMessage()); // サーバーログにだけ記録
    http_response_code(500);
    die(json_encode(['error' => 'サーバーエラーが発生しました。管理者にお問い合わせください。']));
} finally {
    $pdo = null; // DB切断
}

// 正常終了時の JSON レスポンス
echo json_encode($returnData);
exit;
