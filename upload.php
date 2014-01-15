<?php

require('functions/settings.php');
require(ABSPATH . '/wp-load.php');

if (!is_user_logged_in()) {
    wp_redirect('/');
    exit();
}

get_header();

?>

<script type="text/javascript">
  var nonce = '<?php echo wp_create_nonce("upload_pic_".get_current_user_id()); ?>';
</script>

<div id="luka">
  <div class="hamburger"> </div>
  <p> <a href="/"> <?php echo get_bloginfo(); ?> </a> </p>
</div>

<div id="page">

  <div class="headerSpacer"> </div>
  <div id="maincontent" class="center" style="width:700px;">

    <h1>添加新内容</h1>
    <div id="panelLogin" class="panel"></div>

    <div id="uploadDiv" class="wrapSignupForm">
      <h2>选择本地文件上传</h2>

      <input type="file" name="file_upload" id="file_upload" />
      <label for="file_upload">点击上传按钮从你的计算机上传照片</label>
      <input type="hidden" id="filename" />
      <input type="hidden" id="picWidth" />
      <input type="hidden" id="picHeight" />

    </div>


<div id="category" style="display:none">
  <?php wp_dropdown_categories(array("hide_empty" => false)); ?>
</div>

    <div id="crawlDiv" class="wrapSignupForm">
      <h2>或直接获取网络照片</h2>
      <label for="url">请将图片地址或图片所在的网页地址粘贴至此</label>
      <input type="text" id="url" />
      <a class="actionButton" href="javascript:;" id="remoteImgBtn">一键获取</a>
      <div class="preview-small-container clearfix">
        <label>请选择要添加的照片</label>
      </div>
    </div>

  </div>
</div>

<div id="loader">
</div>


 <?php get_footer(); ?>