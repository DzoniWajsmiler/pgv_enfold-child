<?php

/**
 * Template name: Dokumenti
 */

get_header();
if (is_user_logged_in() ) {
$user = wp_get_current_user();

$email = $user->user_email;
// if(current_user_can('administrator')) {
//     $email = 'peter.ribaric@planetgv.si';
// }
if(isset($_GET['pg']) && !empty($_GET['pg']))
    $page = $_GET['pg'];
else
    $page = 1;
$nextpage = $page+1;
$potrdila = get_potrdilo(null, $email, $page);
/*if(!$potrdila)
    wp_safe_redirect('/');*/
// if(current_user_can('administrator')) {
//     echo json_encode($potrdila, JSON_PRETTY_PRINT);
// }
//$dogodki = get_user_events($email);
//print_r($dogodki);

?>

<div id="primary" class="content-area dogodki">
  <main id="main" class="site-main" role="main" style="padding: 5rem 0;">
      <?php if(isset($_GET['e']) && !empty($_GET['e'])) { 
        $event_id = $_GET['e'];
        $dogodek = wc_get_product($event_id);
        if($dogodek) {
            //$kotizacija_id = get_field('kotizacija_id', $event_id);
            
            $event_title = $dogodek->get_name();
        }
        else {
            
        }
        $potrdilo_data = get_potrdilo(null, $email, sanitize_text_field($_GET['pg']));
        //print_r($potrdilo_data);
        if($potrdilo_data) {
            foreach ($potrdilo_data['documents'] as $doc) {
                if(!empty($doc['cotization_id'])) {
                    $kot_id = explode('_', $doc['cotization_id'])[0];
                    $dogodek = get_event($kot_id);
                    $id = $dogodek->get_id();
                }
                else {
                    $id = $doc['event_id'];
                }
                $dogodki[] = $id;
                if($id == $event_id) {
                    $potrdilo_pdf = $doc['base64'];
                    $event_title = $doc['event_title'];
                }
            }
        }

        $icon_path = trailingslashit(get_stylesheet_directory_uri()).'file-icons';
        $icons = [
            'docx' => 'DOC.svg',
            'doc' => 'DOC.svg',
            'txt' => 'DOC.svg',
            'ppt' => 'PPT.svg',
            'pptx' => 'PPT.svg',
            'xls' => 'XLS.svg',
            'xlsx' => 'XLS.svg',
            'pdf' => 'PDF.svg',
            'jpg' => 'JPG.svg',
            'jpeg' => 'JPG.svg',
            'zip' => 'ZIP.svg',
            'rar' => 'ZIP.svg',
        ];
        
       /* if($potrdilo_data['status'] == 1) {
            $potrdilo_pdf = $potrdilo_data['documents'][0]['base64'];
        }*/
        
        $index = array_search($event_id, $dogodki);
        $before = "";
        $after = "";
        if($index === false){
        }else{
            $before = $index > 0 ? $dogodki[$index - 1] : $dogodki[count($dogodki)-1];
            $after = ($index + 1) < count($dogodki) ? $dogodki[$index + 1] : $dogodki[0];
        }
        
        $before = '/dokumenti?e='.$before.'?pg='.$_GET['pg'];
        $after = '/dokumenti?e='.$after.'?pg='.$_GET['pg'];
      ?>
       <h2>Udeležbe na<br>dogodkih in konferencah</h2>
        <div class="nav"><div class="nav-inner"><a href="<?php echo $before; ?>" ><i class="fa-solid fa-chevron-left circle"></i></a><h3><?php echo $event_title; ?></h3><a href="<?php echo $after; ?>" ><i class="fa-solid fa-chevron-right circle"></i></a></div></div>
        <?php if($potrdilo_pdf) { ?>
        <table class="potrdilo">
          <thead>
            <tr>
              <th scope="col" style="width: 90%;">Potrdilo o udeležbi</th>
              <th scope="col" style="width: 10%;"></th>
            </tr>
          </thead>
          <tbody>
            <?php
             
              echo '
              <tr>
                <td class="left"><img width="35px" height="45px" src="'.$icon_path.'/certificate.png'.'">' . wp_specialchars('Potrdilo o udeležbi') . '</td>
                <td class="right"><a><i class="fa-solid fa-chevron-right circle"></i><div style="display:none;"><object id="potrdilo_object" data="data:application/pdf;base64,' . $potrdilo_pdf . '" type="application/pdf" style="height:70vh;width:80vw;"></object></div></a></td>
              </tr>
              ';
            ?>
          </tbody>
        </table>
        <?php } ?>
        
        <table class="gradiva">
          <thead>
            <tr>
              <th scope="col" style="width: 90%;">Gradiva dogodka</th>
              <th scope="col" style="width: 10%;"></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $gradiva = get_field('gradiva', $event_id);
            if($gradiva) {
              foreach($gradiva as $g) { 
                  $ext = '';
                  $temp= explode('.',$g['datoteka']['url']);
                 $ext = end($temp);
                 
                 $icon = '';
                 if(isset($icons[$ext]))
                    $icon = $icon_path . '/' .$icons[$ext];
                 echo '
                  <tr onclick="window.location=\''.$g['datoteka']['url'].'\';">
                    <td class="left"><img width="35px" height="45px" src="'.$icon.'">' . $g['naziv'] . '</td>
                    <td class="right"><a href="'.$g['datoteka']['url'].'"><i class="fa-solid fa-chevron-right circle"></i></a></td>
                  </tr>
                  ';
              }
            }
            ?>
    
          </tbody>
        </table>
      <?php
      }
      else { 
      ?>
        <h2>Udeležbe na<br>dogodkih in konferencah</h2>
        <table class="udelezbe">
          <thead>
            <tr>
              <th scope="col" style="width: 25%; padding-left: 50px;">Datum</th>
              <th scope="col" style="width: 60%; padding-left: 40px;">Dogodek</th>
              <th scope="col" style="width: 15%;"></th>
            </tr>
          </thead>
          <tbody>
            <?php
            /*foreach ($dogodki as $id) {
                $dogodek = wc_get_product($id);
                $start_date = get_post_meta($id, 'ow_event_start_date', true);
              $start_date = date("d.m.Y", strtotime($start_date));
              echo '
              <tr onclick="window.location=\'/dokumenti?e='.$id.'\';">
                <td class="date left" data-label="Datum">' . $start_date . '</td>
                <td data-label="Dogodek" class="left">' . $dogodek->get_name() . '</td>
                <td class="right"><i class="fa-solid fa-chevron-right circle"></i></td>
              </tr>
              ';
            }*/
            foreach ($potrdila['documents'] as $doc) {
                if(!empty($doc['cotization_id'])) {
                    $kot_id = explode('_', $doc['cotization_id'])[0];
                    $dogodek = get_event($kot_id);
                    $id = $dogodek->get_id();
                    $start_date = get_post_meta($id, 'ow_event_start_date', true);
                  $start_date = date("d.m.Y", strtotime($start_date));
                }
                else {
                    $id = $doc['event_id'];
                    $start_date = date("d.m.Y", strtotime($doc['event_date']));
                }
                  echo '
                  <tr onclick="window.location=\'/dokumenti?e='.$id.'&pg='.$page.'\';">
                    <td class="date left" data-label="Datum">' . $start_date . '</td>
                    <td data-label="Dogodek" class="left">' . $doc['event_title']. '</td>
                    <td class="right"><i class="fa-solid fa-chevron-right circle"></i></td>
                  </tr>
                  ';
                
            }
            ?>
    
          </tbody>
        </table>
        <a class="ow-button custom_button button--1 color-pink nextpage" href="/dokumenti/?pg=<?php echo $nextpage; ?>" target="">Naslednja stran</a>
        <?php } 
        
        echo do_shortcode('[strokovne_revije_banner]');
        
        ?>

  </main><!-- #main -->
</div><!-- #primary -->
<?php
}
else {
    ?>

<div id="primary" class="content-area dogodki">
  <main id="main" class="site-main" role="main" style="padding: 5rem 0;">
    <div>
        <img height="100" width="300" src="https://www.planetgv.si/wp-content/uploads/2021/03/planetgv-logo.svg" alt="Planet GV">
        <h2 style="margin-top: 50px;">Prijava</h2>
        <h4 style="margin-bottom: 30px;">Vpišite svoj e-poštni naslov in geslo</h4>
        <?php if(isset($_GET['login']) && $_GET['login'] == 'failed') {
            echo '<p style="color:red;">Uporabniško ime ali geslo se ne ujemata.</p>';
        } ?>
        <?php wp_login_form(); ?>
        <div class="form-actions">
            <a href="<?php echo esc_url( wp_registration_url() ); ?>"><?php esc_html_e( 'Niste registrirani?', 'wordpress' ); ?></a>
            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Ste pozabili geslo?', 'wordpress' ); ?></a>
        </div>
        
    </div>
  </main><!-- #main -->
</div><!-- #primary -->

<?php
}
?>
<script>
    jQuery(function($) {
       var potrdilo = "<?php echo $potrdilo_pdf; ?>";
 const fileName = 'potrdilo.pdf';

    $('.potrdilo tr').click(function() {
        const newWindow = window.open('', fileName, "width=1000,height=1200");
        setTimeout( function() {

            var pdf = '<object data="data:application/pdf;base64,' + '<?php echo $potrdilo_pdf; ?>' + '" type="application/pdf" ></object>';

            const object_file = newWindow.document.createElement('object');
            object_file.setAttribute('data', 'data:application/pdf;base64,' + '<?php echo $potrdilo_pdf; ?>');
            object_file.setAttribute('type', 'application/pdf');
            object_file.setAttribute('style', 'height:100vh;width:100vh;');
            
            newWindow.document.body.appendChild(object_file);
        }, 100);
    });
    
    
        
    });
</script>
<?php get_footer();
