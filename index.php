<?php get_header(); ?>

<script type="text/javascript">
var nonce = '<?php echo wp_create_nonce("user_pic_action_".get_current_user_id()); ?>';
</script>

<!-- ugly fix for loading ad -->
<div id="ad-300-250" style="display:none">
  <script type="text/javascript">
  /*300*250，创建于2014-4-11*/
  var cpro_id = "u1518821";
  </script>
  <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
</div>
<div id="ad-160-600" style="display:none">
  <script type="text/javascript">
  /*160*600，创建于2014-4-11*/
  var cpro_id = "u1518847";
  </script>
  <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
</div>

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
          <h2>欢迎来到摄影圈，在这里发现并分享美丽的影与像。</h2>
          <a href="/signup" class="actionButton blueButton">现在加入</a>
      </div>
      <?php endif; ?>

      <!--  内容  -->
      <div id="images">
<?php
  require_once('functions/get_data.php');

  //处理最新内容逻辑
  if (is_recent()) {
    get_index_recent_image();
  }
  //处理热门内容逻辑
  else{
    get_index_popular_image();
  }
?>

      </div>
    </div>

    <div id="loader">
    </div>



<?php  get_footer();  ?>