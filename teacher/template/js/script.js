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

	var accordion = new Accordion($('#accordion'), false);
});

jQuery(document).ready(function (e) {
    function t(t) {
        e(t).bind("click", function (t) {
            t.preventDefault();
            e(this).parent().fadeOut()
        })
    }
    e(".link").click(function () {
        var t = e(this).parents(".button-dropdown").children(".submenu").is(":hidden");
        e(".button-dropdown .submenu").hide();
        e(".button-dropdown .link").removeClass("active");
        if (t) {
            e(this).parents(".button-dropdown").children(".submenu").toggle().parents(".button-dropdown").children(".link").addClass("active")
        }
    });
    e(document).bind("click", function (t) {
        var n = e(t.target);
        if (!n.parents().hasClass("button-dropdown")) e(".button-dropdown .submenu").hide();
    });
    e(document).bind("click", function (t) {
        var n = e(t.target);
        if (!n.parents().hasClass("button-dropdown")) e(".button-dropdown .link").removeClass("active");
    })
});