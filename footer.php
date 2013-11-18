
    <script src="<?php echo URL;?>/js/jquery.min.js"> </script>

    <script type="text/javascript">
    <?php if (is_home()) { ?>
        var pageConfig = {
          type: 'popular'
        };
    <?php } else if (is_page('profile') || is_page('upload')) { ?>

        var pageConfig = {
          type: 'user',
          userId: '<?php echo get_current_user_id(); ?>',
          page: 1
        };
    <?php } ?>
    </script>


    <script type="text/javascript" src="<?php echo URL;?>/js/common.js"> </script>
    <script type="text/javascript" src="<?php echo URL;?>/js/grid.js"> </script>
    <?php if (is_page('upload')): ?>
        <script type="text/javascript" src="<?php echo URL;?>/js/jquery.uploadify.js"></script>
        <script type="text/javascript" src="<?php echo URL;?>/js/upload.js"></script>
    <?php endif ?>
    <?php //wp_footer();?>
  </body>

</html>