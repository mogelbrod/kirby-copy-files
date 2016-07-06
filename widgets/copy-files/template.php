<div class="copy-files-widget">
<?php
  $form = new Kirby\Panel\Form([
    'source' => [
      'type' => 'page',
      'required' => true,
      'label' => 'Source',
      'placeholder' => 'Page/Folder/File URL',
    ],
    'dest' => [
      'type' => 'page',
      'required' => true,
      'label' => 'Destination (must not exist)',
      'placeholder' => 'Destination URL',
    ]
  ], []);
  $form->on('post', function() {}); // append csrf
  $form->action('copy-files/api/copy');
  $form->attr('data-autosubmit', 'native');
  $form->buttons->submit->val('Copy');
  $form->buttons->cancel = '';
  echo $form;
?>
</div>

<script>
  $('.copy-files-widget form').submit(function(evt) {
    evt.preventDefault()
    var $form = $(this)
    var $buttons = $form.find('.fieldset.buttons')
    $buttons.find('.outcome').remove()
    function showMessage(msg, success) {
      var $msg = $('<p class="outcome" />').text(msg)
      $msg.css({color: success ? '#8dae28' : '#b3000a', float: 'left'})
      $msg.prependTo($buttons)
    }
    $.ajax($form.attr('action'), {
      method: ($form.attr('method') || 'post').toLowerCase(),
      data: $form.serialize(),
      success: function(data) {
        if (data && data.data && data.data.url) {
          window.location.href = data.data.url
        } else {
          $form[0].reset()
          $form.find('input[name="source"]').focus()
          showMessage(data.message, true)
        }
      },
      error: function(res) {
        showMessage(res.responseJSON ? res.responseJSON.message : res.responseText, false)
      }
    })
  })
</script>

