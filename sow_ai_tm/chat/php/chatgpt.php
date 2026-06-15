<?php
    $api_key = getenv('OPENAI_API_KEY');
    if ($api_key === false) {
        http_response_code(500);
        echo "Error: OPENAI_API_KEY is not set";
        exit;
    }
    $header = array(
      "Content-Type: application/json",
      "api-key: " . $api_key
    );
    $messages = array(
        array("role" => "user", "content" => $_POST['request'])
    );
    $data = array(
        "model" => "gpt-3.5-turbo",
        "messages" => $messages,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    //echo 'console.log('.$response.')';
    $result = json_decode($response, true)["choices"][0]["message"]["content"];
    echo htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
    exit;
  ?>