<?php

// Pagestatus määrittelee mikä sivu tulostetaan.
//  0 = etusivu
// -1 = virheellinen tunniste
// -2 = tietokantavirhe
$pagestatus = 0;

// Tallennetaan perusosoite muuttujaan
$baseurl = "https://neutroni.hayo.fi/~akoivu/redirect/";

// Määritellään yhteys-muuttujat.
// Tietokannan nimi, käyttäjä ja salasana, haetaan palvelimen ympäristömuuttujista.
$dsn = "mysql:host=localhost;dbname={$_SERVER['DB_DATABASE']};charset=utf8mb4";
$user = $_SERVER['DB_USERNAME'];
$pwd = $_SERVER['DB_PASSWORD'];
$options = [
    // Mahdolliset tietokantalauseiden virheet näkyville.
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // Oletus hakutulos assosiatiiviseksi taulukoksi.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Poista valmisteltujen lauseiden emulointi käytöstä.
    PDO::ATTR_EMULATE_PREPARES => false
];


// Löytyykö urlista hash-parametri.
if (isset($_GET["hash"])) {

    // Tallennetaan hash-arvo muuttujaan.
    $hash = $_GET["hash"];

    try {

        // Tietokantayhteyden avaus.
        $yhteys = new PDO($dsn, $user, $pwd, $options);

        // Tunnisteen haku tietokannasta.
        // Kyselyn valmistelu.
        $kysely = "SELECT url FROM osoite WHERE tunniste = ?";
        $lause = $yhteys->prepare($kysely);
        // Sidotaan $hash -tunniste kyselyn parametriin.
        $lause->bindValue(1, $hash);
        // Ja suoritetaan haku.
        $lause->execute();
        // Hakutuloksen rivi tallennetaan $tulos -muuttujaan.
        $tulos = $lause->fetch();

        // Tarkistetaan kyselyn tulos.

        // Löytyykö kannasta riviä hash-tunnisteella.
        if ($tulos) {

            // Rivi löytyi, haetaan osoite.
            $url = $tulos['url'];

            // Edelleenohjataan tietokannasta löytyvään osoitteeseen.
            header("Location: " . $url);
            exit;

        } else {

            // Kannassa ei ole hash-muuttujaa vastaavaa tunnistetta,
            // tulostetaan virheilmoitus.
            $pagestatus = -1;

        }

    } catch (PDOException $e) {
        // Virhe avaamisessa, tulostetaan virheilmoitus.
        $pagestatus = -2;
        $error = $e->getMessage();
    }

}

?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='styles.css' rel='stylesheet'>
    <title>Lyhentäjä</title>
</head>
<body>
    <div class='page'>

        <header>
            <h1>Lyhentäjä</h1>
            <div>ällistyttävä osoitelyhentäjä</div>
        </header>

        <main>
            <?php 
                if($pagestatus == 0) { 
            ?>
                <div class='form'>
                    <p>Tällä palvelulla voit lyhentää pitkän osoitteen lyhyeksi. Syötä alla olevaan kenttään pitkä osoite ja paina nappia, saat käyttöösi lyhytosoitteen, jota voit jakaa eteenpäin.</p>
                    <form action='' method='POST'>
                        <label for='url'>Syötä lyhennettävä osoite</label>
                        <div class='url'>
                            <input type='text' name='url' placeholder='tosi pitkä osoite'>
                            <input type='submit' name='shorten' value='lyhennä'>
                        </div>
                    </form>
                </div>
            <?php 
                }

                if($pagestatus == -1) { 
            ?>
                <div class='error'>
                    <h2>HUPSISTA!</h2>
                    <p>Näyttää siltä, että lyhytosoitetta ei löytynyt. Ole hyvä ja tarkista antamasi osoite.</p>
                    <p>Voit tehdä <a href="<?=$baseurl?>">tällä palvelulla</a> oman lyhytosoitteen.</p>
                </div>
            <?php 
                }

                if($pagestatus == -2) { 
            ?>
                <div class='error'>
                    <h2>NYT KÄVI HASSUSTI!</h2>
                    <p>Nostamme käden ylös virheen merkiksi, palvelimellamme on pientä hässäkkää. Ole hyvä ja kokeile myöhemmin uudelleen.</p>
                    <p>(virheilmoitus: <?=$error?>)</p>
                </div>
            <?php 
                }
            ?>
        </main>

        <footer>
            <hr>
            &copy; Kurpitsa Solutions
        </footer>

    </div>
</body>
</html>