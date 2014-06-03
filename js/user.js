var gbks = gbks || {};

gbks.User = function() {

  this.init = function(config) {
    this.config = config;
    this.auth = $("body").hasClass("auth");

    if (this.auth) {
      $('Button.follow').click($.proxy(this.onClickFollowButton, this));
    }

    $('#userInfo .expand').click($.proxy(this.onClickExpand, this));
    $('#userInfo .following .hider').click($.proxy(this.viewAllFollowing, this));
    $(".more-follow-area .more-btn").one("click", $.proxy(this.loadMoreFollowList, this));

    setTimeout(function(){$('#userNav').show();}, 25);
  };

  this.viewAllFollowing = function(event) {
    $('#userInfo .following .hidden').show();
    $('#userInfo .following .hider').hide();

    this.layout.layout();
  };

  this.toggleInfoSection = function(event) {
    var target = $(event.currentTarget);
    var container = $(target.parent());
    var list = $('ul', container);
    list.toggle();
  };

  this.onClickFollowButton = function(event) {
    event.preventDefault();
    event.stopPropagation();

    var button = $(event.currentTarget);
    var userId = button.attr('data-id');
    var isFollowing = button.hasClass('active');

    if(userId) {
      var url = CANON_ABSPATH + "/functions/follow_user.php";
      var text = '已关注';
      var data =  {
        targetId: userId,
        nonce: nonce
      };

      if(isFollowing) {
        text = '关 注';
        button.removeClass('active');
        data.action = "unfollow";
      }
      else {
        data.action = "follow";
        button.addClass('active');
      }

      button.html(text);

      $.ajax({
        url: url,
        data: data,
        type: 'POST',
        success: $.proxy(this.onSubmitFollow, this)
      });
    }


  };

  this.onSubmitFollow = function(data, textStatus, jqXHR) {
    this.hideLoader();
  };

  this.onClickExpand = function(event) {
    event.preventDefault();
    event.stopPropagation();

    var target = $(event.currentTarget);
    var holder = $(target.parents('ul')[0]);
    var expand = $('.expand', holder);
    var hidden = $('.archived', holder);
    expand.hide();
    hidden.show();

    grid.layout.resize();
  };

  this.showLoader = function(message) {
    if(!this.loader || this.loader.length === 0) {
      this.loader = $('#loader');
    }

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
    if (!!this.loader) {
      this.loader.stop();
    }
    var callback = null;
    if(this.onHideLoader) {
      callback = $.proxy(this.onHideLoader, this);
    }
    this.loader.animate({opacity:0}, 250, callback);
  };

  this.onHideLoader = function(event) {
    this.loader.hide();
  };

  this.loadMoreFollowList = function(e){
    e.preventDefault();
    var $btn = $(".more-follow-area .more-btn");
    var $data = $btn.parent().data("more");
    if (!$data) {
      return false;
    }
    else{
      var html = '';
      for (var i = 0; i < $data.length; i++) {
        var name = $data[i].display_name;
        var id = $data[i].follower_id || $data[i].followee_id;
        html += '<li><a href="/profile/'+id+'">'+name+'</a></li>';
      }
      $btn.parent().parent().append(html);
      $btn.parent().remove();

      grid.layout.resize();
    }
  };

};

var userInstance = null;
$(document).ready(function() {
  if(pageConfig) {
    userInstance = new gbks.User();
    userInstance.init(pageConfig);
  }
});