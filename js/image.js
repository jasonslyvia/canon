var gbks = gbks || {};
gbks.imageInstance = null;
gbks.Image = function() {

  this.init = function() {
      gbks.imageInstance = this;

      this.setup();

      this.polaroid = new gbks.Polaroid();
      this.polaroid.init();

      this.sharePopup = null;
      this.flagPopup = null;
      this.ctrlDown = false;
      this.shiftDown = false;
      this.imageLoadTimer = null;

      this.loader = $('#loader');

      if(this.auth) {
        //保存按钮
        $('#details #tagOptions .saveButton').live('click', $.proxy(this.onClickAddImage, this));
        //喜欢按钮
        $('#details #likeImageButton').live('click', $.proxy(this.onClickLikeImage, this));
        //关注按钮
        $('.followButton').click($.proxy(this.onClickFollowButton, this));

        // Add to group.
        // $('#addToGroupsButton').live('click', $.proxy(this.onClickAddToGroup, this));
        // //$('#formCreateGroup input').focus($.proxy(this.onFocusAddToGroupInput, this));
        // //$('#formCreateGroup').submit($.proxy(this.onClickCreateGroup, this));
        // $('#addToGroups li input[type=checkbox]').live('change', $.proxy(this.onToggleGroupCheckbox, this));
      }
      else{
          //未登录single中评论按钮
          $("#details #commentForm textarea").live("keyup", function(e){
              e.stopPropagation();

              var imageId = $(this).closest('#commentForm').attr("data-imageid");
              location.href = "/signup?next=" + encodeURIComponent("/?p=" + imageId);
          });

          //未登录single中喜欢与保存按钮
          $("#details #likeImageButton, #details .saveButton").live("click", function(e){
              e.stopPropagation();

              var $this = $(this);
              var imageId = $this.attr("data-id");

              location.href = "/signup?next=" + encodeURIComponent("/?p="+imageId);
          });

          //未登录single中关注按钮跳转
          $(".followButton").live("click", function(e){
              e.stopPropagation();

              var userId = $(this).attr("data-id");
              location.href = "/signup?next=" + encodeURIComponent("/profile/" + userId);
          });
      }

      $('#details #shareImageButton').live('click', $.proxy(this.onClickShareImage, this));

      $('.backup img').live('click', $.proxy(this.onClickScrollUp, this));

      $('#details .expander').click($.proxy(this.onClickExpand, this));

      this.updateImageSize();
      $(window).resize($.proxy(this.resize, this));


      this.commentKeyUpMethod = $.proxy(this.onCommentKeyUp, this);
      this.commentForm = $('#commentForm', this.details);
      this.commentInput = $('textarea', this.commentForm);
      if(this.commentInput.length > 0) {
        this.commentInput.focus($.proxy(this.onFocusCommentField, this));
        this.commentInput.blur($.proxy(this.onBlurCommentField, this));
      }

      this.fadeImages();
  };

  //图片加载完成后渐显载入
  this.fadeImages = function() {
    if(Modernizr.opacity && Modernizr.cssanimations) {
      var imgs = $('img');
      imgs.each(function(index) {
        if(!this.complete) {
          var t = $(this);
          t.addClass('imageLoading');
          t.one('load', function(index) {
            $(this).addClass('imageLoaded');
          });
        }
      });
    }
  };

  this.onScroll = function(event) {
    //console.log('onScroll', $('#details').height(), $(window).scrollTop());
    //$('#details').css('margin-top', $(window).scrollTop()+'px');
  };

  this.onKeyDown = function(event) {
    if(event.which == 224) this.ctrlDown = true;
    if(event.which == 16) this.shiftDown = true;
  };

  this.onKeyUp = function(event) {
    if(event.which == 224) this.ctrlDown = false;
    if(event.which == 16) this.shiftDown = true;
  };

  //初始化基本对象
  this.setup = function() {
    this.auth = $('body').hasClass('auth');

    this.canvas = $('#image');
    this.details = $('#details');

    this.pixelRatio = (!!window.devicePixelRatio ? window.devicePixelRatio : 1);

    this.isVideo = (this.canvas.attr('data-video') == 'true');

    this.imageId = this.canvas.attr('data-id');
  };

  this.onClickSimilarImage = function(event) {
    this.handleImageClick(event, 'similar');
  };

  this.onClickNextImage = function(event) {
    this.handleImageClick(event, 'next');
  };

  this.onClickMoreImage = function(event) {
    this.handleImageClick(event, 'more');
  };

  this.handleImageClick = function(event, source) {
    if(!this.ctrlDown) {
      event.preventDefault();
      event.stopPropagation();

      var item = $(event.currentTarget);
      var link = item.attr('href');
      var success = gbks.common.history.push(link);
      this.loadImage(link, source);
    }
  };

  this.loadImage = function(link, source) {
    window.scrollTo(0, 0);
    $('#page').addClass('loadingImage');

    clearTimeout(this.imageLoadTimer);
    this.imageLoadTimer = setTimeout($.proxy(this.onImageLoadTimer, this), 1000);

    $.ajax({
      url: link,
      type: 'GET',
      success: $.proxy(this.onLoadImage, this)
    });
  };

  this.onImageLoadTimer = function(event) {
    $('#page').addClass('spinner');
  };

  this.onLoadImage = function(data) {
    $('#page').removeClass('loadingImage');
    $('#page').removeClass('spinner');

    clearTimeout(this.imageLoadTimer);
    var page = $(data);
    var wrap = $('.wrap', page);
    var image = $('#image', page);
    var moreImages = $('#moreImages', page);
    var details = $('#details', page);

    $('#image').replaceWith(image);
    $('#details .options').replaceWith($('.options', details));
    $('#details .similar').replaceWith($('.similar', details));
    $('#details .detailsWrap').replaceWith($('.detailsWrap', details));
    $('#details .sharer').replaceWith($('.sharer', details));

    $('#nextImage a').attr('href', $($('#details .similar a')[0]).attr('href'));

    var a = $('#details .adminImage');
    if(a.length > 0) a.replaceWith($('.adminImage', details));
    var oldSections = $('div.section', moreImages);
    var adSections = $('#moreImages .section.werbung');
    var newSections = $('div.section', moreImages);

    if(adSections.length > 0) {
      // Clear out all old sections that aren't ads.
      $("#moreImages .section:not(.werbung)").remove();

      // Insert new non-ad sections in between ads.
      var firstAd = $(adSections[0]);
      $('.section:not(.werbung)', moreImages).insertAfter(firstAd);
    } else {
      $('#moreImages').replaceWith(moreImages);
    }

    var h = parseInt($('#image').attr('data-height'));
    if(h > 650) $('#details .ad').show();
    else $('#details .ad').hide();

    var title = page.filter('title').html();
    if(title && title.length > 0) document.title = title;

    this.setup();
    this.updateImageSize();
    this.fadeImages();
  };

  this.onHistoryChange = function(event) {
    var state = event.originalEvent.state;
    if(state && state.url) {
      this.loadImage(state.url, 'history');
    }
  };

  this.onClickExpand = function(event) {
    event.preventDefault();
    event.stopPropagation();

    var target = $(event.currentTarget);
    var holder = $(target.parents('div')[0]);
    var expand = $('.expand', holder);
    var hidden = $('.hidden', holder);
    expand.hide();
    hidden.show();
  };

  this.resize = function() {
    clearTimeout(this.layoutTimer);
    this.layoutTimer = setTimeout($.proxy(this.updateImageSize, this), 500);
  };

  this.toggleEmbedOption = function() {
    var option = $('.embedOption');
    console.log('toggleEmbedOption', option, option.hasClass('active'));

    this.track('Image', 'toggleEmbed', this.imageId);

    if(option.hasClass('active')) {
      option.removeClass('active');
      $('#details .embedPanel').slideUp();
    } else {
      option.addClass('active');
      $('#details .embedPanel').slideDown();
    }
  };

  this.onFocusEmbed = function() {
    this.track('Image', 'focusEmbed', this.imageId);
    $('#details .embedPanel textarea').select();
  };

  this.displayShareOptions = function(event) {
    event.stopPropagation();
    event.preventDefault();

    if(addthis) {
      var holder = $('.shareBox');
      var content = '<div id="addthisoptions" class="addthis_toolbox addthis_default_style divide"><a class="addthis_button_facebook"></a><a class="addthis_button_twitter"></a><a class="addthis_button_email"></a><a class="addthis_button_google_plusone" g:plusone:size="small" g:plusone:count="false"></a></div>';
      holder.html(content);
      addthis.toolbox("#addthisoptions");
    }
  };

  //点击编辑图片按钮
  this.onClickAddImage = function(event) {
    event.stopPropagation();
    event.preventDefault();

    var imageId = $(event.currentTarget).attr('data-id');
    this.toggleSaveButton(true);

    $('#details #tagOptions .saveButton').addClass('loading');

    $.ajax({
      url:  CANON_ABSPATH + "/functions/save_pic.php",
      type: 'POST',
      data: {imageId: imageId, nonce: nonce},
      dataType: 'json',
      success: $.proxy(this.onAddImageComplete, this)
    });
  };

  this.onAddImageComplete = function(result) {
    var saveButton = $('#details #tagOptions .saveButton');
    saveButton.removeClass('loading');

    this.savePopup = new gbks.common.SavePopup();
    this.savePopup.init(saveButton, result, $.proxy(this.onClickRemoveImage, this), $.proxy(this.onCloseSavePopup, this));
  };

  this.onCloseSavePopup = function(event) {
  };

  this.onClickRemoveImage = function() {
    this.toggleSaveButton(false);
  };

  this.onRemoveImageComplete = function(event) {
    this.hideLoader();
  };

  this.toggleSaveButton = function(active) {
    var saveButton = $('#details #tagOptions .saveButton');
    if(active === true) {
      saveButton.addClass('active');
      $('span', saveButton).html('编辑');
    } else {
      saveButton.removeClass('active');
      $('span', saveButton).html('保存');
    }
  };

  this.onClickLikeImage = function( event ) {
    event.stopPropagation();
    event.preventDefault();

    var $btn = $(event.currentTarget);
    var imageId = $btn.attr('data-id');
    $btn.toggleClass('active');

    this.showLoader('发送喜欢请求……');

    $.ajax({
      url: CANON_ABSPATH + "/functions/like_pic.php",
      data: {imageId:imageId, nonce: nonce},
      type: 'POST',
      success: $.proxy(this.hideLoader, this)
    });
  };

  /**
   * Groups.
   */

  this.onClickAddToGroup = function( event ) {
    event.preventDefault();
    event.stopPropagation();

    $('#addToGroups').toggle();
  };

  this.onFocusAddToGroupInput = function( event ) {
    $('#formCreateGroup').addClass('active');

    var field = $(event.currentTarget);
    var def = field.attr('data-default');
    var value = field.val();
    if(value == def) {
      field.val('');
    }
  };

  this.onClickCreateGroup = function( event ) {
    event.preventDefault();

    var form = $('#formCreateGroup');
    var nameField = $( 'input[type=text]', form );
    var imageId = $( 'input[name="imageId"]', form ).val();
    var groupName = nameField.val();

    var groupPrivate = false;
    var privateField = $('input[type=checkbox]', form);
    if(privateField.length > 0) groupPrivate = privateField.is(':checked');

    var nameDefault = nameField.attr('data-default');

    if(groupName.length > 0 && groupName != nameDefault) {
      //console.log('onClickCreateGroup', form, imageId, groupName, url, groupPrivate);

      // Hide and clear group creation form.
      form.removeClass('active');
      nameField.val('');

      this.showLoader('Creating group');

      // Make call.
      $.ajax({
        url: '/groups/create',
        data: {imageId:imageId, groupName:groupName, groupPrivate:groupPrivate},
        type: 'POST',
        success: $.proxy(this.onCreateGroup, this)
      });
    }
  };

  // this.onCreateGroup = function(json) {
  //   //var group = $.parseJSON(json);
  //   console.log('onCreateGroup', json);

  //   var html = '<li><input type="checkbox" name="groupId" value="'+json.id+'" checked="true" />'+json.name+'</li>';

  //   $('#addToGroups ul').append(html);
  //   $('#addToGroups ul').removeClass('empty');

  //   this.hideLoader();
  // };

  // this.onToggleGroupCheckbox = function( event ) {
  //   var box = $(event.currentTarget);
  //   var groupId = box.val();
  //   var checked = box.attr('checked');
  //   var imageId = box.attr('data-imageId');

  //   var url = '/groups/removeImageFromGroup';
  //   if(checked) {
  //     url = '/groups/addImageToGroup';
  //   }

  //   //console.log('onToggleGroupCheckbox', box, groupId, checked, imageId);

  //   $(box.parents('li')[0]).addClass('loading');

  //   //this.showLoader('Saving to group');

  //   // Make call.
  //   var data = {imageId:imageId, groupId:groupId};
  //   $.ajax({
  //     url: url,
  //     data: data,
  //     type: 'POST',
  //     success: $.proxy(this.onToggleGroupSaved, this)
  //   });
  // };

  // this.onToggleGroupSaved = function( event ) {
  //   $('#addToGroups li').removeClass('loading');
  // };

  // this.onClickDownloadImage = function(event) {
  //   this.track('Image', 'download', this.imageId);
  // };

  // this.onClickFlagImage = function(event) {
  //   event.preventDefault();
  //   event.stopPropagation();

  //   var button = $(event.currentTarget);
  //   if(this.flagPopup) {
  //     button.removeClass('active');
  //     this.flagPopup.remove();
  //     this.flagPopup = null;
  //   } else {
  //     button.addClass('active');
  //     $.ajax({
  //       url: '/groups/popup',
  //       data: {imageId:this.imageId},
  //       type: 'POST',
  //       success: $.proxy(this.onLoadFlagInfo, this)
  //     });
  //   }
  // };

  // this.onLoadFlagInfo = function(data) {
  //   var html = gbks.common.wrapPopupContent(data.html);
  //   this.flagPopup = $(html);
  //   gbks.common.positionPopup(this.flagPopup);
  // };

  //分享图片
  this.onClickShareImage = function(event) {
    event.preventDefault();
    event.stopPropagation();

    //var sharing = $('#details .sharing');
    var button = $('#shareImageButton');
    var isActive = button.hasClass('active');

    // if(this.sharePopup) {
    //   this.sharePopup.hide();
    //   this.sharePopup = null;
    // }

    if(isActive) {
      button.removeClass('active');
      $("#sharePopup").fadeOut(200);
    } else {
      button.addClass('active');
      $("#sharePopup").fadeIn(200);
      $("#sharePopup").one("click", function(){
          $(this).hide();
          button.removeClass('active');
      });

      // this.sharePopup = new gbks.common.SharePopup();
      // this.sharePopup.display(this.imageId, button, $.proxy(this.onHideSharePopup, this));
    }
  };

  this.onHideSharePopup = function() {
    $('#details #shareImageButton').removeClass('active');
  };

  this.track = function(one, two, three) {
    if(typeof(_gaq) !== 'undefined') {
      _gaq.push(['_trackEvent', one, two, three]);
    }
  };

  /**
   * Adjust image to browser window width.
   * Makes sure it always fits in screen, and
   * expands when there is room up to full size
   */
  this.updateImageSize = function() {
    var image = $('#image');
    var w = image.attr('data-width');
    var h = image.attr('data-height');

    // Adjust to pixel ration.
    if(this.pixelRatio == 2) {
      w /= this.pixelRatio;
      h /= this.pixelRatio;
    }

    var details = $('details');
    var detailsWidth = 317;
    var detailsHeight = details.height();
    var windowWidth = $(window).width() - 40 - 60;

    if($('body').hasClass('hidenav') !== true) {
      windowWidth -= $('#kaori').width();
    }

    var imageMaxWidth = windowWidth - detailsWidth;

    var imageWidth = Math.min(imageMaxWidth, w);
    if(this.isVideo) {
      imageWidth = imageMaxWidth;
    }

    imageWidth = Math.max(imageWidth, 200);
    var wrapWidth = detailsWidth + imageWidth;

    if(windowWidth <= 530) {
      image.parent().css('width', 'auto');
      imageWidth = Math.round(windowWidth);
    } else {
      image.parent().css('width', wrapWidth+'px');
    }

    var imageHeight = imageWidth*h/w;
    if(this.isVideo) {
      imageHeight = Math.round(imageWidth/16*9);
    }

    var minHeight = Math.max(600, detailsHeight);
    var boxHeight = Math.max(minHeight, imageHeight);
    var imgY = Math.round((boxHeight-imageHeight)/2);

    $('.image', image).css('width', imageWidth+'px');
    var img = $('.image img', image);
    if(img.length == 0) img = $('.image iframe', image);
    img.attr({
      width: imageWidth,
      height: imageHeight
    });
    img.css('padding-top', imgY+'px');
    $('.image', image).css('height', boxHeight+'px');
  };

  this.showLoader = function(message) {
    if(!this.loader || this.loader.length == 0) {
      this.loader = $('#loader');
    }
    return;

    this.loader.stop();

    if(message && message.length > 0) {
      this.loader.html(message);
    } else {
      this.loader.html('');
    }

    this.loader.show();
    this.loader.animate({opacity:1}, 50);
  };

  this.hideLoader = function() {
    this.loader.stop();
    var callback = null;
    if(this.onHideLoader) {
      callback = $.proxy(this.onHideLoader, this);
    }
    this.loader.animate({opacity:0}, 250, callback);
  };

  this.onHideLoader = function(event) {
    this.loader.hide();
  };

  this.onClickScrollUp = function(event) {
    event.preventDefault();
    event.stopPropagation();
    gbks.common.scroller.scrollToPosition(0);
  };

  //关注用户
  this.onClickFollowButton = function(event) {
    event.preventDefault();
    event.stopPropagation();

    var target = $(event.currentTarget);
    var link = $('a', target);
    var userId = target.attr('data-id');
    var type = target.attr('data-type');
    var data = {
      targetId: userId,
      nonce: nonce
    };

    var isFollowing = target.hasClass('active');

    if(userId) {
      var url = CANON_ABSPATH + "/functions/follow_user.php";
      var text = '取消关注';

      if(isFollowing) {
        text = '关注';
        target.removeClass('active');
        data.action = "unfollow";
      }
      else {
        data.action = "follow";
        target.addClass('active');
      }

      link.html(text);

      $.ajax({
        url: url,
        data: data,
        type: 'POST',
        success: $.proxy(this.onSubmitFollow, this)
      });
    }
  };

  this.onSubmitFollow = function(data, textStatus, jqXHR) {
    //this.hideLoader();
  };

  this.onFocusCommentField = function(event) {
    this.commentForm.addClass('active');
    this.commentInput.addClass('active');
    if(this.commentInput.val() == this.commentInput.attr('placeholder')) {
      this.commentInput.val('');
    }
    $(document).bind('keyup', this.commentKeyUpMethod);
  };

  this.onBlurCommentField = function(event) {
    if(this.commentInput.val() == '') {
      this.commentInput.val(this.commentInput.attr('placeholder'));
      this.commentForm.removeClass('active');
    }
    $(document).unbind('keyup', this.commentKeyUpMethod);
  };

  this.onCommentKeyUp = function(event) {
    var comment = this.commentInput.val();
    if(event.which == 13 && comment.length > 2 && comment != this.commentInput.attr('placeholder')) {
      event.stopPropagation();
      event.preventDefault();
      this.saveComment(comment);
    }
  };

  //发表评论
  this.saveComment = function(comment) {
    $(document).unbind('keyup', this.commentKeyUpMethod);

    var lower = comment.toLowerCase();
    var isGood = true;
    if(comment == this.commentInput.attr('placeholder')) isGood = false;
    if(lower.length < 3) isGood = false;

    if (isGood) {
        this.commentInput.attr("disabled", "disabled");
        $.ajax({
            url: CANON_ABSPATH + "/functions/add_comment.php",
            data: {
                imageId: this.imageId,
                comment: comment,
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

  this.onSaveComment = function(data, textStatus, jqXHR) {
    $('#comments .comments').append(data.html).hide().fadeIn();
    $('#comments').removeClass('empty');
    this.commentForm.remove();
  };

};

$(document).ready(function(){
  var image = new gbks.Image();
  image.init();
});
