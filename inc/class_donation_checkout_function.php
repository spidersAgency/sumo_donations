<?php

class FP_Donation_Checkout_Function {

    // Construct the Donation Checkout Function
    public function __construct() {
        if (get_option('_fp_donation_display_checkout') == 'yes') {
            add_action('woocommerce_before_checkout_form', array($this, 'checkout_donation_option'), 1);
        }
    }

    // Checkout Donation Field

    public static function checkout_donation_option() {
        if (is_checkout()) {
            if (sumo_check_global_settings_to_display_df()) {
                //echo FP_DonationSystem_Main_Function::initialize_post_data_after_submit('checkout');
                ?>
                <style type='text/css'>
                <?php echo get_option('_fp_donation_checkout_css'); ?>
                </style>
                <form name="fp_donation_form" method="post">
                    <?php
                    echo FP_DonationSystem_Main_Function::add_donation_amount_fields('checkout');
                    ?>
                </form>
                <?php
            }
        }
    }

}

new FP_Donation_Checkout_Function();
