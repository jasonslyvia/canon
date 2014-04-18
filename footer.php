
    <script src="/wp-content/themes/canon/js-dist/jquery.min.41a99f3b.js"> </script>

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

    <script type="text/javascript" src="/wp-content/themes/canon/js-dist/main.min.2ba550ec.js"></script>
    

    <?php if (is_upload() || is_plugin()): ?>
    <script type="text/javascript" src="/wp-content/themes/canon/js-dist/uploadify.min.41206d0c.js"></script>
    <script type="text/javascript" src="/wp-content/themes/canon/js-dist/color-picker.min.d738a299.js"></script>
    <script type="text/javascript" src="/wp-content/themes/canon/js-dist/upload.min.43e0d0df.js"></script>
    <script type="text/javascript">
    if($("#color").length){
        //初始化color picker
        $("#color").colorPicker({
            onColorChange: function(id, newColor){
                $("#picColor").val(newColor);
            }
        });
    }
    </script>
    <?php endif ?>
    <?php if (is_settings()): ?>
    <script type="text/javascript" src="/wp-content/themes/canon/js-dist/uploadify.min.41206d0c.js"></script>
    <script type="text/javascript" src="/wp-content/themes/canon/js-dist/settings.min.7c3f5f3c.js"></script>
    <?php endif ?>

    <?php if (is_profile()): ?>
    <script type="text/javascript" src="/wp-content/themes/canon/js-dist/user.min.d995e575.js"></script>
    <?php endif ?>

    <?php if (is_single()): ?>
    <script type="text/javascript" src="/wp-content/themes/canon/js-dist/image.min.4d3ee894.js"></script>
    <script type="text/javascript" src="http://v3.jiathis.com/code/jia.js?uid=1373892207080614" charset="utf-8"></script>
    <?php endif ?>

    <?php if (is_edit()): ?>
    <script type="text/javascript" src="/wp-content/themes/canon/js-dist/edit.min.43ad4966.js"></script>
    <?php endif ?>
    <?php if (is_admin_front()): ?>
    <script type="text/javascript" src="/wp-content/themes/canon/js-dist/admin.min.3c33347f.js"></script>
    <?php endif ?>
    <?php if (is_color()): ?>
    <script type="text/javascript" src="/wp-content/themes/canon/js-dist/color.min.a8333fa4.js"></script>
    <?php endif ?>
    <?php if (is_login() || is_signup()): ?>
    <script type="text/javascript" src="/wp-content/themes/canon/js-dist/social.min.5e214ce6.js"></script>
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
          url: CANON_ABSPATH +　'/functions/loadmore.php',
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

<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F720c8180a3bd772b60a4b24041def31f' type='text/javascript'%3E%3C/script%3E"));
</script>
  </body>

</html>