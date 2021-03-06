<?php
/*
 *  喜欢/取消喜欢一张图片
 *
 *  @param {int} imageId 图像id（即post_id）
 *  @param {int} userId 用户id
 *  @return {json}
 */
header('Content-Type: application/json');
require('common.php');

//首先验证ajax请求的有效性
if (verify_ajax(array("imageId"),
                "post",
                true,
                "user_pic_action"))
{

    $image_id = $_POST['imageId'];
    $user_id = get_current_user_id();

    //首先检查记录是否存在
    global $wpdb;
    $like_row = $wpdb->get_row(
            $wpdb->prepare('SELECT like_id FROM pic_like
                            WHERE pic_id = %d
                            AND user_id = %d',
                            $image_id, $user_id)
    );

    //若喜欢记录存在，删除
    if ($like_row ) {
        $deleted = $wpdb->delete('pic_like',
                                 array("like_id" => $like_row->like_id));
        if ($deleted) {

            $like_count = get_post_meta($image_id, 'like_count', true);
            update_post_meta($image_id, 'like_count', --$like_count);

            send_result(false, "取消喜欢成功");
        }
        else{
            send_result(false, "取消喜欢失败");
        }
    }
    //若喜欢记录不存在，插入新纪录
    else{
        $inserted = $wpdb->insert('pic_like',
                                array("pic_id" => $image_id,
                                      "user_id" => $user_id),
                                array("%d", "%d"));
        //插入成功
        if ($inserted !== false) {

            $like_count = get_post_meta($image_id, 'like_count', true);
            update_post_meta($image_id, 'like_count', ++$like_count);

            send_result(false, "喜欢成功");
        }
        else{
            send_result(true, "喜欢失败");
        }
    }
}

?>