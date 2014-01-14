var wookmark = wookmark || {};
wookmark.Bookmarklet = function () {
    this.init = function () {

        this.isie = 0 <= navigator.appName.indexOf("Internet Explorer");
        this.isff = 0 <= navigator.userAgent.indexOf("Firefox");
        this.iswk = 0 <= navigator.userAgent.indexOf("Safari");

        this.frames = document.getElementsByTagName("frame");
        this.doc = this.getDocument();
        this.head = this.doc.getElementsByTagName("head")[0];

        this.data = {};
        this.overlays = [];
        this.buttons = {};

        this.scrollPosition = {
            x: document.all ? document.scrollLeft : window.pageXOffset,
            y: document.all ? document.scrollTop : window.pageYOffset
        };

        this.selectedText = this.getSelectedText();
        this.selectedImage = null;
        this.images = this.doc.getElementsByTagName("img");
        this.imageCount = this.images.length;
        this.checkImageIndex = 0;
        this.imagesFound = 0;

        this.createContainer();
        this.checkNextImage();
        window.scrollTo(0, 0);
        this.toggleHideElements(!1);
    };
    this.toggleHideElements = function (e) {
        if (this.elementsToHide) {
            var t = 0,
                n = this.elementsToHide.length,
                r;
            for (; t < n; t++) {
                r = this.elementsToHide[t];
                r.style.visibility = e ? "visible" : "hidden";
            }
        }
    };
    this.createContainer = function () {
        var e = this.doc.createElement("div"),
            t = this.getDocHeight(),
            n = "height: " + t + "px; position: absolute; top: 0px; left: 0px; right: 0px; bottom: 0px; z-index:4294967295;";
        n += "background: rgba(0,0,0,.8) url('http://xiaoshelang.undefinedblog.com/wp-content/themes/canon/img/bg-texture-80.png');";
        this.setStyles(e, n);
        this.container = e;
        this.createHeader();
        var r = this.getColumnCount(),
            i = Math.round((this.getDocWidth() - r * 230 + 30) / 2) - 22,
            s = this.doc.createElement("div");
        n = "margin: 0 0 25px " + i + "px; clear:both;";
        this.setStyles(s, n);
        e.appendChild(s);
        this.doc.body.appendChild(e);
        this.imageHolder = s;
    };
    this.setStyles = function (e, t) {
        e.setAttribute("style", t);
        e.style.cssText = t
    };
    this.getColumnCount = function () {
        var e = this.getDocWidth(),
            t = Math.floor(e / 230);
        return t;
    };
    this.checkNextImage = function () {
        if (this.checkImageIndex < this.imageCount) {
            var e = this.checkImage(this.images[this.checkImageIndex]);
            e && this.imagesFound++;
            this.checkImageIndex++;
            setTimeout(this.delegate(this.checkNextImage, this), 10);
        } else if (this.imagesFound === 0) {
            window.alert("在当前页面无法找到任何图片！");
            this.cancel();
        }
    };
    this.checkImage = function (e) {
        var t = !1;
        if (e.width < 150 && e.height < 150 || e.width < 80 || e.height < 80) return t;
        if (e.src.match(/\.(tif|tiff)$/i)) return t;
        t = !0;
        this.createGridItem(e);
        return t;
    };
    this.createGridItem = function (e) {
        var t = this.doc.createElement("div"),
            n = "float: left; width: 200px; height: 200px; margin: 15px; position: relative; overflow: hidden;";
        t.className = "wkGridImage";
        var r = "wkGridImage_" + this.overlays.length;
        t.id = r;
        this.setStyles(t, n);
        var i = e.width,
            s = e.height,
            o = Math.min(i, 200),
            u = Math.min(s, 200),
            a = o / i,
            f = u / s;
        a < f ? u = s * a : o = i * f;
        var l = Math.round((200 - o) / 2),
            c = Math.round((200 - u) / 2),
            h = this.doc.createElement("div");
        n = "display: table; vertical-align: middle; position: relative;";
        n += "padding: " + c + "px 0 0 " + l + "px;";
        this.setStyles(h, n);
        var p = this.doc.createElement("img");
        n = "-moz-box-shadow: 0 2px 12px rgba(0,0,0,.75); -webkit-box-shadow: 0 2px 12px rgba(0,0,0,.75); box-shadow: 0 2px 12px rgba(0,0,0,.75); display: inline-block;";
        this.setStyles(p, n);
        p.src = e.src;
        p.width = o;
        p.height = u;
        p.title = i + "x" + s;
        var d = this.doc.createElement("div");
        n = "width: " + o + "px; height: " + u + "px; position: absolute; top: " + c + "px; left: " + l + "px; opacity: 0; line-height: " + u + "px; text-align: center; font-weight: bold; color:#ffffff;";
        n += "background: url('http://xiaoshelang.undefinedblog.com/wp-content/themes/canon/img/bk_shader.png'); font-size: 18px; text-shadow: 0 1px 3px rgba(0,0,0,.75); font-family: Helvetica, arial, sans-serif;";
        n += "-webkit-transition: all 0.2s ease-out; -moz-transition: all 0.2s ease-out; -ms-transition: all 0.2s ease-out; -o-transition: all 0.2s ease-out; transition: all 0.2s ease-out;";
        n += "cursor: pointer;";
        d.innerHTML = "保存图片";
        d.title = i + "x" + s;
        this.setStyles(d, n);
        h.appendChild(p);
        t.appendChild(h);
        t.appendChild(d);
        this.imageHolder.appendChild(t);
        t.onclick = this.delegate(this.onClickDiv, this);
        t.onmouseover = this.delegate(this.onMouseOverDiv, this);
        t.onmouseout = this.delegate(this.onMouseOutDiv, this);
        this.data[r] = e;
        this.buttons[r] = d;
        this.overlays.push(t)
    };
    this.createHeader = function () {
        var e = this.doc.createElement("div"),
            t = "width: 100%; height: 60px;";
        this.setStyles(e, t);
        var n = this.doc.createElement("span");
        t = "display: inline-block; float:left; line-height: 40px; background-color: transparent; text-indent: 0px;";
        t += "font-size: 20px; color: #ffffff; font-family: Helvetica, arial, sans-serif; font-weight: bold; padding: 0 10px 0 0; cursor: pointer;";
        this.setStyles(n, t);
        n.innerHTML = "Wookmark";
        n.onclick = this.delegate(this.onClickLogo, this);
        var r = this.doc.createElement("span");
        t = "display: inline-block; float:left; line-height: 40px; background-color: transparent; text-indent: 0px; text-shadow: 0 1px 3px rgba(0,0,0,.75); ";
        t += "font-size: 13px; color: #ffffff; font-family: Helvetica, arial, sans-serif; font-weight: normal; padding: 3px 0 0 0;";
        this.setStyles(r, t);
        r.innerHTML = "点击图片将其保存至小摄郎";
        var i = this.doc.createElement("p");
        t = "display: inline-block; float:left; line-height: 40px; background-color: transparent;";
        t += "font-size: 20px; color: #ffffff; font-family: Helvetica, arial, sans-serif; font-weight: bold; padding: 6px 0 0 20px; cursor: pointer;";
        this.setStyles(i, t);
        i.appendChild(r);
        e.appendChild(i);
        var s = this.doc.createElement("p");
        t = "display: inline-block; float:right; width: 38px; height: 38px; background: url('http://xiaoshelang.undefinedblog.com/wp-content/themes/canon/img/bk_close.png') no-repeat center center;";
        t += "margin: 10px 10px 0 0; cursor: pointer; padding: 0;";
        this.setStyles(s, t);
        s.onclick = this.delegate(this.cancel, this);
        e.appendChild(s);
        this.container.appendChild(e);
        this.header = e;
    };
    this.cancel = function () {
        this.toggleHideElements(!0);
        this.doc.body.removeChild(this.container);
        window.scrollTo(this.scrollPosition.x, this.scrollPosition.y);
    };
    this.onClickLogo = function () {
        var e = "http://xiaoshelang.undefinedblog.com";
        window.open(e);
    };
    this.getDocHeight = function () {
        return Math.max(Math.max(this.doc.body.scrollHeight, this.doc.documentElement.scrollHeight), Math.max(this.doc.body.offsetHeight, this.doc.documentElement.offsetHeight), Math.max(this.doc.body.clientHeight, this.doc.documentElement.clientHeight))
    };
    this.getDocWidth = function () {
        var e = window,
            t = "inner";
        if (!("innerWidth" in window)) {
            t = "client";
            e = document.documentElement || document.body;
        }
        return e[t + "Width"];
    };
    this.onClickDiv = function (e) {
        var t = this.normalizeEventTarget(e),
            n = t.id,
            r = this.data[n],
            i = e.shiftKey;
        this.disableOverlay(t);
        this.saveToWookmark(r, i);
        e.shiftKey || this.cancel();
    };
    this.normalizeEventTarget = function (e) {
        window.event && (e = window.event);
        var t = e.currentTarget ? e.currentTarget : e.srcElement;
        while (t.className != "wkGridImage" && t.parentNode) t = t.parentNode;
        return t
    };
    this.onMouseOverDiv = function (e) {
        var t = this.normalizeEventTarget(e),
            n = this.data[t],
            r = t.getElementsByTagName("div"),
            i = r[1];
        this.selectedImage = n;
        i.style.opacity = 1
    };
    this.onMouseOutDiv = function (e) {
        var t = this.normalizeEventTarget(e),
            n = t.getElementsByTagName("div"),
            r = n[1];
        this.selectedImage = null;
        r.style.opacity = 0
    };
    this.disableOverlay = function (e) {
        var t = e.getElementsByTagName("div"),
            n = t[1];
        n.innerHTML = "Saved";
        n.style.opacity = 1;
        e.onclick = function () {};
        e.onmouseover = function () {};
        e.onmouseout = function () {}
    };
    this.getDocument = function () {
        var e = document;
        0 < this.frames.length && (e = window[0].document);
        return e
    };
    this.saveToWookmark = function (e, t) {
        if (e === null) return;

        var n = e.source && e.id,
            r, i = "",
            s, o = this.selectedText;
        if (n) {
            r = e.image;
            s = e.source + ":" + e.id;
            i = e.title
        } else {
            r = e.src;
            i = e.alt;
            s = "";
        }

        var u = {
            url: r,
            referer: e.src == location.href ? document.referrer : location.href,
            title: this.doc.title,
            alt: i,
            description: o,
            token: this.getKeywords()
        }, a = [];
        a.push("http://xiaoshelang.undefinedblog.com/plugin/add");
        a.push("?");

        for (var f in u) {
            a.push(encodeURIComponent(f));
            a.push("=");
            a.push(encodeURIComponent(u[f]));
            a.push("&")
        }

        if (t) {
            var l = this.doc.createElement("iframe");
            l.src = a.join("");
            l.width = 1;
            l.height = 1;
            var c = "position: absolute; left: -1000px; top: -1000px;";
            this.setStyles(l, c);
            this.container.appendChild(l);
        } else {
            var h = "status=no,resizable=yes,scrollbars=no,personalbar=no,directories=no,location=no,toolbar=no,menubar=no,width=600,height=575,left=0,top=0";
            window.open(a.join(""), "小摄郎", h);
        }
        return !1;
    };
    this.removeOverlays = function () {
        var e, t = 0;
        for (; t < this.overlays.length; t++) {
            e = this.overlays[t];
            e.parentNode.removeChild(e);
        }
        this.data = {};
        this.overlays = [];
    };
    this.getElementOffset = function (e) {
        var t = 0,
            n = 0,
            r;
        do {
            n += e.offsetTop || 0;
            t += e.offsetLeft || 0;
            e = e.offsetParent;
            if (e) {
                r = e.style.position;
                if (r == "relative" || r == "absolute") break;
            }
        } while (e);
        return [t, n];
    };
    this.getSelectedText = function () {
        var e = "";
        window.getSelection ? e = window.getSelection() : this.doc.getSelection ? e = this.doc.getSelection() : e = this.doc.selection.createRange().text;
        e = "" + e;
        e = e.replace(/(^\s+|\s+$)/g, "");
        return e;
    };
    this.getKeywords = function () {
        var e = "",
            t = document.getElementsByTagName("meta");
        if (t)
            for (var n = 0, r = t.length; n < r; n++) t[n].name.toLowerCase() == "keywords" && (e += t[n].content);
        return e !== "" ? e : "";
    };
    this.delegate = function (e, t) {
        var n = function () {
            return e.apply(t, arguments);
        };
        return n;
    };

    "indexOf" in Array.prototype || (Array.prototype.indexOf = function (e, t) {
        t === undefined && (t = 0);
        t < 0 && (t += this.length);
        t < 0 && (t = 0);
        for (var n = this.length; t < n; t++)
            if (t in this && this[t] === e) return t;
        return -1
    });




};
wookmark.instance = new wookmark.Bookmarklet;
wookmark.instance.init();