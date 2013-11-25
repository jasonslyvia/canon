<?php

/*
 * Template Name: signup
 */

//登录用户直接转向个人主页
if (is_user_logged_in()) {
    wp_redirect('/profile/'.get_current_user_id());
    exit();
}

//若是注册
if ($_POST) {

    //进行基本的数据校验
    $display_name = $_POST['display_name'];
    $user_email = $_POST['user_email'];
    $user_password = $_POST['user_password'];


    $is_error = false;

    if (empty($display_name)) {
        $signup_error = "请设置您的昵称";
    }
    else{
        if (mb_strlen($display_name, "utf8") < 2 &&
            strlen($display_name, "utf8") > 10) {
            $signup_error = "昵称有效长度为 2 - 10 个字符";
        }
        else if (!preg_match('/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]+$/u', $display_name)) {
            $signup_error = "昵称可包含的有效字符为大小写字母、数字或汉字";
        }
    }

    if (empty($user_email)) {
        $signup_error .= "<br />请设置您的邮件地址";
    }
    else if(!preg_match('/^\w+@\w+\.\w{2,4}$/', $user_email)){
        $signup_error .= "<br />请输入有效的邮件地址";
    }

    if (empty($user_password)) {
        $signup_error .= "<br />请设置您的登录密码";
    }
    else if (strlen($user_password) < 6 || strlen($user_password) > 16) {
        $signup_error .= "<br />密码的有效长度为 6 - 16 位";
    }

    //若用户输入无误
    if (!isset($signup_error)) {
        //创建用户
        $user_id = username_exists($user_email);
        if (!$user_id && email_exists($user_email) == false) {
            $user_id = wp_create_user($user_email, $user_password, $user_email);

            //设置用户昵称
            wp_update_user(array("ID" => $user_id,
                                 "display_name" => $display_name));
            add_user_meta($user_id, 'avatar', 'default_avatar.png');
            add_user_meta($user_id, 'avatar_small', 'default_avatar_small.png');

            //尝试用新创建的用户登录
            $creds = array("user_login" => $user_email,
                            "user_password" => $user_password,
                            "remember" => true);
            //尝试登录
            $user = wp_signon($creds, false);
            if (is_wp_error($user)) {
                //登录失败
                $signup_error = $user->get_error_message();
            }
            else{
                //登录成功
                wp_redirect('/profile/'.$user->ID);
                exit();
            }
        }
        else{
            $signup_error = "用户 {$user_email} 已存在&nbsp;&nbsp;&nbsp;&nbsp;<a href='/login'>现在登录</a>";
        }
    }
}



//若是忘记密码
if ($_GET['forget'] == 1) {
    //
}

 get_header();?>


<div id="luka">
  <div class="hamburger"></div>
  <p><a href="/"><?php echo get_bloginfo(); ?></a></p>
</div>

<div id="page">

  <div class="headerSpacer"></div>

  <div id="maincontent" class="center">

    <h1>欢迎加入<?php echo get_bloginfo(); ?></h1>

    <?php if (isset($signup_error)): ?>
        <div class="error">
            <p><?php echo($signup_error); ?></p>
        </div>
    <?php endif ?>

    <div class="wrapSignupForm">

      <form action="/signup" method="post" accept-charset="utf-8" id="form_signup">

        <label for="displayName">昵称</label>
        <input type="text" name="display_name" placeholder="设置您的昵称"
                id="displayName"
                value="<?php if(isset($display_name)) echo($display_name); ?>" />

        <label for="userEmail">Email</label>
        <input type="text" name="user_email"  placeholder="电子邮件地址"
                id="userEmail"
                value="<?php if(isset($user_email)) echo($user_email); ?>"  />

        <label for="passWord">密码</label>
        <input type="password" name="user_password" placeholder="设置您的密码"
                id="passWord" />

        <input type="submit" name="send" class="actionButton blueButton signupButton"
                value="马上体验<?php echo get_bloginfo(); ?>" />

      </form>
    </div>

    <div class="social clearfix signup-social">
        <h2>或使用社交网络帐号注册</h2>
        <div class="weibo-login-div">
            <a href="#" class="actionButton weibo-login">新浪微博</a>
        </div>
        <div class="qq-login-div">
            <a href="#" class="actionButton qq-login">腾讯微博</a>
        </div>
    </div>

  </div>
</div>

<div id="loader">
</div>

<?php get_footer();?>