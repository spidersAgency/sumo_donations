<?php

// Declare the Flybox Class
class FP_Donation_FlyBox_Function {

    public function __construct() {
        // Construct the Flybox Functionality
        if (get_option('_fp_donation_display_flybox') == 'yes') {
            add_action('wp_head', array($this, 'donation_flybox_visibility'));
        }
    }

    public static function donation_flybox_visibility() {
        if ((get_option('_fp_donation_flybox_display_cart') == 'yes')) {
            if (function_exists('is_cart')) {
                if (is_cart()) {
                    echo self::donation_flybox();
                }
            }
        }
        if ((get_option("_fp_donation_flybox_display_checkout") == 'yes')) {
            if (function_exists('is_checkout')) {
                if (is_checkout()) {
                    echo self::donation_flybox();
                }
            }
        }
    }

    public static function donation_flybox() {
        global $woocommerce;
        ob_start();

        if (sumo_check_global_settings_to_display_df()) {
            if (!isset($_COOKIE['fpdonationflybox'])) {
                ?>
                <style type="text/css">
                    .fp_donation_flybox {
                        position:fixed;
                        visibility:hidden;
                        bottom:0%;
                        z-index:9999999;
                        padding:10px;
                        background:#<?php echo get_option('_fp_donation_flybox_bgcolor'); ?>;
                        <?php if (get_option('_fp_donation_flybox_position') == '1') { ?>
                            border-right: 1px solid #<?php echo get_option("_fp_donation_flybox_border_color"); ?>;
                        <?php } else { ?>
                            border-left: 1px solid #<?php echo get_option("_fp_donation_flybox_border_color"); ?>;
                        <?php } ?>
                        border-top: 1px solid #<?php echo get_option("_fp_donation_flybox_border_color"); ?>;
                    }

                    .fp_donation_flybox h3.fp_donation_heading {
                        color: #<?php echo get_option('_fp_donation_flybox_head_text_color'); ?>;
                    }

                    .fp_donation_flybox p.fp_donation_description {
                        color: #<?php echo get_option('_fp_donation_flybox_description_text_color'); ?>;
                    }

                    span.fpdonationflyboxclose {
                        position: absolute;
                        <?php if (get_option('_fp_donation_flybox_position') == '1') { ?>
                            right:0px;
                            top:0px;
                        <?php } else { ?>
                            left:0px;
                            top:0px;
                        <?php } ?>
                        cursor:pointer;
                        padding-right:10px;
                    }

                </style>

                <?php
                $get_hide_screen_size = get_option('_fp_donation_flybox_hide_screen_size');
                // check it is not empty
                if (!empty($get_hide_screen_size) && ($get_hide_screen_size)) {
                    $explode_size = explode(',', $get_hide_screen_size);
                    if (is_array($explode_size)) {
                        foreach ($explode_size as $eachsize) {
                            $each_size_explode = explode('x', $eachsize);
                            $min = $each_size_explode[0];
                            $max = $each_size_explode[1];
                            ?>
                            <style type='text/css'>
                                @media (width:<?php echo $min; ?>px) and (height:<?php echo $max; ?>px) {
                                    .fp_donation_flybox{
                                        display:none;
                                    }
                                }
                            </style>
                            <?php
                        }
                    }
                }
                ?>
                <style type="text/css">
                <?php echo get_option('_fp_donation_flybox_css'); ?>
                </style>
                <div class='fp_donation_flybox'>
                    <span class='fpdonationflyboxclose'>X</span>
                    <form name="fp_donation_form" method="post">
                        <?php echo FP_DonationSystem_Main_Function::add_donation_amount_fields('flybox'); ?>
                    </form>
                </div>
                <script type="text/javascript">
                    function setCookie(key, value) {
                        var expires = new Date();
                        expires.setTime(expires.getTime() + ('<?php echo get_option('_fp_donation_flybox_enable_cookies') == 'yes' ? get_option('_fp_donation_flybox_click_to_close') : 0; ?>' * 24 * 60 * 60 * 1000));
                        document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
                        // document.cookie = key + '=' + value + ';path=/' + ';expires=' + expires.toUTCString();
                    }
                    function getCookie(key) {
                        var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
                        return keyValue ? keyValue[2] : null;
                    }

                    jQuery(function () {
                        if (getCookie('fpdonationflybox') !== '1') {
                            var get_current_width = jQuery('.fp_donation_flybox').outerWidth();
                            //console.log(get_current_width);
                <?php if (get_option('_fp_donation_flybox_position') == '1') { ?>
                                jQuery('.fp_donation_flybox').css('left', '-' + get_current_width + 'px');
                <?php } else { ?>
                                jQuery('.fp_donation_flybox').css('right', '-' + get_current_width + 'px');
                <?php } ?>
                            jQuery('.fpdonationflyboxclose').click(function () {
                                var getwidth = jQuery('.fp_donation_flybox').outerWidth();
                <?php if (get_option('_fp_donation_flybox_position') == '1') { ?>
                                    jQuery('.fp_donation_flybox').stop().animate({left: '-' + getwidth, easing: "swing"}, 600);
                <?php } else { ?>
                                    jQuery('.fp_donation_flybox').stop().animate({right: '-' + getwidth, easing: "swing"}, 600);
                <?php } ?>
                <?php if (get_option('_fp_donation_flybox_enable_cookies') == 'yes') { ?>
                                    setCookie('fpdonationflybox', '1');
                <?php } ?>
                            });
                            jQuery('.fp_donation_flybox').css('visibility', 'visible');
                        } else {
                            jQuery('.fp_donation_flybox').css('display', 'none');
                        }
                    });
                    jQuery(window).scroll(function () {
                        jQuery('.fp_donation_flybox').css('visibility', 'visible');
                        var currY = jQuery(this).scrollTop();
                        var postHeight = jQuery(this).height();
                        var scrollHeight = jQuery(document).height();
                        //
                        var scrollPercent = (currY / (scrollHeight - postHeight)) * 100;
                <?php if (get_option('_fp_donation_flybox_position') == '2') { ?>
                            if (getCookie('fpdonationflybox') !== '1') {
                                if (scrollPercent >= '<?php echo get_option('_fp_donation_flybox_scroll_percentage'); ?>') {
                                    //jQuery('.fp_donation_flybox').css('visibility', 'visible');
                                    jQuery('.fp_donation_flybox').stop().animate({right: "0px"}, 600);
                                } else {
                                    var width = jQuery('.fp_donation_flybox').outerWidth();
                                    //console.log(width);
                                    jQuery('.fp_donation_flybox').stop().animate({right: '-' + width, easing: "swing"}, 600);
                                }
                            } else {
                                //console.log('cookie is there');
                            }
                <?php } else { ?>
                            if (getCookie('fpdonationflybox') !== '1') {
                                if (scrollPercent >= '<?php echo get_option('_fp_donation_flybox_scroll_percentage'); ?>') {
                                    //console.log(scrollPercent);
                                    // jQuery('.fp_donation_flybox').css('visibility', 'visible');
                                    jQuery('.fp_donation_flybox').stop().animate({left: "0px"}, 600);
                                } else {
                                    //console.log(scrollPercent);
                                    //jQuery('.fp_donation_flybox').show();
                                    var width = jQuery('.fp_donation_flybox').outerWidth();
                                    //console.log(width);
                                    jQuery('.fp_donation_flybox').stop().animate({left: '-' + width, easing: "swing"}, 600);
                                }
                            } else {
                                //console.log('cookie is there');
                            }
                <?php } ?>
                    });
                </script>

                <?php
            }
        }
        return ob_get_clean();
    }

}

new FP_Donation_FlyBox_Function();
