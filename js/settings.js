/*
 *  处理用户信息修改
 */
$(function(){
    //为页面中的所有input绑定自动选择的功能
    $(".settings input").bind("focus", function(){
        if ($(this).val()) {
            this.select();
        }
    }).bind("mouseup", function(){
        return false;
    });


    var userId = pageConfig.userId;
    var data = {
        userId: userId,
        type: 'avatar'
    };

    //绑定头像上传功能
    $("#file_upload").uploadify({
        swf: ABSPATH + '/uploads/uploadify.swf',
        uploader: ABSPATH + '/uploads/uploadify.php',
        buttonText: '上传',
        fileSizeLimit: '5MB',
        formData: data,
        onUploadSuccess: function(file, data, response){
            var result = $.parseJSON(data);
            if (result.error) {
                alert(result.message);
            }
            else{
                updatePreview(result.large, result.small);
                $('#file_upload').uploadify('settings','buttonText','重新选择');
            }
        }
    });

    //更新上传后的头像
    function updatePreview(large, small){
        $(".avatar-preview .large").attr("src", ABSPATH + "/uploads/avatar/"+
                                        large);
        $(".avatar-preview .small").attr("src", ABSPATH + "/uploads/avatar/"+
                                        small);
    }
});