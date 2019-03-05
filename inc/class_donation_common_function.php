<?php

class FP_Donation_Common_Function {

    // Construct the Common Function
    public function __construct() {
        
    }

    /*
     * Function for searching Product with backward compatibility
     * @params
     * $product_and_variation = 1; // If one means it is for only simple product, if it is 2 then both product and variation should apply
     * $multiple = true/false, if false it allow single selection, if it is true then it will allow multiple
     */

    public static function search_product_selection( $product_and_variation , $multiple , $name , $iteration , $value , $subname ) {
        global $woocommerce ;
        ob_start() ;
        if ( $product_and_variation == '1' ) {
            $product_selection = "woocommerce_json_search_products" ;
        } else {
            $product_selection = 'woocommerce_json_search_products_and_variations' ;
        }

        if ( $multiple ) {
            if ( $iteration != '' ) {
                $multiple_name = $name . "[$iteration]" . "[$subname]" ;
                //var_dump($multiple_name)
            } else {
                $multiple_name = $name ;
            }
        } else {
            if ( $iteration != '' ) {
                $multiple_name = $name . "[$iteration]" . "[$subname]" ;
            } else {
                $multiple_name = $name ;
            }
        }

        if ( $multiple ) {
            $new_attribute = 'multiple' ;
        } else {
            $new_attribute = '' ;
        }

        if ( $iteration == '' ) {
            $option_name = get_option( $name ) ;
        } else {
            $option_name = $value[ $subname ] ;
        }
        $list_of_produts = $option_name ;
        if ( ! is_array( $list_of_produts ) ) {
            $product_ids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $list_of_produts ) ) ) ;
        } else {
            $product_ids = $list_of_produts ;
        }
        $class    = '' ;
        $selected = $product_ids ;
        sumo_donation_product_select2( $multiple_name , $name , $class , $selected , $multiple , $product_selection ) ;

        return ob_get_clean() ;
    }

    // Backward Compatibility Chosen

    public static function add_chosen_to_product( $id , $product_selection ) {
        global $woocommerce ;
        ob_start() ;
        ?>
        <script type="text/javascript">
        <?php if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) { ?>
                jQuery( function () {
                    jQuery( "select#<?php echo $id ; ?>" ).ajaxChosen( {
                        method : 'GET' ,
                        url : '<?php echo admin_url( 'admin-ajax.php' ) ; ?>' ,
                        dataType : 'json' ,
                        afterTypeDelay : 100 ,
                        data : {
                            action : '<?php echo $product_selection ; ?>' ,
                            security : '<?php echo wp_create_nonce( "search-products" ) ; ?>'
                        }
                    } , function ( data ) {
                        var terms = { } ;

                        jQuery.each( data , function ( i , val ) {
                            terms[i] = val ;
                        } ) ;
                        return terms ;
                    } ) ;
                } ) ;
        <?php } ?>
        </script>
        <?php
        $getcontent = ob_get_clean() ;
        return $getcontent ;
    }

    // Add Chosen/Select2 for Backward Compatibility

    public static function add_chosen_or_select2( $id ) {
        ob_start() ;
        global $woocommerce ;
        ?>
        <script type="text/javascript">
            jQuery( function () {
        <?php if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) { ?>
                    jQuery( '#<?php echo $id ; ?>' ).chosen() ;
        <?php } else { ?>
                    jQuery( '#<?php echo $id ; ?>' ).select2() ;
        <?php } ?>
            } ) ;
        </script>
        <?php
        $content = ob_get_clean() ;
        return $content ;
    }

    public static function create_new_product( $title ) {
        $args          = array (
            'post_author'  => get_current_user_id() ,
            'post_content' => '' ,
            'post_status'  => "publish" ,
            'post_title'   => $title ,
            'post_parent'  => '' ,
            'post_type'    => "product" ,
                ) ;
        $post_id       = wp_insert_post( $args ) ;
        $meta_updation = array (
            '_visibility'        => 'visible' ,
            '_stock_status'      => 'instock' ,
            'total_sales'        => '0' ,
            '_downloadable'      => 'no' ,
            '_virtual'           => 'yes' ,
            '_regular_price'     => '0' ,
            '_price'             => '0' ,
            '_sale_price'        => '' ,
            '_featured'          => '' ,
            '_sold_individually' => 'yes' ,
            '_manage_stock'      => 'no' ,
            '_backorders'        => 'no' ,
            '_stock'             => '' ,
                ) ;
        foreach ( $meta_updation as $key => $value ) {
            update_post_meta( $post_id , $key , $value ) ;
        }
        return $post_id ;
    }

    // Worth Cost of All Products
    public static function worth_of_products( $products ) {
        $amount = 0 ;
        if ( ! is_array( $products ) ) {
            $explode = explode( ',' , $products ) ;
            if ( is_array( $explode ) && ! empty( $explode ) ) {

                foreach ( $explode as $key => $value ) {
                    if ( $value ) {
                        $product = sumo_donation_get_product( $value ) ;
                        $amount  += $product->get_price() ;
                    } else {
                        $amount += 0 ;
                    }
                }
            }
        } else {
            if ( is_array( $products ) && ( ! empty( $products )) ) {
                foreach ( $products as $key => $value ) {
                    if ( $value ) {
                        $product = sumo_donation_get_product( $value ) ;
                        $amount  += $product->get_price() ;
                    } else {
                        $amount += 0 ;
                    }
                }
            }
        }
        return $amount ;
    }

    // List of Product Title
    public static function list_of_product_title( $products ) {
        ob_start() ;
        echo "<ul>" ;
        if ( ! is_array( $products ) ) {
            $explode = explode( ',' , $products ) ;
            if ( is_array( $explode ) && ! empty( $explode ) ) {

                foreach ( $explode as $key => $value ) {
                    if ( $value ) {
                        $product = sumo_donation_get_product( $value ) ;
                        $id      = sumo_donation_get_product_parent_id( $product ) ;
                        $product = get_permalink( $id ) ;
                        $title   = get_the_title( $value ) ;
                        echo "<li><a href=" . $product . ">$title</a></li>" ;
                    }
                }
            }
        } else {
            if ( is_array( $products ) && ( ! empty( $products )) ) {
                foreach ( $products as $key => $value ) {
                    if ( $value ) {
                        $product = sumo_donation_get_product( $value ) ;
                        $id      = sumo_donation_get_product_parent_id( $product ) ;
                        $product = get_permalink( $id ) ;
                        $title   = get_the_title( $value ) ;
                        echo "<li><a href=" . $product . ">$title</a></li>" ;
                    }
                }
            }
        }
        echo "</ul>" ;
        return ob_get_clean() ;
    }

}

new FP_Donation_Common_Function() ;

function sumo_donation_product_select2( $name , $id , $class , $selected , $multiple , $product_and_variation ) {
    $array         = $multiple ? '[]' : '' ;
    $data_multiple = $multiple ? 'multiple="multiple"' : '' ;
    if ( $product_and_variation == '1' ) {
        $product_selection = "woocommerce_json_search_products" ;
    } else {
        $product_selection = 'woocommerce_json_search_products_and_variations' ;
    } if ( ( float ) WC()->version >= ( float ) '3.0.0' ) {
        ?>
        <select class="wc-product-search <?php echo $class ?>" data-action="<?php echo $product_selection ; ?>" name="<?php echo $name . $array ?>" id="<?php echo $id ?>" <?php echo $data_multiple ?> style="width:300px" data-placeholder="<?php _e( 'Search for a product&hellip;' , 'woocommerce' ) ; ?>">
            <?php
            if ( ! empty( $selected ) ) {
                foreach ( $selected as $each_value ) {
                    $product = sumo_donation_get_product( $each_value ) ;
                    if ( is_object( $product ) ) {
                        echo '<option value="' . $each_value . '" ' . selected( 1 , 1 ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>' ;
                    }
                }
            }
            ?>
        </select>
        <?php
    } elseif ( ( float ) WC()->version > ( float ) '2.2.0' && ( float ) WC()->version < ( float ) '3.0.0' ) {
        ?>
        <input type="hidden" class="wc-product-search <?php echo $class ?>" data-action="<?php echo $product_selection ; ?>" style="width: 100%;" name="<?php echo $name ?>" id="<?php echo $id ?>" data-placeholder="<?php _e( 'Search for a product&hellip;' , 'woocommerce' ) ; ?>" data-multiple="<?php echo $multiple ?>" data-selected="<?php
               $json_ids = array () ;
               if ( ! empty( $selected ) ) {
                   foreach ( $selected as $product_id ) {
                       $product = sumo_donation_get_product( $product_id ) ;
                       if ( is_object( $product ) ) {
                           $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() ) ;
                       }
                   }
               }
               echo esc_attr( json_encode( $json_ids ) ) ;
               ?>" value="<?php echo implode( ',' , array_keys( $json_ids ) ) ; ?>" />
               <?php
           } else {
               ?>
        <select id='<?php echo $id ; ?>' name="<?php echo $name . $array ; ?>" class="<?php echo $class ?>" <?php echo $data_multiple ?>>
            <?php
            if ( ! empty( $selected ) ) {
                $list_of_produts = $selected ;
                foreach ( $list_of_produts as $rs_free_id ) {
                    $product = sumo_donation_get_product( $product_id ) ;
                    if ( is_object( $product ) ) {
                        echo '<option value="' . $rs_free_id . '" ' ;
                        selected( 1 , 1 ) ;
                        echo '>' . ' #' . $rs_free_id . ' &ndash; ' . get_the_title( $rs_free_id ) . '</option>' ;
                    }
                }
            } else {
                ?>
                <option value=""></option>
                <?php
            }
            ?>
        </select>
        <?php
    }
}

function sumo_donation_get_product_id( $product ) {
    if ( ( float ) WC()->version >= ( float ) '3.0.0' ) {
        $id = $product->get_id() ;
    } else {
        $id = $product->variation_id ? $product->variation_id : $product->id ;
    }
    return $id ;
}

function sumo_donation_get_product_parent_id( $product ) {
    if ( ( float ) WC()->version >= ( float ) '3.0.0' ) {
        $id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id ;
    } else {
        $id = $product->id ;
    }
    return $id ;
}

function sumo_donation_get_product( $product_id ) {
    if ( function_exists( 'wc_get_product' ) ) {
        $product = wc_get_product( $product_id ) ;
    } else {
        if ( function_exists( 'get_product' ) ) {
            $product = get_product( $product_id ) ;
        }
    }
    return $product ;
}

function sumo_donation_get_product_type( $product ) {
    if ( ( float ) WC()->version >= ( float ) '3.0.0' ) {
        $type = $product->get_type() ;
    } else {
        $type = $product->product_type ;
    }
    return $type ;
}

function sumo_donation_get_order_billing_last_name( $order ) {
    if ( ( float ) WC()->version >= ( float ) '3.0.0' ) {
        $billing_last_name = $order->get_billing_last_name() ;
    } else {
        $billing_last_name = $order->billing_last_name ;
    }
    return $billing_last_name ;
}

function sumo_donation_get_order_billing_first_name( $order ) {
    if ( ( float ) WC()->version >= ( float ) '3.0.0' ) {
        $billing_first_name = $order->get_billing_first_name() ;
    } else {
        $billing_first_name = $order->billing_first_name ;
    }
    return $billing_first_name ;
}

function sumo_donation_get_order_billing_email( $order ) {
    if ( ( float ) WC()->version >= ( float ) '3.0.0' ) {
        $billing_email = $order->get_billing_email() ;
    } else {
        $billing_email = $order->billing_email ;
    }
    return $billing_email ;
}

function sumo_donation_get_order_status( $order ) {
    if ( ( float ) WC()->version >= ( float ) '3.0.0' ) {
        $status = $order->get_status() ;
    } else {
        $status = $order->status ;
    }
    return $status ;
}

function sumo_donation_get_order_date( $order ) {
    if ( ( float ) WC()->version >= ( float ) '3.0.0' ) {
        $date_created = wc_rest_prepare_date_response( $order->get_date_created() , false ) ;
    } else {
        $date_created = $order->order_date ;
    }
    return $date_created ;
}

function sumo_check_global_settings_to_display_df() {
    if ( get_option( '_fp_hide_donation_form_when_dp_aisin_cart' ) == 'yes' ) {
        $product_id           = get_option( 'ds_select_particular_products' ) ;
        $find_product_in_cart = '' ;
        if ( $product_id ) {
            $cart_id              = FP_DonationRewards_Cart::generate_cart_item_key( $product_id ) ;
            $find_product_in_cart = WC()->cart->find_product_in_cart( $cart_id ) ;
        }
        if ( $find_product_in_cart ) {
            $return = false ;
        } else {
            $return = true ;
        }
    } else {
        $return = true ;
    }
    return $return ;
}

function sumo_product_id_from_other_lang( $product_id ) {
    global $sitepress ;
    $id_from_other_lang = '' ;
    if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && is_object( $sitepress ) ) {
        $trid         = $sitepress->get_element_trid( $product_id ) ;
        $translations = $sitepress->get_element_translations( $trid ) ;
        foreach ( $translations as $translation ) {
            if ( $translation->language_code == ICL_LANGUAGE_CODE ) {
                $id_from_other_lang = $translation->element_id ;
            }
        }
        $product_id = $id_from_other_lang ;
    }
    return $product_id ;
}

function sumo_check_auto_donation_pro_is_in_cart( $product_id ) {
    global $sitepress , $woocommerce ;
    $find_product_in_cart = '' ;
    $id_from_other_lang   = '' ;
    if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && is_object( $sitepress ) ) {
        $trid         = $sitepress->get_element_trid( $product_id ) ;
        $translations = $sitepress->get_element_translations( $trid ) ;
        foreach ( $translations as $translation ) {
            $id_from_other_lang = $translation->element_id ;
            if ( $id_from_other_lang ) {
                $cart_id              = FP_DonationRewards_Cart::generate_cart_item_key( $id_from_other_lang ) ;
                $find_product_in_cart = $woocommerce->cart->find_product_in_cart( $cart_id ) ;
            }
            if ( $find_product_in_cart ) {
                return $find_product_in_cart ;
            }
        }
        $product_id = $id_from_other_lang ;
    } else {
        if ( $product_id ) {
            $cart_id              = FP_DonationRewards_Cart::generate_cart_item_key( $product_id ) ;
            $find_product_in_cart = $woocommerce->cart->find_product_in_cart( $cart_id ) ;
        }
    }
    return $find_product_in_cart ;
}
