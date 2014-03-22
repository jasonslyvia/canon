<?php
/*
 *  站外获取图片后一键发布至本站
 *
 *  @param {string} url 图片地址
 *  @param {string} referer 来源网址
 *  @param {string} title 图片标题
 */

require('functions/settings.php');
require_once(CANON_ABSPATH . '/wp-load.php');

//首先判断用户是否已登录
if (!is_user_logged_in()) {
    wp_redirect("/login?next=%2Fplugin%2Fadd".
                "&url={$_GET['url']}".
                "&title={$_GET['title']}".
                "&referer={$_GET['referer']}");
    exit();
}

$url = urldecode($_GET['url']);
$title = urldecode($_GET['title']);
$referer = urldecode($_GET['referer']);
if (!$url) {
  wp_redirect('/');
  exit();
}

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

    <h1>添加新内容</h1>
    <div id="panelLogin" class="panel"></div>

    <div id="uploadDiv" class="wrapSignupForm">
        <div class="preview">
            <img src="<?php echo $url; ?>">
            <div class="op">
                <label for="referrer">照片来源网址（原创则留空）</label><br>
                <input type="text" id="referrer" value="<?php echo $referer; ?>">
                <label for="title">照片标题（一句话形容这幅作品）</label><br>
                <input type="text" id="title" value="<?php echo $title; ?>">
                <label for="cat">照片主题</label>
                <?php wp_dropdown_categories(array("hide_empty" => false,
                                                  "id" => "picCat")); ?>
                <br />
                <label for='color'>照片颜色</label>
                <input id='color' type='text' name='color' />
                <input type="hidden" id="picColor" />
                <input type="hidden" id="filename" value="<?php echo $url; ?>" />
                <a href="javascript:;" class="actionButton blueButton" id="publishNewBtn">发布新照片</a>
            </div>
        </div>
    </div>
  </div>
</div>

<div id="loader">
</div>


 <?php get_footer(); ?>