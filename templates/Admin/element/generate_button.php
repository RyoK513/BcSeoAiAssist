<div class="bca-seo-ai-assist" style="margin-top: 10px;">
  <button type="button"
    id="bc-seo-ai-assist-generate-btn"
    class="bca-btn"
    data-bca-btn-type="generate"
    data-bca-btn-size="sm">
    <i class="bca-icon--magic"></i> ディスクリプションを生成
  </button>
  <span id="bc-seo-ai-assist-status" style="margin-left: 10px; display: none;"></span>
</div>

<script>
  (function() {
    document.addEventListener('DOMContentLoaded', function() {
      const generateBtn = document.getElementById('bc-seo-ai-assist-generate-btn');
      const statusSpan = document.getElementById('bc-seo-ai-assist-status');

      if (!generateBtn) return;

      generateBtn.addEventListener('click', function(e) {
        e.preventDefault();

        // ボタンを無効化
        generateBtn.disabled = true;
        generateBtn.innerHTML = '<i class="bca-icon--spinner bca-icon--spin"></i> 生成中...';

        // ステータス表示
        statusSpan.style.display = 'inline';
        statusSpan.textContent = '生成中...';
        statusSpan.style.color = '#666';

        let body = '';
        const burgerContent = document.getElementById('bge-content');
        const textareaContent = document.querySelector('textarea[name*="detail"]') ||
          document.querySelector('textarea[name*="content"]');

        if (burgerContent) {
          // BurgerEditor の場合
          body = burgerContent.value || burgerContent.textContent;
        } else if (textareaContent) {
          // 通常のtextarea の場合
          body = textareaContent.value;
        }

        // キーワード取得
        const keywordsInput = document.querySelector('input[name="seo_meta[keywords]"]');
        const keywords = keywordsInput ? keywordsInput.value : '';

        // タイトル取得
        const titleInput = document.querySelector('input[name*="title"]');
        const title = titleInput ? titleInput.value : '';

        // リクエストデータ
        const requestData = {
          body: body,
          keywords: keywords,
          content_title: title,
          content_type: 'page'
        };

        // API呼び出し
        fetch('/baser/api/admin/bc-seo-ai-assist/generate/generate.json', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-Token': document.querySelector('input[name="_csrfToken"]').value
            },
            body: JSON.stringify(requestData)
          })
          .then(response => response.json())
          .then(data => {
            if (data.success && data.description) {
              // description に反映
              const descInput = document.querySelector('textarea[name="seo_meta[description]"]');
              if (descInput) {
                descInput.value = data.description;
              }

              // og_description に反映
              const ogDescInput = document.querySelector('textarea[name="seo_meta[og_description]"]');
              if (ogDescInput) {
                ogDescInput.value = data.description;
              }

              // 成功メッセージ
              statusSpan.textContent = '✓ ' + (data.message || 'SEOディスクリプションを生成しました');
              statusSpan.style.color = '#28a745';

              setTimeout(() => {
                statusSpan.style.display = 'none';
              }, 5000);
            } else {
              // エラーメッセージ
              const errorMsg = data.error || data.message || '生成に失敗しました';

              statusSpan.textContent = '✗ ' + errorMsg;
              statusSpan.style.color = '#dc3545';

              alert(errorMsg);
            }
          })
          .catch(error => {
            statusSpan.textContent = '✗ 通信エラーが発生しました';
            statusSpan.style.color = '#dc3545';
            alert('ディスクリプション生成中にエラーが発生しました。');
          })
          .finally(() => {
            // ボタンを再有効化
            generateBtn.disabled = false;
            generateBtn.innerHTML = '<i class="bca-icon--magic"></i> AI生成';
          });
      });
    });
  })();
</script>
