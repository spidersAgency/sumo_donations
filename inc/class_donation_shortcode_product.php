<?php

class FP_Donation_Shortcode_Function {

    // Construct the Donation Shortcode
    public function __construct() {
        add_shortcode("fp_donation_form", array($this, 'set_up_shortcode'));
        add_shortcode("fp_donation_table", array('FP_Donation_Product_Function', 'get_entire_details_about_donar'));
        add_shortcode('fp_donation_rewards_table', array('FP_DonationSystem_Main_Function', 'show_donation_rewards_table'));
    }

    public static function set_up_shortcode() {
        ob_start();
        if (get_option('_fp_donation_display_shortcode') == 'yes') {
            echo FP_DonationSystem_Main_Function::initialize_post_data_after_submit('product');
            ?>
            <style type="text/css">
            <?php
            echo get_option('_fp_donation_shortcode_css');
            ?>
                #fp_donation_form {
                    background:#<?php echo get_option('_fp_donation_form_background_color'); ?>;
                }
                #fp_predefined_buttons td {
                    text-align:center;
                }
                #fp_donation_form {
                    padding-left:10px;
                    padding-right:10px;
                }

                @media screen and (min-width: 0px) and (max-width: 430px)  {
                    #fp_predefined_buttons div {
                        padding: 10px 20px !important;
                    }
                }
            </style>
            <form id='fp_donation_form' name="fp_donation_form" method="post">
                <?php
                echo FP_DonationSystem_Main_Function::add_donation_amount_fields('shortcode');
                ?>
            </form>
            <?php
        }
        $content = ob_get_clean();
        return $content;
    }

}

new FP_Donation_Shortcode_Function();
