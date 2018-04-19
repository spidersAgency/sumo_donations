<?php

/*
 * Plugin Name: SUMO Donations
 * Plugin URI:
 * Description: Complete Donation System for WooCommerce
 * Version: 1.9
 * Author: FantasticPlugins
 * Author URI:
 */

class FP_DonationSystem {

    // Construct the Donation System

    public function __construct() {

        // Try to Avoid the Fatal Error when calling init hook
        include_once (ABSPATH . 'wp-admin/includes/plugin.php');
        add_action('init', array($this, 'check_woocommerce_is_active'));
        add_action('init', array($this, 'avoid_header_already_sent_problem'));
        // Screenids alteration
        if (isset($_GET['page'])) {
            if (($_GET['page'] == 'donationsystem')) {
                add_filter('woocommerce_screen_ids', array($this, 'allow_css_from_woocommerce'), 1);
            }
        }
        add_action('plugins_loaded', array($this, 'translate_ready'));

        //  Include the File from Subfolder
        include('inc/class_donation_main_system.php'); // For Cart Page
        include('inc/class_donation_checkout_function.php');
        include('inc/class_donation_product_function.php');
        include('inc/class_donation_shortcode_product.php');
        include('inc/admin/class_admin_menu_tab.php');
        include('inc/admin/class_wp_list_table_donationtable.php');
        include('inc/class_donation_rewards_at_cart.php');
        include('inc/class_donation_common_function.php');
        include('inc/class_donation_flybox_function.php');

        add_action('admin_enqueue_scripts', array($this, 'donation_admin_enqueue_script'));
    }

    // Check WooCommerce is Active or Not

    public static function check_woocommerce_is_active() {

        if (is_multisite()) {
            // This Condition is for Multi Site WooCommerce Installation
            if (!is_plugin_active_for_network('woocommerce/woocommerce.php') && (!is_plugin_active('woocommerce/woocommerce.php'))) {
                if (is_admin()) {
                    $variable = "<div class='error'><p> SUMO Donations will not work until WooCommerce Plugin is Activated. Please Activate the WooCommerce Plugin. </p></div>";
                    echo $variable;
                }
                return;
            }
        } else {
            // This Condition is for Single Site WooCommerce Installation
            if (!is_plugin_active('woocommerce/woocommerce.php')) {
                if (is_admin()) {
                    $variable = "<div class='error'><p> SUMO Donations will not work until WooCommerce Plugin is Activated. Please Activate the WooCommerce Plugin. </p></div>";
                    echo $variable;
                }
                return;
            }
        }
    }

    // Avoid Header Already Sent Problem

    public static function avoid_header_already_sent_problem() {
        ob_start();
    }

    /*
     *  Allow CSS from WooCommerce
     */

    public static function allow_css_from_woocommerce() {
        global $my_admin_page;

        $newscreenids = get_current_screen();

        if (isset($_GET['page'])) {
            if (($_GET['page'] == 'donationsystem')) {
                $array[] = $newscreenids->id;
                return $array;
            } else {
                $array[] = '';
                return $array;
            }
        }
    }

    /*
     * Translate Ready
     */

    public static function translate_ready() {
        load_plugin_textdomain('donationsystem', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    // Load Admin Enqueue Script

    public static function donation_admin_enqueue_script() {
        wp_enqueue_script('jscolor', plugins_url('/jscolor/jscolor.js', __FILE__));
    }

}

new FP_DonationSystem();