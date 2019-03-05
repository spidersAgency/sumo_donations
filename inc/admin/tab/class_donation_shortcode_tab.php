<?php

class FP_Donation_Shortcode_Tab {

    // Construct the Donation Messages
    public function __construct() {

        add_action('woocommerce_donationsystem_settings_tabs_array', array($this, 'initialize_tab'));

        add_action('woocommerce_donationsystem_settings_tabs_fp_donationsystem_shortcodes', array($this, 'initialize_visual_appearance_admin_fields'));

        add_action('woocommerce_update_options_fp_donationsystem_shortcodes', array($this, 'update_data_from_admin_fields'));

        add_action('admin_init', array($this, 'add_option_to_donationsystem'));

        add_action('woocommerce_admin_field_fp_style_tab', array($this, 'add_style_to_hide'));
    }

    // Initialize the Settings from Donation Messages

    public static function initialize_tab($settings_tab) {
        if(!is_array($settings_tab)){
            $settings_tab = (array)$settings_tab;
        }
        $settings_tab['fp_donationsystem_shortcodes'] = __('Shortcodes', 'donationsystem');
        return array_filter($settings_tab);
    }

    // Initialize Settings Page array

    public static function initialize_admin_fields() {
        global $woocommerce;
        return apply_filters('woocommerce_donationsystem_shortcode_settings', array(
            array(
                'name' => __('List of Shortcodes', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_shortcode_settings'
            ),
            array(
                'name' => __('[fp_donation_form]', 'donationsystem'),
                'type' => 'title',
                'desc' => __('Use this Shortcode in any Post/Page to display Donation Form', 'donationsystem'),
                'id' => '_donationsystem_shortcode_form',
            ),
            array(
                'name' => __('[fp_donation_table]', 'donationsystem'),
                'type' => 'title',
                'desc' => __('Use this Shortcode in any Post/Page to display Donation Table', 'donationsystem'),
                'id' => '_donationsystem_shortcode_donar_table',
            ),
            array(
                'name' => __('[fp_donation_rewards_table]', 'donationsystem'),
                'type' => 'title',
                'desc' => __('Use this Shortcode in any Post/Page to display Donation Rewards Table', 'donationsystem'),
                'id' => '_donationsystem_shortcode_donar_rewards_table',
            ),
            array(
                'type' => 'fp_style_tab',
            ),
            array(
                'type' => 'sectionend',
                'id' => '_donationsystem_shortcode_settings'
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

    public static function add_style_to_hide() {
        ?>
        <style type="text/css">
            p.submit {
                display:none;
            }
        </style>
        <?php

    }

}

new FP_Donation_Shortcode_Tab();
