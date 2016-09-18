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
            $week = date("W");
            $week = date("Y");
            $id = $_POST['id'];
            $sql = "SELECT huisnaam FROM odt_users WHERE id = '$id'";
            $huisgenoot = $conn->query($sql)->fetch_array();
            $huisnaam = $huisgenoot['huisnaam'];
            $sql = "DELETE FROM odt_users WHERE id = '$id'";
            //echo "id = $id<br/>";
            $conn->query($sql);
            $sql1 = "DELETE FROM odt_schoonmaakpouleAB WHERE huisnaam = '$huisnaam' AND week>='$week' AND jaar = '$jaar'";
            $sql2 = "DELETE FROM odt_schoonmaakpouleAB WHERE huisnaam = '$huisnaam' AND jaar>'$jaar'";
            $sql3 = "DELETE FROM odt_schoonmaakpouleCD WHERE huisnaam = '$huisnaam' AND week>='$week' AND jaar = '$jaar'";
            $sql4 = "DELETE FROM odt_schoonmaakpouleCD WHERE huisnaam = '$huisnaam' AND jaar > '$jaar'";
            $conn->query($sql1);
            $conn->query($sql2);
            $conn->query($sql3);
            $conn->query($sql4);
        }
        elseif ($add) {
            echo "add: $add<br/>";
            echo "Add<br/>";
            $voornaam_lower = mb_strtolower($_POST['voornaam']);
            $voornaam = ucfirst($voornaam_lower);
            echo "Voornaam: $voornaam <br/>";
            $achternaam_los = explode(" ",mb_strtolower($_POST['achternaam']));
            $achternaam_laatste = ucfirst(end($achternaam_los));
            array_pop($achternaam_los); // verwijdert het laatste gedeelte van de achternaam
            $achternaam_los[] = $achternaam_laatste; // voegt het laatste gedeelte weer toe met een hoofdletter
            $achternaam = implode(" ",$achternaam_los);
            echo "Achternaam: $achternaam <br/>";
            $entrydate = $_POST['entrydate'];
            $huisnaam_lower = mb_strtolower($_POST['huisnaam']);
            $huisnaam = ucfirst($huisnaam_lower);
            $sql = "INSERT INTO odt_users VALUES ('','$voornaam','$achternaam','$entrydate','$huisnaam','','')";
            echo "Query: $sql <br/>";
            $conn->query($sql);
        }
        // Hierarchy update
        $sql = "SELECT id FROM odt_users ORDER BY entrydate ASC";
        $res = $conn->query($sql);
        $i=0;
        while($fetch = $res->fetch_array(MYSQLI_ASSOC)){
            $i++;
            $id = $fetch['id'];
            $sql = "UPDATE odt_users SET hierarchy = '$i' WHERE id = '$id'";
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