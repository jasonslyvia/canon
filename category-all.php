<?php
//列出所有的主题
require_once('functions/settings.php');
require_once(CANON_ABSPATH . '/wp-load.php');

get_header();
?>

<div id="luka">
  <div class="hamburger"></div>
  <p><a href="/">主题 - <?php echo get_bloginfo(); ?></a></p>
</div>

<div id="page">
    <div id="overview">
        <div class="headerSpacer"></div>

          <?php if(!is_user_logged_in()): ?>
      <!--  欢迎语   -->
      <div id="siteIntro">
          <h2>欢迎来到摄影圈，在这里发现并分享美丽的影与像。</h2>
          <a href="/signup" class="actionButton blueButton">现在加入</a>
      </div>
      <?php endif; ?>

        <div class="intro clearfix">
            <h1>热门主题</h1>
        </div>
        <ul class="clearfix">
<?php
    $category_ids = get_all_category_ids();
    if (count($category_ids) > 0) {
        foreach ($category_ids as $category_id) {
            $cat_name = get_the_category_by_ID($category_id);
            $cat_url = get_category_link($category_id);
            $cat_post = new WP_Query('cat='.$category_id.'&posts_per_page=1');
            if ($cat_post->have_posts()) {
                $cat_post->the_post();
                $thumb = canon_get_image(get_the_ID(), true);
?>
            <li> <h3><a href="<?php echo $cat_url; ?>"><?php echo $cat_name; ?></a></h3>
            <p>
                <a href="<?php echo $cat_url; ?>">
                <img src="<?php echo $thumb; ?>" style="min-height:200px;min-width:267px;" alt="<?php echo $cat_name; ?>"> </a>
            </p>
            </li>
<?php
            }// have_posts
        }//foreach
    }//category_ids
?>
        </ul>
    </div><!-- overview 结束 -->
</div>


<?php get_footer(); ?>