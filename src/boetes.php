<?php
/**
 * Created by PhpStorm.
 * User: XAnder
 * Date: 10-7-2015
 * Time: 14:21
 */
?>
<!DOCTYPE html>
<html>
<title>Schoonmaakrooster ORW21</title>
<head>
    <META
        HTTP-EQUIV="Refresh"
        CONTENT="300; URL=overzicht.php">
    <script src="scripts/week.js"></script>
    <link rel="stylesheet" type="text/css" href="algemeen.css">
    <?php

    include 'verborgen/functies.php';
    include 'favicon.php';
    $conn = connect2DB();

    ?>
</head>

<body>
<div id="main">
    <?php include 'col_rechts.php'; // Rechterkolom

    $namen = NULL;
    $fout_cur = NULL;
    $fout_prev = NULL;
    $boetes = NULL;

    //### HUISGENOTEN
    // Creert een vector met daarin alles huisgenoten
    /*$sql = "SELECT huisnaam FROM odt_users";
    $result = $conn->query($sql);
    $aantal = $result->num_rows;
    while ($naam = $result->fetch_array(MYSQLI_NUM)) {
        $namen[] = $naam[0];
    }*/
    $namen = get_huisgenoten($conn);
    $aantal = count($namen);

    //### BOETEREGELING
    $sql = "SELECT boete FROM odt_boeteregeling ORDER BY aantal ASC";
    $result = $conn->query($sql);
    $boetes[] = 0; // geen boete als je niks verkeerd heb gedaan
    while ($fetch = $result->fetch_array(MYSQLI_NUM)) {
        $boetes[] = $fetch[0];
    }

    //### BOETES (huidige en vorige maand, per persoon en totaal)
    $sum_boetes_month_prev = 0;
    $sum_boetes_month_cur = 0;
    $d1 = date('Y-m-d', strtotime("first day of -1 month"));     // er mag geen overlap in de dagen zijn (BETWEEN is met grenzen inclusief)
    //echo $d1 . '<br/>';
    $d2 = date('Y-m-d', strtotime("last day of -1 month"));      // er mag geen overlap in de dagen zijn (BETWEEN is met grenzen inclusief)
    //echo $d2 . '<br/>';
    $d3 = date('Y-m-d', strtotime("first day of this month"));   // er mag geen overlap in de dagen zijn (BETWEEN is met grenzen inclusief)
    //echo $d3 . '<br/>';
    $d4 = date('Y-m-d', strtotime("-1 day"));                    // Als de einddatum vandaag is, kan deze nog steeds worden afgerond
    //echo $d4 . '<br/>';
    foreach ($namen as $naam) {
        $sql_month_prev = sprintf("SELECT COUNT(*) FROM odt_schoonmaakrooster WHERE persoon = '%s' AND einddatum BETWEEN '$d1' AND '$d2' AND controle = ''", $naam);
        $sql_month_cur = sprintf("SELECT COUNT(*) FROM odt_schoonmaakrooster WHERE persoon = '%s' AND einddatum BETWEEN '$d3' AND '$d4' AND controle = ''", $naam);
        //echo $sql_month_cur;
        $res_prev = $conn->query($sql_month_prev)->fetch_array(MYSQLI_NUM);
        $res_cur = $conn->query($sql_month_cur)->fetch_array(MYSQLI_NUM);
        $fout_prev[] = $res_prev[0];
        $fout_cur[] = $res_cur[0];
        $sum_boetes_month_prev += $boetes[$res_prev[0]];
        $sum_boetes_month_cur += $boetes[$res_cur[0]];
    }
    $classes = ['col1', 'col2'];
    $col1 = ceil($aantal / 2); // bij een oneven aantal huisgenoten komt de grootste kolom links (meeste huisgenoten)
    $col2 = floor($aantal/2);
    $cols = [$col1, $col2]
    ?>

    <div class="block1" id="huisgenoten">
        <h1>Boetes deze maand</h1>
        <?php
        for ($p = 0; $p < 2; $p++) {
            echo sprintf("<div class='%s'><ul>", $classes[$p]);
            for ($i = 0; $i < $cols[$p]; $i++) {
                echo sprintf("<li class='naam_active' id='%s'>%s</li><span class='col_float_r'>€ %s,-</span>", $namen[$i+$p*$col1], $namen[$i+$p*$col1], $boetes[$fout_cur[$i+$p*$col1]]);
            }
            echo "</ul></div>";
        }
        ?>
    </div>
    <div class="block1" id="huisgenoten">
        <h1>Boetes vorige maand</h1>
        <?php
        for ($p = 0; $p < 2; $p++) {
            echo sprintf("<div class='%s'><ul>", $classes[$p]);
            for ($i = 0; $i < $cols[$p]; $i++) {
                echo sprintf("<li class='naam_active' id='%s'>%s</li><span class='col_float_r'>€ %s,-</span>", $namen[$i+$p*$col1], $namen[$i+$p*$col1], $boetes[$fout_prev[$i+$p*$col1]]);
            }
            echo "</ul></div>";
        }
        ?>
    </div>

    <div class="block1" id="gang&vloer">
        <h1 id="gang">Totaal</h1>

        <div class="col1">

            <h2>Vorige maand</h2>
            <?php
            echo '€ ' . $sum_boetes_month_prev . ',-';
            ?>
        </div>
        <div class="col2">
            <h2>Deze maand</h2>
            <?php
            echo '€ ' . $sum_boetes_month_cur . ',-';
            ?>
        </div>
    </div>

</div>

</body>
</html>
