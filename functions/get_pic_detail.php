<?php
/*
 *  返回图像的详细信息
 *
 *  @param {int} imageId 图像id（即post_id）
 *  @param {int} userId 当前登录的用户id
 *  @return {json}
 */
header('Content-Type: application/json');

if (isset($_GET['imageId'])) {

    require_once('common.php');
    define('WP_USE_THEMES', false);
    require_once(ABSPATH.'wp-load.php');

    //获得图片详细信息
    $image_id = $_GET['imageId'];
    $post = get_post($image_id);
    $user_id = $_GET['userId'];
    //若用户id不存在则设为0，模拟用户未登录情况
    $user_id = get_user_by('id', $user_id) ? $user_id : 0;

    //作者id
    $author = $post->post_author;
    //作者昵称
    $author_name = get_userdata($author)->display_name;
    //图片地址
    $pic = IMAGE_PATH . $author . '/' . $post->post_content;
    //来源
    $referer = get_post_meta($image_id, 'referrer', true);
    if ($referer != '') {
        //获取refer的域名部分
        $short_referer = parse_url($referer)["host"];
    }
    else{
        $short_referer = '原创';
        $referer = '/profile/'.$author;
    }
    //标题
    $title = $post->post_name;
    $history = '';

    //heredoc 常数
    $AVATAR = AVATAR;

    //喜欢与保存数据
    require_once('get_pic_save_like.php');
    $op_result = get_pic_save_like($image_id, $user_id);
    $like_arr = $op_result["like"];
    $save_arr = $op_result["save"];
    $save_op = $save_arr['class_name'] == '' ? '保存': '编辑';



    //渲染内容
    $html = <<<html
<div class="image" style="background-color: #000000">
    <div class="arrowLeft">
    </div>
    <div class="arrowRight">
    </div>
    <div class="layoutButton">
    </div>
    <div class="wrap">
        <div class="imageWrap">
            <a href="/{$title}" target="_blank">
                <img src="$pic"
                alt="{$title}" width="{$width}" height="{$height}"
                data-width="{$width}" data-height="{$height}"/>
            </a>
        </div>
    </div>
</div>
<div class="details">
    <div class="wrap">
        <div id="lightboxDetails" class="auth">
            <div class="header">
                <div class="options clearfix">
                    <p id="tagOptions">
                        <a href="#" class="saveButton{$save_arr['class_name']}"
                            id="addImageButton"
                            data-id="{$image_id}" title="保存这张图片"><em></em> <span>{$save_op}</span>
                        </a>
                        <a href="#" class="likeButton{$like_arr['class_name']}"
                            id="likeImageButton"
                            data-id="{$image_id}" title="喜欢这张图片"><em></em> <span>喜欢</span>
                        </a>
                        <a href="#" class="shareButton" id="shareImageButton"
                            title="分享给你的好友"
                            data-id="{$image_id}"><em></em><span>分享</span>
                        </a>
                    </p>
                </div>
            </div>
            <div class="lower">
                <div class="similar">
                    <ul class="clearfix">
html;

    $query = new WP_Query("author={$author}&posts_per_page=9");
    while ($query->have_posts()) {
        $query->the_post();
        $html .= "<li>".
            "<a href='" . get_permalink() . "' data-id='" . get_the_ID() . "'>".
                "<img src='".get_thumb(get_the_content(), $author, true)."'
                      width='100'  height='75' />".
            "</a>";
    }

    $html .= <<<html
                    </ul>
                </div>
                <div class="info">
                    <div class="finder clearfix">
                        <div class="userPic">
                            <a href="/profile/{$author}">
                                <img src="{$AVATAR}default_avatar_small.png"
                                    width="40" height="40" alt=""/>
                            </a>
                        </div>
                        <h4><a href="/profile/{$author}" class="userLink">{$author_name}</a><br/>
                        来自 <a href="{$referer}">{$short_referer}</a></h4>
                        <button type="button" class="follow blue active" data-type="1" data-id="7650">已关注</button>
                    </div>
                    <div class="stats saves clearfix">
                    {$like_arr["sample_html"]}
                    {$save_arr["sample_html"]}
                    </div>
                    <div class="activity clearfix">
                        <div id="comments" class="empty">
                            <div class="comments">
                            </div>
                        </div>
                        <div id="commentForm" data-imageid="{$image_id}">
                            <div class="userPic">
                                <img src="{$AVATAR}/default_avatar_small.png" width="30" height="30" alt=""/>
                            </div>
                            <textarea name="comment" placeholder="说点儿什么">说点儿什么</textarea>
                        </div>
                        <div class="group clearfix">
                            <a href="http://www.wookmark.com/profile/aerynn" class="userPic">
                            <img src="http://www.wookmark.com/images/profile/30/a_kiss_in_the_dark_by_hiritai-d4rrx82_2.jpg" width="30" height="30" alt=""/></a>
                            <p>
                                <a href="http://www.wookmark.com/profile/aerynn">Aerynn</a> 保存了这张图</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
html;

    $width = get_post_meta($image_id, 'width', true);
    $height = get_post_meta($image_id, 'height', true);

    send_result(null,null, array("imageId" => $image_id,
                                 "width" => $width,
                                 "height" => $height,
                                 "url" => $pic,
                                 "referer" => $referer,
                                 "html" => $html,
                                 "history" => ""),
                true);
}
else{
    send_result(true, "未知错误");
}


?>