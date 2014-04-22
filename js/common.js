//核心对象 gbks，用于整个网站交互
var gbks = gbks || {};
gbks.common = gbks.common || {};

//模拟console
window.console || (console = {
    log: function () {}
});


var gbks = gbks || {};
gbks.common = gbks.common || {};
gbks.common.Loader = gbks.common.Loader || {};
gbks.common.Loader.show = function (e) {
    var t = gbks.common.Loader.getLoader();
    t.stop();
    e && e.length > 0 ? t.html(e) : t.html("");
    t.show();
    t.animate({
        opacity: 1
    }, 50);
};
gbks.common.Loader.hide = function () {
    var e = gbks.common.Loader.getLoader();
    e.stop();
    var t = null;
    e.animate({
        opacity: 0
    }, 250, t)
};
gbks.common.Loader.onHide = function (e) {
    gbks.common.Loader.getLoader().hide();
};
gbks.common.Loader.getLoader = function () {
    return $("#loader");
};
gbks.common.track = function () {
};
gbks.common.onWindowError = function (e, t, n) {
};
gbks.common.wrapPopupContent = function (e, t, n) {
    var r = n ? " horizontal" : "",
        i = '<div id="' + e + '" class="popupWrap' + r + '">';
    n ? i += '<div class="arrow"><img class="left" width="17" height="29" src="'+CANON_ABSPATH+'/img/overlay-arrow-left.png"><img class="right" width="17" height="29" src="'+CANON_ABSPATH+'/img/overlay-arrow-right.png"></div>' : i += '<div class="arrow"><img class="up" width="29" height="17" src="'+CANON_ABSPATH+'/img/overlay-arrow-up.png"><img class="down" width="29" height="17" src="'+CANON_ABSPATH+'/img/overlay-arrow-down.png"></div>';
    i += t;
    i += "</div>";
    return i;
};
gbks.common.positionPopupHorizontal = function (e, t) {
    var n = $(window).height(),
        r = t.offset(),
        i = e.height();
    if (i == 0) return !1;
    var s = Math.round(r.top + t.height() / 2 - i / 2) - 2,
        o = s + i,
        u = n - 10 - o,
        a = i / 2;
    if (u < 0) {
        s += u;
        a -= u
    }
    if (s < 0) {
        s = 10;
        a += s - 10
    }
    var f = r.left + t.width() + 17;
    e.css({
        left: f + "px",
        top: s + "px"
    });
    $(".arrow", e).css("top", a + "px");
    return !0
};
gbks.common.positionPopup = function (e, t) {
    var n = $(window).width(),
        r = t.offset(),
        i = e.width(),
        s = Math.round(r.left + t.width() / 2 - i / 2) - 2,
        o = s + i,
        u = n - 10 - o,
        a = i / 2;
    if (u < 0) {
        s += u;
        a -= u
    }
    if (s < 0) {
        s -= s - 10;
        a += s - 10
    }
    var f = $(window).height(),
        l = f / 2,
        c = t.offset().top - window.pageYOffset + t.height() / 2,
        h = c,
        p = f - h,
        d = h > p;
    d ? e.addClass("topper") : e.removeClass("topper");
    var v = r.top + t.height() + 17;
    d && (v = r.top - 17 - e.height());
    e.css({
        left: s + "px",
        top: v + "px"
    });
    console.log("positionPopup", t.offset(), t.position());
    $(".arrow", e).css("left", a + "px")
};
gbks.common.Cookie = function (e, t, n) {
    if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(t)) || t === null || t === undefined)) {
        n = $.extend({}, n);
        if (t === null || t === undefined) n.expires = -1;
        if (typeof n.expires == "number") {
            var r = n.expires,
                i = n.expires = new Date;
            i.setDate(i.getDate() + r);
        }
        t = String(t);
        return document.cookie = [encodeURIComponent(e), "=", n.raw ? t : encodeURIComponent(t), n.expires ? "; expires=" + n.expires.toUTCString() : "", n.path ? "; path=" + n.path : "", n.domain ? "; domain=" + n.domain : "", n.secure ? "; secure" : ""].join("")
    }
    n = t || {};
    var s = n.raw ? function (e) {
            return e
        } : decodeURIComponent,
        o = document.cookie.split("; ");
    for (var u = 0, a; a = o[u] && o[u].split("="); u++)
        if (s(a[0]) === e) return s(a[1] || "");
    return null;
};
gbks.common.Shortcuts = function () {
    this.init = function () {
        this.keyUpMethod = $.proxy(this.onKeyUp, this);
        $(document).bind("keyup", this.keyUpMethod);
    };
    this.onKeyUp = function (e) {
        var t = $("*:focus");
        if (t.length > 0) return;
        switch (e.which) {
        case 83:
            var n = "fadeSaved",
                r = $("body");
            r.hasClass(n) ? r.removeClass(n) : r.addClass(n);
            break;
        case 71:
            this.toggleGroupOverlay();
            break;
        case 73:
            this.toggleTileInfo();
        }
    };
    this.toggleTileInfo = function () {
        var e = $("body"),
            t = "hideTileInfo";
        e.hasClass(t) ? e.removeClass(t) : e.addClass(t);
        gbks.tilesInstance && gbks.tilesInstance.layout();
    };
    this.toggleGroupOverlay = function () {
        if (this.groupsOverlay) {
            var e = $(".wrap", this.groupsOverlay);
            if (this.groupsOverlay.is(":visible")) {
                e.removeClass("zapMeIn");
                e.addClass("zapMeOut");
                var t = this.groupsOverlay;
                setTimeout(function () {
                    t.hide();
                }, 150);
            } else {
                e.addClass("zapMeIn");
                e.removeClass("zapMeOut");
                this.groupsOverlay.show();
            }
        } else this.loadGroupOverlay();
    };
    this.loadGroupOverlay = function () {
        if (!this.groupsOverlay) {
            this.groupsOverlay = $('<div id="quickGroupNav"><div class="wrap"><div class="wrap2"><div class="content clearfix"></div></div></div></div>');
            this.groupsOverlay.click($.proxy(this.onClickOverlay, this));
            $("body").append(this.groupsOverlay);
            $(".content", this.groupsOverlay).load("/autocomplete/quicknav", $.proxy(this.onLoadGroupOverlay, this))
        }
    };
    this.onClickOverlay = function (e) {
        var t = $(e.currentTarget),
            n = t.parents("#quickGroupNav .content").length > 0;
        n || this.toggleGroupOverlay()
    };
    this.onLoadGroupOverlay = function () {
        this.groupsOverlay.addClass("loaded");
        var e = $(".wrap", this.groupsOverlay);
        e.addClass("zapMeIn")
    }
};
gbks.common.shortcutInstance = new gbks.common.Shortcuts;
gbks.common.shortcutInstance.init();
gbks.common.history = gbks.common.history || {};
gbks.common.history.push = function (e, t, closeFlag) {
    if (gbks.common.history.supported()) {

        history.pushState({
            url: e,
            title: t
        }, t, e);

        //虽然pushState支持title参数，但是不会自动更新document.title
        //因此此处手动更新之
        //若是打开，则在当前title前续加上图片名称
        if (t && !closeFlag) {
            document.title = t + ' _ ' + document.title;
        }
        //若是关闭，则直接恢复原title
        else if (t && closeFlag) {
            document.title = t;
        }
        return !0
    }
    return !1
};
gbks.common.history.onChange = function (e) {
    var t = e.state;
};
gbks.common.history.supported = function () {
    return typeof history.pushState != "undefined"
};
gbks.common.scroller = gbks.common.scroller || {};
gbks.common.scroller.scrollToPosition = function (e) {
    gbks.common.scroller.scrollInfo = {
        startTime: (new Date).getTime(),
        startValue: window.pageYOffset,
        endValue: e,
        duration: 1500,
        lastUpdate: (new Date).getTime()
    };
    $(window).unbind("mousewheel", gbks.common.scroller.mousewheelFunction);
    $(window).bind("mousewheel", gbks.common.scroller.mousewheelFunction);
    clearTimeout(gbks.common.scroller.scrollInterval);
    gbks.common.scroller.scrollInterval = setTimeout($.proxy(gbks.common.scroller.onScrollInterval, gbks.common.scroller), 25)
};
gbks.common.scroller.onScrollInterval = function (e) {
    var t = gbks.common.scroller.scrollInfo,
        n = (new Date).getTime() - t.startTime;
    n = Math.min(n, t.duration);
    var r = gbks.common.scroller.easeInOutCubic(null, n, t.startValue, t.endValue - t.startValue, t.duration);
    window.scrollTo(0, r);
    if (Math.abs(n) < t.duration) {
        var i = (new Date).getTime() - t.lastUpdate,
            s = Math.max(5, 25 - i);
        clearTimeout(gbks.common.scroller.scrollInterval);
        gbks.common.scroller.scrollInterval = setTimeout($.proxy(gbks.common.scroller.onScrollInterval, gbks.common.scroller), s)
    } else $(window).unbind("mousewheel", gbks.common.scroller.mousewheelFunction);
    t.lastUpdate = (new Date).getTime()
};
gbks.common.scroller.onMouseWheel = function (e) {
    $(window).unbind("mousewheel", gbks.common.scroller.mousewheelFunction);
    clearInterval(gbks.common.scroller.scrollInterval)
};
gbks.common.scroller.easeInOutCubic = function (e, t, n, r, i) {
    return (t /= i / 2) < 1 ? r / 2 * t * t * t + n : r / 2 * ((t -= 2) * t * t + 2) + n
};
gbks.common.scroller.mousewheelFunction = $.proxy(gbks.common.scroller.onMouseWheel, gbks.common.scroller);

gbks.common.zapIn = function (e) {
    if (Modernizr.cssanimations) {
        e.removeClass("zapMeOut");
        e.addClass("zapMeIn");
    } else e.show();
};
gbks.common.zapOut = function (e, t, n) {
    if (Modernizr.cssanimations) {
        e.removeClass("zapMeIn");
        e.addClass("zapMeOut")
    } else e.hide();
    t && setTimeout(t, 150);
    n && setTimeout(e.hide, 150)
};


//核心功能，实现点击图片弹出窗口展现主要内容
var gbks = gbks || {};
gbks.common = gbks.common || {};
gbks.common.lightboxInstance = null;
gbks.common.Lightbox = function () {

    if (gbks.common.lightboxInstance) {
        gbks.common.lightboxInstance.hide();
        gbks.common.lightboxInstance = null
    }
    gbks.common.lightboxInstance = this;

    //初始化
    this.init = function () {
        this.canvas = null;
        this.hideTimer = null;
        this.savePopup = null;
        this.sharePopup = null;
        this.auth = $("body").hasClass("auth");
        this.layoutMode = gbks.common.Cookie("layout");
        this.updateHistory();
        this.initHistory = window.location.href;
        this.initTitle = window.document.title;
        this.keyUpMethod = $.proxy(this.onKeyUp, this);
        this.commentKeyUpMethod = $.proxy(this.onCommentKeyUp, this);
        this.resizeMethod = $.proxy(this.onResize, this);
        this.scrollMethod = $.proxy(this.onScroll, this);
    };

    //更新Histroy对象
    this.updateHistory = function () {
        this.initHistory = window.location.href;
        this.initTitle = window.document.title;
    };

    this.display = function () {};

    //隐藏详细内容，同时更新history对象
    this.hide = function () {
        if (this.canvas) {
            this.canvas.unbind("scroll", this.scrollMethod);
            this.canvas.remove();
            this.canvas = null;
            gbks.common.history.push(this.initHistory, this.initTitle, true);
            this.hidePopups();
            if (this.savePopup) {
                this.savePopup.hide();
                this.savePopup = null
            }
            if (this.sharePopup) {
                this.sharePopup.hide();
                this.sharePopup = null
            }
            $(window).unbind("resize", this.resizeMethod)
        }
        $("body").removeClass("lightboxActive");
        gbks.tilesInstance.startEndlessScroll()
    };

    //创建弹窗
    this.createCanvas = function () {
        if (!this.canvas) {
            console.log("creating canvas");
            var e = "";
            Modernizr.cssanimations && Modernizr.opacity ? e = '<div id="lightbox" data-id="0">' : e = '<div id="lightbox" data-id="0">';
            e += '  <div class="cover"></div>';
            e += '  <div class="loader"></div>';
            e += '  <div class="contentWrap">';
            e += '  <div class="lightboxContent"></div>';
            e += "  </div>";
            e += '  <div class="closeButton"></div>';
            e += "</div>";
            $("body").append(e);

            this.canvas = $("#lightbox");
            this.canvas.click($.proxy(this.onClickLightbox, this));

            $(".similar li a", this.canvas).live("click", $.proxy(this.onClickSimilarImage, this));

            $(document).keyup(this.keyUpMethod);
            $(window).bind("resize", this.resizeMethod);

            this.canvas.bind("scroll", this.scrollMethod);
            this.layoutMode == "big" && this.canvas.addClass("biggy");
        }
        $("body").addClass("lightboxActive");
        this.canvas.focus()
    };
    this.fadeIn = function () {
        this.canvas.removeClass("hidden")
    };

    //点击相似图像
    this.onClickSimilarImage = function (e) {
        e.preventDefault();
        e.stopPropagation();
        gbks.common.track("Polaroid", "Lightbox", "Similar");
        var t = $(e.currentTarget),
            n = t.attr("data-id");
        this.updateFromId(n)
    };

    //判断是否全屏
    this.detectFullscreen = function () {
        for (var e = 0; e < Modernizr._domPrefixes.length; e++)
            if (document[Modernizr._domPrefixes[e].toLowerCase() + "CancelFullScreen"]) return !0;
        return !!document.cancelFullScreen || !1
    };

    //若点击的不是链接或图片，则关闭弹窗
    this.onClickLightbox = function (e) {
        var t = $(e.target),
            n = t.parents("#lightboxDetails").length > 0;

        if ($("#sharePopup").is(":visible")) {
            $("#sharePopup").fadeOut(100);
            $(".shareButton").removeClass("active");
            return false;
        }

        if (!t.is("img") && !t.is("a") && !n) {
            e.preventDefault();
            e.stopPropagation();
            this.hide();
            $(document).unbind("keyup", this.keyUpMethod)
        }
    };

    //判断键盘操作
    this.onKeyUp = function (e) {
        var t = $("*:focus");
        if (t.length > 0) return;
        if (this.canvas) switch (e.which) {
        case 37:
            this.previous();
            break;
        case 39:
            this.next();
            break;
        case 70:
            this.goFullScreen()
        }
    };

    //进入全屏
    this.goFullScreen = function () {
        if (this.detectFullscreen()) {
            var e = this.canvas[0];
            e.requestFullScreen ? e.requestFullScreen() : e.mozRequestFullScreen ? e.mozRequestFullScreen() : e.webkitRequestFullScreen && e.webkitRequestFullScreen()
        }
    };

    //获取前一幅图像
    this.previous = function (e) {
        gbks.common.track("Polaroid", "Lightbox", "Previous");
        var t = $(".tile"),
            n = this.canvas.attr("data-id"),
            r = $("#image_" + n),
            i;
        if (r.length > 0) {
            var s = t.index(r);
            if (s > 0) {
                i = $(t[s - 1]);
                while (i.hasClass("ad") && i != null && i.length > 0) {
                    s--;
                    i = $(t[s - 1])
                }
            }
        }
        if (i && i.length > 0 && i.attr("id") != undefined) this.update(i);
        else {
            var o = $(".similar a", this.canvas);
            if (o.length > 0) {
                var u = $(o[o.length - 1]),
                    a = u.attr("data-id");
                this.updateFromId(a)
            } else this.hide()
        } if (e) {
            e.preventDefault();
            e.stopPropagation()
        }
        this.hideSharePopup()
    };

    //获取下一幅图像
    this.next = function (e) {
        gbks.common.track("Polaroid", "Lightbox", "Next");
        var t = $(".tile"),
            n = this.canvas.attr("data-id"),
            r = $("#image_" + n),
            i;
        if (r.length > 0) {
            var s = t.index(r);
            if (s < t.length - 1) {
                i = $(t[s + 1]);
                while (i.hasClass("ad") && i != null && i.length > 0) {
                    s++;
                    i = $(t[s + 1])
                }
            }
        }
        if (i && i.length > 0 && i.attr("id") != undefined) this.update(i);
        else {
            var o = $(".similar a", this.canvas);
            if (o.length > 0) {
                var u = $(o[0]).attr("data-id");
                this.updateFromId(u)
            } else this.hide()
        } if (e) {
            e.preventDefault();
            e.stopPropagation()
        }
        this.hideSharePopup()
    };
    this.update = function (e) {
        this.activeTile = e;
        gbks.tilesInstance.stopEndlessScroll();
        var t = e.attr("id"),
            n = t.split("_");
        this.imageId = n[1];
        if (!this.canvas) {
            this.createCanvas();
            this.createPlaceholder(e);
        }
        this.updateFromId(this.imageId)
    };
    this.updateFromId = function (e) {
        this.imageId = e;
        this.canvas.addClass("loading");
        clearTimeout(this.hideTimer);
        this.hideTimer = setTimeout($.proxy(this.updateContent, this), 10)
    };
    this.getCenterPosition = function (e, t) {
        var n = $(".left", this.canvas).position(),
            r = {
                x: 0,
                y: 0
            };
        if (n) {
            var i = n.left,
                s = $(window).height(),
                o = Math.round((i - e - 20) / 2),
                u = Math.round((s - t - 20) / 2);
            u = Math.max(u, 30);
            r = {
                x: o,
                y: u
            }
        }
        return r
    };
    this.updateContent = function () {
        this.canvas.attr("data-id", this.imageId);
        gbks.common.track("Polaroid", "Lightbox", this.imageId);
        this.loadDetails()
    };
    this.onImageLoaded = function () {
        var e = $(".image .wrap a img", this.canvas);
        if (e.length > 0 && !(e.attr("width") > 0)) {
            e.removeAttr("width");
            e.removeAttr("height");
            e.attr("data-width", e.width());
            e.attr("data-height", e.height());
            this.updateLayout()
        }
        $(".image .wrap", this.canvas).removeClass("loading");
        $(".loader", this.canvas).fadeOut(250);
        $(".arrowLeft", this.canvas).click($.proxy(this.previous, this));
        $(".arrowRight", this.canvas).click($.proxy(this.next, this));
        $(".layoutButton", this.canvas).click($.proxy(this.toggleLayoutMode, this));
        this.canvas.removeClass("loadingBigImage");
        setTimeout($.proxy(this.removePreview, this), 250);
        var t = $("#commentForm textarea", this.canvas);
        t.focus($.proxy(this.onFocusCommentField, this));
        t.blur($.proxy(this.onBlurCommentField, this))
    };
    this.removePreview = function () {
        $(".preview", this.canvas).remove();
    };
    this.toggleLayoutMode = function (e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation()
        }
        if (this.layoutMode == "big") {
            this.layoutMode = "small";
            this.canvas.removeClass("biggy")
        } else {
            this.layoutMode = "big";
            this.canvas.addClass("biggy")
        }
        gbks.common.Cookie("layout", this.layoutMode, {
            path: "/"
        });
        this.updateLayout()
    };


    this.createPlaceholder = function () {
        var e = $("#image_" + this.imageId),
            t = e.attr("data-w"),
            n = e.attr("data-h"),
            r = $(window),
            i = r.width(),
            s = r.height(),
            o = 318,
            u = 600,
            a = Math.min(960, i - 60);
        this.layoutMode == "big" && (a = i - 60);
        var f = a - o,
            l = Math.min(t, f),
            c = l * n / t,
            h = Math.max(c, u);
        this.layoutMode == "big" && (h = Math.max(s - 40, h));
        var p = Math.max(20, Math.round((s - h) / 2)),
            d = Math.floor((f - l) / 2),
            v = Math.max(0, Math.round((h - c) / 2)),
            m = '<div class="lightboxContent" style="top: ' + p + "px; width: " + a + "px; height: " + h + 'px;">';
        m += ' <div class="preview">';
        m += '   <div class="wrap">';
        m += '     <div class="imageWrap">';
        m += "       <a>";
        m += '         <img src="' + $(".imageLink img", e).attr("src") + '" width="' + l + '" height="' + c + '" style="margin-top: ' + v + "px; margin-left:" + d + 'px;">';
        m += "       </a>";
        m += "     </div>";
        m += "   </div>";
        m += " </div>";
        m += ' <div class="details"></div>';
        m += "</div>";
        $(".contentWrap", this.canvas).html(m);
        var g = $(".contentWrap", this.canvas),
            y = Math.max(s, h + 40);
        h >= s - 40 ? g.css("height", y + "px") : g.css("height", "")
    };


    this.updateLayout = function () {
        var e = $(window),
            t = e.width(),
            n = e.height(),
            r = $(".lightboxContent .image .wrap a img", this.canvas);
        r.length == 0 && (r = $(".lightboxContent .image iframe", this.canvas));
        var i = r.attr("data-width"),
            s = r.attr("data-height");
        i == 0 && (i = r.width());
        s == 0 && (s = r.height());
        if (i == 0 || s == 0) {
            var o = $("#image_" + this.imageId);
            if (o.length == 1) {
                i = o.attr("data-w");
                s = o.attr("data-h")
            }
            if (i == 0 || s == 0) {
                r = $(".lightboxContent .image .wrap a img", this.canvas);
                if (r.length > 0) {
                    r.removeAttr("width");
                    r.removeAttr("height");
                    r.removeAttr("data-width");
                    r.removeAttr("data-height");
                    r.css("width", "");
                    r.css("height", "")
                }
                return
            }
        }
        var u = 318,
            a = 600,
            f = Math.min(960, t - 60);
        this.layoutMode == "big" && (f = t - 60);
        var l = f - u,
            c = Math.min(i, l),
            h = c * s / i;
        console.log("layoutMode", this.layoutMode);
        console.log("image size", i, s);
        console.log("new image size", c, h);
        var p = Math.max(h, a);
        this.layoutMode == "big" && (p = Math.max(n - 40, p));
        var d = Math.max(20, Math.round((n - p) / 2)),
            v = Math.floor((l - c) / 2),
            m = Math.max(0, Math.round((p - h) / 2));
        r.css({
            "margin-top": m + "px",
            left: v + "px",
            width: c + "px",
            height: h + "px"
        });
        var g = $(".lightboxContent", this.canvas);
        g.css({
            top: d + "px",
            width: f + "px",
            height: p + "px"
        });
        var y = $(".lightboxContent .contentWrap", this.canvas),
            b = Math.max(n, p + 40);
        p >= n - 40 ? y.css("height", b + "px") : y.css("height", "");
        this.updateActivityScrolling(p)
    };
    this.updateActivityScrolling = function (e) {
        var t = $(".activity", this.canvas);
        if (t.length > 0) {
            var n = t.position(),
                r = n.top + t.height();
            // if (r > e) {
                var i = e - n.top;
                console.log("newHeight", i);
                t.css("height", i - 11 + "px");
                t.addClass("scrollable")
            // } else {
            //     t.css("height", "");
            //     t.removeClass("scrollable")
            // }
        }
    };
    this.positionImage = function () {
        var e = $(".image a", this.canvas),
            t = $("img", e),
            n = this.getCenterPosition(t.width(), t.height());
        e.css({
            position: "absolute",
            left: n.x + "px",
            top: n.y + "px"
        });
        t.css("margin", 0);
        this.canvas.removeClass("loading");
        var t = $(".image .wrap a img", this.canvas),
            r = parseInt(t.attr("height"));
        r == 0 && t.attr("height", "")
    };
    this.loadDetails = function () {
        this.canvas.addClass("loading");
        $.ajax({
            url: CANON_ABSPATH + '/functions/get_pic_detail.php?imageId='+this.imageId+
                '&userId='+ pageConfig.userId,
            dataType: "json",
            type: "POST",
            success: $.proxy(this.onLoadDetails, this)
        });
    };
    this.resizeImage = function () {
        var e = $(".image .wrap img", this.canvas);
        e.length == 0 && (e = $(".image .wrap iframe"));
        var t = e.attr("data-width"),
            n = e.attr("data-height");
        if (window.devicePixelRatio && window.devicePixelRatio > 1) {
            t /= window.devicePixelRatio;
            n /= window.devicePixelRatio
        }
        var r = $(".image", this.canvas).width(),
            i = $(window).height() - 80,
            s = r - 60,
            o = $(window).height() - 180,
            u = t / n;
        t = Math.min(t, s);
        n = t / u;
        n > i;
        t = Math.round(t);
        n = Math.round(n);
        e.css({
            width: t + "px",
            height: n + "px"
        })
    };
    this.onLoadDetails = function (e) {
        if (e && e.html) {
            $(".image", this.canvas).remove();
            $(".details", this.canvas).remove();

            var t = $(".lightboxContent", this.canvas);
            this.canvas.addClass("loadingBigImage");
            t.append($(e.html));

            var n = $(".details .similar img", this.canvas);
            Modernizr.cssanimations ? n.bind("load", function (e) {
                $(this).addClass("ready");
            }) : n.addClass("ready");

            this.resizeImage();
            gbks.common.history.push(e.history, e.title);

            if (this.auth) {
                $(".details #addImageButton", this.canvas).click($.proxy(this.onClickSaveImage, this));
                $(".details #likeImageButton", this.canvas).click($.proxy(this.onClickLikeImage, this));
                $(".details #unlikeImageButton", this.canvas).click($.proxy(this.onClickLikeImage, this));
                $(".details #shareImageButton", this.canvas).click($.proxy(this.onClickShareImage, this));
                $(".details button.follow", this.canvas).click($.proxy(this.onClickFollowButton, this));
            }
            else{
                //未登录Lightbox中评论按钮
                $(".details #commentForm textarea").live("keyup", function(e){
                    e.stopPropagation();

                    var imageId = $(this).closest('#commentForm').attr("data-imageid");
                    location.href = "/signup?next=" + encodeURIComponent("/?p=" + imageId);
                });

                //未登录lightbox中喜欢与保存按钮
                $(".details .likeButton, .details .saveButton").live("click", function(e){
                    e.stopPropagation();

                    var $this = $(this);
                    var imageId = $this.attr("data-id");

                    location.href = "/signup?next=" + encodeURIComponent("/?p="+imageId);
                });

                //未登录lightbox中关注按钮跳转
                $(".details .follow").live("click", function(e){
                    e.stopPropagation();

                    var userId = $(this).attr("data-id");
                    location.href = "/signup?next=" + encodeURIComponent("/profile/" + userId);
                });
            }
            $(".details .expand", this.canvas).click($.proxy(this.onClickExpand, this));

            this.updateLayout();

            var r = $(".image .wrap a img", this.canvas);
            if (r.length > 0) {
                r.load($.proxy(this.onImageLoaded, this));
            }
            else {
                this.canvas.removeClass("loadingBigImage");
                this.onImageLoaded();
            }
        }
        this.canvas.removeClass("loading")
    };

    this.onClickExpand = function (e) {
        e.preventDefault();
        e.stopPropagation();
        var t = $(e.currentTarget),
            n = $(t.parents("p")[0]),
            r = $(".expand", n),
            i = $(".hidden", n);
        r.hide();
        i.show()
    };
    this.onScroll = function (e) {
        this.sharePopup && this.sharePopup.updatePosition();
        this.savePopup && this.savePopup.updatePosition();
        this.commentPopup && this.commentPopup.updatePosition()
    };
    this.onResize = function (e) {
        this.sharePopup && this.sharePopup.updatePosition();
        this.savePopup && this.savePopup.updatePosition();
        this.commentPopup && this.commentPopup.updatePosition();
        $(".contentPreview", this.canvas).remove();
        this.updateLayout();
        return
    };


    /*******************************************
     *  分享
     *******************************************/

    this.hideSharePopup = function () {
        if (this.sharePopup) {
            this.sharePopup.hide();
            this.sharePopup = null;
        }
    };
    //点击分享按钮
    this.onClickShareImage = function (e) {
        e.stopPropagation();
        e.preventDefault();
        var t = $(e.currentTarget),
            n = t.attr("data-id");
        this.hidePopups("share");
        if (t.hasClass("active")) {
            t.removeClass("active");
            $("#sharePopup").fadeOut(200);
            // this.onHideSharePopup()
        } else {
            t.addClass("active");
            $("#sharePopup").fadeIn(200);
            $("#sharePopup").one("click", function(){
                $(this).hide();
                t.removeClass('active');
            });
            // this.sharePopup = new gbks.common.SharePopup;
            // this.sharePopup.display(n, t, $.proxy(this.onHideSharePopup, this))
        }
    };
    this.hidePopups = function (e) {
        e != "save" && this.hideSavePopup();
        e != "share" && this.onHideSharePopup();
    };
    this.onHideSharePopup = function (e) {
        if (this.sharePopup) {
            this.sharePopup.hide();
            this.sharePopup = null;
        }
        $("#shareImageButton", this.canvas).removeClass("active");
    };

    /*******************************************
     *  保存
     *******************************************/

    //点击保存图片按钮
    this.onClickSaveImage = function (e) {
        e.stopPropagation();
        e.preventDefault();

        this.hidePopups("save");

        var t = $(e.currentTarget),
            n = t.attr("data-id");

        t.addClass("active");
        t.addClass("loading");

        $.ajax({
            url: CANON_ABSPATH + "/functions/save_pic.php",
            type: "POST",
            data: {userId: pageConfig.userId, imageId: n, nonce: nonce},
            dataType: "json",
            success: $.proxy(this.onAddImageComplete, this)
        });
    };

    this.onAddImageComplete = function (e) {
        if (e.error) {
            alert(e.message);
        }
        var t = $(".details #addImageButton", this.canvas);
        t.removeClass("loading");
        this.savePopup = new gbks.common.SavePopup;
        this.savePopup.init(t,
                            e,
                            $.proxy(this.onClickRemoveImage, this),
                            $.proxy(this.onCloseSavePopup, this));
    };

    this.hideSavePopup = function () {
        this.savePopup && this.savePopup.hide();
    };
    this.onCloseSavePopup = function (e) {};
    this.onClickRemoveImage = function () {
        var e = $(".details #addImageButton", this.canvas);
        e.removeClass("active")
    };


    /*******************************************
     *  喜欢
     *******************************************/

    //点击喜欢按钮
    this.onClickLikeImage = function (e) {

        e.stopPropagation();
        e.preventDefault();
        var t = $(e.currentTarget),
            n = t.attr("data-id"),
            r = !t.hasClass("active");
        r ? t.addClass("active") : t.removeClass("active");
        t.addClass("loading");

        $.ajax({
            url: CANON_ABSPATH + "/functions/like_pic.php",
            data: {
                imageId: n,
                userId: pageConfig.userId || USER_ID,
                nonce: nonce
            },
            type: "POST",
            success: $.proxy(this.onLikeComplete, this),
            error: $.proxy(this.onLikeComplete, this)
        });
    };

    this.onLikeComplete = function (e) {
        if (e.error) {
            alert(e.message);
        }
        else{
            $(".details #likeImageButton", this.canvas).removeClass("loading");
            $(".details #unlikeImageButton", this.canvas).removeClass("loading");
        }
    };


    /*******************************************
     *  关注
     *******************************************/

    //点击关注按钮
    this.onClickFollowButton = function (e) {
        e.preventDefault();
        e.stopPropagation();

        var t = $(e.currentTarget),
            r = t.attr("data-id"),
            i = t.hasClass("active");

        if (r) {
            var s = CANON_ABSPATH + "/functions/follow_user.php",
                o = "已关注",
                data = {
                    targetId: r,
                    nonce: nonce
                };

            //若未关注，点击后关注
            if (!i) {
                t.addClass("active");
                data.action = "follow";
            }
            //若已关注，点击后取消关注
            else {
                o = "关 注";
                t.removeClass("active")
                data.action = "unfollow";
            }
            t.html(o);
            $.ajax({
                url: s,
                data: data,
                type: "POST",
                success: $.proxy(this.onSubmitFollow, this)
            });
        }
    };
    this.onSubmitFollow = function (e, t, n) {
        if (e.error) {
            alert(e.message);
        }
    };


    /*******************************************
     *  评论
     *******************************************/

    this.onFocusCommentField = function (e) {
        var t = $("#commentForm", this.canvas),
            n = $("textarea", t);
        t.addClass("active");
        $("textarea", t).addClass("active");
        $.trim(n.val()) == n.attr("placeholder") && n.val("");
        $(document).bind("keyup", this.commentKeyUpMethod);
    };

    this.onBlurCommentField = function (e) {
        var t = $("#commentForm", this.canvas),
            n = $("textarea", t);
        if (n.val() == "") {
            n.val(n.attr("placeholder"));
            t.removeClass("active")
        }
        $(document).unbind("keyup", this.commentKeyUpMethod)
    };

    this.onCommentKeyUp = function (e) {
        var t = $("#commentForm", this.canvas),
            n = $("textarea", t),
            r = n.val();
        if (e.which == 13 && r != n.attr("placeholder")) {
            e.stopPropagation();
            e.preventDefault();
            this.saveComment(r);
        }
    };

    //发送评论请求
    this.saveComment = function (e) {
        $(document).unbind("keyup", this.commentKeyUpMethod);
        var t = $("#commentForm", this.canvas),
            n = $("textarea", t),
            r = e.toLowerCase(),
            i = !0;

        e == n.attr("placeholder") && (i = !1);
        r.length < 3 && (i = !1);

        if (i) {
            n.attr("disabled", "disabled");
            $.ajax({
                url: CANON_ABSPATH + "/functions/add_comment.php",
                data: {
                    imageId: this.imageId,
                    comment: e,
                    nonce: nonce,
                    userId: pageConfig.userId,
                    format: "big"
                },
                type: "POST",
                success: $.proxy(this.onSaveComment, this)
            });
        } else {
            this.canvas.addClass("error");
            alert("评论内容太短了，多说点儿什么吧！");
        }
    };

    this.onSaveComment = function (e, t, n) {
        if (e.error) {
            alert(e.message);
        }
        else{
            var r = $("#commentForm", this.canvas);
            $(e.html).insertBefore(r).hide().fadeIn();
            r.remove();
            this.updateActivityScrolling();
        }
    }
};


//保存弹窗
var gbks = gbks || {};
gbks.common = gbks.common || {};
gbks.common.savePopupInstance = null;
gbks.common.SavePopup = function () {

    if (gbks.common.savePopupInstance) {
        gbks.common.savePopupInstance.hide();
        gbks.common.savePopupInstance = null
    }

    gbks.common.savePopupInstance = this;

    this.init = function (e, t, n, r) {
        this.button = e;
        this.data = t;
        this.unsaveCallback = n;
        this.closeCallback = r;
        this.createCanvas();
        this.resizeMethod = $.proxy(this.onResize, this);
        this.clickDocumentMethod = $.proxy(this.onClickDocument, this);
    };

    this.createCanvas = function () {
        var e = this.data.groups;
        //if (!e) return;
        var t = this.data.html;
        this.canvas && $("#groupsOverlay").remove();
        this.canvas = $(t);

        $("body").append(this.canvas);
        this.updatePosition();

        // $("input[type=checkbox]", this.canvas).bind("change", $.proxy(this.onClickGroupOverlayItem, this));
        $(".closeButton", this.canvas).click($.proxy(this.hide, this));
        // $(".addForm input[type=text]", this.canvas).bind("focus", $.proxy(this.onFocusCreateGroupInput, this));
        // $(".addForm input[type=text]", this.canvas).bind("blur", $.proxy(this.onBlurInput, this));
        // $("#formCreateGroup", this.canvas).submit($.proxy(this.onClickCreateGroup, this));
        $(".unsaveButton", this.canvas).click($.proxy(this.onClickGroupOverlayUnsave, this));
        // $("#dropboxButton", this.canvas).click($.proxy(this.onClickDropbox, this));
        // $("input[type=radio]", this.canvas).bind("change", $.proxy(this.onClickPrivacyOption, this));
        $(document).mousedown(this.clickDocumentMethod);
        $(window).resize(this.resizeMethod)
    };

    this.updatePosition = function () {
        var e = $(window).width(),
            t = this.button.offset(),
            n = this.canvas.width(),
            r = Math.round(t.left + this.button.width() / 2 - n / 2) - 2,
            i = r + n,
            s = e - 10 - i,
            o = n / 2;
        if (s < 0) {
            r += s;
            o -= s
        }
        if (r < 0) {
            r -= r - 10;
            o += r - 10
        }
        var u = $(window).height(),
            a = u / 2,
            f = this.button.offset().top - window.pageYOffset + this.button.height() / 2,
            l = f,
            c = u - l,
            h = l > c;
        h ? this.canvas.addClass("topper") : this.canvas.removeClass("topper");
        var p = t.top + this.button.height() + 17;
        h && (p = t.top - 17 - this.canvas.height());
        this.canvas.css({
            left: r + "px",
            top: p + "px"
        });
        $(".arrow", this.canvas).css("left", o + "px")
    };

    //取消保存图片
    this.unsaveImage = function () {

        $.ajax({
            url: CANON_ABSPATH + "/functions/save_pic.php",
            type: "POST",
            data: {
                userId: pageConfig.userId,
                imageId: this.data.imageId,
                nonce: nonce,
                action: "unsave"
            },
            dataType: "json",
            success: $.proxy(this.onUnsaveImage, this)
        });

        this.hide();
    };

    this.onUnsaveImage = function (e) {};

    this.onClickGroupOverlayUnsave = function (e) {
        this.unsaveImage(this.data.imageId);
        this.unsaveCallback && this.unsaveCallback(this.data.imageId)
    };
    this.onClickDocument = function (e) {
        var t = $(e.target),
            n = t.parents("#groupsOverlay");
        if (n.length == 0) {
            this.hide(null);
            e.stopPropagation();
            e.preventDefault()
        }
    };
    this.hide = function (e) {
        $(document).unbind("mousedown", this.clickDocumentMethod);
        $(window).unbind("resize", this.resizeMethod);
        if (this.canvas) {
            this.canvas.remove();
            this.canvas = null
        }
    };
    this.showLoader = function (e) {
        gbks.common.Loader.show(e)
    };
    this.hideLoader = function () {
        gbks.common.Loader.hide()
    };
    this.onResize = function (e) {
        this.updatePosition()
    }
};


//分享弹窗
var gbks = gbks || {};
gbks.common = gbks.common || {};
gbks.common.sharePopupInstance = null;
gbks.common.SharePopup = function () {
    if (gbks.common.sharePopupInstance) {
        gbks.common.sharePopupInstance.hide();
        gbks.common.sharePopupInstance = null
    }
    gbks.common.sharePopupInstance = this;
    this.canvas = null;
    this.element = null;
    this.target = null;
    this.hideCallback = !1;
    this.resizeMethod = $.proxy(this.updateLayout, this);

    this.display = function (e, t, n) {
        this.imageId = e;
        this.element = t;
        this.hideCallback = n;
        this.clickDocumentMethod = $.proxy(this.onClickDocument, this);
        $(window).resize(this.resizeMethod);
        this.createCanvas();
        this.updatePosition();
        this.canvas.show();
        $(document).click(this.clickDocumentMethod)
    };
    this.hide = function () {
        $(document).unbind("click", this.clickDocumentMethod);
        $(window).unbind("resize", this.resizeMethod);
        if (this.canvas) {
            this.canvas.hide();
            this.canvas.remove();
            this.canvas = null
        }
    };
    this.onClickDocument = function (e) {
        var t = $(e.target),
            n = t.parents("#sharePopup");
        if (n.length == 0) {
            this.hide();
            if (this.hideCallback) {
                this.hideCallback(e);
                this.hideCallback = null
            }
            e.stopPropagation();
            e.preventDefault()
        }
    };
    this.createCanvas = function () {
        if (this.canvas) {
            this.canvas.remove();
            this.canvas = null
        }

        var e= ''+
'<div class="bdsharebuttonbox" data-tag="share_1">'+
    '<a class="bds_mshare" data-cmd="mshare"></a>' +
    '<a class="bds_qzone" data-cmd="qzone" href="#"></a>'+
    '<a class="bds_tsina" data-cmd="tsina"></a>'+
    '<a class="bds_baidu" data-cmd="baidu"></a>'+
    '<a class="bds_renren" data-cmd="renren"></a>'+
    '<a class="bds_tqq" data-cmd="tqq"></a>'+
'</div>';

        e = gbks.common.wrapPopupContent("sharePopup", e, !1);
        this.canvas = $(e);
        $("body").append(this.canvas);
        $("li", this.canvas).click($.proxy(this.onClickItem, this))
    };
    this.onClickItem = function (e) {};
    this.updatePosition = function () {
        gbks.common.positionPopup(this.canvas, this.element)
    }
};



//侧边栏相关
var gbks = gbks || {};
gbks.common = gbks.common || {};
gbks.common.Kaori = function () {
    this.init = function () {
        this.canvas = $("#kaori");
        this.nav = $("#luka");
        this.expandClass = "hidenav";
        this.expandTimer = null;

        this.hamburgerTime = null;
        $(".hamburger", this.nav).click($.proxy(this.onClickHamburger, this));
        $("#images").length > 0 && $("li", this.canvas).click($.proxy(this.clickNavItem, this));

        this.resizeTimer = null;
        $(window).resize($.proxy(this.onWindowResize, this));
        this.resize();

        this.initSearch();

        $(".profileImage img").hover($.proxy(this.onHoverAvatar, this));
        $(".profileImage").bind("mouseleave", $.proxy(this.onLeaveAvatar, this));

    };
    this.onWindowResize = function (e) {
        clearTimeout(this.resizeTimer);
        this.resizeTimer = setTimeout($.proxy(this.resize, this))
    };
    this.resize = function (e) {
        this.canvas.css("height", $(window).height() + "px")
    };
    this.onClickHamburger = function (e) {
        e.preventDefault();
        e.stopPropagation();
        var t = (new Date).getTime();
        (this.hamburgerTime == null || t - this.hamburgerTime > 250) && this.toggle();
        this.hamburgerTime = t
    };
    this.toggle = function () {
        var e = $("body").hasClass(this.expandClass);
        $("body").toggleClass(this.expandClass);
        gbks.imageInstance && gbks.imageInstance.updateImageSize();
        gbks.tvInstance && gbks.tvInstance.resize();
        gbks.tilesInstance && gbks.tilesInstance.layout();
        gbks.common.Cookie("hidenav", e === !0 ? "false" : "true", {
            path: "/"
        });
        return !e
    };
    this.updateTileLayout = function () {
        gbks.tilesInstance && gbks.tilesInstance.layout();
    };
    this.expand = function (e) {
        clearTimeout(this.expandTimer);
        $("#lukas").addClass(this.expandClass);
        $(".cover", this.canvas).animate({
            right: -180
        }, 350);
        this.canvas.animate({
            width: 180
        }, 350)
    };
    this.collapse = function (e) {
        clearTimeout(this.expandTimer);
        $("#lukas").removeClass(this.expandClass);
        $(".cover", this.canvas).animate({
            right: -50
        }, 350);
        this.canvas.animate({
            width: 60
        }, 350);
    };
    this.isExpanded = function () {
        return this.lukas.hasClass(this.expandClass)
    };
    this.initSearch = function(){
        $(".search input").bind("keyup", function(e){
            if (e.keyCode === 13) {
                var $input = $(this);
                var term = $.trim($input.val());
                if (!term) {
                    alert("请输入要搜索的关键词！");
                    $input.focus();
                    return false;
                }
                else{
                    window.location = "/?s="+encodeURIComponent(term);
                }
            }
        });
    };
    this.onHoverAvatar = function(){
        if ($(".profileImage .tip").length == 0) {
            $("<a href='/settings' class='tip'>更改头像</a>").appendTo(".profileImage");
        }
    };
    this.onLeaveAvatar = function(){
        $(".profileImage .tip").remove();
    };
};



gbks.common.kaoriInstance = new gbks.common.Kaori;
gbks.common.kaoriInstance.init();