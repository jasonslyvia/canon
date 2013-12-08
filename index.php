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
//获取热门图片排序
//计算方法：保存数+喜欢数的和按倒序排列
  global $wpdb;
  $save_c = $wpdb->get_results("
      SELECT pic_id, count(pic_id) as c
      FROM pic_save
      GROUP BY pic_id
      ORDER BY c DESC
    ", ARRAY_A);
  $like_c = $wpdb->get_results("
      SELECT pic_id, count(pic_id) as c
      FROM pic_like
      GROUP BY pic_id
      ORDER BY c DESC
  ",  ARRAY_A);
  var_dump(array_merge($save_c, $like_c));
  global $query;
  $query = new WP_Query("posts_per_page=50");
  require('functions/get_pic_grid.php');
?>

      </div>
    </div>

    <div id="loader">
    </div>

<?php  get_footer();  ?>