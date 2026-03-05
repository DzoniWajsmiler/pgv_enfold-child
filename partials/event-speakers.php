<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 15.5.2019
 * Time: 13:36
 */

$speakers = get_field("predavatelji");
?>

<?php if ($speakers && !empty($speakers)): ?>

<?php $speakers_count = count($speakers); ?>

<div class="ow-predavatelji-wrap container">

    <div class="ow-predavatelji-borders">

        <?php if($speakers_count > 1): ?>

        <h2>
            <?php echo "Predavatelji"; ?>
        </h2>

        <div class="ow-predavatelji-slider">

            <?php foreach($speakers as $speaker): ?>

                <div class="ow-predavatelj-single">

                    <?php if (isset($speaker["slika_predavatelja"]) and !empty($speaker["slika_predavatelja"])): ?>
                        <div class="ow-speaker-img">
                            <img data-lazy="<?php echo $speaker["slika_predavatelja"]; ?>">
                        </div>
                    <?php endif; ?>

                    <?php if (isset($speaker["ime_predavateljca"]) and !empty($speaker["ime_predavateljca"])): ?>
                        <h3 class="ow-speaker-name">
                            <?php echo $speaker["ime_predavateljca"]; ?>
                        </h3>
                    <?php endif; ?>

                    <?php if (isset($speaker["opis"]) and !empty($speaker["opis"])): ?>
                        <p class="ow-speaker-about">
                            <?php echo $speaker["opis"]; ?>
                        </p>
                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

        <?php elseif($speakers_count == 1): ?>

        <?php foreach($speakers as $speaker): ?>

        <div class="ow-row ow-predavatelji-skip-slider">
            <div class="ow-left">
                <?php if (isset($speaker["slika_predavatelja"]) and !empty($speaker["slika_predavatelja"])): ?>
                    <div class="ow-speaker-img">
                        <img src="<?php echo $speaker["slika_predavatelja"]; ?>">
                    </div>
                <?php endif; ?>
            </div>

            <div class="ow-right">
<!--                <h3><?php echo "Predavatelj/ica"; ?></h3>-->
				<h3>
					<?php 
					if (get_the_ID() == 22981) { // zamenjava naziva predavatelja-ice
						echo "Izvajalka";
					} else {
						echo "Predavatelj/ica";
					}
					?>
				</h3>
                <?php if (isset($speaker["ime_predavateljca"]) and !empty($speaker["ime_predavateljca"])): ?>
                    <h2 class="ow-speaker-name">
                        <?php echo $speaker["ime_predavateljca"]; ?>
                    </h2>
                <?php endif; ?>

                <?php if (isset($speaker["opis"]) and !empty($speaker["opis"])): ?>
                    <p class="ow-speaker-about">
                        <?php echo $speaker["opis"]; ?>
                    </p>
				<?php endif; ?>
 
				<?php if (get_the_ID() == 22981): ?>  <!-- Izjema za Coaching akademija – ustvarjalna pot do osebne in poslovne odličnosti -->
					<a href="https://www.planetgv.si/karin-elena-sanchez/" class="ow-button custom_button button--1 color-pink" target="_blank">Več o izvajalki  <span class="button__container" style="filter: none;">
    <span class="circle top-left" style="transform: matrix(0, 0, 0, 0, -24, -42); opacity: 0;"></span>
    <span class="circle top-left" style="transform: matrix(0.28284, -0.28284, 0.28284, 0.28284, -45, -42); opacity: 0;"></span>
    <span class="circle top-left" style="transform: matrix(0, 0, 0, 0, -55, -14); opacity: 0;"></span>
    <span class="button__bg" style="transform: matrix(1, 0, 0, 1, 0, -26);"></span>
    <span class="circle bottom-right" style="transform: matrix(0, 0, 0, 0, 24, 43); opacity: 0;"></span>
    <span class="circle bottom-right" style="transform: matrix(0.28284, -0.28284, 0.28284, 0.28284, 39, 35); opacity: 0;"></span>
    <span class="circle bottom-right" style="transform: matrix(0, 0, 0, 0, 55, 14); opacity: 0;"></span>
  </span></a><br>
					<a href="https://www.planetgv.si/karin-elena-sanchez/#prijava" class="ow-button custom_button button--1 color-purple" target="_blank">Prijava na brezplačno online spoznavno srečanje  <span class="button__container" style="filter: none;">
    <span class="circle top-left" style="transform: matrix(0, 0, 0, 0, -24, -42); opacity: 0;"></span>
    <span class="circle top-left" style="transform: matrix(0.28284, -0.28284, 0.28284, 0.28284, -45, -42); opacity: 0;"></span>
    <span class="circle top-left" style="transform: matrix(0, 0, 0, 0, -55, -14); opacity: 0;"></span>
    <span class="button__bg" style="transform: matrix(1, 0, 0, 1, 0, -26);"></span>
    <span class="circle bottom-right" style="transform: matrix(0, 0, 0, 0, 24, 43); opacity: 0;"></span>
    <span class="circle bottom-right" style="transform: matrix(0.28284, -0.28284, 0.28284, 0.28284, 39, 35); opacity: 0;"></span>
    <span class="circle bottom-right" style="transform: matrix(0, 0, 0, 0, 55, 14); opacity: 0;"></span>
  </span></a><br>

                <?php endif; ?>
            </div>
        </div>

        <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>
<?php endif; ?>