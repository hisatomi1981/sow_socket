<?php
function renderPrintHeadUI($device,$school_id_userid) {
  echo <<<HTML
    <div id="printhead" class="container py-3">
      <!-- 上段：学年・組・番・名前 -->
      <div class="row justify-content-center g-2 align-items-center">
        <div class="col-auto">
          <label for="print_nen" class="me-1 mb-0">年</label>
          <input type="text" id="print_nen" class="form-control form-control-sm d-inline-block text-center" style="width:6ch;">
        </div>
        <div class="col-auto">
          <label for="print_kumi" class="me-1 mb-0">組</label>
          <input type="text" id="print_kumi" class="form-control form-control-sm d-inline-block text-center" style="width:6ch;">
        </div>
        <div class="col-auto">
          <label for="print_ban" class="me-1 mb-0">番</label>
          <input type="text" id="print_ban" class="form-control form-control-sm d-inline-block text-center" style="width:6ch;">
        </div>
        <div class="col-auto">
          <label for="print_name" class="me-1 mb-0">名前：</label>
          <input type="text" id="print_name" class="form-control form-control-sm d-inline-block" style="width:24ch;">
        </div>
      </div>

      <!-- タイトル -->
      <div class="row justify-content-center my-3">
        <div class="col-md-8">
          <div class="row g-2 justify-content-center align-items-center">
            <div class="col-auto"><span class="mb-0">タイトル：</span></div>
            <div class="col">
              <input type="text" id="print_title" class="form-control form-control-sm">
            </div>
          </div>
        </div>
      </div>

      <!-- 構想／仕様 -->
      <div class="row justify-content-center g-3">
        <div class="col-sm-5">
          <div class="mb-1">構想：</div>
          <div class="d-flex gap-2 justify-content-center">
            <div class="printwaku flex-shrink-0" id="print-koso"></div>
            <textarea class="form-control text-height-5" id="print-text-koso"></textarea>
          </div>
        </div>
        <div class="col-sm-5">
          <div class="mb-1">仕様：</div>
          <div class="d-flex gap-2 justify-content-center">
            <div class="printwaku flex-shrink-0" id="print-siyo"></div>
            <textarea class="form-control text-height-5" id="print-text-siyo"></textarea>
          </div>
        </div>
      </div>

      <!-- 工夫点／感想 -->
      <div class="row justify-content-center g-3 mt-2">
        <div class="col-sm-5">
          <div class="mb-1">工夫点：</div>
          <div class="d-flex gap-2 justify-content-center">
            <div class="printwaku flex-shrink-0" id="print-kuhu"></div>
            <textarea class="form-control text-height-5" id="print-text-kuhu"></textarea>
          </div>
        </div>
        <div class="col-sm-5">
          <div class="mb-1">感想：</div>
          <div class="d-flex gap-2 justify-content-center">
            <div class="printwaku flex-shrink-0" id="print-kanso"></div>
            <textarea class="form-control text-height-5" id="print-text-kanso"></textarea>
          </div>
        </div>
      </div>

      <!-- ボタン行（印刷／アップロード） -->
      <div class="row justify-content-center g-3 my-1" id="allprintbtn">
        <div class="col-auto backcolor_print">
          <a class="printbtn" id="prtbtn1" href="#" onClick="printBlock()">印刷</a>
          <div class="row justify-content-center mt-1">
            <small>PDF出力する際は「印刷」から出力できます</small>
          </div>
        </div>

        <div class="col-auto backcolor_upload ms-5">
          <div class="row justify-content-center align-items-center g-2">
            <div class="col-auto">
              <label for="pass" class="mb-0">パスワード：</label>
            </div>
            <div class="col-auto">
              <input type="text" id="pass" class="form-control form-control-sm" style="width:14ch;" placeholder="4桁以上の数字" inputmode="numeric">
            </div>
            <div class="col-auto">
              <a class="printbtn" id="prtbtn3" href="#" onClick="saveReport('{$device}','{$school_id_userid}')">アップロード</a>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-auto">
              <small>パスワードは後から閲覧する際必要です</small>
            </div>
          </div>
        </div>
      </div>

      <!-- ボタン行（キャンセル） -->
      <div class="row justify-content-center g-3 my-1" id="allprintbtn2">
        <div class="col-auto">
          <a class="printbtn" id="prtbtn2" href="#" onClick="printNoDisplay()">キャンセル</a>
        </div>
      </div>
    </div>
    <script>
      // 年・番は数字のみ
      document.querySelectorAll('#print_nen, #print_ban').forEach(el => {
        el.addEventListener('input', function() {
          this.value = this.value.replace(/[０-９]/g, s =>
            String.fromCharCode(s.charCodeAt(0) - 0xFEE0)
          );
          this.value = this.value.replace(/[^0-9]/g, '');
        });
      });

      // 組は数字＋アルファベット許可
      document.querySelector('#print_kumi').addEventListener('input', function() {
        // 全角数字→半角
        this.value = this.value.replace(/[０-９]/g, s =>
          String.fromCharCode(s.charCodeAt(0) - 0xFEE0)
        );
        // 全角アルファベット→半角
        this.value = this.value.replace(/[Ａ-Ｚａ-ｚ]/g, s =>
          String.fromCharCode(s.charCodeAt(0) - 0xFEE0)
        );
        // 数字・アルファベット以外を削除
        this.value = this.value.replace(/[^0-9a-zA-Z]/g, '');
      });
    </script>
  HTML;
}

function render_ai_answer_section() {
  echo <<<HTML
    <div id="ai_anser" class="container py-3">
      <div class="row justify-content-center" id="ai_button">
        <div class="col-auto">
          <a class="printbtn" id="aibtn" href="#" onClick="get_ai()">AIで解析する</a>
        </div>
      </div>
      <div class="row" style="height: 10px"></div>
      <div class="row justify-content-center">
        <div class="col-auto">
          <div id="answerArea">
            <div>ここにAIの分析結果が表示されます。</div>
          </div>
        </div>
      </div>
      <div class="row" style="height: 10px"></div>
      <div class="row justify-content-center">
        <div class="col-auto">
          <a class="printbtn" href="#" onClick="aiNoDisplay()">閉じる</a>
        </div>
      </div>
    </div>
  HTML;
}
?>
