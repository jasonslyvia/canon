
    <script src="js/jquery.js"> </script>

    <script type="text/javascript">
    //全局设置对象
        var pageConfig = {
    <?php if(is_user_logged_in()): ?>
          userId: '<?php echo get_current_user_id(); ?>',
    <?php endif; ?>
    <?php if(is_profile()): $view_user_id = preg_replace('/^.*?\/(\d+).*?$/', '$1', $_SERVER['REQUEST_URI']);?>
          viewUserId: '<?php echo $view_user_id ?>',
    <?php endif; ?>
          page: 2
        };

<?php
    $URI = $_SERVER['REQUEST_URI'];
    if (is_home() && $_GET['type'] == 'recent') {
        $type =  'index_recent';
    }
    else if(is_home()){
        $type = 'index_popular';
    }
    else if (is_profile() && preg_match('/likes/i', $URI)) {
        $type = 'user_like';
    }
    else if (is_profile() && preg_match('/notes/i', $URI)) {
        $type = 'user_note';
    }
    else if (is_profile() && preg_match('/following/i', $URI)) {
        $type = 'user_following';
    }
    else if (is_profile() && preg_match('/followed/i', $URI)) {
        $type = 'user_followed';
    }
    else if (is_profile() && !is_activity()) {
        $type = 'user_save';
    }
    else if (is_search() && $_GET['type'] == "user") {
        $type = 'search_user_'.$_GET['s'];
    }
    else if (is_search() && $_GET['type'] == "popular") {
        $type = 'search_popular_'.$_GET['s'];
    }
    else if (is_search() && $_GET['type'] == "my") {
        $type = 'search_my_'.$_GET['s'];
    }
    else if (is_search()) {
        $type = 'search_recent_'.$_GET['s'];
    }
    else if (is_category()) {
        global $cat_id;
        $type = 'category_'.$cat_id;
    }
    else if (is_activity()) {
        $type = 'user_activity';
    }

    echo 'pageConfig.type="'.$type.'";';
?>
    </script>

    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/grid.js"></script>

    <?php if (is_upload() || is_plugin()): ?>
    <script type="text/javascript" src="js/uploadify.js"></script>
    <script type="text/javascript" src="js/upload.js"></script>
    <?php endif ?>
    <?php if (is_settings()): ?>
    <script type="text/javascript" src="js/uploadify.js"></script>
    <script type="text/javascript" src="js/settings.js"></script>
    <?php endif ?>

    <?php if (is_profile()): ?>
    <script type="text/javascript" src="js/user.js"></script>
    <?php endif ?>

    <?php if (is_single()): ?>
    <script type="text/javascript" src="js/image.js"></script>
    <script type="text/javascript" src="http://v3.jiathis.com/code/jia.js?uid=1373892207080614" charset="utf-8"></script>
    <?php endif ?>

    <?php if (is_edit()): ?>
    <script type="text/javascript" src="js/edit.js"></script>
    <?php endif ?>
    <?php if (is_admin_front()): ?>
    <script type="text/javascript" src="js/admin.js"></script>
    <?php endif ?>
    <?php if (is_activity()): ?>
    <script type="text/javascript">
    //dirty hack for activity lazy load
    var ajaxFlag = false;
    var noMore = false;
    $(window).bind("scroll", function(){
      var scrollTop = $("body").scrollTop();
      var documentHeight = $(document).height();

      //离文档底部还有200像素时尝试加载更多内容
      if ((documentHeight - scrollTop < 500) && !ajaxFlag && !noMore) {
        ajaxFlag = true;
        $.ajax({
          url: ABSPATH +　'/functions/loadmore.php',
          data: pageConfig,
          type: 'GET',
          success: function(html){
            if (html.match(/noMoreImages/)) {
                noMore = true;
            }
            else{
                $("#maincontent").append(html);
                pageConfig.page ++;
            }
            ajaxFlag = false;
          }
        });
      }
    });
    </script>
    <?php endif ?>
  </body>

</html>