<?php
//列出所有的颜色
require_once('functions/settings.php');
require_once(ABSPATH . '/wp-load.php');

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
          <h2>欢迎来到小摄郎，在这里发现并分享美丽的影与像。</h2>
          <a href="/signup" class="actionButton blueButton">现在加入</a>
      </div>
      <?php endif; ?>

      <div id="colorsTile" class="tileTrash">
        <ol class="clearfix">
<?php $args = array('meta_key=color&meta_compare=EXISTS');
      $query = new WP_Query($args);
      $colors = array();
      while ($query->have_posts()) {
        $query->the_post();
        $color = get_post_meta(get_the_ID(), 'color', true);
        //确保是HEX颜色同时避免重复
        if(preg_match('/^#[A-F0-9]{6}$/i', $color) && !in_array($color, $colors)):
          array_push($colors, $color);
?>
          <li data-hex="<?php echo substr($color, 1); ?>"
              style="background-color: <?php echo $color; ?>;">
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