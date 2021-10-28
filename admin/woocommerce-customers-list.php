<?php
/**
 * WooCommerce GL Customers Template
 */

$gl_customers_table = new GL_Customers_Table;
$gl_customers_table->prepare_items();

// Get membership filter
$membership_filter = (int) filter_input( INPUT_GET, 'product', FILTER_SANITIZE_NUMBER_INT );
?>

<div class="wrap">
    <h2>Gineico Lighting Customers</h2>

    <div class="gl-customers-table-outer">
        <?php $gl_customers_table->display(); ?>
    </div>
    
</div>
