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

    <title>摄影圈 有趣的摄影</title>
    <meta name="description" content="摄影圈网是最有趣的摄影作品分享与交流类网站，简单、有趣,精选，是摄影爱好者和专业摄影师们最信任的作品展示，讨论分享的摄影社区；同时摄影圈网也参与并支持民间公益事业">
    <meta name="keywords" content="摄影圈网、专业摄影社区、器材、外拍、模特、中国摄影、纪实、胶片、日系摄影、建筑摄影、旅游摄影、运动摄影、人像、婚纱摄影、大视代视觉摄影网">

    <link rel="shortcut icon" href="<?php echo URL;?>/favicon.ico"/>

    <link rel="stylesheet" href="/wp-content/themes/canon/style.min.403afc70.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/wp-content/themes/canon/css-dist/home.min.1590418c.css" type="text/css" media="all" />
    <?php if(is_login() || is_upload() || is_signup() || is_settings() || is_activity() || is_page() || is_edit() || is_plugin() || is_color()): ?>
    <link rel="stylesheet" href="/wp-content/themes/canon/css-dist/login.min.d45f953e.css" type="text/css" media="all" />
    <?php endif; ?>
    <?php if (is_profile() && !is_activity()): ?>
    <link rel="stylesheet" href="/wp-content/themes/canon/css-dist/user.min.25a2fa15.css" type="text/css" media="all" />
    <?php endif; ?>
    <?php if (is_single()): ?>
    <link rel="stylesheet" href="/wp-content/themes/canon/css-dist/image.min.f19fd8a0.css" type="text/css" media="all" />
    <?php endif ?>
    <?php if (is_custom_category()): ?>
    <link rel="stylesheet" href="/wp-content/themes/canon/css-dist/overview.min.af803dcb.css" type="text/css" media="all" />
    <?php endif ?>
    <?php if (is_search() || is_admin_front()): ?>
    <link rel="stylesheet" href="/wp-content/themes/canon/css-dist/search.min.f864ab2c.css" type="text/css" media="all" />
    <?php endif; ?>

    <script src="/wp-content/themes/canon/js-dist/modernizr.min.a8b68605.js"></script>
    <script type="text/javascript">
      var CANON_ABSPATH = '<?php echo URL;?>';
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
        <img src="<?php echo canon_get_avatar($user->ID, 'avatar_small');?>"
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

        <li class="color<?php if(is_color()) echo ' active';?>">
          <a href="/color" title="按照颜色浏览">色彩</a>
        </li>
      </ol>

      <?php if (!is_user_logged_in()):  ?>
      <a href="/login" class="login">登录</a>
      <?php endif; ?>

      <div class="qr-code">
        <img src="<?php echo URL;?>/img/qr-code.png" width="150" height="150" alt="摄影圈二维码" />
      </div>

      <p class="bottom">
        <a href="/help">帮助</a>
        <?php if (is_user_logged_in()):?>
        <a href="/settings">设置</a>
        <a href="<?php echo wp_logout_url(home_url()); ?>">退出</a>
        <?php endif; ?>
      </p>
    </div>