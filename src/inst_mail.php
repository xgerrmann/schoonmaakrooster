<?php
/**
 * Created by PhpStorm.
 * User: Xander
 * Date: 15-7-2015
 * Time: 16:32
 * Verwerkt de wijzigingen in de vakanties van instellingen.php
 */

include 'verborgen/functies.php';
include 'favicon.php';
$conn = connect2DB();
if (check_ingelogd($conn)) {
    mail_huisgenoten($conn);
}
$conn->close();
###### Ga terug naar instellingen pagina
header("location: instellingen.php");
exit();

