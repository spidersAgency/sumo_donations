<?php

/*
 * GDPR Compliance
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('SUMO_Donation_Privacy')) :

    /**
     * SUMO_Donation_Privacy class
     */
    class SUMO_Donation_Privacy {

        /**
         * SUMO_Donation_Privacy constructor.
         */
        public function __construct() {
            $this->init_hooks();
        }

        /**
         * Register SUMO Donations
         */
        public function init_hooks() {
            add_action('admin_init', array(__CLASS__, 'add_privacy_content_for_donation'), 20);
        }

        /**
         * Return the privacy policy content for SUMO Reward Points.
         */
        public static function get_privacy_content() {
            return
                    '<h2>' . __('SUMO Donations', 'donationsystem') . '</h2>' .
                    '<p>' . __('This includes the basics of what personal data your store may be collecting, storing and sharing. Depending on what settings are enabled and which additional plugins are used, the specific information shared by your store will vary.', 'donationsystem') . '</p>' .
                    '<h2>' . __('What the plugin does', 'donationsystem') . '</h2>'
                    . '<ul>'
                    . '<li>' . __('Receive Donations from your Customers when they purchase on your Shop', 'donationsystem') . '</li>'
                    . '</ul>'
                    . '<h2>' . __('What we collect and store', 'donationsystem') . '</h2>'                    
                    . '<h2>' . __('User ID', 'donationsystem') . '</h2>'
                    . '<dl>'
                    . '<dt>' . __('We use the user id to', 'donationsystem') . '</dt>'
                    . '<dd>' . __('- Identify the user', 'donationsystem') . '</dd>'
                    . '<dd>' . __('- Identify the donation made by the user', 'donationsystem') . '</dd>'
                    . '</dl>';
        }

        /**
         * Add the privacy policy text to the policy postbox.
         */
        public static function add_privacy_content_for_donation() {
            if (function_exists('wp_add_privacy_policy_content')) {
                $content = self::get_privacy_content();
                wp_add_privacy_policy_content(__('SUMO Donations'), $content);
            }
        }

    }

    new SUMO_Donation_Privacy();

endif;