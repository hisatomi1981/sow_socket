<?php
// ─── 機能番号定義 ───────────────────────────────────────
define('BLOCK_SEQUENCE',  1);   // 順次プログラム
define('BLOCK_REPEAT',    2);   // 反復プログラム
define('BLOCK_CONDITION', 3);   // 条件分岐
define('BLOCK_IMAGE_RECOGNITION', 4);   // 画像認識
define('BLOCK_TEACHABLE', 5);   // TeacchableMachine
define('BLOCK_AI_ANALYSIS', 6);   // AI分析
define('BLOCK_REGISYSTEM', 7);   // レジシステム
define('BLOCK_FACE_RECOGNITION', 8);   // 顔認証
define('BLOCK_FACE_EXPRESSION', 9);   // 表情認証
define('BLOCK_CATE_LED', 10);   // LED点灯
define('BLOCK_CATE_FADE', 11);   // LEDフェードイン
define('BLOCK_CATE_LEDCON', 12);   // LED連続
define('BLOCK_CATE_BACKLIGHT', 13);   // バックライト
define('BLOCK_CATE_TIMER', 14);   // タイマー
define('BLOCK_CATE_WAIT', 15);   // 信号待ち
define('BLOCK_CATE_SOUND', 16);   // サウンド
define('BLOCK_CATE_OUTPUT', 17);   // 出力
define('BLOCK_CATE_SENSORBOARD', 18);   // センサボード
define('BLOCK_CATE_VALIABLE', 19);   // 変数

define('FLOW_SEQUENCE',  31);   // 順次プログラム
define('FLOW_REPEAT',    32);   // 反復プログラム
define('FLOW_CONDITION', 33);   // 条件分岐
define('FLOW_IMAGE_RECOGNITION', 34);   // 画像認識
define('FLOW_TEACHABLE', 35);   // TeacchableMachine
define('FLOW_AI_ANALYSIS', 36);   // AI分析
define('FLOW_REGISYSTEM', 37);   // レジシステム
define('FLOW_FACE_RECOGNITION', 38);   // 顔認証
define('FLOW_FACE_EXPRESSION', 39);   // 表情認証
define('FLOW_CATE_LED',         40);// LED点灯
define('FLOW_CATE_FADE',        41);// LEDフェードイン
define('FLOW_CATE_LEDCON',      42);// LED連続
define('FLOW_CATE_BACKLIGHT',   43);// バックライト
define('FLOW_CATE_TIMER',       44);// タイマー
define('FLOW_CATE_WAIT',        45);// 信号待ち
define('FLOW_CATE_SOUND',       46);// サウンド
define('FLOW_CATE_OUTPUT',      47);// 出力
define('FLOW_CATE_SENSORBOARD', 48);// センサボード
define('FLOW_CATE_VALIABLE',    49);// 変数
define('FLOW_CATE_ROUTINE',    50);// サブルーチン

define('NET_BLOCK_SEQUENCE',  61);   // 順次プログラム
define('NET_BLOCK_REPEAT',    62);   // 反復プログラム
define('NET_BLOCK_CONDITION', 63);   // 条件分岐
define('NET_BLOCK_IMAGE_RECOGNITION', 64);   // 画像認識
define('NET_BLOCK_TEACHABLE', 65);   // TeacchableMachine
define('NET_BLOCK_AI_ANALYSIS', 66);   // AI分析
define('NET_BLOCK_REGISYSTEM', 67);   // レジシステム
define('NET_BLOCK_FACE_RECOGNITION', 68);   // 顔認証
define('NET_BLOCK_FACE_EXPRESSION', 69);   // 表情認証
define('NET_BLOCK_CATE_SEND', 70);   // 送信
define('NET_BLOCK_CATE_SOUND', 71);   // 音
define('NET_BLOCK_CATE_AI', 72);   // AI
define('NET_BLOCK_CATE_FONT', 73);   // フォント
define('NET_BLOCK_CATE_VALUE', 74);   // 変数
define('NET_BLOCK_CATE_SETUP', 75);   // サーバ設定
define('NET_BLOCK_CATE_WAIT', 76);   // 待ち

define('NET_FLOW_SEQUENCE',  91);   // 順次プログラム
define('NET_FLOW_REPEAT',    92);   // 反復プログラム
define('NET_FLOW_CONDITION', 93);   // 条件分岐
define('NET_FLOW_IMAGE_RECOGNITION', 94);   // 画像認識
define('NET_FLOW_TEACHABLE', 95);   // TeacchableMachine
define('NET_FLOW_AI_ANALYSIS', 96);   // AI分析
define('NET_FLOW_REGISYSTEM', 97);   // レジシステム
define('NET_FLOW_FACE_RECOGNITION', 98);   // 顔認証
define('NET_FLOW_FACE_EXPRESSION', 99);   // 表情認証
define('NET_FLOW_CATE_SEND', 100);   // 送信
define('NET_FLOW_CATE_SOUND', 101);   // 音
define('NET_FLOW_CATE_AI', 102);   // AI
define('NET_FLOW_CATE_FONT', 103);   // フォント
define('NET_FLOW_CATE_VALUE', 104);   // 変数
define('NET_FLOW_CATE_SETUP', 105);   // サーバ設定
define('NET_FLOW_CATE_WAIT', 106);   // 待ち
define('NET_FLOW_CATE_RESOUND', 107);   // 受信　音
define('NET_FLOW_CATE_REFONT', 108);   // 受信　フォント
define('NET_FLOW_CATE_RECONDITION', 109);   // 受信分岐
//ここで追加したらfeature_admin.phpでも追加する
// ─── 機能表示用ラベル ────────────────────────────────────
function get_feature_label($feature_id) {
    $labels = [
        //=== ブロック ===
        // プログラム
        BLOCK_SEQUENCE          => '順次プログラム',
        BLOCK_REPEAT            => '反復',
        BLOCK_CONDITION         => '条件分岐',
        // カテゴリ
        BLOCK_CATE_LED          => 'LED点灯',
        BLOCK_CATE_FADE         => 'LEDフェード',
        BLOCK_CATE_LEDCON       => 'LED連続点灯',
        BLOCK_CATE_BACKLIGHT    => 'バックライト',
        BLOCK_CATE_TIMER        => 'タイマ',
        BLOCK_CATE_WAIT         => '信号待ち',
        BLOCK_CATE_SOUND        => 'サウンド',
        BLOCK_CATE_OUTPUT       => '出力',
        BLOCK_CATE_SENSORBOARD  => 'センサボード出力(UD-1/2のみ)',
        BLOCK_CATE_VALIABLE     => '変数(UC-7/8のみ)',
        // 機能
        BLOCK_IMAGE_RECOGNITION => '画像認識',
        BLOCK_TEACHABLE         => 'TeacchableMachine(Win,Chromebook)',
        BLOCK_FACE_RECOGNITION  => '顔検知',
        BLOCK_FACE_EXPRESSION   => '表情検知',
        BLOCK_AI_ANALYSIS       => 'AI分析',
        BLOCK_REGISYSTEM        => 'レジシステム',

        //=== フローチャート ===
        // プログラム
        FLOW_SEQUENCE           => '順次プログラム',
        FLOW_REPEAT             => '反復プログラム',
        FLOW_CONDITION          => '条件分岐',
        // カテゴリ
        FLOW_CATE_LED           => 'LED点灯',
        FLOW_CATE_FADE          => 'LEDフェード',
        FLOW_CATE_LEDCON        => 'LED連続点灯',
        FLOW_CATE_BACKLIGHT     => 'バックライト',
        FLOW_CATE_TIMER         => 'タイマ',
        FLOW_CATE_WAIT          => '信号待ち',
        FLOW_CATE_SOUND         => 'サウンド',
        FLOW_CATE_OUTPUT        => '出力',
        FLOW_CATE_SENSORBOARD   => 'センサボード出力(UD-1/2のみ)',
        FLOW_CATE_VALIABLE      => '変数(UC-7/8のみ)',
        FLOW_CATE_ROUTINE       => 'サブルーチン',
        // 機能
        FLOW_IMAGE_RECOGNITION  => '画像認識',
        FLOW_TEACHABLE          => 'TeacchableMachine(Win,Chromebook)',
        FLOW_FACE_RECOGNITION   => '顔検知',
        FLOW_FACE_EXPRESSION    => '表情検知',
        FLOW_AI_ANALYSIS        => 'AI分析',
        FLOW_REGISYSTEM         => 'レジシステム',

        NET_BLOCK_SEQUENCE      => '順次プログラム',
        NET_BLOCK_REPEAT        => '反復プログラム',
        NET_BLOCK_CONDITION     => '条件分岐',
        NET_BLOCK_IMAGE_RECOGNITION => '画像認識',
        NET_BLOCK_TEACHABLE     => 'TeacchableMachine(Win,Chromebook)',
        NET_BLOCK_AI_ANALYSIS   => 'AI分析',
        NET_BLOCK_REGISYSTEM    => 'レジシステム',
        NET_BLOCK_FACE_RECOGNITION  => '顔検知',
        NET_BLOCK_FACE_EXPRESSION   => '表情検知',
        NET_BLOCK_CATE_SEND     => '送信',
        NET_BLOCK_CATE_SOUND    => '音',
        NET_BLOCK_CATE_AI       => 'AI',
        NET_BLOCK_CATE_FONT     => 'フォント',
        NET_BLOCK_CATE_VALUE    => '変数',
        NET_BLOCK_CATE_SETUP    => 'サーバ設定',
        NET_BLOCK_CATE_WAIT     => '待ち',

        NET_FLOW_SEQUENCE       => '順次プログラム',
        NET_FLOW_REPEAT         => '反復プログラム',
        NET_FLOW_CONDITION      => '条件分岐',
        NET_FLOW_IMAGE_RECOGNITION  => '画像認識',
        NET_FLOW_TEACHABLE      => 'TeacchableMachine(Win,Chromebook)',
        NET_FLOW_AI_ANALYSIS    => 'AI分析',
        NET_FLOW_REGISYSTEM     => 'レジシステム',
        NET_FLOW_FACE_RECOGNITION  => '顔検知',
        NET_FLOW_FACE_EXPRESSION   => '表情検知',
        NET_FLOW_CATE_SEND      => '送信',
        NET_FLOW_CATE_SOUND     => '音',
        NET_FLOW_CATE_AI        => 'AI',
        NET_FLOW_CATE_FONT      => 'フォント',
        NET_FLOW_CATE_VALUE     => '変数',
        NET_FLOW_CATE_SETUP     => 'サーバ設定',
        NET_FLOW_CATE_WAIT      => '待ち',
        NET_FLOW_CATE_RESOUND   => '受信　音',
        NET_FLOW_CATE_REFONT    => '受信　フォント',
        NET_FLOW_CATE_RECONDITION  => '受信分岐',
    ];
    return $labels[$feature_id] ?? '未定義';
}

// ─── デフォルトで除外する機能（レジシステム・顔認証・表情認証系） ────
function get_default_off_features() {
    return [
        BLOCK_REGISYSTEM,
        BLOCK_FACE_RECOGNITION,
        BLOCK_FACE_EXPRESSION,
        FLOW_REGISYSTEM,
        FLOW_FACE_RECOGNITION,
        FLOW_FACE_EXPRESSION,
        NET_BLOCK_FACE_RECOGNITION,
        NET_BLOCK_FACE_EXPRESSION,
        NET_FLOW_FACE_RECOGNITION,
        NET_FLOW_FACE_EXPRESSION,
    ];
}

// ─── 機能設定の取得（DBには「除外リスト」が保存されている） ────
// 戻り値：除外されている機能IDの配列
function load_features(PDO $pdo, $school_id_userid) {
    if (empty($school_id_userid)) {
        return get_default_off_features();
    }

    $sql = "SELECT enabled_features 
            FROM feature_setting 
            WHERE school_id_userid = :school";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':school' => $school_id_userid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // レコードなし → nullを返す（ログイン側でnoneに変換される）
        return null;
    }

    // DBの値＝除外リストをそのまま返す
    return !empty($row['enabled_features'])
        ? array_values(array_filter(array_map('intval', explode(',', $row['enabled_features']))))
        : [];
}

// ─── POSTから機能配列を復元 ──────────────────────────────
function features_from_post() {
    if (!isset($_POST['enabled_features'])) {
        return null;
    }
    if ($_POST['enabled_features'] === 'none') {
        return null; // DBレコードなし → デフォルト除外を適用
    }
    if ($_POST['enabled_features'] === '') {
        return []; // 全チェック外し → 除外なし（全表示）
    }
    return array_values(array_filter(
        array_map('intval', explode(',', $_POST['enabled_features']))
    ));
}

// 指定した機能IDが除外されていなければ true（⁼表示する）
function show_feature($feature_id) {
	global $disabled_features;
	// null=DBレコードなし → デフォルト除外適用、[]=除外なし（全表示）
	$list = ($disabled_features === null) ? get_default_off_features() : $disabled_features;
	return !in_array((int)$feature_id, $list, true);
}

// ─── 機能が有効か判定 ────────────────────────────────────
function has_feature(array $features, $feature_id) {
    return in_array((int)$feature_id, $features, true);
}

//機能をグループキーに振り分け
function get_feature_group($feature_id) {
    switch ($feature_id) {
        //=== ブロック ===
        // プログラム
        case BLOCK_SEQUENCE:
        case BLOCK_REPEAT:
        case BLOCK_CONDITION:
            return 'block_program';

        // カテゴリ
        case BLOCK_CATE_LED:
        case BLOCK_CATE_FADE:
        case BLOCK_CATE_LEDCON:
        case BLOCK_CATE_BACKLIGHT:
        case BLOCK_CATE_TIMER:
        case BLOCK_CATE_WAIT:
        case BLOCK_CATE_SOUND:
        case BLOCK_CATE_OUTPUT:
        case BLOCK_CATE_SENSORBOARD:
        case BLOCK_CATE_VALIABLE:
            return 'block_category';

        // 機能
        case BLOCK_IMAGE_RECOGNITION:
        case BLOCK_TEACHABLE:
        case BLOCK_FACE_RECOGNITION:
        case BLOCK_FACE_EXPRESSION:
        case BLOCK_AI_ANALYSIS:
        case BLOCK_REGISYSTEM:
            return 'block_function';

        //=== フローチャート ===
        // プログラム
        case FLOW_SEQUENCE:
        case FLOW_REPEAT:
        case FLOW_CONDITION:
            return 'flow_program';

        // カテゴリ
        case FLOW_CATE_LED:
        case FLOW_CATE_FADE:
        case FLOW_CATE_LEDCON:
        case FLOW_CATE_BACKLIGHT:
        case FLOW_CATE_TIMER:
        case FLOW_CATE_WAIT:
        case FLOW_CATE_SOUND:
        case FLOW_CATE_OUTPUT:
        case FLOW_CATE_SENSORBOARD:
        case FLOW_CATE_VALIABLE:
        case FLOW_CATE_ROUTINE:
            return 'flow_category';

        // 機能
        case FLOW_IMAGE_RECOGNITION:
        case FLOW_TEACHABLE:
        case FLOW_FACE_RECOGNITION:
        case FLOW_FACE_EXPRESSION:
        case FLOW_AI_ANALYSIS:
        case FLOW_REGISYSTEM:
            return 'flow_function';


        //=== ブロック ===
        // プログラム
        case NET_BLOCK_SEQUENCE:
        case NET_BLOCK_REPEAT:
        case NET_BLOCK_CONDITION:
            return 'net_block_program';
        // カテゴリ
        case NET_BLOCK_CATE_SEND:
        case NET_BLOCK_CATE_SOUND:
        case NET_BLOCK_CATE_AI:
        case NET_BLOCK_CATE_FONT:
        case NET_BLOCK_CATE_VALUE:
        case NET_BLOCK_CATE_SETUP:
        case NET_BLOCK_CATE_WAIT:
            return 'net_block_category';
        // 機能
        case NET_BLOCK_IMAGE_RECOGNITION:
        case NET_BLOCK_TEACHABLE:
        case NET_BLOCK_AI_ANALYSIS:
        case NET_BLOCK_REGISYSTEM:
        case NET_BLOCK_FACE_RECOGNITION:
        case NET_BLOCK_FACE_EXPRESSION:
            return 'net_block_function';

        //=== フローチャート ===
        // プログラム
        case NET_FLOW_SEQUENCE:
        case NET_FLOW_REPEAT:
        case NET_FLOW_CONDITION:
            return 'net_flow_program';
        // カテゴリ
        case NET_FLOW_CATE_SEND:
        case NET_FLOW_CATE_SOUND:
        case NET_FLOW_CATE_AI:
        case NET_FLOW_CATE_FONT:
        case NET_FLOW_CATE_VALUE:
        case NET_FLOW_CATE_SETUP:
        case NET_FLOW_CATE_WAIT:
        case NET_FLOW_CATE_RESOUND:
        case NET_FLOW_CATE_REFONT:
        case NET_FLOW_CATE_RECONDITION:
            return 'net_flow_category';
        // 機能
        case NET_FLOW_IMAGE_RECOGNITION:
        case NET_FLOW_TEACHABLE:
        case NET_FLOW_AI_ANALYSIS:
        case NET_FLOW_REGISYSTEM:
        case NET_FLOW_FACE_RECOGNITION:
        case NET_FLOW_FACE_EXPRESSION:
            return 'net_flow_function';


        default:
            return 'other_function';
    }
}

//グループの表示ラベル
function get_group_label($group_key) {
    switch ($group_key) {
        // ブロック
        case 'block_program':  return 'プログラム（ブロック）';
        case 'block_category': return 'カテゴリ（ブロック）';
        case 'block_function': return '機能（ブロック）';

        // フローチャート
        case 'flow_program':   return 'プログラム（フローチャート）';
        case 'flow_category':  return 'カテゴリ（フローチャート）';
        case 'flow_function':  return '機能（フローチャート）';

        // その他
        case 'other_program':
        case 'other_category':
        case 'other_function':
        case 'other':          return 'その他';

        // 互換用（旧キー）
        case 'block':          return 'ブロック';
        case 'flow':           return 'フローチャート';

        default:               return $group_key;
    }
}