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

    //作者id
    $author = $post->post_author;
    //作者昵称
    $author_name = get_userdata($author)->display_name;
    //图片地址
    $pic = IMAGE_PATH . $author . '/' . $post->post_content;

    $referer = get_post_meta($image_id, 'referrer', true);
    if ($referer != '') {
        //获取refer的域名部分
        $short_referer = parse_url($referer)["host"];
    }
    else{
        $short_referer = '原创';
        $referer = '/profile/'.$author;
    }

    $title = $post->post_name;
    $history = '';

    //heredoc 常数
    $AVATAR = AVATAR;

    global $wpdb;
    //当前用户是否喜欢该图片
    $current_user_like = $wpdb->get_var(
            $wpdb->prepare('
                    SELECT count(*) FROM pic_like
                    WHERE pic_id = %d AND user_id = %d
                ', $image_id, $user_id)
        );
    $current_user_like = $current_user_like == 0 ? '' : ' active';

    //获取保存和喜欢的数据
    $like_row = $wpdb->get_results(
        $wpdb->prepare('
            SELECT * FROM pic_like
            WHERE pic_id = %d
            ORDER BY time DESC
        ', $image_id)
    );

    if ($like_row) {
        $like_count = count($like_row);

        $like_user_a = $like_row[0]->user_id;
        $like_user_a_name = get_userdata($like_user_a)->display_name;

        $like_user_b = $like_row[1]->user_id;
        $like_user_b_name = get_userdata($like_user_b)->display_name;

        $like_record_arr = array();
        if ($like_user_a) {
            array_push($like_record_arr, array("id" => $like_user_a,
                                             "name" => $like_user_a_name
                                        ));
        }
        if ($like_user_b) {
            array_push($like_record_arr, array("id" => $like_user_b,
                                               "name" => $like_user_b_name
                                        ));
        }
    }
    else{
        send_result(true, "喜欢记录读取失败");
    }




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
                        <a href="#" class="saveButton" id="addImageButton"
                            data-id="{$image_id}" title="保存这张图片"><em></em> <span>编辑</span></a>
                        <a href="#" class="likeButton{$current_user_like}" id="likeImageButton"
                            data-id="{$image_id}" title="喜欢这张图片"><em></em> <span>喜欢</span></a>
                        <a href="#" class="shareButton" id="shareImageButton" title="分享给你的好友"
                            data-id="{$image_id}"><em></em><span>分享</span></a>
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
html;

    //若存在喜欢记录，则渲染相关内容
    if (count($like_record_arr) > 0) {

        $like_html = '';
        foreach($like_record_arr as $like_record){
            $like_html .= "<a href='/profile/{$like_record['id']}'>{$like_record['name']}</a> ";
        }
        preg_replace('/\s$/', '', $like_html);
        $real_count = $like_count - count($like_record_arr);

        $html .= "<p class='likes'>{$like_html}";
        if ($real_count > 0) {
            $html .= " 和 <b>另外{$real_count}个人</b>喜欢这张图";
        }
        $html .= "</p>";
    }


    $html .= <<<html
                        <p class="saves">
                            <a href="http://www.wookmark.com/profile/badora">badora</a>, <a href="http://www.wookmark.com/profile/alstone-caillier">Alstone Caillier</a> 和 <b>另外 5 个人</b> 喜欢这张图
                        </p>
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