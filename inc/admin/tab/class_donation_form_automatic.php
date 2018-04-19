<?php

class FP_Donation_Automatic {

// Construct the Automatic Donation
    public function __construct() {

        add_action('woocommerce_donationsystem_settings_tabs_array', array($this, 'initialize_tab'));

        add_action('woocommerce_donationsystem_settings_tabs_fp_donationsystem_automatic', array($this, 'initialize_visual_appearance_admin_fields'));

        add_action('woocommerce_update_options_fp_donationsystem_automatic', array($this, 'update_data_from_admin_fields'));

        add_action('woocommerce_admin_field__fp_donation_automatic_include_selected_products', array($this, 'add_donation_include_products'));

        add_action('woocommerce_admin_field__fp_donation_automatic_exclude_selected_products', array($this, 'add_donation_exclude_products'));

        add_action('woocommerce_admin_field_fp_jquery_script_list_categories', array($this, 'jquery_script_for_category'));

        add_action('admin_init', array($this, 'add_option_to_donationsystem'));

        add_action('woocommerce_admin_field__fp_donation_new_product_automatic', array($this, 'initialize_button_to_make_new_product'));

        add_action('woocommerce_admin_field__fp_choose_existing_product_automatic', array($this, 'initialize_function_for_single_selection_product'));

        add_action('wp_ajax_donation_create_new_product_automatic', array($this, 'ajax_requesting_donation_system'));

//  add_action('wp_head', array($this, 'add_to_cart_automatic'));

        add_action('woocommerce_before_calculate_totals', array($this, 'automatic_main_function_donation_form'),999);

//add_action('wp_head', array($this, 'remove_donation_product_from_cart'));

        add_filter('woocommerce_product_is_taxable', array($this, 'make_product_not_taxable'), 10, 2);
    }

// Initialize the Settings from Donation Messages

    public static function initialize_tab($settings_tab) {
        if (!is_array($settings_tab)) {
            $settings_tab = (array) $settings_tab;
        }
        $settings_tab['fp_donationsystem_automatic'] = __('Automatic Donation', 'donationsystem');
        return array_filter($settings_tab);
    }

// Initialize Settings Page array

    public static function initialize_admin_fields() {
        global $woocommerce;

        $categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
        $list_categories = array();
//var_dump($categories);
        if (is_array($categories) && !empty($categories)) {
            foreach ($categories as $key => $value) {
                $list_categories[$value->term_id] = $value->name;
            }
        }

        return apply_filters('woocommerce_donationsystem_automatic_settings', array(
            array(
                'name' => __('Automatic Donation Settings', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_automatic_settings'
            ),
            array(
                'name' => __('Enable Automatic Donation', 'donationsystem'),
                'id' => '_fp_donation_automatic_enable',
                'css' => '',
                'std' => 'no',
                'class' => '',
                'default' => 'no',
                'newids' => '_fp_donation_automatic_enable',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Donation Product Setup', 'donationsystem'),
                'desc' => __('Choose Donation Product from Existing/New', 'donationsystem'),
                'id' => '_fp_donation_product_selection_automatic',
                'css' => 'min-width:150px;',
                'std' => '1',
                'class' => '_fp_donation_product_selection_automatic',
                'default' => '1',
                'newids' => '_fp_donation_product_selection_automatic',
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
                'id' => '_fp_donation_new_product_title_automatic',
                'css' => 'min-width:150px;',
                'std' => 'Donation',
                'class' => '',
                'default' => 'Donation',
                'newids' => '_fp_donation_new_product_title_automatic',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Create New Product', 'donationsystem'),
                'type' => '_fp_donation_new_product_automatic',
            ),
            array(
                'name' => __('Choose Existing Product for Donation', 'donationsystem'),
                'id' => '_fp_donation_existing_product_automatic',
                'css' => 'min-width:150px;',
                'std' => '',
                'class' => '',
                'default' => '',
                'newids' => '_fp_donation_existing_product_automatic',
                'type' => '_fp_choose_existing_product_automatic',
                'desc_tip' => false,
            ),
            array(
                'name' => __('Force Automatic Donation', 'donationsystem'),
                'id' => '_fp_force_donation_automatic',
                'desc' => __('Removing the Automatic Donation when you Turn On this Option', 'donationsystem'),
                'css' => '',
                'std' => 'yes',
                'class' => '',
                'default' => 'yes',
                'newids' => '_fp_force_donation_automatic',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Automatic Donation Value', 'donationsystem'),
                'id' => '_fp_automatic_donation_value',
                'css' => '',
                'std' => '1',
                'class' => '',
                'default' => '1',
                'newids' => '_fp_automatic_donation_value',
                'options' => array(
                    '1' => __('% of Cart Total', 'donationsystem'),
                    '2' => __('Fixed Value', 'donationsystem'),
                ),
                'type' => 'select',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Percentage of Cart Total as Donation', 'donationsystem'),
                'id' => '_fp_donation_automatic_percentage_value',
                'css' => '',
                'std' => '',
                'class' => 'fp_donation_value_is_percentage',
                'newids' => '_fp_donation_automatic_percentage_value',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Include Tax', 'donationsystem'),
                'id' => '_fp_donation_automatic_including_tax',
                'css' => '',
                'std' => 'no',
                'class' => 'fp_donation_value_is_percentage_tax',
                'newids' => '_fp_donation_automatic_including_tax',
                'type' => 'checkbox',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Fixed Value as Donation', 'donationsystem'),
                'id' => '_fp_donation_automatic_fixed_value',
                'css' => '',
                'std' => '',
                'class' => 'fp_donation_value_is_fixed',
                'newids' => '_fp_donation_automatic_fixed_value',
                'type' => 'text',
                'desc_tip' => true,
            ),
            array(
                'name' => __('Donation Value Added/Subtracted', 'donationsystem'),
                'id' => '_fp_donation_value_add_sub',
                'css' => '',
                'std' => '1',
                'default' => '1',
                'class' => 'fp_donation_value_is_percentage',
                'newids' => '_fp_donation_value_add_sub',
                'type' => 'select',
                'options' => array(
                    '1' => __('Donation Value Subtracted from Cart Total', 'donationsystem'),
                    '2' => __('Donation Value Added to Existing Cart Total', 'donationsystem'),
                ),
                'desc_tip' => true,
            ),
            array(
                'type' => 'sectionend',
                'id' => '_donationsystem_automatic_settings'
            ),
            array(
                'name' => __('Automatic Donation Valid for', 'donationsystem'),
                'type' => 'title',
                'id' => '_fp_donationsystem_automatic_validation',
            ),
            array(
                'name' => __('Product', 'donationsystem'),
                'id' => '_fp_donation_automatic_product_type',
                'css' => '',
                'std' => '1',
                'default' => '1',
                'class' => '',
                'newids' => '_fp_donation_automatic_product_type',
                'type' => 'select',
                'options' => array(
                    '1' => __('All Products', 'donationsystem'),
                    '2' => __('Include Selected Products', 'donationsystem'),
                    '3' => __('Exclude Selected Products', 'donationsystem'),
                ),
            ),
            array(
                'type' => '_fp_donation_automatic_include_selected_products',
            ),
            array(
                'type' => '_fp_donation_automatic_exclude_selected_products',
            ),
            array(
                'type' => 'fp_jquery_script_list_categories',
            ),
            array('type' => 'sectionend', 'id' => '_fp_donationsystem_automatic_validation'),
        ));
    }

    public static function jquery_script_for_category() {
        $list_of_ids = array('_fp_donation_automatic_include_selected_categories', '_fp_donation_automatic_exclude_selected_categories');
        foreach ($list_of_ids as $eachid) {
            echo FP_Donation_Common_Function::add_chosen_or_select2($eachid);
        }
        ?>
        <script type="text/javascript">
            jQuery(function () {

                jQuery(document).on('change', '#_fp_donation_product_selection_automatic', function () {
                    var value = jQuery(this).val();
                    if (value === '1') {
                        // Existing Product ID
                        //jQuery('#_fp_donation_existing_id').parent().parent().show();
                        jQuery('.fp_donation_new_product_automatic').parent().parent().hide();
                        jQuery('#_fp_donation_new_product_title_automatic').parent().parent().hide();

                    } else {
                        // New Product Creation
                        // jQuery('#_fp_donation_existing_id').parent().parent().show();
                        jQuery('.fp_donation_new_product_automatic').parent().parent().show();
                        jQuery('#_fp_donation_new_product_title_automatic').parent().parent().show();
                    }
                });

                var newvalue = jQuery('#_fp_donation_product_selection_automatic').val();

                if (newvalue === '1') {
                    // Existing Product ID
                    // jQuery('#_fp_donation_existing_id').parent().parent().show();
                    jQuery('.fp_donation_new_product_automatic').parent().parent().hide();
                    jQuery('#_fp_donation_new_product_title_automatic').parent().parent().hide();
                } else {
                    // New Product Creation
                    //   jQuery('#_fp_donation_existing_id').parent().parent().show();
                    jQuery('.fp_donation_new_product_automatic').parent().parent().show();
                    jQuery('#_fp_donation_new_product_title_automatic').parent().parent().show();
                }

                jQuery('.fp_donation_new_product_automatic').click(function () {
                    jQuery(this).attr('disabled', 'disabled');
                    var getvalue = jQuery('#_fp_donation_new_product_title_automatic').val();
                    var dataparam = ({
                        action: 'donation_create_new_product_automatic',
                        title: getvalue
                    });
                    jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                            function (response) {
                                var myproductid = response.productid;
                                var myproductname = response.productname;
                                jQuery('body').trigger('wc-enhanced-select-init');
                                jQuery('.fp_donation_new_product_automatic').removeAttr('disabled');
                                location.reload(true);
                            }, 'json');

                    return false;
                });

                // For Include Exclude Categories and Products

                jQuery(document).on('change', '#_fp_donation_automatic_product_type', function () {
                    var currentvalue = jQuery(this).val();
                    if (currentvalue === '1') {
                        jQuery('#_fp_donation_automatic_include_selected_products').parent().parent().hide();
                        jQuery('#_fp_donation_automatic_exclude_selected_products').parent().parent().hide();
                    } else if (currentvalue === '2') {
                        jQuery('#_fp_donation_automatic_include_selected_products').parent().parent().show();
                        jQuery('#_fp_donation_automatic_exclude_selected_products').parent().parent().hide();
                    } else {
                        jQuery('#_fp_donation_automatic_include_selected_products').parent().parent().hide();
                        jQuery('#_fp_donation_automatic_exclude_selected_products').parent().parent().show();
                    }
                });

                var newcurrentvalue_one = jQuery('#_fp_donation_automatic_product_type').val();
                if (newcurrentvalue_one === '1') {
                    jQuery('#_fp_donation_automatic_include_selected_products').parent().parent().hide();
                    jQuery('#_fp_donation_automatic_exclude_selected_products').parent().parent().hide();
                } else if (newcurrentvalue_one === '2') {
                    jQuery('#_fp_donation_automatic_include_selected_products').parent().parent().show();
                    jQuery('#_fp_donation_automatic_exclude_selected_products').parent().parent().hide();
                } else {
                    jQuery('#_fp_donation_automatic_include_selected_products').parent().parent().hide();
                    jQuery('#_fp_donation_automatic_exclude_selected_products').parent().parent().show();
                }

                // On upon Change event
                jQuery(document).on('change', '#_fp_donation_automatic_category_type', function () {
                    var currentvalue = jQuery(this).val();
                    if (currentvalue === '1') {
                        jQuery('#_fp_donation_automatic_include_selected_categories').parent().parent().hide();
                        jQuery('#_fp_donation_automatic_exclude_selected_categories').parent().parent().hide();
                    } else if (currentvalue === '2') {
                        jQuery('#_fp_donation_automatic_include_selected_categories').parent().parent().show();
                        jQuery('#_fp_donation_automatic_exclude_selected_categories').parent().parent().hide();
                    } else {
                        jQuery('#_fp_donation_automatic_include_selected_categories').parent().parent().hide();
                        jQuery('#_fp_donation_automatic_exclude_selected_categories').parent().parent().show();
                    }
                });

                var newcurrentvalue = jQuery('#_fp_donation_automatic_category_type').val();
                if (newcurrentvalue === '1') {
                    jQuery('#_fp_donation_automatic_include_selected_categories').parent().parent().hide();
                    jQuery('#_fp_donation_automatic_exclude_selected_categories').parent().parent().hide();
                } else if (newcurrentvalue === '2') {
                    jQuery('#_fp_donation_automatic_include_selected_categories').parent().parent().show();
                    jQuery('#_fp_donation_automatic_exclude_selected_categories').parent().parent().hide();
                } else {
                    jQuery('#_fp_donation_automatic_include_selected_categories').parent().parent().hide();
                    jQuery('#_fp_donation_automatic_exclude_selected_categories').parent().parent().show();
                }

                // Donation Value is based on Percentage/Fixed and show corresponding fields
                jQuery(document).on('change', '#_fp_automatic_donation_value', function () {
                    common_function_for_checking_donation_value(jQuery(this).val());
                });
                var currentdnval = jQuery('#_fp_automatic_donation_value').val();
                common_function_for_checking_donation_value(currentdnval);

                function common_function_for_checking_donation_value(value) {
                    if (value === '1') {
                        // Show Percentage and Hide Fixed
                        jQuery('.fp_donation_value_is_percentage').parent().parent().show();
                        jQuery('.fp_donation_value_is_percentage_tax').parent().parent().parent().parent().show();
                        jQuery('.fp_donation_value_is_fixed').parent().parent().hide();
                    } else {
                        // Show Fixed and Hide Percentage
                        jQuery('.fp_donation_value_is_percentage').parent().parent().hide();
                        jQuery('.fp_donation_value_is_fixed').parent().parent().show();
                        jQuery('.fp_donation_value_is_percentage_tax').parent().parent().parent().parent().hide();
                    }
                }
            });

        </script>
        <?php
    }

    public static function ajax_requesting_donation_system() {
        if (isset($_POST)) {
            $title = $_POST['title'];
            $productid = FP_Donation_Common_Function::create_new_product($title);

            $product = sumo_donation_get_product($productid);
            $formatted_name = $product->get_formatted_name();
            $main_id = array();
            $main_id[$productid] = $formatted_name;
            $array = array('id' => $productid, 'text' => $formatted_name);

            update_option('ds_select_particular_products_automatic', $productid);
            echo json_encode($array);
        }
        exit();
    }

    public static function initialize_visual_appearance_admin_fields() {
        woocommerce_admin_fields(self::initialize_admin_fields());
    }

    public static function update_data_from_admin_fields() {
        woocommerce_update_options(self::initialize_admin_fields());
        update_option('ds_select_particular_products_automatic', $_POST['ds_select_particular_products_automatic']);
        update_option('_fp_donation_automatic_include_selected_products', $_POST['_fp_donation_automatic_include_selected_products']);
        update_option('_fp_donation_automatic_exclude_selected_products', $_POST['_fp_donation_automatic_exclude_selected_products']);
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

// Admin Field of Donation System

    public static function add_donation_include_products() {
        ?>
        <tr>
            <th>
                <label for="fp_donation_automatic_include_selected_products">
                    <?php _e('Include Selected Products', 'donationsystem'); ?>
                </label>
            </th>
            <td>
                <?php
                $product_and_variation = '1';
                $multiple = true;
                $name = "_fp_donation_automatic_include_selected_products";
                $iteration = '';
                $value = '';
                $subname = '';
                echo FP_Donation_Common_Function::search_product_selection($product_and_variation, $multiple, $name, $iteration, $value, $subname);
                ?>
            </td>
        </tr>
        <?php
    }

// Automatic Main Function for Donation Form
    public static function automatic_main_function_donation_form($cart_object) {
        global $woocommerce;
// First thing check is enabled
        //var_dump($woocommerce->cart->calculate_totals());

        $new_auto_donation = array();
        $another_alteration = array();
        $check_is_enabled = get_option('_fp_donation_automatic_enable');
        $get_percentage_charity = get_option('_fp_donation_automatic_percentage_value');
        $force_donation = get_option('_fp_force_donation_automatic');
        $auto_donation_type = get_option('_fp_automatic_donation_value'); // Percentage of Cart Total/Fixed Value(1/2)
        $auto_donation_fixed_value = get_option('_fp_donation_automatic_fixed_value');
        $auto_donation_value_add_sub = get_option('_fp_donation_value_add_sub'); // Sub from CT/Add to ExCT 1/2
        $price_product = '';

        $getproduct_automatic = sumo_product_id_from_other_lang(get_option('ds_select_particular_products_automatic'));



        $list_of_ids = array($getproduct_automatic, get_option('ds_select_particular_products'));
        $get_available_cart_contents = $cart_object->cart_contents;
        if (!empty($get_available_cart_contents) && (is_array($get_available_cart_contents))) {
            $count = 0;

            foreach ($get_available_cart_contents as $key => $value) {
                if (($check_is_enabled == 'yes') && (!empty($getproduct_automatic))) {
                    if ($getproduct_automatic) {
                        $find_product_in_cart = sumo_check_auto_donation_pro_is_in_cart($getproduct_automatic);
                        if (!$find_product_in_cart) {
                            if ($force_donation != 'no') {
                                $woocommerce->cart->add_to_cart($getproduct_automatic);
                            }
                        }
                    }
                    if (!in_array($value['product_id'], $list_of_ids)) {
                        if (self::filter_by_product($value)) {
                            if ($find_product_in_cart) {
                                $quantity = $value['quantity'];
                                $product_id = $value['variation_id'] ? $value['variation_id'] : $value['product_id'];
                                $each_price = get_post_meta($product_id,'_price',true);
                                $price_product = $each_price * $quantity;
                                if ($auto_donation_type == '1') {
                                    $percentage_calculation = $price_product * ($get_percentage_charity / 100);
                                    $new_auto_donation[$value['product_id']] = $percentage_calculation;
                                    if ($auto_donation_value_add_sub == '1') {
                                        $altered_price = ($price_product - $percentage_calculation) / $quantity;
                                        $value['data']->set_price($altered_price);
                                    }
                                }
                            }
                            $count++;
                        }
                    } else {

                        if ($value['product_id'] == $getproduct_automatic) {
                            $another_alteration[$value['product_id']] = $value['data'];
                        }
                    }
                } else {
                    // var_dump($value['product_id']);
                    // var_dump($getproduct_automatic);
                    if ($value['product_id'] == $getproduct_automatic) {
                        // echo "You are right";
                        $cart_id = FP_DonationRewards_Cart::generate_cart_item_key($value['product_id']);
                        $woocommerce->cart->set_quantity($cart_id, '0');
                    }
                    $price_product = $value['data']->get_price();
                    $value['data']->set_price($price_product);
                    continue;
                }
            }

            if ($another_alteration) {
                if ($auto_donation_type == '1') {
                    $new_object = $another_alteration[$getproduct_automatic];
                    $new_object->set_price(array_sum($new_auto_donation));
                } else {
                    $new_object = $another_alteration[$getproduct_automatic];
                    $new_object->set_price($auto_donation_fixed_value != '' ? $auto_donation_fixed_value : "0");
                }
                WC()->session->set('fp_donation_cart_amount_auto', $new_object->get_price());
                WC()->session->set('fp_donation_cart_product_auto', $getproduct_automatic);
            }

            WC()->session->set('fp_donation_amount_auto', array_sum($new_auto_donation));
            WC()->session->set('fp_donation_product_auto', $getproduct_automatic);
        }
    }

// Product shouldn't be taxable
    public static function make_product_not_taxable($taxable, $product) {
        $getproduct_automatic = sumo_product_id_from_other_lang(get_option('ds_select_particular_products_automatic'));
        $get_product = array($getproduct_automatic);
        $product_id = sumo_donation_get_product_id($product);
        if (!empty($get_product)) {
            if (in_array($product_id, $get_product)) {
                if (get_option('_fp_donation_automatic_including_tax') === 'no') {
                    $taxable = false;
                    return $taxable;
                } else {
                    return $taxable;
                }
            }
        }
        return $taxable;
    }

// Function to make filtering option by product as well as category

    public static function filter_by_product($value) {
        $bool = false;
        $check_type_for_product = get_option('_fp_donation_automatic_product_type');
        $get_included_products = get_option('_fp_donation_automatic_include_selected_products');
        $get_excluded_products = get_option('_fp_donation_automatic_exclude_selected_products');
//var_dump($get_included_products);
        if ($check_type_for_product == '2') {
            if (!empty($get_included_products)) {
//var_dump($value);
                $productid = $value['variation_id'] != "" ? $value['variation_id'] : $value['product_id'];

//exit();
// var_dump($get_included_products);
                if (in_array($productid, (array) $get_included_products)) {
                    $bool = true;
                    return $bool;
                } else {
                    return $bool;
                }
            }
        } else if ($check_type_for_product == '3') {
            if (!empty($get_excluded_products)) {
                $productid = $value['variation_id'] ? $value['variation_id'] : $value['product_id'];
                if (!in_array($productid, $get_excluded_products)) {
                    $bool = true;
                    return $bool;
                } else {
                    return $bool;
                }
            }
        } else {
            $bool = true;
            return $bool;
        }

        return $bool;
    }

//Remove Donation Product from Cart
    public static function remove_donation_product_from_cart() {
        global $woocommerce;
        $check_is_enabled = get_option('_fp_donation_automatic_enable');

        $getproduct_automatic = sumo_product_id_from_other_lang(get_option('ds_select_particular_products_automatic'));

        if (($check_is_enabled == 'no' || $check_is_enabled == '')) {
            if ($getproduct_automatic) {
                $cart_item_key = FP_DonationRewards_Cart::generate_cart_item_key($getproduct_automatic);
                $woocommerce->cart->set_quantity($cart_item_key, 0);
            }
        }
    }

//



    public static function add_to_cart_automatic() {
        global $woocommerce;
        $getsession_product = WC()->session->get('fp_donation_product_auto');
//var_dump($getsession_product);

        if ($getsession_product) {
            $cart_id = FP_DonationRewards_Cart::generate_cart_item_key($getsession_product);
            $find_product_in_cart = $woocommerce->cart->find_product_in_cart($cart_id);
            if (!$find_product_in_cart) {
//if (did_action('wp')) {
                $woocommerce->cart->add_to_cart($getsession_product);
// }
            }
        }
    }

    public static function add_product_to_cart() {
        global $woocommerce;
// First thing check is enabled
        $new_auto_donation = array();
        $another_alteration = array();
        $record_alteration = array();
        $check_is_enabled = get_option('_fp_donation_automatic_enable');
        $getproduct_automatic = sumo_product_id_from_other_lang(get_option('ds_select_particular_products_automatic'));
        $get_percentage_charity = get_option('_fp_donation_automatic_percentage_value');

        if (($check_is_enabled == 'yes') && (!empty($getproduct_automatic)) && (!empty($get_percentage_charity))) {
            $list_of_ids = array($getproduct_automatic, get_option('ds_select_particular_products'));
            $get_available_cart_contents = WC()->cart->get_cart();
            if (!empty($get_available_cart_contents) && (is_array($get_available_cart_contents))) {
                $count = 0;
// if (did_action('wp_head')) {
                foreach ($get_available_cart_contents as $key => $value) {
                    if (!in_array($value['product_id'], $list_of_ids)) {
                        $price_product = $value['data']->get_price();
                        $percentage_calculation = $price_product * ($get_percentage_charity / 100);
                        $new_auto_donation[$value['product_id']] = $percentage_calculation;
                        $record_alteration[$value['product_id']] = $price_product - $percentage_calculation;
                        $count++;
                    }
// }
                }

                WC()->session->set('fp_donation_amount_auto', $new_auto_donation);
                WC()->session->set('fp_donation_product_auto', $getproduct_automatic);
            }
        }
    }

// Cart Total Consideration
// Add Admin Field Donation for Exclude Products

    public static function add_donation_exclude_products() {
        ?>
        <tr>
            <th>
                <label for="fp_donation_automatic_exclude_selected_products">
                    <?php _e('Exclude Selected Products', 'donationsystem'); ?>
                </label>
            </th>
            <td>
                <?php
                $product_and_variation = '1';
                $multiple = true;
                $name = "_fp_donation_automatic_exclude_selected_products";
                $iteration = '';
                $value = '';
                $subname = '';
                echo FP_Donation_Common_Function::search_product_selection($product_and_variation, $multiple, $name, $iteration, $value, $subname);
                ?>
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
                <input type="submit" class="fp_donation_new_product_automatic button-primary" value="Create New Donation Product" name="fp_donation_new_product"/>

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
                <label for="ds_select_particular_products_automatic"><?php _e("Product that should be used for Donation (Product should be non-taxable and non-shippable)", 'donationsystem'); ?></label>
            </th>
            <td class="forminp forminp-select">
                <?php
                $product_and_variation = '2';
                $multiple = false;
                $name = "ds_select_particular_products_automatic";
                $iteration = '';
                $value = '';
                $subname = '';
                echo FP_Donation_Common_Function::search_product_selection($product_and_variation, $multiple, $name, $iteration, $value, $subname);
                ?>
            </td>
        </tr>
        <?php
    }

}

new FP_Donation_Automatic();
