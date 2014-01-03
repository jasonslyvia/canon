<?php
/*
 *  加载更多内容
 *
 *  @param {string} type
 *    index_popular:首页热门  index_recent:首页最新
 *    user_save:用户保存  user_like:用户喜欢  user_note:用户评论
      user_following:用户关注  user_followed:用户粉丝
 *    search_recent_x:搜索最新  search_popular_x:搜索热门
      search_user_x:搜索用户  search_my_x:搜索我的图片
          x为搜索关键词
 *    user_activity:用户动态
 *    category_x:主题x
          x为category id
 *  @param {int} page 页数
 */

header('Content-Type: text/html');
require_once('get_data.php');

if (isset($_GET['type']) && isset($_GET['page'])) {
    $type = $_GET['type'];
    $page = $_GET['page'];
    $user_id = $_GET['userId'];

    if (preg_match('/category/i', $type)) {
        $type = explode('_', $type);
        $cat_id = $type[1];
        $type = $type[0];
    }
    else if (preg_match('/search/i', $type)) {
        $type = explode('_', $type);
        $term = $type[2];
        $type = $type[0] . '_' . $type[1];
    }

    switch ($type) {
        case 'index_popular':{
            get_index_popular_image($page);
        }
        break;
        case 'index_recent':{
            get_index_recent_image($page);
        }
        break;
        case 'user_save':{
            get_user_saved_image($user_id, $page, true);
        }
        break;
        case 'user_like':{
            get_user_liked_image($user_id, $page, true);
        }
        break;
        case 'user_note':{
            get_user_comment_image($user_id, $page, true);
        }
        break;
        case 'user_following':{
            get_user_following_image($user_id, $page, true);
        }
        break;
        case 'user_followed':{
            get_user_followed_image($user_id, $page, true);
        }
        break;
        case 'search_my':{
            get_search_my_image($term, $page, true);
        }
        break;
        case 'search_popular':{
            get_search_popular_image($term, $page, true);
        }
        break;
        case 'search_user':{
            get_search_user($term, $page, true);
        }
        break;
        case 'search_recent':{
            get_search_recent_image($term, $page, true);
        }
        break;
        case 'user_activity':{
            get_activity($user_id, $page, true, true);
        }
        break;
        case 'category':{
            get_category_image($cat_id, $page, true);
        }
        break;
        //类型均不符合，按照无更多内容返回
        default:
            echo '<div id="noMoreImages"></div>';
            break;
    }
}

 ?>