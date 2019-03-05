<?php

class FP_Donation_FlyBox_Tab {

    // Construct the Donation Messages
    public function __construct() {

        add_action('woocommerce_donationsystem_settings_tabs_array', array($this, 'initialize_tab'));

        add_action('woocommerce_donationsystem_settings_tabs_fp_donationsystem_flybox', array($this, 'initialize_visual_appearance_admin_fields'));

        add_action('woocommerce_update_options_fp_donationsystem_flybox', array($this, 'update_data_from_admin_fields'));
        
        if(isset($_POST['reset_fp_donationsystem_flybox'])){
            add_action('admin_head', array($this, 'reset_option_to_donationsystem'));
        }

        add_action('admin_init', array($this, 'add_option_to_donationsystem'));
    }

    // Initialize the Settings from Donation Messages

    public static function initialize_tab($settings_tab) {
        if(!is_array($settings_tab)){
            $settings_tab = (array)$settings_tab;
        }
        $settings_tab['fp_donationsystem_flybox'] = __('Fly Box', 'donationsystem');
        return array_filter($settings_tab);
    }

    // Initialize Settings Page array

    public static function initialize_admin_fields() {
        global $woocommerce;
        return apply_filters('woocommerce_donationsystem_flybox_settings', array(
            array(
                'name' => __('Fly Box Settings', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_flybox_settings'
            ),
            array(
                'name' => __('Show Fly Box', 'donationsystem'),
                'id' => '_fp_donation_display_flybox',
                'css' => '',
                'std' => 'no',
                'class' => '',
                'default' => 'no',
                'newids' => '_fp_donation_display_flybox',
                'type' => 'checkbox',
            ),
            array(
                'name' => __('Title of Donation Form', 'donationsystem'),
                'id' => '_fp_donation_form_title_flybox',
                'css' => 'min-width:350px',
                'std' => 'Make a Donation',
                'class' => '',
                'default' => 'Make a Donation',
                'newids' => '_fp_donation_form_title_flybox',
                'type' => 'text',
            ),
            array(
                'name' => __('Description for Donation Form', 'donationsystem'),
                'id' => '_fp_donation_form_description_flybox',
                'css' => '',
                'std' => 'You can make a donation here',
                'class' => '',
                'default' => 'You can make a donation here',
                'newids' => '_fp_donation_form_description_flybox',
                'type' => 'textarea',
            ),
            array(
                'name' => __('Show Donation Rewards Table', 'donationsystem'),
                'id' => '_fp_donation_display_donation_rewards_table_flybox',
                'css' => '',
                'std' => 'no',
                'class' => '',
                'default' => 'no',
                'newids' => '_fp_donation_display_donation_rewards_table_flybox',
                'type' => 'checkbox',
            ),
            array(
                'name' => __('Donation Field Type', 'donationsystem'),
                'id' => '_fp_donation_form_type_flybox',
                'css' => '',
                'std' => '3',
                'class' => '',
                'default' => '3',
                'newids' => '_fp_donation_form_type_flybox',
                'type' => 'select',
                'options' => array(
                    '1' => __('Editable Text Field', 'donationsystem'),
                    '4' => __('Non-Editable Text Field', 'donationsystem'),
                    '2' => __('Predefined Buttons', 'donationsystem'),
                    '3' => __('List Box', 'donationsystem'),
                ),
            ),
            array(
                'name' => __('Also Display Editable Text Field', 'donationsystem'),
                'id' => '_fp_donation_display_editable_field_flybox',
                'css' => '',
                'std' => 'no',
                'class' => '',
                'default' => 'no',
                'newids' => '_fp_donation_display_editable_field_flybox',
                'type' => 'checkbox',
            ),
            array(
                'name' => __('Default Donation Value', 'donationsystem'),
                'desc' => __('Enter the Default Donation Value', 'donationsystem'),
                'id' => '_fp_donation_default_value_flybox',
                'css' => 'min-width:150px;',
                'std' => '',
                'class' => '',
                'default' => '',
                'newids' => '_fp_donation_default_value_flybox',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Minimum Donation Value', 'donationsystem'),
                'desc' => __('Enter Minimum Donation Value', 'donationsystem'),
                'id' => '_fp_donation_amount_minimum_flybox',
                'css' => 'min-width:150px;',
                'std' => '',
                'class' => '',
                'default' => '',
                'newids' => '_fp_donation_amount_minimum_flybox',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Maximum Donation Value', 'donationsystem'),
                'desc' => __('Enter Maximum Donation Value', 'donationsystem'),
                'id' => '_fp_donation_amount_maximum_flybox',
                'css' => 'min-width:150px;',
                'std' => '',
                'class' => '',
                'default' => '',
                'newids' => '_fp_donation_amount_maximum_flybox',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Label for Simple Form', 'donationsystem'),
                'id' => '_fp_donation_form_simple_label_flybox',
                'css' => '',
                'std' => 'Donate {currency_symbol}',
                'class' => '',
                'default' => 'Donate {currency_symbol}',
                'newids' => "_fp_donation_form_simple_label_flybox",
                'type' => 'textarea',
            ),
            array(
                'name' => __('Donation Value Separated by commas(,)', 'donationsystem'),
                'id' => '_fp_donation_form_value_listbox_flybox',
                'css' => 'min-width:350px;',
                'std' => '10,20,30,40,50',
                'class' => '',
                'default' => '10,20,30,40,50',
                'newids' => '_fp_donation_form_value_listbox_flybox',
                'type' => 'textarea',
            ),
            array(
                'type' => '_fp_donation_predefined_buttons_flybox',
            ),
            array(
                'name' => __('Button Background Color', 'donationsystem'),
                'id' => '_fp_donation_button_bg_color_flybox',
                'css' => '',
                'std' => 'EB6F31',
                'class' => 'color',
                'default' => 'EB6F31',
                'newids' => '_fp_donation_button_bg_color_flybox',
                'type' => 'text',
            ),
            array(
                'name' => __('Button Hover color', 'donationsystem'),
                'id' => '_fp_donation_button_hover_color_flybox',
                'css' => '',
                'std' => '00A0D3',
                'class' => 'color',
                'default' => '00A0D3',
                'newids' => '_fp_donation_button_hover_color_flybox',
                'type' => 'text',
            ),
            array(
                'name' => __('Button Selected Color', 'donationsystem'),
                'id' => '_fp_donation_button_selected_color_flybox',
                'css' => '',
                'std' => 'A0CE4D',
                'class' => 'color',
                'default' => 'A0CE4D',
                'newids' => '_fp_donation_button_selected_color_flybox',
                'type' => 'text',
            ),
            array(
                'name' => __('Button Text Color', 'donationsystem'),
                'id' => '_fp_donation_button_text_color_flybox',
                'css' => '',
                'std' => 'FFFFFF',
                'class' => 'color',
                'default' => 'FFFFFF',
                'newids' => '_fp_donation_button_text_color_flybox',
                'type' => 'text',
            ),
            array(
                'name' => __('Custom CSS for Donation Form in Fly Box', 'donationsystem'),
                'id' => '_fp_donation_flybox_css',
                'css' => 'min-width:350px;min-height:200px;',
                'placeholder' => 'Custom CSS',
                'std' => '',
                'class' => '',
                'default' => '',
                'newids' => '_fp_donation_flybox_css',
                'type' => 'textarea',
                'desc_tip' => true,
            ),
            array(
                'type' => 'sectionend',
                'id' => '_donationsystem_flybox_settings'
            ),
            array(
                'name' => __('Fly Box Customization', 'donationsystem'),
                'type' => 'title',
                'id' => '_fp_donationsystem_flybox_customization',
            ),
            array(
                'name' => __('Position of Fly Box', 'donationsystem'),
                'id' => '_fp_donation_flybox_position',
                'css' => '',
                'std' => '1',
                'class' => '',
                'default' => '1',
                'type' => 'select',
                'options' => array(
                    '1' => __('Left Side', 'donationsystem'),
                    '2' => __('Right Side', 'donationsystem'),
                ),
                'newids' => '_fp_donation_flybox_position',
            ),
            array(
                'name' => __('Show Fly Box in Percentage of Scroll in %', 'donationsystem'),
                'type' => 'text',
                'desc' => __("Enter the Scroll Percentage in order to show Fly Box (don't enter %  in text field)", 'donationsystem'),
                'css' => '',
                'std' => '50',
                'class' => '',
                'default' => '50',
                'type' => 'text',
                'desc_tip' => true,
                'newids' => '_fp_donation_flybox_scroll_percentage',
                'id' => '_fp_donation_flybox_scroll_percentage',
            ),
            array(
                'name' => __('Display Fly Box in Cart Page', 'donationsystem'),
                'id' => '_fp_donation_flybox_display_cart',
                'css' => '',
                'std' => 'yes',
                'class' => '',
                'default' => 'yes',
                'type' => 'checkbox',
                'newids' => '_fp_donation_flybox_display_cart',
            ),
            array(
                'name' => __('Display Fly Box in Checkout Page', 'donationsystem'),
                'id' => '_fp_donation_flybox_display_checkout',
                'css' => '',
                'std' => 'no',
                'class' => '',
                'default' => 'no',
                'type' => 'checkbox',
                'newids' => '_fp_donation_flybox_display_checkout',
            ),
            array(
                'name' => __('Background Color for Fly Box', 'donationsystem'),
                'id' => '_fp_donation_flybox_bgcolor',
                'css' => '',
                'std' => "FF4A36",
                'class' => 'color',
                'default' => 'FF4A36',
                'type' => 'text',
                'newids' => '_fp_donation_flybox_bgcolor',
            ),
            array(
                'name' => __('Border Color for Fly Box', 'donationsystem'),
                'id' => '_fp_donation_flybox_border_color',
                'css' => '',
                'std' => 'fff',
                'class' => 'color',
                'default' => 'fff',
                'type' => 'text',
                'newids' => '_fp_donation_flybox_border_color',
            ),
            array(
                'name' => __('Fly Box Heading Text Color', 'donationsystem'),
                'id' => '_fp_donation_flybox_head_text_color',
                'css' => "",
                'std' => '000',
                'class' => 'color',
                'default' => '000',
                'type' => 'text',
                'newids' => '_fp_donation_flybox_head_text_color',
            ),
            array(
                'name' => __('Fly Box Description Text Color', 'donationsystem'),
                'id' => '_fp_donation_flybox_description_text_color',
                'css' => '',
                'std' => '000',
                'class' => 'color',
                'default' => '000',
                'type' => 'text',
                'newids' => '_fp_donation_flybox_description_text_color',
            ),
            array('type' => 'sectionend', 'id' => '_fp_donationsystem_flybox_customization'),
            array(
                'name' => __('Fly Box Cookies Settings', 'donationsystem'),
                'type' => 'title',
                'id' => '_fp_donationsystem_flybox_cookies',
            ),
            array(
                'name' => __('Enable Cookies for Fly Box', 'donationsystem'),
                'type' => 'checkbox',
                'id' => '_fp_donation_flybox_enable_cookies',
                'css' => '',
                'std' => 'no',
                'default' => 'no',
                'newids' => '_fp_donation_flybox_enable_cookies',
            ),
            array(
                'name' => __('Fly Box Click to Close for X Days', 'donationsystem'),
                'type' => 'text',
                'id' => '_fp_donation_flybox_click_to_close',
                'css' => '',
                'std' => '1',
                'desc' => __('After click close button in Fly Box, Show Fly Box again after X Days', 'donationsystem'),
                'default' => '1',
                'newids' => '_fp_donation_flybox_click_to_close',
            ),
            array('type' => 'sectionend', 'id' => "_fp_donationsystem_flybox_cookies"),
            array(
                'name' => __('Advanced Settings', 'donationsystem'),
                'type' => 'title',
                'id' => '_fp_donation_flybox_advanced_settings',
            ),
            array(
                'name' => __('Hide Fly Box in following screensize', 'donationsystem'),
                'type' => 'textarea',
                'id' => '_fp_donation_flybox_hide_screen_size',
                'css' => '',
                'std' => '',
                'desc' => __('Enter the Screen Size something like 320x240,380x420 with comma separated values', 'donationsystem'),
            ),
            array('type' => 'sectionend', 'id' => '_fp_donation_flybox_advanced_settings'),
        ));
    }

    public static function initialize_visual_appearance_admin_fields() {
        woocommerce_admin_fields(self::initialize_admin_fields());
    }

    public static function update_data_from_admin_fields() {
        woocommerce_update_options(self::initialize_admin_fields());
        $listofarray = array('flybox');
        foreach ($listofarray as $eacharray) {
            update_option('fp_predefined_buttons_' . $eacharray, $_POST['fp_predefined_buttons_' . $eacharray]);
            update_option('fp_predefined_buttons_columns_' . $eacharray, $_POST['fp_predefined_buttons_columns_' . $eacharray]);
        }
    }

    public static function add_option_to_donationsystem() {
        foreach (self::initialize_admin_fields() as $setting)
            if (isset($setting['newids']) && isset($setting['std'])) {
                add_option($setting['newids'], $setting['std']);
            }

        $listofarray = array('flybox');
        foreach ($listofarray as $eacharray) {
            add_option('fp_predefined_buttons_columns_' . $eacharray, '3');
        }
    }

    public static function reset_option_to_donationsystem() {
        foreach (self::initialize_admin_fields()as $setting) {
            if (isset($setting['newids']) && isset($setting['std'])) {
                delete_option($setting['newids']);
                add_option($setting['newids'], $setting['std']);
            }
        }
        $listofarray = array('flybox');
        foreach ($listofarray as $eacharray) {
            delete_option('fp_predefined_buttons_columns_' . $eacharray);
            add_option('fp_predefined_buttons_columns_' . $eacharray, '3');
        }
    }

}

new FP_Donation_FlyBox_Tab();
