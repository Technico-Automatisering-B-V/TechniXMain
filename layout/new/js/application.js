
$(function () {

	Application.init ();

    $('a.export').on('click', function (e) {
        $('#exportModal').modal();
    })

    $('a.print').on('click', function (e) {
        e.preventDefault();
        $('.table').parent().print();
        return false;
    });

    $(".pagination").appendTo($(".pagination-wrapper"));
});



var Application = function () {

	var validationRules = getValidationRules ();
    var searchInput = $('#search');

	return { init: init, validationRules: validationRules };

	function init () {

        enableBackToTop();
		enableLightbox();
		enableCirque();
		enableEnhancedAccordion();
        setDataTablesDefaults();
        enableDataTable();
	}

    function setDataTablesDefaults () {
        $.extend(true, $.fn.dataTable.defaults, {
            "sDom": "tp",
            "sPaginationType": "bootstrap",
            "iDisplayLength": 10
        });
    }

    function enableDataTable () {
        var oTable = $('table').dataTable();

        if (searchInput) {
            searchInput.focus();

            $(searchInput).keyup(function() {
                oTable.fnFilter(this.value);
            }).keypress(function (e) {
                var charCode = e.charCode || e.keyCode;
                if (charCode  == 13) { //Enter key's keycode
                    return false;
                }
            });
        }
    }

	function enableCirque () {
		if ($.fn.lightbox) {
			$('.ui-lightbox').lightbox ();
		}
	}

	function enableLightbox () {
		if ($.fn.cirque) {
			$('.ui-cirque').cirque ({  });
		}
	}

	function enableBackToTop () {
		var backToTop = $('<a>', { id: 'back-to-top', href: '#top' });
		var icon = $('<i>', { class: 'icon-chevron-up' });

		backToTop.appendTo ('body');
		icon.appendTo (backToTop);

	    backToTop.hide();

	    $(window).scroll(function () {
	        if ($(this).scrollTop() > 150) {
	            backToTop.fadeIn();
	        } else {
	            backToTop.fadeOut();
	        }
	    });

	    backToTop.click (function (e) {
	    	e.preventDefault();

	        $('body, html').animate({
	            scrollTop: 0
	        }, 600);
	    });
	}

	function enableEnhancedAccordion () {
		$('.accordion').on('show', function (e) {
	         $(e.target).prev('.accordion-heading').parent().addClass('open');
	    });

	    $('.accordion').on('hide', function (e) {
	        $(this).find('.accordion-toggle').not($(e.target)).parents('.accordion-group').removeClass('open');
	    });

	    $('.accordion').each (function () {
	    	$(this).find('.accordion-body.in').parent().addClass('open');
	    });
	}

	function getValidationRules () {
		var custom = {
	    	focusCleanup: false,

			wrapper: 'div',
			errorElement: 'span',

			highlight: function(element) {
				$(element).parents('.control-group').removeClass('success').addClass('error');
			},
			success: function(element) {
				$(element).parents('.control-group').removeClass('error').addClass('success');
				$(element).parents('.controls:not(:has(.clean))').find('div:last').before('<div class="clean"></div>');
			},
			errorPlacement: function(error, element) {
				error.appendTo(element.parents('.controls'));
			}

	    };

	    return custom;
	}

}();