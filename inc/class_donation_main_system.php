<?php

class FP_DonationSystem_Main_Function {

    // Construct the Main Function

    public function __construct() {
        if (get_option('_fp_donation_display_cart') == 'yes') {
            add_action('woocommerce_after_cart_table', array($this, 'donation_in_cart_page'));
        }
        add_action('woocommerce_before_calculate_totals', array($this, 'add_donation_in_woocommerce'), 998);
        add_action('woocommerce_checkout_update_order_meta', array($this, 'unset_session_data_from_place_order'));
        add_action('wp_head', array($this, 'call_initialize_post_data'), 1);
        add_action('pre_get_posts', array($this, 'hide_product_from_shop'));
        add_action('wp_head', array($this, 'predefined_buttons_in_donation'));
        add_filter('woocommerce_cart_product_price', array($this, 'show_donation_amount_as_cart_price'), 10, 2);
        add_filter('woocommerce_cart_product_subtotal', array($this, 'show_donation_amount_as_product_price'), 10, 4);
        add_action('woocommerce_checkout_order_processed', array($this, 'checkout_validation_error'), 10, 2);

        add_action('wp_head', array($this, 'validate_manual_donation_session'));

        //Below are Subscriptions API to provide compatibility to this plugin
        add_filter('sumosubscriptions_product_price_msg_for_subsc_fee', array($this, 'display_subscr_product_donation_price'), 10, 3);
        if (!is_admin()) {
            add_filter('sumosubscriptions_cart_product_total', array($this, 'add_subscription_product_donation'), 10, 2);
        }
        add_action('sumosubscriptions_active_subscription', array($this, 'record_subsc_id_belongs_to_the_subsc_product'), 10, 2);
        add_filter('sumosubscriptions_renewal_item_total', array($this, 'add_subscription_product_donation_on_subsc_renewal'), 10, 3);
    }

    public function validate_manual_donation_session() {

        $user_id = get_current_user_id();

        $get_productid = sumo_product_id_from_other_lang(get_option('ds_select_particular_products'));

        if ($get_productid > 0 && $this->is_cart_contains_donation_product($get_productid)) {
            WC()->session->set('fp_donation_product', $get_productid);
        } else {
            WC()->session->__unset('fp_donation_product');
        }

        if (count(WC()->cart->cart_contents) === 0) {
            WC()->session->__unset('fp_donation_amount');
        }

        //Clear subsc product meta values other than subsc donation product in cart
        $this->clear_meta_values($user_id);
    }

    public function is_cart_contains_donation_product($productid) {

        foreach (WC()->cart->cart_contents as $each_content) {
            if (isset($each_content['product_id']) && $each_content['product_id'] == $productid) {
                return true;
            }
        }
        return false;
    }

    public function display_subscr_product_donation_price($subcr_fee, $product_id, $product_obj) {
        global $post;

        $this_subscription_id = isset($post->ID) ? $post->ID : 0;

        $user_id = get_post_meta($this_subscription_id, 'sumo_get_user_id', true);

        if (is_admin() && $user_id > 0) {

            $get_donation_info = get_user_meta($user_id, 'fp_subsc_product_donation_info', true);

            $recurring_donation_amt = isset($get_donation_info[$product_id]['donation_amt']) ? $get_donation_info[$product_id]['donation_amt'] : '';
            $saved_subsc_id = isset($get_donation_info[$product_id]['subscr_id']) ? $get_donation_info[$product_id]['subscr_id'] : '';

            if ($recurring_donation_amt != "" && $saved_subsc_id == $this_subscription_id) {

                return self::format_price($recurring_donation_amt);
            }
        } else if (!is_admin()) {
            $auto_donation_amt_for_subsc_product = WC()->session->get('fp_donation_cart_amount_auto');
            $manual_donation_amt_for_subsc_product = WC()->session->get('fp_donation_amount');

            $getproduct_automatic = sumo_product_id_from_other_lang(get_option('ds_select_particular_products_automatic'));
            $getproduct_manual = get_option('ds_select_particular_products');

            if ($auto_donation_amt_for_subsc_product != '' && $product_id == $getproduct_automatic) {

                return self::format_price($auto_donation_amt_for_subsc_product);
            }
            if ($manual_donation_amt_for_subsc_product != '' && $product_id == $getproduct_manual) {

                return self::format_price($manual_donation_amt_for_subsc_product);
            }
            return $subcr_fee;
        }
        return $subcr_fee;
    }

    public function add_subscription_product_donation($subscription_product_total, $product_id) {

        $user_id = get_current_user_id();

        $manual_donation_amt_for_subsc_product = WC()->session->get('fp_donation_amount');

        $getproduct_automatic = sumo_product_id_from_other_lang(get_option('ds_select_particular_products_automatic'));
        $getproduct_manual = get_option('ds_select_particular_products');

        //Initially clear values if subscription id not set for the product
        $this->clear_meta_values($user_id, $product_id);

        if ($getproduct_automatic == $getproduct_manual) {

            $donation_amt = $manual_donation_amt_for_subsc_product > 0 ? $manual_donation_amt_for_subsc_product : WC()->session->get('fp_donation_cart_amount_auto');

            if ($getproduct_manual == $product_id && $donation_amt != '') {

                WC()->session->set('fp_donation_cart_amount_auto', $donation_amt);
            }
        }

        $auto_donation_amt_for_subsc_product = WC()->session->get('fp_donation_cart_amount_auto');

        if ($auto_donation_amt_for_subsc_product != '' && $product_id == $getproduct_automatic) {

            $this->record_donation_info_belongs_to_the_user($user_id, $auto_donation_amt_for_subsc_product, $product_id);

            return $auto_donation_amt_for_subsc_product;
        }
        if ($manual_donation_amt_for_subsc_product != '' && $product_id == $getproduct_manual) {

            $this->record_donation_info_belongs_to_the_user($user_id, $manual_donation_amt_for_subsc_product, $product_id);

            return $manual_donation_amt_for_subsc_product;
        }
        return $subscription_product_total;
    }

    public function record_subsc_id_belongs_to_the_subsc_product($subscription_id, $orderid) {

        $user_id = get_post_meta($subscription_id, 'sumo_get_user_id', true);

        $donation_info = get_user_meta($user_id, 'fp_subsc_product_donation_info', true);

        $subscription_product_info = get_post_meta($subscription_id, 'sumo_subscription_product_details', true);

        if (is_array($donation_info) && !empty($donation_info) && isset($subscription_product_info['productid'])) {

            $subscription_product_id = $subscription_product_info['productid'];

            if (array_key_exists($subscription_product_id, $donation_info) && isset($donation_info[$subscription_product_id]['subscr_id'])) {

                $donation_info[$subscription_product_id]['subscr_id'] = $subscription_id;

                update_user_meta($user_id, 'fp_subsc_product_donation_info', $donation_info);
            }
        }
    }

    public function add_subscription_product_donation_on_subsc_renewal($item_total, $productid, $subscription_id) {

        $user_id = get_post_meta($subscription_id, 'sumo_get_user_id', true);

        $donation_info = get_user_meta($user_id, 'fp_subsc_product_donation_info', true);

        if (is_array($donation_info) && !is_array($productid) && isset($donation_info[$productid]['subscr_id']) && $donation_info[$productid]['subscr_id'] == $subscription_id) {

            $donation_amt = $donation_info[$productid]['donation_amt'];

            return ($donation_amt != '' && $donation_amt != NULL) ? $donation_amt : $item_total;
        }

        return $item_total;
    }

    public function record_donation_info_belongs_to_the_user($user_id, $donation_amt, $product_id) {

        $get_donation_info = (array) get_user_meta($user_id, 'fp_subsc_product_donation_info', true);
        $values = '';

        if (!isset($get_donation_info[$product_id]['subscr_id'])) {

            $new_info = array(
                $product_id => array(
                    'donation_amt' => $donation_amt,
                    'subscr_id' => ''
            ));
            $values = $new_info + $get_donation_info;
        } else if (isset($get_donation_info[$product_id]['subscr_id'])) {

            $new_info = array(
                $product_id => array(
                    'donation_amt' => $donation_amt,
                    'subscr_id' => $get_donation_info[$product_id]['subscr_id'] > 0 ? $get_donation_info[$product_id]['subscr_id'] : ''
            ));
            $values = $new_info + $get_donation_info;
        }

        if (is_array($values)) {
            update_user_meta($user_id, 'fp_subsc_product_donation_info', $values);
        }
    }

    public function clear_meta_values($user_id, $subsc_product_id = 0) {

        $get_donation_info = get_user_meta($user_id, 'fp_subsc_product_donation_info', true);

        if (is_array($get_donation_info) && !empty($get_donation_info)) {

            if ($subsc_product_id > 0 && isset($get_donation_info[$subsc_product_id]['subscr_id']) && $get_donation_info[$subsc_product_id]['subscr_id'] == "") {

                $get_donation_info[$subsc_product_id]['donation_amt'] = '';
            } else {
                foreach ($get_donation_info as $each_info_key => $each_info) {

                    if ($each_info_key > 0 && isset($get_donation_info[$each_info_key]['subscr_id']) && $get_donation_info[$each_info_key]['donation_amt'] == "" && $get_donation_info[$each_info_key]['subscr_id'] == "") {
                        unset($get_donation_info[$each_info_key]);
                    }
                }
            }
            update_user_meta($user_id, 'fp_subsc_product_donation_info', $get_donation_info);
        }
    }

    // Donation Amount to Cart Page
    public static function donation_in_cart_page() {
        if (sumo_check_global_settings_to_display_df()) {
            ?>
            <style type='text/css'>
            <?php echo get_option('_fp_donation_cart_css'); ?>
            </style>
            <?php
            echo self::add_donation_amount_fields('cart');
        }
    }

    //Display Predefined Buttons in Donation
    public static function predefined_buttons_in_donation($suffix) {

        ob_start();
        $filter_buttons = array_filter((array) get_option('fp_predefined_buttons_' . $suffix));
        if (is_array($filter_buttons) && !empty($filter_buttons)) {
            ?>
            <style type="text/css">

                .fp_predefined_buttons {

                    border:none;
                }
                .fp_predefined_buttons td {
                    border:none;
                    text-align:center;
                }
                #fp_predefined_buttons ~ #fp_donation_submit {
                    margin:0 auto;
                    display:table;
                }


                #fp_predefined_buttons div {
                    background-color: #<?php echo get_option('_fp_donation_button_bg_color_' . $suffix); ?>;
                    background-image: none;
                    border: 0 none;
                    border-radius: 3px;
                    box-shadow: none;
                    color: #<?php echo get_option('_fp_donation_button_text_color_' . $suffix); ?>;
                    cursor: pointer;
                    display: inline-block;
                    font-family: inherit;
                    font-size: 100%;
                    font-weight: 700;
                    left: auto;
                    line-height: 1;
                    margin: 0;
                    overflow: visible;
                    padding: 1em 3em;
                    position: relative;
                    text-decoration: none;
                    text-shadow: none;
                    white-space: nowrap;
                }

                .fp_input_predefined_buttons_selected {
                    background-color:#<?php echo get_option('_fp_donation_button_selected_color_' . $suffix); ?> !important;
                }
                #fp_predefined_buttons div:hover {
                    background-color:#<?php echo get_option('_fp_donation_button_hover_color_' . $suffix); ?>;
                }
            </style>
            <script type="text/javascript">
                jQuery(function () {
                    jQuery(document).on('click', '.fp_input_predefined_buttons', function () {
                        jQuery('.fp_input_predefined_buttons').removeClass('fp_input_predefined_buttons_selected');
                        jQuery(this).addClass('fp_input_predefined_buttons_selected');
                        var dataprice = jQuery(this).attr('data-price');
                        jQuery('.fp_donation_amount_predefined_buttons').val(dataprice);
                    });
                });

            </script>
            <table class="fp_predefined_buttons" id="fp_predefined_buttons">
                <?php
                $i = 1;
                foreach ($filter_buttons as $key => $eachbutton) {
                    if ($i == 1) {
                        echo "<tr>";
                    }
                    ?>
                    <td>
                        <div class="fp_input_predefined_buttons" data-price="<?php echo $eachbutton; ?>"><?php echo self::format_price($eachbutton); ?></div>
                    </td>

                    <?php
                    $get_split_column = get_option('fp_predefined_buttons_columns_' . $suffix);
                    $defaultvalue_column = $get_split_column == '' ? '3' : $get_split_column;
                    if ($i % $defaultvalue_column == 0) {
                        echo "</tr><tr>";
                    }
                    $i++;
                }
                ?>
            </table>
            <?php
        }
        return ob_get_clean();
    }

    // Add Donation Amount to Contribute
    public static function add_donation_amount_fields($suffix) {
        ob_start();

        $get_product_id = get_option('ds_select_particular_products');

        if (($get_product_id != '') && ($get_product_id)) {
            ?>

            <h3 class="fp_donation_heading"><?php echo get_option("_fp_donation_form_title_" . $suffix); ?></h3>
            <p class="fp_donation_description"><?php echo get_option("_fp_donation_form_description_" . $suffix); ?></p>
            <?php
            if (get_option('_fp_donation_display_donation_rewards_table_' . $suffix) == 'yes') {

                echo self::show_donation_rewards_table();
            }
            ?>
            <p class="form-fields fp_donation_form">
                <?php
                if ((get_option("_fp_donation_form_type_" . $suffix) == '1') || (get_option("_fp_donation_form_type_" . $suffix) == '4')) {
                    $gettype = get_option('_fp_donation_form_type_' . $suffix);
                    echo str_replace('{currency_symbol}', get_woocommerce_currency_symbol(), get_option('_fp_donation_form_simple_label_' . $suffix));
                    ?><input type="number" min="0" <?php echo $gettype == '4' ? 'readonly' : ''; ?> name="fp_donation_amount"  value="<?php echo get_option('_fp_donation_default_value_' . $suffix); ?>" id="fp_donation_amount" class="fp_donation_amount"/>
                <?php } elseif (get_option('_fp_donation_form_type_' . $suffix) == '3') { ?>
                    <select name="fp_input_predefined_listbox" class="fp_input_predefined_listbox">
                        <?php
                        $listofdonation = get_option('_fp_donation_form_value_listbox_' . $suffix);
                        if (($listofdonation)) {
                            $explode = explode(',', $listofdonation);
                            foreach ($explode as $value) {
                                ?>
                                <option value="<?php echo $value; ?>"><?php echo self::format_price($value); ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            var value = jQuery('.fp_input_predefined_listbox').val();
                            jQuery('.fp_donation_amount_predefined_listbox').val(value);
                            jQuery('.fp_input_predefined_listbox').change(function () {
                                jQuery('.fp_donation_amount_predefined_listbox').val(jQuery(this).val());
                            });
                        });
                    </script>
                    <?php
                    if (get_option('_fp_donation_display_editable_field_' . $suffix) == 'yes') {
                        echo str_replace('{currency_symbol}', get_woocommerce_currency_symbol(), get_option('_fp_donation_form_simple_label_' . $suffix));
                        ?><input type="number" min="0" name="fp_donation_amount"  value="" id="fp_donation_amount" class="fp_donation_amount_predefined_listbox"/><?php
                    } else {
                        ?>
                        <input type="hidden" name="fp_donation_amount" class="fp_donation_amount_predefined_listbox" value=""/>

                        <?php
                    }
                } else {
                    echo self::predefined_buttons_in_donation($suffix);
                    if (get_option('_fp_donation_display_editable_field_' . $suffix) == 'yes') {
                        echo str_replace('{currency_symbol}', get_woocommerce_currency_symbol(), get_option('_fp_donation_form_simple_label_' . $suffix));
                        ?><input type="number" min="0" name="fp_donation_amount"  value="" id="fp_donation_amount" class="fp_donation_amount_predefined_buttons"/><?php
                    } else {
                        ?>
                        <input type="hidden" name="fp_donation_amount" class="fp_donation_amount_predefined_buttons" value=""/>

                        <?php
                    }
                }
                ?>
                <input type="hidden" name="fp_donation_suffix" class="fp_donation_suffix" value="<?php echo $suffix; ?>"/>
                <input type="hidden" name="fp_donation_product" value="<?php echo $get_product_id; ?>"/>
                <input type="submit" id='fp_donation_submit' name="fp_donation_submit" value="<?php echo get_option('_fp_donation_caption'); ?>" class="button-primary <?php echo get_option('_fp_donation_custom_class_name'); ?>"/>

            </p>
            <?php
        }
        $get_results = ob_get_clean();
        return $get_results;
    }

    // Show Donation Rewards Table
    public static function show_donation_rewards_table() {
        global $woocommerce;
        ob_start();
        $rule = get_option('fp_donation_rewards_rule');
        if (!empty($rule)) {
            ?>
            <h5 class="fp_donation_rewards_table_heading">
                <?php echo get_option('_fp_donation_rule_rewards_title'); ?>
            </h5>

            <table class="fpdonation_rewards_table table">
                <thead>
                <th>
                    <?php echo get_option('_fp_donation_rewards_amount_range_label'); ?>
                </th>
                <th>
                    <?php echo get_option('_fp_donation_rewards_free_product_caption'); ?>
                </th>
                <th>
                    <?php echo get_option('_fp_donation_rewards_free_product_worth'); ?>
                </th>


            </thead>
            <tbody>
                <?php
                $get_data = get_option('fp_donation_rewards_rule');
                foreach ($get_data as $key => $value) {
                    //  echo $value['min'] . " - " . $value['max'];
                    ?>
                    <tr>
                        <td>
                            <?php echo self::format_price($value['min']) . ' - ' . self::format_price($value['max']); ?>
                        </td>
                        <td>
                            <?php echo FP_Donation_Common_Function::list_of_product_title($value['product']); ?>
                        </td>
                        <td>
                            <?php echo self::format_price(FP_Donation_Common_Function::worth_of_products($value['product'])); ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>

            </tbody>
            </table>
            <?php
        }
        return ob_get_clean();
    }

// call initialize post data function

    public static function call_initialize_post_data() {
        if (isset($_POST['fp_donation_suffix'])) {
            $suffix = $_POST['fp_donation_suffix'];
            echo self::initialize_post_data_after_submit($suffix);
        }
    }

    public static function checkout_validation_error($orderid, $posted) {
        if (get_option('_fp_force_donation_manual') == 'yes') {
            $order = new WC_Order($orderid);
            $productid = array();
            $listofproducts = array(get_option('ds_select_particular_products'));
            foreach ($order->get_items() as $eachorder) {
                $productid[] = $eachorder['product_id'];
            }
            $array_intersect = array_intersect($listofproducts, $productid);
            if (!$array_intersect) {
                throw new Exception(__(get_option('_fp_force_manual_donation_error_message'), 'donationsystem'));
            }
        }
    }

    // wp head hook to initialize post data
    public static function initialize_post_data_after_submit($suffix) {
        ob_start();
        global $woocommerce;
        if (is_cart() || (is_checkout()) || is_product() || is_single() || is_page()) {
            $minimum = get_option('_fp_donation_amount_minimum_' . $suffix);
            $maximum = get_option('_fp_donation_amount_maximum_' . $suffix);

            $minimum = $minimum ? $minimum : "0";
            $maximum = $maximum ? $maximum : "0";
            $response = 'false';
            if (isset($_POST['fp_donation_submit'])) {

                $donation_amount = $_POST['fp_donation_amount'];
                $donation_product = $_POST['fp_donation_product'];

                if (($donation_amount == '') || ($donation_amount == '0')) {
                    wc_add_notice(get_option('_fp_donation_empty_error_message'), 'error');
                    if ((is_page() || is_single()) && !is_product() && (!is_checkout())) {
                        wp_safe_redirect($woocommerce->cart->get_cart_url());
                        exit;
                    }
                    return;
                }
                if (get_option('_fp_donation_form_type_' . $suffix) == '1') {
                    if (($donation_amount < $minimum) && ($minimum > 0)) {
                        $minimum_error = get_option('_fp_donation_minimum_error_message');
                        $shortcode_find_min = array('{minimum_donation}');
                        $replace_find_min = array(self::format_price($minimum));
                        $minimum_message = str_replace($shortcode_find_min, $replace_find_min, $minimum_error);
                        wc_add_notice($minimum_message, 'error');
                        if ((is_page() || is_single()) && !is_product() && (!is_checkout())) {
                            wp_safe_redirect($woocommerce->cart->get_cart_url());
                            exit;
                        }
                        return;
                    }
                    if (($donation_amount > $maximum) && ($maximum > 0)) {
                        $maximum_error = get_option('_fp_donation_maximum_error_message');
                        $shortcode_find = array('{maximum_donation}');
                        $replace_find = array(self::format_price($maximum));
                        $maximum_message = str_replace($shortcode_find, $replace_find, $maximum_error);
                        wc_add_notice($maximum_message, 'error');
                        if ((is_page() || is_single()) && !is_product() && (!is_checkout())) {
                            wp_safe_redirect($woocommerce->cart->get_cart_url());
                            exit;
                        }
                        return;
                    }
                }
                $generate_cart_item_key = $woocommerce->cart->generate_cart_id($donation_product);
                $check_product = $woocommerce->cart->find_product_in_cart($generate_cart_item_key);
                if (!$check_product) {
                    $response = $woocommerce->cart->add_to_cart($donation_product);
                    $session_currency = class_exists('WCML_Multi_Currency') ? WC()->session->get('client_currency') : get_option('woocommerce_currency');
                    WC()->session->set('fp_donation_currency', $session_currency);

                    if (get_option('_fp_donation_rewards_apply_type') == '1') {
                        FP_DonationRewards_Cart::main_function_free_add_to_cart($donation_amount);
                    }
                    if ($response) {
                        wc_add_notice(get_option('_fp_donation_success_message'), 'success');
                        if ((is_page() || is_single()) && !is_product() && (!is_checkout())) {
                            WC()->session->set('fp_donation_product', $donation_product);
                            WC()->session->set('fp_donation_amount', $donation_amount);
                            wp_safe_redirect($woocommerce->cart->get_cart_url());
                            exit;
                        }
                    } else {
                        if ((is_page() || is_single()) && !is_product() && (!is_checkout())) {
                            WC()->session->set('fp_donation_product', $donation_product);
                            WC()->session->set('fp_donation_amount', $donation_amount);
                            wp_safe_redirect($woocommerce->cart->get_cart_url());
                            exit;
                        }
                    }
                } else {
                    wc_add_notice(get_option('_fp_donation_success_message'), 'success');
                    WC()->session->set('fp_donation_product', $donation_product);
                    WC()->session->set('fp_donation_amount', $donation_amount);
                    if (get_option('_fp_donation_rewards_apply_type') == '1') {
                        FP_DonationRewards_Cart::main_function_free_add_to_cart($donation_amount);
                    }
                    if ((is_page() || is_single()) && !is_product() && (!is_checkout())) {
                        wp_safe_redirect($woocommerce->cart->get_cart_url());
                        exit;
                    }
                }
                WC()->session->set('fp_donation_product', $donation_product);
                WC()->session->set('fp_donation_amount', $donation_amount);
            } else {
                WC()->session->__unset('fp_donation_currency');
            }
        }
        return ob_get_clean();
    }

    //Show Donation Amount as Cart Price
    public static function show_donation_amount_as_cart_price($price, $object) {
        //var_dump($price);

        $get_session_productid = WC()->session->get('fp_donation_product');
        $get_session_productvalue = WC()->session->get('fp_donation_amount');
        if (!empty($get_session_productid) && !empty($get_session_productvalue)) {
            $product_id1 = sumo_donation_get_product_id($object);
            if ($get_session_productid == $product_id1) {
                if (class_exists('WCML_Multi_Currency')) {// Compatible for WPML MultiCurrency Switcher
                    global $woocommerce_wpml;
                    $session_currency = WC()->session->get('fp_donation_currency');
                    $current_currency = WC()->session->get('client_currency');
                    $wpml_currency = 1;
                    if ($current_currency != $session_currency) {
                        $wpml_currency = $woocommerce_wpml->settings['currency_options'][$session_currency]['rate'];
                        $get_session_productvalue = self::fp_wpml_multi_currency_in_cart($get_session_productvalue, $session_currency, $current_currency);
                    }
                }
                $price = self::format_price($get_session_productvalue);
                return $price;
            } else {
                return $price;
            }
        } else {
            return $price;
        }
    }

    //Show Donation Amount as Product Price
    public static function show_donation_amount_as_product_price($price, $object, $qty, $cart_object) {

        $get_session_productid = WC()->session->get('fp_donation_product') ? WC()->session->get('fp_donation_product') : '';
        $get_session_productvalue = WC()->session->get('fp_donation_amount') ? WC()->session->get('fp_donation_amount') : '';

        if (!empty($get_session_productid) && !empty($get_session_productvalue)) {
            $product_id1 = sumo_donation_get_product_id($object);
            if ($get_session_productid == $product_id1) {
                if (class_exists('WCML_Multi_Currency')) {// Compatible for WPML MultiCurrency Switcher
                    global $woocommerce_wpml;
                    $session_currency = WC()->session->get('fp_donation_currency');
                    $current_currency = WC()->session->get('client_currency');
                    $wpml_currency = 1;
                    if ($current_currency != $session_currency) {
                        $wpml_currency = $woocommerce_wpml->settings['currency_options'][$session_currency]['rate'];
                        $get_session_productvalue = self::fp_wpml_multi_currency_in_cart($get_session_productvalue, $session_currency, $current_currency);
                    }
                }
                $price = $get_session_productvalue;
                return self::format_price($price * $qty);
            } else {
                return $price;
            }
        } else {
            return $price;
        }
    }

    // set formatted price

    public static function format_price($price) {
        if (function_exists('wc_price')) {
            return wc_price($price);
        } else {
            if (function_exists('woocommerce_price')) {
                return woocommerce_price($price);
            }
        }
    }

    // Added Donation Amount to the Product
    public static function add_donation_in_woocommerce($object) {
        // Parameter as Cart Object

        if (is_object($object)) {
            // var_dump($object);
            $get_cart_contents = $object->cart_contents;
            $get_session_productid = WC()->session->get('fp_donation_product');
            $get_session_productvalue = WC()->session->get('fp_donation_amount');
            global $sitepress;
            $id_from_other_lang = array();
            if (is_plugin_active('sitepress-multilingual-cms/sitepress.php') && is_object($sitepress)) {
                $trid = $sitepress->get_element_trid($get_session_productid);
                $translations = $sitepress->get_element_translations($trid);
                foreach ($translations as $translation) {
                    $id_from_other_lang[] = $translation->element_id;
                }
            }
            foreach ($get_cart_contents as $key => $value) {
                if ($value['product_id'] == $get_session_productid || in_array($value['product_id'], $id_from_other_lang)) {
                    if (isset($get_session_productvalue) && ($get_session_productvalue != '')) {
                        if (class_exists('WCML_Multi_Currency')) {// Compatible for WPML MultiCurrency Switcher
                            global $woocommerce_wpml;
                            $session_currency = WC()->session->get('fp_donation_currency');
                            $current_currency = WC()->session->get('client_currency');
                            $wpml_currency = 1;
                            if ($current_currency != $session_currency) {
                                $wpml_currency = $woocommerce_wpml->settings['currency_options'][$session_currency]['rate'];
                                $get_session_productvalue = self::fp_wpml_multi_currency_in_cart($get_session_productvalue, $session_currency, $current_currency);
                            }
                        }
                        $value['data']->set_price($get_session_productvalue);
                    }
                }
            }
        }
    }

    public static function fp_wpml_multi_currency_in_cart($price, $previous_currency, $current_currency) {
        $return = $price;
        if (class_exists('WCML_Multi_Currency')) {// Compatible for WPML MultiCurrency Switcher
            global $woocommerce_wpml;
            $site_currency = get_option('woocommerce_currency');
            if ($site_currency != $previous_currency) {
                $previous_value = $woocommerce_wpml->settings['currency_options'][$previous_currency]['rate'];
                $original_amount = $price / $previous_value;
            } else {
                $original_amount = $price;
            }
            if ($site_currency != $current_currency) {
                $current_value = $woocommerce_wpml->settings['currency_options'][$current_currency]['rate'];
                $return = $original_amount * $current_value;
            } else {
                $return = $original_amount;
            }
        }
        return $return;
    }

    // Unset the Session data from cart on upon place order

    public static function unset_session_data_from_place_order($order_id) {
        $order = new WC_Order($order_id);
        $get_donation_productid = WC()->session->get('fp_donation_product');
        $get_donation_productvalue = WC()->session->get('fp_donation_amount');

        // Setted Donation Product ID Automatic
        $get_donation_productid_automatic = WC()->session->get('fp_donation_cart_product_auto');
        $get_donation_productvalue_automatic = WC()->session->get('fp_donation_cart_amount_auto');

        update_post_meta($order_id, 'fp_donation_product', $get_donation_productid);
        update_post_meta($order_id, 'fp_donation_value', $get_donation_productvalue);

        // Update Post Meta Order ID
        update_post_meta($order_id, 'fp_donation_product_automatic', $get_donation_productid_automatic);
        update_post_meta($order_id, 'fp_donation_amount_automatic', $get_donation_productvalue_automatic);

        // Optimization to avoid looping
        if ($get_donation_productid) {
            $get_data = get_option('_fp_donated_order_ids') ? get_option('_fp_donated_order_ids') : '';
            $get_previous_data = (array) $get_data;
            $current_orderid = array($order_id);
            $merge_data = array_merge($get_previous_data, $current_orderid);
            $merge_data = array_filter(array_unique($merge_data));
            update_option('_fp_donated_order_ids', $merge_data);
        }
        // After successfully saved data just delete that session info
        WC()->session->__unset('fp_donation_product');
        WC()->session->__unset('fp_donation_amount');

        WC()->session->__unset('fp_donation_cart_product_auto');
        WC()->session->__unset('fp_donation_cart_amount_auto');
    }

    // Hide that product from shop page

    public static function hide_product_from_shop($main_query) {

        if (!$main_query->is_main_query())
            return;
        if (!$main_query->is_post_type_archive())
            return;

        if (!is_admin() && is_shop()) {
            $get_product_id = get_option('ds_select_particular_products');
            $get_product_id_auto = sumo_product_id_from_other_lang(get_option('ds_select_particular_products_automatic'));
            if ($get_product_id_auto) {
                $main_query->set('post__not_in', array($get_product_id, $get_product_id_auto));
            }
        }

        remove_action('pre_get_posts', array('FP_DonationSystem_Main_Function', 'hide_product_from_shop'));
    }

}

new FP_DonationSystem_Main_Function();
