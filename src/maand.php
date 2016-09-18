<!DOCTYPE html>
<html>
<title>Schoonmaakrooster ORW21</title>
<head>
    <META
        HTTP-EQUIV="Refresh"
        CONTENT="300; URL=overzicht.php">
    <script src="scripts/maand.js"></script>
    <link rel="stylesheet" type="text/css" href="algemeen.css">
    <link rel="stylesheet" type="text/css" href="maand.css">

    <?php

    include 'verborgen/functies.php';
    $conn = connect2DB();

    include 'favicon.php';

    ?>
</head>

<body>
<div id="main">
    <?php include 'col_rechts.php';

    $bool_wissel = $_POST['bool_wissel'];
    if ($bool_wissel == true) { // werkt
        $pers_wissel = $_POST['pers_selected'];
        $id_wissel = $_POST['id_selected'];
        //echo $id_wissel;
        //echo $pers_wissel;
    } else {
        $bool_wissel = false;
        $id_wissel = '';
        $pers_wissel = '';
    }

    $namen = get_huisgenoten($conn);
    $conn->close();
    ?>
    <div class="block1" id="huisgenoten">
        <h1>
            <?php if (!$bool_wissel) {//$wissel is true of false // werkt
                echo "Kies een persoon";
            } else {
                echo "Wissel met";
            }
            ?>
        </h1>
        <?php
        for ($p = 0; $p < 2; $p++) {
            $aantal = count($namen);
            $classes = ['col1', 'col2'];
            $col1 = ceil($aantal / 2); // bij een oneven aantal huisgenoten komt de grootste kolom links (meeste huisgenoten)
            $col2 = floor($aantal / 2);
            $cols = [$col1, $col2];
            echo sprintf("<div class='%s'><ul>", $classes[$p]);
            for ($i = 0; $i < $cols[$p]; $i++) {
                if ($namen[$i +$p*$col1] == $pers_wissel) {
                    echo sprintf("<li class='naam_inactive'>%s</li>", $namen[$i +$p*$col1]);
                } else {
                    echo sprintf("<li class='naam' onclick=\"toon_agenda('%s','%s','%s','%s');\" id='%s'>%s</li>", $namen[$i+$p*$col1], $bool_wissel, $id_wissel, $pers_wissel, $namen[$i+$p*$col1], $namen[$i+$p*$col1]);
                }
            }
            echo "</ul></div>";
        }
        ?>
        <!--
        <div class="col1">
            <ul>
                <?php /*
        $aantal = count($namen);
        //echo $aantal;
        $col = (int)ceil($aantal / 2);
        $classes = ['col1', 'col2'];
        $col1 = ceil($aantal / 2); // bij een oneven aantal huisgenoten komt de grootste kolom links (meeste huisgenoten)
        $col2 = floor($aantal/2);
        $cols = [$col1, $col2];
        for ($i = 0; $i < $col; $i++) {
            if ($namen[$i] == $pers_wissel) {
                echo sprintf("<li class='naam_inactive'>%s</li>", $namen[$i]);
            } else {
                echo sprintf("<li class='naam' onclick=\"toon_agenda('%s','%s','%s','%s');\" id='%s'>%s</li>", $namen[$i], $bool_wissel, $id_wissel, $pers_wissel, $namen[$i], $namen[$i]);
            }
        }*/
        ?>
            </ul>
        </div>
        -->
        <!--<div class="col2">
            <ul>
                <?php /*
                $col = (int)floor($aantal / 2);
                for ($i = $col; $i < $aantal; $i++) {
                    if ($namen[$i] == $pers_wissel) {
                        echo sprintf("<li class='naam_inactive'>%s</li>", $namen[$i]);
                    } else {
                        echo sprintf("<li class='naam' onclick=\"toon_agenda('%s','%s','%s','%s');\" id='%s'>%s</li>", $namen[$i], $bool_wissel, $id_wissel, $pers_wissel, $namen[$i], $namen[$i]);
                    }
                }*/
        ?>
            </ul>
        </div>-->

    </div>

</div>

</body>
</html>