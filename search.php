<?php
/*
 *  搜索结果
 *  可搜索类型：图片、用户
 *  搜索结果类型：热门图片、最新图片、我的图片、用户
 *
 *  @param {string} s 搜索关键词（经过前端encodeURI）
 *  @param {string} type 用户：user  热门图片：popular  我的图片：my  默认为最新图片
 */

$term = urldecode($_GET['s']);
$type = $_GET['type'];
//若未设置搜索关键词，则跳转回上一页
if (!$term) {
  wp_redirect($_SERVER['HTTP_REFERER']);
  exit();
}

global $wpdb;
$popular_class = "";
$my_class = "";
$user_class = "";
$default_class = "";

//搜索热门图片
if ($type === "popular") {

  $result = $wpdb->get_results(
      $wpdb->prepare("
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
          left join wp_postmeta pm on pm.post_id = p.ID
          where (p.post_type = 'post' and p.post_status = 'publish')
                and
                (p.post_title LIKE '%%%s%%' OR
                (pm.meta_key = 'referrer' AND pm.meta_value LIKE '%%%s%%'))
          group by p.ID
          order by (coalesce(s.sc,0) + coalesce(l.lc, 0)) desc
        ", $term, $term), ARRAY_N
    );

  if (count($result)) {
    $query = new WP_Query(array("post__in" => array_values(call_user_func_array('array_merge', $result))));
  }
  $popular_class = " class='active'";
}


//搜索我的图片
else if ($type === "my") {
  if (!is_user_logged_in()) {
    wp_redirect($_SERVER['HTTP_REFERER']);
    exit();
  }

  $result = $wpdb->get_results(
      $wpdb->prepare("
          SELECT p.ID as pid FROM wp_posts p
          LEFT JOIN wp_postmeta pm ON pm.post_id = p.ID
          LEFT JOIN wp_users u ON u.ID IS NOT NULL
          WHERE (p.post_title LIKE '%%%s%%' OR
                (pm.meta_key = 'referrer' AND pm.meta_value LIKE '%%%s%%'))
                AND
                (p.post_type = 'post' AND p.post_status = 'publish')
                AND
                u.ID = %d
          GROUP BY pid
          ORDER BY p.post_date DESC
        ", $term, $term, get_current_user_id()), ARRAY_N
    );
  if (count($result)) {
    $query = new WP_Query(array("post__in" => array_values(call_user_func_array('array_merge', $result))));
  }

  $my_class = " class='active'";
}


//搜索用户
else if ($type === "user") {
  $user_class = " class='active'";

  $user_result = $wpdb->get_results(
      $wpdb->prepare("
          SELECT display_name, ID
          FROM wp_users
          WHERE display_name LIKE '%%%s%%'
        ", $term), ARRAY_A
    );
}


//搜索最新图片
else{
  $result = $wpdb->get_results(
      $wpdb->prepare("
          SELECT p.ID as pid FROM wp_posts p
          LEFT JOIN wp_postmeta pm ON pm.post_id = p.ID
          WHERE (p.post_title LIKE '%%%s%%' OR
                (pm.meta_key = 'referrer' AND pm.meta_value LIKE '%%%s%%'))
                AND
                (p.post_type = 'post' AND p.post_status = 'publish')
          GROUP BY pid
          ORDER BY p.post_date DESC
        ", $term, $term), ARRAY_N
    );
  if (count($result)) {
    $query = new WP_Query(array("post__in" => array_values(call_user_func_array('array_merge', $result))));
  }

  $default_class = " class='active'";
}

//显示符合搜索结果的用户数
$user_find = $wpdb->get_var(
    $wpdb->prepare("
        SELECT count(*) FROM wp_users
        WHERE display_name LIKE '%%%s%%'
      ", $term)
  );

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
    <div class="tile" id="intro">
      <h1>搜索结果<br>
      <b><?php echo $term; ?></b></h1>
      <ol class="nav">
        <li<?php echo $default_class;?>>
          <a href="/?s=<?php echo urlencode($term); ?>">最新图片</a>
        </li>
        <li<?php echo $popular_class;?>>
          <a href="/?s=<?php echo urlencode($term); ?>&type=popular">热门图片</a>
        </li>
        <?php if(is_user_logged_in()): ?>
        <li<?php echo $my_class;?>>
          <a href="/?s=<?php echo urlencode($term); ?>&type=my">我的图片</a>
        </li>
        <?php endif; ?>
        <li<?php echo $user_class;?>>
          <a href="/?s=<?php echo urlencode($term); ?>&type=user"><?php echo $user_find; ?> 位用户</a>
        </li>
      </ol>
    </div>

<?php
if ($type === "user") {
    if (count($user_result)) {
      foreach ($user_result as $user) {
        $uid = $user["ID"];
        $avatar = AVATAR. get_user_meta($uid, "avatar", true);
?>
    <div class="usertile tile">
      <h4><?php echo $user["display_name"]; ?></h4>
      <div class="picture">
        <a href="/profile/<?php echo $uid; ?>">
          <img src="<?php echo $avatar; ?>" width="200" />
        </a>
      </div>
    </div>
<?php
      }
    }
}
else{
 require('functions/get_pic_grid.php');
}
?>

    <div class="clear">
    </div>

  </div>
  <div class="clear">
  </div>
</div>
<div id="loader">
</div>

<?php get_footer();?>