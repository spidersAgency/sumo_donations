<?php

class FP_Donation_Labels_Tab {

    // Construct the Donation Labels
    public function __construct() {

        add_action('woocommerce_donationsystem_settings_tabs_array', array($this, 'initialize_tab'));

        add_action('woocommerce_donationsystem_settings_tabs_fp_donationsystem_labels', array($this, 'initialize_visual_appearance_admin_fields'));

        add_action('woocommerce_update_options_fp_donationsystem_labels', array($this, 'update_data_from_admin_fields'));

        if (isset($_POST['reset_fp_donationsystem_labels'])) {
            add_action('admin_head', array($this, 'reset_option_to_donationsystem'));
        }

        add_action('admin_init', array($this, 'add_option_to_donationsystem'));
    }

    // Initialize the Settings from Donation Labels

    public static function initialize_tab($settings_tab) {
        if(!is_array($settings_tab)){
            $settings_tab = (array)$settings_tab;
        }
        $settings_tab['fp_donationsystem_labels'] = __('Labels', 'donationsystem');
        return array_filter($settings_tab);
    }

    // Initialize Settings Page array

    public static function initialize_admin_fields() {
        global $woocommerce;
        return apply_filters('woocommerce_donationsystem_label_settings', array(
            array(
                'name' => __('Donation Form Settings', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_label_settings'
            ),
            array(
                'name' => __('Donation Button Caption', 'donationsystem'),
                'desc' => __('Enter Donation Button Caption in Single Product Page/Cart Page/ Checkout Page', 'donationsystem'),
                'id' => '_fp_donation_caption',
                'css' => 'min-width:150px;',
                'std' => 'Donate',
                'class' => '',
                'default' => 'Donate',
                'newids' => '_fp_donation_caption',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'type' => 'sectionend',
                'id' => '_donationsystem_label_settings'
            ),
            array(
                'name' => __('Donation Table Settings', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_table_settings',
            ),
            array(
                'name' => __('Donation Table Heading', 'donationsystem'),
                'desc' => __('Enter the Caption which is heading for Donation Table', 'donationsystem'),
                'id' => '_fp_donar_details_heading',
                'css' => 'min-width:150px;',
                'std' => 'Donor Details',
                'class' => '',
                'default' => 'Donor Details',
                'newids' => '_fp_donar_details_heading',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Customize S.No in Donation Table', 'donationsystem'),
                'desc' => __('Customize the S.No in Donation Table', 'donationsystem'),
                'id' => '_fp_donar_details_sno',
                'css' => 'min-width:150px;',
                'std' => 'S.No',
                'class' => '',
                'default' => 'S.No',
                'newids' => '_fp_donar_details_sno',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Customize Name in Donation Table', 'donationsystem'),
                'desc' => __('Customize the Donor Name in Donor Details Table', 'donationsystem'),
                'id' => '_fp_donar_details_name',
                'css' => 'min-width:150px;',
                'std' => 'Donor Name',
                'class' => '',
                'default' => 'Donor Name',
                'newids' => '_fp_donar_details_name',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Customize Donor Email in Table', 'donationsystem'),
                'desc' => __('Customize the Donor Email in Donor Details Table', 'donationsystem'),
                'id' => '_fp_donar_details_email',
                'css' => 'min-width:150px;',
                'std' => 'Donor Email',
                'class' => '',
                'default' => 'Donor Email',
                'newids' => '_fp_donar_details_email',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Customize Donated Amount in Table', 'donationsystem'),
                'desc' => __('Customize Donated Amount in Table', 'donationsystem'),
                'id' => '_fp_donar_details_amount',
                'css' => 'min-width:150px;',
                'std' => 'Donated Amount',
                'class' => '',
                'default' => 'Donated Amount',
                'newids' => '_fp_donar_details_amount',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Customize the Status in Table', 'donationsystem'),
                'desc' => __('Customize Status of Donated Amount in Table', 'donationsystem'),
                'id' => '_fp_donar_details_status',
                'css' => 'min-width:150px;',
                'std' => 'Status',
                'class' => '',
                'default' => 'Status',
                'newids' => '_fp_donar_details_status',
                'type' => 'text',
                'desc_tip' => true,
            ),            
            array(
                'name' => __('Customize the Memorable in Table', 'donationsystem'),
                'desc' => __('Customize Memorable of Donated Amount in Table', 'donationsystem'),
                'id' => '_fp_donar_details_memorable',
                'css' => 'min-width:150px;',
                'std' => 'Memorable For',
                'class' => '',
                'default' => 'Memorable For',
                'newids' => '_fp_donar_details_memorable',
                'type' => 'text',
                'desc_tip' => true,
            ),            
            array(
                'name' => __('Customize the Honorable in Table', 'donationsystem'),
                'desc' => __('Customize Honorable of Donated Amount in Table', 'donationsystem'),
                'id' => '_fp_donar_details_Honorable',
                'css' => 'min-width:150px;',
                'std' => 'Honorable For',
                'class' => '',
                'default' => 'Honorable For',
                'newids' => '_fp_donar_details_Honorable',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Customize the Reason in Table', 'donationsystem'),
                'desc' => __('Customize Reason of Donated Amount in Table', 'donationsystem'),
                'id' => '_fp_donar_details_reason',
                'css' => 'min-width:150px;',
                'std' => 'Reason For',
                'class' => '',
                'default' => 'Reason For',
                'newids' => '_fp_donar_details_reason',
                'type' => 'text',
                'desc_tip' => true,
            ),
            
            array(
                'type' => 'sectionend',
                'id' => '_donationsystem_table_settings',
            ),
            array(
                'name' => __('Free Product Customization', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_free_product_caption',
            ),
            array(
                'name' => __('List of Free Products Caption in Cart Page', 'donationsystem'),
                'desc' => __('Customize the List of Free Products Caption in Cart Page', 'donationsystem'),
                'id' => '_fp_donation_free_products_caption',
                'css' => '',
                'std' => 'List of Free Products',
                'class' => '',
                'default' => 'List of Free Products',
                'newids' => '_fp_donation_free_products_caption',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array('type' => 'sectionend', 'id' => '_donationsystem_free_product_caption'),
            array(
                'name' => __('Donation Rewards Table Customization', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_rewards_table_customization',
            ),
            array(
                'name' => __('Donation Amount Range Label Customization', 'donationsystem'),
                'desc' => __('Customize the Donation Amount Range Label', 'donationsystem'),
                'id' => '_fp_donation_amount_range_label',
                'css' => '',
                'std' => 'Donation Amount Range',
                'class' => '',
                'default' => 'Donation Amount Range',
                'newids' => '_fp_donation_rewards_amount_range_label',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('List of Free Products Label Customization', 'donationsystem'),
                'desc' => __('Customize the List of Free Products Caption', 'donationsystem'),
                'id' => '_fp_donation_rewards_free_product_caption',
                'css' => '',
                'std' => 'List of Free Products',
                'class' => '',
                'default' => 'List of Free Products',
                'newids' => '_fp_donation_rewards_free_product_caption',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Free Product Worth Label Customization', 'donationsystem'),
                'desc' => __('Customize the Free Product Worth Label', 'donationsystem'),
                'id' => '_fp_donation_rewards_free_product_worth',
                'css' => '',
                'std' => 'Free Product Worth',
                'class' => '',
                'default' => 'Free Product Worth',
                'newids' => '_fp_donation_rewards_free_product_worth',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array('type' => 'sectionend', 'id' => '_donationsystem_rewards_table_customization'),
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

new FP_Donation_Labels_Tab();
