<?php
/**
 * Created by PhpStorm.
 * User: Xander
 * Date: 15-7-2015
 * Time: 16:32
 * Verwerkt de wijzigingen in de vakanties van instellingen.php
 */

include 'verborgen/functies.php';
include 'favicon.php';
$conn = connect2DB();
if (check_ingelogd($conn)) {
    echo "Enter if 1 <br/>";
    $delete = (bool) $_POST['delete'];
    $add    = (bool) $_POST['add'];
    echo "delete: $delete<br/>";
    echo "add: $add<br/>";
    if ($delete or $add) {
        echo "Enter if 2<br/>";
        if ($delete) {
            echo "delete: $delete<br/>";
            echo "Delete<br/>";
            $id = $_POST['id'];
            $sql = "DELETE FROM odt_afwezigheid WHERE id = '$id'";
            //echo "id = $id<br/>";
            $conn->query($sql);
        }
        elseif ($add) {
            echo "add: $add<br/>";
            echo "Add<br/>";
            $voornaam_lower = mb_strtolower($_POST['huisnaam']);
            $voornaam = ucfirst($voornaam_lower);
            $van = $_POST['van'];
            $tot = $_POST['tot'];
            $sql = "INSERT INTO odt_afwezigheid VALUES ('','$voornaam','$van','$tot')";
            $conn->query($sql);
        }

    }
    else{
        echo "FOUT!!";
    }

}
$conn->close();
###### Ga terug naar instellingen pagina
header("location: instellingen.php");
exit();