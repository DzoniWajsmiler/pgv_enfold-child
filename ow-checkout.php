<?php

/* checkout fields settings */

/* allow only one item in cart - empty cart before adding new item */
add_filter('woocommerce_add_cart_item_data', 'woo_custom_add_to_cart');

function woo_custom_add_to_cart($cart_item_data)
{
    global $woocommerce;
    $woocommerce->cart->empty_cart();

    $qty = $_GET['quantity'];
    WC()->session->set('ow_qty', $qty);

    return $cart_item_data;
}


/* remove message "added to cart" */
add_filter('wc_add_to_cart_message', 'wc_add_to_cart_message_filter', 10, 2);
function wc_add_to_cart_message_filter($message, $product_id = null)
{
    $message = "";
    return $message;
}

/* change order of price & payment fields */
remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);
remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
add_action('woocommerce_checkout_before_terms_and_conditions', 'ow_before_woocommerce_order_review', 20);
add_action('woocommerce_checkout_before_terms_and_conditions', 'woocommerce_order_review', 21);
add_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 10);

function ow_before_woocommerce_order_review()
{
    echo "<h4 id='order_review_heading'>" . __('Cena', 'woocommerce') . "</h4>";
}


function get_module_data($product_id, $variation_id)
{
    $module_name = $module_date = '';
    $variation = new WC_Product_Variation($variation_id);
    $modules = get_field("ow_event_akademija_moduli", $product_id);

    if ($modules) {
        foreach ($modules as $module) {
            $selected_variation_id = $module['ow_event_srecanje_modul'];

            if ($selected_variation_id != $variation_id) {
                continue;
            }

            $module_date = $module['ow_event_srecanje_date'];
            $module_date = date('d. m. Y', strtotime($module_date));
            $module_name = $variation->name;
        }
    }

    $module_data = Array(
        'name' => $module_name,
        'date' => $module_date
    );

    return $module_data;
}

function get_parent_id($product_id){
    $parent_id = wp_get_post_parent_id($product_id);

    if (!$parent_id || $parent_id < 1) {
        $parent_id = $product_id;
    }

    return $parent_id;
}


/* Event fields */
/* Product title */
add_action('woocommerce_before_checkout_form', 'ow_event_fields');
function ow_event_fields($checkout, $product_id = null)
{

    echo '<div id="ow_event_fields">';

    echo '<h4>' . __('PRIJAVA NA DOGODEK') . '</h4>';

    foreach (WC()->cart->get_cart() as $cart_item) {
        $product_in_cart = $cart_item['product_id'];
        $parent_id = get_parent_id($product_in_cart);

        $title = get_the_title($parent_id);

        $start_date = get_field("ow_event_start_date", $product_in_cart);
        $end_date = get_field("ow_event_end_date", $product_in_cart);

           

        /*$start_date = str_replace('/', '-', $start_date);
        if ($end_date && trim($end_date) != '') {
            $end_date = str_replace('/', '-', $end_date);
        }*/
        

        if (($start_date == $end_date) || !$end_date || trim($end_date) == '') {
            $dateObj = DateTime::createFromFormat("m/d/Y", $start_date);
            $event_date = $dateObj->format("j. n. Y");
        } else {
            $dateObj_start = DateTime::createFromFormat("m/d/Y", $start_date);
            $start_date = $dateObj_start->format("j. n. Y");
            $dateObj_end = DateTime::createFromFormat("d/m/Y", $end_date);
            $end_date = $dateObj_end->format("j. n. Y");
            $event_date = $start_date . " - " . $end_date;
        }

        if (isset($cart_item['variation_id']) and $cart_item['variation_id'] > 0) {
            $variation_id = $cart_item['variation_id'];
            $module_data = get_module_data($product_in_cart, $variation_id);

            if ($module_data) {
                if ($module_data['name'] != '') {
                    $title = $module_data['name'];
                }
                if ($module_data['date'] != '') {
                    $event_date = $module_data['date'];
                }
            }
        }

        $location_data = get_event_address($product_in_cart);
        $location = $location_data['location_full'];
        
        if ($title) {
            echo '<h3>' . $title . '</h3>';
            echo '<div class="ow-checkout-event-details">';
            echo '<p><b class="event-details-label">Termin:</b> ' . $event_date . '</p>';
            if($location) {
                echo '<p><b class="event-details-label">Lokacija:</b> ' . $location . '</p>';
            }
            echo '</div>';
            break;
        }
    }
    echo '</div>';

}


remove_action('woocommerce_checkout_terms_and_conditions', 'wc_terms_and_conditions_page_content', 30);
remove_action('woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20);
//add_action('woocommerce_checkout_terms_and_conditions', 'ow_checkout_privacy_policy_text', 10, 1);


function ow_checkout_privacy_policy_text()
{

    $privacy_args = Array(
        'type' => 'checkbox',
        'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label">' . get_field("privacy_policy_text", "option") . '</span>'
    );

    woocommerce_form_field('privacy-policy-checkbox', $privacy_args, $value = null);

}

add_action('woocommerce_form_field_text', 'additional_paragraph_after_billing_address_2', 10, 4);
function additional_paragraph_after_billing_address_2($field, $key, $args, $value)
{
  if (is_checkout() && $key == 'billing_address_1') {
    $field2 = applicant_fields() . $field;
    return $field2;
  }
  return $field;
}
function applicant_fields()
{
  ob_start();
  woocommerce_form_field('applicant', ['type' => 'checkbox', 'class' => ['ow-conditional-required-checkbox'], 'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label conditional-checkbox">' . __('Prijavo oddajam za druge udeležence in želim prejeti potrdilo o prijavi in račun', 'woocommerce') . '<span class="req-star">(*)</span></span>'], $value = null);
  echo '<div class="applicant-fields">';

  woocommerce_form_field('applicant_first_name', array(
    'type' => 'text',
    'class' => array('form-row-wide', 'ow-conditional-required'),
    'label' => __('Ime prijavitelja', 'woocommerce'),
    'autocomplete' => 'given-name',
    'required' => 0
  ));

  woocommerce_form_field('applicant_last_name', array(
    'type' => 'text',
    'class' => array('form-row-wide', 'ow-conditional-required'),
    'label' => __('Priimek prijavitelja', 'woocommerce'),
    'autocomplete' => 'family-name',
    'required' => 0
  ));

  /*woocommerce_form_field('applicant_company', array(
    'type' => 'text',
    'class' => array('form-row-wide'),
    'label' => __('Delovno mesto prijavitelja', 'woocommerce'),
    'autocomplete' => 'organization',
    'required' => 0

  ));*/

  woocommerce_form_field('applicant_email', array(
    'type' => 'email',
    'class' => array('form-row-wide', 'ow-conditional-required'),
    'label' => __('E-pošta prijavitelja', 'woocommerce'),
    'autocomplete' => 'email',
    'required' => 0,
    'validate' => array('email'),

  ));

  woocommerce_form_field('applicant_phone', array(
    'type' => 'tel',
    'class' => array('form-row-wide'),
    'label' => __('Telefon prijavitelja', 'woocommerce'),
    'autocomplete' => 'tel',
    'required' => 0,
    'validate' => array('phone'),
   // 'description' => get_default_field('checkout_info_phone_text', 'options')

  ));

  /*$privacy_args = array(
    'type' => 'checkbox',
    'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label">Prijavitelj - ' . get_default_field("privacy_policy_text", "option") . '</span>',
    'class' => ['ow-conditional-required-checkbox'],
  );*/

  /*echo '<div class="gdpr-options">';
  woocommerce_form_field('applicant_gdpr_checkbox', $privacy_args, $value = null);
  //woocommerce_form_field('gdpr_all', ['type' => 'checkbox', 'label' => '<span class="gdpr-all woocommerce-privacy-policy-text ow-checkbox-label conditional-checkbox">' . __('Vsi kanali', 'woocommerce') . '</span>'], $value = null);
  woocommerce_form_field('applicant_gdpr_email', ['type' => 'checkbox', 'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label conditional-checkbox">' . __('E-mail', 'woocommerce') . '</span>'], $value = null);
  woocommerce_form_field('applicant_gdpr_sms', ['type' => 'checkbox', 'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label conditional-checkbox">' . __('SMS', 'woocommerce') . '</span>'], $value = null);
  woocommerce_form_field('applicant_gdpr_phone', ['type' => 'checkbox', 'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label conditional-checkbox">' . __('Phone', 'woocommerce') . '</span>'], $value = null);
  woocommerce_form_field('applicant_gdpr_post', ['type' => 'checkbox', 'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label conditional-checkbox">' . __('Pošta', 'woocommerce') . '</span>'], $value = null);
  echo '</div>';*/

  /*woocommerce_form_field('applicant_gdpr_privacy', [
    'type' => 'checkbox',
    'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label">Prijavitelj - strinjam se, da se moji podatki analizirajo za namen priprave optimalne in prilagojene ponudbe izdelkov in storitev. V kolikor ne oddate soglasja za obveščanje in izvajanje analiz ter tržnih raziskav, ne boste mogli prejemati posebnih ponudb izdelkov in storitev, prilagojenih posebej za vas</span>',
    'class' => ['ow-conditional-required-checkbox', 'ow-conditional-required'],
    'required' => 1
  ]);*/

 /* woocommerce_form_field('applicant_gdpr_terms', [
    'type' => 'checkbox',
    'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label">Prijavitelj - potrjujem, da sem seznanjen s Splošnimi pogoji</span>',
    'class' => ['ow-conditional-required-checkbox', 'ow-conditional-required'],
    'required' => 1
  ]);*/
  echo '</div>';


  return ob_get_clean();
}

add_filter( 'default_checkout_billing_on_company', 'custom_override_default_billing_on_company' );

function custom_override_default_billing_on_company( $value ) {
    // $value is read from logged in user's meta or null or the result of previous filters
    if ( is_null($value) ) {
        // Only use default if null, aka. if user has no previous preference saved.
        $value = 1;
    }
    return $value;
}

add_action('woocommerce_before_checkout_billing_form', 'fields_title', 11);
function fields_title() {
      echo '<h4>Podatki o plačniku računa</h4>';
}
/* Additional ticket fields */
add_action('woocommerce_before_checkout_billing_form', 'ow_new_ticket', 10);

function add_new_person($i)
{
    /* save id in session for validation purpose */
    $old_additional_fields = Array();
    if (WC()->session->get('additional_fields_ids') && $i > 2) {
        $old_additional_fields = WC()->session->get('additional_fields_ids');
    }
    if (!in_array($i, $old_additional_fields)) {
        array_push($old_additional_fields, $i);
    }

    WC()->session->set('additional_fields_ids', $old_additional_fields);

    /* generate content */
    echo '<div class="ow_new_person ow_event_new_ticket_' . $i . '" data-index=' . $i . '>';

     woocommerce_form_field('additional_first_name_' . $i, array(
    'type' => 'text',
    'class' => array('form-row-wide'),
    'label' => __('Ime udeleženca', 'woocommerce'),
    'autocomplete' => 'given-name',
    'required' => 1

  ));
  woocommerce_form_field('additional_last_name_' . $i, array(
    'type' => 'text',
    'class' => array('form-row-wide'),
    'label' => __('Priimek udeleženca', 'woocommerce'),
    'autocomplete' => 'family-name',
    'required' => 1

  ));
  woocommerce_form_field('additional_company_' . $i, array(
    'type' => 'text',
    'class' => array('form-row-wide'),
    'label' => __('Delovno mesto udeleženca', 'woocommerce'),
    'autocomplete' => 'organization',
    'required' => 0

  ));

    woocommerce_form_field('additional_email_' . $i, array(
    'type' => 'email',
    'class' => array('form-row-wide'),
    'label' => __('E-pošta udeleženca', 'woocommerce'),
    'autocomplete' => 'email',
    'required' => 1,
    'validate' => array('email'),

  ));

    woocommerce_form_field('additional_phone_' . $i, array(
    'type' => 'tel',
    'class' => array('form-row-wide ow-info'),
    'label' => __('Telefon udeleženca', 'woocommerce'),
    'autocomplete' => 'tel',
    'required' => 0,
    'validate' => array('phone'),
    //'description' => get_default_field('checkout_info_phone_text', 'options')

  ));
  
  $privacy_args = array(
    'type' => 'checkbox',
    'label' => '<span class="ow-checkbox-label">Želim, da me obveščate o novostih in dogodkih s tega področja</span>',
    'class' => ['ow-conditional-required-checkbox', 'main-checkbox']
  );


  echo '<div class="gdpr-options">';
  woocommerce_form_field('additional_gdpr_checkbox_' . $i, $privacy_args, $value = null);
  //woocommerce_form_field('gdpr_all', ['type' => 'checkbox', 'label' => '<span class="gdpr-all ow-checkbox-label conditional-checkbox">' . __('Vsi kanali', 'woocommerce') . '</span>'], $value = null);
  woocommerce_form_field('additional_gdpr_email_' . $i, ['type' => 'checkbox', 'label' => '<span class="ow-checkbox-label conditional-checkbox">' . __('E-mail', 'woocommerce') . '</span>'], $value = null);
  woocommerce_form_field('additional_gdpr_sms_' . $i, ['type' => 'checkbox', 'label' => '<span class="ow-checkbox-label conditional-checkbox">' . __('SMS', 'woocommerce') . '</span>'], $value = null);
  woocommerce_form_field('additional_gdpr_phone_' . $i, ['type' => 'checkbox', 'label' => '<span class="ow-checkbox-label conditional-checkbox">' . __('Telefon', 'woocommerce') . '</span>'], $value = null);
  woocommerce_form_field('additional_gdpr_post_' . $i, ['type' => 'checkbox', 'label' => '<span class="ow-checkbox-label conditional-checkbox">' . __('Pošta', 'woocommerce') . '</span>'], $value = null);
  echo '</div>';
  
   woocommerce_form_field('additional_gdpr_terms_' . $i, [
    'type' => 'checkbox',
    'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label">Potrjujem, da sem seznanjen s Splošnimi pogoji</span>',
    'class' => ['ow-conditional-required-checkbox', 'validate-required']
  ]);


    echo '<span class="ow_remove_ticket" data-number="' . $i . '">Odstrani udeleženca</span>';

    echo '</div>';
}

function ow_new_ticket($checkout)
{

    echo '<h4>Udeleženec</h4>';
  woocommerce_form_field('additional_first_name_1', array(
    'type' => 'text',
    'class' => array('form-row-wide'),
    'label' => __('Ime udeleženca', 'woocommerce'),
    'autocomplete' => 'given-name',
    'required' => 1
  ));
  woocommerce_form_field('additional_last_name_1', array(
    'type' => 'text',
    'class' => array('form-row-wide'),
    'label' => __('Priimek udeleženca', 'woocommerce'),
    'autocomplete' => 'family-name',
    'required' => 1

  ));

  woocommerce_form_field('additional_company_1', array(
    'type' => 'text',
    'class' => array('form-row-wide'),
    'label' => __('Delovno mesto udeleženca', 'woocommerce'),
    'autocomplete' => 'organization',
    'required' => 0

  ));

  woocommerce_form_field('additional_email_1', array(
    'type' => 'email',
    'class' => array('form-row-wide'),
    'label' => __('E-pošta udeleženca', 'woocommerce'),
    'autocomplete' => 'email',
    'required' => 1,
    'validate' => array('email'),

  ));

  woocommerce_form_field('additional_phone_1', array(
    'type' => 'tel',
    'class' => array('form-row-wide ow-info'),
    'label' => __('Telefon udeleženca', 'woocommerce'),
    'autocomplete' => 'tel',
    'required' => 0,
    'validate' => array('phone'),
   // 'description' => get_default_field('checkout_info_phone_text', 'options')
  ));



  $privacy_args = array(
    'type' => 'checkbox',
    'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label">Želim, da me obveščate o novostih in dogodkih s tega področja</span>',
    'class' => ['ow-conditional-required-checkbox', 'main-checkbox']
  );
  

  echo '<div class="gdpr-options">';
  //woocommerce_form_field('additional_gdpr_checkbox_1', $privacy_args, $value = null);
  //woocommerce_form_field('gdpr_all', ['type' => 'checkbox', 'label' => '<span class="gdpr-all woocommerce-privacy-policy-text ow-checkbox-label conditional-checkbox">' . __('Vsi kanali', 'woocommerce') . '</span>'], $value = null);
  woocommerce_form_field('additional_gdpr_email_1', ['type' => 'checkbox', 'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label conditional-checkbox">' . __('E-mail', 'woocommerce') . '</span>'], $value = null);
  woocommerce_form_field('additional_gdpr_sms_1', ['type' => 'checkbox', 'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label conditional-checkbox">' . __('SMS', 'woocommerce') . '</span>'], $value = null);
  woocommerce_form_field('additional_gdpr_phone_1', ['type' => 'checkbox', 'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label conditional-checkbox">' . __('Phone', 'woocommerce') . '</span>'], $value = null);
  woocommerce_form_field('additional_gdpr_post_1', ['type' => 'checkbox', 'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label conditional-checkbox">' . __('Pošta', 'woocommerce') . '</span>'], $value = null);
  echo '</div>';

 /* woocommerce_form_field('additional_gdpr_privacy_1', [
    'type' => 'checkbox',
    'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label">Strinjam se, da se moji podatki analizirajo za namen priprave optimalne in prilagojene ponudbe izdelkov in storitev. V kolikor ne oddate soglasja za obveščanje in izvajanje analiz ter tržnih raziskav, ne boste mogli prejemati posebnih ponudb izdelkov in storitev, prilagojenih posebej za vas</span>',
    'class' => ['ow-conditional-required-checkbox', 'validate-required']
  ]);*/

/*  woocommerce_form_field('additional_gdpr_terms_1', [
    'type' => 'checkbox',
    'label' => '<span class="woocommerce-privacy-policy-text ow-checkbox-label">Potrjujem, da sem seznanjen s Splošnimi pogoji</span>',
    'class' => ['ow-conditional-required-checkbox', 'validate-required']
  ]);
*/
  echo '<div class="ow_event_new_ticket">';
  echo '<h4>' . __('Prijavljate več oseb?', 'woocommerce') . '</h4>';
  echo '<div id="ow_event_new_ticket_fields" class="ow_event_new_ticket_fields" >';

  $items_count = WC()->cart->get_cart_contents_count();

  if (!empty(WC()->session->get('ow_qty'))) {
    $qty = WC()->session->get('ow_qty');
  } else {
    $qty = 1;
  }

  if ($items_count > 0 && (int)$qty > 1) {
    for ($i = 1, $j = 2; $i < (int)$qty; $i++, $j++) {
      echo add_new_person($j);
    }
  }

  echo '</div>';
  echo '<span class="ow_add_ticket" ><span class="plus">+</span><span class="text">Dodaj udeleženca</span></span>';
  echo '</div>';
}


/* ajax script for adding/removing new tickets in checkout */
add_action('wp_footer', 'custom_checkout_script');
function custom_checkout_script()
{
    ?>
    <script type="text/javascript">
    jQuery(function($) {
      setTimeout(function() {
        $('#billing_address_1').attr('placeholder', '');
        $('#billing_address_1_field label').text('Ulica in hišna številka')
      }, 300);

       var ticket_number = '';

            <?php if(WC()->session->get('ow_qty')): ?>
            var ticket_number = <?php echo WC()->session->get('ow_qty'); ?>;
            <?php endif; ?>

            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

            // update cart on delivery location checkbox option
            $("body").on("click", '.ow_add_ticket', function () {
                change_number_of_tickets("add", false, $(this));
            });

            $("body").on("click", '.ow_remove_ticket', function () {
                var remove_ticket_number = $(this).data("number");
                change_number_of_tickets("remove", remove_ticket_number, $(this));
            });


            function change_number_of_tickets(change, remove_ticket_number, el) {

                if(!el.hasClass("disabled")) {

                var additional_fields_ids = Array();

                if (change === "add") {
                    ticket_number++;

                    var last_ticket_index = $("#ow_event_new_ticket_fields .ow_new_person").last().data("index");

                    if (last_ticket_index < 1 || !last_ticket_index) {
                        last_ticket_index = 1;
                    }
                    var new_ticket_index = last_ticket_index + 1;

                    additional_fields_ids.push(new_ticket_index);

                    console.log("new_ticket_index: " + new_ticket_index);
                } else {
                    if (ticket_number > 1) {
                        ticket_number--;
                    } else {
                        ticket_number = 1;
                    }
                }

                $("#ow_event_new_ticket_fields .ow_new_person").each(function () {
                    var index = $(this).data("index");
                    if (index === remove_ticket_number) {
                        return;
                    }
                    additional_fields_ids.push(index);
                });

                console.log(additional_fields_ids);

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        'action': 'woo_get_ajax_data',
                        'ow_qty': ticket_number,
                        'new_ticket_index': new_ticket_index,
                        'change': change,
                        'additional_fields_ids': additional_fields_ids
                    },
                    beforeSend: function (xhr) {
                        el.addClass("disabled");
                        el.parent().addClass("ow-loader-active new_ticket_loader " + change);
                    },
                    success: function (result) {

                        console.log("new QTY: " + ticket_number);

                        if (change === "add") {
                            $("#ow_event_new_ticket_fields").append(result);
                            $("#ow_event_new_ticket_fields").find('.ow-checkbox-label').addClass('foo');

                            $("#ow_event_new_ticket_fields .ow_event_new_ticket_" + new_ticket_index + " .form-row:not(.validate-required) input").each(function () {
                                $(this).after("<span class='ow-optional'>Opcijsko</span>");
                            });
                            $("#ow_event_new_ticket_fields .ow_event_new_ticket_" + new_ticket_index + " .ow-info input").each(function () {
                                $(this).after("<span class='ow-info-icon-wrap'><span class='ow-info-icon'>i</span></span>");
                            });
                            $("#ow_event_new_ticket_fields .ow_event_new_ticket_" + new_ticket_index + " .ow-info-icon-wrap").on("click", function () {
                                $(this).siblings(".description").toggleClass("active");
                            });

                        } else {
                            console.log("removed ticket number: " + remove_ticket_number);
                            var field = $("#ow_event_new_ticket_fields .ow_event_new_ticket_" + remove_ticket_number);
                            field.slideUp();
                            field.remove();
                        }

                        $('body').trigger('update_checkout');
                    },
                    error: function (error) {
                        console.log(error); // just for testing
                    },
                    complete: function () {
                        el.removeClass("disabled");
                        el.parent().removeClass("ow-loader-active new_ticket_loader " + change);
                    }
                });
                } else {
                    console.log("skip click event - multiple clicks");
                }
            }

      if ($('#applicant').prop("checked")) {
        $('.applicant-fields').find(".ow-conditional-required").addClass("validate-required");
        $('.applicant-fields').find(".ow-conditional-required .ow-optional").remove();
      }

      $('#applicant').on("change", function() {
        if ($(this).prop("checked")) {
          $('.applicant-fields').find(".ow-conditional-required").addClass("validate-required");
          $('.applicant-fields').find(".ow-conditional-required .ow-optional").remove();
        } else {
          $('.applicant-fields').find(".ow-conditional-required").removeClass("validate-required");
        }
      });

      /* make vat & company fields required if checkbox is checked*/
      var payment_on_company_checkbox = $("#billing_on_company_field .ow-conditional-required-checkbox .input-checkbox");

      if (payment_on_company_checkbox.prop("checked")) {
        console.log("checked by default");
        $(".ow-conditional-required").addClass("validate-required");
        $(".ow-conditional-required .ow-optional").remove();
      }

      $('#billing_on_company').on("change", function() {
        if ($(this).prop("checked")) {
            $('#applicant_field').show();
          $('#billing_vat_field').addClass("validate-required");
          $('#billing_vat_field').find(".ow-optional").remove();
          $('#billing_company_address_field').addClass("validate-required");
          $('#billing_company_address_field').find(".ow-optional").remove();
          $('#order_number_field').show();
        } else {
          $('#billing_vat_field').removeClass("validate-required");
          $('#billing_company_address_field').removeClass("validate-required");
          $('#applicant_field').hide();
          $('#order_number_field').hide();
        }
      });

      /* if (gdpr_checkbox.prop("checked")) {
           console.log("checked by default");
           $(".ow-conditional-required").addClass("validate-required");
           $(".ow-conditional-required .ow-optional").remove();
       }*/

        /*$(document).on('change', '.main-checkbox .input-checkbox', function() {

        if ($(this).prop("checked")) {
            $('.gdpr-options').css('display', 'block');
            $('.gdpr-options > p').css('display', 'block !important');
          $(this).parent('label').find('.foo').addClass('checked');
          console.log("gdpr checked");
          $(this).closest('.gdpr-options').find('.form-row').attr('style', 'display: flex !important');
        } else {
            $('.gdpr-options').css('display', 'none');
            $('.gdpr-options > p').css('display', 'none !important');
          $(this).parent('label').find('.foo').removeClass('checked');
          console.log("gdpr UNchecked");
          $(this).closest('.gdpr-options').find('.form-row:not(.ow-conditional-required-checkbox) .input-checkbox').prop('checked', false);
          $(this).closest('.gdpr-options').find('.form-row').not('.ow-conditional-required-checkbox').hide();
        }
      });*/
      
      $(document).on('change', '.gdpr-options .ow-conditional-required-checkbox .input-checkbox', function() {

        if ($(this).prop("checked")) {
          $(this).parent('label').find('.foo').addClass('checked');
          console.log("gdpr checked");
          $(this).closest('.gdpr-options').find('.form-row').attr('style', 'display: flex !important');
          $(this).closest('.gdpr-options').find('input').addClass('checked');
          $(this).closest('.gdpr-options').find('input').prop('checked', true);
          $(this).closest('.gdpr-options').find('label').find('.foo').addClass('checked');
        } else {
          $(this).parent('label').find('.foo').removeClass('checked');
          console.log("gdpr UNchecked");
          $(this).closest('.gdpr-options').find('.form-row:not(.ow-conditional-required-checkbox) .input-checkbox').prop('checked', false);
          $(this).closest('.gdpr-options').find('.form-row').not('.ow-conditional-required-checkbox').hide();
          $(this).closest('.gdpr-options').find('input').removeClass('checked');
          $(this).closest('.gdpr-options').find('input').prop('checked', false);
          $(this).closest('.gdpr-options').find('label').find('.foo').removeClass('checked');
        }
      });

      //.form-row:not(.ow-conditional-required-checkbox)
      //:not(#gdpr_all)
      $('body').on('click', '.ow_event_new_ticket .input-checkbox', function() {

        if ($(this).prop("checked")) {
          $(this).parent('label').find('.foo').addClass('checked');
        } else {
          $(this).closest('.gdpr-options').find('.input-checkbox').prop('checked', false);
          $(this).parent('label').find('.foo').removeClass('checked');
        }
      });

      $('#customer_details').on('change', "input[class^='additional_gdpr_checkbox']", function() {
        console.log('test')
        if ($(this).prop("checked")) {
          console.log('test checked')
          $(this).closest('.gdpr-options').find('input').prop('checked', true);
          $(this).siblings('.ow-checkbox-label').addClass('checked');
        } else {
          console.log('test unchecked')
          $(this).closest('.gdpr-options').find('.input-checkbox').prop('checked', false);
          $(this).closest('label').find('span.foo').removeClass('checked');
          $(this).closest('.gdpr-options').find('p:first-child .input-checkbox').prop('checked', true);
        }
      });

      $(document).on('change', '#applicant_field', function() {
        $('.applicant-fields').slideToggle(400);
      });
    });
  </script>
    <?php
}


/* function, executed after ajax call*/
add_action('wp_ajax_woo_get_ajax_data', 'woo_get_ajax_data');
add_action('wp_ajax_nopriv_woo_get_ajax_data', 'woo_get_ajax_data');
function woo_get_ajax_data()
{
    $qty = 1;
    $change = "add";

    if (isset($_POST['ow_qty'])) {
        WC()->session->set('ow_qty', $_POST['ow_qty']);
    } else {
        WC()->session->set('ow_qty', '1');
    }

    if (isset($_POST['additional_fields_ids'])) {
        WC()->session->set('additional_fields_ids', $_POST['additional_fields_ids']);
    } else {
        WC()->session->set('additional_fields_ids', Array());
    }

    if (isset($_POST['change'])) {
        $change = $_POST['change'];
    }

    if (isset($_POST['new_ticket_index'])) {
        $new_ticket_index = $_POST['new_ticket_index'];
    }

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $cart_item['quantity'] = WC()->session->get('ow_qty');
        $qty = $cart_item['quantity'];
    }

    if ($change == "add") {
        if (!$new_ticket_index) {
            $new_ticket_index = $qty;
        }
        echo add_new_person($new_ticket_index);
    }

    die(); // Always at the end (to avoid server error 500)
}


add_action('woocommerce_before_calculate_totals', 'change_cart_item_quantities', 20, 1);
function change_cart_item_quantities($cart, $product_ids = null)
{
    if (is_admin() && !defined('DOING_AJAX'))
        return;

    if (did_action('woocommerce_before_calculate_totals') >= 2)
        return;

    // HERE below define your specific products IDs
    $new_qty = 1;
    if (WC()->session->get('ow_qty')) {
        $new_qty = WC()->session->get('ow_qty');
    }

    // Checking cart items
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
//        $product_id = $cart_item['data']->get_id();
        $product_id = $cart_item['product_id'];

        $array_rule_sets = get_post_meta($product_id, '_pricing_rules', true);

        $new_individual_price = wc_get_product($product_id)->get_price(); //original price for one person

        if (isset($cart_item['variation_id']) and $cart_item['variation_id'] > 0) {
            $variation_id = $cart_item['variation_id']; 
            $variation = new WC_Product_Variation($variation_id);

            $new_individual_price = $variation->get_price();

            if (!empty($array_rule_sets)) {
                foreach ($array_rule_sets as $array_rule_set) {
                    if (in_array($cart_item['variation_id'], $array_rule_set['variation_rules']['args']['variations'])) {
                        $rules = $array_rule_set['rules'];

                        foreach ($rules as $key => $rule) {
                            if ($new_qty >= $rule['from'] && ($new_qty <= $rule['to'] || empty($rule['to']))) {
                                $new_individual_price = $rule['amount'];
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            if (!empty($array_rule_sets)) {
                foreach ($array_rule_sets as $pricing_rule_sets) {
                    foreach ($pricing_rule_sets['rules'] as $key => $value) {
                        if ($new_qty >= $value['from'] && ($new_qty <= $value['to'] || empty($value['to']))) {
                            $new_individual_price = $pricing_rule_sets['rules'][$key]['amount'];
                            break;
                        }
                    }
                }
            }
        }

        // Check for specific product IDs and change quantity
        if ($cart_item['quantity'] != $new_qty) {
            $cart->set_quantity($cart_item_key, $new_qty); // Change quantity
        }
        $cart_item['data']->set_price($new_individual_price); //change price
    }
}


/**
 * Process the checkout - custom validation
 */
add_action('woocommerce_checkout_process', 'ow_custom_validation');

function ow_custom_validation()
{
  if (WC()->session->get('additional_fields_ids')) {
    $additional_fields_ids = WC()->session->get('additional_fields_ids');
    $additional_fields_ids[] = 1;
  } else {
    $additional_fields_ids[] = 1;
  }

  if (isset($_POST['additional_email']) && trim($_POST['additional_email']) == '') {
    wc_add_notice(__('Izpolnite polja.', 'woocommerce'), 'error');
  }

  if (isset($_POST['additional_email']) && trim($_POST['additional_email']) == '') {
    wc_add_notice(__('Izpolnite polja.', 'woocommerce'), 'error');
  }

  if (isset($_POST['additional_email']) && trim($_POST['additional_email']) == '') {
    wc_add_notice(__('Izpolnite polja.', 'woocommerce'), 'error');
  }

  if (isset($_POST['billing_on_company']) && $_POST['billing_on_company'] == 1) {
    if (isset($_POST['billing_vat']) && trim($_POST['billing_vat']) == '') {
      wc_add_notice(__('When applying with company <strong> VAT code </strong> is required.', 'woocommerce'), 'error');
    }
    if (isset($_POST['billing_company_address']) && trim($_POST['billing_company_address']) == '') {
      wc_add_notice(__('When applying with company <strong> Company </strong> is required.', 'woocommerce'), 'error');
    }
  }

  foreach ($additional_fields_ids as $id) {

    if (isset($_POST['additional_first_name_' . $id]) && trim($_POST['additional_first_name_' . $id]) == '') {
      wc_add_notice(__('<strong>Full name</strong> is required for additional person.', 'woocommerce'), 'error');
    }
    if (isset($_POST['additional_first_name_' . $id]) && trim($_POST['additional_first_name_' . $id]) == '') {
      wc_add_notice(__('Izpolnite polja.', 'woocommerce'), 'error');
    }
    if (isset($_POST['additional_email_' . $id]) && (trim($_POST['additional_email_' . $id]) == '' || !is_email(trim($_POST['additional_email_' . $id])))) {
      wc_add_notice(__('<strong>E-mail</strong> is required for additional person.', 'woocommerce'), 'error');
    }
    if (isset($_POST['additional_email_' . $id]) && (trim($_POST['additional_email_' . $id]) == '' || !is_email(trim($_POST['additional_email_' . $id])))) {
      wc_add_notice(__('<strong>E-mail</strong> is required for additional person.', 'woocommerce'), 'error');
    }
  }
  
  $emails = [];
 
   foreach($additional_fields_ids as $id) {
      if(isset($_POST['additional_email_' . $id])) {
        $email = trim($_POST['additional_email_'.$id]);
        if(in_array($email, $emails)) {
            wc_add_notice( 'Udeleženci morajo imeti različne e-poštne naslove.', 'error');
            break;
        }
        $emails[] = $email;
      }
  }
  
}


add_filter('gettext', 'ow_translate_words');
add_filter('ngettext', 'ow_translate_words');
function ow_translate_words( $translated ) {
    $words = array(
        'Card Number' => 'Številka kratice',
        'Expiry Date' => 'Datum poteka',
        'Card Code (CVC)' => 'Koda (CVC)',
        'The card was declined.'=>'Kartica je bila zavrnjena.',
        'The card number is incomplete.'=>'Številka kartice ni veljavna.',
        'The Številka kratice is incomplete.'=>'Številka kartice ni veljavna.',
        'The card number is not a valid credit card number.'=>'Številka kartice ni veljavna.',
        'The Številka kratice is not a valid credit Številka kratice.'=>'Številka kartice ni veljavna.',
        'The card\'s expiration date is incomplete.'=>'Datum poteka ni veljaven.',
        'The card\'s expiration year is in the past'=>'Datum poteka je v preteklosti.',
        'The card\'s security code is incomplete.'=>'Varnostna koda ni veljavna.',
        'The card\'s security code is incorrect.'=>'Varnostna koda je nepravilna.',
        'An error occurred while processing the card.'=> 'Pri obdelavi kartice je prišlo do napake.',
        'Unable to process this payment, please try again or use alternative method.'=>'Dokončanje plačila ni mogoče, prosimo poskusite kasneje ali uporabite drugo plačilno metodo.',
        "Plačnik %s" => "%s",
        'Thank you for your order'=>'Hvala za vašo prijavo',
    );
    $translated = str_ireplace(  array_keys($words),  $words,  $translated );
    return $translated;
}


/**
 * Update the order meta with additional field values
 */
add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta');

function my_custom_checkout_field_update_order_meta($order_id)
{

  if (WC()->session->get('additional_fields_ids')) {
    $additional_fields_ids = WC()->session->get('additional_fields_ids');
  } else {
    $additional_fields_ids = array();
  }
  
 /* $order = wc_get_order($order_id);
  foreach($order->get_items() as $key => $item) {
     // $ref_id =
  }*/


  update_post_meta($order_id, '_kotizacija_id', sanitize_text_field($_POST['kotizacija_id']));
  update_post_meta($order_id, '_ref_id', sanitize_text_field($_POST['ref_id']));

  if (!empty($_POST['applicant_first_name']) && !empty($_POST['applicant_first_name'])) {
    update_post_meta($order_id, '_billing_first_name', sanitize_text_field($_POST['applicant_first_name']));
    update_post_meta($order_id, '_billing_last_name', sanitize_text_field($_POST['applicant_last_name']));
    update_post_meta($order_id, '_applicant_first_name', sanitize_text_field($_POST['applicant_first_name']));
    update_post_meta($order_id, '_applicant_last_name', sanitize_text_field($_POST['applicant_last_name']));
  } else {
    update_post_meta($order_id, '_billing_first_name', sanitize_text_field($_POST['additional_first_name_1']));
    update_post_meta($order_id, '_billing_last_name', sanitize_text_field($_POST['additional_last_name_1']));
  }

  if (!empty($_POST['applicant_email']) && !empty($_POST['applicant_email'])) {
    update_post_meta($order_id, '_billing_email', sanitize_text_field($_POST['applicant_email']));
  } else {
    update_post_meta($order_id, '_billing_email', sanitize_text_field($_POST['additional_email_1']));
  }

  if (!empty($_POST['applicant_phone']) && !empty($_POST['applicant_phone'])) {
    update_post_meta($order_id, '_billing_phone', sanitize_text_field($_POST['applicant_phone']));
  } else {
    update_post_meta($order_id, '_billing_phone', sanitize_text_field($_POST['additional_phone_1']));
  }

  // udeleženci
  for ($id = 1; $id < 99; $id++) {
    //foreach ($additional_fields_ids as $id) {

    if (!empty($_POST['additional_first_name_' . $id])) {
      update_post_meta($order_id, '_additional_first_name_' . $id, sanitize_text_field($_POST['additional_first_name_' . $id]));
    } else {
      continue;
    }
    if (!empty($_POST['additional_last_name_' . $id])) {
      update_post_meta($order_id, '_additional_last_name_' . $id, sanitize_text_field($_POST['additional_last_name_' . $id]));
    }

    if (!empty($_POST['additional_company_' . $id])) {
      update_post_meta($order_id, '_additional_company_' . $id, sanitize_text_field($_POST['additional_company_' . $id]));
    }

    if (!empty($_POST['additional_email_' . $id])) {
      update_post_meta($order_id, '_additional_email_' . $id, sanitize_text_field($_POST['additional_email_' . $id]));
    }

    if (!empty($_POST['additional_phone_' . $id])) {
      update_post_meta($order_id, '_additional_phone_' . $id, sanitize_text_field($_POST['additional_phone_' . $id]));
    }

    // if (!empty($_POST['additional_gdpr_checkbox_' . $id]) && $_POST['additional_gdpr_checkbox_' . $id] == 1) {
    update_post_meta($order_id, '_additional_gdpr_checkbox_' . $id, sanitize_text_field($_POST['additional_gdpr_checkbox_' . $id]));

    //if (!empty($_POST['additional_gdpr_email_' . $id])) {
    update_post_meta($order_id, '_additional_gdpr_email_' . $id, sanitize_text_field($_POST['additional_gdpr_email_' . $id]));
    //}
    //if (!empty($_POST['additional_gdpr_sms_' . $id])) {
    update_post_meta($order_id, '_additional_gdpr_sms_' . $id, sanitize_text_field($_POST['additional_gdpr_sms_' . $id]));
    //}
    //if (!empty($_POST['additional_gdpr_phone_' . $id])) {
    update_post_meta($order_id, '_additional_gdpr_phone_' . $id, sanitize_text_field($_POST['additional_gdpr_phone_' . $id]));
    //}
    //if (!empty($_POST['additional_gdpr_post_' . $id])) {
    update_post_meta($order_id, '_additional_gdpr_post_' . $id, sanitize_text_field($_POST['additional_gdpr_post_' . $id]));
    //}
    //if (!empty($_POST['additional_gdpr_privacy_' . $id])) {
   // update_post_meta($order_id, '_additional_gdpr_privacy_' . $id, sanitize_text_field($_POST['additional_gdpr_privacy_' . $id]));
    //}
    //if (!empty($_POST['additional_gdpr_terms_' . $id])) {
    update_post_meta($order_id, '_additional_gdpr_terms_' . $id, sanitize_text_field($_POST['additional_gdpr_terms_' . $id]));
    //}
    //}
    // else 
    //   update_post_meta($order_id, '_additional_gdpr_checkbox_' . $id, 0);

  }

  // prijavitelj
  if (!empty($_POST['applicant']) && $_POST['applicant'] == 1) {
    update_post_meta($order_id, '_applicant_first_name', sanitize_text_field($_POST['applicant_first_name']));
    update_post_meta($order_id, '_applicant_last_name', sanitize_text_field($_POST['applicant_last_name']));

    if (!empty($_POST['applicant_company'])) {
      update_post_meta($order_id, '_applicant_company', sanitize_text_field($_POST['applicant_company']));
    }

    if (!empty($_POST['applicant_email'])) {
      update_post_meta($order_id, '_applicant_email', sanitize_text_field($_POST['applicant_email']));
      update_post_meta($order_id, '_billing_email', sanitize_text_field($_POST['applicant_email']));
    }

    if (!empty($_POST['applicant_phone'])) {
      update_post_meta($order_id, '_applicant_phone', sanitize_text_field($_POST['applicant_phone']));
    }

    //if (!empty($_POST['applicant_gdpr_checkbox'] && $_POST['applicant_gdpr_checkbox'] == 1)) {
    update_post_meta($order_id, '_applicant_gdpr_checkbox', sanitize_text_field($_POST['applicant_gdpr_checkbox']));

    //if (!empty($_POST['applicant_gdpr_email'])) {
    update_post_meta($order_id, '_applicant_gdpr_email', sanitize_text_field($_POST['applicant_gdpr_email']));
    //}
    //if (!empty($_POST['applicant_gdpr_sms'])) {
    update_post_meta($order_id, '_applicant_gdpr_sms', sanitize_text_field($_POST['applicant_gdpr_sms']));
    //}
    //if (!empty($_POST['applicant_gdpr_phone'])) {
    update_post_meta($order_id, '_applicant_gdpr_phone', sanitize_text_field($_POST['applicant_gdpr_phone']));
    //}
    //if (!empty($_POST['applicant_gdpr_post'])) {
    update_post_meta($order_id, '_applicant_gdpr_post', sanitize_text_field($_POST['applicant_gdpr_post']));
    //}
    //if (!empty($_POST['applicant_gdpr_privacy'])) {
   // update_post_meta($order_id, '_applicant_gdpr_privacy', sanitize_text_field($_POST['applicant_gdpr_privacy']));
    //}
    //if (!empty($_POST['applicant_gdpr_terms'])) {
    update_post_meta($order_id, '_applicant_gdpr_terms', sanitize_text_field($_POST['applicant_gdpr_terms']));
    // }
    // }
    // else
    //   update_post_meta($order_id, '_applicant_gdpr_checkbox', 0);
  }


  if (!empty($_POST['order_number'])) {
    update_post_meta($order_id, '_order_number', sanitize_text_field($_POST['order_number']));
  }
}


/*
* Order template in admin
*/
add_action('woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1);

function my_custom_checkout_field_display_admin_order_meta($order)
{
  echo "<style>
        .order_data_column:nth-child(n+2) > *:not(.ow-custom-admin-order-data){
            display:none;
        }
        #order_data .order_data_column{
        width:48%;
        }
        </style>";

  echo '<div class="ow-custom-admin-order-data" style="padding-left:30px; float:left; width:100%; display:block;">';

  if (get_post_meta($order->get_id(), '_applicant_first_name', true)) {
    echo '<h3>Prijavitelj</h3>';
    echo '<p><strong>' . __('Ime') . ':</strong> ' . get_post_meta($order->get_id(), '_applicant_first_name', true) . '</p>';
    echo '<p><strong>' . __('Priimek') . ':</strong> ' . get_post_meta($order->get_id(), '_applicant_last_name', true) . '</p>';
    echo '<p><strong>' . __('Delovno mesto') . ':</strong> ' . get_post_meta($order->get_id(), '_applicant_company', true) . '</p>';
    echo '<p><strong>' . __('E-pošta') . ':</strong> ' . get_post_meta($order->get_id(), '_applicant_email', true) . '</p>';
    echo '<p><strong>' . __('Telefon') . ':</strong> ' . get_post_meta($order->get_id(), '_applicant_phone', true) . '</p>';

    if (get_post_meta($order->get_id(), '_applicant_gdpr_checkbox', true)) {
      echo '<p><strong>' . __('Soglasje novice') . ':</strong> DA</p>';
      if (get_post_meta($order->get_id(), '_applicant_gdpr_email', true)) {
        echo '<p><strong>' . __('E-pošta') . ':</strong> DA</p>';
      } else {
        echo '<p><strong>' . __('E-pošta') . ':</strong> NE</p>';
      }
      if (get_post_meta($order->get_id(), '_applicant_gdpr_sms', true)) {
        echo '<p><strong>' . __('SMS') . ':</strong> DA</p>';
      } else {
        echo '<p><strong>' . __('SMS') . ':</strong> NE</p>';
      }
      if (get_post_meta($order->get_id(), '_applicant_gdpr_phone', true)) {
        echo '<p><strong>' . __('Telefon') . ':</strong> DA</p>';
      } else {
        echo '<p><strong>' . __('Telefon') . ':</strong> NE</p>';
      }
      if (get_post_meta($order->get_id(), '_applicant_gdpr_post', true)) {
        echo '<p><strong>' . __('Pošta') . ':</strong> DA</p>';
      } else {
        echo '<p><strong>' . __('Pošta') . ':</strong> NE</p>';
      }
    } else {
        echo '<p><strong>' . __('Soglasje novice') . ':</strong> NE</p>';
    }
    if (get_post_meta($order->get_id(), '_applicant_gdpr_terms', true)) {
    echo '<p><strong>' . __('Soglasje splošni pogoji') . ':</strong> DA</p>';
  } else {
    echo '<p><strong>' . __('Soglasje splošni pogoji') . ':</strong> NE</p>';
  }
  } 
  /*if (get_post_meta($order->get_id(), '_applicant_gdpr_privacy', true)) {
    echo '<p><strong>' . __('Soglasje obdelava podatkov') . ':</strong> DA</p>';
  } else {
    echo '<p><strong>' . __('Soglasje obdelava podatkov') . ':</strong> NE</p>';
  }*/

  
  echo '<hr>';


  /* dodatne osebe*/
  echo '<h3>Udeleženci</h3>';

  //print_r(get_post_meta($order->get_id()));

  //$i = 1;
  /*foreach (get_post_meta($order->get_id()) as $data_key => $data_value) {

        if (strpos($data_key, '_additional_full_name_') > -1) {
            $i++;
            echo '<h4>Dodatna oseba ' . $i . ':</h4>';
            echo '<p><strong>' . __('Ime in priimek') . ':</strong> ' . get_post_meta($order->get_id(), $data_key, true) . '</p>';
        }

        if (strpos($data_key, '_additional_company_') > -1) {
            echo '<p><strong>' . __('Delovno mesto') . ':</strong> ' . get_post_meta($order->get_id(), $data_key, true) . '</p>';
        }

        if (strpos($data_key, '_additional_email_') > -1) {
            echo '<p><strong>' . __('E-pošta') . ':</strong> ' . get_post_meta($order->get_id(), $data_key, true) . '</p>';
        }

        if (strpos($data_key, '_additional_phone_') > -1) {
            echo '<p><strong>' . __('Telefonska številka') . ':</strong> ' . get_post_meta($order->get_id(), $data_key, true) . '</p>';
        }
    }*/

  for ($i = 1; $i < 99; $i++) {
    if (get_post_meta($order->get_id(), '_additional_first_name_' . $i, true)) {
      echo '<h3>Udeleženec ' . $i . ':</h3>';
      echo '<p><strong>' . __('Ime') . ':</strong> ' . get_post_meta($order->get_id(), '_additional_first_name_' . $i, true) . '</p>';
      echo '<p><strong>' . __('Priimek') . ':</strong> ' . get_post_meta($order->get_id(), '_additional_last_name_' . $i, true) . '</p>';
      echo '<p><strong>' . __('E-pošta') . ':</strong> ' . get_post_meta($order->get_id(), '_additional_email_' . $i, true) . '</p>';
      echo '<p><strong>' . __('Telefon') . ':</strong> ' . get_post_meta($order->get_id(), '_additional_phone_' . $i, true) . '</p>';
      echo '<p><strong>' . __('Delovno mesto') . ':</strong> ' . get_post_meta($order->get_id(), '_additional_company_' . $i, true) . '</p>';

      if (get_post_meta($order->get_id(), '_additional_gdpr_checkbox_' . $i, true)) {
        echo '<p><strong>' . __('Soglasje novice') . ':</strong> DA</p>';
        if (get_post_meta($order->get_id(), '_additional_gdpr_email_' . $i, true)) {
          echo '<p><strong>' . __('E-pošta') . ':</strong> DA</p>';
        } else {
          echo '<p><strong>' . __('E-pošta') . ':</strong> NE</p>';
        }
        if (get_post_meta($order->get_id(), '_additional_gdpr_sms_' . $i, true)) {
          echo '<p><strong>' . __('SMS') . ':</strong> DA</p>';
        } else {
          echo '<p><strong>' . __('SMS') . ':</strong> NE</p>';
        }
        if (get_post_meta($order->get_id(), '_additional_gdpr_phone_' . $i, true)) {
          echo '<p><strong>' . __('Telefon') . ':</strong> DA</p>';
        } else {
          echo '<p><strong>' . __('Telefon') . ':</strong> NE</p>';
        }
        if (get_post_meta($order->get_id(), '_additional_gdpr_post_' . $i, true)) {
          echo '<p><strong>' . __('Pošta') . ':</strong> DA</p>';
        } else {
          echo '<p><strong>' . __('Pošta') . ':</strong> NE</p>';
        }
      } else {
        echo '<p><strong>' . __('Soglasje novice') . ':</strong> NE</p>';
      }
      /*if (get_post_meta($order->get_id(), '_additional_gdpr_privacy_' . $i, true)) {
        echo '<p><strong>' . __('Soglasje obdelava podatkov') . ':</strong> DA</p>';
      } else {
        echo '<p><strong>' . __('Soglasje obdelava podatkov') . ':</strong> NE</p>';
      }*/

      if (get_post_meta($order->get_id(), '_additional_gdpr_terms_' . $i, true)) {
        echo '<p><strong>' . __('Soglasje splošni pogoji') . ':</strong> DA</p>';
      } else {
        echo '<p><strong>' . __('Soglasje splošni pogoji') . ':</strong> NE</p>';
      }
    } else {
      continue;
    }
  }

  echo '<hr>';

  /* naslov za plačilo*/
  echo '<h3>Naslov za plačilo</h3>';
  if (get_post_meta($order->get_id(), '_billing_on_company', true) == true) {
    $payment_on_company = "DA";
  } else {
    $payment_on_company = "NE";
  }

  echo '<p><strong>' . __('Plačilo na podjetje') . ':</strong> ' . $payment_on_company . '</p>';
  if ($payment_on_company == "DA") {
    echo '<p><strong>' . __('Davčna številka') . ':</strong> ' . get_post_meta($order->get_id(), '_billing_vat', true) . '</p>';
    echo '<p><strong>' . __('Podjetje') . ':</strong> ' . get_post_meta($order->get_id(), '_billing_company_address', true) . '</p>';
  }
  echo '<p><strong>' . __('Naslov') . ':</strong> ' . get_post_meta($order->get_id(), '_billing_address_1', true) . '</p>';
  echo '<p><strong>' . __('Poštna št.') . ':</strong> ' . get_post_meta($order->get_id(), '_billing_postcode', true) . '</p>';
  echo '<p><strong>' . __('Mesto') . ':</strong> ' . get_post_meta($order->get_id(), '_billing_city', true) . '</p>';
  echo '<p><strong>' . __('Država') . ':</strong> ' . get_post_meta($order->get_id(), '_billing_country', true) . '</p>';

  echo '<hr>';

  /* opombe */
  echo '<h3>Opombe</h3>';
  echo '<p><strong>' . __('Opombe') . ':</strong> ' . $order->get_customer_note() . '</p>';
  echo '<hr>';


  /* plačila */
  echo '<h3>Način plačila</h3>';
  echo '<p><strong>' . __('Način plačila') . ':</strong> ' . get_post_meta($order->get_id(), '_payment_method_title', true) . '</p>';

  //echo '<hr>';

  /* soglasja */
  /*echo '<h3>Soglasje za prejemanje novic</h3>';
    if (get_post_meta($order->get_id(), '_privacy-policy-checkbox', true) == true) {
        $agreement = "DA";
    } else {
        $agreement = "NE";
    }*/

  //echo '<p><strong>' . __('Soglasje') . ':</strong> ' . $agreement . '</p>';


  echo '<hr><p><strong>' . __('Številka naročilnice') . ':</strong> ' . get_post_meta($order->get_id(), '_order_number', true) . '</p>';
  echo '<p><strong>' . __('Dogodek ID') . ':</strong> ' . get_post_meta($order->get_id(), '_kotizacija_id', true) . '</p>';
  echo '</div>';
}


add_action('woocommerce_after_checkout_billing_form', 'action_woocommerce_after_checkout_billing_form', 10, 1);

function action_woocommerce_after_checkout_billing_form($checkout)
{

    echo '<div id="ow-checkout-billing-address">';
   // echo '<h4>Naslov za plačilo</h4>';

    $fields = $checkout->get_checkout_fields('billing2');

    foreach ($fields as $key => $field) {
        if (isset($field['country_field'], $fields[$field['country_field']])) {
            $field['country'] = $checkout->get_value($field['country_field']);
        }
        woocommerce_form_field($key, $field, $checkout->get_value($key));
    }

    echo '</div>';
    
    foreach (WC()->cart->get_cart() as $cart_item) {
    $kotizacija_id = get_field('kotizacija_id', $cart_item['product_id']);
    $ref_id = $cart_item['lisica_sku'];
  }
  echo '<input type="hidden" name="kotizacija_id" id="kotizacija_id" value="' . $kotizacija_id . '">';
  echo '<input type="hidden" name="ref_id" id="ref_id" value="' . $ref_id . '">';

}


/* Customize default billing fields */
add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');
function custom_override_checkout_fields($fields)
{

   /* billing fields in first part */
  /*$fields['billing']['billing_full_name'] = Array(
        'label' => __('Full name', 'woocommerce'),
        'required' => 1,
        'class' => Array('form-row-wide'),
        'priority' => 10,
        'autocomplete' => 'given-name' . ' ' . 'family-name',
        'placeholder' => ''
    );*/

  $fields['billing']['billing_on_company'] = array(
    'label' => '<span class="ow-checkbox-label">' . __('Plačilo sklepam na podjetje', 'pgv') . '</span>',
    'class' => array('form-row-wide ow-conditional-required-checkbox'),
    'priority' => 1,
    'type' => 'checkbox'
  );

  $fields['billing']['billing_vat'] = array(
    'label' => __('Davčna številka', 'pgv'),
    'class' => array('form-row-wide address-field ow-conditional-required ow-conditional-required-vat'),
    'priority' => 1.1,
    'placeholder' => ''
  );

  $fields['billing']['billing_company_address'] = array(
    'label' => __('Naziv podjetja', 'pgv'),
    'class' => array('form-row-wide ow-conditional-required ow-conditional-required-company'),
    'priority' => 1.2,
    'autocomplete' => 'organization',
    'placeholder' => ''
  );

  /*$fields['billing']['billing_company']['label'] = __('Job', 'woocommerce');
    $fields['billing']['billing_company']['priority'] = 20;
    $fields['billing']['billing_company']['placeholder'] = '';*/

  /* $fields['billing']['billing_email']['label'] = __('E-mail', 'woocommerce');
    $fields['billing']['billing_email']['priority'] = 30;
    $fields['billing']['billing_email']['placeholder'] = '';*/
  unset($fields['billing']['billing_email']);

  /* $fields['billing']['billing_phone']['label'] = __('Phone', 'woocommerce');
    $fields['billing']['billing_phone']['priority'] = 40;
    $fields['billing']['billing_phone']['placeholder'] = '';
    $fields['billing']['billing_phone']['class'] = array('ow-info');
    $fields['billing']['billing_phone']['description'] = get_default_field('checkout_info_phone_text', 'options');*/
  unset($fields['billing']['billing_phone']);

  $country = $fields['billing']['billing_country'];

  unset($fields['billing']['billing_company']);
  unset($fields['billing']['billing_first_name']);
  unset($fields['billing']['billing_last_name']);
  unset($fields['billing']['billing_country']);
  unset($fields['billing']['billing_address_1']);
  unset($fields['billing']['billing_address_2']);
  unset($fields['billing']['billing_postcode']);
  unset($fields['billing']['billing_city']);
  unset($fields['billing']['billing_state']);


  /* billing fields in second part */
  $fields['billing2']['billing_address_1'] = array(
    'label' => 'Ulica in hišna številka',
    'required' => 1,
    'class' => array('form-row-wide address-field'),
    'priority' => 10,
    'autocomplete' => 'address-line1',
    //'placeholder' => 'test'
  );

  $fields['billing2']['billing_postcode'] = array(
    'label' => __('ZIP code', 'woocommerce'),
    'required' => 1,
    'class' => array('form-row-first address-field'),
    'priority' => 20,
    'autocomplete' => 'postal-code',
    'placeholder' => ''
  );

  $fields['billing2']['billing_city'] = array(
    'label' => __('City', 'woocommerce'),
    'required' => 1,
    'class' => array('form-row-last address-field'),
    'priority' => 30,
    'autocomplete' => 'address-level2',
    'placeholder' => ''
  );

  $fields['billing2']['billing_country'] = $country;
  $fields['billing2']['billing_country']['priority'] = 40;
  $fields['billing2']['billing_country']['label'] = '';
  $fields['billing2']['billing_country']['placeholder'] = '';

  $fields['billing2']['order_number'] = array(
    'label' => 'Številka naročilnice',
    'required' => 0,
    'class' => array('form-row-wide'),
    'priority' => 50,
    'placeholder' => ''
  );


  $fields['order']['order_comments']['label'] = __('Opombe', 'woocommerce');
  $fields['order']['order_comments']['placeholder'] = '';

  return $fields;
}


if (!function_exists('woocommerce_form_field')) {

    /**
     * Outputs a checkout/address form field.
     *
     * @param string $key Key.
     * @param mixed $args Arguments.
     * @param string $value (default: null).
     * @return string
     */
    function woocommerce_form_field($key, $args, $value = null)
    {
        $defaults = array(
            'type' => 'text',
            'label' => '',
            'description' => '',
            'placeholder' => '',
            'maxlength' => false,
            'required' => false,
            'autocomplete' => false,
            'id' => $key,
            'class' => array(),
            'label_class' => array(),
            'input_class' => array(),
            'return' => false,
            'options' => array(),
            'custom_attributes' => array(),
            'validate' => array(),
            'default' => '',
            'autofocus' => '',
            'priority' => '',
        );

        $args = wp_parse_args($args, $defaults);
        $args = apply_filters('woocommerce_form_field_args', $args, $key, $value);

        if ($args['required']) {
            $args['class'][] = 'validate-required';
        } else {
        }

        if (is_string($args['label_class'])) {
            $args['label_class'] = array($args['label_class']);
        }

        if (is_null($value)) {
            $value = $args['default'];
        }

        // Custom attribute handling.
        $custom_attributes = array();
        $args['custom_attributes'] = array_filter((array)$args['custom_attributes'], 'strlen');

        if ($args['maxlength']) {
            $args['custom_attributes']['maxlength'] = absint($args['maxlength']);
        }

        if (!empty($args['autocomplete'])) {
            $args['custom_attributes']['autocomplete'] = $args['autocomplete'];
        }

        if (true === $args['autofocus']) {
            $args['custom_attributes']['autofocus'] = 'autofocus';
        }

        if ($args['description']) {
            $args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
        }

        if (!empty($args['custom_attributes']) && is_array($args['custom_attributes'])) {
            foreach ($args['custom_attributes'] as $attribute => $attribute_value) {
                $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
            }
        }

        if (!empty($args['validate'])) {
            foreach ($args['validate'] as $validate) {
                $args['class'][] = 'validate-' . $validate;
            }
        }

        $field = '';
        $label_id = $args['id'];
        $sort = $args['priority'] ? $args['priority'] : '';
        $field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr($sort) . '">%3$s</p>';

        switch ($args['type']) {
            case 'country':
                $countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

                if (1 === count($countries)) {

                    $field .= '<strong>' . current(array_values($countries)) . '</strong>';

                    $field .= '<input type="hidden" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" value="' . current(array_keys($countries)) . '" ' . implode(' ', $custom_attributes) . ' class="country_to_state" readonly="readonly" />';

                } else {

                    $field = '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" class="country_to_state country_select ' . esc_attr(implode(' ', $args['input_class'])) . '" ' . implode(' ', $custom_attributes) . '><option value="">' . esc_html__('Select a country&hellip;', 'woocommerce') . '</option>';

                    foreach ($countries as $ckey => $cvalue) {
                        $field .= '<option value="' . esc_attr($ckey) . '" ' . selected($value, $ckey, false) . '>' . $cvalue . '</option>';
                    }

                    $field .= '</select>';

                    $field .= '<noscript><button type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__('Update country', 'woocommerce') . '">' . esc_html__('Update country', 'woocommerce') . '</button></noscript>';

                }

                break;
            case 'state':
                /* Get country this state field is representing */
                $for_country = isset($args['country']) ? $args['country'] : WC()->checkout->get_value('billing_state' === $key ? 'billing_country' : 'shipping_country');
                $states = WC()->countries->get_states($for_country);

                if (is_array($states) && empty($states)) {

                    $field_container = '<p class="form-row %1$s" id="%2$s" style="display: none">%3$s</p>';

                    $field .= '<input type="hidden" class="hidden" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" value="" ' . implode(' ', $custom_attributes) . ' placeholder="' . esc_attr($args['placeholder']) . '" readonly="readonly" />';

                } elseif (!is_null($for_country) && is_array($states)) {

                    $field .= '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" class="state_select ' . esc_attr(implode(' ', $args['input_class'])) . '" ' . implode(' ', $custom_attributes) . ' data-placeholder="' . esc_attr($args['placeholder']) . '">
						<option value="">' . esc_html__('Select an option&hellip;', 'woocommerce') . '</option>';

                    foreach ($states as $ckey => $cvalue) {
                        $field .= '<option value="' . esc_attr($ckey) . '" ' . selected($value, $ckey, false) . '>' . $cvalue . '</option>';
                    }

                    $field .= '</select>';

                } else {

                    $field .= '<input type="text" class="input-text ' . esc_attr(implode(' ', $args['input_class'])) . '" value="' . esc_attr($value) . '"  placeholder="' . esc_attr($args['placeholder']) . '" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" ' . implode(' ', $custom_attributes) . ' />';

                }

                break;
            case 'textarea':
                $field .= '<textarea name="' . esc_attr($key) . '" class="input-text ' . esc_attr(implode(' ', $args['input_class'])) . '" id="' . esc_attr($args['id']) . '" placeholder="' . esc_attr($args['placeholder']) . '" ' . (empty($args['custom_attributes']['rows']) ? ' rows="2"' : '') . (empty($args['custom_attributes']['cols']) ? ' cols="5"' : '') . implode(' ', $custom_attributes) . '>' . esc_textarea($value) . '</textarea>';

                break;
            case 'checkbox':
                $field = '<label class="checkbox ' . implode(' ', $args['label_class']) . '" ' . implode(' ', $custom_attributes) . '>
						<input type="' . esc_attr($args['type']) . '" class="input-checkbox ' . esc_attr(implode(' ', $args['input_class'])) . '" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" value="1" ' . checked($value, 1, false) . ' /> ' . $args['label'] . '</label>';

                break;
            case 'text':
            case 'password':
            case 'datetime':
            case 'datetime-local':
            case 'date':
            case 'month':
            case 'time':
            case 'week':
            case 'number':
            case 'email':
            case 'url':
            case 'tel':
                $field .= '<input type="' . esc_attr($args['type']) . '" class="input-text ' . esc_attr(implode(' ', $args['input_class'])) . '" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" placeholder="' . esc_attr($args['placeholder']) . '"  value="' . esc_attr($value) . '" ' . implode(' ', $custom_attributes) . ' />';

                break;
            case 'select':
                $field = '';
                $options = '';

                if (!empty($args['options'])) {
                    foreach ($args['options'] as $option_key => $option_text) {
                        if ('' === $option_key) {
                            // If we have a blank option, select2 needs a placeholder.
                            if (empty($args['placeholder'])) {
                                $args['placeholder'] = $option_text ? $option_text : __('Choose an option', 'woocommerce');
                            }
                            $custom_attributes[] = 'data-allow_clear="true"';
                        }
                        $options .= '<option value="' . esc_attr($option_key) . '" ' . selected($value, $option_key, false) . '>' . esc_attr($option_text) . '</option>';
                    }

                    $field .= '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" class="select ' . esc_attr(implode(' ', $args['input_class'])) . '" ' . implode(' ', $custom_attributes) . ' data-placeholder="' . esc_attr($args['placeholder']) . '">
							' . $options . '
						</select>';
                }

                break;
            case 'radio':
                $label_id .= '_' . current(array_keys($args['options']));

                if (!empty($args['options'])) {
                    foreach ($args['options'] as $option_key => $option_text) {
                        $field .= '<input type="radio" class="input-radio ' . esc_attr(implode(' ', $args['input_class'])) . '" value="' . esc_attr($option_key) . '" name="' . esc_attr($key) . '" ' . implode(' ', $custom_attributes) . ' id="' . esc_attr($args['id']) . '_' . esc_attr($option_key) . '"' . checked($value, $option_key, false) . ' />';
                        $field .= '<label for="' . esc_attr($args['id']) . '_' . esc_attr($option_key) . '" class="radio ' . implode(' ', $args['label_class']) . '">' . $option_text . '</label>';
                    }
                }

                break;
        }

        if (!empty($field)) {
            $field_html = '';

            if ($args['label'] && 'checkbox' !== $args['type']) {
                $field_html .= '<label for="' . esc_attr($label_id) . '" class="' . esc_attr(implode(' ', $args['label_class'])) . '">' . $args['label'] . '</label>';
            }

            $field_html .= '<span class="woocommerce-input-wrapper">' . $field;

            if ($args['description']) {
                $field_html .= '<span class="description" id="' . esc_attr($args['id']) . '-description" aria-hidden="true">' . wp_kses_post($args['description']) . '</span>';
            }

            $field_html .= '</span>';

            $container_class = esc_attr(implode(' ', $args['class']));
            $container_id = esc_attr($args['id']) . '_field';
            $field = sprintf($field_container, $container_class, $container_id, $field_html);
        }

        /**
         * Filter by type.
         */
        $field = apply_filters('woocommerce_form_field_' . $args['type'], $field, $key, $args, $value);

        /**
         * General filter on form fields.
         *
         * @since 3.4.0
         */
        $field = apply_filters('woocommerce_form_field', $field, $key, $args, $value);

        if ($args['return']) {
            return $field;
        } else {
            echo $field; // WPCS: XSS ok.
        }
    }
}


// modify the address formats
add_filter('woocommerce_localisation_address_formats', function ($formats) {
    foreach ($formats as $key => &$format) {
        $format = "\n{full_name}" . $format;
    }
    return $formats;
});

// add the replacement value
add_filter('woocommerce_formatted_address_replacements', function ($replacements, $args) {
    $replacements['{full_name}'] = $args['full_name'];
    return $replacements;
}, 10, 2);


add_filter('woocommerce_order_formatted_billing_address', 'woo_custom_order_formatted_billing_address', 10, 2);

function woo_custom_order_formatted_billing_address($address, $WC_Order)
{

    $address = array(
        'full_name' => $WC_Order->billing_full_name,
        'vat' => $WC_Order->billing_vat,
        'company' => $WC_Order->billing_company,
        'address_1' => $WC_Order->billing_address_1,
        'address_2' => $WC_Order->billing_address_2,
        'city' => $WC_Order->billing_city,
        'state' => $WC_Order->billing_state,
        'postcode' => $WC_Order->billing_postcode,
        'country' => $WC_Order->billing_country
    );

    return $address;
}

add_filter('woocommerce_localisation_address_formats', 'woocommerce_custom_address_format', 20);


function woocommerce_custom_address_format($formats)
{
    $formats['SI'] = '{company} {full_name} {address_1} {address_2} {postcode} {city} {country}';
    return $formats;
}



// define the woocommerce_coupon_error callback
function filter_woocommerce_coupon_error( $err, $err_code, $instance ) {
    wc_add_notice($err, 'notice');
    return $err;
};

// add the filter
add_filter( 'woocommerce_coupon_error', 'filter_woocommerce_coupon_error', 10, 3 );



// define the woocommerce_coupon_is_valid callback
function filter_woocommerce_coupon_is_valid( $true, $instance ) {
    // make filter magic happen here...
    return $true;
};

// add the filter
add_filter( 'woocommerce_coupon_is_valid', 'filter_woocommerce_coupon_is_valid', 10, 2 );




/* ORDERS SUBSCRIPTION TO NEWSLETTER */
// Custom column on order list
add_filter( 'manage_edit-shop_order_columns', 'ow_shop_order_column', 20 );
function ow_shop_order_column($columns)
{
    $reordered_columns = array();

    // Inserting columns to a specific location
    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;
        if( $key ==  'order_status' ){
            $reordered_columns['privacy-policy-checkbox'] = __( 'Soglasje za e-novice','theme_domain');
        }
    }
    return $reordered_columns;
}

// Adding custom fields meta data for each new column
add_action( 'manage_shop_order_posts_custom_column' , 'ow_orders_list_column_content', 20, 2 );
function ow_orders_list_column_content( $column, $post_id )
{
    switch ( $column )
    {
        case 'privacy-policy-checkbox' :
            // Get custom post meta data
            $agreement = get_post_meta($post_id, '_privacy-policy-checkbox', true);
            if(!empty($agreement)) {
                echo "DA";
            }
            break;
    }
}

// Allow to sort orders by privacy-policy-checkbox
add_filter( 'manage_edit-shop_order_sortable_columns', 'ow_sortable_shop_column' );
function ow_sortable_shop_column( $columns ) {
    $columns['privacy-policy-checkbox'] = 'privacy-policy-checkbox';

    return $columns;
}

//Add link to start export script export_subscribers.php
add_filter('views_edit-shop_order','ow_export_subscribers');
function ow_export_subscribers($views){
    $views['export'] = '<a href="'.get_theme_root_uri().'/enfold-child/export_subscribers.php" class="primary" style="color:green;">Export subscribers</a>';
    return $views;
}



/*
* add LISICA SKU field to product & product variations
*/

//add LISICA SKU to product
add_action( 'woocommerce_product_options_sku', 'ow_add_lisica_sku_to_product' );
function ow_add_lisica_sku_to_product() {
    $args = array(
        'label' => __( 'ŠIFRA LISICA', 'woocommerce' ),
        'id' => 'lisica_sku',
    );
    woocommerce_wp_text_input( $args );
}

//save LISICA SKU to product
add_action( 'woocommerce_process_product_meta', 'ow_save_lisica_sku_to_product' );
function ow_save_lisica_sku_to_product( $post_id ) {
    $custom_sku = isset( $_POST[ 'lisica_sku' ] ) ? sanitize_text_field( $_POST[ 'lisica_sku' ] ) : '';
    $product = wc_get_product( $post_id );
    $product->update_meta_data( 'lisica_sku', $custom_sku );
    $product->save();
}

//add LISICA SKU to variations
add_action( 'woocommerce_product_after_variable_attributes', 'ow_add_lisica_sku_to_product_variations', 10, 3 );
function ow_add_lisica_sku_to_product_variations( $loop, $variation_data, $variation ) {
    echo '<div class="variation-custom-fields" style="clear:both;">';
    woocommerce_wp_text_input(
        array(
            'id'            => '_lisica_sku['. $loop .']',
            'label'         => __('ŠIFRA LISICA', 'woocommerce' ),
            'value'         => get_post_meta($variation->ID, '_lisica_sku', true),
        )
    );
    echo "</div>";
}

//save LISICA SKU to variations
add_action( 'woocommerce_save_product_variation', 'save_lisica_variation_fields', 10, 2 );
function save_lisica_variation_fields( $variation_id, $i) {
    $input = $_POST['_lisica_sku'][$i];
    update_post_meta( $variation_id, '_lisica_sku', $input );

    //automatically create variation SKU if empty
    if( empty( get_post_meta( $variation_id, '_sku', true ) ) ) {
        $auto_sku = generate_unique_sku();
        update_post_meta( $variation_id, '_sku', $auto_sku );
    }
}

//automatically create product SKU if empty
function ow_save_post( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    $post_type = get_post_type($post_id);
    if( $post_type == 'product' ) {
        if( empty( get_post_meta( $post_id, '_sku', true ) ) ) {
            $auto_sku = generate_unique_sku();
            update_post_meta( $post_id, '_sku', $auto_sku );
        }
    }
}
add_action( 'save_post', 'ow_save_post' );

//SKU generator
function generate_unique_sku(){
    global $wpdb;
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 6; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $randomString ) );

    if ( $product_id ) {
        return generate_unique_sku();
    } else {
        return $randomString;
    }
}

//add LISICA SKU to cart item
add_filter( 'woocommerce_add_cart_item_data', 'ow_add_lisica_sku_to_cart_item', 10, 3 );
function ow_add_lisica_sku_to_cart_item( $cart_item_data, $product_id, $variation_id ) {
    $lisica_sku = get_post_meta( $product_id, 'lisica_sku', true );
    if (isset($variation_id)) {
        $lisica_sku = get_post_meta($variation_id, '_lisica_sku', true);
    }

    if ( empty( $lisica_sku ) ) {
        return $cart_item_data;
    }

    $cart_item_data['lisica_sku'] = $lisica_sku;

    return $cart_item_data;
}

//save LISICA SKU to order
add_action( 'woocommerce_checkout_create_order_line_item', 'ow_add_lisica_sku_to_order_items', 10, 4 );
function ow_add_lisica_sku_to_order_items( $item, $cart_item_key, $values, $order ) {
    if ( empty( $values['lisica_sku'] ) ) {
        return;
    }

    $item->add_meta_data('lisica_sku', $values['lisica_sku'] );
}



//Stripe input fields custom syling
add_filter( 'wc_stripe_elements_styling', 'ow_modify_stripe_fields_styles' );
function ow_modify_stripe_fields_styles( $styles ) {
    return array(
        'base' => array(
            'iconColor'     => '#4E4B4C',
            'color'         => '#4E4B4C',
            'lineHeight'    => '45px',
            'fontWeight'    => '300',
            'fontSize'      => '16px',
            'fontFamily'    => '"Montserrat", sans-serif',
            '::placeholder' => array(
                'color'         => '#8d8b8b',
                'fontFamily'    => '"Montserrat", sans-serif',
                'fontWeight'    => '300',
                'fontSize'      => '16px',
            ),
        ),
        'invalid' => array(
            'color'     => '#b90066'
        )
    );
}

// Customize Stripe options
add_filter("wc_stripe_elements_options", "ow_custom_stripe_options");
function ow_custom_stripe_options($options) {
    $options["fonts"] = array(
        array(
            "family" => "Montserrat",
            "src" => "url(".get_stylesheet_directory_uri()."/fonts/Montserrat-Light.woff) format(woff),
                url('".get_stylesheet_directory_uri()."/fonts/Montserrat-Light.eot?#iefix') format('embedded-opentype'), 
                url('".get_stylesheet_directory_uri()."/fonts/Montserrat-Light.woff2') format('woff2'), 
                url('".get_stylesheet_directory_uri()."/fonts/Montserrat-Light.ttf')  format('truetype'), 
                url('".get_stylesheet_directory_uri()."/fonts/Montserrat-Light.svg#svgFontName') format('svg');",
            "fontWeight"    => "300",
        )
    );
    return $options;
}

// Payment method discounts
//add_action('woocommerce_cart_calculate_fees', 'pgv_add_discount', 20, 1);
function pgv_add_discount($cart_object)
{
  if (is_admin() && !defined('DOING_AJAX')) return;

  $cart_total = $cart_object->subtotal_ex_tax;

  $chosen_payment_method = WC()->session->get('chosen_payment_method');  //Get the selected payment method
  if ('bacs' == $chosen_payment_method) {

    $label_text = __("Popust - predračun");

    // Calculating percentage
    $discount = number_format(($cart_total / 100) * 2, 2);

    // Adding the discount
    $cart_object->add_fee($label_text, -$discount, false);
  }
  if ('stripe' == $chosen_payment_method) {

    $label_text = __("Popust - kreditna kartica");

    // Calculating percentage
    $discount = number_format(($cart_total / 100) * 2, 2);

    // Adding the discount
    $cart_object->add_fee($label_text, -$discount, false);
  }
}
//add_action('woocommerce_review_order_before_payment', 'pgv_refresh_payment_method');
function pgv_refresh_payment_method()
{
  // jQuery
?>
  <script type="text/javascript">
    (function($) {
      $('form.checkout').on('change', 'input[name^="payment_method"]', function() {
        $('body').trigger('update_checkout');
      });
    })(jQuery);
  </script>
<?php
}
//add_action('woocommerce_review_order_before_payment', 'pgv_discount_info');
function pgv_discount_info()
{
  echo '<p style="font-size: 15px;">Pri plačilu z <strong>kreditno kartico</strong> ali po <strong>predračunu</strong> imate <strong>2%</strong> popusta!</p>';
}

function ac_iframes($order_id)
{
  $order = wc_get_order($order_id);

  $item = reset($order->get_items());
  $product = $item->get_product();
  switch_to_blog(1); // to Planet GV
  $event = wc_get_products([
    'status' => ['draft', 'publish'],
    'limit' => 1,
    'meta_key'      => 'kotizacija_id',
    'meta_value'    => get_post_meta($product->get_id(), 'kotizacija_id', true),
  ])[0];

  $event_date = get_field('ow_event_start_date', $event->get_id());
  $event_time = get_post_meta($event->get_id(), 'ow_event_start_time', true);

  $event_loc_name = get_post_meta($event->get_id(), 'ow_venue_name', true);
  $event_city = get_post_meta($event->get_id(), 'ow_venue_city', true);
  $event_street = get_post_meta($event->get_id(), 'ow_venue_street', true);
  $event_post = get_post_meta($event->get_id(), 'ow_venue_postcode', true);
  $event_country = get_post_meta($event->get_id(), 'ow_venue_country', true);
  $address = [$event_street, $event_city, $event_post, $event_country];
  $event_address = implode(', ', array_filter($address));

  wp_reset_query();
  restore_current_blog(); // back to Congress

  // payment method
  $pm_code = $order->get_payment_method();
  $pms = [
    'bacs' => 'Plačilo po predračunu',
    'cheque' => 'Plačilo po opravljeni storitvi (proračunski uporabniki)',
    'stripe' => 'Plačilo s kreditno kartico'
  ];

  // udeleženci
  $params = [];
  for ($id = 1; $id < 999; $id++) {
    if (get_post_meta($order_id, '_additional_first_name_' . $id, true)) {
      $gdpr = get_post_meta($order_id, '_additional_gdpr_checkbox_' . $id, true) ? 'Da' : 'Ne';
      $gdpr_email = get_post_meta($order_id, '_additional_gdpr_email_' . $id, true) ? 'Da' : 'Ne';
      $gdpr_sms = get_post_meta($order_id, '_additional_gdpr_sms_' . $id, true) ? 'Da' : 'Ne';
      $gdpr_phone = get_post_meta($order_id, '_additional_gdpr_phone_' . $id, true) ? 'Da' : 'Ne';
      $gdpr_post = get_post_meta($order_id, '_additional_gdpr_post_' . $id, true) ? 'Da' : 'Ne';
      //$gdpr_privacy = get_post_meta($order_id, '_additional_gdpr_privacy_' . $id, true) ? 'Da' : 'Ne';
      $gdpr_terms = get_post_meta($order_id, '_additional_gdpr_terms_' . $id, true) ? 'Da' : 'Ne';

      $params[] = [
        'firstname' => get_post_meta($order_id, '_additional_first_name_' . $id, true),
        'lastname' => get_post_meta($order_id, '_additional_last_name_' . $id, true),
        'field[1]' => get_post_meta($order_id, '_additional_company_' . $id, true),
        'email' => get_post_meta($order_id, '_additional_email_' . $id, true),
        'phone' => get_post_meta($order_id, '_additional_phone_' . $id, true),
        'field[9]' => 'Udeleženec',
        'field[8]' => $gdpr,
        'field[3]' => $gdpr_email,
        'field[5]' => $gdpr_sms,
        'field[4]' => $gdpr_phone,
        'field[6]' => $gdpr_post,
        'field[18]' => $gdpr_privacy,
        'field[7]' => $gdpr_terms,
        'customer_account' => get_post_meta($order_id, '_billing_company_address', true),
        'ca[2][v]' => get_post_meta($order_id, '_billing_address_1', true),
        'ca[6][v]' => get_post_meta($order_id, '_billing_postcode', true),
        'ca[4][v]' => get_post_meta($order_id, '_billing_city', true),
        'ca[12][v]' => get_post_meta($order_id, '_billing_vat', true),
        'field[13]' => get_post_meta($order_id, '_order_number', true),
        'field[10]' => $order->get_customer_note(),
        'field[14]' => $item->get_product_id(),
        'field[15]' => $item->get_product()->get_name(),
        'field[17]' => $event_date,
        'field[16]' => $order->get_date_created()->format('d.m.Y'),
        'field[11]' => $pms[$pm_code],
        'field[12]' => $order->get_total(),
        'field[39]' => $event_loc_name,
        'field[40]' => $event_address,
        'field[38]' => $event_time,
      ];
    }
  }
  /*echo '<pre>';
    print_r($params);
    echo '</pre>';
    */
  foreach ($params as $param)
    echo '<iframe style="display: none;" src="https://planet-gv.activehosted.com/proc.php?u=1&f=1&c=0&m=0&act=sub&v=2&' . http_build_query($param) . '"></iframe>';

  $params = [];
  if (get_post_meta($order_id, '_applicant_first_name', true)) {
    $gdpr = get_post_meta($order_id, '_applicant_gdpr_checkbox', true) ? 'Da' : 'Ne';
    $gdpr_email = get_post_meta($order_id, '_applicant_gdpr_email', true) ? 'Da' : 'Ne';
    $gdpr_sms = get_post_meta($order_id, '_applicant_gdpr_sms', true) ? 'Da' : 'Ne';
    $gdpr_phone = get_post_meta($order_id, '_applicant_gdpr_phone', true) ? 'Da' : 'Ne';
    $gdpr_post = get_post_meta($order_id, '_applicant_gdpr_post', true) ? 'Da' : 'Ne';
   // $gdpr_privacy = get_post_meta($order_id, '_applicant_gdpr_privacy', true) ? 'Da' : 'Ne';
    $gdpr_terms = get_post_meta($order_id, '_applicant_gdpr_terms', true) ? 'Da' : 'Ne';

    $params = [
      'firstname' => get_post_meta($order_id, '_applicant_first_name', true),
      'lastname' => get_post_meta($order_id, '_applicant_last_name', true),
      'field[1]' => get_post_meta($order_id, '_applicant_company', true),
      'email' => get_post_meta($order_id, '_applicant_email', true),
      'phone' => get_post_meta($order_id, '_applicant_phone', true),
      'field[9]' => 'Prijavitelj',
      'field[8]' => $gdpr,
      'field[3]' => $gdpr_email,
      'field[5]' => $gdpr_sms,
      'field[4]' => $gdpr_phone,
      'field[6]' => $gdpr_post,
      'field[18]' => $gdpr_privacy,
      'field[7]' => $gdpr_terms,
      'customer_account' => get_post_meta($order_id, '_billing_company', true),
      'ca[2][v]' => get_post_meta($order_id, '_billing_address_1', true),
      'ca[6][v]' => get_post_meta($order_id, '_billing_post', true),
      'ca[4][v]' => get_post_meta($order_id, '_billing_city', true),
      'ca[12][v]' => get_post_meta($order_id, '_billing_vat', true),
      'field[13]' => get_post_meta($order_id, '_order_number', true),
      'field[10]' => $order->get_customer_note(),
      'field[14]' => $item->get_product_id(),
      'field[15]' => $item->get_product()->get_name(),
      'field[17]' => $event_date,
      'field[16]' => $order->get_date_created()->format('d.m.Y'),
      'field[11]' => $pms[$pm_code],
      'field[12]' => $order->get_total(),
      'field[39]' => $event_loc_name,
      'field[40]' => $event_address,
      'field[38]' => $event_time,
    ];
    echo '<iframe style="display: none;" src="https://planet-gv.activehosted.com/proc.php?u=1&f=1&c=0&m=0&act=sub&v=2&' . http_build_query($params) . '"/></iframe>';
  }
}

function pgv_wc_terms($terms_is_checked)
{
  return true;
}
add_filter('woocommerce_terms_is_checked', 'pgv_wc_terms', 10);
add_filter('woocommerce_terms_is_checked_default', 'pgv_wc_terms', 10);

add_action('woocommerce_after_order_notes', 'pojasnilo');
function pojasnilo() {
    echo '<p style="font-size: 0.9rem; margin-top: 15px; line-height: 24px;">Opomba: <span class="req-star">(*)</span> V primeru, ko prijavite več udeležencev hkrati, lahko izberete možnost, da vnesete prejemnika potrdila o prijavi in računa za kotizacijo. V kolikor te možnosti izberete, bo potrdilo o prijavi in račun dobil prvi navedeni udeleženec v prijavi.</p>';
}

add_action('woocommerce_review_order_before_submit', 'opomba_before_payment');
function opomba_before_payment() {
    echo '<p style="font-size: 0.9rem; margin-top: 15px; line-height: 24px;">Opomba: <span class="req-star">(**)</span> Ob plačilu po opravljeni storitvi ni možnosti odjave, možna je zgolj zamenjava udeleženca.</p>';
}

add_action('wp_ajax_ddv_check_ajax', 'ddv_check_ajax');
add_action('wp_ajax_nopriv_ddv_check_ajax', 'ddv_check_ajax');
function ddv_check_ajax() {
    if(isset($_POST['ddv_id']) && !empty($_POST['ddv_id'])) {
        $ddv_id = trim(sanitize_text_field(wp_unslash($_POST['ddv_id'])));
        global $wpdb;
        $res = $wpdb->get_results("SELECT * FROM zavezanci WHERE ddv_id='{$ddv_id}'");
        if(!empty($res))
            echo json_encode($res[0], true);
        else
            return false;
        wp_die();
    }
    wp_die();
}

add_action('wp_footer', 'ddv_check_js');
function ddv_check_js() {
    if( !is_checkout()) return;
    ?>
    <script id="ddv_validation">
    var rege = /^[0-9]{0,8}$/;
    jQuery(function($) {
        $("#billing_vat").keyup(function() {
            var ddvid = $(this).val().replace(/\s/g, '').replace(/SI/g, '');
            if(rege.test(ddvid)) {
                $.ajax({
				type:    'POST',
				url: wc_checkout_params.ajax_url,
				data: {
					'action': 'ddv_check_ajax',
					'ddv_id': ddvid
				},
				success: function (result) {
				    var res_obj = $.parseJSON(result);
				    if(result != false) {
				        $("#billing_vat").prop('disabled', false);
                        $('#billing_company_address').val(res_obj.name);
                        $(this).closest('p').removeClass('woocommerce-invalid');
                        $('#billing_address_1').val(res_obj.address);
                        $('#billing_postcode').val(res_obj.postcode);
                        $('#billing_city').val(res_obj.city);
                        $("#billing_vat").val(res_obj.ddv_id);
                        $('#billing_company_address_field label, #billing_address_1_field label, #billing_postcode_field label, #billing_city_field label').addClass('small-label');
				    }
				    else {
				        $("#billing_vat").closest('p').addClass('woocommerce-invalid');
				    }
				},
				error:   function(error) {
					$("#billing_vat").prop('disabled', false);
				}
			});
            }
            else {
                $(this).closest('p').addClass('woocommerce-invalid');
                $(this).prop('disabled', false);
            }
        });
    });    
    </script>
    <?php
}