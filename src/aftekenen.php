<?php

include 'verborgen/functies.php';
include 'favicon.php';
$conn = connect2DB();

$id             = $_POST['id'];
$schoonmaker    = $_POST['schoonmaker'];
$controleur     = $_POST['controleur'];
$taak           = $_POST['taak'];

$week = date("W");

$timestamp = date('Y-m-d G:i:s');

// Verwerk in schoonmaakrooster
$sql = sprintf("UPDATE odt_schoonmaakrooster SET datum='%s',taak='%s',controle='%s'  WHERE id='%s'", $timestamp, $taak, $controleur, $id);
$conn->query($sql);

// Verwerk in tabel met wijzigingen
$string = sprintf('%s is afgetekend door %s voor de %s',$schoonmaker,$controleur,$taak);
mededeling($conn,$string);

?>

<!DOCTYPE html>
<html>
<title>Schoonmaakrooster ORW21</title>
<head>
    <META
        HTTP-EQUIV="Refresh"
        CONTENT="1; URL=overzicht.php">

    <script src="scripts/week.js"></script>
    <link rel="stylesheet" type="text/css" href="algemeen.css">

</head>

<body>
<div id="main">
    <?php include 'col_rechts.php'; ?>

    <div class="block1" id="loguit">
        <h1><?php echo $schoonmaker; ?> is nu afgetekend</h1>
    </div>
</div>

</body>
</html>
