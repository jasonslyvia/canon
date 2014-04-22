<?php

get_header();

?>

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
<div id="ad-160-600-2" style="display:none">
  <script type="text/javascript">
     document.write('<a style="display:none!important" id="tanx-a-mm_44751182_5976304_20910905"></a>');
     tanx_s = document.createElement("script");
     tanx_s.type = "text/javascript";
     tanx_s.charset = "gbk";
     tanx_s.id = "tanx-s-mm_44751182_5976304_20910905";
     tanx_s.async = true;
     tanx_s.src = "http://p.tanx.com/ex?i=mm_44751182_5976304_20910905";
     tanx_h = document.getElementsByTagName("head")[0];
     if(tanx_h)tanx_h.insertBefore(tanx_s,tanx_h.firstChild);
  </script>
</div>


<div id="luka">
  <div class="hamburger"> </div>
  <p> <a href="#"> <?php single_cat_title();?> - <?php echo get_bloginfo(); ?> </a> </p>
</div>

<div id="page">
    <div class="headerSpacer"></div>

      <?php if(!is_user_logged_in()): ?>
      <!--  欢迎语   -->
      <div id="siteIntro">
          <h2>欢迎来到摄影圈，在这里发现并分享美丽的影与像。</h2>
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