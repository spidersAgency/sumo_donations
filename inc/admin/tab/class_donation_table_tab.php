<?php

class FP_Donation_Table_Tab {

    // Construct the Donation Messages
    public function __construct() {

        add_action('woocommerce_donationsystem_settings_tabs_array', array($this, 'initialize_tab'));

        add_action('woocommerce_donationsystem_settings_tabs_fp_donationsystem_table', array($this, 'initialize_visual_appearance_admin_fields'));

        add_action('woocommerce_update_options_fp_donationsystem_table', array($this, 'update_data_from_admin_fields'));

        add_action('admin_init', array($this, 'add_option_to_donationsystem'));

        add_action('woocommerce_admin_field_fp_log_donation_table', array($this, 'add_field_for_donation_table'));
    }

    // Initialize the Settings from Donation Messages

    public static function initialize_tab($settings_tab) {
        if(!is_array($settings_tab)){
            $settings_tab = (array)$settings_tab;
        }
        $settings_tab['fp_donationsystem_table'] = __('Donation Table', 'donationsystem');
        return array_filter($settings_tab);
    }

    // Initialize Settings Page array

    public static function initialize_admin_fields() {
        global $woocommerce;
        return apply_filters('woocommerce_donationsystem_table_settings', array(
            array(
                'name' => __('Donation Table', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_donationtable_settings'
            ),
            array('type' => 'sectionend'),
            array(
                'name' => __('[fp_donation_table]', 'donationsystem'),
                'type' => 'title',
                'desc' => __('Use this Shortcode in any Post/Page to display Donation Table', 'donationsystem'),
                'id' => '_donationsystem_donationtable_settings',
            ),
            array(
                'type' => 'fp_style_tab',
            ),
            array(
                'type' => 'fp_log_donation_table',
            ),
            array(
                'type' => 'sectionend',
                'id' => '_donationsystem_donationtable_settings'
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

    // Add Admin Field for Donation Table
    public static function add_field_for_donation_table() {
        // echo "Donation Table";
        $newwp_list_donation_table = new FP_List_Table_DonationTable();
        $newwp_list_donation_table->prepare_items();
        $newwp_list_donation_table->display();
    }

}

new FP_Donation_Table_Tab();
