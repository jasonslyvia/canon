<?php

get_header();


/*======================================
获取当前登录用户信息
======================================*/
$c_user_id = get_current_user_id();
//判断当前被浏览用户与当前登录用户是否为同一人
global $same_user;
if ($user->ID == $c_user_id) {
  $same_user = true;
}
//当前用户保存的所有图片
$c_saved_record = $wpdb->get_col("
    SELECT pic_id as p FROM pic_save
    WHERE user_id = {$c_user_id}
  ");
//当前用户喜欢的所有图片
$c_liked_record = $wpdb->get_col("
    SELECT pic_id as p FROM pic_like
    WHERE user_id = {$c_user_id}
  ");

?>


<script type="text/javascript">
var nonce = '<?php echo wp_create_nonce("user_pic_action_".get_current_user_id()); ?>';
</script>

  <!--  顶栏    -->
    <div id="luka">

      <div class="hamburger">
      </div>

      <p>小摄郎上的热门内容</p>
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
<?php $query = new WP_Query("posts_per_page=50");
while ($query->have_posts()) {
    $query->the_post();

    //获取图片基本信息
    $pid = get_the_ID();
    $like_count = get_post_meta($pid, 'like_count', true);
    $save_count = get_post_meta($pid, 'save_count', true);
    $width = get_post_meta($pid, 'width', true);
    $height = get_post_meta($pid, 'height', true);
    $author_id = get_the_author_meta('ID');
    $thumb = preg_replace('/(\..{3,4})$/', '_200$1', get_the_content());

    //判断当前用户是否保存或喜欢了当前被浏览用户保存的图片
    //以确定是否给操作选项增加 active 的 class
    if (in_array($id, $c_saved_record)) {
        $save_class = " active";
    }
    else{
        $save_class = "";
    }
    if (in_array($id, $c_liked_record)) {
        $like_class = " active";
    }
    else{
      $like_class = "";
    }
?>
<div class="polaroid tile" id="image_<?php the_ID();?>"
     data-likes="<?php echo $like_count; ?>"
     data-saves="<?php echo $save_count; ?>"
     data-w="<?php echo $width; ?>"
     data-h="<?php echo $height; ?>">
  <div class="options">
        <div class="save<?php echo $save_class;?>" data-id="<?php echo $pid; ?>" title="保存这个图像">
          <em></em><span>编辑</span>
        </div>
        <div class="like<?php echo $like_class;?>" data-id="<?php echo $pid; ?>" title="喜欢这个图像">
          <em></em><span>喜欢</span>
        </div>
  </div>

  <a href="<?php the_permalink(); ?>" class="imageLink">
    <img src="<?php echo URL."/uploads/images/$author_id/".$thumb;?>"
         alt="<?php the_title(); ?>"
         width="200" />
  </a>

  <div class="stats">
    <p>
      <a href="<?php the_permalink(); ?>">
        <em class="s"> </em> <span class="saves"> <?php echo $save_count; ?> </span>
        <em class="l"> </em> <span class="likes"> <?php echo $like_count; ?> </span>
      </a>
    </p>
  </div>
</div>
<?php
      }
 ?>

      </div>
    </div>

    <div id="loader">
    </div>

<?php  get_footer();  ?>