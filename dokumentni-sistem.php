<?php
function gv_redirect_login_page()
{
  if (!is_user_logged_in() && is_page_template('template-dokumenti.php')) {
    wp_redirect('/wp-login.php');
    exit();
  }
}
//add_action('template_redirect', 'gv_redirect_login_page');

function gv_restrict_wpadmin_access()
{
  if (!defined('DOING_AJAX') || !DOING_AJAX) {
    $user = wp_get_current_user();

    if (isset($user->roles) && is_array($user->roles)) {
      if (in_array('customer', $user->roles)) {
        wp_redirect('/dokumenti');
        exit();
      }
    }
  }
}
//add_action('admin_init', 'gv_restrict_wpadmin_access');


function custom_login_failed( $username )
{
    $referrer = wp_get_referer();

    if ( $referrer && ! strstr($referrer, 'wp-login') && ! strstr($referrer,'wp-admin') )
    {
        wp_redirect( add_query_arg('login', 'failed', $referrer) );
        exit;
    }
}
add_action( 'wp_login_failed', 'custom_login_failed' );

function custom_authenticate_username_password( $user, $username, $password )
{
    if ( is_a($user, 'WP_User') ) { return $user; }

    if ( empty($username) || empty($password) )
    {
        $error = new WP_Error();
        $user  = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));

        return $error;
    }
}
add_filter( 'authenticate', 'custom_authenticate_username_password', 30, 3);

function get_user_events($email) {
    global $wpdb;
    
    $events_email = $wpdb->get_results("SELECT * FROM eventSignupsParticipants WHERE email='{$email}'");
    
    $events = [];
    foreach($events_email as $e) {
        $signup = $wpdb->get_results("SELECT * FROM eventSignups WHERE eventSignupID='{$e->eventSignupID}'")[0];
        if(!empty($signup->kotizacija_id)) {
            $product = wc_get_products([
                'meta_key' => 'kotizacija_id', //meta key name here
                'meta_value' => $signup->kotizacija_id, 
                'meta_compare' => '=',
            ]);
            if(!empty($product))
                $events[] = $product[0]->get_id();
        }
    }
    
    return $events;
}

function get_event($kot_id) {
    $product = wc_get_products([
                'meta_key' => 'kotizacija_id', //meta key name here
                'meta_value' => $kot_id, 
                'meta_compare' => '=',
            ]);
    return $product[0];
}

function get_potrdilo($event_id, $email, $page)
{
  
  /*global $wpdb;

  $signup = $wpdb->get_results("SELECT * FROM eventSignups WHERE kotizacija_id='{$kotizacija_id}'")[0];
  $participant = $wpdb->get_results("SELECT * FROM eventSignupsParticipants WHERE (eventID='{$signup->eventID}' AND email='{$email}')");
  if (!empty($participant)) {
    $event_ref = $wpdb->get_results("SELECT ref_id FROM eventsTBL WHERE eventID='{$signup->eventID}'")[0];
  }*/

  $wsdl = 'https://planet-gv.intrix.si/soap/participation/wsdl?wsdl';

  $auth           = new stdClass();
  $auth->username = 'soap';
  $auth->password = 'bFJTXrVpt5pFcSDSf';

  $headers = new SoapHeader('auth', 'authenticate', new SoapVar($auth, SOAP_ENC_OBJECT), false);

  $client = new SoapClient($wsdl);
  $client->__setSoapHeaders($headers);

  $data = [
   'email'      => $email,
   'per_page'   => 5,
   'page'       => $page,
   'order_by'   => 'DESC'
  ];

  
  try {    
    $result = $client->getPotrdila(json_encode($data));
    error_log(print_r($result, true));
  }catch(Throwable $e){
    return false;
  }

  return json_decode($result, true);
}


add_action('wp_head', 'dokumentni_sistem_css');
function dokumentni_sistem_css()
{
  if (is_page_template('template-dokumenti.php')) {
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
      .dogodki main {
        display: flex;
        max-width: 1200px;
        margin: 0 auto;

        flex-wrap: wrap;
        align-items: center;
        flex-direction: column;
      }

      .dogodki h2 {
        text-align: center;
      }

      table {
        border: none;
        border-collapse: collapse !important;
        margin: 0;
        padding: 0;
        width: 860px;
        table-layout: fixed;
        margin-top: 50px;
      }

      table caption {
        font-size: 1.5em;
        margin: .5em 0 .75em;
      }

        tbody {
            display: table;
            width: 770px;
            margin-left: 50px;
        }
      
      
      thead {
          border: none;
      }
      
      thead tr {
          border-bottom: none !important;
      }

      table th,
      table td {
        padding: .925em;
        border: none !important;
        border-left: none !important;
        border-top: none !important;
        border-right: none !important;
        font-size: 1rem !important;
    font-weight: 600!important;
        
        
        text-align: center;
      }
      
      .dogodki td {
        display: table-cell;
        border: none;
        vertical-align: middle;
        line-height: 1.5;
      }

      table th {
        font-size: .85em;
        font-weight: 600;
        color: #fff;
        letter-spacing: .1em;
        text-transform: uppercase;
        background-color: #992383;
        border-bottom: none !important;
        text-align: left !important;
      }
      
      tbody td {
          border: none !important;
        border-bottom: 1px solid #ddd !important;
        padding: 1.2rem 0;
      }
      
      table td.date {
          color: #992383;
      }
      table th:first-child{
      border-radius:50px 0 0 50px;
    }
    
    table th:last-child{
      border-radius:0 50px 50px 0;
    }
    
    .right {
        text-align: right !important;
    }
    .left {
        text-align: left !important;
    }
    table.gradiva th,
    table.potrdilo th {
        background-color: #F5E9F2;
        color: #992383;
        padding-left: 50px;
    }
    
    table.potrdilo td {
        border-bottom: unset !important;
        padding-top: 40px;
    }
    
    table.gradiva tr td:first-child,
    table.potrdilo tr td:first-child{
        display: flex;
        align-items: center;
    }

    table.gradiva td img,
    table.potrdilo td img {
        margin-right: 25px;
    }
    
    .nextpage {
    display: inline-block;
    flex: 0;
    max-width: 200px;
    padding: 10px;
    text-align: center;
    }
    
    .nav {
        width: 100%;
        display: flex;
        justify-content: center;
    }
    .nav-inner {
        max-width: 860px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .nav h3 {
        color: #992383 !important;
    }

      #colorbox {
          z-index: 999;
      }
      
      .dogodki i {
          font-family: "Font Awesome\ 6 Free";
         display: inline-block;
         padding-right: 3px;
         font-weight: 900;
         content: "\f00c";
         font-size: 1rem !important;
         color: #992383 !important;
      }
      .dogodki i.circle {
        background-color:transparent;
        border:1px solid #992383;    
        height:24px;
        border-radius:50%;
        -moz-border-radius:50%;
        -webkit-border-radius:50%;
        width:24px;
        padding: 3px;
        text-align: center !important;
      }
      
      .dogodki #loginform .login-username input,
      .dogodki #loginform .login-password input {
          border-radius: 50px;
            height: 48px;
      }
      .dogodki #loginform .login-username label,
      .dogodki #loginform .login-password label {
          display: none;
      }
      
      .dogodki #loginform .login-remember {
          text-align: center;
      }
      .dogodki #loginform .login-remember input {
          width: 18px;
          height: 18px;
      }
      
      .dogodki .login-remember {
          margin-top: 30px;
      }
      .dogodki #rememberme {
        position: relative;
        top: 3px;
        left: -3px;
      }
      
      .dogodki .login-submit {
          text-align: center;
                    margin-top: 20px;
      }
      .dogodki #wp-submit {
          background-color: #992383 !important;
          color: #fff !important;
          border-radius: 50px;
                  width: 160px;
        padding: 10px 30px;
        
        text-transform: uppercase;
      }
      .dogodki .form-actions {
          width: 100%;
          display: flex;
          justify-content: space-between;
      }
      
      .dogodki .magazines-wrap:before {
          content: "";
              background-color: #e6e6e6;
    background-repeat: no-repeat;
    background-image: url(https://planetgv.si/wp-content/uploads/2019/04/Group_2-2.png);

content: "";    background-position: top left;
    width: 100wv;
    display: block;
    position: absolute;
    top: 0;
    bottom: 0;
    right: 0;
      }
      
      .dogodki .magazines-inner-wrap {
          flex-wrap: nowrap;
      }
      .dogodki .single-magazine {
              flex-direction: column;
              margin-top: 0 !important;
      }
      .dogodki .single-magazine .ow-badge {
              display: none;
      }
      
      .dogodki .magazine-images {
          height: 15vh !important;
          margin-top: 30px;
      }
      .dogodki .first-img {
          height: unset !important;
      }
      .dogodki .second-img {
          height: unset !important;
      }
      .dogodki .magazine-texts {
          width: 80%;
      }
      
      table.udelezbe tbody tr,
      table.gradiva tbody tr,
      table.potrdilo tbody tr {
          cursor: pointer;
      }
      
      @media screen and (max-width: 600px) {
        table {
          border: 0;
          max-width: 100%;
          width: 100%;
        }

        table caption {
          font-size: 1.3em;
        }

        table thead {
          border: none;
          clip: rect(0 0 0 0);
          height: 1px;
          margin: -1px;
          overflow: hidden;
          padding: 0;
          position: absolute;
          width: 1px;
        }

        table tr {
          border-bottom: 3px solid #ddd;
          display: block;
          margin-bottom: .625em;
        }
        
        table .right {
            width: 10%;
        }
        table tr td:nth-child(1) {
            width: 30%;
            padding-left: 8px;
        }
        table tr td:nth-child(2) {
            width: 50%;
            padding-left: 5px;
        }

        table td {
          border-bottom: 1px solid #ddd;
          display: block;
          font-size: .8em;
          text-align: right;
        }
        table tbody {
          max-width: 100%;
          width: 100%;
          margin-left: 0;
        }

        table td::before {
            display: none;
          /*
    * aria-label has no advantage, it won't be read inside a table
    content: attr(aria-label);
    */
          content: attr(data-label);
          float: left;
          font-weight: bold;
          text-transform: uppercase;
        }

        table td:last-child {
          border-bottom: 0;
        }
        .gradiva li object {
          height:80vh;
          width:90vw;
      }
      
      .page-template-template-dokumenti .magazine-images {
          height: 45vh !important;
      }
      
 
    }
    table.gradiva tr td:first-child, table.potrdilo tr td:first-child {
        display: table-cell;
        width: auto;
    }
      }

      
    </style>
  <?php
  }
}

function pw_reset_css() {
    if( (isset($_GET['action'] ) && $_GET['action'] == 'lostpassword') || (isset($_GET['action'] ) && $_GET['action'] == 'rp') ) {
        ?>
         <style>
             .login h1 a {
                background-image: url(https://www.planetgv.si/wp-content/uploads/2021/03/planetgv-logo.svg) !important;
             }
             #nav {
                 display: none !important;
             }
             .login .message {
                 border-color: #992383;
             }
             #wp-submit {
                    display: block !important;
                     float: unset !important;
                     background-color: #992383 !important;
                     border-radius: 100px;
                     font-family: "Montserrat Regular",sans-serif;
                     margin: 0 auto !important;
                     padding: 10px 30px !important;
                     outline: none !important;
                     box-shadow: unset !important;
                     text-shadow: unset !important;
                     font-size: 15px !important;
                     height: auto !important;
                     border: unset !important;
             }
             a[href] {
                 color: #992383 !important;
             }
         </style>
        <?php
    }
}
add_action('login_headerurl', 'pw_reset_css');

function acc_created_css() {
    global $wp;
        
    if( $wp->request == 'wp-signup.php' ) {
        ?>
         <style>
         #main {
             background-color: transparent !important;
         }
             #signup-content {
                 max-width: 100% !important;
             }
             .mu_register {
                 display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
             }
             .mu_register p {
                 margin-top: 10px;
             }
         </style>
        <?php
    }
}
add_action('wp_footer', 'acc_created_css');

function acc_activated_css() {
    if( $GLOBALS['pagenow'] == 'wp-activate.php' ) {
        ?>
         <style>
         h2 {
             display: block !important;
         }
         #main {
             background-color: transparent !important;
         }
             #signup-content {
                 max-width: 100% !important;
             }
             .wp-activate-container {
                 display: flex;
                flex-direction: column;
                justify-content: center;
             }
             .wp-activate-container p {
                 margin-top: 10px;
             }
             #signup-welcome p {
                 font-weight: 600 !important;
             }
             p span {
                 padding-left: 0 !important;
                 font-weight: 300 !important;
             }
             p.view a[href] {
                color: #992383 !important;
             }
         </style>
        <?php
    }
}
add_action('wp_head', 'acc_activated_css', 99);

add_action('wp_head', 'register_css');
function register_css() {
    ?>
    <style>
        .page-id-35 #wrap_all {
          position: relative !important;
      }

        .page-id-35 #main {
            position: relative !important;
            z-index: 4 !important;
        }
        
        .mu_register {
                margin: 50px auto 120px auto !important;
        }
        
        #signup-content {
            max-width: 500px;
            margin: 0 auto;
        }
        #setupform:before {
            margin-top: 50px;
            content:"";
            display: flex;
            background-repeat: no-repeat;
            background-image: url(https://www.planetgv.si/wp-content/uploads/2021/03/planetgv-logo.svg);
            background-position: 50%;
            width: 300px;
            height: 100px;
            margin: 0 auto !important;
            position: relative;
    top: 30px;
        }
        #user_name,
        #signup-content h2,
        #setupform > p:nth-child(6),
        #setupform > p:nth-child(5),
        #setupform > input:nth-child(6),
        #setupform > label,
        #setupform > input:nth-child(7),
        #setupform > br:nth-child(8),
        #setupform > #text:nth-child(9){
            display:none !important;
        }
        #setupform {
            font-size: 0px;
            max-width: 400px !important;
        }
        #setupform p.before {
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }
        #setupform label,
        #setupform input {
            font-size: 1rem !important;
        }
        #setupform #user_email {
            margin-bottom: -20px;
        }
        
        #setupform input {
          border-radius: 50px !important;
            height: 48px;
      }
      
      #setupform .submit {
          text-align: center !important;
          margin-top: -20px;
      }
      #setupform input.submit {
          background-color: #992383 !important;
          color: #fff !important;
          border-radius: 50px;
                  width: 160px;
        padding: 10px 30px;
      }
      
      a.privacy {
          display: block;
          color: #992383 !important;
              margin-bottom: 10px;
      }

      
      .dogodki .second-img {
          height: unset !important;
      }
        
        /* general styling */
      body {
        /*line-height: 1.25;*/
      }
      
      ::placeholder {
           text-align: center; 
        }
        
        /* or, for legacy browsers */
        
        ::-webkit-input-placeholder {
           text-align: center;
        }
        
        :-moz-placeholder { /* Firefox 18- */
           text-align: center;  
        }
        
        ::-moz-placeholder {  /* Firefox 19+ */
           text-align: center;  
        }
        
        :-ms-input-placeholder {  
           text-align: center; 
        }
        
        .form-actions a {
            color: #b90066 !important;
        }

    </style>
    <?php
}

add_action('wp_footer', 'register_js');
function register_js() {
    ?>
    <script>
    jQuery(function($) {
        $('#setupform #user_email').attr('placeholder', 'E-poštni naslov');
        $('#user_email').on('change',function(){
            $('#user_name').val($(this).val());
        });
        $('#setupform #user_email').before($('<p class="before">').text("Vpišite svoj e-poštni naslov za začetek postopka registracije"));
        $('#setupform .submit').before($('<a href="https://www.planetgv.si/politika-zasebnosti/" class="privacy">').text("Politika zasebnosti"));
        $('.wp-activate-container .view a:first').attr('href', "<?php echo get_site_url().'/dokumenti' ?>");
        $('.wp-activate-container .lead-in a:first').attr('href', "<?php echo get_site_url().'/dokumenti' ?>");
      });
    </script>
    <?php
}

function wpse_295037_disable_username_character_type_restriction( $result ) {
    $errors = $result['errors'];
    $user_name = $result['user_name'];

    // The error message to look for. This should exactly match the error message from ms-functions.php -> wpmu_validate_user_signup().
    $error_message = __( 'Usernames can only contain lowercase letters (a-z) and numbers.' );

    // Look through the errors for the above message.
    if ( !empty($errors->errors['user_name']) ) foreach( $errors->errors['user_name'] as $i => $message ) {

        // Check if it's the right error message.
        if ( $message === $error_message ) {

            // Remove the error message.
            unset($errors->errors['user_name'][$i]);

            // Validate using different allowed characters based on sanitize_email().
            $pattern = "/[^a-z0-9+_.@-]/i";
            if ( preg_match( $pattern, $user_name ) ) {
                $errors->add( 'user_name', __( 'Username is invalid. Usernames can only contain: lowercase letters, numbers, and these symbols: <code>+ _ . @ -</code>.' ) );
            }

            // If there are no errors remaining, remove the error code
            if ( empty($errors->errors['user_name']) ) {
                unset($errors->errors['user_name']);
            }
        }
    }

    return $result;
}
add_filter( 'wpmu_validate_user_signup', 'wpse_295037_disable_username_character_type_restriction', 20 );

add_action('wp_footer', 'dokumentni_sistem_js');
function dokumentni_sistem_js()
{
  if (is_page_template('template-dokumenti.php')) {
  ?>
    <script>
      jQuery(function($) {
        $('.login-username input').attr('placeholder', 'E-poštni naslov'); 
        $('.login-password input').attr('placeholder', 'Geslo');
      })
    </script>
<?php
  }
}

add_filter( 'body_class', 'logged_in_body_class' );

function logged_in_body_class( $classes ) {

    if(is_user_logged_in())
        $classes[] = 'logged-in';
    else
        $classes[] = 'logged-out';
    

    return $classes;
}

function wpse27856_set_content_type(){
    return "text/html";
}
add_filter( 'wp_mail_content_type','wpse27856_set_content_type' );

function auto_redirect_after_logout(){
  wp_safe_redirect( '/' );
  exit;
}
add_action('wp_logout','auto_redirect_after_logout');

function after_lost_password_redirect() {
    wp_safe_redirect( '/dokumenti' ); 
    exit;
}
add_action('after_password_reset', 'after_lost_password_redirect');

function before_lost_password_redirect() {

    // Check if have submitted 
    $confirm = ( isset($_GET['checkemail'] ) ? $_GET['checkemail'] : '' );

    if( $confirm ) {
        wp_redirect( '/dokumenti' ); 
        exit;
    }
}
add_action('login_headerurl', 'before_lost_password_redirect');

function logout_url() {
    echo wp_logout_url();
}
add_shortcode('logout-url', 'logout_url');

function wpa_remove_menu_item( $items, $menu, $args ) {
    if ( is_admin() || ! is_user_logged_in() ) 
        return $items;
    foreach ( $items as $key => $item ) {
        if ( 'Odjava' == $item->title ) {
            $items[$key]->url = wp_logout_url();
        }
    }
    return $items;
}
add_filter( 'wp_get_nav_menu_items', 'wpa_remove_menu_item', 10, 3 );