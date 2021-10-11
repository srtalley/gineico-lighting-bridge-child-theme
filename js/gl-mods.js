// v1.9
jQuery(function($) {

    $('document').ready(function() {

        // Add the ajax to the add to my favourites button
        setup_add_to_favourites_button();

        // Adds the ability to select options for a product so a
        // quote can be generated on a list page
        setup_select_product_options_for_quote_and_list();

        // Allow replacing the item in the list with one with product options
        // setup_select_product_options_for_list();

        // Adds the ability to copy items to other lists
        setup_copy_to_another_list();

        // Change the request a quote and projects disabled message
        change_disabled_ywraq_and_projects_button_message();
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

        $( document ).on('added_to_wishlist', function(event, element){
            console.log(event);
            if(event.type == 'added_to_wishlist') {
                $('.gl-wcwl-add-to-projects-wrapper').addClass('added_to_wishlist');
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
                    console.log(wraq_button);
                    console.log(update_wcwl_button);
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
                if($('#pp_full_res').find('.pp_inline .gl-wcwl-quote-select-option-variation').length) {
                    $('#pp_full_res').find('.variations_form').slideUp();
                    $('#pp_full_res .pp_inline .gl-wcwl-quote-select-option-variation .yith-ywraq-add-to-quote').addClass('view-quote');
                    $('.pp_content_container').find('.pp_content').css('height', 'auto');
                }
            }
        });

        /**
         * Add an item with a variation ID to the list
         */
        $( document ).on('click', '.gl-update-wcwl-list', function (e) {
            e.preventDefault();

            var product_id = $(this).data('product_id');
            var variation_id = $(this).data('variation_id');
            var popup_window = $('.gl-wcwl-quote-select-options-popup'),
            // form = popup_content.find('form'),
            // row_id = form.find( '.row-id' ).val(),
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

                    // $('#pp_full_res').find('.gl-wcwl-quote-select-option-variation .add-request-quote-button.button').addClass('disabled');
                    // $('#pp_full_res').find('.gl-wcwl-quote-select-option-variation .yith_ywraq_add_item_browse_message a').attr('target', '_blank');
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

    // var gl_wcwl_variation_selection_id;
    function setup_select_product_options_for_list(){

        if( typeof $.prettyPhoto === 'undefined' ){
            return;
        }
        // The href of this link must match the ID of the div
        // we want to open
        // $('.gl-wcwl-list-select-options').on('click', function(){
        //     console.log('cliccc');
        //     var t = $(this);

        //     gl_wcwl_variation_selection_id = t.closest('[data-row-id]').data('row-id');

        // }).prettyPhoto(
        //     {

        //         social_tools          : false,
        //         social_tools          : false,
        //         theme                 : 'pp_woocommerce gl-wcwl-quote-select-options-popup',
        //         horizontal_padding    : 20,
        //         opacity               : 0.8,
        //         deeplinking           : false,
        //         overlay_gallery       : false,
        //         default_width         : 500,
        //         default_height        : 100,
        //         allow_resize          : true,
        //         changepicturecallback : function(){
                    
        //             $(document).trigger( 'yith_wcwl_popup_opened', [ this ] );
        //             $.ajax({
        //                 type: 'POST',
        //                 dataType: 'json',
        //                 url: gl_mods_init.ajaxurl,
        //                 data: {
        //                     'action': 'gl_popup_select_variation_options',
        //                     'nonce': gl_mods_init.ajaxnonce,
        //                     'product_id': gl_wcwl_variation_selection_id,
        //                     'wraq_button': false,
        //                     'update_wcwl_button': true
        //                 },
        //                 complete: function(){
                            
        //                     $('#pp_full_res').find('.gl-wcwl-quote-select-option-variation-loader').hide();
        //                     $('#pp_full_res').find('.gl-wcwl-quote-select-option-variation .add-request-quote-button.button').addClass('disabled');
        //                     $('#pp_full_res').find('.gl-wcwl-quote-select-option-variation .yith_ywraq_add_item_browse_message a').attr('target', '_blank');
        //                 },
        //                 success: function(data) {
        //                     $('#pp_full_res').find('.pp_inline .gl-wcwl-quote-select-option-variation').html(data.html);

        //                 },
        //                 error: function(jqXHR, textStatus, errorThrown) {
        //                     console.log(jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown);
        //                 }
        //             });

        //         },
        // });
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

        $(document).on('click', '.woocommerce-variation-add-to-cart-disabled + .yith-ywraq-add-to-quote, .gl-yith-wcwl-wrapper.disable-project-list .gl-wcwl-add-to-projects-wrapper .yith-wcwl-add-button, .gl-update-wraq-list-wrapper.disabled, .gl-update-wcwl-list-wrapper.disabled', function (event) {
            event.stopPropagation();
            // select the first drop down
            if($(this).parentsUntil('.qode-single-product-summary').parent().length) {
                console.log('this');
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
            $('.gl-yith-wcwl-wrapper').removeClass('disable-project-list');
        });

        // add the project disabled if the variations are cleared
        $( document ).on( "reset_data", function ( event ) {
            // disable the add to projects button
            $('.gl-yith-wcwl-wrapper').addClass('disable-project-list');
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
});