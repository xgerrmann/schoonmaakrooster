<?php

function connect2DB()
{
    $servername = "10.3.1.104";
    $username = "knor_schoonmaken";
    $password = "schoonmaakbaas";
    $database = "knor_schoonmaken";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_errno > 0) {
        die('Unable to connect to database [' . $conn->connect_error . ']');
    }
    return $conn;
}

function get_huisgenoten($conn)
{
    $namen = NULL;
    $naam = NULL;

    $sql = "SELECT huisnaam FROM odt_users ORDER BY hierarchy ASC";
    $result = $conn->query($sql);
    while ($naam = $result->fetch_array(MYSQL_ASSOC)) {
        $namen[] = $naam['huisnaam'];
    }
    return $namen;
}


function check_ingelogd($conn)
{
    //echo 'Enter check_ingelogd <br/>';
    $sql = "SELECT pass, ipv6, tijd, ingelogd, t_end FROM odt_admin_pass LIMIT 1"; // als het goed is toch maar 1 rij
    $res = $conn->query($sql)->fetch_array(MYSQL_ASSOC);
    $logged_in = $res['ingelogd'];
    //echo $sql.'<br/>';
    $thisip = $_SERVER['REMOTE_ADDR'];
    // Eerst tijd checken die verlopen is sinds inloggen
    $maxtime = $res['t_end'];
    $tijd = time();
    if ($logged_in == 1) { // Als er al is ingelogd
        //echo "er is al ingelogd <br/>";
        if ($tijd > $maxtime) {// LOGOUT
            //echo "De tijd is verstreken <br/>";
            //echo "uitloggen<br/>";
            logout($conn);
            return false;
        } elseif ($thisip == $res['ipv6']) {// maximale tijd is nog niet verstreken & IP is het zelfde
            //echo "IP adres is correct <br/>";
            // staat nu nog wel meerdere devices binnen hetzelfde netwerk toe
            return true;
        } else {
            //echo "Nog niet ingelogd<br/>";
            return false;
        }
    } else {
        return false;
    }
}

function login($conn, $pass)
{
    $thisip = $_SERVER['REMOTE_ADDR'];
    $tijd = time();
    $sql = "SELECT pass FROM odt_admin_pass LIMIT 1"; // als het goed is toch maar 1 rij
    $res = $conn->query($sql)->fetch_array(MYSQL_ASSOC);
    $truepass = $res['pass'];
    $hashedpass = md5($pass);
    if ($hashedpass == $truepass) {
        //echo "wachtwoord correct <br/>";
        $maxelapsed = 60 * 5; // Na 5 minuten automatisch uitloggen
        $maxtime = $tijd + $maxelapsed;
        $sql = "UPDATE odt_admin_pass SET ipv6='$thisip', tijd='$tijd', ingelogd='1', t_end='$maxtime'";
        //echo $thisip.'<br/>';
        //echo $sql."<br/>";
        $conn->query($sql);
        return true;
    } else {
        return false;
    }
}

function logout($conn)
{
    //echo "Enter LOGOUT() <br/>";
    $sql = "UPDATE odt_admin_pass SET ipv6='', tijd='', ingelogd='', t_end =''";
    $conn->query($sql);
    //echo 'LOGUIT <br/>';
    //echo $sql.'<br/>';
}

function is_thuis($conn, $week, $jaar, $huisgenoot)
{
//    echo "Functie: is_thuis";
    //echo $week.'<br/>';
    //echo $jaar.'<br/>';
    $datum = date("Y-m-d", strtotime($jaar . "W" . $week));
    //echo $datum.'<br/>';
    $sql = "SELECT * FROM odt_afwezigheid WHERE huisnaam = '$huisgenoot' AND (begin <= '$datum' AND eind >= '$datum')"; // datum ligt in de vakantie
//    echo $sql . "<br/>";
    $res = $conn->query($sql);
    $aantal = $res->num_rows;
    echo "Aantal afwezige weken: " . $aantal . "<br/>";
    if ($aantal != 0) {
        echo "$huisgenoot is NIET thuis<br/>";
        return false;
    } else {
        echo "$huisgenoot is WEL thuis<br/>";
        return true;
    }
}

function wekenNaarDagen($WeekStart, $YearStart, $NumberOfWeeks)
{
    $NumberOfWeeks = $NumberOfWeeks - 1;
    $WeeksThisYear = date("W", mktime(0, 0, 0, 12, 28, $YearStart)); //28th of december is ALWAYS in the last week

    $WeekEnd = weekMod($WeekStart + $NumberOfWeeks );
    $YearEnd = jaarMod($YearStart,$WeekStart + $NumberOfWeeks );

    //echo "Eindweek: $WeekEnd <br/>";
    // echo "Eindjaar: $YearEnd <br/>";

    $DayStart = date("Y-m-d", strtotime($YearStart . 'W' . $WeekStart));
    $DayEnd = date("Y-m-d", strtotime($DayStart . "+$NumberOfWeeks weeks - 1 day"));
    //echo "Dag start: " . $DayStart . "<br/>";
    //echo "Dag eind: " . $DayEnd . "<br/>";
    return array($DayStart, $DayEnd);

}

function isOutside($left, $right, $val)
{
    if (($val < $left) OR (($val > $right))) {
        return true;
    } else {
        return false;
    }
}

function isVakantie($conn, $week, $jaar)
{
    list($dag_start, $dag_eind) = wekenNaarDagen($week, $jaar, 1);
    //echo 'getVakanties';
    $vakanties = get_vakanties($conn, $dag_start, $dag_eind);
    // Berekenen van de eerste dag van de schoonmaakweek
    $dag = date("Y-m-d", strtotime($jaar . 'W' . $week));
    //Controleren op overlap met vakantieweken
    foreach ($vakanties as $vakantie) {
        $check = isOutside($vakantie['begin'], $vakantie['eind'], $dag);
        if (!$check) {//day is inside one of the holidays
            return 1; // gegeven week is een vakantieweek
        }
    }

    return 0; // gegeven week is geen vakantieweek
}

function WekeninJaar($jaar)
{
    return date("W", mktime(0, 0, 0, 12, 28, $jaar)); //28th of december is ALWAYS in the last week
}

function get_vakanties($conn, $dag_start, $dag_eind)
{
    $sql = "SELECT begin, eind FROM odt_vakanties WHERE (eind BETWEEN '$dag_start' AND '$dag_eind') OR (begin BETWEEN '$dag_start' AND '$dag_eind') OR (begin <= '$dag_start' AND eind>='$dag_start')";
    //echo $sql . '<br/>';
    $res = $conn->query($sql);
    $vakanties = NULL;
    while ($fetch = $res->fetch_array()) {
        $vakanties[] = $fetch; // uitgaande van 1 vakantie
        //echo "Begin: ".$vakanties[0]['begin']."Eind: ".$vakanties[0]['eind']."<br/>";
    }
    return $vakanties;
}

function jaarMod($jaar_start, $weken)
{
    $jaar = $jaar_start;
    // Deze functie geeft het jaar terug waarin de gegeven week valt
    // stel als input wordt week 55 gegeven, en het huidige jaar (2015) bevat 52 weken dan geeft hij 2016 terug
    $res = weekMod($weken);
    if ($res == $weken) {
        return $jaar;
    } else {
        return ($jaar + 1);
    }
}

function weekMod($weken)
{
    //$jaar = date('Y');
    $jaar  = date('Y',strtotime('thursday this week'));
    $maxweken = WekeninJaar($jaar);
    return ($weken - 1) % $maxweken + 1;
}

function isIngedeeld($conn,$week,$jaar){
    $sql = "SELECT * FROM odt_schoonmaakrooster WHERE jaar = '$jaar' AND week = '$week' LIMIT 1";
    //echo $sql.'<br/>';
    $result = $conn->query($sql)->num_rows;
    if($result>0) return 1;
    else return 0;
}

function mededeling($conn,$string){
    $timestamp = date('Y-m-d G:i:s');
    $sql    = sprintf("INSERT INTO odt_wijzigingen (informatie,datum) VALUES ('%s','%s')", $string,$timestamp);
    $conn->query($sql);
}

function mail_huisgenoten($conn){
## haal de taken op die bij de categorieen horen TODO: maak hier een functie van
    $sql = "SELECT * FROM odt_categorieen";
    //echo $sql.'<br/>';
    $res = $conn->query($sql);
    $taken = NULL;
    while ($fetch = $res->fetch_array(MYSQLI_ASSOC)) {
        $categorie = $fetch['categorie'];
        $taak = $fetch['taak'];
        $cat2taak["$categorie"] = $taak;
    }

    $huisgenoten = get_huisgenoten($conn);
    $boetelijst = get_boetes($conn, $huisgenoten);
    // TODO: restore after debugging:
    foreach ($huisgenoten as $huisgenoot) {

        //$to = 'xander@gerrmann.nl'; //TODO
        $to = get_email($conn, $huisgenoot); // TODO
        setlocale(LC_ALL, 'nl_NL');
        $maand = ucfirst(strftime('%B', time()));
        $jaar = date('Y',strtotime('thursday this week'));
        $subject = 'Schoonmaakrooster ' . $maand.' - '.$jaar;
        $message = create_message($conn, $huisgenoten, $huisgenoot, $cat2taak, $boetelijst);
        $headers = "From : schoonmaakrooster@reetro21.nl\r\n";
        $headers = "Reply-To : no-reply@reetro21.nl\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        mail($to, $subject, $message, $headers);
    }
}

function get_email($conn, $huisgenoot)
{
    $sql = "SELECT email FROM odt_users WHERE huisnaam ='$huisgenoot'";
    $res = $conn->query($sql);
    $fetch = $res->fetch_array(MYSQLI_ASSOC);
    return $fetch['email'];
}

function create_message($conn, $huisgenoten, $huisgenoot, $cat2taak, $boetelijst)
{
    $week_start = weekMod(date('W')+1)-1;
    $week_end = $week_start + 5;
    $jaar = date('Y');
    //#### Begin van het bericht ###
    setlocale(LC_ALL, 'nl_NL');
    $maand = ucfirst(strftime('%B', time()));
    $message = '
    <html>
    <head>
    <title> Rooster voor ' . $maand . '</title>
    </head>
    <body>
    <p>Beste ' . $huisgenoot . ', <br/> <br/>
    Bij dezen jouw gepersonaliseerde schoonmaakrooster voor <b>' . $maand . '</b>:</p>
    ';
    $week_end = $week_end + 1; ## voor als een taak begint in een maand en eindigt in een andere maand
    $sql = "SELECT * FROM odt_schoonmaakrooster WHERE week >= '$week_start' AND week <= '$week_end' AND jaar >= $jaar AND persoon = '$huisgenoot'";
    echo $sql."<br/>";
    $res = $conn->query($sql);
    $aantal = $res->num_rows;
    $message .= '
<table style="border:1px solid">
                <tr>
                <th style="width: 50px; text-align: left;">Week</th>
                <th style="width: 200px; text-align: left;">Ingangsdatum:</th>
                <th style="width: 150px; text-align: left;">Taak</th>
                <th style="width: 50px; text-align: left; ">Dagen</th>
                </tr>
                ';
    //### Vul de tabel met de schoonmaaktaken
    for ($i = 0; $i < $aantal; $i++) {
        setlocale(LC_ALL, 'nl_NL');
        $taak = $res->fetch_array();
        $categorie = $taak['categorie'];
        $time_start = strtotime($taak['ingangsdatum']);
        $time_end = strtotime($taak['einddatum']);
        $day_start = strftime('%a', $time_start);
        $day_end = strftime('%a', $time_end);
        $jaar = $taak['jaar'];
        $week = $taak['week'];
        $weekstart = strtotime($jaar . 'W' . $week);
        $ingangsdatum = date('d-m-Y', $time_start);
        $message .= '
        <tr><td style="padding-left:10px;">' . $week . '</td>
        <td style="padding-left:10px;">' . $ingangsdatum . '</td>
        <td style="padding-left:10px;">' . $cat2taak["$categorie"] . '</td>
        <td style="padding-left:10px;">' . $day_start . '-' . $day_end . '</td></tr>
        ';
    }
    $message .= '</table>';
    // ### BOETETABEL
    $message .= '<p>De volgende boetes zijn het resultaat van de afgelopen maand:</p>
<table style="border:1px solid">
<tr><th style="width: 80px; text-align: left;">Naam</th><th style="width: 50px; text-align: left;">Boete</th></tr>';
    foreach ($huisgenoten as $naam) {
        $message .= '
    <tr><td style="padding-left:10px;">' . $naam . '</td><td style="padding-left:10px;">€ ' . $boetelijst["$naam"] . '</td></tr>
    ';
    }

    $message .= '
    </table>
    <p>Succes met schoonmaken!<br/><br/>
    Groeten, Knor</p>
    </body>
    </html>
    ';
    //echo $message;
    return $message;
}

function get_boetes($conn, $huisgenoten)
{
    //### BOETEREGELING
    $sql = "SELECT boete FROM odt_boeteregeling ORDER BY aantal ASC";
    $result = $conn->query($sql);
    $boetes[] = 0; // geen boete als je niks verkeerd heb gedaan
    while ($fetch = $result->fetch_array(MYSQLI_ASSOC)) {
        $boetes[] = $fetch['boete'];
    }
    // ## boetes per persoon
    //$d1 = date('Y-m-d', strtotime("first day of -1 month"));     // er mag geen overlap in de dagen zijn (BETWEEN is met grenzen inclusief)
    //$d2 = date('Y-m-d', strtotime("last day of -1 month"));      // er mag geen overlap in de dagen zijn (BETWEEN is met grenzen inclusief)
    $d1 = date('Y-m-d', strtotime("first day of -1 month"));     // er mag geen overlap in de dagen zijn (BETWEEN is met grenzen inclusief)
    $d2 = date('Y-m-d', strtotime("last day of -1 month"));      // er mag geen overlap in de dagen zijn (BETWEEN is met grenzen inclusief)
    foreach ($huisgenoten as $huisgenoot) {
        $sql= sprintf("SELECT COUNT(*) FROM odt_schoonmaakrooster WHERE persoon = '%s' AND einddatum BETWEEN '$d1' AND '$d2' AND controle = ''", $huisgenoot);
        echo $sql."<br/>";
        $res_prev = $conn->query($sql)->fetch_array(MYSQLI_NUM);
        $boetelijst["$huisgenoot"] = $boetes[$res_prev[0]];
        echo $boetes[$res_prev[0]]."<br/>";
    }
    return $boetelijst;
}

function cat2taak($conn){
    ## haal de taken op die bij de categorieen horen
    $sql = "SELECT * FROM odt_categorieen";
    //echo $sql.'<br/>';
    $res = $conn->query($sql);
    $taken = NULL;
    $cat2taak = NULL;
    while ($fetch = $res->fetch_array(MYSQLI_ASSOC)) {
        $categorie = $fetch['categorie'];
        $taak = $fetch['taak'];
        $cat2taak["$categorie"] = $taak;
    }
    return $cat2taak;
}