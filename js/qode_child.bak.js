var add_to_quote_buttons_check_timer = null;

$j(document).ready(function() {
	"use strict";

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

    $j('.woocommerce-account .woocommerce-MyAccount-content .woocommerce-Button[name="save_account_details"], .woocommerce-account .woocommerce form.register .woocommerce-Button[name="register"]').click(function(event) {
    	if($j('#account_first_name').val().length <= 0 ||
    		$j('#account_last_name').val().length <= 0 ||
    		$j('#account_company').val().length <= 0) {
	    	alert('Please fill all the required fields.');
	    	return false;
    	}
    });

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
		    window.history.replaceState({}, document.title, clean_uri);
		}
	});

	if($j('#search-results').length > 0) {
		var uri = window.location.toString() + "#search-results";
		window.history.replaceState({}, document.title, uri);
	}
});

function add_to_quote_buttons_checker() {
	if(!$j('.yith-ywraq-add-button').is(':visible')) {
		$j('.quantity.buttons_added').hide();
		clearInterval(add_to_quote_buttons_check_timer);
	}
}