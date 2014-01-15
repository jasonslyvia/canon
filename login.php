<?php
/*
 *  登录界面
 */

require('functions/settings.php');
require(ABSPATH . '/wp-load.php');

//若是登录
if ($_POST) {

    //通过email获取登录用户名
    if (is_email($_POST['user_email'])) {
        $user_login = get_user_by('email', $_POST['user_email'])->user_login;
    }
    else{
        $user_login = $_POST['user_email'];
    }

    $creds = array("user_login" => $user_login,
                    "user_password" => $_POST['user_password'],
                    "remember" => true);
    //尝试登录
    $user = wp_signon($creds, false);
    if (is_wp_error($user)) {
        //登录失败
        $login_error = $user->get_error_message();
    }
    else{
        //登录成功后，若有下一步操作跳转到下一步url
        if (isset($_POST['next'])) {
            wp_redirect("{$_POST['next']}".
                "?url={$_POST['url']}".
                "&title={$_POST['title']}".
                "&referer={$_POST['referer']}");
            exit();
        }
        //否则跳转到用户主页
        else{
            wp_redirect('/profile/'.$user->ID);
            exit();
        }
    }
}

get_header();?>


<div id="luka">
  <div class="hamburger"></div>
  <p><a href="/"><?php echo get_bloginfo(); ?></a></p>
</div>

<div id="page">

  <div class="headerSpacer"></div>
  <div id="maincontent" class="center">

    <h1>欢迎回来</h1>
    <div id="panelLogin" class="panel"></div>
    <?php if (isset($login_error)): ?>
        <div class="error">
            <p><?php echo($login_error); ?></p>
        </div>
    <?php endif ?>
    <div class="wrapSignupForm">

      <h2>通过邮箱登录</h2>
      <form action="/login" method="post" accept-charset="utf-8" id="form_login">

        <label for="email">Email</label> <br />
        <input type="text" name="user_email" value="" /> <br />

        <label for="pass">密码</label> <br />
        <input type="password" name="user_password" value="" /> <br />

        <input type="submit" name="send" value="登录" />

<?php //若登录后有下一步操作，保存相关表单参数
if (isset($_GET['next'])) {
    foreach ($_GET as $key => $value) {
        echo "<input type='hidden' name='{$key}' value='{$value}' />";
    }
} ?>

      </form>
    </div>
    <div class="resetPassword">
      <p><a href="/forgetpassword">忘记密码</a> - <a href="/signup">注册</a></p>
    </div>

  </div>
</div>

<div id="loader">
</div>

<?php get_footer();?>