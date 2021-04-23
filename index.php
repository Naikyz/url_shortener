<?php
    if (isset($_GET['q'])) {
        $shortcut = htmlspecialchars($_GET['q']);

        $db = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8', 'root', '');

        $req = $db->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut = ?');
        $req->execute(array($shortcut));

        while ($result = $req->fetch()) {
            if ($result['x'] != 1) {
                header('location: ../?error=true&message=Adresse url non connue');
                exit();
            }
        }

        $req = $db->prepare('SELECT * FROM links WHERE shortcut = ?');
        $req->execute(array($shortcut));

        while ($result = $req->fetch()) {
            header('location: '.$result['url']);
        }

    }

    if (!empty($_POST['url'])){
        $url = $_POST['url'];

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            header('location: ../?error=true&message=Adresse url non valide');
            exit();
        }

        $shortcut = crypt($url, rand());

        $db = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8', 'root', '');

        $req = $db->prepare('SELECT COUNT(*) AS x FROM links WHERE URL = ?');
        $req->execute(array($url));

        while ($result = $req->fetch()) {
            if ($result['x'] != 0) {
                header('location: ../?error=true&message=Adresse déjà raccourcie');
                exit();
            }
        }

        $req = $db->prepare('INSERT INTO links(url, shortcut) VALUES(?, ?)');
        $req->execute(array($url, $shortcut));

        header('location: ../?short='.$shortcut);
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" type="image/png" href="pictures/favico.png"/>
        <link rel="stylesheet" type="text/css" href="design/default.css"/>
        <title>Bitly</title>
    </head>
    <body>
        <section id="hello">
            <div class="container">
                <header>
                    <img src="pictures/logo.png" alt="logo" id="logo"/>
                    <h1>Une url longue ? Raccourcissez-là</h1>
                    <h2>Largement meilleur et plus court que les autres.</h2>
                </header>
                <form method="post" action="../">
                    <input type="url" name="url" placeholder="Collez un lien à raccourcir"/>
                    <input type="submit" value="Raccourcir"/>
                </form>

                <?php
                    if (isset($_GET['error']) && isset($_GET['message'])) { ?>
                        <div class="center">
                            <div id="result">
                                <b><?php echo htmlspecialchars($_GET['message']); ?></b>
                            </div>
                        </div>
                    <?php } else if (isset($_GET['short'])) { ?>
                        <div class="center">
                            <div id="result">
                                <b>URL RACCOURCIE : http://localhost/?q=<?php echo htmlspecialchars($_GET['short']); ?></b>
                            </div>
                        </div>
                    <?php }?>
            </div>
        </section>
        <section id="brands">
            <div class="container">
                <h3>Ces marques nous font confiance</h3>
                <img src="pictures/1.png" alt="1" class="picture">
                <img src="pictures/2.png" alt="2" class="picture">
                <img src="pictures/3.png" alt="3" class="picture">
                <img src="pictures/4.png" alt="4" class="picture">
            </div>
        </section>
        <footer>
            <img src="pictures/logo2.png" alt="logo" id="logo2"/>
            <br>2021 © Kylian Paulin<br>
            <a href="#">Contact</a> - <a href="#">À propos</a>
        </footer>
    </body>
</html>