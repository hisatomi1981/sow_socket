<?php
if (!isset($_REQUEST['mode'])) {
    require_once("../function/errorhtml.php");
    get_direct_access_error_html();
    exit;
} else {
    $id_number_kanri = $_POST['id_number_kanri'];
    $serial_number_kanri = $_POST['serial_number_kanri'];
    $serial_number_userid = $_POST['serial_number_userid'];
    require_once("../function/function.php");
	require_once("../../function/database.php");
	/** @var PDO $pdo */
}
//print_r($_POST);
try {
    //教員用データ取得
        $sql = "select *
                from userid
                where source_number_userid = '" . $serial_number_userid . "'
                and (kubun_userid = '0' or kubun_userid = '9')";

        $stmt = $pdo->query($sql);
        if (!$stmt) {
            $info = $pdo->errorInfo();
            header('Content-Type: text/plain; charset=UTF-8');
            exit($info[2]);
        }
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $teacher = $stmt->fetch();    //fetchAll：全件 //fetch:1件

    //生徒用データ取得
        $sql = "select * from userid where source_number_userid = '" . $serial_number_userid . "' and kubun_userid = '1';";
        $stmt = $pdo->query($sql);
        if (!$stmt) {
            $info = $pdo->errorInfo();
            header('Content-Type: text/plain; charset=UTF-8');
            exit($info[2]);
        }
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $student = $stmt->fetchAll();    //fetchAll：全件 //fetch:1件

    //生徒の数
        $studentcnt = $stmt->rowCount();
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}finally {
    $pdo = null;
}
///////////////　　PDF出力　　//////////////////////////////////////
require_once('tcpdf/tcpdf.php');

//PDFを作成するための新しいインスタンスを作成
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

//PDFドキュメントのプロパティを設定
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Author');
$pdf->SetTitle('双方向通信アプリ ID パスワード');
$pdf->SetSubject('Subject');
$pdf->SetKeywords('Keywords');

//PDFページのヘッダーとフッターを設定
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING);
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

//ヘッダーフッターの非表示
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//余白の設定
$pdf->SetMargins(10, 10, 10); // 上余白を0に設定
$pdf->SetHeaderMargin(0);   // ヘッダー余白を0に
$pdf->SetFooterMargin(0);   // フッター余白を0に

//PDFドキュメントにページを追加
$pdf->AddPage();

///////////////////////////////////////////////////////////////////////
//ページのフォントを設定
$font = new TCPDF_FONTS();
// フォント・サブセットを使用するか（初期はtrueなので呼ばなくてもいい）  
$pdf->setFontSubsetting(true);
// ttfフォントファイルからtcpdf用フォントファイルを生成（tcpdf用フォントファイルがある場合は再生成しない）  
$fontX = $font->addTTFfont('tcpdf/fonts/ttf/Meiryo-Bold-01.ttf');
$pdf->SetFont($fontX, '', 20);

///////////////////////////////////////////////////////////////////////
function getModelName($code) {
    $model_map = [
        "51" => "双方向　SOW5/6",
        "52" => "双方向　SOW5/6(iPad)",
        
        "1"  => "UC-7/8　オーロラクロック2N",
        "2"  => "UC-7/8　オーロラクロック2N(iPad)",
        "3"  => "UD-1/2　オーロラクロック3",
        "4"  => "UD-1/2　オーロラクロック3(iPad)",

        "31" => "UC7/8　オーロラクロック2N　計測制御",
        "32" => "UC7/8　オーロラクロック2N(iPad)　計測制御",
        "33" => "UD-1/2　オーロラクロック3　計測制御",
        "34" => "UD-1/2　オーロラクロック3(iPad)　計測制御",
													
        "35" => "AM-1　計測制御",
        "36" => "AM-1(iPad)　計測制御",
        "37" => "AT-2　計測制御",
        "38" => "HR-1　計測制御",
        "39" => "LC-12　計測制御",

        "61" => "双方向　SOW5/6　AI版",
        "62" => "双方向　SOW5/6(iPad)　AI版",

        "5"  => "UC-7/8　オーロラクロック2N",
        "6"  => "UC-7/8　オーロラクロック2N(iPad)",
        "7"  => "UD-1/2　オーロラクロック3",
        "8"  => "UD-1/2　オーロラクロック3(iPad)",

        "0"  => "全部"
    ];

    return $model_map[$code] ?? "未定義";
}
///////////////////////////////////////////////////////////////////////
//1ページ目
//重要
    $boxW = 120;
    $boxH = 28;

    $pageW   = $pdf->getPageWidth();
    $m       = $pdf->getMargins();
    $usableW = $pageW - $m['left'] - $m['right'];
    $x = $m['left'] + ($usableW - $boxW) / 2;

    $pdf->Ln(6);
    $pdf->SetLineWidth(0.8);

    // 1行目
    $pdf->SetFont($fontX, '', 30);
    $pdf->SetTextColor(255, 0, 0);//文字色を赤に
    $pdf->SetXY($x, $pdf->GetY());
    $pdf->Cell($boxW, 14, '重要', 0, 1, 'C');

    // 2行目
    $pdf->SetFont($fontX, '', 16);
    $pdf->SetX($x);
    $pdf->Cell($boxW, 14, '※アプリ起動に必要です', 0, 1, 'C');

    $pdf->SetLineWidth(0.8);   // ← ここを太くする
    // 枠をまとめて囲う
    $yTop = $pdf->GetY() - 28;
    $pdf->SetDrawColor(255, 0, 0);//枠線の色を赤に
    $pdf->Rect($x, $yTop, $boxW, 28);

    $pdf->SetLineWidth(0.2);

    $pdf->SetDrawColor(0, 0, 0);//枠線の色を黒に
    $pdf->SetTextColor(0, 0, 0);//文字色を黒に
//名前
    $pdf->SetFont($fontX, '', 25);
    $pdf->Ln(15);
    $htmlcontent = "<div style=\"text-align: center\">";
    $htmlcontent .= $teacher['school_name_userid'] . "　様";
    $htmlcontent .= "</div>";
    $pdf->writeHTML($htmlcontent, true, false, true, false, '');
//アプリ名
    $pdf->SetFont($fontX, '', 18);
    $pdf->Ln(10);
    $htmlcontent = "<div style=\"text-align: center\">";
    $htmlcontent .= "製品名：". getModelName($teacher['kengen_userid']) ."";
    $htmlcontent .= "</div>";    
    $pdf->writeHTML($htmlcontent, true, false, true, false, '');
//期限
    $pdf->Ln(5);
    $htmlcontent = "<div style=\"text-align: center\">";
    $htmlcontent .= "有効期限：" . date("Y/m/d", strtotime($teacher['kigen_userid'])) . "";
    $htmlcontent .= "</div>";
    $pdf->writeHTML($htmlcontent, true, false, true, false, '');
//ID、パスワード
    $pdf->Ln(20);    
    if ($teacher['kubun_userid'] == "9"){
        $x = 30; // ← 左から40mmの位置に出したい場合
        $y = $pdf->GetY();
        $pdf->writeHTMLCell(
            0,      // 幅（0＝残り全幅）
            0,      // 高さ（自動）
            $x,     // ★ X座標
            $y,     // Y座標
            '個人用',
            0,
            1,
            false,
            true,
            'L',
            true
        );
    }
    else{
        $x = 30; // ← 左から40mmの位置に出したい場合
        $y = $pdf->GetY();
        $pdf->writeHTMLCell(
            0,      // 幅（0＝残り全幅）
            0,      // 高さ（自動）
            $x,     // ★ X座標
            $y,     // Y座標
            '教員用',
            0,
            1,
            false,
            true,
            'L',
            true
        );
    }
    // 教員用データの設定
    $data = array(
        array('ID', $teacher['id_number_userid']),
        array('パスワード', $teacher['pass_userid']),
    );

//ページの幅取得
    $pageWidth = $pdf->getPageWidth();
    $margins   = $pdf->getMargins();
    $usableWidth = $pageWidth - $margins['left'] - $margins['right'];
    $tableWidth  = 50 + 70;
    $offset = 20; // 右に 5mm（好みで 3〜10）
    $x = ($pageWidth - $tableWidth) / 2 + $offset;

$htmlblank = "<div style=\"height: 3px\"></div>";

foreach ($data as $row) {
    $pdf->SetX($x); // テーブルのx座標を設定する
    $pdf->Cell(50, 6, $row[0], 0);
    $pdf->Cell(70, 6, $row[1], 0);
    $pdf->Ln(6);
    $pdf->writeHTML($htmlblank, true, false, true, false, '');
}

//////////////////////////////
if ($teacher['kubun_userid'] == "9"){//個人なら何も表示しない
    $pdf->writeHTML("　", true, false, true, false, '');
    $pdf->Ln(30);
}
else{    
    $x = 30; // ← 左から40mmの位置に出したい場合
    $y = $pdf->GetY();
    $pdf->writeHTMLCell(
        0,      // 幅（0＝残り全幅）
        0,      // 高さ（自動）
        $x,     // ★ X座標
        $y,     // Y座標
        '生徒用',
        0,
        1,
        false,
        true,
        'L',
        true
    );

    $offset = 20; // 右に 5mm（好みで 3〜10）
    $x = ($pageWidth - $tableWidth) / 2 + $offset;

    foreach ($student as $val) {
        // 生徒用データの設定
        $data_student = array(
            array('ID', $val['id_number_userid']),
            array('パスワード', $val['pass_userid']),
        );

        foreach ($data_student as $row) {
            $pdf->SetX($x); // テーブルのx座標を設定する
            $pdf->Cell(50, 4, $row[0], 0);
            $pdf->Cell(70, 4, $row[1], 0);
            $pdf->Ln(6);
            $pdf->writeHTML($htmlblank, true, false, true, false, '');
        }
    }
}
///////////////////////////////////////////////////////////////////////
$pdf->Ln(20);
$htmlcontent = "<div style=\"text-align: center; line-height:1.6; color:#ff0000;\">";
$htmlcontent .= "<u>取扱説明書に記載のQRコードからログイン画面を開き</u><br>";
$htmlcontent .= "<u>上記のID、パスワードからアプリを起動してください。</u>";
$htmlcontent .= "</div>";
$pdf->writeHTML($htmlcontent, true, false, true, false, '');

$pdf->Ln(15);
$htmlcontent = "<div style=\"text-align: center\">";
$htmlcontent .= "ご不明な点がございましたらご連絡下さい。<br>";
$htmlcontent .= "久富電機産業株式会社<br>";
$htmlcontent .= "TEL：084-955-6889";
$htmlcontent .= "</div>";
$pdf->writeHTML($htmlcontent, true, false, true, false, '');
//HTMLコンテンツをPDFに変換

///////////////////////////////////////////////////////////////////////

//PDFドキュメントを出力
$pdf->Output('sow.pdf', 'I');//D:ダウンロード I:ブラウザでPDFを直接開く
