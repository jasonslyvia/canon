/*
 *  处理文件上传
 */

var gbks = gbks || {};
gbks.common = gbks.common || {};
gbks.common.upload = function(){

    var self = gbks.common.upload;

    if (typeof URL === undefined || typeof USER_ID === undefined) {
        console.log("获取全局信息错误！");
        return false;
    }

    //防止表单重复提交
    self.ajaxFlag = false;

    //照片上传成功后的回调处理
    self.onPicUploadSuccess = function(file, data, response){
        var result = $.parseJSON(data);
        if (result.error) {
            alert(result.message);
        }
        else{
            //将信息临时存到隐藏控件中
            $("#filename").val(result.filename);
            $("#picWidth").val(result.width);
            $("#picHeight").val(result.height);

            self.createPreview(result.filename);
            $('#file_upload').uploadify('settings','buttonText','重新选择');
        }
    };

    self.uploadify = function(){
        $("#file_upload").uploadify({
            swf: URL + '/uploads/uploadify.swf',
            uploader: URL + '/uploads/uploadify.php',
            buttonText: '上传',
            fileSizeLimit: '5MB',
            formData: {userId: USER_ID},
            onUploadSuccess: self.onPicUploadSuccess
        });
    };
    self.uploadify();

    self.createPreview = function(filename, width){
        //避免重新选择时内容重复
        $(".preview").remove();

        width = width || 620;
        $("<div class='preview'>"+
            "<img src='"+ URL +"/uploads/images/"+ USER_ID+"/"+
                    filename +
                    "' width='"+ width +"' />"+
            "<div class='op'>"+
                "<label for='referrer'>照片来源网址（原创则留空）</label><br />"+
                "<input type='text' id='referrer' />"+
                "<a href='#' class='actionButton blueButton'"+
                " id='publishNewBtn'>发布新照片</a>"+
            "</div>"+
          "</div>").appendTo("#uploadDiv");

        self.addPic();
    };

    self.addPic = function(){
        $("#publishNewBtn").bind("click", function(e){
            e.preventDefault();

            if (self.ajaxFlag) {
                return false;
            }

            var filename = $("#filename").val();
            var width = $("#picWidth").val();
            var height = $("#picHeight").val();
            if (!filename || !width || !height) {
                alert("获取照片信息失败！");
                return false;
            }

            var referrer = $("#referrer").val();

            if (typeof nonce === undefined) {
                alert("获取用户登录凭证失败！");
                return false;
            }

            self.ajaxFlag = true;
            $(".op").empty().append("<div class='ajax-message'>"+
                                "<img src='" + URL + "/img/loader.gif' />"+
                                "<span>上传中，请稍候</span>"+
                            "</div>");

            $.ajax({
                url: URL + '/functions/add_pic.php',
                type: 'post',
                dataType: 'json',
                data: {
                    filename: filename,
                    width: width,
                    height: height,
                    referrer: referrer,
                    userId: USER_ID,
                    nonce: nonce
                },
                success: self.onAddPicSuccess
            });

        });
    };

    self.onAddPicSuccess = function(result){
        self.ajaxFlag = false;

        var $message = $(".ajax-message");
        $message.empty().append(result.message);

        if (!result.error) {
            $message.append("<br />"+
                "<a href='javascript:location.reload();'>继续上传</a> "+
                "<a href='/?p="+result.postId+"'>查看详情</a>");
        }
    };

};

gbks.common.upload();

