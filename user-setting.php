<?php

require('functions/settings.php');
require(ABSPATH . '/wp-load.php');

if (!is_user_logged_in()) {
    wp_redirect('/');
    exit();
}
else{

    $AVATAR = '/wp-content/themes/canon/uploads/avatar/';
    //回填当前用户的信息
    $uid = get_current_user_id();
    $user = get_user_by('id', $uid);
    $name = $user->display_name;
    $email = $user->user_email;
    $avatar = get_user_meta($uid, 'avatar', true);
    $avatar_small = get_user_meta($uid, 'avatar_small', true);
}

 get_header();

 ?>

<script type="text/javascript">
  var nonce = '<?php echo wp_create_nonce("user_info_action_".get_current_user_id()); ?>';
</script>

<div id="luka">
  <div class="hamburger"> </div>
  <p> <a href="/"> 个人资料设置 </a> </p>
</div>

<div id="page">

  <div class="headerSpacer"> </div>
  <div id="maincontent" class="settings" style="width:700px;">

    <h1>修改个人信息</h1>
    <div id="panelLogin" class="panel"></div>

    <div id="uploadDiv" class="wrapSignupForm">
      <h2>头像</h2>
      <label>当前头像</label>
      <div class="avatar-preview">
        <img src="<?php echo $AVATAR.$avatar; ?>" class="large"
             width="200" height="200"/>
        <img src="<?php echo $AVATAR.$avatar_small; ?>" class="small"
             width="45" height="45" />
      </div>
      <input type="file" name="file_upload" id="file_upload" />
      <label for="file_upload">点击上传按钮从你的计算机上传头像</label>
      <input type="hidden" id="filename" />

    </div>

    <div id="baseinfoDiv" class="wrapSignupForm">

      <h2>基本信息</h2>
      <label for="displayName">昵称</label>
      <input type="text" name="display_name" placeholder="设置您的昵称"
             id="displayName" value="<?php echo $name; ?>" />
      <label for="userEmail">邮箱</label>
      <input type="text" name="user_email" placeholder="设置您的邮箱地址"
             id="userEmail" value="<?php echo $email; ?>" />

      <input id="infoSubmitBtn" type="button"
             class="actionButton blueButton" value="确认" />

    </div>

    <div id="passwordDiv" class="wrapSignupForm">

      <h2>修改密码</h2>

      <label for="currentPassword">当前密码</label>
      <input type="password" name="display_name" placeholder="输入您当前的密码"
             id="currentPassword" />
      <label for="newPassword">新密码</label>
      <input type="password" name="password" placeholder="输入您的新密码"
             id="newPassword" />

      <label for="renewPassword">再次输入新密码</label>
      <input type="password" name="repassword" placeholder="再次输入您的新密码"
             id="renewPassword" />

      <input type="button" class="actionButton" value="确认"
             id="changePasswordBtn" />
    </div>

  </div>
</div>

<div id="loader">
</div>


 <?php get_footer(); ?>