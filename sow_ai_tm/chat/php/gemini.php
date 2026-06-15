<?php
    // --- 設定 ---
    $api_key = getenv('GEMINI_API_KEY');
    if ($api_key === false) {
        http_response_code(500);
        echo "Error: GEMINI_API_KEY is not set";
        exit;
    }
    $model   = "gemini-2.5-flash";   // 使用するモデル

    // ヘッダーの設定
    $header = array(
        "Content-Type: application/json"
    );

    // リクエストデータ (Gemini専用の構造)
    $data = array(
        "contents" => array(
            array(
                "parts" => array(
                    array("text" => $_POST['request'])
                )
            )
        ),
        // 翻訳などの短文回答を高速化・安定化させる設定
        "generationConfig" => array(
            "temperature" => 0.1,      // ランダム性を抑え、翻訳の正確性を向上
            "maxOutputTokens" => 1000  // 応答の長さを制限（短文ならこれで十分です）
        )
    );

    // cURLを使用してリクエストを送信
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$api_key}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    // 応答の処理
    if ($error) {
        echo "cURL Error: " . $error;
    } else {
        $result_arr = json_decode($response, true);
        
        // Geminiのレスポンスからテキスト部分を抽出
        if (isset($result_arr['candidates'][0]['content']['parts'][0]['text'])) {
            $result = $result_arr['candidates'][0]['content']['parts'][0]['text'];
            echo htmlspecialchars(trim($result), ENT_QUOTES, 'UTF-8');
        } else {
            // エラーが発生した場合（APIキー間違いや安全フィルターなど）
            echo "API Error: " . ($result_arr['error']['message'] ?? 'Unknown error');
        }
    }
    
    exit;
?>