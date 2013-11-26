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


    $("input").bind("keyup", function(e){
        if (e.keyCode == 13) {
            $(this).closest("div").find("input[type='button']").trigger("click");
        }
    });
    //绑定基本信息更新
    $("#infoSubmitBtn").bind("click", function(){
        var $btn = $(this);
        $(".error").remove();

        //验证昵称字段的有效性
        var name = $("#displayName").val();
        if (!name) {
            $btn.after("<span class='error'>昵称不得为空</span>");
            return false;
        }
        else if (name.length < 2 || name.length > 10) {
            $btn.after("<span class='error'>昵称长度为 2 - 10 个字符</span>");
            return false;
        }
        else if (!name.match(/^[a-zA-Z0-9\u4e00-\u9fa5]+$/)) {
            $btn.after("<span class='error'>昵称可包含的有效字符为大小写字母、数字或汉字</span>");
            return false;
        }

        //验证邮箱的有效性
        var email = $("#userEmail").val();
         if (!email) {
            $btn.after("<span class='error'>邮箱地址不得为空</span>");
            return false;
        }
        else if (!email.match(/^\w+@\w+\.\w{2,4}$/)) {
            $btn.after("<span class='error'>请输入有效的邮箱地址</span>");
            return false;
        }

        $.ajax({
            url: ABSPATH + '/functions/update_user_info.php',
            type: 'POST',
            data: {
                name: name,
                email: email,
                userId: pageConfig.userId,
                nonce: nonce
            },
            dataType: 'json',
            success: function(data){
                if (data.error) {
                    $btn.after("<span class='error'>"+data.message+"</span>");
                }
                else{
                    $btn.after("<span class='success'>更新成功</span>");
                }
                setTimeout(function(){
                    $(".error, .success").fadeOut(function(){
                        location.reload();
                    });
                }, 1000);
            }
        });
    });

    //绑定密码更新
    $("#changePasswordBtn").bind("click", function(){
        $(".error").remove();

        var $btn = $(this);
        var password = $("#currentPassword").val();
        var newPassword = $("#newPassword").val();
        var renewPassword = $("#renewPassword").val();

        //验证数据有效性
        if (!password || !newPassword || !renewPassword) {
            $btn.after("<span class='error'>密码不能为空</span>");
            return false;
        }
        else if (newPassword != renewPassword) {
            $btn.after("<span class='error'>两次输入密码不一致</span>");
            return false;
        }
        else if (newPassword.length < 6 || newPassword.length > 16) {
            $btn.after("<span class='error'>密码有效长度为 6 - 16 位</span>");
            return false;
        }

        //发送请求
        $.ajax({
            url: ABSPATH + '/functions/update_user_info.php',
            type: 'POST',
            dataType: 'json',
            data: {
                password: password,
                newPassword: newPassword,
                nonce: nonce,
                userId: pageConfig.userId,
                target: "password"
            },
            success: function(data){
                if (data.error) {
                    $btn.after("<span class='error'>"+data.message+"</span>");
                }
                else{
                    $btn.after("<span class='success'>更新成功</span>");
                    setTimeout(function(){
                        $(".error, .success").fadeOut(function(){
                            location.reload();
                        });
                    }, 1000);
                }
            }
        });
    });
});