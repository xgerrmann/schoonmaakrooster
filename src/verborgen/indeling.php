<?php

function indeling($conn, $week_start, $jaar_start, $aantalweken)
{
    echo "Enter indeling()";
    #### OUTPUT UIT
    //ob_start();

    for ($teller = 0; $teller < $aantalweken; $teller++) {
        // Berekenen van de eerste dag van de schoonmaakweek
        $week = weekMod($week_start + $teller);
        $jaar = jaarMod($jaar_start, $week_start + $teller);
        //Controleren op overlap met vakantieweken
        if (!isVakantie($conn, $week, $jaar)) {
            echo "  <br/>   ##################  <br/>
                    <b>     Week: $week         </b><br/>
                            ##################  <br/><br/>";
            deel_in($conn, $week, $jaar);
            $mededeling = "Week $week is ingedeeld";
            mededeling($conn, $mededeling);
        } else {
            // doe niks, want dit is een vakantieweek
            echo "  <br/>   ##################      <br/>
                    <b>     Week: $week, Vakantie:  </b><br/>
                            ##################      <br/><br/>";
        }

    }

## OUTPUT weer AAN
    //ob_end_clean();
}

// TODO: mededeling dat er een week is ingedeeld (per week)
function deel_in($conn, $week, $jaar)
{
    echo "Start: deel_in()<br/>";
## STAPPEN:
//    Bij de eerste keer runnen zijn poule A,B,C & D leeg, bij de volgende keren zijn ze gedeeltelijk gevuld. De poules moeten daarom eerst gevuld worden, waarna er uit getrokken kan worden.
//1.	Haal alle huisgenoten op uit de tabel met huisgenoten
//2.	Check hoe vol de poules// zoek dan naar de laatste poule (er kan een vakantie tussen zitten)
//3.	Haal de laatste persoon uit de poules
//4.	Creer een vector met het aantal huisgenoten dat ontbreekt in de poule, begin bij de persoon die volgt op de laatste persoon uit stap 2.
//6.	Update de tabellen op de server

//Nu volgt de indeling voor de taken C&D en A&B, deze gaat als volgt (begin met poule C&D)
//8.	Pak de eerste persoon uit de betreffende poule en voer de stappen 9 t/m 12 uit totdat je uit elke poule genoeg personen hebt gehaald.
//9.	Check of de persoon thuis is
//10.	Check voor of de persoon niet reeds is geselecteerd voor een andere taak
//11.	2x JA:	sla de naam op die bij de persoon hoort en haal hem uit de tabel ga verder naar punt 13.
//12.	1x of 2x NEE: pak de eerstvolgende persoon uit de poule en ga naar punt 9.

//Nu volgt de indeling voor de taken A&B, deze gaat als volgt:
//13.	Wijs de personen de volgende taken toe: [C,D,A1,A1,A2,A2,B1,B1,B2,B2]
//Nu de indeling is gemaakt dient deze te worden opgeslagen.
//14.	Schrijf de personen met de bijbehorende taken weg in het schoonmaakrooster.

    #### Variabelen (die moeten worden gereset);

    $pouleA = NULL;
    $pouleB = NULL;
    $pouleC = NULL;
    $pouleD = NULL;
    $huisgenoten = NULL;
    $aanvulling = NULL;
    $categorieen = NULL;
    $schoonmakers = NULL;
    $poule = NULL;
## 1
    $huisgenoten = get_huisgenoten($conn);
    $aantalhuisgenoten = count($huisgenoten);

## 2
    $poules = ["A","B","C","D"];

    foreach ($poules as $poulenaam) {
        echo "------------------------- <br/>";
        echo "Loop for poule $poulenaam <br/>";
        //Vind de vorige poule met een weeknummer lager dan die van de huidige week (soms is de poule voor de huidige week al berekend en moet deze worden herberekend)
        //Jaar gelijk aan dit jaar of daarvoor, dit is belangrijk voor rondom jaarwisselingen: dan wordt er gekeken naar het huidige jaar als het volgende jaar al gedeeltelijk is ingedeeld
        //Sorteren op week en jaar, want alleen sorteren op weeknummer levert de week van het verkeerde jaar op.
        $sql = "SELECT week, jaar FROM odt_schoonmaakpoule" . $poulenaam . " WHERE jaar<='$jaar' ORDER BY jaar DESC, week DESC LIMIT 1";
        $res = $conn->query($sql);
        echo $sql . "<br/>";
        if ($res->num_rows != 0) {
            echo "Schoonmaakpoules zijn NIET leeg <br/>";
            $fetch = $res->fetch_array(MYSQL_ASSOC);
            $week_prev = $fetch['week']; // bij opeenvolgende weken is dit de voorafgaande week. In geval van een vakantie is dit de week voor de vakantie
            $jaar_prev = $fetch['jaar']; // bij opeenvolgende weken is dit de voorafgaande week. In geval van een vakantie is dit de week voor de vakantie
        } else {
            echo "Schoonmaakpoules zijn leeg <br/>";
            $week_prev = 0;
            $jaar_prev = 0;
        }
        echo "Voorafgaande week: $week_prev<br/>";

        $sql = "SELECT huisnaam from odt_schoonmaakpoule" . $poulenaam . " WHERE week = '$week_prev' and jaar='$jaar_prev' ORDER BY id ASC"; // Haal de poule op van de betreffende week
        echo $sql . "<br/>";
        $res = $conn->query($sql);
        $aantal = $res->num_rows;
        echo "Aantal in poule " . $poulenaam . " :" . $aantal . "<br/>";

## 3    // Haalt de huidige poule op en slaat deze intern op in een array
        echo "Poule $poulenaam: <br/>";
        while ($fetch = $res->fetch_array(MYSQLI_ASSOC)) {
            $poule[$poulenaam][] = $fetch['huisnaam'];
            echo $fetch['huisnaam']."<br/>";
        }

        $laatste = end($poule[$poulenaam]);
        echo "Laatste: " . $laatste . "<br/>";

## 4
        $index_nul = array_search($laatste, $huisgenoten);
        if ($index_nul == false) {
            $index_nul = -1;
        }
        echo 'Index: ' . $index_nul . '<br/>';

        $tekort = $aantalhuisgenoten - $aantal;
        echo "Aanvulling aan schoonmaakpoule:" . $tekort . "<br/>";
        // aanvulling mag alleen gedaan worden met mensen die nog niet in de poule staan,
        // als iemand bijv. eventjes weg is en nog in de poule staat, mag deze niet nogmaals erin worden geplaatst
        for ($i = 0; $i < $tekort; $i++) {
            $index = ($i + $index_nul + 1) % $aantalhuisgenoten;
            if(in_array($huisgenoten[$index],$poule[$poulenaam])){ // Als de huisgenoot als in de poule zit
                $tekort ++;
                echo "Huisgenoot: $huisgenoten[$index] zit al in de poule <br/>";
                // do nothing
            }
            else{
                $poule[$poulenaam][] = $huisgenoten[$index];
                echo "Index: $index, huisgenoot: $huisgenoten[$index] <br/>";
            }
        }


## 6
        foreach ($poule[$poulenaam] as $pouler) {
            $sql = "INSERT INTO odt_schoonmaakpoule$poulenaam  (huisnaam, week, jaar) VALUES ('$pouler','$week','$jaar')"; //Schrijf de nieuwe poule week onder de huidige week
            $conn->query($sql);
            echo "$sql <br/>";
        }
        echo "Einde van loop voor poule:" . $poulenaam . "<br/><br/>";
    }


## 7
    echo "##7<br/>";
    $aantalPERpoule = ["C" => 1, "D" => 1, "A" => 4,"B" => 4];
    $schoonmakers = NULL;
    foreach ($aantalPERpoule as $poulenaam => $aantal) {//Eerst de C&D schoonmakers selecteren, vervolgdens de A&B schoonmakers
        $count = 0;
## 8
        echo "##8<br/>";
        foreach ($poule[$poulenaam] as $huisgenoot) { // $huisgenoot[id of huisnaam] // 2D
            //echo "Index: $index, Huisgenoot:".$huisgenoot['id']." <br/>";
## 9
## 10
            echo "##9<br/>";
            echo "##10<br/>";
            //echo "$week";
            if (!in_array($huisgenoot, $schoonmakers) && is_thuis($conn, $week, $jaar, $huisgenoot)) {
                // Checkt of persoon niet reeds is gekozen voor een andere
                // Persoon moet dan ook thuis zijn
                // JA:
## 11
                echo "##11<br/>";
                // voegt de huisgenoot aan de lijst met schoonmakers toe
                $schoonmakers[] = $huisgenoot;
                $count++;
                // Haal de geselecteerde huisgenoot uit de poule
                $sql = sprintf("DELETE FROM odt_schoonmaakpoule$poulenaam WHERE huisnaam='%s' AND week = '%s' AND jaar='$jaar'", $huisgenoot, $week);
                $conn->query($sql);
                echo $sql . "<br/>";

                if ($count == $aantal) {
                    echo "Selectie $poulenaam klaar<br/>";
                    break;
                }
            } ##12
            else { ##Do nothing
                // Persoon blijft in de poule zitten
                echo "$huisgenoot is reeds gekozen voor andere taak of niet thuis <br/>";
            }
        }
    }


// Maak een vector met de categorieen
## 13
    echo "##13<br/>";
    $categorieen = ['C', 'D', 'A1', 'A1', 'A2', 'A2', 'B1', 'B1', 'B2', 'B2']; // belangrijk dat deze volgorde overeenkomt
    // met de volgorde van onderdeel #7:
    // $aantalPERpoule = ["C" => 1, "D" => 1, "A" => 4,"B" => 4];

// creer bijpassende ingangs- en einddata
//    $dagvandeweek = date('N');      //Dag van de week/*
//    $verschil = $dagvandeweek - 1;  //Verschil van vandaag met afgelopen maandag;
//    $offset = [-$verschil, -$verschil + 1, -$verschil + 2, -$verschil + 3, -$verschil + 4, -$verschil + 5, -$verschil + 6, -$verschil + 7];
    //echo $dagvandeweek."<br/>";
    //echo date("H:i",strtotime('now'));


    $jaarcalc = date('Y',strtotime('thursday this week'));
    $maxweken = WekeninJaar($jaarcalc);
    $diffWeek = $week-date('W')%$maxweken;
    echo $diffWeek;

    $ma = date('Y-m-d', strtotime(" monday  this week +$diffWeek week"));
    $di = date('Y-m-d', strtotime(" tuesday this week +$diffWeek week"));
    $do = date('Y-m-d', strtotime(" thursday this week +$diffWeek week"));
    $vr = date('Y-m-d', strtotime(" friday  this week +$diffWeek week"));
    $zo = date('Y-m-d', strtotime(" sunday  this week +$diffWeek week"));


// Verdeel  de selectie over de schoonmaakfuncties en plaats deze in het schoonmaakrooster
    // en plaats de namen en de schoonmaakfuncties in het schoonmaakrooster
    $length = count($schoonmakers);
    for ($j = 0; $j < $length; $j++) { // $length is normaliter 10
        if ($categorieen[$j] == 'A1' || $categorieen[$j] == 'B1') {
            $dag1 = $ma;
            $dag2 = $di;
        } elseif ($categorieen[$j] == 'A2' || $categorieen[$j] == 'B2') {
            $dag1 = $do;
            $dag2 = $vr;
        } else {
            $dag1 = $ma;
            $dag2 = $zo;
        }
## 14
        echo "##14<br/>";
        echo '<br/>Persoon: ' . $schoonmakers[$j] . '<br/>Categorie: ' . $categorieen[$j] . '<br/>';
        $sql = sprintf("INSERT INTO odt_schoonmaakrooster (id, week, jaar, persoon, categorie, ingangsdatum, einddatum, controle,  datum, taak)
              VALUES ('','%d','%d','%s','%s','%s','%s','','','')", $week, $jaar, $schoonmakers[$j], $categorieen[$j], $dag1, $dag2);// Werkt
        echo $sql."<br/>";

        $conn->query($sql); // Voer de query uit

    }
}

function resetter($conn, $week_start, $jaar_start)
{
//leegt de schoonmaakpoules

    $poules = ["A","B","C","D"];

    $mededeling = "Reset vanaf week: $week_start, $jaar_start ";
    echo $mededeling;
    foreach ($poules as $poulenaam) {
        $sql1 = "Delete FROM odt_schoonmaakpoule$poulenaam WHERE week >= '$week_start' AND jaar = '$jaar_start'"; //tm einde vh jaar
        $sql2 = "Delete FROM odt_schoonmaakpoule$poulenaam WHERE jaar > '$jaar_start'";//volgend jaar
        $conn->query($sql1);
        $conn->query($sql2);
    }
    //echo $sql1 . "<br/>";

// Leegt het schoonmaakrooster
    $sql1 = "Delete FROM odt_schoonmaakrooster WHERE week >= '$week_start' AND jaar = '$jaar_start'";  //tm einde vh jaar
    $sql2 = "Delete FROM odt_schoonmaakrooster WHERE jaar > '$jaar_start'";                             //volgend jaar
    $conn->query($sql1);
    $conn->query($sql2);

    //echo "### Weken na $week_start - $jaar_start zijn gewist!! ### <br/>";
}


