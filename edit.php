<?php
/*
 *  编辑图片
 *
 *  @param {int} pid 要编辑的图片id
 */

require('functions/settings.php');
require(ABSPATH . '/wp-load.php');

if (!is_user_logged_in()) {
    wp_redirect('/');
    exit();
}

$pid = $_GET['pid'];
$uid = get_current_user_id();
$post = get_post($pid);
$author_id = $post->post_author;
//若post id有误或作者并不是当前登录用户
if ($post == null || $uid != $author_id) {
  wp_redirect('/');
  exit();
}

//获取需要回填的内容
$title = $post->post_title;
$image_file = $post->post_content;
$image = IMAGE_PATH.$uid.'/'.$image_file;
$referrer = get_post_meta($pid, 'referrer', true);
$category = get_the_category($pid);
$category = $category[0]->cat_ID;

get_header();

?>

<script type="text/javascript">
  var nonce = '<?php echo wp_create_nonce("upload_pic_".get_current_user_id()); ?>';
</script>

<div id="luka">
  <div class="hamburger"> </div>
  <p> <a href="/"> <?php echo get_bloginfo(); ?> </a> </p>
</div>

<div id="page">

  <div class="headerSpacer"> </div>
  <div id="maincontent" class="center" style="width:700px;">
    <h1>编辑内容</h1>
    <div id="updateDiv" class="wrapSignupForm">
      <div class="preview">
        <img src="<?php echo $image; ?>" width="620">
        <div class="op">
          <input type="hidden" id="pid" value="<?php echo $pid; ?>" />
          <label for="referrer">照片来源网址（原创则留空）</label><br>
          <input type="text" id="referrer" value="<?php echo $referrer; ?>" />
          <label for="title">照片标题（一句话形容这幅作品，必填）</label><br>
          <input type="text" id="title" value="<?php echo $title; ?>" />
          <label for="cat">照片主题</label><br>
          <?php wp_dropdown_categories(array("hide_empty" => false, "selected" => $category)); ?>
          <br>
          <a href="javascript:;" class="actionButton blueButton" id="updateBtn">更新</a>
        </div>
      </div>
    </div>

  </div>
</div>

<div id="loader">
</div>

 <?php get_footer(); ?>