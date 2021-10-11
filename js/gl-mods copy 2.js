// v1.0
jQuery(function($) {

    $('document').ready(function() {

        setup_add_to_favourites_button();

        setup_list_select_options();
    });

    // $( ".variations_form" ).on( "woocommerce_variation_select_change", function () {
    $( document ).on( "woocommerce_variation_select_change", ".variations_form", function () {
        // Fires whenever variation selects are changed
    } );
    
    // $( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
    $( document ).on( "show_variation", ".single_variation_wrap", function ( event, variation ) {
        console.log(event);
        // Fired when the user selects all the required dropdowns / attributes
        // and a final variation is selected / shown
        // Change the link on the Add to favourites link
        $('.gl_add_to_favourites').attr('href', '?add_to_wishlist=' + variation.variation_id); 
        $('.gl_add_to_favourites').attr('data-product_id', variation.variation_id); 
        // check if the product is already in my favourites
        check_if_product_id_in_my_favourites(variation.variation_id);
    } );
   

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

    var gl_wcwl_variation_selection_id;
    function setup_list_select_options(){

        if( typeof $.prettyPhoto === 'undefined' ){
            return;
        }
        $('.gl-wcwl-quote-select-options').on('click', function(){
            
            var t = $(this);

            gl_wcwl_variation_selection_id = t.closest('[data-row-id]').data('row-id');

        }).prettyPhoto(
            {

                social_tools          : false,
                social_tools          : false,
                theme                 : 'pp_woocommerce',
                horizontal_padding    : 20,
                opacity               : 0.8,
                deeplinking           : false,
                overlay_gallery       : false,
                default_width         : 500,
                changepicturecallback : function(){
                    
                    $(document).trigger( 'yith_wcwl_popup_opened', [ this ] );
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: gl_mods_init.ajaxurl,
                        data: {
                            'action': 'gl_select_variation_options',
                            'nonce': gl_mods_init.ajaxnonce,
                            'product_id': gl_wcwl_variation_selection_id,
                        },
                        beforeSend: function(){
                            block($('#pp_full_res').find('.pp_inline'));
                        },
                        complete: function(){
                            unblock($('#pp_full_res').find('.pp_inline'));
                            $('#pp_full_res').find('.gl-wcwl-quote-select-option-variation-ywraq-buttons').fadeIn();
                        },
                        success: function(data) {
                            console.log(data)
                            $('#pp_full_res').find('.pp_inline .gl-wcwl-quote-select-option-variation').html(data.html);
                       
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown);
                        }
                    });

                },
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