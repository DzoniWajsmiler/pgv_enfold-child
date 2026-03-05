<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 16.5.2019
 * Time: 7:49
 */

/**
 * @param $eventDateTime
 * @return false|string
 */
function eventDate($eventDateTime) {
    $date = substr($eventDateTime, 0, 10);
    setlocale(LC_TIME, 'sl_SI');


    return strftime('%e. %B %Y', $date);

	
}

/**
 * @param $eventDateTime
 * @return bool|string
 */
function eventTime($eventDateTime) {
    $time = substr($eventDateTime, 0, -3);

    return $time;
}

function ticketDate($date) {
    return date('j. m. Y', $date);
}