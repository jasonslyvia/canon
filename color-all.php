<?php
//列出所有的颜色
require_once('functions/settings.php');
require_once(CANON_ABSPATH . '/wp-load.php');

get_header();
?>

<div id="luka">
  <div class="hamburger"></div>
  <p><a href="/">色彩 - <?php echo get_bloginfo(); ?></a></p>
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

      <div id="colorsTile" class="tileTrash">
        <ol class="clearfix">
<?php
      global $wpdb;
      $colors = $wpdb->get_results('SELECT DISTINCT meta_value as color
                              FROM wp_postmeta
                              WHERE meta_key = "color"', ARRAY_N);
      foreach($colors as $key => $color){
        //确保是HEX颜色同时避免重复
        if(preg_match('/^#[A-Fa-f0-9]{6}$/', $color[0])):
?>
          <li data-hex="<?php echo substr($color[0], 1); ?>"
              style="background-color: <?php echo $color[0]; ?>;">
            <span></span>
          </li>
<?php   endif;
      }
?>
          </ol>
      </div>

      <div id="images">
        <div id="clue" class="tileTrash">
          <h2>点击上面的色块查看详细颜色的图片</h2>
        </div>
        <div class="clear"></div>
      </div>

      <script type="text/javascript"> var pageConfig = {type:'color'}; </script>
    </div><!-- overview 结束 -->
</div>
<?php get_footer(); ?>