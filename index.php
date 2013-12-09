<?php get_header(); ?>

<script type="text/javascript">
var nonce = '<?php echo wp_create_nonce("user_pic_action_".get_current_user_id()); ?>';
</script>

  <!--  顶栏    -->
    <div id="luka">
      <div class="hamburger"></div>
      <p><?php echo get_bloginfo(); ?>上的热门内容</p>
    </div>

    <!--  主内容   -->
    <div id="page">

      <div class="headerSpacer"> </div>
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
  //处理最新内容逻辑
  if (is_recent()) {
    $query = new WP_Query("posts_per_page=50");
  }
  //处理热门内容逻辑
  else{
    //获取热门图片排序
    //计算方法：保存数+喜欢数的和按倒序排列
    //可选参数 评论数、浏览量
    global $wpdb;
    $hot_result = $wpdb->get_results("
        select p.ID
        from wp_posts p
        left join (
          select pic_id, count(pic_id) as sc
          from pic_save
          group by pic_id
        ) s on s.pic_id = p.ID
        left join (
          select pic_id, count(pic_id) as lc
          from pic_like
          group by pic_id
        ) l on l.pic_id = p.ID
        where p.post_type = 'post' and p.post_status = 'publish'
        group by p.ID
        order by (coalesce(s.sc,0) + coalesce(l.lc, 0)) desc
    ", ARRAY_N);
    $hot_ids = array_values(call_user_func_array('array_merge', $hot_result));
    $query = new WP_Query(array("post__in" => $hot_ids,
                                "orderby" => "none",
                                "posts_per_page" => 100));
  }
  require('functions/get_pic_grid.php');
?>

      </div>
    </div>

    <div id="loader">
    </div>

<?php  get_footer();  ?>