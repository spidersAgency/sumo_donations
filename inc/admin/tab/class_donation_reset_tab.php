<?php

class FP_Donation_Reset_Tab {

    // Construct the Donation Reset
    public function __construct() {

        add_action('woocommerce_donationsystem_settings_tabs_array', array($this, 'initialize_tab'));

        add_action('woocommerce_donationsystem_settings_tabs_fp_donationsystem_reset', array($this, 'initialize_visual_appearance_admin_fields'));

        add_action('woocommerce_update_options_fp_donationsystem_reset', array($this, 'update_data_from_admin_fields'));

        add_action('admin_init', array($this, 'add_option_to_donationsystem'));

        add_action('woocommerce_admin_field_fp_reset_data', array($this, 'add_reset_button_in_tab'));

        add_action('wp_ajax_fp_donation_reset_info', array($this, 'ajax_request_for_donation'));
    }

    // Initialize the Settings from Donation Messages

    public static function initialize_tab($settings_tab) {
        if(!is_array($settings_tab)){
            $settings_tab = (array)$settings_tab;
        }
        $settings_tab['fp_donationsystem_reset'] = __('Reset', 'donationsystem');
        return array_filter($settings_tab);
    }

    // Initialize Settings Page array

    public static function initialize_admin_fields() {
        global $woocommerce;
        return apply_filters('woocommerce_donationsystem_reset_settings', array(
            array(
                'name' => __('Reset Settings', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_reset_settings'
            ),
            array(
                'type' => 'fp_style_tab',
            ),
            array(
                'name' => __('Reset Donation Table', 'donationsystem'),
                'id' => '_fp_reset_donar_info',
                'css' => '',
                'std' => 'no',
                'class' => '',
                'default' => 'no',
                'newids' => '_fp_reset_donar_info',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Reset Plugin Settings (All Tabs)', 'donationsystem'),
                'id' => '_fp_reset_entire_settings',
                'css' => '',
                'std' => 'no',
                'class' => '',
                'default' => 'no',
                'newids' => '_fp_reset_entire_settings',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array(
                'type' => 'fp_reset_data'
            ),
            array(
                'type' => 'sectionend',
                'id' => '_donationsystem_reset_settings'
            ),
        ));
    }

    public static function initialize_visual_appearance_admin_fields() {
        woocommerce_admin_fields(self::initialize_admin_fields());
    }

    public static function update_data_from_admin_fields() {
        woocommerce_update_options(self::initialize_admin_fields());
    }

    public static function add_option_to_donationsystem() {
        foreach (self::initialize_admin_fields() as $setting)
            if (isset($setting['newids']) && isset($setting['std'])) {
                add_option($setting['newids'], $setting['std']);
            }
    }

    public static function add_reset_button_in_tab() {
        ?>
        <tr>
            <td>
                <input type="submit" name="fp_reset_data" value="<?php _e('Reset', 'donationsystem'); ?>" id="fp_reset_data" class="fp_reset_data button-primary"/>
                <div class="fp_reset_response"></div>
                <script type="text/javascript">
                    jQuery(function () {
                        jQuery(document).on('click', '#fp_reset_data', function () {
                            jQuery(this).attr('disabled', 'disabled');
                            var get_checked_data = jQuery('#_fp_reset_donar_info').is(':checked');
                            var entire_settings = jQuery('#_fp_reset_entire_settings').is(':checked');
                            if ((get_checked_data === true) || (entire_settings === true)) {
                                var reset_confirm = confirm("Are you sure want to reset the data?");
                                if (reset_confirm === true) {
                                    jQuery('.fp_reset_response').html("Please Wait Data Resetting is under Progress");
                                    var dataparam = ({
                                        action: 'fp_donation_reset_info',
                                        master_log: get_checked_data,
                                        settings: entire_settings,
                                    });
                                    jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                            function (response) {
                                                var newresponse = jQuery.trim(response);
                                                if (newresponse === '1') {
                                                    jQuery('.fp_reset_response').html("Data resetted successfully");
                                                    jQuery('#fp_reset_data').removeAttr('disabled');
                                                }
                                            });
                                    return false;
                                }
                            }
                        });
                    });
                </script>

            </td>
        </tr>
        <?php
    }

    // Ajax Request for Donation

    public static function ajax_request_for_donation() {
        if (isset($_POST['master_log'])) {
            $master_log = $_POST['master_log'];
            $settings = $_POST['settings'];
            if (($master_log) && ($settings)) {
                update_option('_fp_donated_order_ids', array());
                // Run the list of tab function here to reset the data
                FP_DonationSystem_General_Tab::reset_option_to_donationsystem();
                FP_Donation_Labels_Tab::reset_option_to_donationsystem();
                FP_Donation_Messages_Tab::reset_option_to_donationsystem();
                FP_Donation_Rewards_Tab::reset_option_to_donationsystem();
                FP_Donation_Shortcode_Tab::reset_option_to_donationsystem();
                FP_Donation_Form_Tab::reset_option_to_donationsystem();
                FP_Donation_FlyBox_Tab::reset_option_to_donationsystem();
                echo "1";
            } elseif ($master_log && (!$settings)) {
                update_option('_fp_donated_order_ids', array());
                echo "1";
            } else {
                // List of function to reset the data
                FP_DonationSystem_General_Tab::reset_option_to_donationsystem();
                FP_Donation_Labels_Tab::reset_option_to_donationsystem();
                FP_Donation_Messages_Tab::reset_option_to_donationsystem();
                FP_Donation_Rewards_Tab::reset_option_to_donationsystem();
                FP_Donation_Shortcode_Tab::reset_option_to_donationsystem();
                FP_Donation_Form_Tab::reset_option_to_donationsystem();
                FP_Donation_FlyBox_Tab::reset_option_to_donationsystem();
                echo "1";
            }
        }
        exit();
    }

}

new FP_Donation_Reset_Tab();
