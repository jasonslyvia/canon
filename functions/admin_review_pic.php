<?php
/*
 *  管理员审核一张图片是否通过
 *
 *  @param {int} imageId 图像id（即post_id）
 *  @param {string} action 操作  pass:通过  delete:删除
 *  @return {json}
 */
header('Content-Type: application/json');
require('common.php');

//首先验证ajax请求的有效性
if (verify_ajax(array("imageId"),
                "post",
                true,
                "admin"))
{

    $image_id = $_POST['imageId'];
    $user_id = get_current_user_id();
    $action = $_POST['action'];

    if (!is_current_user_admin()) {
        send_result(true, "无权进行当前操作！");
    }
    else if (!get_post($image_id)) {
        send_result(true, "图片{$image_id}不存在！");
    }
    else{
        if ($action == "pass") {
            $result = wp_update_post(array('ID'=>$image_id,
                                           'post_status'=>'publish'));
            if ($result != 0) {
                send_result(false, '发布成功！');
            }
            else{
                send_result(true, "发布失败！");
            }
        }
        else if ($action == "delete") {
            $result = wp_update_post(array('ID'=>$image_id,
                                           'post_status'=>'trash'));
            //同时删除保存记录
            global $wpdb;
            $delete_result = $wpdb->delete('pic_save', array("image_id" => $image_id));
            if ($result != 0) {
                send_result(false, '删除成功！');
            }
            else{
                send_result(true, "删除失败！");
            }
        }
        else{
            send_result(true, "未知操作！");
        }
    }


}

?>