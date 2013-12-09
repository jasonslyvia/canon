<?php

require('functions/settings.php');
require(ABSPATH . '/wp-load.php');

if (!is_user_logged_in()) {
    wp_redirect('/');
    exit();
}

//用户只能查看自己的动态
$c_user_id = get_current_user_id();
$user_id = preg_replace('/^\/profile\/(\d+)\/activity\/?$/i',
                        '$1',
                        $_SERVER['REQUEST_URI']);
if ($c_user_id != $user_id) {
  wp_redirect("/profile/{$c_user_id}/activity");
  exit();
}

get_header();

date_default_timezone_set('UTC');
//首先选出该用户的所有图片id
global $wpdb;
$c_user_pics = $wpdb->get_results("
  SELECT ID FROM wp_posts
  WHERE post_author = {$c_user_id}
  ", ARRAY_N);

//然后选出保存和喜欢了这些图片的所有用户id、昵称及操作时间
$c_user_pics_id = implode(',', call_user_func_array('array_merge', $c_user_pics));
$activity_result = $wpdb->get_results("
  select
      op.user_id as uid,
      u.display_name as name,
      p.post_title as title,
      p.post_content as content,
      pm.meta_value as avatar,
      pic_id as pid, time, event
  from (
      select user_id, pic_id, time, '保存' as event
      from pic_save
      where pic_id in ({$c_user_pics_id})
      union all
      select user_id, pic_id, time, '喜欢' as event
      from pic_like
      where pic_id in ({$c_user_pics_id})
      union all
      select follower_id as user_id, '' as pic_id, time, '关注' as event
      from user_relation
      where followee_id = {$c_user_id}
  ) as op
  left join wp_users u on u.ID = op.user_id
  left join wp_posts p on p.ID = pic_id
  left join wp_usermeta pm on (pm.user_id = op.user_id and pm.meta_key = 'avatar_small')
  order by time desc
  ");
?>
<div id="luka">
  <div class="hamburger"> </div>
  <p> <a href="#"> 动态 - <?php echo get_bloginfo(); ?> </a> </p>
</div>

<div id="page">

  <div class="headerSpacer"> </div>
  <div id="maincontent" class="center" style="width:700px;">
<?php if (count($activity_result) != 0) {
  foreach ($activity_result as $activity) {
    $uid = $activity->uid;
    //若操作由本人进行则忽略
    if ($uid == $c_user_id) {
      continue;
    }
?>
    <div class="wrapSignupForm">
      <a href="/profile/<?php echo $uid; ?>" class="avatar">
           <img src="<?php echo AVATAR.$activity->avatar ?>"
           width="30" height="30" />
      </a>
      <span class="date"><?php echo human_time_diff(strtotime($activity->time),
                                                    current_time('timestamp')); ?>前</span>
      <div class="activity-detail">
        <div class="activity-text">
          <a href="/profile/<?php echo $uid ?>"><?php echo $activity->name; ?></a>
          <?php if ($activity->event != "关注") { ?>
          <span class="event"><?php echo $activity->event; ?></span>了你的图片
          <div class="image-preview">
            <a href="/?p=<?php echo $activity->pid; ?>">
              <img src="<?php echo IMAGE_PATH.$c_user_id.'/'.
                                    preg_replace('/(\..{3,4})$/',
                                                  '_200$1',
                                                  $activity->content); ?>"
                   width="200" />
            </a>
          </div>
          <?php }else{ ?>
          <span class="event">关注</span>了你
          <?php } ?>
      </div>
    </div>
  </div>
<?php  }
}
else{ ?>
    <div class="notification">当有人关注您，或保存、喜欢您的图片时，您都可以在这里看到。</div>
<?php } ?>  </div>
</div>

<div id="loader">
</div>

<?php get_footer(); ?>