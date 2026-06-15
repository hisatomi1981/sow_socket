<?php
if (!isset($_POST['filename']) || !isset($_POST['content'])) {
    http_response_code(400);
    exit('Invalid request');
}

$filename = basename($_POST['filename']);
$content  = $_POST['content'];

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: private, no-transform, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// 重要：出力バッファをクリア
while (ob_get_level()) { ob_end_clean(); }

echo $content;
exit; // ← これが非常に重要
