<?php

/*
 * Template Name: profile
 *
 */

//获取当前 被 浏览的用户信息
$uid = preg_replace('/^.*?\/(\d+)\/?$/', '$1', $_SERVER['REQUEST_URI']);
$user = get_user_by('id', $uid);
if ( $user == false) {
  wp_redirect('/');
  exit();
}
else{
  $name = $user->display_name;
}


//获取当前登录的用户信息
$c_user_id = get_current_user_id();


//判断当前被浏览用户与当前登录用户是否为同一人
global $same_user;
if ($user->ID == $c_user_id) {
  $same_user = true;
}




get_header();

?>


<div id="luka">
  <div class="hamburger">
  </div>
  <p>
    <a href="/">
      <?php echo get_bloginfo(); ?>
    </a>
  </p>
</div>

<div id="page">
  <div class="headerSpacer">
  </div>
  <div id="images">

    <!-- 用户信息 -->
    <div class="tile" id="userInfo">
      <h1><b><a href="/profile/<?php echo $uid; ?>"><?php echo $name; ?></a></b></h1>
      <div class="picture">
        <a href="/profile/<?php echo $uid; ?>"><img src="/images/profile/200/22250_jason.yang.79677471.jpg" width="200" height="204.44444444444" alt="Jason Yang"></a>
      </div>
      <div class="statistics clearfix">
        <p>
          <a href="/profile/<?php echo $uid; ?>">5<br>
          <span>图像</span></a>
        </p>
        <p>
          <a href="/profile/<?php echo $uid; ?>/notes">3<br>
          <span>评论</span></a>
        </p>
        <p>
          <a href="/profile/<?php echo $uid; ?>/likes">2<br>
          <span>喜欢</span></a>
        </p>
        <p>
          <a href="/profile/<?php echo $uid; ?>/following">3<br>
          <span>关注</span></a>
        </p>
      </div>
    </div>

    <!-- 保存的内容 -->
    <div class="polaroid tile saved" id="image_331510" data-likes="11" data-saves="19" data-w="544" data-h="1024" style="position: absolute; top: 0px; left: 240px; display: block;">
      <div class="options">
        <div class="save active" data-id="331510" title="Save image">
          <em></em><span>Edit</span>
        </div>
        <div class="like" data-id="331510" title="Like image">
          <em></em><span>Like</span>
        </div>
      </div>
      <a href="http://www.wookmark.com/image/331510/clutter-chaos" class="imageLink"><img src="http://images2.wookmark.com/331510_69189a15e95694d703c2137d99d6e6f2.jpg" alt="Clutter &amp; Chaos" width="200" height="376"></a>
      <div class="stats">
        <p>
          <a href="http://www.wookmark.com/image/331510/clutter-chaos"><em class="s"></em><span class="saves">19</span><em class="l"></em><span class="likes">11</span></a>
        </p>
      </div>
    </div>
    <div class="polaroid tile saved" id="image_332506" data-likes="5" data-saves="11" data-w="600" data-h="1065" style="position: absolute; top: 0px; left: 480px; display: block;">
      <div class="options">
        <div class="save active" data-id="332506" title="Save image">
          <em></em><span>Edit</span>
        </div>
        <div class="like" data-id="332506" title="Like image">
          <em></em><span>Like</span>
        </div>
      </div>
      <a href="http://www.wookmark.com/image/332506/512f7b6ed29966672bf66721659cd810-jpg-image-jpeg-648x972-pixels-redimensionn-e-64" class="imageLink"><img src="http://images2.wookmark.com/332506_322465_2066.jpg" alt="512f7b6ed29966672bf66721659cd810.jpg (Image JPEG, 648x972 pixels) - Redimensionnée (64%)" width="200" height="355"></a>
      <div class="stats">
        <p>
          <a href="http://www.wookmark.com/image/332506/512f7b6ed29966672bf66721659cd810-jpg-image-jpeg-648x972-pixels-redimensionn-e-64"><em class="s"></em><span class="saves">11</span><em class="l"></em><span class="likes">5</span></a>
        </p>
      </div>
    </div>
    <div class="polaroid tile saved" id="image_330809" data-likes="1" data-saves="9" data-w="455" data-h="650" style="position: absolute; top: 0px; left: 720px; display: block;">
      <div class="options">
        <div class="save active" data-id="330809" title="Save image">
          <em></em><span>Edit</span>
        </div>
        <div class="like active" data-id="330809" title="Like image">
          <em></em><span>Like</span>
        </div>
      </div>
      <a href="http://www.wookmark.com/image/330809/tumblr-mtcy0gkjpe1ro46rko1-500-jpg-image-jpeg-500x750-pixels-redimensionn-e-81" class="imageLink"><img src="http://images3.wookmark.com/330809_cdcad233c2222a27b3a1e1c4a7786b07.jpg" alt="tumblr_mtcy0gkJpE1ro46rko1_500.jpg (Image JPEG, 500x750 pixels) - Redimensionnée (81%)" width="200" height="286"></a>
      <div class="stats">
        <p>
          <a href="http://www.wookmark.com/image/330809/tumblr-mtcy0gkjpe1ro46rko1-500-jpg-image-jpeg-500x750-pixels-redimensionn-e-81"><em class="s"></em><span class="saves">9</span></a>
        </p>
      </div>
    </div>
    <div class="polaroid tile saved" id="image_329841" data-likes="2" data-saves="9" data-w="600" data-h="600" style="position: absolute; top: 342px; left: 720px; display: block;">
      <div class="options">
        <div class="save active" data-id="329841" title="Save image">
          <em></em><span>Edit</span>
        </div>
        <div class="like" data-id="329841" title="Like image">
          <em></em><span>Like</span>
        </div>
      </div>
      <a href="http://www.wookmark.com/image/329841/vinyl-label-artwork-for-mita-003-on" class="imageLink"><img src="http://images1.wookmark.com/329841_a806011249a41f578f5730d09f846063.jpg" alt="Vinyl Label Artwork for MITA 003 on" width="200" height="200"></a>
      <div class="stats">
        <p>
          <a href="http://www.wookmark.com/image/329841/vinyl-label-artwork-for-mita-003-on"><em class="s"></em><span class="saves">9</span><em class="l"></em><span class="likes">2</span></a>
        </p>
      </div>
    </div>
    <div class="polaroid tile saved" id="image_332215" data-likes="3" data-saves="4" data-w="480" data-h="580" style="position: absolute; top: 411px; left: 480px; display: block;">
      <div class="options">
        <div class="save active" data-id="332215" title="Save image">
          <em></em><span>Edit</span>
        </div>
        <div class="like" data-id="332215" title="Like image">
          <em></em><span>Like</span>
        </div>
      </div>
      <a href="http://www.wookmark.com/image/332215" class="imageLink"><img src="http://images2.wookmark.com/332215_tumblr_mr8lc0qaup1swrzvto1_500.jpg" alt="+++++" width="200" height="242"></a>
      <div class="stats">
        <p>
          <a href="http://www.wookmark.com/image/332215"><em class="s"></em><span class="saves">4</span><em class="l"></em><span class="likes">3</span></a>
        </p>
      </div>
    </div>

    <div class="clear">
    </div>

  </div>
  <div class="clear">
  </div>
</div>
<div id="loader">
</div>

<?php get_footer();?>