<?php
/**
 * Created by PhpStorm.
 * User: xander
 * Date: 10-10-2015
 * Time: 09:23
 */

include ('../verborgen/functies.php');
$conn = connect2DB();

$datum = date('Y-m-d', time());
## haal de personen op die moeten schoonmaken vanaf vandaag
//$datum = '2015-10-08'; //TODO: dit weghalen
$sql = "SELECT * FROM odt_schoonmaakrooster WHERE ingangsdatum= '$datum'";
echo $sql . '<br/>';
$res = $conn->query($sql);

$taken = cat2taak($conn);

while ($fetch = $res->fetch_array(MYSQLI_ASSOC)) {
    echo 'Enter while loop<br/>';
    $huisgenoot = $fetch['persoon'];
    echo "Persoon: $huisgenoot <br/>";
    $categorie = $fetch['categorie'];
    echo "Categorie: $categorie <br/>";
    $ingangsdatum = $fetch['ingangsdatum'];
    echo "Ingangsdatum: $ingangsdatum <br/>";
    $einddatum = $fetch['einddatum'];
    echo "Einddatum: $einddatum <br/>";
    $taak = $taken["$categorie"];
    echo "Taak: $taak <br/>";

    // TODO: restore after debugging:
    //$to = 'xander@gerrmann.nl'; //TODO
    $to = get_email($conn, $huisgenoot); // TODO
    setlocale(LC_ALL, 'nl_NL');
    $maand = ucfirst(strftime('%B', time()));
    $jaar = date('Y');
    $subject = "Herinnering van de schoonmaak taak voor $ingangsdatum  - $einddatum";
    $message = create_reminder($conn, $huisgenoot, $taak,$ingangsdatum,$einddatum);
    $headers = "From : schoonmaakrooster@reetro21.nl\r\n";
    $headers = "Reply-To : no-reply@reetro21.nl\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    echo $message;
    mail($to, $subject, $message, $headers);
}


function create_reminder($conn, $huisgenoot, $taak,$ingangsdatum,$einddatum)
{
    $message = '
    <html>
    <head>
    <title> Herinnering van de schoonmaak taak voor ' . $ingangsdatum . ' - '.$einddatum. '</title>
    </head>
    <body>
    <p>Beste ' . $huisgenoot . ', <br/> <br/>
    Graag wil ik jou herinneren aan de schoonmaaktaak van ' . $ingangsdatum . ' tot '.$einddatum. '.<br/>
    Je staat dan ingedeeld om de '.$taak.' schoon te maken. <br/>
    </p>


    <p>Succes met schoonmaken!<br/><br/>
    Groeten, Knor</p>

    <p>
    Deze mail niet meer ontvangen? <br/>
    Hier komt ooit nog een linkje ;)
    </p>
    </body>
    </html>
    ';
    return $message;
}