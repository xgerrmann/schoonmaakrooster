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

    //TODO: checken op herverzending, bijvoorbeeld bij refreshen.//
    // Wissel mag maar een keer worden doorgevoerd, misschien handig om ook de namen te checken.
    $id1    = $_POST['id1'];
    $id2    = $_POST['id2'];
    $pers1  = $_POST['pers_wissel1'];
    $pers2  = $_POST['pers_wissel2'];

    //echo "id1:".$id1."<br/>";
    //echo "id1:".$id2."<br/>";
    //echo "pers1:".$pers1."<br/>";
    //echo "pers2:".$pers2."<br/>";
    // Alleen namen wisselen;
    $sql    = "SELECT persoon FROM odt_schoonmaakrooster WHERE id = '$id1' OR id = '$id2' ORDER BY id ASC";
    $res    = $conn->query($sql);

    // Zorgt ervoor dat de ids behoren bij de juiste persoon
    if($id1<$id2){
        $pers1  = $res->fetch_array(MYSQL_ASSOC);
        $pers2  = $res->fetch_array(MYSQL_ASSOC);
    }
    else{ //id2 -> persoon1
        $pers2  = $res->fetch_array(MYSQL_ASSOC);
        $pers1  = $res->fetch_array(MYSQL_ASSOC);
    }

    // UPDATE schoonmaakrooster
    $sql1    = "UPDATE odt_schoonmaakrooster SET persoon = '".$pers2['persoon']."' WHERE id = '$id1'";
    $sql2    = "UPDATE odt_schoonmaakrooster SET persoon = '".$pers1['persoon']."' WHERE id = '$id2'";
    $conn->query($sql1);
    $conn->query($sql2);
    //echo "Update in odt_schoonmaakrooster voltooid <br/>";

    // INSERT INTO tabel met wijzigingen
    $timestamp = date('Y-m-d G:i:s');
    $string = sprintf('%s heeft een taak gewisseld met %s',$pers1['persoon'], $pers2['persoon']);
    $sql    = sprintf("INSERT INTO odt_wijzigingen (informatie,datum) VALUES ('%s','%s')", $string,$timestamp);
    $conn->query($sql);

    //echo "Insertion in odt_wijzigingen voltooid <br/>";
    ?>
</head>

<body>
<div id="main">
    <?php include 'col_rechts.php'; ?>

    <div class="block1" id="loguit">
        <h1>Wissel voltooid</h1>
    </div>

</div>

</body>
</html>