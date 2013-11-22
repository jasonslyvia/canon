<?php
/*
 *  添加一张图片到数据库
 *
 *  @param {string} filename 图片文件名
 *  @param {string} nonce AJAX凭证
 *  @param {int} userId 用户id
 *  @param {int} width 图片宽
 *  @param {int} height 图片高
 *  @param {string} optional referrer 图片图片来源
 *  @return {json}
 */
header('Content-Type: application/json');
require('common.php');

if (verify_ajax(array("filename", "userId"), "post", true, "upload_pic")) {

    $filename = $_POST['filename'];
    $userId = $_POST['userId'];
    //添加新文章
    $post_name = basename($filename);
    $post_id = wp_insert_post(array("post_author" => $userId,
                                    "post_name" => substr(md5(time().$post_name),
                                                          5,
                                                          26),
                                    "post_content" => $filename,
                                    "post_status" => 'publish'));
    if (!$post_id) {
        send_result(true, "无法创建新内容");
    }
    else{
        //增加文章信息
        add_post_meta($post_id, 'like_count', 0);
        add_post_meta($post_id, 'save_count', 1);
        add_post_meta($post_id, 'width', $_POST['width']);
        add_post_meta($post_id, 'height', $_POST['height']);
        add_post_meta($post_id, 'referrer', $_POST['referrer']);

        global $wpdb;
        $save_record = $wpdb->insert('pic_save',
                                     array("pic_id" => $post_id,
                                           "user_id" => $userId));

        send_result(false, "照片发布成功！", array("postId" => $post_id));
    }
}


 ?>