<html>
<title>Schoonmaakrooster ORW21</title>
<head>
    <META
        HTTP-EQUIV="Refresh"
        CONTENT="300; URL=overzicht.php">
    <script src="scripts/instellingen.js"></script>
    <link rel="stylesheet" type="text/css" href="algemeen.css">
    <link rel="stylesheet" type="text/css" href="instellingen.css">
    <?php
    //echo "include";

    include("verborgen/functies.php");
    include 'favicon.php';
    //echo "connect";
    $conn = connect2DB();
    //echo "check_ingelogd";
    $ingelogd = check_ingelogd($conn);
    //echo "Ingelogd: $ingelogd <br/>";
    if (!$ingelogd) { // als er nog niet is ingelogd moet de login gechecked worden
        //echo "Niet ingelogd <br/>";
        $pass = $_POST['pass'];
        $ingelogd = login($conn, $pass);

    }

    $vakanties = NULL;
    $sql = "SELECT id, naam, begin, eind FROM odt_vakanties ORDER BY begin ASC";
    $res = $conn->query($sql);
    while ($fetch = $res->fetch_array(MYSQLI_ASSOC)) {
        $vakanties[] = $fetch;
    }

    // TODO: hier nog een datum check
    // Als de datum in het verleden ligt, heeft het geen nut dit nog te tonen in de tabel
    $afwezigen = NULL;
    $sql = "SELECT id, huisnaam, begin, eind FROM odt_afwezigheid ORDER BY id ASC";
    $res = $conn->query($sql);
    while ($fetch = $res->fetch_array()) {
        $afwezigen[] = $fetch;
    }

    $huisgenoten = NULL;
    $sql = "SELECT id, voornaam, achternaam, entrydate, hierarchy, huisnaam FROM odt_users ORDER BY hierarchy ASC";
    $res = $conn->query($sql);
    while ($fetch = $res->fetch_array(MYSQLI_ASSOC)) {
        $huisgenoten[] = $fetch;
    }

    ?>


</head>

<body>
<div id="main">

    <?php
    //echo "include";
    include 'col_rechts.php'; ?>


    <?php if (!$ingelogd) { ?>
        <div class="block1" id="login">
            <h1>Geef wachtwoord op</h1>

            <form action="instellingen.php" method="POST">
                <input type="password" name="pass"/><br/>
                <input type="submit" value="Log in" class="knop"/>
            </form>
        </div>
    <?php } else {
    //###### Indeling ######
    echo "<div class='block1' id='indeling'>";
    echo "<h1>Indeling</h1>"; ?>
    <div class="col3">
        <p>Normaal gesproken wordt alles automatisch ingedeeld tot 5 weken van te voren. Door het te laat instellen van
            de opties op deze pagina kan het zijn dat een herindeling nodig is. Hier kan het rooster worden gereset
            vanaf de vorige week (dus de huidige week en later).</p>

        <p>
            Het volgende gaat verloren en kan niet (echt niet) worden hersteld:
        </p>
        <ul style="margin-left: 20px;">
            <li style="list-style-type: disc">Gedane beurten</li>
            <li style="list-style-type: disc">Wissels</li>
        </ul>


    </div>
    <button id="knop_links" onclick="deel_in();" class="knop">Deel opnieuw in</button>
    <!--<button id="knop_rechts" onclick="reset();" class="knop">Reset</button>-->
    <?php
    echo "</div>";

    //###### Mail ######?>
    <div class='block1' id='mail'>
        <h1>Mail</h1>

        <div class='col3'>
            <form id='stuur_emails' action='inst_mail.php' method='POST'>
                <input type='submit' value='Stuur mail' class='knop' id='knop_rechts'/>
            </form>
        </div>
        <?php
        echo " </div > ";

        //###### Vakanties ######?>
        <div class='block1'>
            <h1>Vakanties</h1>

            <div class='col3'>
                <p>Hier kan je de vakanties instellen zodat in de vakantie geen mensen worden ingedeeld. Belangrijk is
                    dat
                    dit wordt gedaan, zodat de taken eerlijk worden verdeeld .</p>
                <?php echo "<table id = 'vakanties' > ";
                echo "<tr ><th ></th ></th ><th > Vakantie</th ><th > Begin</th ><th > Eind</th ></tr > ";
                foreach ($vakanties as $vakantie) {
                    echo sprintf("<tr onclick = \"hide_element('vakantie_nieuw');delete_vakantie(%s,'%s')\" class='content'><td class='delete'>&ndash;</td><td>%s</td><td>%s</td><td>%s</td></tr>", $vakantie['id'], $vakantie['naam'], $vakantie['naam'], $vakantie['begin'], $vakantie['eind']);
                }
                echo "<tr onclick=\"show_element('vakantie_nieuw');\" class='content'><td class='knop_voeg_toe'>+</td><td colspan ='3' class='knop_voeg_toe'>Voeg toe</td></tr>";
                echo "</table>"; ?>
                <form id='vakantie_nieuw' class="hidden" action='inst_vakanties.php' method='POST'>
                    <table>
                        <table>
                            <tr>
                                <td>Vakantie:</td>
                                <td><input type='text' name="vakantie" placeholder="Vakantie"></td>
                            </tr>
                            <tr>
                                <td>Van:</td>
                                <td><input type='date' name="begin" placeholder="jjjj-mm-dd"></td>
                            </tr>
                            <tr>
                                <td>Tot:</td>
                                <td><input type='date' name="eind" placeholder="jjjj-mm-dd"></td>
                            </tr>
                        </table>
                    </table>
                    <input type='hidden' name="add" value=1>
                    <input type='hidden' name="delete" value=0>
                    <input type='submit' value='Voeg toe' class='knop' id='knop_rechts'/>
                </form>
            </div>
        </div>
        <?php
        //###### AFWEZIGE HUISGENOTEN ######?>
        <div class='block1'>
            <h1>Afwezige huisgenoten</h1>

            <div class='col3'>
                <p>Hier kan je instellen wie wanneer weg is. Belangrijk is dat dit wordt gedaan, zodat de
                    taken
                    eerlijk worden verdeeld en mensen geen boetes krijgen wanneer zij er niet zijn. Dit
                    hoeft
                    niet te worden gedaan voor in de vakanties, alleen voor reguliere periodes. Let erop dat
                    je
                    de data in hele weken geeft, dus ma-zo. Als iemand halverwege een week thuiskomt geldt
                    die
                    week als een week van afwezigheid.</p>
                <?php echo "<table id='afwezigen'>";
                echo "<tr><th></th><th>Naam</th><th>Van</th><th>Tot</th></tr>";
                foreach ($afwezigen as $afwezige) {
                    echo sprintf("<tr onclick=\"hide_element('afwezige_nieuw');delete_afwezige(%s,'%s')\" class='content'><td class='delete'>&ndash;</td><td>%s</td><td>%s</td><td>%s</td></tr>", $afwezige['id'], $afwezige['huisnaam'], $afwezige['huisnaam'], $afwezige['begin'], $afwezige['eind']);
                }
                echo "<tr onclick=\"show_element('afwezige_nieuw');\" class='content'><td class='knop_voeg_toe'>+</td><td colspan ='3' class='knop_voeg_toe'>Voeg toe</td></tr>";
                echo "</table> "; ?>
                <form class='hidden' id="afwezige_nieuw" action='inst_afwezigheid.php' method='POST'>
                    <table>
                        <tr>
                            <td>Huisnaam:</td>
                            <?php $bewoners = get_huisgenoten($conn); ?>
                            <td><select name="huisnaam">
                                    <?php
                                    foreach ($bewoners as $huisgenoot) {
                                        printf('<option value="%s">%s</option>', $huisgenoot, $huisgenoot);
                                    }
                                    ?>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Van:</td>
                            <td><input type='date' name="van" placeholder="jjjj-mm-dd"></td>
                        </tr>
                        <tr>
                            <td>Tot:</td>
                            <td><input type='date' name="tot" placeholder="jjjj-mm-dd"></td>
                        </tr>
                    </table>
                    <input type='hidden' name="add" value=1>
                    <input type='hidden' name="delete" value=0>
                    <input type='submit' value='Voeg toe' class='knop' id="knop_rechts">

                </form>
            </div>
        </div>
        <?php
        //###### HUISGENOTEN ######?>
        <div class='block1'>
            <h1>Huisgenoten</h1>

            <div class='col3'>
                <p>Hier kan je de huisgenoten beheren. Als iemand het huis verlaat (niet voor
                    korte termijn), dan kan die hier worden verwijderd. Ook kunnen hier de nieuwe
                    huisgenoten
                    worden toegevoegd.</p>
                <?php echo "<table id='huisgenoten'>";
                echo "<tr><th></th><th>Huisnaam</th><th>Voornaam</th><th>Achternaam</th></tr>";
                foreach ($huisgenoten as $huisgenoot) {
                    echo "<tr>";
                    echo sprintf("<tr onclick=\"hide_element('huisgenoot_nieuw');delete_huisgenoot(%s,'%s')\" class='content'><td class='delete'>&ndash;</td><td>%s</td><td>%s</td><td>%s</td></tr>", $huisgenoot['id'], $huisgenoot['huisnaam'], $huisgenoot['huisnaam'], $huisgenoot['voornaam'], $huisgenoot['achternaam']);
                }
                echo "<tr onclick=\"show_element('huisgenoot_nieuw');\" class='content'><td class='knop_voeg_toe'>+</td><td colspan ='3' class='knop_voeg_toe'>Nieuwe huisgenoot</td></tr>";
                echo "</table>"; ?>
                <form class="hidden" id="huisgenoot_nieuw" method="post"
                      action="inst_huisgenoten.php">
                    <table>
                        <tr>
                            <td>Voornaam:</td>
                            <td><input type='text' name="voornaam" placeholder="Voornaam"></td>
                        </tr>
                        <tr>
                            <td>Achternaam:</td>
                            <td><input type='text' name="achternaam" placeholder="Achternaam">
                            </td>
                        </tr>
                        <tr>
                            <td>Huisnaam:</td>
                            <td><input type='text' name="huisnaam" placeholder="Huisnaam"></td>
                        </tr>
                        <tr>
                            <td>Intrekdatum:</td>
                            <td><input type='date' name="entrydate"
                                       placeholder="Datum binnenkomst">
                            </td>
                        </tr>
                    </table>
                    <input type='hidden' name="add" value=1>
                    <input type='hidden' name="delete" value=0>
                    <input type="submit" value="Voeg toe" class="knop" id="knop_rechts">

                </form>


                <?php
                }
                ?>


            </div>

</body>
</html>