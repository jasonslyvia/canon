<?php

/****************************************\

        常量定义

\*****************************************/
define("URL", get_template_directory_uri());
define("HOME", get_settings('home'));
//用户头像
define("AVATAR", '/wp-content/themes/canon/uploads/avatar/');
//用户上传图像储存相对地址
define('IMAGE_PATH', '/wp-content/themes/canon/uploads/images/');





/****************************************\

        重写URL
        !!!该函数仅支持apache .htaccess重写
            nginx服务器需手动配置重写规则

\*****************************************/
add_action('generate_rewrite_rules', 'themes_dir_add_rewrites');
function themes_dir_add_rewrites() {
  $theme_name = next(explode('/themes/', get_stylesheet_directory()));

  global $wp_rewrite;
  $new_non_wp_rules = array(
    'signup/?$' => 'wp-content/themes/'. $theme_name . '/signup.php',
    'login/?$' => 'wp-content/themes/'. $theme_name . '/login.php',
    'upload/?$' => 'wp-content/themes/'. $theme_name . '/upload.php',
    'profile/\d/?$' => 'wp-content/themes/' . $theme_name . '/user-profile.php',
    'profile/\d/notes/?$' => 'wp-content/themes/' . $theme_name . '/user-notes.php',
    'profile/\d/likes/?$' => 'wp-content/themes/' . $theme_name . '/user-likes.php',
    'profile/\d/following/?$' => 'wp-content/themes/' . $theme_name . '/user-following.php',
    'profile/\d/followed/?$' => 'wp-content/themes/' . $theme_name . '/user-followed.php',
    'settings/?$' => 'wp-content/themes/' . $theme_name . '/user-setting.php',
  );
  $wp_rewrite->non_wp_rules += $new_non_wp_rules;
}

//定义几个检测当前页面的工具函数
function is_login(){
    return preg_match('/^\/login\/?$/i', $_SERVER['REQUEST_URI']);
}
function is_signup(){
    return preg_match('/^\/signup\/?$/i', $_SERVER['REQUEST_URI']);
}
//is_profile 同时判断了用户的个人主页、用户的评论等子类信息页面
function is_profile(){
    return preg_match('/^\/profile\/\d+(\/)?(\w+)?$/i', $_SERVER['REQUEST_URI']);
}
function is_upload(){
    return preg_match('/^\/upload\/?$/i', $_SERVER['REQUEST_URI']);
}
function is_settings(){
    return preg_match('/^\/settings\/?$/i', $_SERVER['REQUEST_URI']);
}
function is_current_following(){
    $uid = preg_replace('/^\/profile\/(\d+)\/following\/?$/i',
                        '$1',
                        $_SERVER['REQUEST_URI']);
    $c_uid = get_current_user_id();
    return $uid == $c_uid;
}
function is_recent(){
    return $_SERVER['REQUEST_URI'] == '/?type=recent';
}
/*****************************************\

        处理各种action及filter

\*****************************************/
//自定义title
add_action('wp_title', 'rw_title', 10, 3);
function rw_title($title, $sep, $direction){
    global $page, $paged;

    if ($direction == 'right') {
        $title .= get_bloginfo('name');
    }
    else{
        $title = get_bloginfo('name').$title;
    }

    $desc = get_bloginfo('description', 'display');
    if ($desc && (is_home() || is_front_page())) {
        $title .= "{$sep}{$desc}";
    }

    if ($paged >=2 || $page >= 2 && !is_page('profile')) {
        $title .= "{$sep}"."第".max($page, $paged)."页";
    }

    if (is_profile()) {
        $uid = preg_replace('/^.*?\/(\d+)(\/)?(\w+)?$/', '$1', $_SERVER['REQUEST_URI']);
        $user = get_user_by('id', $uid);
        $name = $user->display_name;
        $title = "{$name} _ ". $title;
    }

    return $title;
}

?>