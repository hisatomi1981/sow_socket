<?php
// ★ ダウンロード系は「出力しない」が鉄則（warningも出力扱いになる）
// ini_set('display_errors', '0'); // 本番は通常OFF
// error_reporting(E_ALL);

if (!isset($_POST['mode'])) {
    require_once("../login/function/errorhtml.php");
    get_direct_access_error_html();
    exit;
}

$school_id_userid = $_POST['school_id_userid'] ?? '';
require_once("../login/function/function.php");
require_once("../function/database.php");
/** @var PDO $pdo */

// ZipArchiveが無い場合はここで終了（Fatal回避）
if (!class_exists('ZipArchive')) {
    http_response_code(500);
    echo "サーバに ZipArchive (php-zip) が有効化されていません。管理者にて php-zip を有効化してください。";
    exit;
}

// 日付：複数フォーマットを許容
function parseDateFlexible(?string $s): ?DateTime {
    if ($s === null || $s === '') return null;
    $s = str_replace('T', ' ', $s);
    $formats = ['Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d'];
    foreach ($formats as $fmt) {
        $dt = DateTime::createFromFormat($fmt, $s);
        if ($dt instanceof DateTime) return $dt;
    }
    return null;
}

try {
    $search_start_raw = $_POST['start_date_search'] ?? null;
    $search_end_raw   = $_POST['end_date_search'] ?? null;
    $sort_order       = $_POST['sort_order'] ?? "0";

    $dt1 = parseDateFlexible($search_start_raw);
    $dt2 = parseDateFlexible($search_end_raw);

    if (!$dt1 || !$dt2) {
        http_response_code(400);
        echo "エラー: 日付のフォーマットが正しくありません";
        exit;
    }

    // SQLの期間条件
    $diff = $dt1->diff($dt2);
    $threeMonthsAgo = new DateTime();
    $threeMonthsAgo->modify('-1 months');

    if ($dt1 < $threeMonthsAgo || $diff->format('%R') == '-') {
        // 範囲不正：ダウンロードを中止してHTML返す（echoはここだけ）
        http_response_code(400);
        if ($dt1 < $threeMonthsAgo) echo "1ヶ月以上前の日付は選択できません";
        if ($diff->format('%R') == '-') echo "終了日を開始日より前に指定することはできません";
        exit;
    }

    // ★ bindする値は文字列化（秒ありに統一）
    $search_start = $dt1->format('Y-m-d H:i:s');
    $search_end   = $dt2->format('Y-m-d H:i:s');

    $datebetween = " AND created_time_report BETWEEN :search_start AND :search_end";

    $sqlst = "SELECT * FROM report WHERE school_id_report = :school_id" . $datebetween;

    $allowed_sort_columns = [
        "0" => "serial_number_report DESC",
        "1" => "serial_number_report ASC",
        "2" => "gakunen_report ASC",
        "3" => "gakunen_report DESC",
        "4" => "kumi_report ASC",
        "5" => "kumi_report DESC",
        "6" => "seitono_report ASC",
        "7" => "seitono_report DESC"
    ];
    if (!array_key_exists($sort_order, $allowed_sort_columns)) $sort_order = "0";
    $sqlst .= " ORDER BY " . $allowed_sort_columns[$sort_order];

    $stmt = $pdo->prepare($sqlst);
    $stmt->bindValue(':school_id', $school_id_userid, PDO::PARAM_STR);
    $stmt->bindValue(':search_start', $search_start, PDO::PARAM_STR);
    $stmt->bindValue(':search_end', $search_end, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    http_response_code(500);
    // ユーザーにDB詳細を出さない。ログへ。
    error_log("report_download PDO error: " . $e->getMessage());
    echo "サーバ処理でエラーが発生しました。（DB）";
    exit;
} finally {
    $pdo = null;
}

// 件数制限：ここで「echoでJS」は出さず、HTMLメッセージで終了
if (count($rows) > 50) {
    http_response_code(400);
    echo "ファイル数が50を超えています。日時範囲を絞って再実行してください。";
    exit;
}

// ZIPの保存先（絶対パス）
$zipDir = __DIR__ . '/reportImg';
if (!is_dir($zipDir)) {
    // 必要なら作成
    if (!mkdir($zipDir, 0775, true)) {
        http_response_code(500);
        echo "ZIP保存用フォルダ(reportImg)の作成に失敗しました。";
        exit;
    }
}
if (!is_writable($zipDir)) {
    http_response_code(500);
    echo "ZIP保存用フォルダ(reportImg)に書き込み権限がありません。";
    exit;
}

$zipPath = $zipDir . '/report_data_' . $school_id_userid . '.zip';

$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    http_response_code(500);
    echo "ZIPファイルの作成に失敗しました。";
    exit;
}

$errors = [];
foreach ($rows as $val) {
    $remoteUrl = 'https://www.hisatomi-kk.com/app/upload_report/' . $val['img_file_name_report'];

    $pngFileName = $val['gakunen_report'] . "年" . $val['kumi_report'] . "組" . $val['seitono_report'] . "番" . $val['img_file_name_report'];
    $zipEntryName = mb_convert_encoding($pngFileName, 'CP932', 'UTF-8');

    // タイムアウト付き（無限待ち回避）
    $context = stream_context_create(['http' => ['timeout' => 10]]);
    $bin = @file_get_contents($remoteUrl, false, $context);

    if ($bin === false) {
        $errors[] = $val['img_file_name_report'];
        continue; // ここでechoしない
    }
    $zip->addFromString($zipEntryName, $bin);
}
$zip->close();

// もし取得失敗があったら中止する方針ならここで止める（今は「欠損ありでZIP作成」方針）
if (!empty($errors)) {
    // 欠損を許容しないなら：unlinkして終了
    // @unlink($zipPath);
    // http_response_code(500);
    // echo "一部画像の取得に失敗しました。対象: " . implode(',', $errors);
    // exit;

    // 許容する場合：ログのみ
    error_log("report_download missing files: " . implode(',', $errors));
}

// ここからダウンロード（この前に絶対にechoしない）
while (ob_get_level() > 0) { ob_end_clean(); }

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($zipPath) . '"');
header('Content-Length: ' . filesize($zipPath));
header('X-Content-Type-Options: nosniff');

readfile($zipPath);

// ダウンロード後削除
@unlink($zipPath);
exit;
