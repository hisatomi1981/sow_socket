<?php
    $api_key = getenv('AZURE_API_KEY');
    if ($api_key === false) {
        http_response_code(500);
        echo "Error: AZURE_API_KEY is not set";
        exit;
    }
    // Azure用のヘッダー
    $header = array(
        "Content-Type: application/json",
        "api-key: " . $api_key
    );

    // ユーザーからのリクエストメッセージ
    $messages = array(
        array("role" => "user", "content" => $_POST['request'])
    );

    // リクエストのデータ
    $data = array(
        "messages" => $messages,
    );

    // cURLを使用してリクエストを送信
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://hisatomiopenai.openai.azure.com/openai/deployments/gpt-35-turbo/chat/completions?api-version=2024-08-01-preview");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // 応答を処理して表示
    $result = json_decode($response, true)["choices"][0]["message"]["content"];
    echo htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
    exit;
?>
