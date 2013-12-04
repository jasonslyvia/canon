<?php
/*
 *  用户个人主页
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
$uid = preg_replace('/^.*?\/(\d+)\/?$/', '$1', $_SERVER['REQUEST_URI']);
$user = get_user_by('id', $uid);
if ( $user == false) {
  wp_redirect('/');
  exit();
}
else{
  $name = $user->display_name;
  $avatar = get_user_meta($uid, 'avatar', true);
}

//获取当前 被 浏览用户的保存数、赞同数、关注数和评论数
global $wpdb;
$user_like_count = $wpdb->get_var("
    SELECT count(*) FROM pic_like
    WHERE user_id = $uid
  ");

$user_save_count = $wpdb->get_var("
    SELECT count(*) FROM pic_save
    WHERE user_id = $uid
  ");
//关注数
$follow_count = $wpdb->get_var("
    SELECT count(*) FROM user_relation
    WHERE follower_id = $uid
  ");
//粉丝数
$followed_count = $wpdb->get_var("
    SELECT count(*) FROM user_relation
    WHERE followee_id = $uid
  ");

$comments = get_comments(array("user_id" => $uid));
$comment_count = count($comments);


$c_user_id = get_current_user_id();
//判断当前被浏览用户与当前登录用户是否为同一人，若是则不显示关注按钮
//同时用于header.php中菜单栏的高亮
global $same_user;
if ($user->ID == $c_user_id) {
  $same_user = true;
}

/*======================================
构建WP_Query，选出当前被浏览用户保存的所有图片
======================================*/
$saved_record = $wpdb->get_col("
    SELECT pic_id as p FROM pic_save
    WHERE user_id = {$uid}
  ");

global $query;
if (count($saved_record) == 0) {
  $query = null;
  $post_count = 0;
}
else{
  $args = array("post__in" => $saved_record);
  $query = new WP_Query($args);
  $post_count = $query->found_posts;
}

get_header();

?>

<script type="text/javascript">
var nonce = '<?php echo wp_create_nonce("user_pic_action_{$c_user_id}"); ?>';
</script>

<div id="luka">
  <div class="hamburger"> </div>
  <p><a href="/"> <?php echo get_bloginfo(); ?> </a> </p>
</div>

<div id="page">
  <div class="headerSpacer"> </div>
  <div id="images">
    <!-- 用户信息 -->
    <div class="tile" id="userInfo">
      <h1><b><a href="/profile/<?php echo $uid; ?>"><?php echo $name; ?></a></b></h1>
      <div class="picture">
        <a href="/profile/<?php echo $uid; ?>">
          <img src="<?php echo AVATAR.$avatar;?>"
                width="200" height="200"
                alt="<?php echo $name; ?>">
        </a>
      </div>
      <?php if (!$same_user) {
          // 若不是同一用户，则判断当前用户关注情况
          $is_following = $wpdb->get_var("
            SELECT count(*) FROM user_relation
            WHERE follower_id = {$c_user_id} AND
                  followee_id = {$uid}
          ");
          if ($is_following != 0) {
            $follow_class = " active";
            $follow_text = "已关注";
          }
          else{
            $follow_class = "";
            $follow_text = "关 注";
          }
      ?>
        <div class="options">
          <button type="button" class="follow blue<?php echo $follow_class;?>"
                  data-type="1"
                  data-id="<?php echo $uid; ?>"><?php echo $follow_text; ?></button>
        </div>
      <?php } ?>
      <div class="statistics clearfix">
        <p>
          <a href="/profile/<?php echo $uid; ?>"><?php echo($post_count); ?><br>
          <span>图像</span></a>
        </p>
        <p>
          <a href="/profile/<?php echo $uid; ?>/notes"><?php echo $comment_count; ?><br>
          <span>评论</span></a>
        </p>
        <p>
          <a href="/profile/<?php echo $uid; ?>/likes"><?php echo $user_like_count; ?><br>
          <span>喜欢</span></a>
        </p>
        <p>
          <a href="/profile/<?php echo $uid; ?>/following"><?php echo $follow_count; ?><br>
          <span>关注</span></a>
        </p>
        <p>
          <a href="/profile/<?php echo $uid; ?>/followed"><?php echo $followed_count; ?><br>
          <span>粉丝</span></a>
        </p>
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