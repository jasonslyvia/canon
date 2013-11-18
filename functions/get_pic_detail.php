<?php
/*
 *  返回图像的详细信息
 *
 *  @param {int} imageId 图像id（即post_id）
 *  @return {json}
 */

require('settings.php');
require('common.php');
define('WP_USE_THEMES', false);
require(ABSPATH.'wp-load.php');

if (isset($_GET['imageId'])) {
    $image_id = $_GET['imageId'];
    $post = get_post($image_id);

    $author = $post->post_author;
    $author_name = get_userdata($author)->display_name;
    $pic = IMAGE_PATH . $author . '/' . $post->post_content;
    $referer = $referer == '' ? '原创' : $referer;
    $title = $post->post_name;
    $history = '';

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
                <!-- User options -->
                <div class="options clearfix">
                    <p id="tagOptions">
                        <a href="#" class="saveButton active" id="addImageButton"
                            data-id="{$image_id}" title="保存这张图片"><em></em> <span>编辑</span></a>
                        <a href="#" class="likeButton" id="likeImageButton"
                            data-id="{$image_id}" title="Like image"><em></em> <span>喜欢</span></a>
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
                "<img src='".get_thumb(get_the_content(), $author, true)."' width='100'  height='75' />".
            "</a>";
    }

    $html .= <<<html
                    </ul>
                </div>
                <div class="info">
                    <!-- Finder -->
                    <div class="finder clearfix">
                        <div class="userPic">
                            <a href="/profile/{$author}">
                                <img src="{$AVATAR}default_avatar_small.png" width="40" height="40" alt=""/>
                            </a>
                        </div>
                        <h4><a href="/profile/{$author}" class="userLink">{$author_name}</a><br/>
                        来自 <a href="#">{$referer}</a></h4>
                        <button type="button" class="follow blue active" data-type="1" data-id="7650">已关注</button>
                    </div>
                    <!-- Owners & likers -->
                    <div class="stats saves clearfix">
                        <p class="saves">
                            <a href="http://www.wookmark.com/profile/aerynn">Aerynn</a>, <a href="http://www.wookmark.com/profile/nikky804">Nikky804</a> 和 <b>另外 13 个人</b> 保存了这张图</b>.
                        </p>
                        <p class="likes">
                            <a href="http://www.wookmark.com/profile/badora">badora</a>, <a href="http://www.wookmark.com/profile/alstone-caillier">Alstone Caillier</a> 和 <b>另外 5 个人</b> 喜欢这张图
                        </p>
                    </div>
                    <!-- Activity -->
                    <div class="activity clearfix">
                        <!-- Comments -->
                        <div id="comments" class="empty">
                            <div class="comments">
                            </div>
                        </div>
                        <!-- Comment form -->
                        <div id="commentForm" data-imageid="{$image_id}">
                            <div class="userPic">
                                <img src="{$AVATAR}/default_avatar_small.png" width="30" height="30" alt=""/>
                            </div>
                            <textarea autocomplete="off" name="comment" placeholder="说点儿什么">说点儿什么</textarea>
                        </div>
                        <!-- Groups -->
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


?>