<?php
/**
 * WooCommerce gl Active Members List & Export Template
 */

$GL_Customers_table = new GL_Customers_Table;
$GL_Customers_table->prepare_items();

// Get membership filter
$membership_filter = (int) filter_input( INPUT_GET, 'product', FILTER_SANITIZE_NUMBER_INT );
?>

<div class="wrap">
    <h2>Active Members List</h2>

    <div class="gl-active-members-table-outer">
        <?php $GL_Customers_table->display(); ?>
    </div>

    <div class="gl-export-active-members">
        <h3>Export</h3>
        <p>Export the list as an excel spreadsheet.</p>
        <a class="button button-primary" href="<?php echo admin_url( 'admin.php?page=gl-members-excel-export&product=' . $membership_filter ); ?>">Export</a>
    </div>
</div>
