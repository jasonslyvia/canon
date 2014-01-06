<?php

require_once('common.php');
define('WP_USE_THEMES', false);
require_once(ABSPATH.'wp-load.php');

if (isset($_GET['imageId'])) {
    header('Content-Type: application/json');
    send_result(false, null, get_pic_detail(), true);
}




/*
 *  返回图片详细信息
 *
 *  @return {array}
 */
function get_pic_detail(){
    //获得图片详细信息
    $image_id = $_GET['imageId'];
    $post = get_post($image_id);

    //作者id
    $author = $post->post_author;
    //作者昵称
    $author_name = get_userdata($author)->display_name;
    //作者头像
    $avatar = get_user_meta($author,'avatar_small', true);
    //图片地址
    $pic = IMAGE_PATH . $author . '/' . $post->post_content;
    //来源
    $referer = get_post_meta($image_id, 'referrer', true);
    if (!empty($referer) && trim($referer) != '') {
        //获取refer的域名部分
        $short_referer = parse_url($referer);
        $short_referer = $short_referer["host"];
    }
    else{
        $short_referer = '原创';
        $referer = '/profile/'.$author;
    }
    //链接
    $permalink = $post->post_name;
    //标题
    $title = $post->post_title ? $post->post_title : '无标题';
    $history = '';

    //网站名词
    $website_name = get_bloginfo();


    //若用户id不存在则设为0，模拟用户未登录情况
    //get_current_user_id在用户未登录情况下返回0
    $user_id =  get_current_user_id();
    //喜欢与保存数据
    require_once('get_pic_save_like.php');
    $op_result = get_pic_save_like($image_id, $user_id);
    $like_arr = $op_result["like"];
    $save_arr = $op_result["save"];
    $save_op = $save_arr['class_name'] == '' ? '保存': '编辑';


    //评论内容
    $comments = get_comments(array("post_id" => $image_id));

    if (count($comments) == 0) {
        $comment_class = " empty";
    }
    else{
        $comment_class = "";
    }
    $comment_html = '<div id="comments"><div class="comments'.$comment_class.'">';
    foreach ($comments as $c) {
        $comment_avatar = AVATAR.get_user_meta($c->user_id, 'avatar_small', true);
        $comment_author = get_userdata($c->user_id)->display_name;
        $c_html = <<<c_html
<div id="comment_{$c->comment_ID}" class="comment clearfix"
     data-imageid="{$post_id}">
    <div class="userPic">
        <a href="/profile/{$c->user_id}">
            <img src="{$comment_avatar}"
                 width="30" height="30" alt="">
        </a>
    </div>
    <p>
        <a href="/profile/{$c->user_id}">
            {$comment_author}</a> – {$c->comment_content}<br>
        </a>
    </p>
</div>
c_html;
        $comment_html .= $c_html;
    }
    $comment_html .= '</div></div>';
    //当前用户头像
    $c_avatar = get_user_meta(get_current_user_id(), 'avatar_small', true);
    if (!$c_avatar) {
        $c_avatar = "default_avatar_small.png";
    }

    //处理关注按钮
    if ($user_id == $author) {
        $follow_btn_html = '';
    }
    else{
        //先判断当前用户是否关注了作者
        global $wpdb;
        $follow = $wpdb->get_var("
            SELECT count(*) FROM user_relation
            WHERE follower_id = {$user_id} AND followee_id = {$author}
        ");
        //若已关注
        if ($follow != 0) {
            $follow_btn_html = <<<html
    <button type="button" class="blue follow active" data-type="1"
            data-id="{$author}">
            已关注
    </button>
html;
        }
        else{
            $follow_btn_html = <<<html
    <button type="button" class="blue follow" data-type="1"
            data-id="{$author}">
            关 注
    </button>
html;

        }
    }








    //heredoc 常数
    $AVATAR = AVATAR;
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
            <a href="{$referer}" target="_blank">
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
                            data-id="{$image_id}" title="保存这张图片">
                            <em></em> <span>{$save_op}</span>
                        </a>
                        <a href="#" class="likeButton{$like_arr['class_name']}"
                            id="likeImageButton"
                            data-id="{$image_id}" title="喜欢这张图片">
                            <em></em> <span>喜欢</span>
                        </a>
                        <a href="#" class="shareButton" id="shareImageButton"
                            title="分享给你的好友"
                            data-id="{$image_id}"><em></em><span>分享</span>
                        </a>
                    </p>
                </div>
                <div class="jiathis_style_32x32" id='sharePopup'>
                    <div class="arrow">
                        <span class="up"></span>
                        <span class="up-wrapper"></span>
                    </div>
                    <a class="jiathis_button_qzone">QQ空间</a>
                    <a class="jiathis_button_tsina">新浪微博</a>
                    <a class="jiathis_button_tqq">腾讯微博</a>
                    <a class="jiathis_button_renren">人人网</a>
                    <a class="jiathis_button_kaixin001">开心网</a>
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
                      style='min-width:105px;min-height:79px;' />".
            "</a>";
    }

    $html .= <<<html
                    </ul>
                </div>
                <div class="info">
                    <div class="finder clearfix">
                        <div class="userPic">
                            <a href="/profile/{$author}">
                                <img src="{$AVATAR}{$avatar}"
                                    width="40" height="40" alt=""/>
                            </a>
                        </div>
                        <h4><a href="/profile/{$author}" class="userLink">
                            {$author_name}</a><br/>
                        来自 <a href="{$referer}">{$short_referer}</a></h4>
                        {$follow_btn_html}
                    </div>
                    <div class="stats saves clearfix">
                    {$like_arr["sample_html"]}
                    {$save_arr["sample_html"]}
                    </div>
                    <div class="activity clearfix">
                        {$comment_html}
                        <div id="commentForm" data-imageid="{$image_id}">
                            <div class="userPic">
                                <img src="{$AVATAR}{$c_avatar}" width="30"
                                     height="30" alt=""/>
                            </div>
                            <textarea name="comment" placeholder="按回车键发表评论">按回车键发表评论</textarea>
                        </div>
                    </div><!-- 相关评论结束-->
                </div><!-- 图片相关信息结束 -->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="http://v3.jiathis.com/code/jia.js?uid=1373892207080614" charset="utf-8"></script>
html;



    $width = get_post_meta($image_id, 'width', true);
    $height = get_post_meta($image_id, 'height', true);

    //更新图片浏览量
    $post_view = get_post_meta($image_id, 'post_view', true);
    update_post_meta($image_id, 'post_view', ++$post_view);

    return array("imageId" => $image_id,
                                 "width" => $width,
                                 "height" => $height,
                                 "url" => $pic,
                                 "referer" => $referer,
                                 "html" => $html,
                                 "title" => $title,
                                 "history" => '/image/'.$permalink);


}


?>