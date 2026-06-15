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
      <div class="col-auto backcolor_print text-center">
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

function renderSetpromptHeadUI() {
  echo <<<HTML
    <div id="setprompt">
      <div class="row justify-content-center">
        AIに送信するプロンプトを設定します。
      </div>
      <div class="row" style="height: 10px"></div>
      <div class="row justify-content-center">
        <div>入力したメッセージ
          <input type="text" id="prompttext" size="50" value="を英語にしてその結果を">
          <select class="select_prompt" name="prompt_select" id='promptSelect'>
            <option value="10文字以内で答えて">10文字以内で答えて</option>
            <option value="20文字以内で答えて">20文字以内で答えて</option>
            <option value="">指定なし</option>
          </select>
        </div>
      </div>
      <div class="row" style="height: 10px"></div>
      <div class="row justify-content-center">
        <a class="printbtn" id="promptbtn" href="#" onClick="promptNoDisplay()">閉じる</a>
      </div>
      <div class="row" style="height: 30px"></div>
    </div>
  HTML;
}
function renderSetGroupHeadUI() {
  echo <<<HTML
    <div id="setgroup">
      <div class="row justify-content-center">
        グループ送信するためのグループの設定を行います。<br>生徒番号を入力し「追加」ボタンを押し追加して下さい。
      </div>
      <table width="70%" border="0" align="center">
        <tbody>
          <tr>
            <td align="center">
              <select class="select_group" name="group_select" id='groupSelect' onChange="change_group()">
                <option value="1">グループ１</option>
                <option value="2">グループ２</option>
                <option value="3">グループ３</option>
              </select><br><br>
              生徒番号<br>
              <input type="text" id="groupaddno" size="10"><button onClick="setgroupno()">追加</button>
            </td>
            <td>
              <form name="form_group">
                <!--<textarea readonly class="textarea_group" name='textarea_group' id='groupText' cols='100' rows='4'></textarea>-->
                <select class="textarea_group" name="textarea_group" id='groupText' size="3"></select>
              </form>
            </td>
            <td>
              削除はリストから削除する生徒番号を選択し、<br>「削除」ボタンを押して下さい<br>
              <button onClick="delete_group()">削除</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="row justify-content-center">
         <div class="col-auto">
          <a class="printbtn" id="groupbtn2" href="#" onClick="groupNoDisplay()">閉じる</a>
         </div>
      </div>
      <div class="row" style="height: 30px"></div>
    </div>
  HTML;
}

function renderSetupNetworkNoHeadUI() {
  echo <<<HTML
    <div id="setup_networkno">
      <div class="row justify-content-center">
        利用するネットワーク番号を指定することができます。
      </div>
      <div class="row justify-content-center">
        <div class="col-auto text-center">
          「例：192.168.〇〇〇.〇〇〇のように数字3桁（半角）を4つにわけ、
          各数字は<span class="color_red fontbold">1から255</span>の範囲内で指定して下さい。」
        </div>
      </div>
      <div class="row justify-content-center">
        ネットワーク番号設定後に「利用開始」をクリックするとブラウザのタブに利用中のネットワーク番号が表示されます。
      </div><br>
      <div class="row justify-content-center">
        <div class="col-auto text-center">
          利用するネットワーク番号：
          <input type="text" id="network_no1" class="netnoip" value="192">.
          <input type="text" id="network_no2" class="netnoip" value="168">.
          <input type="text" id="network_no3" class="netnoip">.
          <input type="text" id="network_no4" class="netnoip">　
          <a href="#" onClick="network_no_default()">初期状態に戻す</a>
        </div>
      </div>
      <div class="row justify-content-center mt-3">
        <div class="col-auto text-center">
          <a class="printbtn" id="prtbtn2" href="#" onClick="change_network_no_notDisplay()">閉じる</a>
        </div>
      </div>
      <div class="row" style="height: 30px"></div>
    </div>
  HTML;
}

function rendercli_sendHeadUI() {
  echo <<<HTML
    <div id="cli_send">
      <div class="row justify-content-center">
        <div class="col-sm-8">
          クライアント<input class="s_c_textarea_seitono" type="text" id="client_sendno" size="6">　番に
          メッセージ　<input class="s_c_textarea" type="text" id="client_sendst" size="16">　を送る　
          <input type="checkbox" name="juyocheckbox" id="s_c_juyo" value="重要">重要
          <a class="s_c_send" href="#" onClick="client_send_message()">送信</a>
          <a class="s_c_cancel" href="#" onClick="printNo_cli_send()">キャンセル</a>
        </div>
      </div>
    </div>
  HTML;
}
?>