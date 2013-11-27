<?php
/*
 * Template Name: profile
 *
 *  用户自行上传的内容默认即为保存状态
 */

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

//获取保存数和赞同数
global $wpdb;
$user_like_count = $wpdb->get_var("
    SELECT count(*) FROM pic_like
    WHERE user_id = $uid
  ");

$user_save_count = $wpdb->get_var("
    SELECT count(*) FROM pic_save
    WHERE user_id = $uid
  ");


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

/*======================================
构建WP_Query，选出当前被浏览用户保存的所有图片
======================================*/
$saved_record = $wpdb->get_col("
    SELECT pic_id as p FROM pic_save
    WHERE user_id = {$uid}
  ");

$args = array("post__in" => $saved_record);
$query = new WP_Query($args);
$post_count = $query->found_posts;

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
          <a href="/profile/<?php echo $uid; ?>/likes"><?php echo $user_like_count; ?><br>
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
    //author_id决定了当前图片的储存位置
    $author_id = get_the_author_meta('ID');
    $thumb = preg_replace('/(\..{3,4})$/', '_200$1', get_the_content());
    $width = get_post_meta($id, 'width', true);
    $height = get_post_meta($id, 'height', true);
    $like_count = get_post_meta($id, 'like_count', true);
    $save_count = get_post_meta($id, 'save_count', true);

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

    <div class="polaroid tile saved" id="image_<?php echo $id; ?>"
        data-likes="<?php echo $like_count; ?>"
        data-saves="<?php echo $save_count; ?>"
        data-w="<?php echo $width; ?>"
        data-h="<?php echo $height; ?>">
      <div class="options">
        <div class="save<?php echo $save_class;?>" data-id="<?php echo $id; ?>" title="保存这个图像">
          <em></em><span>编辑</span>
        </div>
        <div class="like<?php echo $like_class;?>" data-id="<?php echo $id; ?>" title="喜欢这个图像">
          <em></em><span>喜欢</span>
        </div>
      </div>
      <a href="<?php the_permalink(); ?>" class="imageLink">
        <img src="<?php echo URL."/uploads/images/$author_id/".$thumb;?>"
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