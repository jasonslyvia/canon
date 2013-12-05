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
//BUG，直接使用 get_the_author_meta 不行？？
$post = get_post($id);
$author_id = $post->post_author;
$image = $post->post_content;
$thumb = preg_replace('/(\..{3,4})$/', '_200$1', $image);
$width = get_post_meta($id, 'width', true);
$height = get_post_meta($id, 'height', true);

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
?>


<div id="page">
    <div class="headerSpacer">
    </div>

    <div class="wrap">
        <div id="image" data-id="<?php echo $id; ?>"
             data-width="<?php echo $width; ?>" data-height="<?php echo $height; ?>"
             style="background-color: #f8f8f8">
            <div class="image">
                <a href="#"
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
                      width='100'  height='75' />".
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
                            <img src="<?php echo AVATAR.$avatar;?>"
                                    width="40" height="40" alt=""/>
                        </a>
                    </div>
                    <h4><a href="/profile/<?php echo $author_id;?>" class="userLink">
                        {$author_name}</a><br/>
                    来自 <a href="{$referer}">{$short_referer}</a></h4>
                    {$follow_btn_html}
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
    $comment_html .= <<< c_html_form
 <div id="commentForm" data-imageid="{$image_id}">
    <div class="userPic">
        <img src="{$c_avatar}" width="30"
             height="30" alt=""/>
    </div>
    <textarea name="comment" placeholder="按回车键发表评论">按回车键发表评论</textarea>
</div>
c_html_form;

echo $comment_html;

?>

                <div class="owners line likes">
                    <p class="userListPopup" data-type="2" data-id="346470">
                        <a href="http://www.wookmark.com/profile/diana-v-rlan">Diana Vârlan</a> likes this
                    </p>
                </div>

            </div>
            <a href="http://www.wookmark.com/flag?ref=image&amp;imageId=346131" class="flagOption" target="_blank">Flag this image</a>
        </div>
    </div>
    <div class="clear">
    </div>
    <div id="nextImage">
        <a href="http://www.wookmark.com/image/346410/appartement-vendre-lyon-vente-de-3-pi-ces-design-d-int-ri">Next<br>
        Image</a>
    </div>
    <div id="moreImages" class="clearfix">
        <div class="werbung section">
            <div class="superAdContent">
                <div id="lijit_region_229626">
                </div>
                <script type="text/javascript" src="http://www.lijit.com/delivery/fp?u=wookmark&amp;z=229626"></script>
            </div>
            <p>
                Advertisements
            </p>
        </div>
        <div class="section">
            <div class="header">
                <div class="headerWrap clearfix">
                    <div class="info">
                        <p>
                            From the site
                        </p>
                        <h3><a href="http://www.wookmark.com/source/26779/www.pinterest.com">www.pinterest.com</a></h3>
                    </div>
                    <div class="options">
                        <div class="followButton actionButton blueButton" data-id="26779" data-type="2">
                            <a href="#">Follow</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="images">
                <ul class="clearfix">
                    <li class=" first"><a href="http://www.wookmark.com/image/346566/1c5c6c6c004f82c38bd250ed9980060c-jpg-image-jpeg-455x600-pixels"><img src="http://www.wookmark.com/images/200-150/346566_a04550878ca0ed740a1322fe9bda4894.jpg" alt="1c5c6c6c004f82c38bd250ed9980060c.jpg (Image JPEG, 455x600 pixels)" width="200" height="150"></a></li>
                    <li><a href="http://www.wookmark.com/image/346468/1c5c6c6c004f82c38bd250ed9980060c-jpg-image-jpeg-455x600-pixels"><img src="http://www.wookmark.com/images/200-150/346468_45a4e4ae68c51691d3c69dfa9d0ca9a4.jpg" alt="1c5c6c6c004f82c38bd250ed9980060c.jpg (Image JPEG, 455x600 pixels)" width="200" height="150"></a></li>
                    <li><a href="http://www.wookmark.com/image/346466/1c5c6c6c004f82c38bd250ed9980060c-jpg-image-jpeg-455x600-pixels"><img src="http://www.wookmark.com/images/200-150/346466_902a238946e005c0822ed0925ceb9ee8.jpg" alt="1c5c6c6c004f82c38bd250ed9980060c.jpg (Image JPEG, 455x600 pixels)" width="200" height="150"></a></li>
                    <li><a href="http://www.wookmark.com/image/346573/1c5c6c6c004f82c38bd250ed9980060c-jpg-image-jpeg-455x600-pixels"><img src="http://www.wookmark.com/images/200-150/346573_af5d2885936c62adddc81b5af7f6ceea.jpg" alt="1c5c6c6c004f82c38bd250ed9980060c.jpg (Image JPEG, 455x600 pixels)" width="200" height="150"></a></li>
                </ul>
            </div>
        </div>
        <div class="section">
            <div class="header">
                <div class="headerWrap hasPic clearfix">
                    <div class="info">
                        <a href="http://www.wookmark.com/profile/aerynn" class="userpic"><img src="http://www.wookmark.com/images/profile/30/a_kiss_in_the_dark_by_hiritai-d4rrx82_2.jpg" width="30" height="30" alt="Aerynn"></a>
                        <p>
                            More from
                        </p>
                        <h3><a href="http://www.wookmark.com/profile/aerynn">Aerynn</a></h3>
                    </div>
                    <div class="options">
                        <div class="followButton actionButton blueButton" data-id="7650" data-type="1">
                            <a href="#">Follow</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="images">
                <ul class="clearfix">
                    <li class=" first"><a href="http://www.wookmark.com/image/346567/1c5c6c6c004f82c38bd250ed9980060c-jpg-image-jpeg-455x600-pixels"><img src="http://www.wookmark.com/images/200-150/346567_a23659947927ec16447ff3cb39e8b539.jpg" alt="1c5c6c6c004f82c38bd250ed9980060c.jpg (Image JPEG, 455x600 pixels)" width="200" height="150"></a></li>
                    <li><a href="http://www.wookmark.com/image/346572/1c5c6c6c004f82c38bd250ed9980060c-jpg-image-jpeg-455x600-pixels"><img src="http://www.wookmark.com/images/200-150/346572_5730ba8a1d314f8dc86cc2acf91dea31.jpg" alt="1c5c6c6c004f82c38bd250ed9980060c.jpg (Image JPEG, 455x600 pixels)" width="200" height="150"></a></li>
                    <li><a href="http://www.wookmark.com/image/346571/1c5c6c6c004f82c38bd250ed9980060c-jpg-image-jpeg-455x600-pixels"><img src="http://www.wookmark.com/images/200-150/346571_3bccb32a62d5ac5fe84ec9a890f260b3.jpg" alt="1c5c6c6c004f82c38bd250ed9980060c.jpg (Image JPEG, 455x600 pixels)" width="200" height="150"></a></li>
                    <li><a href="http://www.wookmark.com/image/346570/1c5c6c6c004f82c38bd250ed9980060c-jpg-image-jpeg-455x600-pixels"><img src="http://www.wookmark.com/images/200-150/346570_74aeba4ac2939810efdd0c59c2b97fd4.jpg" alt="1c5c6c6c004f82c38bd250ed9980060c.jpg (Image JPEG, 455x600 pixels)" width="200" height="150"></a></li>
                </ul>
            </div>
        </div>
        <div class="section">
            <div class="header">
                <div class="headerWrap clearfix">
                    <div class="info">
                        <p>
                            From the group
                        </p>
                        <h3><a href="http://www.wookmark.com/group/16438/objects-and-clothes">Objects</a> by <a href="http://www.wookmark.com/profile/aerynn">Aerynn</a></h3>
                    </div>
                    <div class="options">
                        <div class="followButton actionButton blueButton" data-id="16438" data-type="4">
                            <a href="#">Follow</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="images">
                <ul class="clearfix">
                    <li class=" first"><a href="http://www.wookmark.com/image/346569/1c5c6c6c004f82c38bd250ed9980060c-jpg-image-jpeg-455x600-pixels"><img src="http://www.wookmark.com/images/200-150/346569_df62daa16676b793c285bfb72ed1907c.jpg" alt="1c5c6c6c004f82c38bd250ed9980060c.jpg (Image JPEG, 455x600 pixels)" width="200" height="150"></a></li>
                    <li><a href="http://www.wookmark.com/image/346453/1c5c6c6c004f82c38bd250ed9980060c-jpg-image-jpeg-455x600-pixels"><img src="http://www.wookmark.com/images/200-150/346453_1c5c6c6c004f82c38bd250ed9980060c.jpg" alt="1c5c6c6c004f82c38bd250ed9980060c.jpg (Image JPEG, 455x600 pixels)" width="200" height="150"></a></li>
                    <li><a href="http://www.wookmark.com/image/345877/ac382f447d57132ad5d5750c2bdcdb3b-jpg-image-jpeg-500x500-pixels"><img src="http://www.wookmark.com/images/200-150/345877_9504f6d152c8b8ec9de9578d9f9d468b.jpg" alt="ac382f447d57132ad5d5750c2bdcdb3b.jpg (Image JPEG, 500x500 pixels)" width="200" height="150"></a></li>
                    <li><a href="http://www.wookmark.com/image/339245/e568627d350273ed71f25c959759ee34-jpg-image-jpeg-550x796-pixels-redimensionn-e-44"><img src="http://www.wookmark.com/images/200-150/339245_1bca3c6ca920e60d68ce008b33f787fc.jpg" alt="e568627d350273ed71f25c959759ee34.jpg (Image JPEG, 550x796 pixels) - RedimensionnÃ©e (44%)" width="200" height="150"></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="clear">
    </div>
</div>

<?php get_footer(); ?>