<?php
$filename   = basename($_SERVER['PHP_SELF']);
$file       = substr_replace($filename,"",-4);
if($file=='index'||$file=='selectie'||$file=='aftekenen'){
    $ids = ['current','','','',''];
}
elseif($file=='maand'||$file=='overzicht_persoonlijk'){
    $ids = ['','current','','',''];
}
elseif($file=='boetes'){
    $ids = ['','','current','',''];
}
elseif($file=='instellingen'){
    $ids = ['','','','current',''];
}
elseif($file=='loguit'){
    $ids = ['','','','','current'];
}


?>

<div id="menu" class="col_right">
    <ul>
        <a href="http://www.schoonmaken.reetro21.nl/overzicht.php"><li id='<?php echo $ids[0]; ?>'>Week</li></a>
        <a href="http://www.schoonmaken.reetro21.nl/maand.php"><li id='<?php echo $ids[1]; ?>'>Persoonlijk</li></a>
        <a href="http://www.schoonmaken.reetro21.nl/boetes.php"><li id='<?php echo $ids[2]; ?>'>Boetes</li></a>
        <a href="http://www.schoonmaken.reetro21.nl/instellingen.php"><li id='<?php echo $ids[3]; ?>'>Instellingen</li></a>
        <?php if(check_ingelogd($conn)){?>
        <a href="http://www.schoonmaken.reetro21.nl/loguit.php"><li id='<?php echo $ids[4]; ?>'>Logout</li></a>
        <?php
        }
        ?>
    </ul>
</div>

