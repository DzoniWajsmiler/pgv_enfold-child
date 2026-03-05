<?php

/**
 * Template name: Dokumenti
 */

get_header();
if (is_user_logged_in() ) {
$user = wp_get_current_user();
//$dogodki = get_field('udelezba', 'user_' . $user->ID);
//get_potrdilo($id, $user->email);
$email = 'zlatniktomaza2@gmail.com';
$dogodki = get_user_events($email);
?>

<div id="primary" class="content-area dogodki">
  <main id="main" class="site-main" role="main" style="padding: 5rem 0;">
    <h2>Moji dogodki</h2>
    <table>
      <thead>
        <tr>
          <th scope="col">Dogodek</th>
          <th scope="col">Datum</th>
          <th scope="col">Gradiva</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($dogodki as $id) {
          $potrdilo_data = get_potrdilo(get_field('kotizacija_id', $id), $email);
          if($potrdilo_data['status'] == 1) {
            $potrdilo_pdf = $potrdilo_data['documents'][0]['base64'];
          }
          $dogodek = wc_get_product($id);
          $start_date = get_post_meta($id, 'ow_event_start_date', true);
          $start_date = date("d.m.Y", strtotime($start_date));
          $gradiva = get_field('gradiva', $id);
          $gradiva_html = '<ul class="gradiva">';
          $gradiva_html .= '<li>Potrdilo o udeležbi <div style="display: none;"><object data="data:application/pdf;base64,' . $potrdilo_pdf . '" type="application/pdf" style="height:70vh;width:80vw;"></object></div><i class="fa-light fa-file-pdf"></i></li>';
          foreach($gradiva as $g) { 
              $ext = '';
              $temp= explode('.',$g['datoteka']['url']);
             $ext = end($temp);
            $gradiva_html .= '<li>'.$g['naziv'].'<div style="display: none;"><object type="application/pdf" data="'.$g['datoteka']['url'].'" style="height:70vh;width:80vw;"></object></div><i class="fa-light fa-file-'.$ext.'"></i></li>';
          }
          $gradiva_html .= '</ul>';
          
          echo '
          <tr>
            <td data-label="Dogodek">' . $dogodek->get_name() . '</td>
            <td data-label="Datum">' . $start_date . '</td>
            <td data-label="Gradiva">'.$gradiva_html.'</td>
          </tr>
          ';
        }
        ?>

      </tbody>
    </table>

  </main><!-- #main -->
</div><!-- #primary -->
<?php
}
else {
    ?>

<div id="primary" class="content-area dogodki">
  <main id="main" class="site-main" role="main" style="padding: 5rem 0;">
    <div>
        <?php wp_login_form(); ?>
        <a href="<?php echo esc_url( wp_registration_url() ); ?>"><?php esc_html_e( 'Niste registrirani?', 'wordpress' ); ?></a>
        
    </div>
  </main><!-- #main -->
</div><!-- #primary -->
<?php
}
get_footer();
