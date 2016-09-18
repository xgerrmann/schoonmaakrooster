<!DOCTYPE html>
<html>
<title>Schoonmaakrooster ORW21</title>
<head>
    <META
        HTTP-EQUIV="Refresh"
        CONTENT="1; URL=overzicht.php">

    <link rel="stylesheet" type="text/css" href="algemeen.css">
    <?php

    include 'verborgen/functies.php';
    include 'favicon.php';
    $conn = connect2DB();
    logout($conn);

    ?>
</head>

<body>
<div id="main">
    <?php include 'col_rechts.php'; ?>

    <div class="block1" id="loguit">
        <h1>Je bent uitgelogd</h1>
    </div>

</div>

</body>
</html>