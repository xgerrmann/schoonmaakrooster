<?php
/**
 * Created by PhpStorm.
 * User: XAnder
 * Date: 8-7-2015
 * Time: 00:23
 */

### Database ###
include 'verborgen/functies.php';
include 'verborgen/indeling.php';
include 'favicon.php';
$conn = connect2DB();

if (check_ingelogd($conn)) { // Functie mag alleen worden uitgevoerd als er al is ingelogd
    $week_start = date("W");

    $jaar_start = date('Y',strtotime('last thursday')); //according to iso 8601 thursday of current week corresponds with the year the week is in
    resetter($conn, $week_start, $jaar_start);
}

//###### Ga terug naar instellingen pagina
header("location: instellingen.php");
exit();


