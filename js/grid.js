window.console || (console = {
    log: function () {}
});


var gbks = gbks || {};
gbks.Grid = function () {

    this.init = function (e) {
        this.config = e;
        this.layout = new gbks.Tiles;
        this.layout.init(e);
        this.polaroid = new gbks.Polaroid;
        this.polaroid.init();
    };

    this.setHistory = function (e, t) {
        var n = typeof history.pushState != "undefined";
        n && history.pushState({
            url: e,
            title: t
        }, t, e);
    };

    this.onHistoryChange = function (e) {
        var t = e.state;
    };
};

//程序入口
var grid, pageConfig = pageConfig || {};
$(document).ready(function () {
    if (pageConfig) {
        grid = new gbks.Grid;
        grid.init(pageConfig);
    }
});

/*
 *  Polaroid 即单个的图片方格，显示图片缩略图、喜欢/保存按钮及喜欢/保存数
 *  同时加上 Tile 类，表示瀑布流中的一个方格
 *
 */
var gbks = gbks || {};
gbks.Polaroid = function () {
    this.init = function () {
        this.polaroids = $(".polaroid");
        var e = $("html");

        this.isSlow = e.hasClass("ie6") || e.hasClass("ie7") || e.hasClass("ie8") || e.hasClass("ie9");
        //若已登录，则在body上添加class "auth"
        this.auth = $("body").hasClass("auth");
        this.loader = $("#loader");
        this.lightbox = null;
        this.groupsOverlay = null;
        this.savePopup = null;
        this.htmlDecoder = null;
        this.autoComplete = null;
        this.focusedField = null;
        this.lastFocusedField = null;
        this.resizeMethod = $.proxy(this.onResize, this);
        this.showGroupInfoTimer = null;
        this.hideGroupInfoTimer = null;
        this.shiftDown = !1;

        $(window).bind("keydown", $.proxy(this.keyDown, this));
        $(window).bind("keyup", $.proxy(this.keyUp, this));
        var t = $(".polaroid .options");

        if (this.auth) {
            $(".like", t).live("click", $.proxy(this.onClickLikeImage, this));
            $(".save", t).live("click", $.proxy(this.onClickSaveIcon, this));
        }
        else{
            //未登录情况下保存和喜欢按钮跳转逻辑
            $(".like, .save").live("click", function(e){
                e.stopPropagation();
                e.preventDefault();

                var $this = $(this);
                var imageId = $this.attr("data-id");

                location.href = "/signup?next=" + encodeURIComponent("/?p="+imageId);
            });

            //未登录下点击关注按钮
            $(".follow").live("click", function(e){
                e.stopPropagation();
                e.preventDefault();

                var userId = $(this).attr("data-id");
                location.href = "/signup?next=" + encodeURIComponent("/profile/"+userId);
            });
        }
        $(".imageLink", this.polaroids).live("click", $.proxy(this.onClickMagnify, this));
        $(".nsfwCover", this.polaroids).live("click", $.proxy(this.onClickNSFWCover, this));
        $(".expander", this.polaroids).live("click", $.proxy(this.onClickExpand, this));
        var n = $(".groups a", this.polaroids);
        // n.live("mouseenter", $.proxy(this.onGroupOver, this));
        // n.live("mouseleave", $.proxy(this.onGroupOut, this));
        this.clickDocumentMethod = $.proxy(this.onClickDocument, this);
    };
    // this.onGroupOver = function (e) {
    //     var t = $(e.currentTarget);
    //     this.groupInfoLink = t;
    //     var n = t.attr("href"),
    //         r = n.split("/"),
    //         i = 0,
    //         s = r.length,
    //         o, u;
    //     for (; i < s; i++){
    //         if (r[i] == "group" && s > i) {
    //             u = r[i + 1];
    //             break;
    //         }
    //     }
    //     if (u) {
    //         clearTimeout(this.hideGroupInfoTimer);
    //         this.groupInfoId = u;
    //         clearTimeout(this.showGroupInfoTimer);
    //         this.showGroupInfoTimer = setTimeout($.proxy(this.loadGroupInfoPopup, this), 1e3)
    //     }
    // };
    // this.loadGroupInfoPopup = function (e) {
    //     $.ajax({
    //         url: "/groups/popup",
    //         data: {
    //             groupId: this.groupInfoId
    //         },
    //         type: "POST",
    //         success: $.proxy(this.onGetGroupPopup, this)
    //     });
    // };
    // this.onGetGroupPopup = function (e) {
    //     if (!e || !this.groupInfoId) return;
    //     if (this.groupInfoPopup) {
    //         this.groupInfoPopup.remove();
    //         this.groupInfoPopup = null;
    //         this.groupInfoId = null
    //     }
    //     var e = gbks.common.wrapPopupContent("groupInfoPopup", e);
    //     this.groupInfoPopup = $(e);
    //     $("body").append(this.groupInfoPopup);
    //     gbks.common.positionPopup(this.groupInfoPopup, this.groupInfoLink)
    // };
    // this.onGroupOut = function (e) {
    //     clearTimeout(this.showGroupInfoTimer);
    //     if (this.groupInfoPopup) {
    //         this.groupInfoPopup.remove();
    //         this.groupInfoPopup = null;
    //         this.groupInfoId = null
    //     }
    //     clearTimeout(this.hideGroupInfoTimer);
    //     this.hideGroupInfoTimer = setTimeout($.proxy(this.hideGroupPopup, this), 500)
    // };
    // this.hideGroupPopup = function (e) {
    //     if (this.groupInfoPopup) {
    //         clearTimeout(this.showGroupInfoTimer);
    //         this.groupInfoPopup.remove();
    //         this.groupInfoPopup = null;
    //         this.groupInfoId = null
    //     }
    // };
    this.onClickLikeImage = function (e) {
        e.stopPropagation();
        e.preventDefault();

        var t = $(e.currentTarget),
            n = $(t.parents(".polaroid")[0]),
            r = $(".likes", n),
            i = $(".likes", n),
            s = !t.hasClass("active"),
            o = t.attr("data-id"),
            u = parseInt(n.attr("data-likes"));

        s ? u++ : u--;
        n.attr("data-likes", u);
        this.updateStats(n, !1, !0);

        if (s) {
            t.addClass("active");
        } else {
            t.removeClass("active");
        }

        $.ajax({
            url: CANON_ABSPATH + "/functions/like_pic.php",
            data: {
                imageId: o,
                userId: pageConfig.userId || USER_ID,
                nonce: nonce
            },
            type: "POST",
            success: $.proxy(this.onLikeImage, this)
        });

        this.updateLayout();
    };

    this.onLikeImage = function (e) {};

    this.keyDown = function (e) {
        e.which == 16 && (this.shiftDown = !0);
    };
    this.keyUp = function (e) {
        e.which == 16 && (this.shiftDown = !1);
    };
    this.setCursorPosition = function (e, t) {
        var n = e.get(0);
        if (n.setSelectionRange) n.setSelectionRange(t, t);
        else if (n.createTextRange) {
            var r = n.createTextRange();
            r.collapse(!0);
            r.moveEnd("character", t);
            r.moveStart("character", t);
            r.select()
        }
    };

    this.onClickSaveIcon = function (e) {
        e.stopPropagation();
        e.preventDefault();
        var t = $(e.currentTarget),
            n = $(t.parents(".polaroid")[0]),
            r = $(".save", n).attr("data-id"),
            i = !1;
        $(".save span", n).html("编辑");
        n.addClass("saved");
        if (this.groupsOverlay) {
            i = this.groupsOverlay.attr("data-imageId") == r;
            this.groupsOverlay.remove();
            this.groupsOverlay = null;
        }
        i || this.saveImage(n);
    };

    this.saveImage = function (e) {
        var t = $(".options .save", e),
            n = $(".saves", e),
            r = $(".saves", e),
            i = t.hasClass("active");

        t.addClass("active");

        var s = e.attr("id"),
            o = s.split("_"),
            u = o[1];

        if (!i) {
            t.addClass("active");
            var a = parseInt(e.attr("data-saves"));
            e.attr("data-saves", a + 1);
            this.updateStats(e, !0, !1);
        }

        this.hideGroupsOverlay();
        var f = $.proxy(this.onSaveImage, this);
        this.shiftDown ? f = null : t.addClass("loading");

        $.ajax({
            url: CANON_ABSPATH + "/functions/save_pic.php",
            type: "POST",
            data: {userId: pageConfig.userId || USER_ID, imageId: u, nonce: nonce},
            dataType: "json",
            success: f
        });

        this.updateLayout();
    };
    this.onCloseSavePopup = function (e) {};
    this.onClickRemoveImage = function () {
        var e = $("#image_" + this.savePopup.data.imageId),
            t = $(".save", e);
        t.removeClass("active");
        $("span", t).html("保存");
        var n = parseInt(e.attr("data-saves"));
        e.attr("data-saves", n - 1);
        this.updateStats(e, !0, !1);
    };
    this.onSaveImage = function (e) {
        var t = $("#image_" + e.imageId),
            n = $(".save", t);
        n.removeClass("loading");
        this.savePopup = new gbks.common.SavePopup;
        this.savePopup.init(n,
                            e,
                            $.proxy(this.onClickRemoveImage, this),
                            $.proxy(this.onCloseSavePopup, this));
    };
    this.unsaveImage = function (e) {
        var t = e.attr("id"),
            n = t.split("_"),
            r = n[1];

        var i = parseInt(e.attr("data-saves"));
        e.attr("data-saves", i - 1);

        this.updateStats(e, !0, !1);

        var s = $(".save", e);
        s.addClass("active");

        $("span", s).html("保存");
        e.removeClass("saved");

        this.hideGroupsOverlay();
        $.ajax({
            url: "/bookmark/removefromuser?imageId=" + r,
            type: "POST",
            dataType: "jsonp",
            success: $.proxy(this.onUnsaveImage, this)
        });
        this.updateLayout();
    };
    this.onUnsaveImage = function (e) {
        console.log("onUnsaveImage", e)
    };

    // this.onFocusCreateGroupInput = function (e) {
    //     $(".addForm", this.groupsOverlay).addClass("active");
    //     this.onFocusInput(e)
    // };
    // this.onClickGroupOverlayUnsave = function (e) {
    //     var t = $(e.currentTarget),
    //         n = this.groupsOverlay.attr("data-imageId"),
    //         r = $("#image_" + n);
    //     this.unsaveImage(r)
    // };
    // this.onClickCreateGroup = function (e) {
    //     e.preventDefault();
    //     var t = $("#formCreateGroup", this.groupsOverlay),
    //         n = $("input[type=text]", t),
    //         r = $('input[name="imageId"]', t).val(),
    //         i = n.val(),
    //         s = n.attr("data-default"),
    //         o = "/groups/create";
    //     if (i.length > 0 && i != s) {
    //         console.log("onClickCreateGroup", t, r, i, o);
    //         gbks.common.track("Polaroid", "CreateGroup", i);
    //         t.removeClass("active");
    //         n.val("");
    //         $("input", t).attr("disabled", !0);
    //         $("input[type=submit]", this.groupsOverlay).addClass("loading");
    //         var u = {
    //             imageId: r,
    //             groupName: i
    //         };
    //         $.ajax({
    //             url: o,
    //             data: u,
    //             type: "POST",
    //             success: $.proxy(this.onCreateGroup, this)
    //         })
    //     }
    // };
    // this.onCreateGroup = function (e) {
    //     $("#formCreateGroup input[type=submit]", this.groupsOverlay).removeClass("loading");
    //     $("#formCreateGroup input", this.groupsOverlay).removeAttr("disabled");
    //     var t = $.parseJSON(e);
    //     t = e;
    //     console.log("onCreateGroup", e, t);
    //     var n = '<li><input type="checkbox" name="groupId" value="' + t.id + '" checked="true" />' + t.name + "</li>",
    //         r = $("ul", this.groupsOverlay),
    //         i = $(r[r.length - 1]);
    //     i.append(n);
    //     r.removeClass("empty");
    //     this.hideLoader()
    // };
    this.updateStats = function (e, t, n) {
        var r = e.attr("data-likes"),
            i = e.attr("data-saves"),
            s = [],
            o;
        /*(i > 0 || t) &&*/ s.push('<em class="s"></em><span class="saves">' + i + "</span>");
        /*(r > 0 || n) &&*/ s.push('<em class="l"></em><span class="likes">' + r + "</span>");
        var u = s.join(""),
            a = $(".stats", e);
        a.html(u);
        u.length > 0 ? a.removeClass("empty") : a.addClass("empty");
    };
    this.onFocusInput = function (e) {
        var t = $(e.currentTarget);
        t.addClass("active");
        t.val() == t.attr("data-default") && t.val("")
    };
    this.onBlurInput = function (e) {
        var t = $(e.currentTarget);
        t.addClass("active");
        t.val() == "" && t.val(t.attr("data-default"))
    };
    this.onClickGroupOverlayItem = function (e) {
        console.log("onClickGroupOverlayItem");
        e.preventDefault();
        e.stopPropagation();
        var t = this.groupsOverlay.attr("data-imageId"),
            n = $(e.currentTarget),
            r = $(n.parents("li")[0]),
            i = r.attr("data-checked");
        console.log("type", i, typeof i);
        i === undefined ? console.log("y") : console.log("n");
        var s = n.val(),
            o = i == "true",
            u = "/groups/removeImageFromGroup",
            a = "Removing from group";
        if (o) {
            n.removeAttr("checked");
            r.attr("data-checked", !1);
            gbks.common.track("Polaroid", "RemoveFromGroup", t + "-" + s)
        } else {
            u = "/groups/addImageToGroup";
            a = "Adding to group";
            n.attr("checked", "checked");
            r.attr("data-checked", !0);
            gbks.common.track("Polaroid", "AddToGroup", t + "-" + s)
        }
        r.addClass("loading");
        $.ajax({
            url: u,
            data: {
                imageId: t,
                groupId: s
            },
            type: "POST",
            success: $.proxy(this.onGroupSaved, this)
        })
    };
    this.onGroupSaved = function (e) {
        this.groupsOverlay && $("li", this.groupsOverlay).removeClass("loading")
    };
    this.onClickDocument = function (e) {
        var t = $(e.target),
            n = t.parents("#groupsOverlay");
        if (n.length == 0) {
            this.hideGroupsOverlay(null);
            e.stopPropagation();
            e.preventDefault()
        }
        console.log("onClickDocument", t, n, e.target)
    };
    this.hideGroupsOverlay = function (e) {
        $(document).unbind("mousedown", this.clickDocumentMethod);
        if (this.groupsOverlay) {
            var t = this.groupsOverlay.attr("data-imageId");
            this.groupsOverlay.remove();
            this.groupsOverlay = null;
            var n = $("#image_" + t);
            n.removeClass("optionsActive");
            console.log("hideGroupsOverlay", t, n)
        }
    };
    this.updateLayout = function () {
        typeof tiles != "undefined" && tiles.layout();
    };
    this.onClickMagnify = function (e) {
        e.preventDefault();
        e.stopPropagation();
        this.hideLightbox();
        var t = $(e.currentTarget),
            n = $(t.parents(".tile")[0]);
        this.createLightbox();
        this.lightbox.updateHistory();
        this.lightbox.update(n)
    };
    this.hideLightbox = function () {
        this.lightbox && this.lightbox.hide()
    };
    this.createLightbox = function () {
        if (!this.lightbox) {
            this.lightbox = new gbks.common.Lightbox;
            this.lightbox.init()
        }
    };
    this.onClickExpand = function (e) {
        e.preventDefault();
        e.stopPropagation();
        var t = $(e.currentTarget),
            n = $(t.parents(".expand")[0]),
            r = n.parent(),
            i = $(".hidden", r);
        n.hide();
        i.show();
        this.updateLayout();
    };
    this.showLoader = function (e) {
        if (!this.loader || this.loader.length == 0) this.loader = $("#loader");
        this.loader.stop();
        e && e.length > 0 ? this.loader.html(e) : this.loader.html("");
        this.loader.show();
        this.loader.animate({
            opacity: 1
        }, 50)
    };
    this.hideLoader = function () {
        this.loader.stop();
        var e = null;
        this.onHideLoader && (e = $.proxy(this.onHideLoader, this));
        this.loader.animate({
            opacity: 0
        }, 250, e)
    };
    this.onHideLoader = function (e) {
        this.loader.hide()
    };
    this.onClickNSFWCover = function (e) {
        var t = $(e.currentTarget),
            n = t.parents(".nsfw");
        console.log("onClickNSFWCover", e, n);
        if (n.length > 0) {
            var r = $(n[0]);
            r.removeClass("nsfw");
            e.preventDefault();
            e.stopPropagation();
        }
    }
};


var gbks = gbks || {};
gbks.tilesInstance = null;
gbks.Tiles = function () {
    this.init = function (e) {
        this.config = e;
        gbks.tilesInstance = this;
        this.columns = [];
        this.columnCount = null;
        this.baseOffset = null;
        this.itemCount = null;
        this.fadeIndex = 0;
        this.superAds = $("#images .tile.superad");
        this.columnWidth = e.columnWidth || 240;
        this.itemOffset = e.itemOffset || 2;
        this.itemWidth = e.itemWidth || 238;
        this.itemMargin = e.itemMargin || 5;
        this.maxWidth = e.maxWidth || null;
        tiles = this;
        this.loader = $("#loader");
        this.currentPage = 1;
        this.tiles = $(".tile");

        console.log("config", this.config);

        if (this.tiles.length > 0) {
            this.layout();
            if (this.tiles.length > 2) {
                this.initAutoLayout();
                this.config.type && this.startEndlessScroll();
            }
        }
        setTimeout($.proxy(this.tiles.show, this.tiles), 25);
        this.fadeImages();
    };
    this.fadeImages = function () {
        if (Modernizr.opacity && Modernizr.cssanimations) {
            var e = $(".polaroid img");
            e.each(function (e) {
                if (!this.complete) {
                    var t = $(this);
                    t.addClass("imageLoading");
                    t.one("load", function (e) {
                        $(this).addClass("imageLoaded")
                    })
                }
            })
        }
    };
    this.initAutoLayout = function () {
        $(window).resize($.proxy(this.resize, this));
    };
    this.resize = function () {
        clearTimeout(this.layoutTimer);
        this.layoutTimer = setTimeout($.proxy(this.layout, this), 500)
    };
    this.onAfterUpdateElement = function (e) {};
    this.columnLayout = function () {
        var e = [],
            t = 0;
        this.config.verticalOffset && (t = this.config.verticalOffset);
        while (e.length < this.columns.length) e.length == 0 ? e.push(0) : e.push(t);
        var n = 0,
            r = this.columns.length,
            i, s = 0,
            o, u;
        for (; n < r; n++) {
            i = this.columns[n];
            o = i.length;
            for (s = 0; s < o; s++) {
                u = i[s];
                u.css({
                    top: e[n] + "px"
                });
                e[n] += u.outerHeight() + this.itemOffset
            }
        }
        var a = 0;
        for (n = 0; n < r; n++) a = Math.max(a, e[n]);
        u.parent().css({
            height: a + "px"
        });
    };
    this.layout = function () {
        var e = $(".tile"),
            t = this.itemMargin,
            n = $(window).width() - t * 2 - 40;
        $("body").hasClass("hidenav") !== !0 && (n -= $("#kaori").width());
        console.log("maxWidth", n);
        $("body").hasClass("isAdmin") && (n -= 60);
        this.maxWidth && (n = this.maxWidth);
        var r = this.columnWidth,
            i = this.itemOffset,
            s = Math.floor(n / r),
            o = Math.round((n - (s * r - i)) / 2) + t;
        o = 0;
        var u = this.itemWidth;
        //dirty hack
        //在色彩页面不应用该逻辑
        if (s == this.columnCount && this.baseOffset == o && this.itemCount == e.length) {
            this.columnLayout();
            if ($("#colorTile").length !== 0) {
                return true;
            }
        }
        this.itemCount = e.length;
        this.columnCount = s;
        this.baseOffset = o;
        var a = 0;
        this.config.verticalOffset && (a = this.config.verticalOffset);
        var f = [];
        while (f.length < s) f.length == 0 ? f.push(0) : f.push(a);
        this.columns = [];
        while (this.columns.length < s) this.columns.push([]);
        var l, c, h, p = 0,
            d = e.length,
            v = 0,
            m = null,
            g = 0;
        for (; p < d; p++) {
            l = $(e[p]);
            m = null;
            g = 0;
            for (v = 0; v < s; v++)
                if (m == null || f[v] < m) {
                    m = f[v];
                    g = v
                }
            c = m;
            h = g * r + o;
            l.css({
                position: "absolute",
                top: c + "px",
                left: h + "px"
            });
            f[g] = m + l.outerHeight() + i;
            this.columns[g].push(l)
        }
        var y = 0;
        for (p = 0; p < this.columnCount; p++) y = Math.max(y, f[p]);
        if (l) {
            var b = $(l.parent()),
                w = Math.round(s * r - i);
            b.css({
                width: w + "px",
                height: y + "px",
                "margin-left": "auto",
                "margin-right": "auto"
            })
        }(!this.superAds || e.length != this.superAds.length) && e.show()
    };
    this.startEndlessScroll = function () {
        console.log("startEndlessScroll");
        clearInterval(this.interval);
        this.interval = setInterval($.proxy(this.evaluateScrollPosition, this), 100)
    };
    this.stopEndlessScroll = function () {
        clearInterval(this.interval)
    };
    this.evaluateScrollPosition = function () {
        var e = 750,
            t = this.getPageHeight() - this.getScrollHeight();
        t < e && this.loadMore();
    };
    this.getScrollHeight = function () {
        var e;
        self.pageYOffset ? e = self.pageYOffset : document.documentElement && document.documentElement.scrollTop ? e = document.documentElement.scrollTop : document.body && (e = document.body.scrollTop);
        return parseInt(e) + this.getWindowHeight()
    };
    this.getWindowHeight = function () {
        var e, t;
        if (self.innerWidth) {
            e = self.innerWidth;
            t = self.innerHeight
        } else if (document.documentElement && document.documentElement.clientWidth) {
            e = document.documentElement.clientWidth;
            t = document.documentElement.clientHeight
        } else if (document.body) {
            e = document.body.clientWidth;
            t = document.body.clientHeight
        }
        return parseInt(t)
    };
    this.getPageHeight = function () {
        return $(document).height()
    };
    this.clear = function () {
        var e = $(".tile"),
            t = 0,
            n = e.length,
            r;
        for (; t < n; t++) {
            r = $(e[t]);
            r.hasClass("superad") ? r.hide() : r.remove()
        }
        $(".tileTrash").remove()
    };

    //加载更多
    this.loadMore = function () {
        this.currentPage++;
        var e = this;
        this.stopEndlessScroll();
        this.showLoader();
        this.config.page = this.currentPage;
        var t = this.loadMoreUrl;
        $.ajax({
            url: CANON_ABSPATH + "/functions/loadmore.php",
            data: this.config,
            success: $.proxy(this.onLoadMore, this),
            error: $.proxy(this.onLoadMoreError, this)
        });
    };
    this.track = function (e, t, n) {
        typeof _gaq != "undefined" && _gaq.push(["_trackEvent", e, t, n])
    };
    this.insertElementsAroundAds = function (e) {
        var t = !1,
            n = $(e),
            r = n.length,
            i = this.superAds.length,
            s = i + 1,
            o = Math.ceil(r / s),
            u = 0,
            a, f;
        r > 1 && (t = !0);
        while (u < s) {
            a = $(this.superAds[u]);
            f = n.slice(u * o, (u + 1) * o);
            a && a.length > 0 ? f.insertBefore(a) : $("#images").append(f);
            u++
        }
        return t;
    };
    this.onLoadMore = function (e) {
        //若是广告
        if (this.currentPage == 0 && this.superAds.length > 0) {
            var t = this.insertElementsAroundAds(e);
            t && this.superAds.show();
        } else {
            $("#images").append(e);
            pageConfig.page++;
        }
        this.fadeImages();
        $("#noMoreImages").length == 0 && this.startEndlessScroll();

        this.hideLoader();
        this.layout()
    };
    this.onLoadMoreError = function (e) {
        this.hideLoader();
    };
    this.showLoader = function () {
        this.loader.stop();
        this.loader.show();
        this.loader.animate({
            opacity: 1
        }, 50)
    };
    this.hideLoader = function () {
        this.loader.stop();
        var e = null;
        this.onHideLoader && (e = $.proxy(this.onHideLoader, this));
        this.loader.animate({
            opacity: 0
        }, 250, e)
    };
    this.onHideLoader = function (e) {
        this.loader.hide()
    }
};
