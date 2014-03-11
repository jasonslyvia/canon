<!doctype html>
<!--[if lt IE 7 ]>
<html class="no-js ie6">
<![endif]-->
<!--[if IE 7 ]>
<html class="no-js ie7">
<![endif]-->
<!--[if IE 8 ]>
<html class="no-js ie8">
<![endif]-->
<!--[if IE 9 ]>
<html class="no-js ie9">
<![endif]-->
<!--[if (gt IE 9)|!(IE)]>
<!-->
<html class="no-js">
<!--
<![endif]-->
<head>
    <meta charset="utf-8">
    <title><?php wp_title("_", true, 'right');?></title>

    <meta name="robots" content="index,follow" />
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
    <meta name="google" content="notranslate" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />

    <link rel="shortcut icon" href="<?php echo URL;?>/favicon.ico"/>

    <link rel="stylesheet" href="/wp-content/themes/canon/style.e0fa0f4c.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/wp-content/themes/canon/css-dist/home.92f350b0.css" type="text/css" media="all" />
    <?php if(is_login() || is_upload() || is_signup() || is_settings() || is_activity() || is_page() || is_edit() || is_plugin()): ?>
    <link rel="stylesheet" href="/wp-content/themes/canon/css-dist/login.1555b1e7.css" type="text/css" media="all" />
    <?php endif; ?>
    <?php if (is_profile() && !is_activity()): ?>
    <link rel="stylesheet" href="/wp-content/themes/canon/css-dist/user.99e456e7.css" type="text/css" media="all" />
    <?php endif; ?>
    <?php if (is_single()): ?>
    <link rel="stylesheet" href="/wp-content/themes/canon/css-dist/image.da7013af.css" type="text/css" media="all" />
    <?php endif ?>
    <?php if (is_custom_category()): ?>
    <link rel="stylesheet" href="/wp-content/themes/canon/css-dist/overview.1a96f5e0.css" type="text/css" media="all" />
    <?php endif ?>
    <?php if (is_search() || is_admin_front()): ?>
    <link rel="stylesheet" href="/wp-content/themes/canon/css-dist/search.daf569c6.css" type="text/css" media="all" />
    <?php endif; ?>

    <script src="/wp-content/themes/canon/js-dist/modernizr.cf69c6f2.js"></script>
    <script type="text/javascript">
      var ABSPATH = '<?php echo URL;?>';
    </script>
</head>
<body class="<?php if(is_user_logged_in()) echo "auth";
           if ($_COOKIE['hidenav'] == 'true' || is_plugin()) { echo " hidenav"; } ?>">
    <!--  导航栏   -->
    <div id="kaori" class="nodrag">

      <div class="search">
        <input type="text" name="s" />
      </div>

      <?php if (is_user_logged_in()): $user = wp_get_current_user(); ?>
      <div class="profileImage">
      <a href="/profile/<?php echo $user->ID; ?>">
        <img src="<?php echo AVATAR.get_user_meta($user->ID, 'avatar_small', true);?>"
              width="45" height="45" alt="<?php echo $user->display_name; ?>">
      </a>
      </div>
    <?php endif; ?>

      <ol>
        <?php if(is_user_logged_in()):  ?>
        <li class="profile<?php global $same_user; if($same_user) echo ' active'; ?>">
          <a href="/profile/<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></a>
        </li>
        <li class="following<?php if(is_current_following()) echo ' active';?>">
          <a href="/profile/<?php echo $user->ID; ?>/following" title="查看所有你关注的人">关注</a>
        </li>
        <li class="activity<?php if(is_activity()) echo ' active';?>">
          <a href="/profile/<?php echo $user->ID; ?>/activity" title="看看大家最近在干什么">动态</a>
        </li>
        <?php if (is_current_user_admin()): ?>
        <li class="admin<?php if(is_admin_front()) echo ' active';?>">
          <a href="/admin-front/">管理</a>
        </li>
        <?php endif ?>
        <li class="upload">
          <a href="/upload/" class="actionButton blueButton" title="分享你的新照片吧">上传</a>
        </li>
        <?php endif; ?>

        <li class="popular<?php if(is_home() && !is_recent()) echo ' active';?>" data-type="popular">
          <a href="/" title="看看最近什么比较热门">热门</a>
        </li>

        <li class="recent<?php if(is_recent()) echo ' active';?>" data-type="recent">
          <a href="/?type=recent" title="最新上传的图片">最新</a>
        </li>

        <li class="categories<?php if(is_custom_category()) echo ' active';?>">
          <a href="/categories" title="按照主题浏览">主题</a>
        </li>

      <!--   <li class="colors">
          <a href="/colors">色彩</a>
        </li> -->
      </ol>

      <?php if (!is_user_logged_in()):  ?>
      <a href="/login" class="login">登录</a>
      <?php endif; ?>


      <p class="bottom">
        <a href="/help">帮助</a>
        <?php if (is_user_logged_in()):?>
        <a href="/settings">设置</a>
        <a href="<?php echo wp_logout_url(home_url()); ?>">退出</a>
        <?php endif; ?>
      </p>
    </div>