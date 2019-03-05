<?php

class FP_Donation_Rewards_Tab {

    // Construct the Donation Rewards
    public function __construct() {

        add_action('woocommerce_donationsystem_settings_tabs_array', array($this, 'initialize_tab'));

        add_action('woocommerce_donationsystem_settings_tabs_fp_donationsystem_rewards', array($this, 'initialize_visual_appearance_admin_fields'));

        add_action('woocommerce_update_options_fp_donationsystem_rewards', array($this, 'update_data_from_admin_fields'));

        add_action('admin_init', array($this, 'add_option_to_donationsystem'));

        add_action('woocommerce_admin_field_fp_donation_rewards_rule', array($this, 'donation_rewards_function'));
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
        $iteration = uniqid();
        ?>

        <table class="widefat fixed donationrule_rewards" cellspacing="0">
            <thead>
                <tr>

                    <th class="manage-column column-columnname" scope="col"><?php _e('Minimum Donation', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname" scope="col"><?php _e('Maximum Donation', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname-link" scope="col"><?php _e('Rewards Type', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname-product" scope="col"><?php _e('Product Selection', 'donationsystem'); ?></th>
        <!--                    <th class="manage-column column-columnname num" scope="col"><?php _e('Remove Linking', 'donationsystem'); ?></th>-->
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
        <!--                    <td class="manage-column column-columnname num" scope="col"> <span class="fpadddonationrule button-primary"><?php _e('Add Rewards', 'donationsystem'); ?></span></td>-->
                </tr>
                <tr>
                    <th class="manage-column column-columnname" scope="col"><?php _e('Minimum Donation', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname" scope="col"><?php _e('Maximum Donation', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname-link" scope="col"><?php _e('Rewards Type', 'donationsystem'); ?></th>
                    <th class="manage-column column-columnname-product" scope="col"><?php _e('Product Selection', 'donationsystem'); ?></th>
        <!--                    <th class="manage-column column-columnname num" scope="col"><?php _e('Add Linking', 'donationsystem'); ?></th>-->
                </tr>
            </tfoot>
            <tbody id="fpdonationrewardsrule">

                <!-- Donation Rewards Rule -->
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
                                }else{
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
                            <?php
                        }
                    } else {
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
                                <select id='fp_donation_rewards_rule<?php echo $iteration; ?>' name='fp_donation_rewards_rule[<?php echo $iteration; ?>][type]'>
                                    <option value='1'><?php _e('Free Product', 'donationsystem'); ?></option>
                                    <option value='2'><?php _e('Coupon Code', 'donationsystem'); ?></option>
                                </select>
                            </p>
                        </td>
                        <td>
                            <?php 
                                $name = 'fp_donation_rewards_rule[' . $iteration . '][product]';
                                $class = '_fp_donation_existing_id fp_donation_product'.$iteration;
                                $multiple = true;
                                $selected = array();
                                $id = '';
                                sumo_donation_product_select2($name, $id, $class, $selected, $multiple,'2');
                            ?>
                            <input type='text' style="display:none;" class="fp_donation_coupon<?php echo $iteration; ?>" name="fp_donation_rewards_rule[<?php echo $iteration; ?>][coupon]" value=""/>
                        </td>
                    </tr>
                    <?php
                }
            }

            public static function process_ajax_request_in_donation() {
                if (isset($_POST)) {
                    $iteration = $_POST['uniq_id'];
                    echo self::perform_on_ajax_request($iteration);
                }
                exit();
            }

        }

        new FP_Donation_Rewards_Tab();
        