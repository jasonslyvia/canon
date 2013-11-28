<?php
/*
 *  更新用户个人信息
 *
 *  @param {int} userId 用户id
 *  @param {string} name 用户昵称（即wordpress中的display_name）
 *  @param {string} email 用户邮箱（即 user_email）
 *  @param {string} password 用户旧密码
 *  @param {string} newPassword 用户新密码
 *  @param {string} optional target 需要更新的内容，info 或 password，默认为 info
 *  @return {json}
 */
header('Content-Type: application/json');
require('common.php');

//更新密码
if ($_POST['target'] == "password") {
    $required = array("password", "newPassword", "userId");
    if (verify_ajax($required, "post", true, "user_info_action")) {
        //验证新密码是否符合规则
        $password = $_POST['password'];
        $new_password = $_POST['newPassword'];
        if (strlen($new_password) < 6 || strlen($new_password) > 16) {
            send_result(true, "密码有效长度为 6 - 16 位");
        }

        define('WP_USE_THEMES', false);
        require_once(ABSPATH.'wp-load.php');

        //找到对应的用户
        $user_id = $_POST['userId'];
        $user = get_user_by('id', $user_id);
        if (!wp_check_password($password, $user->data->user_pass, $user_id)) {
            send_result(true, "原密码错误");
        }
        else{
            wp_set_password($new_password, $user_id);
            send_result(false, "密码更新成功！");
        }
    }
}
//更新基本信息
else{
    $required = array("userId", "name", "email");
    //首先验证ajax请求的有效性
    if (verify_ajax($required, "post", true, "user_info_action")) {

        define('WP_USE_THEMES', false);
        require_once(ABSPATH.'wp-load.php');

        //找到对应的用户
        $user_id = $_POST['userId'];
        $user = get_user_by('id', $user_id);
        if (!$user) {
            send_result(true, "未找到用户id为{$user_id}的用户");
        }

        $name = $_POST['name'];
        //验证数据有效性
        if (empty($name)) {
            send_result(true, "请设置您的昵称");
        }
        else{
            if (mb_strlen($name, "utf8") < 2 &&
                strlen($name, "utf8") > 10) {
                send_result(true, "昵称有效长度为 2 - 10 个字符");
            }
            else if (!preg_match('/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]+$/u', $name)) {
                send_result(true, "昵称可包含的有效字符为大小写字母、数字或汉字");
            }
        }

        $email = $_POST['email'];
        if (empty($email)) {
            send_result(true, "请设置您的邮件地址");
        }
        else if(!preg_match('/^\w+@\w+\.\w{2,4}$/', $email)){
            send_result(true, "请输入有效的邮件地址");
        }

        //数据验证通过后更新用户数据
        wp_update_user(array("ID" => $user_id,
                             "display_name" => $name,
                             "user_email" => $email));
        send_result(false, "信息更新成功！");
    }
}


?>