<?php

get_header();

?>

<script type="text/javascript">
var nonce = '<?php echo wp_create_nonce("user_pic_action_".get_current_user_id()); ?>';
</script>

<div id="luka">
  <div class="hamburger"> </div>
  <p> <a href="#"> <?php single_cat_title();?> - <?php echo get_bloginfo(); ?> </a> </p>
</div>

<div id="page">
    <div class="headerSpacer"></div>

      <?php if(!is_user_logged_in()): ?>
      <!--  欢迎语   -->
      <div id="siteIntro">
          <h2>欢迎来到小摄郎，在这里发现并分享美丽的影与像。</h2>
          <a href="/signup" class="actionButton blueButton">现在加入</a>
      </div>
      <?php endif; ?>

    <div id="images">
        <div class="tile" id="categoryInfo">
            <h1>主题<br>
            <b><?php single_cat_title();?></b></h1>
            <p class="description"><?php echo strip_tags(category_description()); ?></p>
        </div>

<?php
    global $query;
    global $cat_id;
    $cat_id = get_cat_ID(single_cat_title('', false));
    require_once('functions/get_data.php');
    get_category_image($cat_id, 1, true);
?>
    </div>
</div>
</div>

<?php get_footer(); ?>