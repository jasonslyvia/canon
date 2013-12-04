<?php
/*
 *  用户个人所有的粉丝，即关注该用户的用户
 *
 *  URL形式为： /profile/用户id/子类信息
 *  子类信息包括：评论（notes）、喜欢（likes）、关注（following）和
 *  图像（个人主页默认显示所有保存的图片）
 */

require_once('functions/settings.php');
require_once(ABSPATH . '/wp-load.php');

/*======================================
获取当前 被 浏览的用户信息
======================================*/
$uid = preg_replace('/^.*?\/(\d+)\/followed\/?$/', '$1', $_SERVER['REQUEST_URI']);
$user = get_user_by('id', $uid);
if ( $user == false) {
  wp_redirect('/');
  exit();
}
else{
  $name = $user->display_name;
  $avatar = get_user_meta($uid, 'avatar', true);
}

/*======================================
构建WP_Query，选出当前被浏览用户关注的所有用户的照片
======================================*/
global $query;
global $wpdb;
$follow_record = $wpdb->get_col("
    SELECT follower_id FROM user_relation
    WHERE followee_id = {$uid}
  ");
if (count($follow_record) > 0) {
  //BUG 奇怪的wordpress bug，author__in没有正确返回参数
  $query = new WP_Query("author=".implode(',', $follow_record));
  $post_count = count($follow_record);
}
else{
  $query = null;
  $post_count = 0;
}

get_header();

?>

<script type="text/javascript">
var nonce = '<?php echo wp_create_nonce("user_pic_action_".get_current_user_id()); ?>';
</script>

<div id="luka">
  <div class="hamburger"> </div>
  <p><a href="/"> <?php echo $name .' 的所有粉丝 - '. get_bloginfo(); ?> </a> </p>
</div>

<div id="page">
  <div class="headerSpacer"> </div>
  <div id="images">
    <!-- 用户信息 -->
    <div class="tile" id="userInfo">
      <h1><b><a href="/profile/<?php echo $uid; ?>"><?php echo $name; ?></a></b></h1>
      <div class="plain-stat">
        <p>共有 <?php echo $post_count;?> 位用户关注 <?php echo($name); ?></p>
        <ul class="followees">
          <?php foreach ($follow_record as $followee) { ?>
            <li><a href="/profile/<?php echo $followee; ?>"><?php
              echo get_userdata($followee)->display_name; ?></a>
            </li>
          <?php } ?>
        </ul>
      </div>
    </div>

<?php require('functions/get_pic_grid.php'); ?>

    <div class="clear">
    </div>

  </div>
  <div class="clear">
  </div>
</div>
<div id="loader">
</div>

<?php get_footer();?>