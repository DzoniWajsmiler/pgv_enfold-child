<?php

/**

 * Checkout Form

 *

 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.

 *

 * HOWEVER, on occasion WooCommerce will need to update template files and you

 * (the theme developer) will need to copy the new files to your theme to

 * maintain compatibility. We try to do this as little as possible, but it does

 * happen. When this occurs the version of the template file will be bumped and

 * the readme will list any important changes.

 *

 * @see https://docs.woocommerce.com/document/template-structure/

 * @package WooCommerce/Templates

 * @version 3.5.0

 */



if ( ! defined( 'ABSPATH' ) ) {

    exit;

}



//$order_button_text = "Oddajte prijavo";

$blog_id= get_current_blog_id();

if ($blog_id == 26 || $blog_id == 27){
    $order_button_text = __("Submit the registration", "woocommerce");
}else {
    $order_button_text = "Oddajte prijavo";
}


do_action( 'woocommerce_before_checkout_form', $checkout );



?>



<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">



    <?php if ( $checkout->get_checkout_fields() ) : ?>



        <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>



        <div class="col2-set" id="customer_details">

            <div class="col-1 ow-col">

                <?php do_action( 'woocommerce_checkout_billing' ); ?>



                <?php do_action( 'woocommerce_checkout_shipping' ); ?>



                <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

            </div>



            <div class="col-2 ow-col">



                <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>





                <div id="order_review" class="woocommerce-checkout-review-order">



                    <?php do_action( 'woocommerce_checkout_order_review' ); ?>

                </div>



                <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>



                <!-- custom button -->

                <div class="ow-custom-submit ow_button_container">

                    <div class="ow-button custom_button button--1 color-pink">

                        <?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>



                        <span class="button__container"><span class="circle top-left" style="transform: matrix(0.7071, -0.7071, 0.7071, 0.7071, 0, 0);"></span><span class="circle top-left" style="transform: matrix(0.7071, -0.7071, 0.7071, 0.7071, 0, 0);"></span><span class="circle top-left" style="transform: matrix(0.7071, -0.7071, 0.7071, 0.7071, 0, 0);"></span><span class="button__bg"></span><span class="circle bottom-right" style="transform: matrix(0.7071, -0.7071, 0.7071, 0.7071, 0, 0);"></span><span class="circle bottom-right" style="transform: matrix(0.7071, -0.7071, 0.7071, 0.7071, 0, 0);"></span><span class="circle bottom-right" style="transform: matrix(0.7071, -0.7071, 0.7071, 0.7071, 0, 0);"></span></span>

                    </div>

                </div>



            </div>





        </div>







    <?php endif; ?>







</form>



<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

