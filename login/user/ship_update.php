<?php
/**
 * ship_update.php
 * 出荷フラグ・出荷日をAJAXで更新するエンドポイント
 */
header('Content-Type: application/json; charset=UTF-8');

// 直接アクセス防止（serial_number_kanri が未送信ならエラー）
if (!isset($_POST['serial_number_kanri'])) {
    echo json_encode(['result' => 'error', 'error' => 'unauthorized']);
    exit;
}

require_once("../../function/database.php");
/** @var PDO $pdo */

$serial_number_userid  = (int)$_POST['serial_number_userid'];
$shipped               = ($_POST['shipped'] === '1') ? true : false;
$shipped_date          = $_POST['shipped_date'];   // "YYYY-MM-DD" or ""
$serial_number_kanri   = (int)$_POST['serial_number_kanri'];

// 空文字の場合は NULL にする
$shipped_date_val = ($shipped_date !== '') ? $shipped_date : null;

try {
    // serial_number_kanri で所有確認してから更新（セキュリティ）
    $sql = "UPDATE userid
            SET shipped_userid      = :shipped,
                shipped_date_userid = :shipped_date
            WHERE serial_number_userid = :sn
              AND created_userid       = :kanri";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':shipped',      $shipped,        PDO::PARAM_BOOL);
    $stmt->bindValue(':shipped_date', $shipped_date_val); // null or string
    $stmt->bindValue(':sn',           $serial_number_userid, PDO::PARAM_INT);
    $stmt->bindValue(':kanri',        $serial_number_kanri,  PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['result' => 'ok']);

} catch (PDOException $e) {
    echo json_encode(['result' => 'error', 'error' => $e->getMessage()]);
} finally {
    $pdo = null;
}
