<!DOCTYPE html>
<html>
<title>Schoonmaakrooster ORW21</title>
<head>
    <META
        HTTP-EQUIV="Refresh"
        CONTENT="300; URL=overzicht.php">
    <script src="scripts/selectie.js"></script>
    <link rel="stylesheet" type="text/css" href="algemeen.css">
    <link rel="stylesheet" type="text/css" href="week.css">
    <?php

    include 'verborgen/functies.php';
    $conn = connect2DB();
    include 'favicon.php';

    ?>
</head>

<body>
<div id="main">
    <?php include 'col_rechts.php';

    $week = date("W");
    $id = $_POST['id'];
    $schoonmaker = $_POST['schoonmaker'];
    $categorie = $_POST['categorie'];

    //## 1: Controleert op reeds gedane taken en laat deze niet zien
    $week = date("W");
    $jaar = date("Y");
    $sql = "SELECT taak FROM odt_schoonmaakrooster WHERE categorie = '$categorie' AND controle != '' AND week = '$week' AND jaar = '$jaar' ORDER BY categorie ASC";
    $result = $conn->query($sql);
    $voltooid = $result->fetch_array(MYSQL_NUM);


    if ($categorie == 'A1' || $categorie == 'A2') {
        $func1 = 'Keuken';
        $func2 = 'GR';
    } else if ($categorie == 'B1' || $categorie == 'B2') {
        $func1 = 'Douche';
        $func2 = 'Toiletten';
    } else if ($categorie == 'C' || $categorie == 'D') {
        $func1 = 'Gang';
        $func2 = 'Vloer';
    } else {
        echo "FOUT!!, verkeerder categorie meegekregen";
    }
    if ($func1 == $voltooid[0]) {
        $func1 = $func2;
        $func2 = '';
    } elseif ($func2 == $voltooid[0]) {
        $func2 = '';
    }


    $namen = get_huisgenoten($conn);
    $conn->close();
    ?>


    <div class="block1" id="taakkeuze"
        <?php if ($categorie == 'C' || $categorie == 'D') {
            echo "style='display:none;'";
        } ?>>
        <h1>Taak</h1>

        <div class="col1">
            <ul>
                <li class="naam" onclick="select_taak(this);"
                    id="taak1"><?php echo $func1; ?></li>
            </ul>
        </div>
        <div class="col2">
            <ul>
                <li class="naam" onclick="select_taak(this);"
                    id="taak2"><?php echo $func2; ?></li>
            </ul>
        </div>

    </div>

    <div class="block1" id="huisgenoten"
        <?php if ($categorie == 'C' || $categorie == 'D') {
            echo "style='display:block;'";
        } ?>>
        <h1>Controle door:</h1>

        <div class="col1">
            <ul>
                <?php
                $aantal = count($namen);
                $col = (int)ceil($aantal / 2);
                for ($i = 0; $i < $col; $i++) {
                    if($namen[$i]==$schoonmaker) {
                        echo sprintf("<li class='naam_inactive'>%s</li>", $namen[$i]);
                    }
                    else{
                        echo sprintf("<li class='naam' onclick=\"tekenAf('%s','%s','%s','%s');\">%s</li>",
                            $id, $schoonmaker, $categorie, $namen[$i], $namen[$i]);
                    }
                }
                ?>
            </ul>
        </div>
        <div class="col2">
            <ul>
                <?php
                $col = (int)floor($aantal / 2);
                for ($i = $col; $i < $aantal; $i++) {
                    if($namen[$i]==$schoonmaker) {
                        echo sprintf("<li class='naam_inactive'>%s</li>", $namen[$i]);
                    }
                    else{
                        echo sprintf("<li class='naam' onclick=\"tekenAf('%s','%s','%s','%s');\">%s</li>",
                            $id, $schoonmaker, $categorie, $namen[$i], $namen[$i]);
                    }
                }
                ?>
            </ul>
        </div>

    </div>
</div>

</body>
</html><?php
