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

    $persoon        = $_POST['naam_selected'];
    $pers_wissel    = $_POST['pers_wissel'];
    $bool_wissel    = $_POST['bool_wissel'];
    $id_wissel      = $_POST['id_wissel'];

    ?>

    <div class="block1" id="kalender">
        <h1 id="persoon"><?php if($bool_wissel){echo "Wissel met: ";}echo $persoon; ?></h1>
        <table>
            <tr>
                <th>ma</th>
                <th>di</th>
                <th>wo</th>
                <th>do</th>
                <th>vr</th>
                <th>za</th>
                <th>zo</th>
            </tr>
            <?php
            $dagvandeweek = date('N'); //Dag van de week/*
            $verschil = $dagvandeweek - 1; //Verschil van vandaag met afgelopen maandag;
            $offset = [-$verschil, -$verschil + 1, -$verschil + 2, -$verschil + 3, -$verschil + 4, -$verschil + 5, -$verschil + 6, -$verschil + 7];
            //echo $dagvandeweek."<br/>";
            //echo date("H:i",strtotime('now'));
            $dag_nummer = NULL;
            $dag_lang   = NULL;
            for ($i = 0; $i < 5; $i++) {
                for($j=0;$j<7;$j++){
                    $dag_nummer[$j] = date('d', strtotime(" today  $offset[$j] day + $i week"));
                    $dagen_lang[$j] = date('Y-m-d', strtotime(" today  $offset[$j] day + $i week"));
                }

                //week van de eerste maandag
                $week = date('W') + $i;
                //echo $week;

                $sql = "SELECT id, categorie, ingangsdatum, einddatum FROM odt_schoonmaakrooster WHERE persoon = '$persoon' AND week = '$week'";
                //echo $sql;
                $beurten = NULL;
                $res = $conn->query($sql);
                //$ntal = $res->num_rows;
                //echo $ntal;
                while ($fetch = $res->fetch_array(MYSQLI_ASSOC)) {
                    $beurten[] = $fetch;
                    //echo "week: ".$week." - ".$fetch['categorie']."<br/>";
                }

                $categorieen    = array_column($beurten, 'categorie');
                $ingangsdata    = array_column($beurten, 'ingangsdatum');
                $einddata       = array_column($beurten, 'einddatum');
                $classes        = NULL;

                //echo "Ingangsdatum: ".$ingangsdatum.'<br/>';
                //echo "Datum: ".$maa.'<br/>';
                //echo "Categorie: ".$categorie.'<br/>';
                //echo (($ingangsdatum>=$dagen[3])&&($einddatum<=$dagen[5]));

                $aantal_beurten_in_week = count($categorieen);

                // TODO: Deze troep hieronder wat netter coderen, want dit slaat helemaal nergens op
                echo "<tr id='" . $week . "'>";
                for ($t = 0; $t < 7; $t++) {
                    for ($p = 0; $p < $aantal_beurten_in_week; $p++) {
                        if (($dagen_lang[$t] >= $ingangsdata[$p]) && ($dagen_lang[$t] <= $einddata[$p])) {
                            $classes[] = 'active';
                            //echo "active.<br/>";
                            break;    // checkt voor elke beurt of deze van toepassing is op de huidige dag.
                            // Zo ja, dan moet er door worden gegaan naar de volgende dag, zo nee, dan moet de class van de dag 'inactive' zijn
                        }
                    }
                    if (!(($dagen_lang[$t] >= $ingangsdata[$p]) && ($dagen_lang[$t] <= $einddata[$p]))) {
                        // dit is de else clausule voor de if in de for-loop.
                        $classes[] = 'inactive';
                    }

                    echo sprintf("<td class='%s' data-day='%s' onmouseover=\"show_info(this",$classes[$t], $dag_nummer[$t]);
                    foreach($beurten as $beurt){
                        if(($dagen_lang[$t] >=$beurt['ingangsdatum'])&&($dagen_lang[$t]<=$beurt['einddatum'])){
                            echo ",'".$beurt['categorie']."'";
                        }
                    }
                    echo " )\" data-cat='";
                    foreach($beurten as $beurt){
                        if(($dagen_lang[$t] >=$beurt['ingangsdatum'])&&($dagen_lang[$t]<=$beurt['einddatum'])){
                            echo $beurt['categorie'];
                            if ($beurt != end($beurten)) {
                                echo ',';
                            }
                        }
                    }
                    echo "'";
                    echo " data-id='";
                    foreach($beurten as $beurt){
                        if(($dagen_lang[$t] >=$beurt['ingangsdatum'])&&($dagen_lang[$t]<=$beurt['einddatum'])){
                            echo $beurt['id'];
                            if ($beurt != end($beurten)) {
                                echo ',';
                            }
                        }
                    }
                    echo "'";
                    echo "onmouseleave=\"hide_info(this)\"";
                    if($classes[$t] == 'active') {
                        echo " onclick=\"wissel(this,'$bool_wissel','$id_wissel','$pers_wissel','$persoon')\"";
                    }
                    echo sprintf(">%s</td>", $dag_nummer[$t]);
                }
                echo "<tr>";
            }
            $conn->close();
            ?>
        </table>
    </div>
</div>

</body>
</html>