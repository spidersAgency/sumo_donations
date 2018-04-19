<?php

class FP_Donation_Support {

    // Construct the Donation Messages
    public function __construct() {

        add_action('woocommerce_donationsystem_settings_tabs_array', array($this, 'initialize_tab'));

        add_action('woocommerce_donationsystem_settings_tabs_fp_donationsystem_support', array($this, 'initialize_visual_appearance_admin_fields'));

        add_action('woocommerce_update_options_fp_donationsystem_support', array($this, 'update_data_from_admin_fields'));

        add_action('admin_init', array($this, 'add_option_to_donationsystem'));
    }

    // Initialize the Settings from Donation Messages

    public static function initialize_tab($settings_tab) {
        if(!is_array($settings_tab)){
            $settings_tab = (array)$settings_tab;
        }
        $settings_tab['fp_donationsystem_support'] = __('Support', 'donationsystem');
        return array_filter($settings_tab);
    }

    // Initialize Settings Page array

    public static function initialize_admin_fields() {
        global $woocommerce;
        return apply_filters('woocommerce_donationsystem_support_settings', array(
            array(
                'name' => __('Help & Support', 'donationsystem'),
                'type' => 'title',
                'desc' => __('For support, feature request or any help, please <a href="http://support.fantasticplugins.com/">register and open a support ticket on our site.</a> <br> '),
                'id' => '_donationsystem_support_settings'
            ),
            array(
                'name' => __('Documentation', 'donationsystem'),
                'type' => 'title',
                'desc' => 'Please check the documentation as we have lots of information there. The documentation file can be found inside the documentation folder which you will find when you unzip the downloaded zip file.',
                'id' => '_donationsystem_documentation',
            ),
            array(
                'type' => 'fp_style_tab',
            ),
            array(
                'type' => 'sectionend',
                'id' => '_donationsystem_support_settings'
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

new FP_Donation_Support();
