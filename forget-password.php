<?php
/*
 *  重置密码，通过Email地址
 */

require('functions/settings.php');
require(ABSPATH . '/wp-load.php');


//若是提交重置密码的请求
if ($_POST['token']) {
    $token = $_POST['token'];
    $password = $_POST['user_password'];
    try {
        //首先验证token和password的合法性
        if (!$token) {
            throw new Exception("无法获取重置密码凭证！", 1);
        }
        if (!$password) {
            throw new Exception("请设置您的新密码！", 1);
        }

        global $wpdb;
        $token_result = $wpdb->get_row("SELECT * FROM reset_password WHERE token = '{$token}'");
        if ($token_result == null) {
            throw new Exception("重置请求验证失败，请重新提交重置密码的请求！", 1);
        }
        else if ($token_result->reseted_flag == '1') {
           throw new Exception("重置请求已失效，请重新提交重置密码的请求！", 1);
        }

        //尝试重置密码
        $email = $token_result->email;
        $user = get_user_by('email', $email);
        $uid = $user->ID;

        //重置密码
        wp_set_password($password, $uid);
        //更新重置密码表
        $wpdb->update("reset_password",
                      array("reseted_flag" => 1),
                      array("reset_id" => $token_result->reset_id));

        $reset_message = "密码更新成功！马上<a href='/login'>登录</a>";
    } catch (Exception $e) {
        $reset_message = $e->getMessage();
    }
}
//若是设置密码
else if ($_GET['token']) {
    $token = $_GET['token'];
    $mode = "reset";

    global $wpdb;
    $token_result = $wpdb->get_row("SELECT * FROM reset_password WHERE token = '{$token}'");
    if ($token_result == null) {
        $reset_message = "重置请求验证失败，请重新提交重置密码的请求！";
    }
    else if ($token_result->reseted_flag == '1') {
        $reset_message = "重置请求已失效，请重新提交重置密码的请求！";
    }
}
else if($_POST['user_email']){
    //若是提交发送重置密码邮件的请求
    $email = $_POST['user_email'];
    try {
        //通过email获取登录用户名
        if (!is_email($email)) {
            throw new Exception("Email地址不合法！", 1);
        }

        $user = get_user_by('email', $email);
        if (!$user) {
            throw new Exception("该Email地址并未被注册！", 1);
        }

        //若Email合法且被注册，则给该邮箱发送重置密码的链接
        $token = md5(substr(time().$email, -10));
        //站点URL
        $home = get_home_url();
        //站点名称
        $sender = get_bloginfo();
        $message = "重置您在{$sender}的密码，请点击<a href='{$home}/forgetpassword?token={$token}'>这里</a>。";
        $subject = "重置密码 {$sender}";

        //设置邮件的发件人姓名和邮箱地址
        function cdx_from_email() {
            return "no-reply@{$home}.com";
        }
        add_filter( 'wp_mail_from', 'cdx_from_email' );
        function cdx_from_name() {
            return $sender;
        }
        add_filter( 'wp_mail_from_name', 'cdx_from_name' );

        $email_result = wp_mail($email, $subject, $message);
        if ($email_result) {
            $reset_message = "密码重置链接已经发送到您的邮箱，请查收。";
        }
        else{
            $reset_message = "密码重置邮件发送失败！";
        }

        //将重置记录存入数据库
        global $wpdb;
        $wpdb->insert("reset_password", array("token" => $token,
                                              "email" => $email));
    } catch (Exception $e) {
        $reset_message = $e->getMessage();
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

    <h1>找回密码</h1>
    <div id="panelLogin" class="panel"></div>

    <?php if (isset($reset_message)): ?>
        <div class="error">
            <p><?php echo($reset_message); ?></p>
        </div>
    <?php endif ?>

<?php if ($mode != "reset"): //显示输入找回email界面 ?>
    <div class="wrapSignupForm">
      <form action="/forgetpassword" method="post" id="form_login">

        <label for="email">Email</label> <br />
        <input type="text" name="user_email" id="email"
               value="<?php echo $email; ?>" /> <br />

        <input type="submit" value="找回" />
      </form>
    </div>
<?php else: //显示输入新密码页面?>
    <div class="wrapSignupForm">
      <form action="/forgetpassword" method="post" id="form_login">
        <label for="password">新密码</label> <br />
        <input type="text" name="user_password" id="password" /> <br />
        <input type="hidden" name="token" value="<?php echo $token; ?>" />
        <input type="submit" value="确认" />
      </form>
    </div>
<?php endif ?>
    <div class="resetPassword">
      <p><a href="/login">登录</a> - <a href="/signup">注册</a></p>
    </div>
  </div>
</div>

<div id="loader">
</div>

<?php get_footer();?>