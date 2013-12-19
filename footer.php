
    <script src="<?php echo URL;?>/js/jquery.min.js"> </script>

    <script type="text/javascript">
        var pageConfig = {
    <?php if (is_home()) { ?>
          type: 'popular',
    <?php } else { ?>
          type: 'user',
    <?php }if(is_user_logged_in()): ?>
          userId: '<?php echo get_current_user_id(); ?>',
    <?php endif; ?>
          page: 1
        };
    </script>


    <script type="text/javascript" src="<?php echo URL;?>/js/common.js"> </script>
    <script type="text/javascript" src="<?php echo URL;?>/js/grid.js"> </script>
    <?php if (is_upload()): ?>
        <script type="text/javascript" src="<?php echo URL;?>/js/jquery.uploadify.js"></script>
        <script type="text/javascript" src="<?php echo URL;?>/js/upload.js"></script>
    <?php endif ?>
    <?php if (is_settings()): ?>
        <script type="text/javascript" src="<?php echo URL;?>/js/jquery.uploadify.js"></script>
        <script type="text/javascript" src="<?php echo URL;?>/js/settings.js"></script>
    <?php endif ?>

    <?php if (is_profile()): ?>
        <script type="text/javascript" src="<?php echo URL;?>/js/user.js"></script>
    <?php endif ?>

    <?php if (is_single()): ?>
        <script type="text/javascript" src="<?php echo URL;?>/js/image.js"></script>
    <?php endif ?>

    <?php if (is_edit()): ?>
        <script type="text/javascript" src="<?php echo URL;?>/js/edit.js"></script>
    <?php endif ?>
    <?php if (is_admin_front()): ?>
        <script type="text/javascript" src="<?php echo URL;?>/js/admin.js"></script>
    <?php endif ?>
  </body>

</html>