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

?>
<div id="luka">
  <div class="hamburger"> </div>
  <p> <a href="#"> 动态 - <?php echo get_bloginfo(); ?> </a> </p>
</div>

<div id="page">

  <div class="headerSpacer"> </div>
  <div id="maincontent" class="center" style="width:700px;">

<?php
  require_once('functions/get_data.php');
  $activity_result = get_activity($c_user_id, 1, true);
?>
  </div>
</div>

<div id="loader">
</div>


<?php get_footer(); ?>