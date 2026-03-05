<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Tem
 * @version     3.7.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-order">

    <div class="avia-section main_color avia-section-default avia-no-border-styling avia-bg-style-scroll el_before_av_section  avia-builder-el-first  ow-title-section  container_wrap fullsize">
        <div class="container">
            <main role="main" class="template-page content av-content-full alpha units">
                <div class="post-entry post-entry-type-page post-entry-1192">
                    <div class="entry-content-wrapper clearfix">
                        <section class="av_textblock_section">
                            <div class="avia_textblock">
                                <h1><?php echo get_field("naslov_zahvalne_strani", "option"); ?></h1>
                                <?php echo get_field("vsebina_zahvalne_strani", "option"); ?>
                            </div>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>

</div>
