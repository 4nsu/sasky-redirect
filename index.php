<?php

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
            echo "Väärä tunniste :(";

        }

    } catch (PDOException $e) {
        echo $e->getMessage();
    }

} else {

    // hash-parametrille ei löytynyt arvoa,
    // tulostetaan käyttäjälle esittelyteksti.
    echo "Tämä on osoitteiden lyhentäjä.<br>Odota maltilla, tänne tulee tulevaisuudessa lisää toiminnallisuutta.";

}

?>
