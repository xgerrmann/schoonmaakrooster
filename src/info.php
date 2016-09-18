<?php

// functies hoeven niet te worden geinclude, want dit document komt in het document waar dat al wordt gedaan
//if(substr(basename($_SERVER['PHP_SELF']),0,-4)=='logout');


$week   = date('W');
$sql    = "SELECT informatie,datum FROM odt_wijzigingen ORDER BY datum DESC";
$res    = $conn->query($sql);


?>
<div class="col_right" id="info">
    <?php if($res->num_rows >0){
     echo '<h2>Laatste 5 wijzigingen</h2>';
        ?>

    <ul>

       <?php
       /* Set locale to Dutch */


       /* Output: vrijdag 22 december 1978 */
        for($p=0;$p<5;$p++){
           $row = $res->fetch_array(MYSQLI_ASSOC);
           setlocale(LC_ALL, 'nl_NL');
           $datumNL    = strftime("%a %e %b om %H:%M", strtotime($row['datum']));
           echo sprintf("<li class='tijd'>%s</li><li class='informatie'>%s</li>",$datumNL,$row['informatie']);

       }

        ?>
    </ul>
    <?php
    }
    else{
        echo '<h2>Geen wijzigingen deze week</h2>';
    }
    ?>
</div>
<!--
<div class="col_right"  id="bitcoin">
    <h2>Bitcoin</h2>
    <img src="https://bitcoinity.org/markets/image?span=7d&size=medium&currency=EUR&exchange=kraken" alt="bitcoin price chart"/></div>
-->