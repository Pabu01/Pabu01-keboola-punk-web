/*!
 *
 *  Project:  Keboola
 *  Author:   Petr Urbanek - www.r4ms3s.cz
 *  Twitter:  @r4ms3scz
 *
 * @param {Object} window, document, undefined
 *
 */

 (function(window, document, undefined) {

    // Defaults
    // =====================================

    var r4 = window.r4 = {
        utils : {},
        cache : {}
    };


    // Methods
    // =====================================

    r4.utils.init = function() {
        r4.cache.window                = $(window);
        r4.cache.document              = $(document);
        r4.cache.html                  = $('html');
        r4.cache.body                  = $('body');

        r4.cache.header                = r4.cache.body.find('header');


        // MEDIA QUERIES
        r4.mobile = false;
        r4.bounds = [
            [1000, 10000, function() {
                mobile = false;
            }],
            [0, 1000, function() {
                mobile = true;
            }]
        ];
        r4.lastBound = -1;

    };


    // RESIZE
    r4.utils.resize = function(start){
        r4.utils.calcBounds(r4.cache.window, r4.bounds, r4.lastBound, start);
    };


    // RESIZE METHODS
    r4.utils.calcBounds = function(ths, bounds, lastBound, start) {
        var w = ths.width(),
            h = ths.height();
        for(var i = 0, j = bounds.length; i < j; i++) {
            if(w > bounds[i][0] && w < bounds[i][1] && lastBound !== i) {
                bounds[i][2]();
                lastBound = i;
            }
        }

    };


    // NAV BOX HEIGHT
    r4.utils.navsize = function() {

        var match = function(){
            var newHeight = 0;
            $('.box-list > li > article, .partners-list > li > article').each(function(){
                if ($(this).innerHeight() > newHeight) {
                    newHeight = $(this).outerHeight();
                }
            });
            $('.box-list > li > article, .partners-list > li > article').css('height', newHeight);
        };
        var unmatch = function(){
            $('.box-list > li > article, .partners-list > li > article').css('height', 'auto');
        };


        r4.cache.window.smartresize(function(){
            w = r4.cache.window.width();
            match();

            if (w <= 700) {
                unmatch();
            }
        });

        match();
    }


    r4.utils.domLoad = function() {

        r4.utils.resize();
        r4.cache.window.smartresize(function() {
            r4.utils.resize();
        });

        r4.utils.navsize();


        $('label.item-input, label.item-textarea').each(function(){
            var el = $(this);

            el.find('input, textarea').on('focus', function(e){

                el.addClass('act');
            });
        });

    };


    // Initialize Events
    // =====================================

    r4.utils.init();

    jQuery(function($) {
        r4.utils.domLoad();
    });


})(window, document);

// debouncing function from John Hann
// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
(function($,sr) {

    var debounce = function (func, threshold, execAsap) {
        var timeout;

        return function debounced () {
            var obj = this, args = arguments;
            function delayed () {
                if (!execAsap) {
                    func.apply(obj, args);
                }
                timeout = null;
            }

            if (timeout) {
                clearTimeout(timeout);
            } else if (execAsap) {
                func.apply(obj, args);
            }
            timeout = setTimeout(delayed, threshold || 100);
        };
    };

    jQuery.fn[sr] = function(fn){ return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');

$(function() {

	// contact form
	$("#contactForm").validate({
		rules: {
			"item-name": "required",
			"item-email": {
				required: true,
				email: true
			}
		},
		messages: {
			"item-name": "Please enter your name",
			"item-email": "Please enter a valid email address"
		}
	});

	$("#item-name").focus(function() {
		$("#crumbField").val(2);
	});

	// mobile menu
	$('.mobile-btn').click(function() {
		$('.mobile-nav').toggle();
		return false;
	});
});
