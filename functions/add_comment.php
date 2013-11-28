<?php
/*
 *  发布一条新评论
 *
 *  @param {int} imageId 图像id（即post_id）
 *  @return {json}
 */
header('Content-Type: application/json');
require('common.php');

//首先验证ajax请求的有效性
if(verify_ajax(array("imageId", "comment"),
               'post',
               true,
               'user_pic_action'))
{
    $image_id = $_POST['imageId'];
    $user_id = get_current_user_id();
    $user_name = get_userdata($user_id)->display_name;
    $user_avatar = get_user_meta($user_id, 'avatar_small', true);
    $comment = $_POST['comment'];

    if (strlen($comment) < 2) {
        send_result(true, "评论最小长度为 3 个字符");
    }

    $escaped_comment = esc_html($comment);
    $time = current_time('mysql');
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $ip = $_SERVER['REMOTE_ADDR'];

    $data = array(
        'comment_post_ID' => $image_id,
        'comment_content' => $comment,
        'user_id' => $user_id,
        'comment_author_IP' => $ip,
        'comment_agent' => $agent,
        'comment_date' => $time,
        'comment_approved' => 1,
    );

    $comment_id = wp_insert_comment($data);

    $URL = URL;
    $AVATAR = AVATAR;
$html = <<<html
<div id="comment_{$comment_id}" class="comment clearfix" data-imageid="{$image_id}">
    <div class="userPic">
        <a href="/profile/{$user_id}">
            <img src="{$AVATAR}{$user_avatar}"
                 width="30" height="30" alt=""/>
        </a>
    </div>
    <p>
        <a href="/profile/{$user_id}">
            {$user_name}</a> &ndash; {$comment}<br />
    </p>
    <!--TODO 暂不实现编辑评论功能
    <div class="editComment">
        <a href="http://www.wookmark.com/editnote?ref=image&commentId=34501">
            <img src="{$URL}/img/pencil.png"
                 width="16" height="16" alt="编辑评论"/>
        </a>
    </div>-->
</div>
html;
    send_result(false,
                "评论发表成功",
                array("html" => $html));

}

?>