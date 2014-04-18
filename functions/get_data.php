<?php
/*
 *  用于网站各种模块的图片数据获取（Grid/Tile）
 *
 *  - 首页（热门、最新）
 *  - 个人（保存、喜欢、评论、关注、粉丝）
 *  - 搜索（热门、最新、我的图片、用户）
 *  - 主题
 *  - 动态
 */

define('PAGE_SIZE', 10);
define('WP_USE_THEMES', false);
require_once('settings.php');
require_once(CANON_ABSPATH.'wp-load.php');

$page_size = PAGE_SIZE;


/***************************************************
 *  首页
 **************************************************/

//首页热门
//获取热门图片排序
//计算方法：保存数+喜欢数的和按倒序排列
//可选参数 评论数、浏览量
function get_index_popular_image($page = 1){
    global $wpdb;
    global $query;

    $limit = --$page * PAGE_SIZE;
    $page_size = PAGE_SIZE;

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
        limit {$limit},{$page_size}
    ", ARRAY_N);

    if (count($hot_result) === 0) {
        return_no_result();
    }

    $hot_ids = array_values(call_user_func_array('array_merge', $hot_result));
    $query = new WP_Query(array("post__in" => $hot_ids,
                                "orderby" => "none"));
    $query->ad = should_display_ad($page, "index");
    require('get_pic_grid.php');
}

//首页最新
function get_index_recent_image($page = 1){
    global $wpdb;
    global $query;

    $page_size = PAGE_SIZE;

    $query = new WP_Query(array("posts_per_page" => $page_size,
                                "offset" => --$page * $page_size));
    if (!$query->have_posts()) {
        return_no_result();
    }

    $query->ad = should_display_ad($page, "index");
    require('get_pic_grid.php');
}



/***************************************************
 *  个人
 **************************************************/

//个人保存
function get_user_saved_image($user_id, $page = 1, $display = true){
    global $wpdb;
    global $query;

    $page_size = PAGE_SIZE;
    $limit = --$page * $page_size;

    $saved_record = $wpdb->get_col("
        SELECT pic_id as p FROM pic_save
        WHERE user_id = {$user_id}
        LIMIT {$limit},{$page_size}
      ");

    if (count($saved_record) == 0) {
      $query = null;
      $post_count = 0;

      if ($display) {
          return_no_result();
      }
    }
    else{
      $args = array("post__in" => $saved_record,
                    "posts_per_page" => PAGE_SIZE);
      $query = new WP_Query($args);

      $post_count = $query->found_posts;
    }

    if ($display) {
        $query->ad = should_display_ad($page, "user");
        require('get_pic_grid.php');
    }
    else{
        return array($query, $post_count);
    }
}

//个人粉丝
function get_user_followed_image($user_id, $page = 1, $display = true){
    global $query;
    global $wpdb;

    $page_size = PAGE_SIZE;
    $limit = --$page * $page_size;

    $follow_record = $wpdb->get_col("
        SELECT follower_id FROM user_relation
        WHERE followee_id = {$user_id}
        LIMIT {$limit},{$page_size}
      ");

    if (count($follow_record) > 0) {
      $query = new WP_Query(array("author__in" => $follow_record,
                                  "posts_per_page" => PAGE_SIZE));
      $post_count = count($follow_record);
    }
    else{
      $query = null;
      $post_count = 0;

      if ($display) {
          return_no_result();
      }
    }

    if ($display) {
        $query->ad = should_display_ad($page, "user");
        require('get_pic_grid.php');
    }
    else{
        return array($query, $post_count, $follow_record);
    }
}

//个人关注
function get_user_following_image($user_id, $page = 1, $display = true){
    global $query;
    global $wpdb;

    $page_size = PAGE_SIZE;
    $limit = --$page * PAGE_SIZE;

    $follow_record = $wpdb->get_col("
        SELECT followee_id FROM user_relation
        WHERE follower_id = {$user_id}
        LIMIT {$limit},{$page_size}
      ");

    if (count($follow_record) > 0) {
      $query = new WP_Query(array("author__in" => $follow_record,
                                  "posts_per_page" => PAGE_SIZE));
      $post_count = count($follow_record);
    }
    else{
      $query = null;
      $post_count = 0;

      if ($display) {
          return_no_result();
      }
    }

    if ($display) {
        $query->ad = should_display_ad($page, "user");
        require('get_pic_grid.php');
    }
    else{
        return array($query, $post_count, $follow_record);
    }
}

//个人喜欢
function get_user_liked_image($user_id, $page = 1, $display = true){
    global $query;
    global $wpdb;

    $page_size = PAGE_SIZE;
    $limit = --$page * $page_size;

    $like_record = $wpdb->get_col("
        SELECT pic_id FROM pic_like
        WHERE user_id = {$user_id}
        LIMIT {$limit},{$page_size}
      ");

    if (count($like_record) > 0) {
      $args = array("post__in" => $like_record,
                    "posts_per_page" => PAGE_SIZE);
      $query = new WP_Query($args);
      $post_count = $query->found_posts;
    }
    else{
      $query = null;
      $post_count = 0;

      if ($display) {
          return_no_result();
      }
    }

    if ($display) {
        $query->ad = should_display_ad($page, "user");
        require('get_pic_grid.php');
    }
    else{
        return array($query, $post_count);
    }
}

//个人评论
function get_user_comment_image($user_id, $page = 1, $display = true){
    global $query;
    global $wpdb;

    //获取用户的所有评论
    $comments = get_comments(array("user_id" => $user_id));
    if (count($comments) > 0) {
      $comment_post_array = array();
      //获取每一条评论对应的图片id
      foreach ($comments as $comment) {
        array_push($comment_post_array, $comment->comment_post_ID);
      }
      $args = array("post__in" => $comment_post_array,
                    "posts_per_page" => PAGE_SIZE,
                    "offset" => --$page * PAGE_SIZE);
      $query = new WP_Query($args);
      $post_count = count($comments);

      //若没有更多图片
      if (!$query->have_posts() && $display) {
          return_no_result();
      }
    }
    else{
      $query = null;
      $post_count = 0;
    }

    if ($display) {
        $query->ad = should_display_ad($page, "user");
        require('get_pic_grid.php');
    }
    else{
        return array($query, $post_count);
    }
}


/***************************************************
 *  搜索
 **************************************************/

//搜索热门图片
function get_search_popular_image($term, $page = 1, $display = true){
    global $wpdb;
    global $query;

    $page_size = PAGE_SIZE;
    $limit = --$page * $page_size;

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
          limit {$limit},{$page_size}
        ", $term, $term), ARRAY_N
    );

    if (count($result)) {
    $query = new WP_Query(array("post__in" => array_values(call_user_func_array('array_merge', $result)),
                                "posts_per_page" => PAGE_SIZE,
                                "orderby" => "none"));
    }
    else if($display){
        return_no_result();
    }

    if ($display) {
        $query->ad = should_display_ad($page, "search");
        require('get_pic_grid.php');
    }
    else{
        return array($query);
    }
}

//搜索最新图片
function get_search_recent_image($term, $page = 1, $display = true){
    global $wpdb;
    global $query;

    $page_size = PAGE_SIZE;
    $limit = --$page * $page_size;

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
          LIMIT {$limit},{$page_size}
        ", $term, $term), ARRAY_N
    );

    if (count($result)) {
        $query = new WP_Query(array("post__in" => array_values(call_user_func_array('array_merge', $result)),
                                    "posts_per_page" => PAGE_SIZE));
    }
    else if ($display) {
        return_no_result();
    }

    if ($display) {
        $query->ad = should_display_ad($page, "search");
        require('get_pic_grid.php');
    }
    else{
        return array($query);
    }
}

//搜索我的图片
function get_search_my_image($term, $page = 1, $display = true){
    global $wpdb;
    global $query;

    $page_size = PAGE_SIZE;
    $limit = --$page * $page_size;

    $result = $wpdb->get_results(
      $wpdb->prepare("
          SELECT p.ID as pid FROM pic_save pc
          LEFT JOIN wp_posts p ON pc.pic_id = p.ID
          LEFT JOIN wp_postmeta pm ON pm.post_id = p.ID
          WHERE (p.post_title LIKE '%%%s%%' OR
                (pm.meta_key = 'referrer' AND pm.meta_value LIKE '%%%s%%'))
                AND
                (p.post_type = 'post' AND p.post_status = 'publish')
                AND
                pc.user_id = %d
          GROUP BY pid
          ORDER BY p.post_date DESC
          LIMIT {$limit},{$page_size}
        ", $term, $term, get_current_user_id()), ARRAY_N
    );

    if (count($result)) {
        $query = new WP_Query(array("post__in" => array_values(call_user_func_array('array_merge', $result)),
                                    "posts_per_page" => PAGE_SIZE));
    }
    else if ($display) {
        return_no_result();
    }

    if ($display) {
        $query->ad = should_display_ad($page, "search");
        require('get_pic_grid.php');
    }
    else{
        return array($query);
    }
}

//搜索用户
function get_search_user($term, $page = 1, $display = true){
    global $wpdb;

    $page_size = PAGE_SIZE;
    $limit = --$page * $page_size;

    $user_result = $wpdb->get_results(
        $wpdb->prepare("
              SELECT display_name, ID
              FROM wp_users
              WHERE display_name LIKE '%%%s%%'
              LIMIT {$limit},{$page_size}
            ", $term), ARRAY_A
    );

    if ($display) {
        if (count($user_result)) {
            foreach ($user_result as $user) {
                $uid = $user["ID"];
                $avatar = canon_get_avatar($uid, "avatar");
                echo <<<html
            <div class="usertile tile">
              <h4>{$user["display_name"]}</h4>
              <div class="picture">
                <a href="/profile/{$uid}">
                  <img src="{$avatar}" width="200" />
                </a>
              </div>
            </div>
html;
            }
        }
        else{
            return_no_result();
        }
    }
    else{
        return $user_result;
    }
}


/***************************************************
 *  主题
 **************************************************/

function get_category_image($category, $page = 1, $display = true){
    global $wpdb;

    $query = new WP_Query(array("category__in" => array($category),
                                "posts_per_page" => PAGE_SIZE,
                                "offset" => --$page * PAGE_SIZE));

    if (!$query->have_posts() && $display) {
        return_no_result();
    }

    if ($display) {
        $query->ad = should_display_ad($page, "search");
        require_once('get_pic_grid.php');
    }
    else{
        return $query;
    }
}


/***************************************************
 *  动态
 **************************************************/
function get_activity($user_id, $page = 1, $display = true, $ajax = false){
    global $wpdb;

    date_default_timezone_set('UTC');
    //首先选出该用户的所有图片id
    global $wpdb;
    $c_user_pics = $wpdb->get_results("
      SELECT ID FROM wp_posts
      WHERE post_author = {$user_id}
            AND post_status = 'publish'
            AND post_type = 'post'
      ", ARRAY_N);

    if (count($c_user_pics) > 0) {
      //然后选出保存和喜欢了这些图片的所有用户id、昵称及操作时间
      $c_user_pics_id = implode(',', call_user_func_array('array_merge', $c_user_pics));
    }
    else{
      $c_user_pics_id = '';
    }

    $limit = --$page * PAGE_SIZE;
    $page_size = PAGE_SIZE;

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
          where pic_id in ({$c_user_pics_id}) and user_id != {$user_id}
          union all
          select user_id, pic_id, time, '喜欢' as event
          from pic_like
          where pic_id in ({$c_user_pics_id}) and user_id != {$user_id}
          union all
          select follower_id as user_id, 0 as pic_id, time, '关注' as event
          from user_relation
          where followee_id = {$user_id}
      ) as op
      left join wp_users u on u.ID = op.user_id
      left join wp_posts p on (p.ID = pic_id and p.post_status = 'publish')
      left join wp_usermeta pm on (pm.user_id = op.user_id and pm.meta_key = 'avatar_small')
      order by time desc
      limit {$limit},{$page_size}
    ");

    if ($display) {
        //若存在动态内容
        if (count($activity_result) != 0) {
          foreach ($activity_result as $activity) {
            $uid = $activity->uid;
            $pid = $activity->pid;

            //常量heredoc
            $activity->avatar = canon_get_avatar($uid, 'avatar_small');

        if ($activity->event != "关注") {

            $image = canon_get_image($pid, true);
            $message = <<<message
<span class="event">{$activity->event}</span>了你的图片
<div class="image-preview">
<a href="/?p={$activity->pid}">
  <img src="{$image}" width="200" />
</a>
</div>
message;
        }
        else{
            $message = '<span class="event">关注</span>了你';
        }

        $time = human_time_diff(strtotime($activity->time),
                                current_time('timestamp'));
        echo <<<html
<div class="wrapSignupForm">
  <a href="/profile/{$uid}" class="avatar">
       <img src="{$activity->avatar}"
       width="30" height="30" />
  </a>
  <span class="date">{$time}前</span>
  <div class="activity-detail">
    <div class="activity-text">
      <a href="/profile/{$uid}">{$activity->name}</a>
      {$message}
  </div>
</div>
</div>
html;
            }
        }
        else if ($ajax) {
            return_no_result();
        }
        else{
            echo '<div class="notification">当有人关注您，或保存、喜欢您的图片时，您都可以在这里看到。</div>';
        }
    }
    else{
        return $activity_result;
    }
}


/***************************************************
 *  颜色
 **************************************************/

function get_color_image($color, $page = 1, $display = true){
    global $wpdb;

    $args = array(
     'meta_query' => array(
         array(
             'key' => 'color',
             'value' => $color,
             'compare' => 'LIKE',
         )
     ),
      "posts_per_page" => PAGE_SIZE,
      "offset" => --$page * PAGE_SIZE
    );
    $query = new WP_Query($args);

    if (!$query->have_posts() && $display) {
        return_no_result();
    }

    if ($display) {
        require_once('get_pic_grid.php');
    }
    else{
        return $query;
    }
}


//当没有内容时返回的信息
function return_no_result(){
    echo '<div id="noMoreImages"></div>';
    exit();
}

/*
 *  根据内容的类型和数量判断是否显示广告
 *
 *  @param {int} page 当前显示的页数
 *  @param {string} type 内容的类型
 *  @return {bool}
 */
function should_display_ad($page = 1, $type = "index"){
  if ($page == 0 && $type == "index") {
    return false;
  }
  $ads_arr = array("index", "search", "category");
  if ($page % 2 == 0 && in_array($type, $ads_arr)) {
    return true;
  }
  else{
    return false;
  }
}