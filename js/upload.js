/*
 *  处理文件上传
 */

var gbks = gbks || {};
gbks.common = gbks.common || {};
gbks.common.uploadImage = function(){

    var self = gbks.common.uploadImage;

    //检测flash支持
    if (!(typeof swfobject !== 'undefined' && swfobject.getFlashPlayerVersion().major !== 0)) {
        if (confirm("您的浏览器不支持Flash，无法使用上传功能。是否现在下载Flash插件？")) {
            location.assign("http://get.adobe.com/cn/flashplayer/");
        }
        return false;
    }

    if (typeof CANON_ABSPATH === undefined || typeof pageConfig === undefined) {
        console.log("获取全局信息错误！");
        return false;
    }

    var USER_ID = pageConfig.userId;

    //为color picker初始化颜色
    $.fn.colorPicker.defaults.colors = [
    'EE99AA','EEAACC','CCAACC','BBAACC','AAAACC','AAAACC','AABBDD','BBDDEE',
    'BBDDCC','BBDDAA','BBDDAA','DDDD99','FFEE88','FFCC99','EE9999','EE9999',
    'EE5577','DD77AA','BB88AA','9988AA','7788BB','7788BB','7799CC','88CCDD',
    '99CCBB','99CC88','99CC77','BBCC77','EEDD55','EEAA66','EE7766','EE6666',
    'EE2255','CC4488','996699','776699','556699','446699','5588BB','66BBCC',
    '77BB99','77AA55','77BB55','AACC44','EECC22','EE8822','EE4433','DD3344',
    'AA2244','993366','774466','554477','444477','334477','336688','558899',
    '558877','558844','668833','889933','BB9911','BB6622','AA3322','AA2233',
    '771122','662244','553344','443344','223355','223355','224455','336666',
    '335555','335533','445522','556622','776611','774411','772211','771122',
    '331111','331122','221122','221122','111122','111122','112233','223333',
    '223322','223311','223311','333311','443300','442211','331111','331111',
    '000000','111111','222222','333333','444444','555555','666666','777777',
    '888888','888888','999999','AAAAAA','BBBBBB','CCCCCC','DDDDDD','EEEEEE',
    'FF0000','FFFF00','FFFFFF','FF00FF','00FFFF','00FF00','0000FF'
    ];

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
            $("#picColor").val(result.color);

            self.createPreview(result.filename);
            //初始化color picker
            $("#color").colorPicker({
                pickerDefault: result.color.slice(1),
                onColorChange: function(id, newColor){
                    $("#picColor").val(newColor);
                }
            });

            $('#file_upload').uploadify('settings','buttonText','重新选择');
            $("#file_upload").uploadify('disable', true);
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
            swf: CANON_ABSPATH + '/uploads/uploadify.swf',
            uploader: CANON_ABSPATH + '/uploads/uploadify.php',
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

        var imageSrc = CANON_ABSPATH +"/uploads/images/"+ USER_ID+"/"+ filename;
        createPreview(imageSrc, $("#uploadDiv"));

        self.addPic();
    };

    self.addPic = function(){
        $("#publishNewBtn").live("click", function(e){
            e.preventDefault();

            if (self.ajaxFlag) {
                return false;
            }

            var filename = $("#filename").val();
            var width = $("#picWidth").val();
            var height = $("#picHeight").val();
            var color = $("#picColor").val();
            if (!filename) {
                alert("获取照片信息失败！");
                return false;
            }

            var referrer = $("#referrer").val();
            var title = $("#title").val();
            var category = $("#picCat").val();

            if (typeof nonce === undefined) {
                alert("获取用户登录凭证失败！");
                return false;
            }

            self.ajaxFlag = true;
            appendAjaxMessage("上传中，请稍候", $(".op"));

            $.ajax({
                url: CANON_ABSPATH + '/functions/add_pic.php',
                type: 'post',
                dataType: 'json',
                data: {
                    filename: filename,
                    width: width,
                    height: height,
                    referrer: referrer,
                    title: title,
                    category: category,
                    color: color,
                    userId: USER_ID,
                    nonce: nonce
                },
                success: self.onAddPicSuccess,
                error: function(){
                    self.removeAjaxMessage();
                    self.ajaxFlag = false;
                    alert("无法远程保存该图片，请将该图片保存至本地后再上传！");
                }
            });
        });
    };
    //方便插件页面上传图片
    self.addPic();

    self.onAddPicSuccess = function(result){
        self.ajaxFlag = false;
        if (!result.error) {
            var $parent = $(".preview").closest(".wrapSignupForm");
            $(".preview").slideUp(500, function(){
                //移除preview
                $(this).remove();
                $parent.append(
                    '<div class="ajax-result">' +
                        '<p class="tip">'+result.message + "</p>"+
                        "<a class='actionButton blueButton resetUploadBtn'>继续上传</a> "+
                        "<a class='actionButton' href='/?p="+result.postId+"'>查看详情</a>"+
                    '</div>');

                    $(".resetUploadBtn").one("click", function(){
                        //若是普通上传页面
                        if ($("#file_upload").length) {
                            $("#file_upload").uploadify("settings", "buttonText", "上传");
                            $("#file_upload").uploadify("disable", false);
                            $(".ajax-result").remove();
                        }
                        else{
                            window.close();
                        }
                    });
                });
        }
        else{
            alert(result.message);
            $(".ajax-message").remove();
        }
    };

    //处理远程抓取图片的逻辑
    $("#remoteImgBtn").bind("click", function(){

        var $btn = $(this);
        var $parent = $(this).parent();

        //若上一个ajax请求还未完成，则取消本次响应
        if (self.ajaxFlag) {
            return false;
        }

        var url = $.trim($("#url").val());
        if (!url || !url.match(/^(https?:\/\/)?(.+?\.)?.+?\..{2,4}(\/.*)?$/i)) {
            alert("请输入有效的图片地址或网站！");
            return false;
        }

        //若用户输入的是图片地址，直接展示
        if (url.match(/(jpg|jpeg|png|gif|bmp)$/i)) {
            appendAjaxMessage("图片加载中……", $parent);
            var image = new Image();
            image.src = url;
            image.onload = function(){
                if (image.width < 200) {
                    alert("图片宽度最小限制为 200 像素");
                    removeAjaxMessage();
                    $("#url").val('').focus();
                    return false;
                }

                $btn.text("重新获取");
                removeAjaxMessage();
                createPreview(image.src, $parent);

                //初始化color picker
                $("#color").colorPicker({
                    onColorChange: function(id, newColor){
                        $("#picColor").val(newColor);
                    }
                });

                $("#filename").val(url);
                $("#picWidth").val(image.naturalWidth || image.width);
                $("#picHeight").val(image.naturalHeight || image.height);

                self.addPic();
            };
        }
        //若是网址，则开始尝试获取网页中包含的图片
        else{
            $(".preview").remove();
            $(".preview-small-container").hide().find("div").remove();
            appendAjaxMessage("加载中……", $parent);
            loadingCount = 0;

            self.ajaxFlag = true;
            $.ajax({
                url: CANON_ABSPATH +　"/functions/get_remote_image.php",
                type: "post",
                data: {url: url, nonce: nonce},
                dataType: "json",
                success: function(result){
                    self.ajaxFlag = false;
                    createGridPreview.call(this, result, $btn, url);
                }
            });
        }
    });

    /*

      工具函数区域
    */
    //添加发送ajax请求时的提示信息
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
                                "<img src='" + CANON_ABSPATH + "/img/loader.gif' />"+
                                "<span>"+message+"</span>"+
                            "</div>");
        }
    }

    //移除ajax提示信息
    function removeAjaxMessage(){
        $(".ajax-message").remove();
    }

    //为单幅图片添加信息、发布按钮
    function createPreview(image, container){
        $(".preview").remove();

        var referrer = "";
        if ($("#url").val()) {
            referrer = $.trim($("#url").val());
        }

        var selectHTML = $("#category select").html();

        $("<div class='preview'>"+
            "<img src='"+ image +"' />"+
            "<div class='op'>"+
                "<label for='referrer'>照片来源网址（原创则留空）</label><br />"+
                "<input type='text' id='referrer' value='"+ referrer +"' />"+
                "<label for='title'>照片标题（一句话形容这幅作品）</label><br />"+
                "<input type='text' id='title' />"+
                "<label for='cat'>照片主题</label>"+
                "<select id='picCat'>"+
                selectHTML +
                "</select>" + "<br />"+
                "<label for='color'>照片颜色</label>"+
                "<input id='color' type='text' name='color' />"+
                "<a href='javascript:;' class='actionButton blueButton'"+
                " id='publishNewBtn'>发布新照片</a>"+
            "</div>"+
          "</div>").appendTo(container);

        $("#referrer").focus();
    }

    var loadingCount = 0;
    //将网址中包含的图片加载后添加到页面中
    //并绑定响应事件
    function createGridPreview(result, $btn, url){
        $btn.text("重新获取");
        var $container = $(".preview-small-container");
        $container.show();

        var images = result.images;
        if (result.error) {
            alert(result.message);
            return false;
        }
        else if (images.length === 0) {
            $container.find("label").text("目标网页中没有分析到有效的照片！");
            removeAjaxMessage();
        }
        else{
            var domain = url.replace(/^(https?:\/\/[^\/]+).+$/i, "$1");

            loadingCount = images.length;
            for(var i=0, l=images.length; i<l; ++i){
                var image = new Image();
                //前端手动fix src不包含域名的问题
                if (!images[i].match(/https?:\/\//i)) {
                    images[i] = domain + images[i];
                }

                image.src = images[i];
                image.onload = appendSmallPreview.bind(this, image, $container);
                image.onerror = function(){
                    loadingCount--;
                    isAllPreviewLoaded();
                };
            }
        }

        $(".preview-small").live("click", clickPreview);
    }

    //单幅图片加载完成后的回调方法
    function appendSmallPreview(image, $container){
        var width = image.naturalWidth ?
                        image.naturalWidth :
                        image.width;
        var height = image.naturalHeight ?
                        image.naturalHeight :
                        image.height;
        //过滤掉宽度或高度小于100的图片
        //考虑缩略图的大小后，尽量过滤到垃圾图片
        if (width < 100 || height < 100) {
            loadingCount--;
            isAllPreviewLoaded();
            return true;
        }

        $("<div class='preview-small'>"+
            "<img src='" + image.src + "' width='80' height='80' "+
               "data-width='"+width+"' data-height='"+height+"'" +"/>"+
         "</div>").
        appendTo($container);

        loadingCount--;
        isAllPreviewLoaded();
    }

    //点击小图预览后的回调方法
    function clickPreview(d){
        var target = d.target || d.srcElement;
        var imageSrc = target.src;

        createPreview(imageSrc, $("#crawlDiv"));


        //初始化color picker
        $("#color").colorPicker({
            onColorChange: function(id, newColor){
                $("#picColor").val(newColor);
            }
        });


        $("#picHeight").val(target.getAttribute('data-height'));
        $("#picWidth").val(target.getAttribute('data-width'));
        $("#filename").val(imageSrc);

        var offsetTop = $(".preview").offset().top;
        self.addPic();
        $("html, body").animate({"scrollTop": offsetTop - 20});
    }

    //当所有缩略图加载完毕后的回调
    function isAllPreviewLoaded(){
        if (loadingCount === 0) {
            removeAjaxMessage();
            if ($(".preview-small").length === 0) {
                $(".preview-small-container label").text('目标网页中没有分析到有效的照片！');
            }
        }
    }
};

gbks.common.uploadImage();

