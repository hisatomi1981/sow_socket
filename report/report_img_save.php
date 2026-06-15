<?php
require_once("../function/database.php");
/** @var PDO $pdo */
try {
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    if (!$data) {
        file_put_contents("debug_log.txt", $requestBody); // デバッグログ出力
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
        CURLOPT_URL => 'https://www.hisatomi-kk.com/app/upload_report.php',
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

    // ✅ プリペアドステートメントで安全にINSERT
    $sql = "INSERT INTO report (
                school_id_report,
                gakunen_report,
                kumi_report,
                seitono_report,
                name_report,
                title_report,
                pass_report,
                pass_sha1_report,
                message_report,
                active_report,
                img_file_name_report,
                created_time_report
            ) VALUES (
                :school_id,
                :gakunen,
                :kumi,
                :seitono,
                :name,
                :title,
                :pass,
                :pass_sha1,
                :message,
                :active,
                :img_file_name,
                :created_time
            )";

    $stmt = $pdo->prepare($sql);
    if (!$stmt) {
        $info = $pdo->errorInfo();
        throw new Exception("prepare失敗: " . $info[2]);
    }

    $executed = $stmt->execute([
        ':school_id'    => $filenamearray[6] ?? '',
        ':gakunen'      => $filenamearray[7] ?? '',
        ':kumi'         => $filenamearray[8] ?? '',
        ':seitono'      => $filenamearray[9] ?? '',
        ':name'         => $name,
        ':title'        => $title,
        ':pass'         => $pass,
        ':pass_sha1'    => '',
        ':message'      => '',
        ':active'       => true,
        ':img_file_name'=> basename($imgUrl),
        ':created_time' => date("Y-m-d G:i:s"),
    ]);

    if (!$executed) {
        $info = $stmt->errorInfo();
        throw new Exception("INSERT失敗: " . $info[2]);
    }

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'img_url' => $imgUrl]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $pdo = null;
}