//修改选项文字
$(".options").each(function(i,v){
    var $op = $(v);
    var $save = $op.find(".save");
    var $like = $op.find(".like");

    $save.html('<br/><span>&#10003;通过</span>');
    $like.html('<br/><span>&#x2717;删除</span>');

    //通过
    $save.bind("click", adminOp);
    //删除
    $like.bind("click", adminOp);

    function adminOp(e){
        e.stopPropagation();

        var action = "pass";
        if (this.className == "like") {
            action = "delete";
        }

        var pid = $save.attr("data-id");
        $.ajax({
            url: ABSPATH + '/functions/admin_review_pic.php',
            type: 'POST',
            data: {
                imageId: pid,
                nonce: nonce,
                action: action
            },
            success: function(result){
                if (result.error) {
                    alert(result.message);
                }
                else{
                    $save.closest('.tile').fadeOut().remove();
                    //更新待审核数目
                    var reviewCount = $("#reviewCount").text();
                    reviewCount = parseInt(reviewCount, 10) - 1;
                    $("#reviewCount").text(reviewCount);
                }
            }
        });
    }
});