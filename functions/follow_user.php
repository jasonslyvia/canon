<?php
/*
 *  关注/取消关注用户
 *
 *  @param {int} targetId 被关注的用户id
 *  @param {string} optional action 若设为 unfollow 则取消关注，默认为关注
 *  @return {json}
 */
header('Content-Type: application/json');
require('common.php');

//首先验证ajax请求的有效性
if (verify_ajax(array("targetId"),
                "post",
                true,
                "user_pic_action"))
{
    $to_follow = $_POST["action"] == "unfollow" ? false : true;
    $follower = get_current_user_id();
    $followee = $_POST['targetId'];
    if (get_user_by('id', $followee) === false) {
        send_result(true, "被关注的用户{$followee}不存在");
    }

    //首先检查记录是否存在
    global $wpdb;
    $follow_row = $wpdb->get_row(
            $wpdb->prepare('SELECT relation_id as rid FROM user_relation
                            WHERE follower_id = %d
                            AND followee_id = %d',
                            $follower, $followee)
    );

    //若关注记录存在
    if ($follow_row ) {
        if ($to_follow) {
            send_result(false, "已关注该用户");
        }
        else{
            $deleted = $wpdb->delete('user_relation',
                                     array("relation_id" => $follow_row->rid));
            if ($deleted) {
                send_result(false, "取消关注成功");
            }
            else{
                send_result(false, "取消关注失败");
            }
        }
    }
    //若关注记录不存在，插入新纪录
    else{
        if ($to_follow) {
            $inserted = $wpdb->insert('user_relation',
                                    array("follower_id" => $follower,
                                          "followee_id" => $followee),
                                    array("%d", "%d"));
            //插入成功
            if ($inserted !== false) {
                send_result(false, "关注成功");
            }
            else{
                send_result(true, "关注失败");
            }
        }
        else{
            send_result(false, "未关注该用户");
        }
    }
}

?>