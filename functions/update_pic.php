<?php
/*
 *  更新图片信息
 *
 *  @param {int} pid 图片id
 *  @param {string} nonce AJAX凭证
 *  @param {string} optional referrer 图片图片来源
 *  @param {string} optional title 图片标题
 *  @param {string} optional category 图片所属主题
 *  @return {json}
 */
header('Content-Type: application/json');
require('common.php');
define(DEFAULT_WIDTH, 200);


if (verify_ajax(array("pid"), "post", true, "upload_pic")) {

    $pid = $_POST['pid'];
    $userId = get_current_user_id();
    $post = get_post($pid);
    if ($post->post_author != $userId) {
        send_result(true, "权限不足无法编辑该图片！");
    }

    $title = $_POST['title'];
    $category = $_POST['category'];
    $referrer = $_POST['referrer'];

    $post = array("ID" => $pid,
                  "post_title" => $title,
                  "post_category" => array($category));
    $update_result = wp_update_post($post);
    update_post_meta($pid, 'referrer', $referrer);

    if ($update_result != 0) {
        send_result(false, "更新成功", array("pid" => $pid));
    }
    else{
        send_result(true, "更新失败");
    }
}



 ?>