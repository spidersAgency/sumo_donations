<?php

class FP_Donation_Product_Function {

    public function __construct() {
        if (get_option('_fp_donation_display_product') == 'yes') {
            add_action('woocommerce_after_add_to_cart_form', array($this, 'donation_product_function'));
        }
    }

// Donation Product Function

    public static function donation_product_function() {
        if (sumo_check_global_settings_to_display_df()) {
            global $post;
            if (is_product()) {
                ?>
                <style type='text/css'>
                <?php echo get_option('_fp_donation_product_css'); ?>
                </style>
                <?php
                if (get_option('_fp_show_donation_form_in_product') == '1') {
                    // echo FP_DonationSystem_Main_Function::initialize_post_data_after_submit('product');
                    ?>
                    <form name="fp_donation_form" method="post">
                        <?php
                        echo FP_DonationSystem_Main_Function::add_donation_amount_fields('product');
                        ?>
                    </form>
                    <?php
                } else if (get_option('_fp_show_donation_form_in_product') == '2') {
                    // For Included Products alone
                    $post_id = $post->ID;
                    $get_included_products_data = get_option('_fp_donation_form_included_selected_products');
                    if (!is_array($get_included_products_data)) {
                        $get_included_products_data = explode(',', $get_included_products_data);
                    }
                    if (is_array($get_included_products_data) && (!empty($get_included_products_data))) {
                        if (in_array($post_id, $get_included_products_data)) {
                            //  echo FP_DonationSystem_Main_Function::initialize_post_data_after_submit('product');
                            ?>
                            <form name="fp_donation_form" method="post">
                                <?php
                                echo FP_DonationSystem_Main_Function::add_donation_amount_fields('product');
                                ?>
                            </form>
                            <?php
                        }
                    }
                } else {
                    // For Excluded Products alone
                    $post_id = $post->ID;
                    $get_excluded_products_data = get_option('_fp_donation_form_included_selected_products');
                    if (!is_array($get_excluded_products_data)) {
                        $get_excluded_products_data = explode(',', $get_excluded_products_data);
                    }
                    if (is_array($get_excluded_products_data) && (!empty($get_excluded_products_data))) {
                        if (!in_array($post_id, $get_excluded_products_data)) {
                            // echo FP_DonationSystem_Main_Function::initialize_post_data_after_submit('product');
                            ?>
                            <form name="fp_donation_form" method="post">
                                <?php
                                echo FP_DonationSystem_Main_Function::add_donation_amount_fields('product');
                                ?>
                            </form>
                            <?php
                        }
                    }
                }
            }
        }
    }

// Get the Table of whoever contributed
    public static function get_donated_order_ids() {
        $get_product_id = get_option('ds_select_particular_products');

        if (($get_product_id != '') && ($get_product_id)) {
            return get_option('_fp_donated_order_ids');
        } else {
            return false;
        }
    }

// Get the Entire Details from Order about donar

    public static function get_entire_details_about_donar() {
// List of Orderids
        global $post, $woocommerce;
        ob_start();
        $get_orderids = self::get_donated_order_ids();
        if (get_option('_fp_donation_display_table') == 'yes') {
            if ($get_orderids) {
                ?>
                <h3><?php echo get_option('_fp_donar_details_heading'); ?></h3>
                <table class="form-fields">
                    <thead>
                        <tr>
                                <?php if (get_option('_fp_hide_donation_table_sno') == 'yes') { ?>
                                <th>
                                <?php echo get_option('_fp_donar_details_sno'); ?>
                                </th>
                                <?php } if (get_option('_fp_hide_donation_table_name') == 'yes') { ?>
                                <th>
                                <?php echo get_option('_fp_donar_details_name'); ?>
                                </th>
                                <?php } if (get_option('_fp_hide_donation_table_email') == 'yes') { ?>
                                <th>
                                <?php echo get_option('_fp_donar_details_email'); ?>
                                </th>
                                <?php } if (get_option('_fp_hide_donation_table_amount') == 'yes') { ?>
                                <th>
                                <?php echo get_option('_fp_donar_details_amount'); ?>
                                </th>
                                <?php } if (get_option('_fp_hide_donation_table_status') == 'yes') { ?>
                                <th>
                                <?php echo get_option('_fp_donar_details_status'); ?>
                                </th>
                <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (is_array((array) $get_orderids)) {
                            $i = 1;
                            foreach ($get_orderids as $each_order) {
                                $order = new WC_Order($each_order);
                                ?>
                                <tr>
                                        <?php if (get_option('_fp_hide_donation_table_sno') == 'yes') { ?>
                                        <td>
                                        <?php echo $i; ?>
                                        </td>
                                        <?php } if (get_option('_fp_hide_donation_table_name') == 'yes') { ?>
                                        <td>
                                        <?php echo sumo_donation_get_order_billing_first_name($order) . " " . sumo_donation_get_order_billing_last_name($order); ?>
                                        </td>
                                        <?php } if (get_option('_fp_hide_donation_table_email') == 'yes') { ?>
                                        <td>
                                        <?php echo sumo_donation_get_order_billing_email($order); ?>
                                        </td>
                                        <?php } if (get_option('_fp_hide_donation_table_amount') == 'yes') { ?>
                                        <td>
                                            <?php
                                            $donatedamount = get_post_meta($each_order, 'fp_donation_value', true);
                                            echo FP_DonationSystem_Main_Function::format_price($donatedamount);
                                            ?>
                                        </td>
                                        <?php } if (get_option('_fp_hide_donation_table_status') == 'yes') { ?>
                                        <td>
                                        <?php echo sumo_donation_get_order_status($order); ?>
                                        </td>
                                <?php } ?>
                                </tr>
                                <?php
                                $i++;
                            }
                        }
                        ?>

                    </tbody>

                </table>
                <?php
            }
        }
        $getcontents = ob_get_clean();
        return $getcontents;
    }

    // Get Price of Corresponding Item

    public static function get_item_of_corresponding_order($order, $product_id) {
        $items = $order->get_items();
        foreach ($items as $each_item) {
            if ($each_item['product_id'] == $product_id) {
                return $each_item;
            }
        }
        return false;
    }

}

new FP_Donation_Product_Function();
