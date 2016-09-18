<!DOCTYPE html>
<html>
<title>Schoonmaakrooster ORW21</title>
<head>
    <META
        HTTP-EQUIV="Refresh"
        CONTENT="300; URL=overzicht.php">
    <!-- elke 5 minuten refreshen -->

    <script src="scripts/week.js"></script>
    <link rel="stylesheet" type="text/css" href="algemeen.css">
    <link rel="stylesheet" type="text/css" href="week.css">
    <?php

    include 'verborgen/functies.php';
    include 'verborgen/indeling.php';
    include 'favicon.php';
    $conn = connect2DB();
    $ingelogd = check_ingelogd($conn);
    ?>
</head>

<body>
<div id="main">
    <?php include 'col_rechts.php'; ?>
    <?php
    $week = date("W");
    $jaar = date('Y',strtotime('thursday this week'));

    ob_start();
    // Check voor de komende 5 weken of deze zijn ingedeeld
    $numberOfweeks = 5;
    for ($i = 0; $i < $numberOfweeks; $i++) {
        //$week_reset date('W');
        //$jaar_reset = date('Y',strtotime('last thursday'));
        //resetter($conn, $week_reset, $jaar_reset);

        $week_check = weekMod($week + $i);
        echo "Week onder controle: $week_check <br/>";
        $jaar_check = jaarMod($jaar, $week + $i);
        echo "Jaar onder controle: $jaar_check <br/>";
        //echo "isVakantie <br/>";
        $isVakantie = isVakantie($conn, $week_check, $jaar_check);
        //echo "isIngedeeld <br/>";
        $isIngedeeld = isIngedeeld($conn, $week_check, $jaar_check);
        //echo "isvakantie : $isVakantie, isIngedeeld: $isIngedeeld <br/>";
        if (!$isVakantie && !$isIngedeeld) {
            echo "Deel in: week $week_check $jaar_check<br/>";
            //reset alle volgende weken (vanwege anders mogelijke inconsistenties)
            //echo "Reset <br/>";
            resetter($conn, $week_check, $jaar_check);
            //Deel in
            //echo "Deel in <br/>";
            indeling($conn, $week_check, $jaar_check, 1);// Één week indelen
        }
    }
    ob_end_clean();

    //## Als een nieuwe maand is begonnen een mail sturen naar alle huisgenoten
    $maand_cur = date('n');
    $jaar_cur = date('Y');
    $sql = "SELECT * FROM odt_emails WHERE maand='$maand_cur' AND jaar = '$jaar_cur'";
    $aantal = $conn->query($sql)->num_rows;

    if ($aantal == 0) { // als er nog geen mail is verzonden voor deze maand
        // Mail mag maar een keer worden verstuurd
        mail_huisgenoten($conn);
        $sql = "INSERT INTO odt_emails (maand, jaar) VALUES ('$maand_cur', '$jaar_cur') ";
        $conn->query($sql);
    }

    // Bepaal welke week moet worden getoond
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $week_post = $_POST['week'];
        $jaar_post = $_POST['jaar'];
    } else {
        $week_post = $week;
        $jaar_post = $jaar;
    }

    // Dit is belangrijk omdat een persoon dubbel in eenzelfde categorie kan staan door te wisselen
    $sql = "SELECT id, persoon, categorie, taak, controle  FROM odt_schoonmaakrooster WHERE week = $week_post AND jaar = $jaar_post ORDER BY categorie ASC";
    $result = $conn->query($sql);
    $gegevens = NULL;
    while ($res = $result->fetch_array(MYSQL_ASSOC)) {
        $gegevens[] = $res;
        //echo $res['persoon'];
    }
    $conn->close();


    // Controle of de taak afgetekend mag worden of al is gedaan
    $dag = date("D");
    if($week == $week_post) {
        foreach ($gegevens as $check) { // Per rij van de gegevens wordt de check gedaan
            if ($check['controle'] != '') {
                $class[] = 'naam_afgetekend';
                //echo "inactive";
            } elseif (($dag == "Mon" || $dag == "Tue") && ($check['categorie'] == "A1" || $check['categorie'] == "B1" || $check['categorie'] == "C" || $check['categorie'] == "D")) {
                $class[] = 'naam_active';
                //echo "active";
            } elseif (($dag == "Thu" || $dag == "Fri") && ($check['categorie'] == "A2" || $check['categorie'] == "B2" || $check['categorie'] == "C" || $check['categorie'] == "D")) {
                $class[] = 'naam_active';
                //echo "active";
            } elseif (($dag == "Wed" || $dag == "Sat" || $dag == "Sun") && ($check['categorie'] == "C" || $check['categorie'] == "D")) {
                $class[] = 'naam_active';
                //echo "active";
            } else {
                $class[] = 'naam_inactive';
                //echo "inactive";
            }
            //echo "<br/>";
            //$class[] = 'naam_active'; // Voor debugging handig
        }
    }
    else{
        foreach ($gegevens as $check) {
            $class[] = 'naam_inactive';
        }
    }

    ?>
    <div class="block1" id="weekselectie">
        <div class="col3">
            <?php
            if($week != $week_post){
           /* echo '<h4>Huidige week +'.($week_post-$week).'</h4>';*/
            }
             /*if($week_post!=$week){?>
                <button class="knop" id="knop_links" onclick="show_week(<?php echo $week.','.$jaar ?>);">Huidige week</button>
            <?php }*/
            if($week != $week_post){
                echo '<h2 style="display: inline-block; margin: 20px 0 0 60px ; padding: 5px;">Week '.$week_post.'</h2>';
            }
            else{
                echo '<h2 style="display: inline-block; margin: 20px 0 0 60px ; padding: 5px;">Huidige week ('.$week.')</h2>';
            }
            ?>
            <?php if(weekMod($week_post+1)-1<=weekMod($week+3+1)-1){?>
            <button class="knop" id="knop_rechts" onclick="show_week(<?php echo weekMod($week_post+1).','.jaarMod($jaar_post,$week_post+1) ?>);">Volgende week</button>
            <?php }?>
            <?php /*if(weekMod($week_post-1)+1>=weekMod($week)){ ?>
            <button class="knop" id="knop_rechts" onclick="show_week(<?php echo weekMod($week_post-1).','.jaarMod($jaar_post,$week_post+1) ?>);">Vorige week</button>
            <?php }*/?>
        </div>
    </div>
    <div class="block1" id="keuken&gr">
        <h1>Keuken & GR</h1>

        <div class="col1">
            <h2>Maandag - Dinsdag</h2>
            <?php
            for ($i = 0; $i < 2; $i++) {
                if($ingelogd && $class[$i] == 'naam_inactive'){ // Zo kan de admin mensen aftekenen als hij ingelogd is
                    $class[$i] = 'naam_active';
                }
                echo sprintf("<span class='%s'", $class[$i]);
                if ($class[$i] == 'naam_active') {// also if admin has logged in
                    echo sprintf("onclick=\"toon_taken(this,'%s','%s','%s');\"", $gegevens[$i]['id'], $gegevens[$i]['persoon'], $gegevens[$i]['categorie']);
                }
                echo sprintf(">%s</span>", $gegevens[$i]['persoon']);
                echo sprintf("<span class='col_float_r'>%s</span>", $gegevens[$i]['taak']);
            }
            ?>
        </div>
        <div class=" col2">
            <h2>Donderdag - Vrijdag</h2>
            <?php
            for ($i = 2; $i < 4; $i++) {
                if($ingelogd && $class[$i] == 'naam_inactive'){ // Zo kan de admin mensen aftekenen als hij ingelogd is
                    $class[$i] = 'naam_active';
                }
                echo sprintf("<span class='%s'", $class[$i]);
                if ($class[$i] == 'naam_active') {
                    echo sprintf("onclick=\"toon_taken(this,'%s','%s','%s');\"", $gegevens[$i]['id'], $gegevens[$i]['persoon'], $gegevens[$i]['categorie']);
                }
                echo sprintf(">%s</span>", $gegevens[$i]['persoon']);
                echo sprintf("<span class='col_float_r'>%s</span>", $gegevens[$i]['taak']);
            }
            ?>
        </div>
    </div>

    <div class="block1" id="douche&wc">
        <h1>Douche & toiletten</h1>

        <div class="col1">
            <h2>Maandag - Dinsdag</h2>
            <?php
            for ($i = 4; $i < 6; $i++) {
                if($ingelogd && $class[$i] == 'naam_inactive'){ // Zo kan de admin mensen aftekenen als hij ingelogd is
                    $class[$i] = 'naam_active';
                }
                echo sprintf("<span class='%s'", $class[$i]);
                if ($class[$i] == 'naam_active') {
                    echo sprintf("onclick=\"toon_taken(this,'%s','%s','%s');\"", $gegevens[$i]['id'], $gegevens[$i]['persoon'], $gegevens[$i]['categorie']);
                }
                echo sprintf(">%s</span>", $gegevens[$i]['persoon']);
                echo sprintf("<span class='col_float_r'>%s</span>", $gegevens[$i]['taak']);
            }
            ?>
        </div>
        <div class="col2">
            <h2>Donderdag - Vrijdag</h2>
            <?php
            for ($i = 6; $i < 8; $i++) {
                if($ingelogd && $class[$i] == 'naam_inactive'){ // Zo kan de admin mensen aftekenen als hij ingelogd is
                    $class[$i] = 'naam_active';
                }
                echo sprintf("<span class='%s'", $class[$i]);
                if ($class[$i] == 'naam_active') {
                    echo sprintf("onclick=\"toon_taken(this,'%s','%s','%s');\"", $gegevens[$i]['id'], $gegevens[$i]['persoon'], $gegevens[$i]['categorie']);
                }
                echo sprintf(">%s</span>", $gegevens[$i]['persoon']);
                echo sprintf("<span class='col_float_r'>%s</span>", $gegevens[$i]['taak']);
            }
            ?>
        </div>

    </div>

    <div class="block1" id="gang&vloer">


        <div class="col1">
            <h1 id="gang">Gang</h1>

            <h2>Maandag - Zondag</h2>
            <?php
            $i = 8;
            if($ingelogd && $class[$i] == 'naam_inactive'){ // Zo kan de admin mensen aftekenen als hij ingelogd is
                $class[$i] = 'naam_active';
            }
            echo sprintf("<span class='%s'", $class[$i]);
            if ($class[$i] == 'naam_active') {
                echo sprintf("onclick=\"toon_taken(this,'%s','%s','%s');\"", $gegevens[$i]['id'], $gegevens[$i]['persoon'], $gegevens[$i]['categorie']);
            }
            echo sprintf(">%s</span>", $gegevens[$i]['persoon']);

            ?>
        </div>
        <div class="col2">
            <h1 id="vloer">Vloer</h1>

            <h2>Maandag - Zondag</h2>
            <?php
            $i = 9;
            if($ingelogd && $class[$i] == 'naam_inactive'){ // Zo kan de admin mensen aftekenen als hij ingelogd is
                $class[$i] = 'naam_active';
            }
            echo sprintf("<span class='%s'", $class[$i]);
            if ($class[$i] == 'naam_active') {
                echo sprintf("onclick=\"toon_taken(this,'%s','%s','%s');\"", $gegevens[$i]['id'], $gegevens[$i]['persoon'], $gegevens[$i]['categorie']);
            }
            echo sprintf(">%s</span>", $gegevens[$i]['persoon']);
            ?>
        </div>
    </div>


</div>

</body>
</html>