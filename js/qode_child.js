//v2.0
/**
 * This is the original JS file for the theme which has had a few updates in 2021.
 */
var add_to_quote_buttons_check_timer = null;

$j(document).ready(function() {
	"use strict";

    $j('select.colour-select-2').select2({
	    placeholder: "Select Colours"
	});
    if($j('.qode_carousels .slides').length){
		$j('.qode_carousels').each(function(){
			var thisItem = $j(this);
			// thisItem.hide();
			setTimeout(function(){
				thisItem.find('.slides').trigger('destroy');
	        	var numberOfVisibleItems = 6;
	        	if(typeof thisItem.data('number-of-visible-items') !== 'undefined' && thisItem.data('number-of-visible-items') !== '') {
	        		if(thisItem.data('number-of-visible-items') === 4) {
	        			numberOfVisibleItems = 4;
	        		} else if (thisItem.data('number-of-visible-items') === 5) {
	        			numberOfVisibleItems = 5;
	        		}
	        	}
	        	var itemWidth = (thisItem.parents('.grid_section').length == 1) ? 170 : 315;
	        	var maxItems = 6;
	        	if(numberOfVisibleItems === 4) {
	        		itemWidth = (thisItem.parents('.grid_section').length == 1) ? 255 : 472;
	        		maxItems = 4;
	        	} else if (numberOfVisibleItems === 5) {
	        		itemWidth = (thisItem.parents('.grid_section').length == 1) ? 204 : 378;
	        		maxItems = 5;
	        	}

	            thisItem.find('.slides').carouFredSel({
	                circular: true,
	                responsive: true,
	                scroll : {
	                    items           : 1,
	                    duration        : 350,
	                    pauseOnHover    : false
	                },
	                items: {
	                    width: itemWidth,
	                    visible: {
	                        min: 1,
	                        max: maxItems
	                    }
	                },
	                auto: true,
	                mousewheel: false,
	                swipe: {
	                    onMouse: true,
	                    onTouch: true
	                }

	            }).animate({'opacity': 1},1000);

		        thisItem.find('.caroufredsel_wrapper').css({'height' : (thisItem.find('li.item').outerHeight()) + 'px'});
			}, 1000);
		});
    }

    // $j('.woocommerce-account .woocommerce-MyAccount-content .woocommerce-Button[name="save_account_details"], .woocommerce-account .woocommerce form.register .woocommerce-Button[name="register"]').click(function(event) {
    // 	if($j('#account_first_name').val().length <= 0 ||
    // 		$j('#account_last_name').val().length <= 0 ||
    // 		$j('#account_company').val().length <= 0) {
	//     	alert('Please fill all the required fields.');
	//     	return false;
    // 	}
    // });

    setTimeout(function(){
    	$j('.woocommerce .product .images a.mz-thumb').prepend('<span class="light-box light-box icon_plus_alt2"></span>');
    	$j('.woocommerce .product .images a.mz-thumb .light-box').click(function(event) {
    		var parent_image_url = $j(this).parent().attr('href');
    		if($j('.product-image-light-box[href="' + parent_image_url + '"]').length > 0) {
    			$j('.product-image-light-box[href="' + parent_image_url + '"]').click();
    		}
    	});
	}, 1000);

	if(!$j('.yith-ywraq-add-button').is(':visible')) {
		$j('.quantity.buttons_added').hide();
	}

	$j('.single-product.woocommerce .add-request-quote-button').click(function(event) {
		add_to_quote_buttons_check_timer = setInterval(add_to_quote_buttons_checker, 250);
	});

	$j('.gineico-advanced-search .gineico-btn.reset').click(function(event) {
		$j('.gineico-advanced-search input[type="text"]').val('');
		$j('.gineico-advanced-search input').removeAttr('checked');
		var uri = window.location.toString();
		if (uri.indexOf("?") > 0) {
			var clean_uri = uri.substring(0, uri.indexOf("?"));
			$('body').addClass('hide-search-options');
		    window.history.replaceState({}, document.title, clean_uri);
		}
	});

	if($j('#search-results').length > 0) {
		var uri = window.location.toString();
		if (uri.indexOf("#") < 0) {
			var uri = uri + "#search-results";
			window.history.replaceState({}, document.title, uri);
		}
		$j('html, body').animate({
	        scrollTop: $j("#search-results").offset().top
	    }, 500);
	}

	$j('.qode_search_close .search-options, .widget #searchform .search-options').click(function(event) {
		event.stopPropagation();
		event.preventDefault();
		$j('#float-gineico-advanced-filter').fadeIn('slow');
		$j('body').addClass('float-gineico-advanced-filter-opened');
	});

	$j('#float-gineico-advanced-filter, .float-gineico-advanced-filter-close').click(function(event) {
		event.stopPropagation();
		if($j(event.target).is('#float-gineico-advanced-filter') || $j(event.target).is('.float-gineico-advanced-filter-close')) {
			$j('#float-gineico-advanced-filter').fadeOut('slow');
			$j('body').removeClass('float-gineico-advanced-filter-opened');
		}
	});

	$j('#float-gineico-advanced-filter .gineico-advanced-search').on('mousewheel DOMMouseScroll', function(event) {
		var scrollTime = .5;
		var scrollDistance = 170;
		var delta = event.originalEvent.wheelDelta/120 || -event.originalEvent.detail/3;
		var scrollTop = $j(this).scrollTop();
		var finalScroll = scrollTop - parseInt(delta*scrollDistance);
		TweenMax.to($j(this), scrollTime, {
			scrollTo : { y: finalScroll, autoKill:true },
			ease: Power1.easeOut,
			overwrite: 5              
		});
	});

	$j('body').delegate('ul.select2-results__options', 'mousewheel DOMMouseScroll', function(event) {
		var scrollTime = .5;
		var scrollDistance = 170;
		var delta = event.originalEvent.wheelDelta/120 || -event.originalEvent.detail/3;
		var scrollTop = $j(this).scrollTop();
		var finalScroll = scrollTop - parseInt(delta*scrollDistance);
		TweenMax.to($j(this), scrollTime, {
			scrollTo : { y: finalScroll, autoKill:true },
			ease: Power1.easeOut,
			overwrite: 5              
		});
	});

	/** 
	 * Move the error messages on the request a quote page
	 */
	var send_req_btn_container = $j('#yith-ywraq-default-form .raq-send-request').parent();
	if(send_req_btn_container.length > 0) {
		var fix_req_error_message_timer = null;
		send_req_btn_container.click(function(event) {
			fix_req_error_message_timer = setInterval(function() {
					var send_req_error_message = $j('#yith-ywraq-default-form .woocommerce-error.woocommerce-message');
					if(send_req_error_message.length > 0 && !send_req_error_message.hasClass('moved')) {
						send_req_btn_container.after(send_req_error_message);
						send_req_error_message.addClass('moved');
						// send_req_error_message.prepend('<span class="msg-title">Your request was not send<br>The following information is needed:</span><br>');
						new_scroll_to_notices();
						clearInterval(fix_req_error_message_timer);
					}
				}, 10);
		});
	}
	/**
	 * Show the login form on the request a quote page when clicked
	 */
	$j(document).on('click', '#yith-ywraq-default-form .woocommerce-error .showlogin', function (e) {
		e.preventDefault();
		$j('.woocomerce-form.woocommerce-form-login').slideDown();
		var header_height = $j('.header_top_bottom_holder').first().height();
		$j([document.documentElement, document.body]).animate({
			scrollTop: $j(".woocomerce-form.woocommerce-form-login").offset().top - header_height - 300
		}, 500);
		$j('.woocomerce-form.woocommerce-form-login .form-row.form-row-first input').focus();
	});

	$j('.show-top-header').click(function(event) {
		var header_top = $j(this).parents('.header_top');
		if(header_top.hasClass('opened')) {
			header_top.removeClass('opened');
			header_top.height(35);
			$j(this).removeClass('close');
		} else {
			header_top.addClass('opened');
			header_top.height(170);
			$j(this).addClass('close');
		}
	});

	if($j('.woocommerce-account .woocommerce-MyAccount-navigation').length > 0) {
		$j('.woocommerce-account .woocommerce-MyAccount-navigation').prepend('<span class="myaccount_mobile_menu_button"><i class="qode_icon_font_awesome fa fa-bars "></i></span>');
		var active_link_text = $j('.woocommerce-account .woocommerce-MyAccount-navigation ul li.is-active a').text();
		if(active_link_text.length > 0) {
			$j('.qode-child-breadcrumb.breadcrumb .current').append(' <small>(' + active_link_text + ')</small>');
		}
		$j('body').delegate('.woocommerce-MyAccount-navigation .myaccount_mobile_menu_button', 'click', function(event) {
			if($j('.woocommerce-account .woocommerce-MyAccount-navigation ul').hasClass('opened')) {
				$j('.woocommerce-account .woocommerce-MyAccount-navigation ul').slideUp('fast').removeClass('opened');
			} else {
				$j('.woocommerce-account .woocommerce-MyAccount-navigation ul').slideDown('fast').addClass('opened');
			}
		});
	}
});

function new_scroll_to_notices() {
    var scrollElement = $j('.woocommerce-error, .woocommerce-message'),
        isSmoothScrollSupported = 'scrollBehavior' in document.documentElement.style;

    if (!scrollElement.length) {
        scrollElement = ywraq_default_form;
    }

    if (scrollElement.length) {
		var header_height = $j('.header_top_bottom_holder').first().height();
		$j('html, body').stop().animate({
			scrollTop: (scrollElement.offset().top - header_height - 100)
		}, 1000);
    }
}


function add_to_quote_buttons_checker() {
	if(!$j('.yith-ywraq-add-button').is(':visible')) {
		$j('.quantity.buttons_added').hide();
		clearInterval(add_to_quote_buttons_check_timer);
	}
}