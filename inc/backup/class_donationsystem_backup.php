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
                'name' => __('Donation Rewards Settings', 'donationsystem'),
                'type' => 'title',
                'id' => '_donationsystem_rewards_settings'
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
        if (isset($_POST['fp_donation_rewards_rule'])) {
            update_option('fp_donation_rewards_rule', $_POST['fp_donation_rewards_rule']);
        }
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
        echo "Main Testing";
        echo "<pre>";
        var_dump(get_option('fp_donation_rewards_rule'));
        echo "</pre>";
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
                    <th class="manage-column column-columnname-link" scope="col"><?php _e('Rewards Type', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname-product" scope="col"><?php _e('Product Selection', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname num" scope="col"><?php _e('Remove Linking', 'donationsystem'); ?></th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="manage-column column-columnname num" scope="col"> <span class="fpadddonationrule button-primary"><?php _e('Add Rewards', 'donationsystem'); ?></span></td>
                </tr>
                <tr>
                    <th class="manage-column column-columnname" scope="col"><?php _e('Minimum Donation', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname" scope="col"><?php _e('Maximum Donation', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname-link" scope="col"><?php _e('Rewards Type', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname-product" scope="col"><?php _e('Product Selection', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname num" scope="col"><?php _e('Add Linking', 'donationsystem'); ?></th>
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
                                <p class="form-fields"><input type="number" step="any" name="fp_donation_rewards_rule[<?php echo $iteration; ?>][min]" min='0' value='<?php echo $value['min']; ?>' /></p>
                            </td>
                            <td>
                                <p class="form-fields"><input type="number" step='any' name="fp_donation_rewards_rule[<?php echo $iteration; ?>][max]" min='0' value='<?php echo $value['max']; ?>' /></p>
                            </td>
                            <td>
                                <p class='form-fields'>
                                    <select id='fp_donation_rewards_rule<?php echo $iteration; ?>' name='fp_donation_rewards_rule[<?php echo $iteration; ?>][type]'>
                                        <option value='1' <?php echo selected('1', $value['type']); ?>><?php _e('Free Product', 'donationsystem'); ?></option>
                                        <option value='2' <?php echo selected('2', $value['type']); ?>><?php _e('Coupon Code', 'donationsystem'); ?></option>
                                    </select>
                                </p>
                            </td>
                            <td>
                                <?php
                                $list_of_produts = $value['product'];
                                if (!is_array($list_of_produts)) {
                                    $product_ids = array_filter(array_map('absint', (array) explode(',', $list_of_produts)));
                                } else {
                                    $product_ids = $list_of_produts;
                                }
                                $name = 'fp_donation_rewards_rule[' . $iteration . '][product]';
                                $class = '';
                                $multiple = true;
                                $selected = $product_ids;
                                $id = '';
                                sumo_donation_product_select2($name, $id, $class, $selected, $multiple,'2');
                                ?>
                            </td>
                            <td class="column-columnname num">
                                <span class="fpdonation_remove button-secondary"><?php _e('Remove Linking', 'donationsystem'); ?></span>

                                <script type='text/javascript'>
                                    jQuery('.fpdonation_remove').click(function () {
                                        //   alert("Hi");
                                        jQuery(this).parent().parent().remove();
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
                    alert("Hi");
                    jQuery(this).parent().parent().remove();
                });
            });
        </script>
        <?php
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
                <p class="form-fields"><input type="number" step="any" name="fp_donation_rewards_rule[<?php echo $iteration; ?>][min]" min='0' value='' /></p>
            </td>
            <td>
                <p class="form-fields"><input type="number" step='any' name="fp_donation_rewards_rule[<?php echo $iteration; ?>][max]" min='0' value='' /></p>
            </td>
            <td>
                <p class='form-fields'>
                    <select name='fp_donation_rewards_rule[<?php echo $iteration; ?>][type]'>
                        <option value='1'><?php _e('Free Product', 'donationsystem'); ?></option>
                    </select>
                </p>
            </td>
            <td>
                <?php
                $name = 'fp_donation_rewards_rule[' . $iteration . '][product]';
                $class = '_fp_donation_existing_id';
                $multiple = false;
                $selected = array();
                $id = '';
                sumo_donation_product_select2($name, $id, $class, $selected, $multiple,'2');
                ?>
            </td>
            <td class="column-columnname num">
                <span class="fpdonation_remove button-secondary"><?php _e('Remove Linking', 'donationsystem'); ?></span>

                <script type='text/javascript'>
                    jQuery('.fpdonation_remove').click(function () {
                        //   alert("Hi");
                        jQuery(this).parent().parent().remove();
                    });
                </script>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

}

new FP_Donation_Rewards_Tab();
