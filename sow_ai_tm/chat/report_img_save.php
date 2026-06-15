<?php
require_once("../../function/database.php");
/** @var PDO $pdo */
try {
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    if (!$data) {
        error_log('report_img_save: Invalid JSON - ' . substr($requestBody, 0, 200));
        throw new Exception('Invalid JSON or empty POST data');
    }

    $imageData = $data['image'];
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '', $data['filename']); // sanitize
    $title = $data['title'];
    $name = $data['name'];
    $pass = $data['pass'];

    // 一時ファイルにPNGとして保存
    $tempFile = tempnam(sys_get_temp_dir(), 'img_') . '.png';
    file_put_contents($tempFile, base64_decode($imageData));

    // 別サーバにファイルを送信（upload.phpに合わせる）
    $curl = curl_init();
    $postFields = [
        'img' => new CURLFile($tempFile, 'image/png', $filename . '.png'),
        'fname' => $filename
    ];

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://www.hisatomi-kk.com/app/upload_report.php', // ←変更必要
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
    ]);

    $response = curl_exec($curl);
    $curlErr  = curl_error($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // 一時ファイル削除
    unlink($tempFile);

    if ($httpCode !== 200 || !$response) {
        throw new Exception("画像送信失敗: $curlErr");
    }

    $responseData = json_decode($response, true);

    if (!empty($responseData['error'])) {
        throw new Exception("アップロードエラー: " . $responseData['error']);
    }

    $imgUrl = $responseData['data']['img_url'] ?? '';

    // DB登録処理
    $filenamearray = explode("_", $data['filename']);
    $db_data = [
        'school_id_report'     => $filenamearray[6] ?? '',
        'gakunen_report'       => $filenamearray[7] ?? '',
        'kumi_report'          => $filenamearray[8] ?? '',
        'seitono_report'       => $filenamearray[9] ?? '',
        'name_report'          => $name,
        'title_report'         => $title,
        'pass_report'          => $pass,
        'pass_sha1_report'     => hash('sha256', $pass),
        'message_report'       => '',
        'img_file_name_report' => basename($imgUrl),// 保存した画像ファイル名
        'created_time_report'  => date("Y-m-d G:i:s")
    ];

    // プリペアドステートメントでSQLインジェクションを防ぐ
    $columns      = [];
    $placeholders = [];
    $bindParams   = [];
    $i = 1;
    foreach ($db_data as $key => $val) {
        $columns[]      = "\"$key\"";
        $ph             = ":p{$i}";
        $placeholders[] = $ph;
        $bindParams[$ph] = $val;
        $i++;
    }

    $sql  = "INSERT INTO report (" . implode(',', $columns) . ")"
          . " VALUES ("           . implode(',', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);
    foreach ($bindParams as $ph => $val) {
        $stmt->bindValue($ph, $val, PDO::PARAM_STR);
    }
    $stmt->execute();

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'img_url' => $imgUrl]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $pdo = null;
}