<?php

/****************************************\

        常量定义

\*****************************************/
define("URL", get_template_directory_uri());
define("HOME", get_settings('home'));
define("AVATAR", '/uploads/avatar/');





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

    if (is_page('profile')) {
        $uid = preg_replace('/^.*?\/(\d+)\/?$/', '$1', $_SERVER['REQUEST_URI']);
        $user = get_user_by('id', $uid);
        $name = $user->display_name;
        $title = "{$name} _ ". $title;
    }

    return $title;
}

?>