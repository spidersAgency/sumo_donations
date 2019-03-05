<?php

class FP_Donation_Messages_Tab {

    // Construct the Donation Messages
    public function __construct() {

        add_action('woocommerce_donationsystem_settings_tabs_array', array($this, 'initialize_tab'));

        add_action('woocommerce_donationsystem_settings_tabs_fp_donationsystem_messages', array($this, 'initialize_visual_appearance_admin_fields'));

        add_action('woocommerce_update_options_fp_donationsystem_messages', array($this, 'update_data_from_admin_fields'));

        if (isset($_POST['reset_fp_donationsystem_messages'])) {
            add_action('admin_head', array($this, 'reset_option_to_donationsystem'));
        }

        add_action('admin_init', array($this, 'add_option_to_donationsystem'));
    }

    // Initialize the Settings from Donation Messages

    public static function initialize_tab( $settings_tab ) {
        if(!is_array($settings_tab)){
            $settings_tab = (array)$settings_tab;
        }
        $settings_tab['fp_donationsystem_messages'] = __('Messages', 'donationsystem');
        return array_filter($settings_tab);
    }

    // Initialize Settings Page array

    public static function initialize_admin_fields() {
        global $woocommerce;
        return apply_filters('woocommerce_donationsystem_messages_settings', array(
            array(
                'name' => __('Message Settings', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_messages_settings'
            ),
            array(
                'name' => __('Donation Added Successfully Message', 'donationsystem'),
                'desc' => __('Enter the Message which will be displayed after Donation is made', 'donationsystem'),
                'id' => '_fp_donation_success_message',
                'css' => 'min-width:150px;',
                'std' => 'Donation Successfully added to Cart',
                'class' => '',
                'default' => 'Donation Successfully added to Cart',
                'newids' => '_fp_donation_success_message',
                'type' => 'textarea',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Minimum Donation Error Message', 'donationsystem'),
                'desc' => __('Enter the Minimum Donation Error Message', 'donationsystem'),
                'id' => '_fp_donation_minimum_error_message',
                'css' => 'min-width:150px;',
                'std' => 'Please Enter Minimum Donation {minimum_donation}',
                'class' => '',
                'default' => 'Please Enter Minimum Donation {minimum_donation}',
                'newids' => '_fp_donation_minimum_error_message',
                'type' => 'textarea',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Maximum Donation Error Message', 'donationsystem'),
                'desc' => __('Enter the Maximum Donation Error Message', 'donationsystem'),
                'id' => '_fp_donation_maximum_error_message',
                'css' => 'min-width:150px;',
                'std' => 'Please enter the donation less than {maximum_donation}',
                'class' => '',
                'default' => 'Please enter the donation less than {maximum_donation}',
                'newids' => '_fp_donation_maximum_error_message',
                'type' => 'textarea',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Empty Donation Error Message', 'donationsystem'),
                'desc' => __('Enter the Donation Error Message', 'donationsystem'),
                'id' => '_fp_donation_empty_error_message',
                'css' => 'min-width:150px;',
                'std' => 'Please Enter Valid Donation',
                'class' => '',
                'default' => 'Please Enter Valid Donation',
                'newids' => '_fp_donation_empty_error_message',
                'type' => 'textarea',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Force Manual Donation Error Message', 'donationsystem'),
                'desc' => __('Enter the Force Donation Error Message', 'donationsystem'),
                'id' => '_fp_force_manual_donation_error_message',
                'css' => 'min-width:150px',
                'std' => 'You haven\'t made a Donation which is mandatory, please make a Donation in order to Complete the Purchase',
                'class' => '',
                'default' => 'You haven\'t made a Donation which is mandatory, please make a Donation in order to Complete the Purchase',
                'newids' => '_fp_force_manual_donation_error_message',
                'type' => 'textarea',
                'desc_tip' => true,
            ),
            array(
                'type' => 'sectionend',
                'id' => '_donationsystem_messages_settings'
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

    public static function reset_option_to_donationsystem() {
        foreach (self::initialize_admin_fields()as $setting) {
            if (isset($setting['newids']) && isset($setting['std'])) {
                delete_option($setting['newids']);
                add_option($setting['newids'], $setting['std']);
            }
        }
    }

}

new FP_Donation_Messages_Tab();
