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

if ($_POST) {

    $wp_load = '/Code/Develop Environment/Wordpress/wp-load.php';
    while (!realpath($wp_load)) {
        $wp_load = '../'.$wp_load;
    }
    require_once($wp_load);

    //首先验证权限
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'upload_pic')) {
        echo(json_encode(array("error" => true,
                               "message" => "权限验证失败")));
    }
    else{
        $filename = $_POST['filename'];
        if (!$filename) {
            echo(json_encode(array("error" => true,
                               "message" => "未设置文件名")));
            exit();
        }

        $userId = $_POST['userId'];
        if (!$userId) {
            echo(json_encode(array("error" => true,
                               "message" => "未设置用户ID")));
            exit();
        }

        //添加新文章
        $post_name = basename($filename);
        $post_id = wp_insert_post(array("post_author" => $userId,
                                        "post_name" => $post_name,
                                        "post_content" => $filename,
                                        "post_status" => 'publish'));
        if (!$post_id) {
            echo(json_encode(array("error" => true,
                               "message" => "无法创建新内容")));
            exit();
        }
        else{
            //增加文章信息
            add_post_meta($post_id, 'like_count', 0);
            add_post_meta($post_id, 'save_count', 1);
            add_post_meta($post_id, 'width', $_POST['width']);
            add_post_meta($post_id, 'height', $_POST['height']);

            echo(json_encode(array("error" => false,
                                    "message" => "照片发布成功！",
                                    "postId" => $post_id)));
        }
    }
}
else{
    echo json_encode(array("error" => true,
                "message" => "未知错误"));
    exit();
}


 ?>