<?php
/*
 *  清理垃圾信息
 *
 *  @return {json}
 */
header('Content-Type: application/json');
require('common.php');

global $wpdb;

if (!is_current_user_admin()) {
    send_result(true, "无权进行当前操作！");
}
else{
    $images = $wpdb->get_results("SELECT ID, post_content c FROM wp_posts", ARRAY_A);
    $evil_images = array();
    if (count($images)) {
        foreach ($images as $image) {
            //若最后不是.jpg或.png等图片格式结尾
            if (!preg_match('/\.[a-zA-Z]{3,4}$/', $image['c'])) {
                //若开头还是正常的内容
                if (preg_match('/^([a-z0-9]+\.[a-zA-Z]{3,4})/', $image['c'], $matches)) {
                    array_push($evil_images, array("id" => $image["ID"],
                                                   "c" => $matches[1]));
                }
            }
        }
    }

    foreach ($evil_images as $image) {
        $wpdb->update("wp_posts",
                      array("post_content" => $image["c"]),
                      array("ID" => $image["id"]),
                      "%s",
                      "%d");
    }

    send_result(false, "共清理 ".count($evil_images)." 条垃圾信息！");
}

?>