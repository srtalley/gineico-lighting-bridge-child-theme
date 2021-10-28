<?php
/**
 * WooCommerce GL My Favourites and My Projects Template
 */

$gl_customers_wishlist_table = new GL_Customers_Wishlist_Table;
$gl_customers_wishlist_table->prepare_items();

// Get membership filter
$membership_filter = (int) filter_input( INPUT_GET, 'product', FILTER_SANITIZE_NUMBER_INT );
?>

<div class="wrap">
    <h2>My Favourites and My Projects Lists</h2>

    <div class="gl-customers-table-outer">
        <?php $gl_customers_wishlist_table->display(); ?>
    </div>
    
</div>
