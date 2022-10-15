// v2.0.2
jQuery(function($) {

    $('document').ready(function() {

        // change my account links to open in a new window
        setup_my_account_links();
        
        // add target blank if the view your quote button is visible
        if($('.yith_ywraq_add_item_browse_message a').length) {
            $('.yith_ywraq_add_item_browse_message a').attr('target', '_blank');
        }

        // Add the ajax to the add to my favourites button
        setup_add_to_favourites_button();

        // Adds the ability to select options for a product so a
        // quote can be generated on a list page
        setup_select_product_options_for_quote_and_list();

        // Adds the ability to copy items to other lists
        setup_copy_to_another_list();

        // Change the request a quote and projects disabled message
        change_disabled_ywraq_and_projects_button_message();
        
        // Wishlist page add all to quote button
        add_all_to_quote();
    });

    // $( ".variations_form" ).on( "woocommerce_variation_select_change", function () {
    // $( document ).on( "woocommerce_variation_select_change", ".variations_form", function () {
    //     // Fires whenever variation selects are changed
    // } );
    
    // listen for a variation change
    $( document ).on( "show_variation", ".single_variation_wrap", function ( event, variation ) {
        // Fired when the user selects all the required dropdowns / attributes
        // and a final variation is selected / shown

        // Change the link on the Add to favourites link
        check_product_id_and_update_add_to_my_favourites_button(variation.variation_id);

        // enable the request a quote button in the wishlist popup
        if($('#pp_full_res').find('.pp_inline .gl-wcwl-quote-select-option-variation').length) {
            $('#pp_full_res').find('.gl-wcwl-quote-select-option-variation .add-request-quote-button.button').removeClass('disabled');
        }

        // enable the add to quote button in the wishlist popup
        if($('#pp_full_res').find('.pp_inline .yith-ywraq-add-to-quote .add-request-quote-button').length) {
            $('#pp_full_res').find('.gl-update-wraq-list-wrapper').removeClass('disabled');
        }

        // enable the add to list button in the wishlist popup and add the variation ID
        if($('#pp_full_res').find('.pp_inline .gl-update-wcwl-list').length) {
            $('#pp_full_res').find('.gl-update-wcwl-list-wrapper').removeClass('disabled');
            $('#pp_full_res').find('.gl-update-wcwl-list').attr('data-variation_id', variation.variation_id);
        }
    } );
   
    // see if the selected variation was reset. in that case grab
    // the main product ID and see if it's in the favourites
    $( document ).on( "reset_data", function ( event ) {
        var product_id = $('.single_variation_wrap input[name="add-to-cart"]').val();
        check_product_id_and_update_add_to_my_favourites_button(product_id);
    });

    /**
     * Open certain links on my account to open in a new window
     */
    function setup_my_account_links() {
        if($('.woocommerce-MyAccount-navigation').length) {
            if($('.woocommerce-MyAccount-navigation-link--favourites').length) {
                $('.woocommerce-MyAccount-navigation-link--favourites a').attr('target', '_blank');
            }
            if($('.woocommerce-MyAccount-navigation-link--projects').length) {
                $('.woocommerce-MyAccount-navigation-link--projects a').attr('target', '_blank');
            }
            if($('.woocommerce-MyAccount-navigation-link--quotation').length) {
                $('.woocommerce-MyAccount-navigation-link--quotation a').attr('target', '_blank');
            }
        }
    }
    /**
     * Check if a product ID is in the favourites list and 
     * update the add to my favourites button
     */

    function check_product_id_and_update_add_to_my_favourites_button(product_id) {
        // check if the product is already in my favourites
        check_if_product_id_in_my_favourites(product_id);
        $('.gl_add_to_favourites').attr('href', '?add_to_wishlist=' + product_id); 
        $('.gl_add_to_favourites').attr('data-product_id', product_id); 
        // check if the product is already in my favourites
        check_if_product_id_in_my_favourites(product_id);
    }

    /**
     * Add the ajax to the add to my favourites button
     */
    function setup_add_to_favourites_button() {

        $(document).on("click",".gl_add_to_favourites",function(e) {
            e.preventDefault();
            var product_id = $(this).data('product_id');
            var this_wishlist_wrapper = $(this).parentsUntil('.yith-wcwl-add-to-wishlist').parent();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: gl_mods_init.ajaxurl,
                data: {
                    'action': 'gl_add_to_my_favourites',
                    'nonce': gl_mods_init.ajaxnonce,
                    'product_id': product_id,
                },
                beforeSend: function(){
                    block($(this_wishlist_wrapper).find('.gl-wcwl-add-to-my-favourites'));
                },
                complete: function(){
                    unblock($(this_wishlist_wrapper).find('.gl-wcwl-add-to-my-favourites'));
                },
                success: function(data) {
                    $(this_wishlist_wrapper).find('.gl-yith-wcwl-add-product-message').text(data.status_msg);
                    $(this_wishlist_wrapper).find('.gl-wcwl-add-to-my-favourites').addClass('in_my_favourites');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown);
                }
            });
        });

        // This is for clicking add to my projects while logged in
        // and for the add to my favourites button when logged out
        $( document ).on('added_to_wishlist', function(event, element){
            console.log(event);
            if(event.type == 'added_to_wishlist') {
                $('.logged_in .gl-wcwl-add-to-projects-wrapper').addClass('added_to_wishlist');
            }
        });
    }


    /**
     * check if the product is already in my favourites
     */
    function check_if_product_id_in_my_favourites(product_id) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: gl_mods_init.ajaxurl,
            data: {
                'action': 'gl_check_if_product_id_in_my_favourites',
                'nonce': gl_mods_init.ajaxnonce,
                'product_id': product_id,
            },
            beforeSend: function(){
                block($('.gl-wcwl-add-to-my-favourites'));
            },
            complete: function(){
                unblock($('.gl-wcwl-add-to-my-favourites'));
            },
            success: function(data) {
                $('.gl-yith-wcwl-add-product-message').text(data.status_msg);
                if(data.product_id_exists == 'yes') {
                    $('.gl-wcwl-add-to-my-favourites').addClass('in_my_favourites');
                } else {
                    $('.gl-wcwl-add-to-my-favourites').removeClass('in_my_favourites');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown);
                $('.gl-yith-wcwl-add-product-message').text();
            }
        });
    }

    /**
     * Set up a var and a function to handle when the wishlist
     * request a quote popup is clicked when product options
     * are needed to add to quote.
     */
    var gl_wcwl_variation_selection_id;
    var wraq_button = false;
    var update_wcwl_button = false;

    function setup_select_product_options_for_quote_and_list(){

        if( typeof $.prettyPhoto === 'undefined' ){
            return;
        }
        // The href of this link must match the ID of the div
        // we want to open
        $('.gl-wcwl-quote-select-options, .gl-wcwl-list-select-options').on('click', function(){
            var t = $(this);
           wraq_button = $(this).data('wraq_button');
           update_wcwl_button = $(this).data('update_wcwl_button');

            if(typeof wraq_button === 'undefined') {
                wraq_button = false;
            }
            if(typeof update_wcwl_button === 'undefined') {
                update_wcwl_button = false;
            }

            gl_wcwl_variation_selection_id = t.closest('[data-row-id]').data('row-id');

        }).prettyPhoto(
            {

                social_tools          : false,
                social_tools          : false,
                theme                 : 'pp_woocommerce gl-wcwl-quote-select-options-popup',
                horizontal_padding    : 20,
                opacity               : 0.8,
                deeplinking           : false,
                overlay_gallery       : false,
                default_width         : 500,
                default_height        : 100,
                allow_resize          : true,
                changepicturecallback : function(){
                    $(document).trigger( 'yith_wcwl_popup_opened', [ this ] );
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: gl_mods_init.ajaxurl,
                        data: {
                            'action': 'gl_popup_select_variation_options',
                            'nonce': gl_mods_init.ajaxnonce,
                            'product_id': gl_wcwl_variation_selection_id,
                            'wraq_button': wraq_button,
                            'update_wcwl_button': update_wcwl_button
                        },
                        complete: function(){
                            $('#pp_full_res').find('.gl-wcwl-quote-select-option-variation-loader').hide();
                            $('#pp_full_res').find('.gl-wcwl-quote-select-option-variation .add-request-quote-button.button').addClass('disabled');
                            $('#pp_full_res').find('.gl-wcwl-quote-select-option-variation .yith_ywraq_add_item_browse_message a').attr('target', '_blank');
                        },
                        success: function(data) {
                            $('#pp_full_res').find('.pp_inline .gl-wcwl-quote-select-option-variation').html(data.html);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown);
                        }
                    });

                },
        });

        // close pretty photo lightbox when clicking view quote
        $( document ).on('click', '#pp_full_res .gl-wcwl-quote-select-option-variation .yith_ywraq_add_item_browse_message a', function () {
            $.prettyPhoto.close();
        });
        
        /**
         * Check for a quote add, and if this was in the wishlist popup
         * then hide some options
         */
        $( document ).on( "yith_wwraq_added_successfully", function (response, prod_id) {
            if(response.type == 'yith_wwraq_added_successfully') {
                // $('.yith_ywraq_add_item_browse_message a').attr('target', '_blank');
                if($('#pp_full_res').find('.pp_inline .gl-wcwl-quote-select-option-variation').length) {
                    $('#pp_full_res').find('.variations_form').slideUp();
                    $('#pp_full_res .pp_inline .gl-wcwl-quote-select-option-variation .yith-ywraq-add-to-quote').addClass('view-quote');
                    $('.pp_content_container').find('.pp_content').css('height', 'auto');
                }
            }
        });
        
        /**
         * Highlight the add product options button when clicking the disabled
         * request a quote button
         */
         $( document ).on('click', '.gl-wcwl-quote-select-options-wrapper.disabled .yith-ywraq-add-button', function (e) {
            e.preventDefault();
            var wishlist_row = $(this).parentsUntil('td').parent();
            var add_product_options = $(wishlist_row).find('.gl-wcwl-quote-select-options-wrapper')

            $(add_product_options).addClass('highlight_button');
            setTimeout(function(){
                $(add_product_options).removeClass('highlight_button');

            }, 6000);

         });

        /**
         * Add an item with a variation ID to the list and remove the original product
         */
        $( document ).on('click', '.gl-update-wcwl-list', function (e) {
            e.preventDefault();

            var product_id = $(this).data('product_id');
            var variation_id = $(this).data('variation_id');
            var popup_window = $('.gl-wcwl-quote-select-options-popup'),
            table = $('.cart.wishlist_table'),
            wishlist_token = table.data('token');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: gl_mods_init.ajaxurl,
                data: {
                    'action': 'gl_update_wcwl_list_with_variation',
                    'nonce': gl_mods_init.ajaxnonce,
                    'product_id': product_id,
                    'variation_id': variation_id,
                    'wishlist_token': wishlist_token
                },
                beforeSend: function(){
                    block($(popup_window).find('.gl-wcwl-quote-select-option-variation'));
                },
                complete: function(){
                    $('#pp_full_res').find('.gl-wcwl-quote-select-option-variation-loader').hide();
                    unblock($(popup_window).find('.gl-wcwl-quote-select-option-variation'));
                },
                success: function(data) {
                    if(data.already_in_list) {
                        $('#pp_full_res').find('.pp_inline .gl-wcwl-select-option-variation-message').html(data.html);

                    } else {
                        $('#pp_full_res').find('.pp_inline .gl-wcwl-quote-select-option-variation').html('<div style="text-align: center;">' + data.html + '</div>');
                        $('.pp_content_container').find('.pp_content').css('height', 'auto');
                        setTimeout(function () {
                            location.reload(true);
                          }, 2000);
                    }

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown);
                }
            });

        });
    }

    /**
     * Handles the items related to copying to another list
     */

    function setup_copy_to_another_list() {

        if( typeof $.prettyPhoto === 'undefined' ){
            return;
        }
        // The href of this link must match the ID of the div
        // we want to open
        $('.gl-open-popup-copy-to-another-wishlist-button').on('click', function(){
            
            var t = $(this),
            popup = $('#gl_copy_to_another_wishlist'),
            form = popup.find('form'),
            row_id = form.find( '.row-id' ),
            id = t.closest('[data-row-id]').data('row-id');

            if( row_id.length ){
                row_id.remove();
            }

            form.append( '<input type="hidden" name="row_id" class="row-id" value="' + id + '"/>' );

        }).prettyPhoto(
            {

                social_tools          : false,
                social_tools          : false,
                theme                 : 'gl_copy_to_another_list',
                horizontal_padding    : 20,
                opacity               : 0.8,
                deeplinking           : false,
                overlay_gallery       : false,
                default_width         : 500,
                default_height        : 100,
                allow_resize          : true,
                changepicturecallback : function(){
                    
                    $(document).trigger( 'yith_wcwl_popup_opened', [ this ] );

                },
        });

        $(document).on('click', '.gl-copy-to-another-wishlist-button', function(e){
            e.preventDefault();
            var 
            popup_window = $('.gl_copy_to_another_list'),
            popup_content = $('#gl_copy_to_another_wishlist'),  
            form = popup_content.find('form'),
            row_id = form.find( '.row-id' ).val(),
            table = popup_content.parent().find('.cart.wishlist_table'),
            wishlist_token = table.data('token'),
            // item_id = table.find( '[data-row-id]').data('row-id'),
            to_token = $('.pp_content .yith-wcwl-popup-content .change-to-wishlist').first().val();
            
            // originally copied this from YITH but I don't think we
            // need to worry about refreshing fragments since we're not
            // removing from this list or adding to it.
            // var options = {},
            // fragments = $('.wishlist-fragment');

            // if ( fragments.length ) {
            //     fragments.each( function () {
            //         var t = $( this ),
            //             id = t.attr( 'class' ).split( ' ' ).filter( ( val ) => {
            //                 return val.length && val !== 'exists';
            //             } ).join( yith_wcwl_l10n.fragments_index_glue );
    
            //         options[ id ] = t.data( 'fragment-options' );
            //     } );
            // }
            $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: gl_mods_init.ajaxurl,
                    data: {
                        'action': 'gl_copy_to_another_list',
                        'nonce': gl_mods_init.ajaxnonce,
                        wishlist_token            : wishlist_token,
                        destination_wishlist_token: to_token,
                        item_id                   : row_id,
                        // fragments                 : options
                    },
                    beforeSend: function(){
                        block($(popup_window).find('.gl-copy-to-another-wishlist-popup'));
                    },
                    complete: function(){
                        unblock($(popup_window).find('.gl-copy-to-another-wishlist-popup'));
                    },
                    success: function(data) {
                        var copy_to_list_message = $("#pp_full_res .yith-wcwl-popup-footer").find("#copy_to_list_message");
                        if (!copy_to_list_message.length) {
                            $("#pp_full_res .yith-wcwl-popup-footer").append('<div id="copy_to_list_message"></div>');
                        }
                        $('#pp_full_res .yith-wcwl-popup-footer').find("#copy_to_list_message").html(data.message);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown);
                    }
                });
        });
    }

    /**
     * Remove the window alert popup when the ywraq button is disabled
     */
    function change_disabled_ywraq_and_projects_button_message() {

        $(document).on('click', '.gl-yith-wcwl-wrapper.disable-project-list .gl-wcwl-add-to-projects-wrapper .yith-wcwl-add-button, .gl-update-wraq-list-wrapper.disabled, .gl-update-wcwl-list-wrapper.disabled', function (event) {
            event.stopPropagation();
            // select the first drop down
            if($(this).parentsUntil('.qode-single-product-summary').parent().length) {
                var product_summary = $(this).parentsUntil('.qode-single-product-summary').parent();
            } else if($(this).parent().hasClass('single-product')) {
                var product_summary = $(this).parent();
            }
            // var product_summary = $(this).parentsUntil('.qode-single-product-summary').parent();
            var product_summary = $(this).parentsUntil('.summary').parent();
            var form = $(product_summary).find('.variations_form');
            form.find('.variations').addClass('variation_highlight');
            var selections = $(form).find('select');
            selections.first().focus();
            // var gl_yith_wcwl = $(product_summary).find('.gl-yith-wcwl-wrapper');
            // remove it if they choose an option
            $( document ).on( "show_variation", ".single_variation_wrap", function ( event, variation ) {
                // remove a variation highlight 
                form.find('.variations').removeClass('variation_highlight');
            });

            // automatically remove it after a few seconds
            setTimeout(function(){
                form.find('.variations').removeClass('variation_highlight');
            },10000);
            return false;
        });

        $( document ).on( "show_variation", ".single_variation_wrap", function ( event, variation ) {
            // enable the project button
            $('.summary .gl-yith-wcwl-wrapper').removeClass('disable-project-list');
        });

        // add the project disabled if the variations are cleared
        $( document ).on( "reset_data", function ( event ) {
            // disable the add to projects button
            $('.summary .gl-yith-wcwl-wrapper').addClass('disable-project-list');
        });
    }
    /**
     * Block item if possible
     *
     * @param item jQuery object
     * @return void
     * @since 1.0.0
     */
    function block( item ) {
        if( typeof $.fn.block !== 'undefined' ) {
            item.fadeTo('400', '0.6').block( {
                message: null,
                overlayCSS : {
                    background    : 'transparent url(' + yith_wcwl_l10n.ajax_loader_url + ') no-repeat center',
                    backgroundSize: '40px 40px',
                    opacity       : 1
                }
            } );
        }
    }
    
    /**
     * Unblock item if possible
     *
     * @param item jQuery object
     * @return void
     * @since 1.0.0
     */
    function unblock( item ) {
        if( typeof $.fn.unblock !== 'undefined' ) {
            item.stop(true).css('opacity', '1').unblock();
        }
    }

    /** 
     * Add all items to quote from a wish list page
     */
    function add_all_to_quote() {
        if($('.wishlist_table').length) {
            var message = '';
            $('.gl-add-all-to-quotation').on('click', function(e) {
                e.preventDefault();
                
                var products_needing_options = $('.yith-ywraq-add-to-quote.gl-wcwl-quote-select-options-wrapper');
                if($(products_needing_options).length) {
                    message = '<div style="text-align: center"><h1 style="font-size: 24px; margin-bottom:30px;">Please select additional options</h1><p>Some of the items in your list have options that need to be selected before adding to a quote.</p> <p>Please add these options before trying again.</p>';

                    // highlight each row
                    var add_product_options = $('.gl-wcwl-quote-select-options-wrapper');
                    $(add_product_options).addClass('highlight_button');

                } else {
                    var add_to_quote_buttons = $('.yith-ywraq-add-button:not(.hide) .add-request-quote-button');

                    var ajax_add_to_quote_success = false;
                   (async function loop () {
                        for(var i=0;i<add_to_quote_buttons.length;i++){
                            const result = await ajax_add_to_quote($(add_to_quote_buttons[i]));
                            if(result.result == 'true') {
                                ajax_add_to_quote_success = true;
                            } else {
                                return false;
                            }
                        }
                        if(ajax_add_to_quote_success == true) {
                            message = '<div style="text-align: center"><h1 style="font-size: 24px; margin-bottom:30px;">Successfully added to Quote</h1><p><a href="/request-quote/" class="button">View Your Quote</a></p></div>';
                            $('#pp_full_res').find('.pp_inline').html( message );
                        } else {
                            message = '<div style="text-align: center"><h1 style="font-size: 24px; margin-bottom:30px;">An error occurred</h1><p>Please try adding your items you the quote again. You may need to refresh the page.</p></div>';
                            $('#pp_full_res').find('.pp_inline').html( message );
                        }
                    })();

                }

            }).prettyPhoto(
                        {
            
                            social_tools          : false,
                            social_tools          : false,
                            theme                 : 'pp_woocommerce gl-wcwl-quote-info-only',
                            horizontal_padding    : 20,
                            opacity               : 0.8,
                            deeplinking           : false,
                            overlay_gallery       : false,
                            default_width         : 500,
                            default_height        : 100,
                            allow_resize          : true,
                            changepicturecallback: function(){
                                $(document).trigger( 'yith_wcwl_popup_opened', [ this ] );

                                if(message == '') {
                                    message = '<div style="text-align: center"><h1 style="font-size: 24px; margin-bottom:30px;">Adding to Quote</h1><p>Please wait...</p></div>';
                                }

                                $('#pp_full_res').find('.pp_inline').html(message);
                            }
                        });
            // get all the add to quote buttons
        }
    }

    /**
     * Code to call ajax functions to add items to the quote
     * on the list page. Uses modified code from frontend.js
     * in the YITH Request a Quote plugin.
     */
    async function ajax_add_to_quote(button) {

        var $t = $(button),
        $t_wrap = $t.closest('.yith-ywraq-add-to-quote'),
        add_to_cart_info = 'ac',
        $cart_form = '',
        ajax_loader = (typeof ywraq_frontend !== 'undefined') ? ywraq_frontend.block_loader : false;

        var $row_wrap = $t.parentsUntil('tr.ui-sortable-handle').parent();

         // set alerts if out of stock or disabled
         if ($t.hasClass('outofstock')) {
            window.alert(ywraq_frontend.i18n_out_of_stock);
        } else if ($t.hasClass('disabled')) {
            window.alert(ywraq_frontend.i18n_choose_a_variation);
        }

        // find the form
        if ($t.closest('.cart').length) {
            $cart_form = $t.closest('.cart');
        } else if ($t_wrap.siblings('.cart').first().length) {
            $cart_form = $t_wrap.siblings('.cart').first();
        } else if ($('.composite_form').length) {
            $cart_form = $('.composite_form');
        } else if ($t.closest('ul.products').length > 0) {
            $cart_form = $t.closest('ul.products');
        } else {
            $cart_form = $('form.cart:not(.in_loop)').first(); // not(in_loop) for color and label
        }
        var prod_id = $t.data('product_id');

        add_to_cart_info = $cart_form.serializefiles();

        add_to_cart_info.append('context', 'frontend');
        add_to_cart_info.append('action', 'yith_ywraq_action');
        add_to_cart_info.append('ywraq_action', 'add_item');
        add_to_cart_info.append('product_id', $t.data('product_id'));
        add_to_cart_info.append('wp_nonce', $t.data('wp_nonce'));
        add_to_cart_info.append('yith-add-to-cart', $t.data('product_id'));

        var quantity = $row_wrap.find('.gl-wishlist-qty input').val();

        if (quantity > 0) {
            add_to_cart_info.append('quantity', quantity);
        }

        $(document).trigger('yith_ywraq_action_before');

        // let promise = new Promise(function(resolve, reject) {

        return  xhr = $.ajax({
                type: 'POST',
                url: ywraq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'yith_ywraq_action'),
                dataType: 'json',
                data: add_to_cart_info,
                contentType: false,
                processData: false,
                beforeSend: function () {
                $t.after(' <img src="' + ajax_loader + '" class="ywraq-loader" >');
                },
                complete: function () {
                $t.next().remove();
                },
        
                success: function (response) {
                if (response.result == 'true' || response.result == 'exists') {
        
                    if (ywraq_frontend.go_to_the_list == 'yes') {
                    window.location.href = response.rqa_url;
                    } else {
                    $('.yith_ywraq_add_item_response-' + prod_id).hide().addClass('hide').html('');
                    $('.yith_ywraq_add_item_product-response-' + prod_id).show().removeClass('hide').html(response.message);
                    $('.yith_ywraq_add_item_browse-list-' + prod_id).show().removeClass('hide');
                    $t.parent().hide().removeClass('show').addClass('addedd');
                    $('.add-to-quote-' + prod_id).attr('data-variation', response.variations);
                    }
                    console.log('Successfully added to quote.');
                    $(document).trigger('yith_wwraq_added_successfully', [response, prod_id]);
        
                } else if (response.result == 'false') {
                    $('.yith_ywraq_add_item_response-' + prod_id).show().removeClass('hide').html(response.message);
                    console.log('Error adding to quote.');
                    $(document).trigger('yith_wwraq_error_while_adding');
                }
                xhr = false;
                }
            });

        // });
        // return promise;
    }

});