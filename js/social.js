$("#sinaSignupBtn").click(function(e){
    e.preventDefault();
    if (typeof WB2 !== "object") {
        console.log("微博登录组件未加载！");
        return false;
    }
    weiboLogin("register", "sinaSignupBtn");
});

$("#sinaLoginBtn").click(function(e){
    e.preventDefault();
    weiboLogin("login", "sinaLoginBtn");
});

$("#qqSignupBtn").click(function(e){
    e.preventDefault();
    qqLogin("register");
    $(this).text("自动注册中……");
});

$("#qqLoginBtn").click(function(e){
    e.preventDefault();
    qqLogin("login");
    $(this).text("登录中……");
});


function weiboLogin(op, id){
    //微博登录
    WB2.anyWhere(function(W){
        W.widget.connectButton({
            id: id,
            type: '7,5',
            callback : {
                login:function(o){
                    var id = o.idstr;
                    var d = new Date(o.created_at);
                    var year = d.getFullYear();
                    var month = d.getMonth() + 1;
                    month = month < 10 ? '0'+month : month;
                    var day = d.getDate();
                    day = day < 10 ? '0'+day : day;
                    var create_date = year+month+day;
                    var user_email = id + '@sina.com';
                    var avatar = o.avatar_hd;
                    var avatar_small = o.avatar_large;
                    var display_name = o.screen_name;

                    $.post(CANON_ABSPATH+'/functions/social.php', {
                        op: op,
                        display_name: display_name,
                        nonce: nonce,
                        user_email: user_email,
                        avatar: avatar,
                        avatar_small: avatar_small,
                        create_date: create_date,
                        id: id
                    }, function(result){
                        if (result.error) {
                            alert(result.message);
                        }
                        else{
                            if ($("#next").val()) {
                                location.assign($("#next").val());
                            }
                            else{
                                location.assign('/');
                            }
                            return true;
                        }
                    });
                },
                logout:function(){
                    location.reload();
                }
            }
        });
    });
}

function qqLogin(op){
    T.init({
        appkey: 801489010
    });

    if (!T.loginStatus()) {
        T.login(function (loginStatus) {
            getUserInfo();
        }, function (loginError) {
            alert(loginError.message);
        });
    }
    else {
        getUserInfo();
    }

    function getUserInfo() {
        T.api("/user/info")
            .success(function (o) {
                if (o.ret === 0) {
                    o = o.data;
                    var id = o.openid;
                    var d = new Date(o.regtime);
                    var year = d.getFullYear();
                    var month = d.getMonth() + 1;
                    month = month < 10 ? '0'+month : month;
                    var day = d.getDate();
                    day = day < 10 ? '0'+day : day;
                    var create_date = year+month+day;
                    var user_email = id + '@qq.com';
                    var avatar = o.head+'/120';
                    var avatar_small = o.head+'/50';
                    var display_name = o.nick;

                    $.post(CANON_ABSPATH+'/functions/social.php', {
                        op: op,
                        display_name: display_name,
                        nonce: nonce,
                        user_email: user_email,
                        avatar: avatar,
                        avatar_small: avatar_small,
                        create_date: create_date,
                        id: id
                    }, function(result){
                        if (result.error) {
                            alert(result.message);
                        }
                        else{
                            if ($("#next").val()) {
                                location.assign($("#next").val());
                            }
                            else{
                                location.assign('/');
                            }
                            return true;
                        }
                    });
                } else {
                    alert(o.ret);
                }
            })
            .error(function (code, message) {
                alert(message);
            });
    }
}