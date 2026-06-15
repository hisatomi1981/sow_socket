<?php
// Supabase接続情報を環境変数から取得
// Supabaseの接続文字列例: postgresql://postgres.[project-ref]:[password]@aws-0-[region].pooler.supabase.com:5432/postgres
$supabase_db_url = getenv('SUPABASE_DB_URL');

if ($supabase_db_url) {
    // Supabase接続文字列をパース
    $url_parts = parse_url($supabase_db_url);
    
    $db_host = $url_parts['host'] ?? 'localhost';
    $db_port = $url_parts['port'] ?? 5432;
    $db_name = ltrim($url_parts['path'] ?? 'postgres', '/');
    $db_user = $url_parts['user'] ?? 'postgres';
    $db_password = $url_parts['pass'] ?? 'postgres';
} else {
    // フォールバック（ローカル開発用）
    $db_host = getenv('DB_HOST') ?: 'localhost';
    $db_name = getenv('DB_NAME') ?: 'sowaichat';
    $db_user = getenv('DB_USER') ?: 'postgres';
    $db_password = getenv('DB_PASSWORD') ?: 'postgres';
    $db_port = getenv('DB_PORT') ?: '5432';
}

// 本番環境かローカル環境かを判定
$is_production = getenv('APP_ENV') === 'production';

// DSN作成（Supabase用にSSLモード追加）
if ($is_production || strpos($db_host, 'supabase.com') !== false) {
    // Supabase接続にはSSLが必要
    $dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name};sslmode=require";
} else {
    $dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name}";
}

try {
    $pdo = new PDO($dsn, $db_user, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_TIMEOUT => 5,
    ]);
} catch (PDOException $e) {
    // 本番環境では詳細なエラーを表示しない
    if ($is_production) {
        error_log('Database connection error: ' . $e->getMessage());
        die('データベース接続エラーが発生しました。');
    } else {
        die('Connection failed: ' . $e->getMessage());
    }
}
?>