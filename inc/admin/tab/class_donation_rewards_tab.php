<?php

class FP_Donation_Rewards_Tab {

// Construct the Donation Rewards
    public function __construct() {

        add_action('woocommerce_donationsystem_settings_tabs_array', array($this, 'initialize_tab'));

        add_action('woocommerce_donationsystem_settings_tabs_fp_donationsystem_rewards', array($this, 'initialize_visual_appearance_admin_fields'));

        add_action('woocommerce_update_options_fp_donationsystem_rewards', array($this, 'update_data_from_admin_fields'));

        add_action('admin_init', array($this, 'add_option_to_donationsystem'));

        add_action('woocommerce_admin_field_fp_donation_rewards_rule', array($this, 'donation_rewards_function'));

        add_action('wp_ajax_fp_donation_generate_field', array($this, 'process_ajax_request_in_donation'));

        add_action('wp_ajax_fp_change_donation_type', array($this, 'main_function_to_alter_discount'));
    }

// Initialize the Settings from Donation Rewards

    public static function initialize_tab($settings_tab) {
        if(!is_array($settings_tab)){
            $settings_tab = (array)$settings_tab;
        }
        $settings_tab['fp_donationsystem_rewards'] = __('Donation Rewards', 'donationsystem');
        return array_filter($settings_tab);
    }

// Initialize Settings Page array

    public static function initialize_admin_fields() {
        global $woocommerce;
        return apply_filters('woocommerce_donationsystem_rewards_settings', array(
            array(
                'name' => __('Donation Reward Rules', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_rewards_rules',
            ),
            array('type' => 'sectionend'),
            array(
                'name' => __('[fp_donation_rewards_table]', 'donationsystem'),
                'type' => 'title',
                'desc' => __('Use this Shortcode in any Post/Page to display Donation Rewards Table', 'donationsystem'),
                'id' => '_donationsystem_rewards_settings',
            ),
            array(
                'name' => __('Donation Rewards Table Title', 'donationsystem'),
                'type' => 'text',
                'id' => '_fp_donation_rule_rewards_title',
                'css' => 'min-width:350px;',
                'std' => 'One Donation Reward from the following table will be applied',
                'class' => '',
                'default' => 'One Donation Reward from the following table will be applied',
                'newids' => '_fp_donation_rule_rewards_title',
            ),
            array(
                'name' => __('Rule Priority', 'donationsystem'),
                'id' => '_fp_donation_rule_priority',
                'css' => '',
                'std' => '1',
                'class' => '',
                'default' => '1',
                'newids' => '_fp_donation_rule_priority',
                'type' => 'select',
                'options' => array(
                    '1' => __('First Matched Rule', 'donationsystem'),
                    '2' => __('Last Matched Rule', 'donationsystem'),
                    '3' => __('Minimum Reward', 'donationsystem'),
                    '4' => __('Maximum Reward', 'donationsystem'),
                ),
            ),
            array(
                'name' => __('Add Donation Rewards Type', 'donationsystem'),
                'id' => '_fp_donation_rewards_apply_type',
                'css' => '',
                'std' => '1',
                'class' => '',
                'default' => '1',
                'newids' => '_fp_donation_rewards_apply_type',
                'type' => 'select',
                'options' => array(
                    '1' => __('Automatically Add Free Products to Cart', 'donationsystem'),
                    '2' => __('List Free Products in Cart for user to choose', 'donationsystem'),
                ),
            ),
            array(
                'type' => 'fp_donation_rewards_rule',
            ),
            array(
                'type' => 'sectionend',
                'id' => '_donationsystem_rewards_settings'
            ),
        ));
    }

    public static function initialize_visual_appearance_admin_fields() {
        woocommerce_admin_fields(self::initialize_admin_fields());
    }

    public static function update_data_from_admin_fields() {
        woocommerce_update_options(self::initialize_admin_fields());
        //if (isset($_POST['fp_donation_rewards_rule'])) {
        update_option('fp_donation_rewards_rule', $_POST['fp_donation_rewards_rule']);
        //}
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

// It is for Donation Rewards

    public static function donation_rewards_function() {
        self::donation_rewards_table_function();
    }

// Donation Rewards Option
    public static function donation_rewards_table_function() {
        global $woocommerce;
        wp_nonce_field(plugin_basename(__FILE__), 'fpdonationtable_rewards');
        ?>

        <table class="widefat fixed donationrule_rewards" cellspacing="0">
            <thead>
                <tr>

                    <th class="manage-column column-columnname" scope="col"><?php _e('Minimum Donation', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname" scope="col"><?php _e('Maximum Donation', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname-link" scope="col"><?php _e('Reward Type', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname-product" scope="col"><?php _e('Select Free Products', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname num" scope="col"><?php _e('Delete Rule', 'donationsystem'); ?></th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="manage-column column-columnname num" scope="col"> <span class="fpadddonationrule button-primary"><?php _e('Add Rule', 'donationsystem'); ?></span></td>
                </tr>

            </tfoot>
            <tbody id="fpdonationrewardsrule">
                <?php
                $get_data = get_option('fp_donation_rewards_rule');
                if (($get_data) && (is_array($get_data))) {

                    foreach ($get_data as $iteration => $value) {
                        ?>
                        <tr>
                            <td>
                                <p class="form-fields"><input type="number" step="any" required="required" name="fp_donation_rewards_rule[<?php echo $iteration; ?>][min]" min='0' value='<?php echo $value['min']; ?>' /></p>
                            </td>
                            <td>
                                <p class="form-fields"><input type="number" step='any' required="required" name="fp_donation_rewards_rule[<?php echo $iteration; ?>][max]" min='0' value='<?php echo $value['max']; ?>' /></p>
                            </td>
                            <td>
                                <p class='form-fields'>
                                    <select id='fp_donation_rewards_rule<?php echo $iteration; ?>'  data-key ='<?php echo $iteration; ?>' name='fp_donation_rewards_rule[<?php echo $iteration; ?>][type]'>
                                        <option value='1' <?php echo selected('1', $value['type']); ?>><?php _e('Free Products', 'donationsystem'); ?></option>
                <!--                                        <option value='2' <?php echo selected('2', $value['type']); ?>><?php _e('Cart Discount', 'donationsystem'); ?></option>-->
                                    </select>
                                </p>
                            </td>
                            <?php if ($value['type'] == '1') { ?>
                                <td class="fp_donation_type_selection<?php echo $iteration; ?>">
                                    <?php echo self::donation_rewards_common_function($iteration, 'fp_donation_rewards_rule', $value); ?>
                                </td>
                            <?php } else { ?>
                                <td class="fp_donation_type_selection<?php echo $iteration; ?> ">
                                    <input type="number"  min='0' max = '100' id='fp_donation_cartdiscount<?php echo $iteration; ?>' value='<?php echo $value['discount']; ?>' name="fp_donation_rewards_rule[<?php echo $iteration; ?>][discount]" />
                                </td>
                            <?php } ?>
                            <td class="column-columnname num">
                                <span class="fpdonation_remove button-secondary"><?php _e('Delete Rule', 'donationsystem'); ?></span>

                                <script type='text/javascript'>
                                    jQuery(function () {
                                        jQuery('.fpdonation_remove').click(function () {
                                            jQuery(this).parent().parent().remove();
                                        });

                                        //On Change Event
                                        jQuery(document).on('change', '#fp_donation_rewards_rule<?php echo $iteration; ?>', function () {
                                            console.log(jQuery(this).attr('data-key'));
                                            console.log(jQuery(this).val());
                                            var iteration = jQuery(this).attr('data-key');
                                            var datavalue = jQuery(this).val();
                                            jQuery.ajax({
                                                data: ({
                                                    action: 'fp_change_donation_type',
                                                    uniq_id: iteration,
                                                    datavalue: datavalue
                                                }),
                                                type: 'POST',
                                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                                dataType: 'html',
                                                success: function (data) {
                                                    // console.log(data);
                                                    jQuery('.fp_donation_type_selection' + iteration).empty().append(data);
                                                    jQuery('body').trigger('wc-enhanced-select-init');
                                                }
                                            });
                                        });
                                    });

                                </script>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>

        <script type="text/javascript">
            jQuery(function () {
                var counter;
                jQuery(".fpadddonationrule").click(function () {
                    counter = Math.round(new Date().getTime() + (Math.random() * 100));
                    console.log(counter);

                    jQuery.ajax({
                        data: ({
                            action: 'fp_donation_generate_field',
                            uniq_id: counter
                        }),
                        type: 'POST',
                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                        dataType: 'html',
                        success: function (data) {
                            // console.log(data);

                            jQuery('#fpdonationrewardsrule').append(data);

                            jQuery('body').trigger('wc-enhanced-select-init');
                        }
                    });

                    return false;
                });

                jQuery('.fpdonation_remove').click(function () {
                    //alert("Hi");
                    jQuery(this).parent().parent().remove();
                });
            });
        </script>
        <?php
    }

    // Common Function to retrieve it in donation rewards
    public static function donation_rewards_common_function($iteration, $name, $value) {
        $subname = "product";
        $product_and_variation = '2';
        $multiple = true;
        $iteration = $iteration;
        echo FP_Donation_Common_Function::search_product_selection($product_and_variation, $multiple, $name, $iteration, $value, $subname);
    }

    public static function process_ajax_request_in_donation() {
        if (isset($_POST)) {
            $iteration = $_POST['uniq_id'];
            echo self::perform_on_ajax_request($iteration);
        }
        exit();
    }

// Perform something on ajax request
    public static function perform_on_ajax_request($iteration) {
        ob_start();
        ?>
        <tr>
            <td>
                <p class="form-fields"><input type="number" step="any" required="required" name="fp_donation_rewards_rule[<?php echo $iteration; ?>][min]" min='0' value='' /></p>
            </td>
            <td>
                <p class="form-fields"><input type="number" step='any' required="required" name="fp_donation_rewards_rule[<?php echo $iteration; ?>][max]" min='0' value='' /></p>
            </td>
            <td>
                <p class='form-fields'>
                    <select id='fp_donation_rewards_rule<?php echo $iteration; ?>'  data-key ='<?php echo $iteration; ?>' name='fp_donation_rewards_rule[<?php echo $iteration; ?>][type]'>
                        <option value='1'><?php _e('Free Products', 'donationsystem'); ?></option>
        <!--                        <option value="2"><?php _e('Cart Discount', 'donationsystem'); ?></option>-->
                    </select>
                </p>
            </td>
            <td class='fp_donation_type_selection<?php echo $iteration; ?>'>
                <?php
                $name = 'fp_donation_rewards_rule[' . $iteration . '][product]';
                $class = '_fp_donation_existing_id';
                $multiple = true;
                $selected = array();
                $id = '';
                sumo_donation_product_select2($name, $id, $class, $selected, $multiple,'2');
                ?>
            </td>
            <td class="column-columnname num">
                <span class="fpdonation_remove button-secondary"><?php _e('Delete Rule', 'donationsystem'); ?></span>

                <script type='text/javascript'>
                    jQuery('.fpdonation_remove').click(function () {
                        jQuery(this).parent().parent().remove();
                    });
                    jQuery(document).on('change', '#fp_donation_rewards_rule<?php echo $iteration; ?>', function () {
                        console.log(jQuery(this).attr('data-key'));
                        console.log(jQuery(this).val());
                        var iteration = jQuery(this).attr('data-key');
                        var datavalue = jQuery(this).val();

                        jQuery.ajax({
                            data: ({
                                action: 'fp_change_donation_type',
                                uniq_id: iteration,
                                datavalue: datavalue
                            }),
                            type: 'POST',
                            url: "<?php echo admin_url('admin-ajax.php'); ?>",
                            dataType: 'html',
                            success: function (data) {
                                // console.log(data);
                                jQuery('.fp_donation_type_selection' + iteration).empty().append(data);
                                jQuery('body').trigger('wc-enhanced-select-init');
                            }
                        });
                    });
                </script>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    public static function main_function_to_alter_discount() {
        if (isset($_POST)) {
            $iteration = $_POST['uniq_id'];
            $checkvalue = $_POST['datavalue'];

            echo self::display_discount_field($iteration, $checkvalue);
        }
        exit();
    }

    // Display Discount Field/Free Product
    public static function display_discount_field($iteration, $checkvalue) {
        ob_start();
        if ($checkvalue == '2') {
            ?>
            <input type="number" value="" min='0' max = '100' name="fp_donation_rewards_rule[<?php echo $iteration; ?>][discount]" />
            <?php
        } else {
            $name = 'fp_donation_rewards_rule[' . $iteration . '][product]';
            $class = '_fp_donation_existing_id';
            $multiple = true;
            $selected = array();
            $id = '';
            sumo_donation_product_select2($name, $id, $class, $selected, $multiple,'2');
        }
        return ob_get_clean();
    }

}

new FP_Donation_Rewards_Tab();
