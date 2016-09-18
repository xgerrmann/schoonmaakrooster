<?php

### Database ###
include 'verborgen/functies.php';
$conn = connect2DB();
include 'verborgen/indeling.php';
include 'favicon.php';

if (check_ingelogd($conn)) { // Functie mag alleen worden uitgevoerd als er al is ingelogd
    //echo 'Ingelogd!';
//    $week_start = date('W')+1; // Laat de indeling van de huidige week intact en deelt opnieuw in vanaf het begin van de volgende week
    $week_start = date('W');
    $jaar_start = date('Y',strtotime('thursday this week'));
 // according to iso 8601
    $aantalweken = 5;
    resetter($conn,$week_start,$jaar_start); //Delete alles vanaf de  huidige week
    indeling($conn,$week_start,$jaar_start,$aantalweken); // Deelt in vanaf de huidige week tot 5 weken in de toekomst
}
else{
    //echo 'Niet ingelogd!';
}

//###### Ga terug naar instellingen pagina
header("location: instellingen.php");
exit();


