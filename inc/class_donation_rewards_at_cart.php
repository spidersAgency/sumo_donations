<?php

class FP_DonationRewards_Cart {

// Construct the Function

    public function __construct() {

        add_action( 'woocommerce_after_cart_table' , array ( $this , 'list_free_products_in_cart' ) , 9 ) ;
        add_action( 'woocommerce_before_calculate_totals' , array ( $this , 'update_free_product_price_in_cart' ) ) ;
    }

// Display Free Product in that Page

    public static function list_free_products_in_cart() {
        global $woocommerce ;
        $get_detail = get_option( 'fp_donation_rewards_rule' ) ;
        if ( is_array( $get_detail ) && ($get_detail) ) {
            $priority                 = get_option( '_fp_donation_rule_priority' ) ;
            $get_session_productvalue = WC()->session->get( 'fp_donation_amount' ) ;
            $get_product_id           = get_option( 'ds_select_particular_products' ) ;
            if ( $get_product_id ) {
                if ( $get_session_productvalue > 0 ) {
                    $generate_cart_item_key = $woocommerce->cart->generate_cart_id( $get_product_id ) ;
                    $check_product          = $woocommerce->cart->find_product_in_cart( $generate_cart_item_key ) ;
                    if ( $check_product ) {
                        $priority     = get_option( '_fp_donation_rule_priority' ) ;
                        $return_value = self::apply_which_level( $get_session_productvalue , $priority ) ;
                        if ( ! empty( $return_value ) ) {
                            $return_value = $return_value[ 0 ] ;
                            if ( $return_value ) {
                                $count = self::get_product_count( $return_value ) ;
                                if ( $count > 0 ) {
                                    _e( '<h3>' . get_option( "_fp_donation_free_products_caption" ) . '</h3>' ) ;
                                }
                                echo "<ul>" ;
                                foreach ( $get_detail as $key => $value ) {
                                    if ( $key == $return_value ) {
                                        if ( ($value[ 'product' ] != '' ) ) {
                                            if ( ! is_array( $value[ 'product' ] ) ) {
                                                $explode = explode( ',' , $value[ 'product' ] ) ;
                                                foreach ( $explode as $eachvalue ) {
                                                    $cart_id              = self::generate_cart_item_key( $eachvalue ) ;
                                                    $find_product_in_cart = $woocommerce->cart->find_product_in_cart( $cart_id ) ;
                                                    if ( ! $find_product_in_cart ) {
                                                        $product_object = sumo_donation_get_product( $eachvalue ) ;
                                                        if ( $product_object->is_in_stock() ) {

                                                            $url  = $product_object->add_to_cart_url() ;
                                                            $link = get_the_title( $eachvalue ) ;

                                                            if ( $eachvalue ) {
                                                                echo "<li><a href=$url>$link</a></li>" ;
                                                            }
                                                            ?>

                                                            <?php

                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                echo "</ul>" ;
                            }
                        }
                    }
                }
            }
        }
    }

// Get Product Count

    public static function get_product_count( $key ) {
        global $woocommerce ;
        $count      = array () ;
        $get_option = get_option( 'fp_donation_rewards_rule' ) ;
        $get_option = $get_option[ $key ][ 'product' ] ;
        if ( ! is_array( $get_option ) && ! empty( $get_option ) ) {
            $explode_data = explode( ',' , $get_option ) ;
            foreach ( $explode_data as $eachvalue ) {
                $cart_id              = self::generate_cart_item_key( $eachvalue ) ;
                $find_product_in_cart = $woocommerce->cart->find_product_in_cart( $cart_id ) ;

                if ( ! $find_product_in_cart ) {
                    $count[] = $eachvalue ;
                }
            }
        } else {
            if ( is_array( $get_option ) ) {
                foreach ( $get_option as $eachvalue ) {
                    $cart_id              = self::generate_cart_item_key( $eachvalue ) ;
                    $find_product_in_cart = $woocommerce->cart->find_product_in_cart( $cart_id ) ;
                    if ( ! $find_product_in_cart ) {
                        $count[] = $eachvalue ;
                    }
                }
            }
        }
        return count( $count ) ;
    }

// It can be both product and variation
    public static function add_product_automatically_to_cart( $id ) {
        global $woocommerce ;
        $product_id = $id ;
        if ( $product_id ) {
            $qty            = '1' ;
            $product_object = sumo_donation_get_product( $product_id ) ;
            if ( $product_object ) {
                $product_type = sumo_donation_get_product_type( $product_object ) ;
                if ( $product_type != 'simple' ) {
                    $parent_id      = sumo_donation_get_product_parent_id( $product_object ) ;
                    $variation_id   = $product_id ;
                    $get_variations = $product_object->get_variation_attributes() ;
                    $cart_item_data = array () ;
                    $woocommerce->cart->add_to_cart( $parent_id , $qty , $variation_id , $get_variations , $cart_item_data ) ;
                } else {
                    $get_product_id = get_option( 'ds_select_particular_products' ) ;
                    if ( $product_id != $get_product_id ) {
                        $woocommerce->cart->add_to_cart( $product_id , $qty ) ;
                    }
                }
            }
        }
    }

// Add Free Product Automatically to cart
    public static function main_function_free_add_to_cart( $amount ) {
        global $woocommerce ;
        $priority     = get_option( '_fp_donation_rule_priority' ) ;
        $return_value = self::apply_which_level( $amount , $priority ) ;
        if ( ! empty( $return_value ) ) {
            $iteration_value = $return_value[ 0 ] ;
            $get_details     = self::get_which_rule( $iteration_value ) ;
            if ( is_array( $get_details ) && ! empty( $get_details ) ) {
                foreach ( $get_details as $key => $value ) {
                    if ( ($value[ 'product' ] != '' ) ) {
//                        if (!is_array($value['product'])) {
                        $explode = is_array( $value[ 'product' ] ) ? $value[ 'product' ] : explode( ',' , $value[ 'product' ] ) ;
                        foreach ( $explode as $eachvalue ) {
                            $cart_old_key         = self::generate_cart_item_key( $eachvalue ) ;
                            $find_product_in_cart = $woocommerce->cart->find_product_in_cart( $cart_old_key ) ;
                            if ( ! $find_product_in_cart ) {
                                self::add_product_automatically_to_cart( $eachvalue ) ;
                            }
                        }
//                        }
                    }
                }
            }
        }
    }

// Get the Product Type
    public static function get_product_type( $id ) {
        $product_id     = is_array( $id ) ? implode( ',' , $id ) : $id ;
        $product_object = sumo_donation_get_product( $product_id ) ;
        $product_type   = sumo_donation_get_product_type( $product_object ) ;
        return $product_type ;
    }

// generate cart item key for variable Product is in cart
    public static function generate_cart_item_key( $id ) {

        global $woocommerce ;
        $id               = is_array( $id ) ? implode( ',' , $id ) : $id ;
        $get_product_type = self::get_product_type( $id ) ;
        if ( $get_product_type != 'simple' ) {
            $object                   = sumo_donation_get_product( $id ) ;
            $parent_id                = sumo_donation_get_product_parent_id( $object ) ;
            $variation_id             = $id ;
            $get_available_variations = $object->get_variation_attributes() ;
            $generate_cart_id         = $woocommerce->cart->generate_cart_id( $parent_id , $variation_id , $get_available_variations , array () ) ;
        } else {
            $parent_id        = $id ;
            $generate_cart_id = $woocommerce->cart->generate_cart_id( $parent_id ) ;
        }
        return $generate_cart_id ;
    }

// Function to know which rule should apply

    public static function get_which_rule( $iteration ) {
        $get_detail = get_option( 'fp_donation_rewards_rule' ) ;
        $get_detail = $get_detail[ $iteration ] ;

        $additional_array = array () ;
        foreach ( $get_detail as $key => $value ) {
            $additional_array[ $iteration ][ $key ] = $value ;
        }
        return $additional_array ;
    }

//Check which level is first
    public static function apply_which_level( $donated_amount , $priority ) {
        global $woocommerce ;
        $main_array = array () ;
        $get_detail = get_option( 'fp_donation_rewards_rule' ) ;
        if ( is_array( $get_detail ) && ($get_detail) ) {
            foreach ( $get_detail as $keys => $values ) {
                if ( isset( $values[ 'product' ] ) ) {
                    if ( $values[ 'min' ] <= $donated_amount && $values[ 'max' ] >= $donated_amount ) {
                        if ( ! is_array( $values[ 'product' ] ) ) {
                            $explode = explode( ',' , $values[ 'product' ] ) ;
                            $amount  = 0 ;
                            foreach ( $explode as $key => $value ) {
                                if ( $value ) {
                                    $product = sumo_donation_get_product( $value ) ;
                                    $amount  += $product->get_price() ;
                                } else {
                                    $amount += 0 ;
                                }
                            }
                            $main_array[ $keys ] = $amount ;
                        } else {
                            $explode = $values[ 'product' ] ;
                            $amount  = 0 ;
                            foreach ( $explode as $key => $value ) {
                                if ( $value ) {
                                    $product = sumo_donation_get_product( $value ) ;
                                    $amount  += $product->get_price() ;
                                } else {
                                    $amount += 0 ;
                                }
                            }
                            $main_array[ $keys ] = $amount ;
                        }
                    }
                }
            }
        }
        if ( $priority == '1' ) {
            return array_keys( $main_array , reset( $main_array ) ) ;
        } elseif ( $priority == '2' ) {
            return array_keys( $main_array , end( $main_array ) ) ;
        } elseif ( $priority == '3' ) {
            return array_keys( $main_array , min( $main_array ) ) ;
        } else {
            return array_keys( $main_array , max( $main_array ) ) ;
        }
    }

// Calculate Total before cart update
    public static function update_free_product_price_in_cart( $object ) {
// It is applicable for
        global $woocommerce ;
        $check_product     = '' ;
        $get_cart_contents = $object->cart_contents ;
        $get_product_id    = get_option( 'ds_select_particular_products' ) ;
        foreach ( $get_cart_contents as $cartkey => $value ) {
            $get_detail = get_option( 'fp_donation_rewards_rule' ) ;
            if ( is_array( $get_detail ) && ($get_detail) ) {
                $get_session_productvalue = WC()->session->get( 'fp_donation_amount' ) ;
                if ( $get_session_productvalue > 0 ) {
                    $priority     = get_option( '_fp_donation_rule_priority' ) ;
                    $return_value = self::apply_which_level( $get_session_productvalue , $priority ) ;
                    if ( ! empty( $return_value ) ) {
                        if ( isset( $get_detail[ $return_value[ 0 ] ] ) ) {
                            foreach ( $get_detail as $key => $values ) {
                                if ( $key == $return_value[ 0 ] ) {
                                    if ( $values[ 'product' ] != '' ) {
                                        if ( ! is_array( $values[ 'product' ] ) ) {
                                            $explode = explode( ',' , $values[ 'product' ] ) ;
                                            foreach ( $explode as $eachvalue ) {
                                                $myid = $value[ 'variation_id' ] != '' ? $value[ 'variation_id' ] : $value[ 'product_id' ] ;
                                                if ( ($myid == $eachvalue) && ($myid != $get_product_id ) ) {
                                                    if ( $get_product_id ) {
                                                        $generate_cart_item_key = $woocommerce->cart->generate_cart_id( $get_product_id ) ;
                                                        $check_product          = $woocommerce->cart->find_product_in_cart( $generate_cart_item_key ) ;
                                                    }
                                                    if ( $check_product ) {
                                                        $value[ 'data' ]->set_price( 0 ) ;
                                                    } else {
                                                        $woocommerce->cart->set_quantity( $cartkey , 0 ) ;
//                                                        $value['data']->qty = '0';
                                                        // After the Qty is 0 just delete it
                                                        WC()->session->__unset( 'fp_donation_product' ) ;
                                                        WC()->session->__unset( 'fp_donation_amount' ) ;
                                                        WC()->session->__unset( 'fp_donation_currency' ) ;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}

new FP_DonationRewards_Cart() ;
