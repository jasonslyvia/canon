
    <script src="<?php echo URL;?>/js/jquery.min.js">
    </script>

    <script type="text/javascript" src="<?php echo URL;?>/js/common.js">
    </script>

    <script type="text/javascript" src="<?php echo URL;?>/js/grid.js">
    </script>

    <?php if (is_page('upload')): ?>
        <script type="text/javascript" src="<?php echo URL;?>/js/jquery.uploadify.js"></script>
        <script type="text/javascript">
        $("#file_upload").uploadify({
            swf: '/wp-content/themes/canon/uploads/uploadify.swf',
            uploader: '/wp-content/themes/canon/uploads/uploadify.php',
            buttonText: '上传',
            fileSizeLimit: '5MB',
            formData: {userId: <?php echo get_current_user_id(); ?>},
            onUploadSuccess: function(file, data, response){
                var result = $.parseJSON(data);
                if (result.error) {
                    alert(result.message);
                }
                else{
                    //将信息临时存到隐藏控件中
                    $("#filename").val(result.filename);
                    $("#picWidth").val(result.width);
                    $("#picHeight").val(result.height);

                    //避免重新选择时内容重复
                    $(".preview").remove();

                    $("<div class='preview'>"+
                        "<img src='/wp-content/themes/canon/uploads/images/1/"+ result.filename +
                                "' width='620' />"+
                        "<div class='op'>"+
                            "<label for='referrer'>照片来源网址（原创则留空）</label><br />"+
                            "<input type='text' id='referrer' />"+
                            "<a href='#' class='actionButton blueButton' id='publishNewBtn'>发布新照片</a>"+
                        "</div>"+
                      "</div>").appendTo("#uploadDiv");

                    $('#file_upload').uploadify('settings','buttonText','重新选择');
                }
            }

        });
        </script>
    <?php endif ?>


    <?php //wp_footer();?>
  </body>

</html>