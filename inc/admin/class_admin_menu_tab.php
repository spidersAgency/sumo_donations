<?php
/*
 * Control Over Menu System in Admin Settings
 */

class FP_DonationSystem_Admin_Menu {

    // Construct the Gift System Admin Menu

    public function __construct() {

        // Register Admin Submenu
        add_action('admin_menu', array($this, 'add_sub_menu'));

        // Include tab one by one
        include('tab/class_donation_general_tab.php');
        include('tab/class_donation_form_tab.php');
        include('tab/class_donation_form_flybox_tab.php');
        include('tab/class_donation_form_automatic.php');
        include('tab/class_donation_rewards_tab.php');
        include('tab/class_donation_table_tab.php');
        include('tab/class_donation_shortcode_tab.php');
        include('tab/class_donation_labels_tab.php');
        include('tab/class_donation_messages_tab.php');
        include('tab/class_donation_reset_tab.php');
        include('tab/class_donation_form_support_tab.php');
    }

    // Add Sub-Menu under WooCommerce for Admin Menu

    public static function add_sub_menu() {
        // Submenu for Gift System
        add_submenu_page('woocommerce', __('SUMO Donations', 'donationsystem'), __('SUMO Donations', 'donationsystem'), 'manage_woocommerce', 'donationsystem', array('FP_DonationSystem_Admin_Menu', 'main_sub_menu_settings'));
    }

    public static function main_sub_menu_settings() {
        global $woocommerce, $woocommerce_settings, $current_section, $current_tab;
        $tabs = "";
        do_action('woocommerce_donationsystem_settings_start');
        $current_tab = ( empty($_GET['tab']) ) ? 'fp_donationsystem' : sanitize_text_field(urldecode($_GET['tab']));

        $current_section = ( empty($_REQUEST['section']) ) ? '' : sanitize_text_field(urldecode($_REQUEST['section']));
        if (!empty($_POST['save'])) {
            if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'woocommerce-settings'))
                die(__('Action failed. Please refresh the page and retry.', 'discountsystem'));

            if (!$current_section) {
//include_once('settings/settings-save.php');
                switch ($current_tab) {
                    default :
                        if (isset($woocommerce_settings[$current_tab]))
                            woocommerce_update_options($woocommerce_settings[$current_tab]);

// Trigger action for tab
                        do_action('woocommerce_update_options_' . $current_tab);
                        break;
                }

                do_action('woocommerce_update_options');

// Handle Colour Settings
                if ($current_tab == 'fp_donationsystem' && get_option('woocommerce_frontend_css') == 'yes') {
                    
                }
            } else {
// Save section onlys
                do_action('woocommerce_update_options_' . $current_tab . '_' . $current_section);
            }

// Clear any unwanted data
//$woocommerce->clear_product_transients();
            delete_transient('woocommerce_cache_excluded_uris');
// Redirect back to the settings page
            $redirect = add_query_arg(array('saved' => 'true'));
//  $redirect .= add_query_arg('noheader', 'true');

            if (isset($_POST['subtab'])) {
                wp_safe_redirect(esc_url_raw($redirect));
                exit;
            }
        }
// Get any returned messages
        $error = ( empty($_GET['wc_error']) ) ? '' : urldecode(stripslashes($_GET['wc_error']));
        $message = ( empty($_GET['wc_message']) ) ? '' : urldecode(stripslashes($_GET['wc_message']));

        if ($error || $message) {

            if ($error) {
                echo '<div id="message" class="error fade"><p><strong>' . esc_html($error) . '</strong></p></div>';
            } else {
                echo '<div id="message" class="updated fade"><p><strong>' . esc_html($message) . '</strong></p></div>';
            }
        } elseif (!empty($_GET['saved'])) {

            echo '<div id="message" class="updated fade"><p><strong>' . __('Your settings have been saved.', 'giftsystem') . '</strong></p></div>';
        }
        ?>
        <div class="wrap woocommerce">
            <form method="post" id="mainform" action="" enctype="multipart/form-data">
                <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div><h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
                    <?php
                    $tabs = apply_filters('woocommerce_donationsystem_settings_tabs_array', $tabs);

                    foreach ($tabs as $name => $label) {
                        //echo $current_tab;
                        echo '<a href="' . admin_url('admin.php?page=donationsystem&tab=' . $name) . '" class="nav-tab ';
                        if ($current_tab == $name)
                            echo 'nav-tab-active';
                        echo '">' . $label . '</a>';
                    }
                    do_action('woocommerce_donationsystem_settings_tabs');
                    ?>
                </h2>

                <?php
                switch ($current_tab) :

                    default :
                        //var_dump($current_tab);
                        do_action('woocommerce_donationsystem_settings_tabs_' . $current_tab);
                        break;
                endswitch;
                ?>

                <p class="submit">
                    <?php if (!isset($GLOBALS['hide_save_button'])) : ?>
                        <input name="save" class="button-primary" type="submit" value="<?php _e('Save Changes', 'donationsystem'); ?>" />
                    <?php endif; ?>
                    <input type="hidden" name="subtab" id="last_tab" />
                    <?php wp_nonce_field('woocommerce-settings', '_wpnonce', true, true); ?>
                    <?php
                    if ($current_tab == 'fp_donationsystem') {
                        ?>
                        <input style="margin-left: 100px;" class="button-secondary" type="submit" value="<?php _e('Reset this Page', 'donationsystem'); ?>" name="reset_fp_donationsystem">
                        <?php
                    }

                    if ($current_tab == 'fp_donationsystem_form') {
                        ?>
                        <input style="margin-left: 100px;" class="button-secondary" type="submit" value="<?php _e('Reset this Page', 'donationsystem'); ?>" name="reset_fp_donationsystem_form">
                        <?php
                    }

                    if ($current_tab == 'fp_donationsystem_flybox') {
                        ?>
                        <input style="margin-left: 100px;" class="button-secondary" type="submit" value="<?php _e('Reset this Page', 'donationsystem'); ?>" name="reset_fp_donationsystem_flybox">
                        <?php
                    }

                    if ($current_tab == 'fp_donationsystem_labels') {
                        ?>
                        <input style="margin-left: 100px;" class="button-secondary" type="submit" value="<?php _e('Reset this Page', 'donationsystem'); ?>" name="reset_fp_donationsystem_labels">
                        <?php
                    }

                    if ($current_tab == 'fp_donationsystem_messages') {
                        ?>
                        <input style="margin-left: 100px;" class="button-secondary" type="submit" value="<?php _e('Reset this Page', 'donationsystem'); ?>" name="reset_fp_donationsystem_messages">
                        <?php
                    }
                    ?>
                </p>
            </form>
        </div>
        <?php
    }

}

new FP_DonationSystem_Admin_Menu();


