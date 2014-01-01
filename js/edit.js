$("#updateBtn").bind("click", function(e){
    e.preventDefault();

    var pid = $("#pid").val();
    var referrer = $("#referrer").val();
    var title = $("#title").val();
    var category = $("#cat").val();

    if (!pid) {
      alert("无法获取图片信息，请尝试刷新！");
      return false;
    }

    if (typeof nonce === undefined) {
        alert("获取用户登录凭证失败！");
        return false;
    }

    $(".ajax-message").remove();
    $("#updateDiv .op").append("<div class='ajax-message'>"+
                              "<img src='" + ABSPATH + "/img/loader.gif' />"+
                              "<span>信息更新中……</span>"+
                          "</div>");

    $.ajax({
        url: ABSPATH + '/functions/update_pic.php',
        type: 'post',
        dataType: 'json',
        data: {
            pid: pid,
            referrer: referrer,
            title: title,
            category: category,
            nonce: nonce
        },
        success: function(result){
            $(".ajax-message").empty();
            if (result.error) {
              alert(result.message);
              return false;
            }
            else{
                $(".ajax-message").append(result.message + "，跳转中……");
            }

            location.href = "/?p=" + result.pid;
        }
    });
});