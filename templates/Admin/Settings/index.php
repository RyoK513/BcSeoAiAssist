<?php echo $this->BcAdminForm->create($siteConfig, ['novalidate' => true]) ?>

<div class="section">
  <table class="form-table bca-form-table">
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('bc_seo_ai_assist_api_key', 'OpenAI APIキー') ?>
        <span class="bca-label" data-bca-label-type="required">必須</span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('bc_seo_ai_assist_api_key', [
          'type' => 'password',
          'size' => 60,
          'placeholder' => 'sk-...',
          'value' => $siteConfig->bc_seo_ai_assist_api_key ?? '',
          'autocomplete' => 'off'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          OpenAI APIキーを入力してください。<br>
          APIキーは<a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener">OpenAI Platform</a>で取得できます。
        </div>
        <?php echo $this->BcAdminForm->error('bc_seo_ai_assist_api_key') ?>
      </td>
    </tr>
  </table>
</div>

<div class="submit bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(
      __d('baser_core', '保存'),
      ['type' => 'submit', 'class' => 'button bca-btn bca-actions__item', 'data-bca-btn-type' => 'save', 'data-bca-btn-size' => 'lg', 'data-bca-btn-width' => 'lg', 'id' => 'BtnSave']
    ) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>