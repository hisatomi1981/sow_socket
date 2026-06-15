<?php
// --- エラー表示設定 ---
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- 設定 ---
$api_key = getenv('GEMINI_API_KEY');
$model   = "gemini-2.5-flash";       // ★ 目的のモデル名（v1で利用）
$endpoint_version = "v1";            // ★ v1 を使用（v1beta ではない）

$result_mermaid = "";
$debug_msg = "";

// APIキー未設定
if (!$api_key) {
    $debug_msg = "GEMINI_API_KEY が環境変数に設定されていません。";
}

if (!$debug_msg && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['request'])) {
    $user_input = $_POST['request'];

    // Mermaid mindmap 指示（出力が崩れにくいように追加制約）
    $prompt = "以下の文章を mermaid の mindmap 形式コードのみで出力してください。\n"
            . "必ず 'mindmap' から開始してください。\n"
            . "``` や解説文は出力しないでください。\n\n"
            . "内容：\n" . $user_input;

    $data = array(
        "contents" => array(
            array(
                "role" => "user",
                "parts" => array(
                    array("text" => $prompt)
                )
            )
        ),
        // 必要に応じて調整
        "generationConfig" => array(
            "temperature" => 0.2,
            "maxOutputTokens" => 2048
        )
    );

    // ★ v1 エンドポイント
    // 例: https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=XXXX
    $url = "https://generativelanguage.googleapis.com/{$endpoint_version}/models/{$model}:generateContent?key=" . rawurlencode($api_key);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8"));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // ★ タイムアウト（業務利用では必須）
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    // ★ SSL証明書の検証は有効（false はテストでも非推奨）
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        $debug_msg = "cURL通信エラー: " . $curl_error;
    } else {
        $result_arr = json_decode($response, true);

        if ($result_arr === null) {
            $debug_msg = "JSONデコードに失敗しました。応答: " . htmlspecialchars($response);
        } elseif ($http_code !== 200) {
            $api_err = $result_arr['error']['message'] ?? '不明なエラー';

            // 404 の場合は models 一覧確認を案内（原因切り分けが速い）
            if ($http_code == 404) {
                $list_models_url = "https://generativelanguage.googleapis.com/{$endpoint_version}/models?key=" . rawurlencode($api_key);
                $debug_msg = "APIエラー (Status: 404): {$api_err}\n"
                           . "※ モデル名が利用可能一覧に存在しない可能性があります。models一覧: {$list_models_url}";
            } else {
                $debug_msg = "APIエラー (Status: $http_code): " . $api_err;
            }
        } elseif (isset($result_arr['candidates'][0]['content']['parts'][0]['text'])) {
            $raw_text = $result_arr['candidates'][0]['content']['parts'][0]['text'];

            // 万一 ``` が混入しても除去
            $clean = preg_replace('/^```[a-zA-Z]*\s*|```\s*$/m', '', $raw_text);
            $result_mermaid = trim($clean);

            // mindmap が先頭に無い場合の保険
            if (stripos(ltrim($result_mermaid), 'mindmap') !== 0) {
                $result_mermaid = "mindmap\n" . $result_mermaid;
            }
        } else {
            $debug_msg = "APIから期待した応答がありませんでした。";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>デバッグモード</title>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
</head>
<body style="font-family: sans-serif; padding: 20px;">

    <h2>AI図解分析（デバッグ）</h2>

    <?php if ($debug_msg): ?>
        <div style="background: #fee; border: 1px solid red; padding: 10px; margin-bottom: 20px; white-space: pre-wrap;">
            <strong>【デバッグ情報】</strong><br>
            <?php echo htmlspecialchars($debug_msg, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <textarea name="request" rows="3" style="width:100%"><?php echo htmlspecialchars($_POST['request'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea><br>
        <button type="submit" style="padding: 10px 20px; margin-top: 10px;">送信</button>
    </form>

    <?php if ($result_mermaid): ?>
        <div style="margin-top: 20px; border: 1px solid #ccc; padding: 20px;">
            <h3>分析結果</h3>
            <pre class="mermaid"><?php echo htmlspecialchars($result_mermaid, ENT_QUOTES, 'UTF-8'); ?></pre>
        </div>
    <?php endif; ?>

    <script>
        mermaid.initialize({ startOnLoad: true });
    </script>
</body>
</html>