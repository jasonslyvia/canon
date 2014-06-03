<?php
error_reporting(0);

/****************************************\

        常量定义

\*****************************************/
define("URL", get_template_directory_uri());
define("HOME", get_settings('home'));
//用户头像
define("AVATAR", '/wp-content/themes/canon/uploads/avatar/');
//用户上传图像储存相对地址
define('IMAGE_PATH', '/wp-content/themes/canon/uploads/images/');


function canon_get_avatar($user_id, $type){
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    $avatar = get_user_meta($user_id, $type, true);
    if (preg_match('/^http/i', $avatar)) {
        return $avatar;
    }
    else if(count($avatar) > 0 && !empty($avatar) && $avatar != ''){
        return AVATAR.$avatar;
    }
    else{
        return AVATAR.'/default_'.$type.'.png';
    }
}

function canon_get_image($image_id, $small = false){
    $post = get_post($image_id);
    $image = $post->post_content;
    if ($small) {
        $image = preg_replace('/(\..{3,4})$/', '_200$1', $image);
    }
    $author = $post->post_author;
    return HOME.IMAGE_PATH.$author.'/'.$image;
}

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
    'forgetpassword/?$' => 'wp-content/themes/'. $theme_name . '/forget-password.php',
    'plugin/.*$' => 'wp-content/themes/'. $theme_name . '/plugin.php',
    'upload/?$' => 'wp-content/themes/'. $theme_name . '/upload.php',
    'edit' => 'wp-content/themes/'. $theme_name . '/edit.php',
    'admin-front/?$' => 'wp-content/themes/'. $theme_name . '/admin-front.php',
    'categories/?$' => 'wp-content/themes/'. $theme_name . '/category-all.php',
    'color/?$' => 'wp-content/themes/'. $theme_name . '/color-all.php',
    'profile/\d+/?$' => 'wp-content/themes/' . $theme_name . '/user-profile.php',
    'profile/\d+/notes/?$' => 'wp-content/themes/' . $theme_name . '/user-notes.php',
    'profile/\d+/likes/?$' => 'wp-content/themes/' . $theme_name . '/user-likes.php',
    'profile/\d+/following/?$' => 'wp-content/themes/' . $theme_name . '/user-following.php',
    'profile/\d+/followed/?$' => 'wp-content/themes/' . $theme_name . '/user-followed.php',
    'profile/\d+/activity/?$' => 'wp-content/themes/' . $theme_name . '/user-activity.php',
    'settings/?$' => 'wp-content/themes/' . $theme_name . '/user-setting.php',
    'clear-evil-images' => 'wp-content/themes/' . $theme_name . '/functions/clear-evil-images.php',
  );
  $wp_rewrite->non_wp_rules += $new_non_wp_rules;
}

//定义几个检测当前页面的工具函数
function is_login(){
    return preg_match('/^\/(login|forgetpassword)/i', $_SERVER['REQUEST_URI']);
}
function is_signup(){
    return preg_match('/^\/signup/i', $_SERVER['REQUEST_URI']);
}
function is_edit(){
    return preg_match('/^\/edit/i', $_SERVER['REQUEST_URI']);
}
//is_profile 同时判断了用户的个人主页、用户的评论等子类信息页面
function is_profile(){
    return preg_match('/^\/profile\/\d+(\/)?(\w+)?$/i', $_SERVER['REQUEST_URI']);
}
function is_activity(){
    return preg_match('/^\/profile\/\d+\/activity\/?$/i', $_SERVER['REQUEST_URI']);
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
function is_custom_category(){
    return preg_match('/^\/categor.+?\/?$/i', $_SERVER['REQUEST_URI']);
}
function is_admin_front(){
    return preg_match('/^\/admin-front\/?$/i', $_SERVER['REQUEST_URI']);
}
function is_plugin(){
    return preg_match('/^\/plugin.*$/i', $_SERVER['REQUEST_URI']);
}
function is_color(){
    return preg_match('/^\/color\/?$/i', $_SERVER['REQUEST_URI']);
}


//检查当前用户是否为管理员
function is_current_user_admin(){
    $user = wp_get_current_user();
    return in_array("administrator", (array)$user->roles);
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

//隐藏部分后台设置选项
function remove_menus(){
  remove_menu_page( 'index.php' );                  //Dashboard
  remove_menu_page( 'upload.php' );                 //Media
  remove_menu_page( 'edit-comments.php' );          //Comments
  remove_menu_page( 'themes.php' );                 //Appearance
  remove_menu_page( 'plugins.php' );                //Plugins
  remove_menu_page( 'tools.php' );                  //Tools
}
add_action( 'admin_menu', 'remove_menus' );

?>