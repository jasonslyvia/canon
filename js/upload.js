/*
 *  处理文件上传
 */

var gbks = gbks || {};
gbks.common = gbks.common || {};
gbks.common.uploadImage = function(){

    var self = gbks.common.uploadImage;

    if (typeof URL === undefined || typeof pageConfig === undefined) {
        console.log("获取全局信息错误！");
        return false;
    }

    var USER_ID = pageConfig.userId;

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

        var data = {
            userId: USER_ID
        };

        if (typeof uploadConfig === "object") {
            $.extend(data, uploadConfig);
        }

        $("#file_upload").uploadify({
            swf: ABSPATH + '/uploads/uploadify.swf',
            uploader: ABSPATH + '/uploads/uploadify.php',
            buttonText: '上传',
            fileSizeLimit: '5MB',
            formData: data,
            onUploadSuccess: self.onPicUploadSuccess
        });
    };
    self.uploadify();

    self.createPreview = function(filename, width){
        //避免重新选择时内容重复
        $(".preview").remove();

        var imageSrc = ABSPATH +"/uploads/images/"+ USER_ID+"/"+ filename;
        createPreview(imageSrc, $("uploadDiv"));

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
            var title = $("#title").val();
            var category = $("#cat").val();

            if (typeof nonce === undefined) {
                alert("获取用户登录凭证失败！");
                return false;
            }

            self.ajaxFlag = true;
            appendAjaxMessage("上传中，请稍候", $(".op"));

            $.ajax({
                url: ABSPATH + '/functions/add_pic.php',
                type: 'post',
                dataType: 'json',
                data: {
                    filename: filename,
                    width: width,
                    height: height,
                    referrer: referrer,
                    title: title,
                    category: category,
                    userId: USER_ID,
                    nonce: nonce
                },
                success: self.onAddPicSuccess
            });
        });
    };

    self.onAddPicSuccess = function(result){
        self.ajaxFlag = false;
        if (result.message) {
            $message = $("ajax-message");
            $message.empty().append(result.message + "<br />"+
                "<a href='javascript:location.reload();'>继续上传</a> "+
                "<a href='/?p="+result.postId+"'>查看详情</a>");
        }
    };

    //处理远程抓取图片的逻辑
    $("#remoteImgBtn").bind("click", function(){

        var $btn = $(this);
        var $parent = $(this).parent();

        if (self.ajaxFlag) {
            return false;
        }

        var url = $("#url").val();
        if (!url || !url.match(/^(https?:\/\/)?(.+?\.)?.+?\..{2,4}$/i)) {
            alert("请输入有效的图片地址或网站！");
            return false;
        }

        if (url.match(/(jpg|jpeg|png|gif|bmp)$/i)) {
            appendAjaxMessage("图片加载中……", $parent);
            var image = new Image();
            image.src = url;
            image.onload = function(){
                removeAjaxMessage();
                createPreview(image.src, $parent);

                if (image.width < 200) {
                    alert("图片宽度最小限制为 200 像素");
                    return false;
                }

                $("#filename").val(url);
                $("#picWidth").val(image.naturalWidth || image.width);
                $("#picHeight").val(image.naturalHeight || image.height);

                self.addPic();
            };
        }

        // self.ajaxFlag = true;
        // $.ajax({
        //     url: ABSPATH +　"/functions/get_remote_image.php",
        //     type: "post",
        //     data: {url: url},
        //     dataType: "json",
        //     success: function(result){
        //         self.ajaxFlag = false;
        //     }
        // });
    });


    /*

      工具函数区域

    */
    //处理ajax时的提示信息
    function appendAjaxMessage(message, container){
        if (typeof container !== "object") {
            container = $(container);
            if (container.size() === 0) {
                return false;
            }
        }
        else{
            $(".ajax-message").remove();
            container.append("<div class='ajax-message'>"+
                                "<img src='" + ABSPATH + "/img/loader.gif' />"+
                                "<span>"+message+"</span>"+
                            "</div>");
        }
    }

    function removeAjaxMessage(){
        $(".ajax-message").remove();
    }

    function createPreview(image, container){
        $("<div class='preview'>"+
            "<img src='"+ image +"' width='620' />"+
            "<div class='op'>"+
                "<label for='referrer'>照片来源网址（原创则留空）</label><br />"+
                "<input type='text' id='referrer' />"+
                "<label for='title'>照片标题（一句话形容这幅作品，必填）</label><br />"+
                "<input type='text' id='title' />"+
                "<label for='cat'>照片主题</label><br />"+
                $("#category").html()+"<br />"+
                "<a href='#' class='actionButton blueButton'"+
                " id='publishNewBtn'>发布新照片</a>"+
            "</div>"+
          "</div>").appendTo(container);
    }

};

gbks.common.uploadImage();

