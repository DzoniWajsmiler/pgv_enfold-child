<?php
/**
 * Customer on-hold order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-on-hold-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 7.3.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php

$payment_method = $order->get_payment_method();

if($payment_method == "bacs"){     
    $email_text = get_field("mail_predracun", "option");
} else if($payment_method == "cheque"){     
    $email_text = get_field("mail_obroki", "option");
} 


if($email_text):
    echo $email_text;
else: ?>

    <p><?php printf( __( 'Pozdravljeni.', 'woocommerce' ) ); ?></p>
    <p><?php _e( 'Hvala za vašo prijavo na dogodek.<br><br>Za vsa vprašanja in dodatne informacije smo vam na voljo na telefonski številki 080 33 44 ali na e-naslovu <a href="mailto:prijave@planetgv.si">prijave@planetgv.si</a>.<br><br>
Odjava z dogodka brez stroškov je možna do 4 delovne dni pred dogodkom. Odjava mora biti poslana v pisni obliki na naslov <a href="mailto:prijave@planetgv.si">prijave@planetgv.si</a>. Če se boste odjavili 3 delovne dni pred dogodkom, vam bomo zaračunali administrativne stroške v višini 30 % kotizacije. Pri poznejši odjavi ali v primeru, da odjava ni bila pisno poslana, vam bomo kotizacijo zaračunali v celoti.<br><br> Veselimo se srečanja z vami.', 'woocommerce' ); ?></p>


<?php // phpcs:ignore WordPress.XSS.EscapeOutput ?>

<?php endif; ?>



<?php
/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

	list_order_information($order);

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
//do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
// do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
