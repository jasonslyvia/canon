<?php
/*
 *  添加一位用户（微博登录）
 *
 *  @param {string} op register || login
 *  @param {string} display_name 昵称
 *  @param {string} nonce AJAX凭证
 *  @param {string} user_email 邮件（id@source.com，如1234@sina.com）
 *  @param {string} avatar 头像（大）
 *  @param {string} avatar_small 头像（小）
 *  @param {string} create_date 帐号创建时间(yyyymmdd)
 *  @param {string} id 用户id
 *  @return {json}
 */
header('Content-Type: application/json');
require('common.php');

$display_name = $_POST['display_name'];
$user_email = $_POST['user_email'];
$user_password = md5($_POST['create_date'].$_POST['id']);
$nonce = $_POST['nonce'];
$avatar = $_POST['avatar'];
$avatar_small = $_POST['avatar_small'];

if (!wp_verify_nonce($nonce, 'social')) {
    send_result(true, "凭证错误！");
}

if ($_POST['op'] === "register") {
    $is_error = false;
    if (empty($display_name)) {
        $signup_error = "请设置您的昵称";
    }
    else{
        preg_replace('/[^a-zA-Z0-9\x{4e00}-\x{9fa5}]/u', '', $display_name);
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
    else if (strlen($user_password) < 6) {
        $signup_error .= "<br />密码的有效长度为 6 - 16 位";
    }

    //若用户输入无误
    if (!isset($signup_error)) {
        $user_id = username_exists($user_email);
        //若用户不存在则创建用户
        if (!$user_id && email_exists($user_email) == false) {
            $user_id = wp_create_user($user_email, $user_password, $user_email);

            //设置用户昵称
            wp_update_user(array("ID" => $user_id,
                                 "display_name" => $display_name));
            add_user_meta($user_id, 'avatar', $avatar);
            add_user_meta($user_id, 'avatar_small', $avatar_small);
        }
        _login($user_email, $user_password);
    }
    else{
        send_result(true, $signup_error);
    }
}
else if($_POST['op'] === "login"){
    _login($user_email, $user_password);
}


function _login($user_email, $user_password){
    //尝试用新创建的用户登录
    $creds = array("user_login" => $user_email,
                    "user_password" => $user_password,
                    "remember" => true);
    //尝试登录
    $user = wp_signon($creds, false);
    if (is_wp_error($user)) {
        //登录失败
        $signup_error = $user->get_error_message();
        send_result(true, $signup_error);
    }
    else{
        //登录成功
        send_result(false);
    }
}
?>