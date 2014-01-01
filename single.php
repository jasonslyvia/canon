<?php get_header();?>

<div id="luka">
    <div class="hamburger"></div>
    <p><a href="/"><?php echo get_bloginfo(); ?></a></p>
</div>

<?php

require('functions/common.php');

/*======================================
获取当前登录用户信息
======================================*/
$c_user_id = get_current_user_id();
//当前用户保存的所有图片
$c_saved_record = $wpdb->get_col("
    SELECT pic_id as p FROM pic_save
    WHERE user_id = {$c_user_id}
  ");
//当前用户喜欢的所有图片
$c_liked_record = $wpdb->get_col("
    SELECT pic_id as p FROM pic_like
    WHERE user_id = {$c_user_id}
  ");

$id = get_the_ID();
//author_id决定了当前图片的储存位置
//BUG，直接使用 get_the_author_meta('ID') 不行？？
$post = get_post($id);
$author_id = $post->post_author;
$author_name = get_userdata($author_id)->display_name;
$avatar = AVATAR.get_user_meta($author_id,'avatar_small', true);
$image = $post->post_content;
$thumb = preg_replace('/(\..{3,4})$/', '_200$1', $image);
$width = get_post_meta($id, 'width', true);
$height = get_post_meta($id, 'height', true);

//来源
$referer = get_post_meta($id, 'referrer', true);
if (!empty($referer) && trim($referer) != '') {
    //获取refer的域名部分
    $short_referer = parse_url($referer);
    $short_referer = $short_referer["host"];
}
else{
    $short_referer = '原创';
    $referer = '/profile/'.$author_id;
}
//判断当前用户是否保存或喜欢了当前被浏览用户保存的图片
//以确定是否给操作选项增加 active 的 class
if (in_array($id, $c_saved_record)) {
    $save_text = "编辑";
    $save_class = " active";
}
else{
    $save_text = "保存";
    $save_class = "";
}
if (in_array($id, $c_liked_record)) {
    $like_class = " active";
}
else{
  $like_class = "";
}

//更新图片浏览量
$post_view = get_post_meta($id, 'post_view', true);
update_post_meta($id, 'post_view', ++$post_view);

?>

<script type="text/javascript">
var nonce = '<?php echo wp_create_nonce("user_pic_action_{$c_user_id}"); ?>';
</script>


<div id="page">
    <div class="headerSpacer">
    </div>

    <div class="wrap">
        <div id="image" data-id="<?php echo $id; ?>"
             data-width="<?php echo $width; ?>" data-height="<?php echo $height; ?>"
             style="background-color: #f8f8f8">
            <div class="image">
                <a href="<?php echo $referer; ?>"
                   target="_blank" rel="nofollow">
                   <img src="<?php echo IMAGE_PATH . $author_id . '/'. $image; ?>"
                        alt="<?php the_title(); ?>" />
                </a>
            </div>
        </div>
        <div id="details">
            <div class="options clearfix">
                <p id="tagOptions">
                    <a href="#" class="saveButton<?php echo $save_class;?>"
                       id="addImageButton"
                       data-id="<?php echo $id; ?>" title="<?php echo $save_text; ?>">
                        <em></em><span><?php echo $save_text; ?></span>
                    </a>
                    <a href="#" class="likeButton<?php echo $like_class;?>"
                       id="likeImageButton"
                       data-id="<?php echo $id; ?>" title="喜欢这个图片">
                       <em></em><span>喜欢</span>
                    </a>
                    <a href="#" class="shareButton" id="shareImageButton"
                       title="分享" data-id="<?php echo $id; ?>">
                       <em></em><span>分享</span>
                    </a>
                </p>
            </div>
            <div class="similar">
                <ul class="clearfix">
<?php
    $query = new WP_Query("author={$author_id}&posts_per_page=9");
    $i = 0;
    while ($query->have_posts()) {
        $query->the_post();
        if ($i++ % 3 == 0) {
            $li_class = " class='first'";
        }
        else{
            $li_class = "";
        }
        echo "<li{$li_class}>".
            "<a href='" . get_permalink() . "' data-id='" . get_the_ID() . "'>".
                "<img src='".get_thumb(get_the_content(), $author_id, true)."'
                      style='min-width:105px;min-height:79px;' />".
            "</a>";
    }
    wp_reset_postdata();
?>
                </ul>
            </div>
            <div class="detailsWrap">
                <div class="finder clearfix">
                    <div class="userPic">
                        <a href="/profile/<?php echo $author;?>">
                            <img src="<?php echo $avatar;?>" alt=""
                                 width="30" height="30" />
                        </a>
                    </div>
                    <h4><a href="/profile/<?php echo $author_id;?>" class="userLink">
                        <?php echo $author_name; ?></a><br/>
                    来自 <a href="<?php echo $referer; ?>"><?php echo $short_referer; ?></a></h4>
                    <?php echo $follow_btn_html; ?>
                </div>
                <div class="stats saves clearfix">
                    <?php echo $like_arr["sample_html"]; ?>
                    <?php echo $save_arr["sample_html"]; ?>
                </div>

<?php
    //评论内容
    $comments = get_comments(array("post_id" => $id));
    //当前用户头像
    $c_avatar = AVATAR.get_user_meta(get_current_user_id(),
                                    'avatar_small',
                                    true);

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
     data-imageid="{$id}">
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
    $comment_html .= <<< c_html_form
 <div id="commentForm" data-imageid="{$id}">
    <div class="userPic">
        <img src="{$c_avatar}" width="30"
             height="30" alt=""/>
    </div>
    <textarea name="comment" placeholder="按回车键发表评论">按回车键发表评论</textarea>
</div>
c_html_form;

echo $comment_html;

?>

            </div>
        </div>
    </div>
    <div class="clear">
    </div>
    <div id="nextImage">
        <?php next_post_link('%link', '下一张<br/>图片', false); ?>
    </div>
    <div id="moreImages" class="clearfix">
        <div class="werbung section">
            <div class="superAdContent">
                <div id="lijit_region_229626">
                </div>
            </div>
            <p>
                赞助商广告
            </p>
        </div>

<!-- 来自同一网站的更多内容 -->
<?php if ($short_referer !== "原创") { ;?>
        <div class="section">
            <div class="header">
                <div class="headerWrap clearfix">
                    <div class="info">
                        <p>
                            更多作品来自该网站
                        </p>
                        <h3><a href="<?php echo $referer; ?>"><?php echo $short_referer; ?></a></h3>
                    </div>
                </div>
            </div>
            <div class="images">
                <ul class="clearfix">
<?php
    $args = array("posts_per_page" => 4,
                  "meta_query" => array(array("key" => "referrer",
                                      "value" => $short_referer,
                                      "compare" => "LIKE")));
    $query = new WP_Query($args);
    $i = 0;
    while ($query->have_posts()) {
        $query->the_post();
        if ($i ++ == 0) {
            $li_class = " class='first'";
        }
        else{
            $like_class = "";
        }
?>
        <li <?php echo $li_class; ?>>
            <a href="<?php the_permalink(); ?>">
                <img src="<?php echo IMAGE_PATH.get_the_author_meta('ID').'/'.get_the_content(); ?>"
                     alt="<?php the_title(); ?>"
                     width="200" height="150">
            </a>
        </li>
<?php }
    wp_reset_postdata();
?>
                </ul>
            </div>
        </div>
<?php  } ?>


        <div class="section">
            <div class="header">
                <div class="headerWrap hasPic clearfix">
                    <div class="info">
                        <a href="/profile/<?php echo $author_id; ?>" class="userpic">
                            <img src="<?php echo $avatar; ?>"
                                 width="30" height="30"
                                 alt="<?php echo $author_name; ?>">
                        </a>
                        <p>
                            更多作品来自
                        </p>
                        <h3><a href="/profile/<?php echo $author_id; ?>"><?php echo $author_name; ?></a></h3>
                    </div>

<?php

    //处理关注按钮
    if ($c_user_id == $author_id) {
        $no_follow_btn = true;
    }
    else{
        //先判断当前用户是否关注了作者
        global $wpdb;
        $follow = $wpdb->get_var("
            SELECT count(*) FROM user_relation
            WHERE follower_id = {$c_user_id} AND followee_id = {$author_id}
        ");
        //若已关注
        if ($follow != 0) {
            $follow_class = " active";
            $follow_text = "取消关注";
        }
        else{
            $follow_class = "";
            $follow_text = "关 注";
        }
    }

    if (!$no_follow_btn):
 ?>

                    <div class="options">
                        <div class="followButton actionButton blueButton<?php echo $follow_class;?>"
                             data-id="<?php echo $author_id; ?>" data-type="1">
                            <a href="#"><?php echo $follow_text; ?></a>
                        </div>
                    </div>
<?php endif; ?>
                </div>
            </div>
            <div class="images">
                <ul class="clearfix">
<?php
    $args = array("posts_per_page" => 4,
                  "author" => $author_id,
                  "orderby" => "rand");
    $author_query = new WP_Query($args);
    $i = 0;
    while ($author_query->have_posts()) {
        $author_query->the_post();
        if ($i ++ == 0) {
            $li_class = " class='first'";
        }
        else{
            $like_class = "";
        }
?>
        <li <?php echo $li_class; ?>>
            <a href="<?php the_permalink(); ?>">
                <img src="<?php echo IMAGE_PATH.get_the_author_meta('ID').'/'.get_the_content(); ?>"
                     alt="<?php the_title(); ?>"
                     width="200" height="150">
            </a>
        </li>
<?php }
    wp_reset_postdata();
?>
                </ul>
            </div>
        </div>
    </div>
    <div class="clear">
    </div>
</div>

<?php get_footer(); ?>