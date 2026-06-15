<?php
// ZIP拡張モジュールが有効かどうかを確認する
if (extension_loaded('zip')) {
    echo "ZIP拡張モジュールは有効です。\n";
    echo "ZipArchiveクラスは利用可能です。\n";

    // ZipArchiveのバージョン情報などを表示
    if (class_exists('ZipArchive')) {
        echo "ZipArchiveクラスのバージョン情報:\n";
        $zip = new ZipArchive();
        echo "ZipArchiveサポート: 機能しています\n";

        // 利用可能な定数を表示
        echo "\nZipArchiveの主な定数:\n";
        echo "CREATE: " . (defined('ZipArchive::CREATE') ? "定義済み" : "未定義") . "\n";
        echo "OVERWRITE: " . (defined('ZipArchive::OVERWRITE') ? "定義済み" : "未定義") . "\n";
        echo "EXCL: " . (defined('ZipArchive::EXCL') ? "定義済み" : "未定義") . "\n";
        echo "CHECKCONS: " . (defined('ZipArchive::CHECKCONS') ? "定義済み" : "未定義") . "\n";
    } else {
        echo "警告: extension_loaded('zip')はtrueを返しましたが、ZipArchiveクラスが見つかりません。\n";
    }
} else {
    echo "警告: ZIP拡張モジュールは有効になっていません。\n";
    echo "ZipArchiveクラスを使用するには、PHP設定でZIP拡張モジュールを有効にする必要があります。\n";
}

// PHPのバージョンも表示
echo "\nPHPバージョン: " . phpversion() . "\n";

// すべてのロードされた拡張モジュールを表示
echo "\nロードされているすべてのPHP拡張モジュール:\n";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $extension) {
    echo $extension . "\n";
}
