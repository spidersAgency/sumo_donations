<?php

class FP_Donation_Form_Tab {

    // Construct the Donation Form
    public function __construct() {

        add_action( 'woocommerce_donationsystem_settings_tabs_array' , array ( $this , 'initialize_tab' ) ) ;

        add_action( 'woocommerce_donationsystem_settings_tabs_fp_donationsystem_form' , array ( $this , 'initialize_visual_appearance_admin_fields' ) ) ;

        add_action( 'woocommerce_update_options_fp_donationsystem_form' , array ( $this , 'update_data_from_admin_fields' ) ) ;

        if ( isset( $_POST[ 'reset_fp_donationsystem_form' ] ) ) {
            add_action( 'admin_head' , array ( $this , 'reset_option_to_donationsystem' ) ) ;
        }

        add_action( 'admin_init' , array ( $this , 'add_option_to_donationsystem' ) ) ;

        add_action( 'woocommerce_admin_field__fp_donation_predefined_buttons_product' , array ( $this , 'predefined_buttons_product' ) ) ;
        add_action( 'woocommerce_admin_field__fp_donation_predefined_buttons_cart' , array ( $this , 'predefined_buttons_cart' ) ) ;
        add_action( 'woocommerce_admin_field__fp_donation_predefined_buttons_checkout' , array ( $this , 'predefined_buttons_checkout' ) ) ;
        add_action( 'woocommerce_admin_field__fp_donation_predefined_buttons_shortcode' , array ( $this , 'predefined_buttons_shortcode' ) ) ;
        add_action( 'woocommerce_admin_field__fp_donation_predefined_buttons_flybox' , array ( $this , 'predefined_buttons_flybox' ) ) ;
        add_action( 'woocommerce_admin_field__fp_donation_form_included_selected_products' , array ( $this , 'selected_products_include' ) ) ;

        add_action( 'woocommerce_admin_field__fp_donation_form_excluded_selected_products' , array ( $this , 'selected_products_exclude' ) ) ;

        if ( isset( $_GET[ 'tab' ] ) ) {
            if ( ($_GET[ 'tab' ] == 'fp_donationsystem_form') || $_GET[ 'tab' ] == 'fp_donationsystem_flybox' ) {
                add_action( 'admin_head' , array ( $this , 'check_from_jquery' ) ) ;
            }
        }
    }

    // Initialize the Settings from Donation Form

    public static function initialize_tab( $settings_tab ) {
        if ( ! is_array( $settings_tab ) ) {
            $settings_tab = ( array ) $settings_tab ;
        }
        $settings_tab[ 'fp_donationsystem_form' ] = __( 'Donation Form' , 'donationsystem' ) ;
        return array_filter( $settings_tab ) ;
    }

    // Initialize Settings Page array

    public static function initialize_admin_fields() {
        global $woocommerce ;
        return apply_filters( 'woocommerce_donationsystem_form_settings' , array (
            array (
                'name' => __( "Donation Form Product Settings" , 'donationsystem' ) ,
                'type' => 'title' ,
                'id'   => '_fp_donationsystem_product' ,
            ) ,
            array (
                'name'     => __( 'Show Donation Form in Product Page' , 'donationsystem' ) ,
                'id'       => '_fp_donation_display_product' ,
                'css'      => '' ,
                'std'      => 'no' ,
                'class'    => '_fp_donation_form_display' ,
                'default'  => 'no' ,
                'newids'   => '_fp_donation_display_product' ,
                'type'     => 'checkbox' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'    => __( 'Title of Donation Form' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_title_product' ,
                'css'     => 'min-width:350px;' ,
                'std'     => 'Make a Donation' ,
                'class'   => '' ,
                'default' => 'Make a Donation' ,
                'newids'  => '_fp_donation_form_title_product' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Description for Donation Form' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_description_product' ,
                'css'     => '' ,
                'std'     => 'You can make a donation here' ,
                'class'   => '' ,
                'default' => 'You can make a donation here' ,
                'newids'  => '_fp_donation_form_description_product' ,
                'type'    => 'textarea' ,
            ) ,
            array (
                'name'    => __( 'Show Donation Rewards Table' , 'donationsystem' ) ,
                'id'      => '_fp_donation_display_donation_rewards_table_product' ,
                'css'     => '' ,
                'std'     => 'yes' ,
                'class'   => '' ,
                'default' => 'yes' ,
                'newids'  => '_fp_donation_display_donation_rewards_table_product' ,
                'type'    => 'checkbox' ,
            ) ,
            array (
                'name'    => __( 'Donation Field Type' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_type_product' ,
                'css'     => '' ,
                'std'     => '4' ,
                'class'   => '' ,
                'default' => '4' ,
                'newids'  => '_fp_donation_form_type_product' ,
                'type'    => 'select' ,
                'options' => array (
                    '1' => __( 'Editable Text Field' , 'donationsystem' ) ,
                    '4' => __( 'Non-Editable Text Field' , 'donationsystem' ) ,
                    '2' => __( 'Predefined Buttons' , 'donationsystem' ) ,
                    '3' => __( 'List Box' , 'donationsystem' ) ,
                ) ,
            ) ,
            array (
                'name'    => __( 'Also Display Editable Text Field' , 'donationsystem' ) ,
                'id'      => '_fp_donation_display_editable_field_product' ,
                'css'     => '' ,
                'std'     => 'no' ,
                'class'   => '' ,
                'default' => 'no' ,
                'newids'  => '_fp_donation_display_editable_field_product' ,
                'type'    => 'checkbox' ,
            ) ,
            array (
                'name'     => __( 'Default Donation Value' , 'donationsystem' ) ,
                'desc'     => __( 'Enter the Default Donation Value' , 'donationsystem' ) ,
                'id'       => '_fp_donation_default_value_product' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => '' ,
                'default'  => '' ,
                'newids'   => '_fp_donation_default_value_product' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Minimum Donation Value' , 'donationsystem' ) ,
                'desc'     => __( 'Enter Minimum Donation Value' , 'donationsystem' ) ,
                'id'       => '_fp_donation_amount_minimum_product' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => '' ,
                'default'  => '' ,
                'newids'   => '_fp_donation_amount_minimum_product' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Maximum Donation Value' , 'donationsystem' ) ,
                'desc'     => __( 'Enter Maximum Donation Value' , 'donationsystem' ) ,
                'id'       => '_fp_donation_amount_maximum_product' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => '' ,
                'default'  => '' ,
                'newids'   => '_fp_donation_amount_maximum_product' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'    => __( 'Label for Simple Form' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_simple_label_product' ,
                'css'     => '' ,
                'std'     => 'Donate {currency_symbol}' ,
                'class'   => '' ,
                'default' => 'Donate {currency_symbol}' ,
                'newids'  => "_fp_donation_form_simple_label_product" ,
                'type'    => 'textarea' ,
            ) ,
            array (
                'name'    => __( 'Donation Value Separated by commas(,)' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_value_listbox_product' ,
                'css'     => 'min-width:350px;' ,
                'std'     => '' ,
                'class'   => '' ,
                'default' => '' ,
                'newids'  => '_fp_donation_form_value_listbox_product' ,
                'type'    => 'textarea' ,
            ) ,
            array (
                'type' => '_fp_donation_predefined_buttons_product' ,
            ) ,
            array (
                'name'    => __( 'Button Background Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_bg_color_product' ,
                'css'     => '' ,
                'std'     => 'EB6F31' ,
                'class'   => 'color' ,
                'default' => 'EB6F31' ,
                'newids'  => '_fp_donation_button_bg_color_product' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Button Hover color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_hover_color_product' ,
                'css'     => '' ,
                'std'     => '00A0D3' ,
                'class'   => 'color' ,
                'default' => '00A0D3' ,
                'newids'  => '_fp_donation_button_hover_color_product' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Button Selected Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_selected_color_product' ,
                'css'     => '' ,
                'std'     => 'A0CE4D' ,
                'class'   => 'color' ,
                'default' => 'A0CE4D' ,
                'newids'  => '_fp_donation_button_selected_color_product' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Button Text Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_text_color_product' ,
                'css'     => '' ,
                'std'     => 'FFFFFF' ,
                'class'   => 'color' ,
                'default' => 'FFFFFF' ,
                'newids'  => '_fp_donation_button_text_color_product' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Show Donation Form in' , 'donationsystem' ) ,
                'id'      => '_fp_show_donation_form_in_product' ,
                'css'     => '' ,
                'std'     => '1' ,
                'class'   => '' ,
                'default' => '1' ,
                'newids'  => '_fp_show_donation_form_in_product' ,
                'type'    => 'select' ,
                'options' => array (
                    '1' => __( 'All Products' , 'donationsystem' ) ,
                    '2' => __( 'Include Selected Products' , 'donationsystem' ) ,
                    '3' => __( 'Exclude Selected Products' , 'donationsystem' ) ,
                ) ,
            ) ,
            array (
                'type' => '_fp_donation_form_included_selected_products' ,
            ) ,
            array (
                'type' => '_fp_donation_form_excluded_selected_products' ,
            ) ,
            array (
                'name'        => __( 'Custom CSS for Donation Form in Product Page' , 'donationsystem' ) ,
                'id'          => '_fp_donation_product_css' ,
                'css'         => 'min-width:350px;min-height:200px;' ,
                'placeholder' => 'Custom CSS' ,
                'std'         => '' ,
                'class'       => '' ,
                'default'     => '' ,
                'newids'      => '_fp_donation_product_css' ,
                'type'        => 'textarea' ,
                'desc_tip'    => true ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => '_fp_donationsystem_product' ) ,
            array (
                'name' => __( "Donation Form Cart Settings" , 'donationsystem' ) ,
                'type' => 'title' ,
                'id'   => '_fp_donationsystem_cart' ,
            ) ,
            array (
                'name'    => __( 'Show Donation Form in Cart Page' , 'donationsystem' ) ,
                'id'      => '_fp_donation_display_cart' ,
                'css'     => '' ,
                'std'     => 'yes' ,
                'class'   => '_fp_donation_form_display' ,
                'default' => 'yes' ,
                'newids'  => '_fp_donation_display_cart' ,
                'type'    => 'checkbox' ,
            ) ,
            array (
                'name'    => __( 'Title of Donation Form' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_title_cart' ,
                'css'     => 'min-width:350px;' ,
                'std'     => 'Make a Donation' ,
                'class'   => '' ,
                'default' => 'Make a Donation' ,
                'newids'  => '_fp_donation_form_title_cart' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Description for Donation Form' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_description_cart' ,
                'css'     => '' ,
                'std'     => 'You can make a donation here' ,
                'class'   => '' ,
                'default' => 'You can make a donation here' ,
                'newids'  => '_fp_donation_form_description_cart' ,
                'type'    => 'textarea' ,
            ) ,
            array (
                'name'    => __( 'Show Donation Rewards Table' , 'donationsystem' ) ,
                'id'      => '_fp_donation_display_donation_rewards_table_cart' ,
                'css'     => '' ,
                'std'     => 'yes' ,
                'class'   => '' ,
                'default' => 'yes' ,
                'newids'  => '_fp_donation_display_donation_rewards_table_cart' ,
                'type'    => 'checkbox' ,
            ) ,
            array (
                'name'    => __( 'Donation Field Type' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_type_cart' ,
                'css'     => '' ,
                'std'     => '1' ,
                'class'   => '' ,
                'default' => '1' ,
                'newids'  => '_fp_donation_form_type_cart' ,
                'type'    => 'select' ,
                'options' => array (
                    '1' => __( 'Editable Text Field' , 'donationsystem' ) ,
                    '4' => __( 'Non-Editable Text Field' , 'donationsystem' ) ,
                    '2' => __( 'Predefined Buttons' , 'donationsystem' ) ,
                    '3' => __( 'List Box' , 'donationsystem' ) ,
                ) ,
            ) ,
            array (
                'name'    => __( 'Also Display Editable Text Field' , 'donationsystem' ) ,
                'id'      => '_fp_donation_display_editable_field_cart' ,
                'css'     => '' ,
                'std'     => 'no' ,
                'class'   => '' ,
                'default' => 'no' ,
                'newids'  => '_fp_donation_display_editable_field_cart' ,
                'type'    => 'checkbox' ,
            ) ,
            array (
                'name'     => __( 'Default Donation Value' , 'donationsystem' ) ,
                'desc'     => __( 'Enter the Default Donation Value' , 'donationsystem' ) ,
                'id'       => '_fp_donation_default_value_cart' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => '' ,
                'default'  => '' ,
                'newids'   => '_fp_donation_default_value_cart' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Minimum Donation Value' , 'donationsystem' ) ,
                'desc'     => __( 'Enter Minimum Donation Value' , 'donationsystem' ) ,
                'id'       => '_fp_donation_amount_minimum_cart' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => '' ,
                'default'  => '' ,
                'newids'   => '_fp_donation_amount_minimum_cart' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Maximum Donation Value' , 'donationsystem' ) ,
                'desc'     => __( 'Enter Maximum Donation Value' , 'donationsystem' ) ,
                'id'       => '_fp_donation_amount_maximum_cart' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => '' ,
                'default'  => '' ,
                'newids'   => '_fp_donation_amount_maximum_cart' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'    => __( 'Label for Simple Form' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_simple_label_cart' ,
                'css'     => '' ,
                'std'     => 'Donate {currency_symbol}' ,
                'class'   => '' ,
                'default' => 'Donate {currency_symbol}' ,
                'newids'  => "_fp_donation_form_simple_label_cart" ,
                'type'    => 'textarea' ,
            ) ,
            array (
                'name'    => __( 'Donation Value Separated by commas(,)' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_value_listbox_cart' ,
                'css'     => 'min-width:350px;' ,
                'std'     => '' ,
                'class'   => '' ,
                'default' => '' ,
                'newids'  => '_fp_donation_form_value_listbox_cart' ,
                'type'    => 'textarea' ,
            ) ,
            array (
                'type' => '_fp_donation_predefined_buttons_cart' ,
            ) ,
            array (
                'name'    => __( 'Button Background Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_bg_color_cart' ,
                'css'     => '' ,
                'std'     => 'EB6F31' ,
                'class'   => 'color' ,
                'default' => 'EB6F31' ,
                'newids'  => '_fp_donation_button_bg_color_cart' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Button Hover color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_hover_color_cart' ,
                'css'     => '' ,
                'std'     => '00A0D3' ,
                'class'   => 'color' ,
                'default' => '00A0D3' ,
                'newids'  => '_fp_donation_button_hover_color_cart' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Button Selected Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_selected_color_cart' ,
                'css'     => '' ,
                'std'     => 'A0CE4D' ,
                'class'   => 'color' ,
                'default' => 'A0CE4D' ,
                'newids'  => '_fp_donation_button_selected_color_cart' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Button Text Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_text_color_cart' ,
                'css'     => '' ,
                'std'     => 'FFFFFF' ,
                'class'   => 'color' ,
                'default' => 'FFFFFF' ,
                'newids'  => '_fp_donation_button_text_color_cart' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'        => __( 'Custom CSS for Donation Form in Cart Page' , 'donationsystem' ) ,
                'id'          => '_fp_donation_cart_css' ,
                'css'         => 'min-width:350px;min-height:200px;' ,
                'placeholder' => 'Custom CSS' ,
                'std'         => '' ,
                'class'       => '' ,
                'default'     => '' ,
                'newids'      => '_fp_donation_cart_css' ,
                'type'        => 'textarea' ,
                'desc_tip'    => true ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => '_fp_donationsystem_cart' ) ,
            array (
                'name' => __( "Donation Form Checkout Settings" , 'donationsystem' ) ,
                'type' => 'title' ,
                'id'   => '_fp_donationsystem_checkout' ,
            ) ,
            array (
                'name'     => __( 'Show Donation Form in Checkout Page' , 'donationsystem' ) ,
                'id'       => '_fp_donation_display_checkout' ,
                'css'      => '' ,
                'std'      => 'no' ,
                'class'    => '_fp_donation_form_display' ,
                'default'  => 'no' ,
                'newids'   => '_fp_donation_display_checkout' ,
                'type'     => 'checkbox' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'    => __( 'Title of Donation Form' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_title_checkout' ,
                'css'     => 'min-width:350px;' ,
                'std'     => 'Make a Donation' ,
                'class'   => '' ,
                'default' => 'Make a Donation' ,
                'newids'  => '_fp_donation_form_title_checkout' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Description for Donation Form' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_description_checkout' ,
                'css'     => '' ,
                'std'     => 'You can make a donation here' ,
                'class'   => '' ,
                'default' => 'You can make a donation here' ,
                'newids'  => '_fp_donation_form_description_checkout' ,
                'type'    => 'textarea' ,
            ) ,
            array (
                'name'    => __( 'Show Donation Rewards Table' , 'donationsystem' ) ,
                'id'      => '_fp_donation_display_donation_rewards_table_checkout' ,
                'css'     => '' ,
                'std'     => 'yes' ,
                'class'   => '' ,
                'default' => 'yes' ,
                'newids'  => '_fp_donation_display_donation_rewards_table_checkout' ,
                'type'    => 'checkbox' ,
            ) ,
            array (
                'name'    => __( 'Donation Field Type' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_type_checkout' ,
                'css'     => '' ,
                'std'     => '1' ,
                'class'   => '' ,
                'default' => '1' ,
                'newids'  => '_fp_donation_form_type_checkout' ,
                'type'    => 'select' ,
                'options' => array (
                    '1' => __( 'Editable Text Field' , 'donationsystem' ) ,
                    '4' => __( 'Non-Editable Text Field' , 'donationsystem' ) ,
                    '2' => __( 'Predefined Buttons' , 'donationsystem' ) ,
                    '3' => __( 'List Box' , 'donationsystem' ) ,
                ) ,
            ) ,
            array (
                'name'    => __( 'Also Display Editable Text Field' , 'donationsystem' ) ,
                'id'      => '_fp_donation_display_editable_field_checkout' ,
                'css'     => '' ,
                'std'     => 'no' ,
                'class'   => '' ,
                'default' => 'no' ,
                'newids'  => '_fp_donation_display_editable_field_checkout' ,
                'type'    => 'checkbox' ,
            ) ,
            array (
                'name'     => __( 'Default Donation Value' , 'donationsystem' ) ,
                'desc'     => __( 'Enter the Default Donation Value' , 'donationsystem' ) ,
                'id'       => '_fp_donation_default_value_checkout' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => '' ,
                'default'  => '' ,
                'newids'   => '_fp_donation_default_value_checkout' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Minimum Donation Value' , 'donationsystem' ) ,
                'desc'     => __( 'Enter Minimum Donation Value' , 'donationsystem' ) ,
                'id'       => '_fp_donation_amount_minimum_checkout' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => '' ,
                'default'  => '' ,
                'newids'   => '_fp_donation_amount_minimum_checkout' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Maximum Donation Value' , 'donationsystem' ) ,
                'desc'     => __( 'Enter Maximum Donation Value' , 'donationsystem' ) ,
                'id'       => '_fp_donation_amount_maximum_checkout' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => '' ,
                'default'  => '' ,
                'newids'   => '_fp_donation_amount_maximum_checkout' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'    => __( 'Label for Simple Form' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_simple_label_checkout' ,
                'css'     => '' ,
                'std'     => 'Donate {currency_symbol}' ,
                'class'   => '' ,
                'default' => 'Donate {currency_symbol}' ,
                'newids'  => "_fp_donation_form_simple_label_checkout" ,
                'type'    => 'textarea' ,
            ) ,
            array (
                'name'    => __( 'Donation Value Separated by commas(,)' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_value_listbox_checkout' ,
                'css'     => 'min-width:350px;' ,
                'std'     => '' ,
                'class'   => '' ,
                'default' => '' ,
                'newids'  => '_fp_donation_form_value_listbox_checkout' ,
                'type'    => 'textarea' ,
            ) ,
            array (
                'type' => '_fp_donation_predefined_buttons_checkout' ,
            ) ,
            array (
                'name'    => __( 'Button Background Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_bg_color_checkout' ,
                'css'     => '' ,
                'std'     => 'EB6F31' ,
                'class'   => 'color' ,
                'default' => 'EB6F31' ,
                'newids'  => '_fp_donation_button_bg_color_checkout' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Button Hover color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_hover_color_checkout' ,
                'css'     => '' ,
                'std'     => '00A0D3' ,
                'class'   => 'color' ,
                'default' => '00A0D3' ,
                'newids'  => '_fp_donation_button_hover_color_checkout' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Button Selected Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_selected_color_checkout' ,
                'css'     => '' ,
                'std'     => 'A0CE4D' ,
                'class'   => 'color' ,
                'default' => 'A0CE4D' ,
                'newids'  => '_fp_donation_button_selected_color_checkout' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Button Text Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_text_color_checkout' ,
                'css'     => '' ,
                'std'     => 'FFFFFF' ,
                'class'   => 'color' ,
                'default' => 'FFFFFF' ,
                'newids'  => '_fp_donation_button_text_color_checkout' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'        => __( 'Custom CSS for Donation Form in Checkout Page' , 'donationsystem' ) ,
                'id'          => '_fp_donation_checkout_css' ,
                'css'         => 'min-width:350px;min-height:200px;' ,
                'placeholder' => 'Custom CSS' ,
                'std'         => '' ,
                'class'       => '' ,
                'default'     => '' ,
                'newids'      => '_fp_donation_checkout_css' ,
                'type'        => 'textarea' ,
                'desc_tip'    => true ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => '_fp_donationsystem_checkout' ) ,
            array (
                'name' => __( "Donation Form Shortcode Settings" , 'donationsystem' ) ,
                'type' => 'title' ,
                'id'   => '_fp_donationsystem_shortcode' ,
            ) ,
            //For Shortcode
            array (
                'name'     => __( 'Show Donation Form in Shortcode' , 'donationsystem' ) ,
                'id'       => '_fp_donation_display_shortcode' ,
                'css'      => '' ,
                'std'      => 'yes' ,
                'class'    => '_fp_donation_form_display' ,
                'default'  => 'yes' ,
                'newids'   => '_fp_donation_display_shortcode' ,
                'type'     => 'checkbox' ,
                'desc_tip' => true ,
            ) ,
            array (
                'type' => 'fp_info_shortcode' ,
            ) ,
            array (
                'name'    => __( 'Title of Donation Form' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_title_shortcode' ,
                'css'     => 'min-width:350px;' ,
                'std'     => 'Make a Donation' ,
                'class'   => '' ,
                'default' => 'Make a Donation' ,
                'newids'  => '_fp_donation_form_title_shortcode' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Description for Donation Form' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_description_shortcode' ,
                'css'     => '' ,
                'std'     => 'You can make a donation here' ,
                'class'   => '' ,
                'default' => 'You can make a donation here' ,
                'newids'  => '_fp_donation_form_description_shortcode' ,
                'type'    => 'textarea' ,
            ) ,
            array (
                'name'    => __( 'Show Donation Rewards Table' , 'donationsystem' ) ,
                'id'      => '_fp_donation_display_donation_rewards_table_shortcode' ,
                'css'     => '' ,
                'std'     => 'yes' ,
                'class'   => '' ,
                'default' => 'yes' ,
                'newids'  => '_fp_donation_display_donation_rewards_table_shortcode' ,
                'type'    => 'checkbox' ,
            ) ,
            array (
                'name'    => __( 'Donation Field Type' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_type_shortcode' ,
                'css'     => '' ,
                'std'     => '2' ,
                'class'   => '' ,
                'default' => '2' ,
                'newids'  => '_fp_donation_form_type_shortcode' ,
                'type'    => 'select' ,
                'options' => array (
                    '1' => __( 'Editable Text Field' , 'donationsystem' ) ,
                    '4' => __( 'Non-Editable Text Field' , 'donationsystem' ) ,
                    '2' => __( 'Predefined Buttons' , 'donationsystem' ) ,
                    '3' => __( 'List Box' , 'donationsystem' ) ,
                ) ,
            ) ,
            array (
                'name'    => __( 'Also Display Editable Text Field' , 'donationsystem' ) ,
                'id'      => '_fp_donation_display_editable_field_shortcode' ,
                'css'     => '' ,
                'std'     => 'no' ,
                'class'   => '' ,
                'default' => 'no' ,
                'newids'  => '_fp_donation_display_editable_field_shortcode' ,
                'type'    => 'checkbox' ,
            ) ,
            array (
                'name'     => __( 'Default Donation Value' , 'donationsystem' ) ,
                'desc'     => __( 'Enter the Default Donation Value' , 'donationsystem' ) ,
                'id'       => '_fp_donation_default_value_shortcode' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => '' ,
                'default'  => '' ,
                'newids'   => '_fp_donation_default_value_shortcode' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Minimum Donation Value' , 'donationsystem' ) ,
                'desc'     => __( 'Enter Minimum Donation Value' , 'donationsystem' ) ,
                'id'       => '_fp_donation_amount_minimum_shortcode' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => '' ,
                'default'  => '' ,
                'newids'   => '_fp_donation_amount_minimum_shortcode' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Maximum Donation Value' , 'donationsystem' ) ,
                'desc'     => __( 'Enter Maximum Donation Value' , 'donationsystem' ) ,
                'id'       => '_fp_donation_amount_maximum_shortcode' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => '' ,
                'default'  => '' ,
                'newids'   => '_fp_donation_amount_maximum_shortcode' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'    => __( 'Label for Simple Form' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_simple_label_shortcode' ,
                'css'     => '' ,
                'std'     => 'Donate {currency_symbol}' ,
                'class'   => '' ,
                'default' => 'Donate {currency_symbol}' ,
                'newids'  => "_fp_donation_form_simple_label_shortcode" ,
                'type'    => 'textarea' ,
            ) ,
            array (
                'name'    => __( 'Donation Value Separated by commas(,)' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_value_listbox_shortcode' ,
                'css'     => 'min-width:350px;' ,
                'std'     => '' ,
                'class'   => '' ,
                'default' => '' ,
                'newids'  => '_fp_donation_form_value_listbox_shortcode' ,
                'type'    => 'textarea' ,
            ) ,
            array (
                'type' => '_fp_donation_predefined_buttons_shortcode' ,
            ) ,
            array (
                'name'    => __( 'Button Background Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_bg_color_shortcode' ,
                'css'     => '' ,
                'std'     => 'EB6F31' ,
                'class'   => 'color' ,
                'default' => 'EB6F31' ,
                'newids'  => '_fp_donation_button_bg_color_shortcode' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Button Hover color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_hover_color_shortcode' ,
                'css'     => '' ,
                'std'     => '00A0D3' ,
                'class'   => 'color' ,
                'default' => '00A0D3' ,
                'newids'  => '_fp_donation_button_hover_color_shortcode' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Button Selected Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_selected_color_shortcode' ,
                'css'     => '' ,
                'std'     => 'A0CE4D' ,
                'class'   => 'color' ,
                'default' => 'A0CE4D' ,
                'newids'  => '_fp_donation_button_selected_color_shortcode' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Button Text Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_button_text_color_shortcode' ,
                'css'     => '' ,
                'std'     => 'FFFFFF' ,
                'class'   => 'color' ,
                'default' => 'FFFFFF' ,
                'newids'  => '_fp_donation_button_text_color_shortcode' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'    => __( 'Donation Form Background Color' , 'donationsystem' ) ,
                'id'      => '_fp_donation_form_background_color' ,
                'css'     => '' ,
                'std'     => 'fff' ,
                'class'   => 'color' ,
                'default' => 'fff' ,
                'newids'  => '_fp_donation_form_background_color' ,
                'type'    => 'text' ,
            ) ,
            array (
                'name'        => __( 'Custom CSS for Donation Form in Shortcode' , 'donationsystem' ) ,
                'id'          => '_fp_donation_shortcode_css' ,
                'css'         => 'min-width:350px;min-height:200px;' ,
                'placeholder' => 'Custom CSS' ,
                'std'         => '' ,
                'class'       => '' ,
                'default'     => '' ,
                'newids'      => '_fp_donation_shortcode_css' ,
                'type'        => 'textarea' ,
                'desc_tip'    => true ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => '_fp_donationsystem_shortcode' ) ,
                ) ) ;
    }

    public static function initialize_visual_appearance_admin_fields() {
        woocommerce_admin_fields( self::initialize_admin_fields() ) ;
    }

    public static function update_data_from_admin_fields() {
        woocommerce_update_options( self::initialize_admin_fields() ) ;
        $listofarray = array ( 'product' , 'cart' , 'checkout' , 'shortcode' ) ;
        foreach ( $listofarray as $eacharray ) {
            update_option( 'fp_predefined_buttons_' . $eacharray , $_POST[ 'fp_predefined_buttons_' . $eacharray ] ) ;
            update_option( 'fp_predefined_buttons_columns_' . $eacharray , $_POST[ 'fp_predefined_buttons_columns_' . $eacharray ] ) ;
        }
        update_option( '_fp_donation_form_included_selected_products' , $_POST[ '_fp_donation_form_included_selected_products' ] ) ;
        update_option( '_fp_donation_form_excluded_selected_products' , $_POST[ '_fp_donation_form_excluded_selected_products' ] ) ;
    }

    public static function add_option_to_donationsystem() {
        $newdonation_array = array () ;
        foreach ( self::initialize_admin_fields() as $setting )
            if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
            }
        $listofarray            = array ( 'product' , 'cart' , 'checkout' , 'shortcode' ) ;
        $list_of_donation_value = array ( 10 , 20 , 30 , 40 , 50 , 60 , 70 , 80 , 90 ) ;
        foreach ( $listofarray as $eacharray ) {
            add_option( 'fp_predefined_buttons_columns_' . $eacharray , '3' ) ;
            foreach ( $list_of_donation_value as $keys => $newvalues ) {
                $newdonation_array[ $keys ] = $newvalues ;
            }
            add_option( 'fp_predefined_buttons_' . $eacharray , $newdonation_array ) ;
        }
    }

    public static function reset_option_to_donationsystem() {
        $newdonation_array = array () ;
        foreach ( self::initialize_admin_fields()as $setting ) {
            if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                delete_option( $setting[ 'newids' ] ) ;
                add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
            }
        }
        $listofarray            = array ( 'product' , 'cart' , 'checkout' , 'shortcode' ) ;
        $list_of_donation_value = array ( 10 , 20 , 30 , 40 , 50 , 60 , 70 , 80 , 90 ) ;
        foreach ( $listofarray as $eacharray ) {
            delete_option( 'fp_predefined_buttons_' . $eacharray ) ;
            delete_option( 'fp_predefined_buttons_columns_' . $eacharray ) ;
            add_option( 'fp_predefined_buttons_columns_' . $eacharray , '3' ) ;

            foreach ( $list_of_donation_value as $keys => $newvalues ) {
                $newdonation_array[ $keys ] = $newvalues ;
            }
            add_option( 'fp_predefined_buttons_' . $eacharray , $newdonation_array ) ;
        }
    }

    // List of Predefined Buttons
    public static function predefined_buttons_product() {
        echo self::predefined_buttons( 'product' ) ;
    }

    // List of Predefined Buttons for Cart
    public static function predefined_buttons_cart() {
        echo self::predefined_buttons( 'cart' ) ;
    }

    // List of Predefined Buttons for Checkout
    public static function predefined_buttons_checkout() {
        echo self::predefined_buttons( 'checkout' ) ;
    }

    // List of Predefined Buttons for Shortcode
    public static function predefined_buttons_shortcode() {
        echo self::predefined_buttons( 'shortcode' ) ;
    }

    // Show Predefined Buttons in Flybox
    public static function predefined_buttons_flybox() {
        echo self::predefined_buttons( 'flybox' ) ;
    }

    // Show Donation Table Predefined Buttons
    public static function predefined_buttons( $suffix ) {
        ob_start() ;
        ?>
        <tr>
            <th class="titledesc" scope="row"><?php _e( 'Donation Value in Predefined Buttons' , 'donationsystem' ) ; ?></th>
            <td id="fp_predefined_buttons_<?php echo $suffix ; ?>">

                <?php
                //var_dump(get_option('fp_predefined_buttons_' . $suffix));
                for ( $i = 0 ; $i < 12 ; $i ++ ) {
                    ?>
                    <input type="text"   name="fp_predefined_buttons_<?php echo $suffix ?>[<?php echo $i ; ?>]" value="<?php
                    $predefined_buttons = get_option( 'fp_predefined_buttons_' . $suffix ) ;
                    echo isset( $predefined_buttons[ $i ] ) ? $predefined_buttons[ $i ] : "" ;
                    ?>"/>

                    <?php
                    if ( ($i == 2) || ($i == 5) || ($i == 8) ) {
                        echo "<br>" ;
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <th class="titledesc" scope="row">
                <?php _e( 'Enter Number of Columns for to display Predefined Buttons(Frontend)' , 'donationsystem' ) ; ?>
            </th>
            <td>
                <input type="text" class="fp_predefined_buttons_columns_<?php echo $suffix ; ?>" name="fp_predefined_buttons_columns_<?php echo $suffix ; ?>" value="<?php echo get_option( 'fp_predefined_buttons_columns_' . $suffix ) ; ?>"/>
            </td>
        </tr>
        <?php
        return ob_get_clean() ;
    }

    // Selected Products Include
    public static function selected_products_include() {
        ?>
        <tr>
            <th>
                <?php _e( 'Included Selected Products' , 'donationsystem' ) ; ?>
            </th>
            <td>
                <?php
                echo FP_Donation_Common_Function::search_product_selection( '2' , true , '_fp_donation_form_included_selected_products' , '' , '' , '' ) ;
                ?>
            </td>
        </tr>
        <?php
    }

    // Selected Products Exclude

    public static function selected_products_exclude() {
        ?>
        <tr>
            <th>
                <?php _e( 'Excluded Selected Products' , 'donationsystem' ) ; ?>
            </th>
            <td>
                <?php echo FP_Donation_Common_Function::search_product_selection( '2' , true , '_fp_donation_form_excluded_selected_products' , '' , '' , '' ) ; ?>
            </td>
        </tr>
        <?php
    }

    //
    public static function check_from_jquery() {
        ?>
        <script type="text/javascript">
            jQuery( function () {
                // jQuery('#_fp_donation_form_included_selected_products');
                var productvalue = jQuery( '#_fp_show_donation_form_in_product' ).val() ;
                if ( productvalue === '1' ) {
                    jQuery( '#_fp_donation_form_included_selected_products' ).parent().parent().hide() ;
                    jQuery( '#_fp_donation_form_excluded_selected_products' ).parent().parent().hide() ;
                } else if ( productvalue === '2' ) {
                    jQuery( '#_fp_donation_form_included_selected_products' ).parent().parent().show() ;
                    jQuery( '#_fp_donation_form_excluded_selected_products' ).parent().parent().hide() ;
                } else {
                    jQuery( '#_fp_donation_form_included_selected_products' ).parent().parent().hide() ;
                    jQuery( '#_fp_donation_form_excluded_selected_products' ).parent().parent().show() ;
                }

        <?php
        $array = array ( 'product' , 'cart' , 'checkout' , 'shortcode' , 'flybox' ) ;
        foreach ( $array as $eacharray ) {
            ?>
                    // Type Selection for Donation Form
                    var producttype = jQuery( '#_fp_donation_form_type_<?php echo $eacharray ; ?>' ).val() ;
                    //alert(producttype);
                    if ( ( producttype === '1' ) || ( producttype === '4' ) ) {
                        // Default Values
                        jQuery( '#_fp_donation_default_value_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                        jQuery( '#_fp_donation_amount_minimum_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                        jQuery( '#_fp_donation_amount_maximum_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                        // Testing Values for Product
                        jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                        jQuery( '#_fp_donation_form_value_listbox_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        jQuery( '#fp_predefined_buttons_<?php echo $eacharray ; ?>' ).parent().hide() ;
                        jQuery( '#_fp_donation_button_bg_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        jQuery( '#_fp_donation_button_text_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        jQuery( '#_fp_donation_button_hover_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        jQuery( '#_fp_donation_button_selected_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;

                        jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).closest( 'tr' ).hide() ;

                    } else if ( producttype === '2' ) {

                        jQuery( '#_fp_donation_default_value_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        jQuery( '#_fp_donation_amount_minimum_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        jQuery( '#_fp_donation_amount_maximum_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;

                        if ( jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).is( ':checked' ) ) {
                            jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                        } else {
                            jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        }
                        jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).change( function (  ) {
                            if ( jQuery( this ).is( ':checked' ) ) {
                                jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                            } else {
                                jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            }
                        } ) ;
                        jQuery( '#_fp_donation_form_value_listbox_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        jQuery( '#fp_predefined_buttons_<?php echo $eacharray ; ?>' ).parent().show() ;
                        //Color Picker
                        jQuery( '#_fp_donation_button_bg_color_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                        jQuery( '#_fp_donation_button_text_color_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                        jQuery( '#_fp_donation_button_hover_color_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                        jQuery( '#_fp_donation_button_selected_color_<?php echo $eacharray ; ?>' ).parent().parent().show() ;

                        jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).closest( 'tr' ).show() ;
                    } else {

                        jQuery( '#_fp_donation_default_value_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        jQuery( '#_fp_donation_amount_minimum_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        jQuery( '#_fp_donation_amount_maximum_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        if ( jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).is( ':checked' ) ) {
                            jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                        } else {
                            jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        }
                        jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).change( function (  ) {
                            if ( jQuery( this ).is( ':checked' ) ) {
                                jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                            } else {
                                jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            }
                        } ) ;
                        jQuery( '#_fp_donation_form_value_listbox_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                        jQuery( '#fp_predefined_buttons_<?php echo $eacharray ; ?>' ).parent().hide() ;
                        jQuery( '#_fp_donation_button_bg_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        jQuery( '#_fp_donation_button_text_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        jQuery( '#_fp_donation_button_hover_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                        jQuery( '#_fp_donation_button_selected_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;

                        jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).closest( 'tr' ).show() ;
                    }

                    // On upon On Change Event trigger something with donation form
                    jQuery( document ).on( 'change' , '#_fp_donation_form_type_<?php echo $eacharray ; ?>' , function () {
                        var donationtype = jQuery( this ).val() ;
                        if ( ( donationtype === '1' ) || ( donationtype === '4' ) ) {
                            jQuery( '#_fp_donation_default_value_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                            jQuery( '#_fp_donation_amount_minimum_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                            jQuery( '#_fp_donation_amount_maximum_<?php echo $eacharray ; ?>' ).parent().parent().show() ;

                            jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                            jQuery( '#_fp_donation_form_value_listbox_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            jQuery( '#fp_predefined_buttons_<?php echo $eacharray ; ?>' ).parent().hide() ;
                            jQuery( '#_fp_donation_button_bg_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            jQuery( '#_fp_donation_button_text_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            jQuery( '#_fp_donation_button_hover_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            jQuery( '#_fp_donation_button_selected_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;

                            jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).closest( 'tr' ).hide() ;
                        } else if ( donationtype === '2' ) {

                            jQuery( '#_fp_donation_default_value_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            jQuery( '#_fp_donation_amount_minimum_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            jQuery( '#_fp_donation_amount_maximum_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            if ( jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).is( ':checked' ) ) {
                                jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                            } else {
                                jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            }
                            jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).change( function (  ) {
                                if ( jQuery( this ).is( ':checked' ) ) {
                                    jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                                } else {
                                    jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                                }
                            } ) ;
                            jQuery( '#_fp_donation_form_value_listbox_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            jQuery( '#fp_predefined_buttons_<?php echo $eacharray ; ?>' ).parent().show() ;
                            //Color Picker
                            jQuery( '#_fp_donation_button_bg_color_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                            jQuery( '#_fp_donation_button_text_color_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                            jQuery( '#_fp_donation_button_hover_color_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                            jQuery( '#_fp_donation_button_selected_color_<?php echo $eacharray ; ?>' ).parent().parent().show() ;

                            jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).closest( 'tr' ).show() ;

                        } else {
                            jQuery( '#_fp_donation_default_value_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            jQuery( '#_fp_donation_amount_minimum_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            jQuery( '#_fp_donation_amount_maximum_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;

                            jQuery( '#_fp_donation_form_value_listbox_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                            if ( jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).is( ':checked' ) ) {
                                jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                            } else {
                                jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            }
                            jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).change( function (  ) {
                                if ( jQuery( this ).is( ':checked' ) ) {
                                    jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().show() ;
                                } else {
                                    jQuery( '#_fp_donation_form_simple_label_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                                }
                            } ) ;
                            jQuery( '#_fp_donation_button_bg_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            jQuery( '#_fp_donation_button_text_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            jQuery( '#_fp_donation_button_hover_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;
                            jQuery( '#_fp_donation_button_selected_color_<?php echo $eacharray ; ?>' ).parent().parent().hide() ;

                            jQuery( '#fp_predefined_buttons_<?php echo $eacharray ; ?>' ).parent().hide() ;

                            jQuery( '#_fp_donation_display_editable_field_<?php echo $eacharray ; ?>' ).closest( 'tr' ).show() ;
                        }
                    } ) ;
        <?php } ?>

                jQuery( document ).on( 'change' , '#_fp_show_donation_form_in_product' , function () {
                    var currentvalue = jQuery( this ).val() ;
                    if ( currentvalue === '1' ) {
                        jQuery( '#_fp_donation_form_included_selected_products' ).parent().parent().hide() ;
                        jQuery( '#_fp_donation_form_excluded_selected_products' ).parent().parent().hide() ;
                    } else if ( currentvalue === '2' ) {
                        jQuery( '#_fp_donation_form_included_selected_products' ).parent().parent().show() ;
                        jQuery( '#_fp_donation_form_excluded_selected_products' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#_fp_donation_form_included_selected_products' ).parent().parent().hide() ;
                        jQuery( '#_fp_donation_form_excluded_selected_products' ).parent().parent().show() ;
                    }
                } ) ;
            } ) ;
        </script>
        <?php
    }

}

new FP_Donation_Form_Tab() ;
