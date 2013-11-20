<?php

/*
 * Template Name: profile
 *
 *  用户自行上传的内容默认即为保存状态
 */

//获取当前 被 浏览的用户信息
$uid = preg_replace('/^.*?\/(\d+)\/?$/', '$1', $_SERVER['REQUEST_URI']);
$user = get_user_by('id', $uid);
if ( $user == false) {
  wp_redirect('/');
  exit();
}
else{
  $name = $user->display_name;
}


//获取当前登录的用户信息
$c_user_id = get_current_user_id();


//判断当前被浏览用户与当前登录用户是否为同一人
global $same_user;
if ($user->ID == $c_user_id) {
  $same_user = true;
}




get_header();

?>

<script type="text/javascript">
var nonce = '<?php echo wp_create_nonce("user_pic_action_".get_current_user_id()); ?>';
</script>

<div id="luka">
  <div class="hamburger"> </div>
  <p><a href="/"> <?php echo get_bloginfo(); ?> </a> </p>
</div>

<div id="page">
  <div class="headerSpacer"> </div>
  <div id="images">


<?php
  //获取用户文章信息
  $args = array("author" => $uid);
  $query = new WP_Query($args);
  $post_count = $query->found_posts;
?>

    <!-- 用户信息 -->
    <div class="tile" id="userInfo">
      <h1><b><a href="/profile/<?php echo $uid; ?>"><?php echo $name; ?></a></b></h1>
      <div class="picture">
        <a href="/profile/<?php echo $uid; ?>">
          <img src="<?php echo URL.AVATAR;?>default_avatar.png"
                width="200" height="200"
                alt="<?php echo $name; ?>">
        </a>
      </div>
      <div class="statistics clearfix">
        <p>
          <a href="/profile/<?php echo $uid; ?>"><?php echo($post_count); ?><br>
          <span>图像</span></a>
        </p>
        <p>
          <a href="/profile/<?php echo $uid; ?>/notes">0<br>
          <span>评论</span></a>
        </p>
        <p>
          <a href="/profile/<?php echo $uid; ?>/likes">0<br>
          <span>喜欢</span></a>
        </p>
        <p>
          <a href="/profile/<?php echo $uid; ?>/following">0<br>
          <span>关注</span></a>
        </p>
      </div>
    </div>

<?php
  while ($query->have_posts()) {
      $query->the_post();

      $id = get_the_ID();
      $thumb = preg_replace('/(\..{3,4})$/', '_200$1', get_the_content());
      $width = get_post_meta($id, 'width', true);
      $height = get_post_meta($id, 'height', true);
      $like_count = get_post_meta($id, 'like_count', true);
      $save_count = get_post_meta($id, 'save_count', true);
?>

    <div class="polaroid tile saved" id="image_<?php echo $id; ?>"
        data-likes="<?php echo $like_count; ?>"
        data-saves="<?php echo $save_count; ?>"
        data-w="<?php echo $width; ?>"
        data-h="<?php echo $height; ?>">
      <div class="options">
        <div class="save active" data-id="<?php echo $id; ?>" title="保存这个图像">
          <em></em><span>编辑</span>
        </div>
        <div class="like" data-id="<?php echo $id; ?>" title="喜欢这个图像">
          <em></em><span>喜欢</span>
        </div>
      </div>
      <a href="<?php the_permalink(); ?>" class="imageLink">
        <img src="<?php echo URL."/uploads/images/$uid/".$thumb;?>"
              alt="<?php the_title(); ?>" width="200"
              height="<?php echo $height * 200 / $width; ?>"></a>
      <div class="stats">
        <p>
          <a href="<?php the_permalink(); ?>">
            <em class="s"></em><span class="saves"><?php echo $save_count; ?></span>
            <em class="l"></em> <span class="likes"><?php echo $like_count; ?></span>
          </a>
        </p>
      </div>
    </div>

<?php  } ?>

    <div class="clear">
    </div>

  </div>
  <div class="clear">
  </div>
</div>
<div id="loader">
</div>

<?php get_footer();?>