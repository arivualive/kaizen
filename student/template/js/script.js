// accordion 2

$(function(){
	$('.subject-list-accordion .togglebtn').on('click', function(){
		$(this).siblings('.togglemenu').slideToggle();
		$(this).toggleClass('active');
	});
});

//small width add class
$(window).resize(function(){
    //windowの幅をxに代入
    var x = $(window).width();
    //windowの分岐幅をyに代入
    var y = 767;
    if (x <= y) {
        //$('.subject-list-head').addClass('btn-dropdown');
        //$('.subject-list-body').addClass('menu-dropdown');
    }
});

$(function() {
  var $document   = $(document),
    selector    = '[data-rangeslider]',
    $element    = $(selector);
  function valueOutput(element) {
    var value = element.value,
      output = element.parentNode.getElementsByTagName('output')[0];
      output.innerHTML = value;
  }
  for (var i = $element.length - 1; i >= 0; i--) {
    valueOutput($element[i]);
  };
  $document.on('change', 'input[type="range"]', function(e) {
    valueOutput(e.target);
    
  });
  $document.on('input', 'input[type="range"]', function(e) {
    valueOutput(e.target);
    
  });
  $element.rangeslider({
    polyfill: false,
    onInit: function() {},
    onSlide: function(position, value) {
      console.log('onSlide');
      console.log('position: ' + position, 'value: ' + value);
    },
    onSlideEnd: function(position, value) {
      console.log('onSlideEnd');
      console.log('position: ' + position, 'value: ' + value);
    }
  });
});