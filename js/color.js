var gbks = gbks || {};

gbks.Colors = function() {

  this.init = function() {
    this.palette = $('#colorsTile ol');
    this.colors = $('li', this.palette);

    this.colors.click($.proxy(this.onClickColor, this));

    this.actives = [];
    this.choices = [];
    this.loadTimer = null;

    $(window).resize($.proxy(this.resize, this));

    if(typeof(grid) !== 'undefined') {
      grid.layout.initAutoLayout();
    }
  };

  this.onClickColor = function(event) {
    var item = $(event.currentTarget);
    item.closest('ol').find(".active").removeClass("active");
    var hex = item.attr('data-hex');

    grid.layout.currentPage = 0;

    item.addClass('active');
    this.loadColors(hex);

    clearTimeout(this.loadTimer);
    if(this.choices.length > 0) {
      this.loadTimer = setTimeout($.proxy(this.loadColors, this), 250);
    }
  };


  this.loadColors = function(colors) {

    $('#clue', this.canvas).remove();
    $('#noMoreImages').remove();

    var layout = grid.layout;
    layout.config = {type: 'color', page: 0, hex: colors};
    $('#images .polaroid').remove();
    layout.loadMore();
  };

};

$(document).ready(function() {
  var colors = new gbks.Colors();
  colors.init();
});