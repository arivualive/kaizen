// accordion 2

//$(function(){
//	$('.subject-list-accordion .togglebtn').on('click', function(){
//		$(this).siblings('.togglemenu').slideToggle();
//		$(this).toggleClass('active');
//	});
//});

$(function(){
	var Accordion = function(el, multiple) {
		this.el = el || {};
		this.multiple = multiple || false;

		// Variables privadas
		var links = this.el.find('.togglebtn');
		// Evento
		links.on('click', {el: this.el, multiple: this.multiple}, this.dropdown)
	}

	Accordion.prototype.dropdown = function(e) {
		var $el = e.data.el;
			$this = $(this),
			$next = $this.next();

		$next.slideToggle();
		$this.parent().toggleClass('open');

		//if (!e.data.multiple) {
		//	$el.find('.submenu').not($next).slideUp().parent().removeClass('open');
		//};
	}	

	var accordion = new Accordion($('.subject-list-accordion'), false);
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