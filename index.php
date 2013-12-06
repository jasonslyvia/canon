<?php get_header(); ?>

<script type="text/javascript">
var nonce = '<?php echo wp_create_nonce("user_pic_action_".get_current_user_id()); ?>';
</script>

  <!--  顶栏    -->
    <div id="luka">

      <div class="hamburger">
      </div>

      <p><?php echo get_bloginfo(); ?>上的热门内容</p>
    </div>

    <!--  主内容   -->
    <div id="page">

      <div class="headerSpacer">
      </div>

      <?php if(!is_user_logged_in()): ?>
      <!--  欢迎语   -->
      <div id="siteIntro">
          <h2>欢迎来到小摄郎，在这里发现并分享美丽的影与像。</h2>
          <a href="/signup" class="actionButton blueButton">现在加入</a>
      </div>
      <?php endif; ?>

      <!--  内容  -->
      <div id="images">
<?php
  global $query;
  $query = new WP_Query("posts_per_page=50");
  require('functions/get_pic_grid.php');
?>

      </div>
    </div>

    <div id="loader">
    </div>

<?php  get_footer();  ?>