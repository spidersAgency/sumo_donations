<?php

class FP_DonationSystem_General_Tab {

// Construct the Class
    public function __construct() {

        add_action('woocommerce_donationsystem_settings_tabs_array', array($this, 'initialize_tab'));

        add_action('woocommerce_donationsystem_settings_tabs_fp_donationsystem', array($this, 'initialize_visual_appearance_admin_fields'));

        add_action('woocommerce_update_options_fp_donationsystem', array($this, 'update_data_from_admin_fields'));

        if (isset($_POST['reset_fp_donationsystem'])) {
            add_action('admin_head', array($this, 'reset_option_to_donationsystem'));
        }

        add_action('woocommerce_admin_field__fp_donation_new_product', array($this, 'initialize_button_to_make_new_product'));

        add_action('woocommerce_admin_field__fp_choose_existing_product', array($this, 'initialize_function_for_single_selection_product'));

        add_action('admin_head', array($this, 'initialize_function_to_alter_the_new_product'));

        add_action('wp_ajax_donation_create_new_product', array($this, 'ajax_requesting_donation_system'));

        add_action('admin_init', array($this, 'add_option_to_donationsystem'));

        add_action('woocommerce_admin_field_fp_info_shortcode', array($this, 'info_shortcode_donation_form'));

        add_action('woocommerce_admin_field_fp_info_shortcode_table', array($this, 'info_shortcode_donation_table'));
    }

    public static function initialize_tab( $settings_tab ) {
        if(!is_array($settings_tab)){
            $settings_tab = (array)$settings_tab;
        }
        $settings_tab['fp_donationsystem'] = __('General', 'donationsystem');
        return array_filter($settings_tab);
    }

    public static function initialize_admin_fields() {
        global $woocommerce;
        return apply_filters('woocommerce_donationsystem_general_settings', array(
            array(
                'name' => __('Donation Product Settings', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_general_settings'
            ),
            array(
                'name' => __('Donation Product Setup', 'donationsystem'),
                'desc' => __('Choose Donation Product from Existing/New', 'donationsystem'),
                'id' => '_fp_donation_product_selection',
                'css' => 'min-width:150px;',
                'std' => '1',
                'class' => '_fp_donation_product_selection',
                'default' => '1',
                'newids' => '_fp_donation_product_selection',
                'type' => 'select',
                'options' => array(
                    '1' => __('Existing Product', 'donationsystem'),
                    '2' => __('Create New Product', 'donationsystem'),
                ),
                'desc_tip' => true,
            ),
            array(
                'name' => __('Title for New Donation Product', 'donationsystem'),
                'desc' => __('Enter Title for New Donation Product', 'donationsystem'),
                'id' => '_fp_donation_new_product_title',
                'css' => 'min-width:150px;',
                'std' => 'Donation',
                'class' => '',
                'default' => 'Donation',
                'newids' => '_fp_donation_new_product_title',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Create New Product', 'donationsystem'),
                'type' => '_fp_donation_new_product',
            ),
            array(
                'name' => __('Choose Existing Product for Donation', 'donationsystem'),
                'id' => '_fp_donation_existing_product',
                'css' => 'min-width:150px;',
                'std' => '',
                'class' => '',
                'default' => '',
                'newids' => '_fp_donation_existing_product',
                'type' => '_fp_choose_existing_product',
                'desc_tip' => false,
            ),
//            array(
//                'name' => __('Product ID that should be used for Donation', 'donationsystem'),
//                'desc' => __('Choose Product ID that should be used for Donation', 'donationsystem'),
//                'id' => '_fp_donation_existing_id',
//                'css' => 'min-width:150px;',
//                'std' => '',
//                'class' => '',
//                'default' => '',
//                'newids' => '_fp_donation_existing_id',
//                'type' => 'text',
//                'desc_tip' => true,
//            ),
            array(
                'type' => 'sectionend',
                'id' => '_donationsystem_general_settings'
            ),
            array(
                'name' => __("Donation Restriction Settings", 'donationsystem'),
                'type' => 'title',
                'id' => '_fp_donation_restriction_settings',
            ),
            array(
                'name' => __('Hide Donation Form & Fly Box when Donation product already is in Cart', 'donationsystem'),
                'id' => '_fp_hide_donation_form_when_dp_aisin_cart',
                'css' => '',
                'std' => 'yes',
                'class' => '',
                'default' => 'yes',
                'newids' => '_fp_hide_donation_form_when_dp_aisin_cart',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array(
                'type' => 'sectionend',
                'id' => '_fp_donation_restriction_settings',
            ),
            array(
                'name' => __("Donation Settings for Manual", 'donationsystem'),
                'type' => 'title',
                'id' => '_fp_donation_settings_manual',
            ),
            array(
                'name' => __('Force Manual Donation', 'donationsystem'),
                'id' => '_fp_force_donation_manual',
                'css' => '',
                'std' => 'no',
                'class' => '',
                'default' => 'no',
                'newids' => '_fp_force_donation_manual',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array(
                'type' => 'sectionend',
                'id' => '_fp_donation_settings_manual',
            ),
//            array(
//                'name' => __('Donation Value Settings', 'donationsystem'),
//                'type' => 'title',
//                'id' => '_donationsystem_amount_settings',
//            ),
            array(
                'name' => __('Donation Table Shortcode Settings', 'donationsystem'),
                'type' => 'title',
                'id' => '_fp_donation_product_table_settings',
            ),
            array(
                'name' => __('Show Donation Table using Shortcode', 'donationsystem'),
                'id' => '_fp_donation_display_table',
                'css' => '',
                'std' => 'yes',
                'class' => '',
                'default' => 'yes',
                'newids' => '_fp_donation_display_table',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array(
                'type' => 'fp_info_shortcode_table',
            ),
            array(
                'name' => __('Show S.No Column from Donation Table', 'donationsystem'),
                'id' => '_fp_hide_donation_table_sno',
                'css' => '',
                'std' => 'yes',
                'class' => '',
                'default' => 'yes',
                'newids' => '_fp_hide_donation_table_sno',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Show Donor Name Column from Donation Table', 'donationsystem'),
                'id' => '_fp_hide_donation_table_name',
                'css' => '',
                'std' => 'yes',
                'class' => '',
                'default' => 'yes',
                'newids' => '_fp_hide_donation_table_name',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Show Donor Email Column from Donation Table', 'donationsystem'),
                'id' => '_fp_hide_donation_table_email',
                'css' => '',
                'std' => 'yes',
                'class' => '',
                'default' => 'yes',
                'newids' => '_fp_hide_donation_table_email',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Show Donated Amount Column from Donation Table', 'donationsystem'),
                'id' => '_fp_hide_donation_table_amount',
                'css' => '',
                'std' => 'yes',
                'class' => '',
                'default' => 'yes',
                'newids' => '_fp_hide_donation_table_amount',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Show Status Column from Donation Table', 'donationsystem'),
                'id' => '_fp_hide_donation_table_status',
                'css' => '',
                'std' => 'yes',
                'class' => '',
                'default' => 'yes',
                'newids' => '_fp_hide_donation_table_status',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array('type' => 'sectionend', 'id' => '_fp_donation_product_table_settings'),
            array(
                'name' => __('Donate Button Settings', 'donationsystem'),
                'type' => 'title',
                'id' => '_fp_donation_button_settings',
            ),
            array(
                'name' => __('Add Custom Class Name for Donate Button', 'donationsystem'),
                'type' => 'textarea',
                'id' => '_fp_donation_custom_class_name',
                'css' => '',
                'std' => '',
                'class' => '',
                'default' => '',
                'newids' => '_fp_donation_custom_class_name',
                'desc' => __('Enter Class Name for Donate Button and you can add multiple class name with one space', 'donationsystem'),
                'desc_tip' => true,
            ),
            array('type' => 'sectionend', 'id' => '_fp_donation_button_settings'),
        ));
    }

    public static function initialize_visual_appearance_admin_fields() {
        woocommerce_admin_fields(self::initialize_admin_fields());
    }

    public static function update_data_from_admin_fields() {
        woocommerce_update_options(self::initialize_admin_fields());
        update_option('ds_select_particular_products', $_POST['ds_select_particular_products']);
    }

    public static function info_shortcode_donation_form() {
        ?>
        <tr>
            <td class="fp_info_donation_form">
                <h3>[fp_donation_form]</h3>
                <p><?php _e('Use this Shortcode in any Post/Page to display Donation Form', 'donationsystem'); ?></p>
            </td>
        </tr>
        <?php
    }

    public static function info_shortcode_donation_table() {
        ?>
        <tr>
            <td class="fp_info_donation_table">
                <h3>[fp_donation_table]</h3>
                <p><?php _e('Use this Shortcode in any Post/Page to display Donation Table', 'donationsystem'); ?></p>
            </td>
        </tr>
        <?php
    }

    public static function initialize_button_to_make_new_product() {
        ob_start();
        ?>
        <tr>
            <th>
                <?php _e('Create New Donation Product', 'donationsystem'); ?>
            </th>
            <td>
                <input type="submit" class="fp_donation_new_product button-primary" value="Create New Donation Product" name="fp_donation_new_product"/>

            </td>
        </tr>

        <?php
    }

// Choose Single Selection New Product
    public static function initialize_function_for_single_selection_product() {
        global $woocommerce;
        ob_start();
        ?>
        <tr valign="top">
            <th class="titledesc" scope="row">
                <label for="ds_select_particular_products"><?php _e("Product that should be used for Donation (Product should be non-taxable and non-shippable)", 'donationsystem'); ?></label>
            </th>
            <td class="forminp forminp-select">
                <?php
                $product_and_variation = '2';
                $multiple = false;
                $name = "ds_select_particular_products";
                $iteration = '';
                $value = '';
                $subname = '';
                echo FP_Donation_Common_Function::search_product_selection($product_and_variation, $multiple, $name, $iteration, $value, $subname);
                ?>
            </td>
        </tr>
        <?php
    }

// Admin Head
    public static function initialize_function_to_alter_the_new_product() {
        if (isset($_GET['page'])) {
            if ($_GET['page'] == 'donationsystem') {
                ?>
                <script type="text/javascript">
                    jQuery(function () {
                        jQuery(document).on('change', '#_fp_donation_product_selection', function () {
                            var value = jQuery(this).val();
                            if (value === '1') {
                                // Existing Product ID
                                //jQuery('#_fp_donation_existing_id').parent().parent().show();
                                jQuery('.fp_donation_new_product').parent().parent().hide();
                                jQuery('#_fp_donation_new_product_title').parent().parent().hide();

                            } else {
                                // New Product Creation
                                // jQuery('#_fp_donation_existing_id').parent().parent().show();
                                jQuery('.fp_donation_new_product').parent().parent().show();
                                jQuery('#_fp_donation_new_product_title').parent().parent().show();
                            }
                        });

                        var newvalue = jQuery('#_fp_donation_product_selection').val();

                        if (newvalue === '1') {
                            // Existing Product ID
                            // jQuery('#_fp_donation_existing_id').parent().parent().show();
                            jQuery('.fp_donation_new_product').parent().parent().hide();
                            jQuery('#_fp_donation_new_product_title').parent().parent().hide();
                        } else {
                            // New Product Creation
                            //   jQuery('#_fp_donation_existing_id').parent().parent().show();
                            jQuery('.fp_donation_new_product').parent().parent().show();
                            jQuery('#_fp_donation_new_product_title').parent().parent().show();
                        }


                        jQuery('.fp_donation_new_product').click(function () {
                            jQuery(this).attr('disabled', 'disabled');
                            var getvalue = jQuery('#_fp_donation_new_product_title').val();
                            var dataparam = ({
                                action: 'donation_create_new_product',
                                title: getvalue
                            });
                            jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                    function (response) {
                                        var myproductid = response.productid;
                                        var myproductname = response.productname;
                                        jQuery('body').trigger('wc-enhanced-select-init');
                                        jQuery('.fp_donation_new_product').removeAttr('disabled');
                                        location.reload(true);
                                    }, 'json');

                            return false;
                        });



                    });
                </script>
                <?php
                // echo self::show_hide_donation_field();
            }
        }
    }

    // Show/Hide the Field of Donation
    public static function show_hide_donation_field() {
        if (isset($_GET['page']) || isset($_GET['tab'])) {
            if (($_GET['page'] == 'donationsystem') || ($_GET['tab'] == 'fp_donationsystem')) {
                ob_start();
                ?>
                <script type="text/javascript">
                    jQuery(function () {
                        //because of ID there is no other way to loop each checkbox and hence below is final for now
                        var checkbox_cart = jQuery('#_fp_donation_display_cart').is(":checked");
                        var checkbox_checkout = jQuery('#_fp_donation_display_checkout').is(":checked");
                        var checkbox_product = jQuery('#_fp_donation_display_product').is(":checked");
                        var checkbox_shortcode = jQuery('#_fp_donation_display_shortcode').is(":checked");

                        // For Table Settings
                        var checkbox_donationtable = jQuery('#_fp_donation_display_table').is(":checked");

                        if (checkbox_cart) {
                            jQuery('#_fp_donation_cart_css').parent().parent().show();
                        } else {
                            jQuery('#_fp_donation_cart_css').parent().parent().hide();
                        }

                        if (checkbox_checkout) {
                            jQuery('#_fp_donation_checkout_css').parent().parent().show();
                        } else {
                            jQuery('#_fp_donation_checkout_css').parent().parent().hide();
                        }

                        if (checkbox_product) {
                            jQuery('#_fp_donation_product_css').parent().parent().show();
                        } else {
                            jQuery('#_fp_donation_product_css').parent().parent().hide();
                        }

                        if (checkbox_shortcode) {
                            jQuery('#_fp_donation_shortcode_css').parent().parent().show();
                            jQuery('.fp_info_donation_form').parent().show();
                        } else {
                            jQuery('#_fp_donation_shortcode_css').parent().parent().hide();
                            jQuery('.fp_info_donation_form').parent().hide();
                        }

                        // Check condition for Table settings
                        if (checkbox_donationtable) {
                            jQuery('.fp_info_donation_table').parent().show();
                        } else {
                            jQuery('.fp_info_donation_table').parent().hide();
                        }
                        // On upon click function we have to make the visibility
                        jQuery(document).on('click', '#_fp_donation_display_cart', function () {
                            if (jQuery(this).is(":checked")) {
                                jQuery('#_fp_donation_cart_css').parent().parent().show();
                            } else {
                                jQuery('#_fp_donation_cart_css').parent().parent().hide();
                            }
                        });
                        jQuery(document).on('click', '#_fp_donation_display_checkout', function () {
                            if (jQuery(this).is(":checked")) {
                                jQuery('#_fp_donation_checkout_css').parent().parent().show();
                            } else {
                                jQuery('#_fp_donation_checkout_css').parent().parent().hide();
                            }
                        });
                        jQuery(document).on('click', '#_fp_donation_display_product', function () {
                            if (jQuery(this).is(":checked")) {
                                jQuery('#_fp_donation_product_css').parent().parent().show();
                            } else {
                                jQuery('#_fp_donation_product_css').parent().parent().hide();
                            }
                        });
                        jQuery(document).on('click', '#_fp_donation_display_shortcode', function () {
                            if (jQuery(this).is(":checked")) {
                                jQuery('#_fp_donation_shortcode_css').parent().parent().show();
                                jQuery('.fp_info_donation_form').parent().show();
                            } else {
                                jQuery('#_fp_donation_shortcode_css').parent().parent().hide();
                                jQuery('.fp_info_donation_form').parent().hide();
                            }
                        });

                        // For Donation Table Settings
                        jQuery(document).on('click', '#_fp_donation_display_table', function () {
                            if (jQuery(this).is(":checked")) {
                                jQuery('.fp_info_donation_table').parent().show();
                            } else {
                                jQuery('.fp_info_donation_table').parent().hide();
                            }
                        });
                    });
                </script>
                <?php
                $getcontent = ob_get_clean();
                return $getcontent;
            }
        }
    }

// Ajax Functionality for Donation System

    public static function ajax_requesting_donation_system() {
        if (isset($_POST)) {
            $title = $_POST['title'];
            $productid = FP_Donation_Common_Function::create_new_product($title);

            $product = sumo_donation_get_product($productid);
            $formatted_name = $product->get_formatted_name();
            $main_id = array();
            $main_id[$productid] = $formatted_name;
            $array = array('id' => $productid, 'text' => $formatted_name);

            update_option('ds_select_particular_products', $productid);
            echo json_encode($array);
        }
        exit();
    }

// Create Product with all the meta values


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

new FP_DonationSystem_General_Tab();
