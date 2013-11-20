<?php

/*
 * Template Name: upload
 */

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

    <h1>上传新照片</h1>
    <div id="panelLogin" class="panel"></div>

    <div id="uploadDiv" class="wrapSignupForm">
      <h2>选择本地文件上传</h2>

      <input type="file" name="file_upload" id="file_upload" />
      <label for="file_upload">点击上传按钮从你的计算机上传照片</label>
      <input type="hidden" id="filename" />
      <input type="hidden" id="picWidth" />
      <input type="hidden" id="picHeight" />

    </div>

    <div id="crawlDiv" class="wrapSignupForm">

      <h2>或直接获取网络照片</h2>

    </div>


  </div>
</div>

<div id="loader">
</div>


 <?php get_footer(); ?>