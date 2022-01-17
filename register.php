<?php
session_start();
require_once("users.php");
$USERS = new UserStorage("users.json");
$errors = [];

if (isset($_POST)) {
    if (isset($_POST["register"])) {
        $errors = [];

        if (isset($_POST["inName"])) {
            $name = htmlspecialchars($_POST["inName"]);
        }
        else {
            $errors[] = "Hibás név, nem értelmezhető";
        }

        if (isset($_POST["inTaj"]) && strlen(strval($_POST["inTaj"])) == 9) {
            $taj = htmlspecialchars($_POST["inTaj"]);
        }
        else {
            $errors[] = "Hibás TAJ szám, a TAJ számnak 9 hosszúnak kell lennie!";
        }

        if (isset($_POST["inAddress"])) {
            $address = htmlspecialchars($_POST["inAddress"]);
        }
        else {
            $errors[] = "Hibás cím, nem értelmezhető!";
        }

        if (isset($_POST["inEmail"]) ) {
            $email = htmlspecialchars($_POST["inEmail"]);
        }
        else {
            $errors[] = "Hibás e.mial cím, nem értelmezhető!";
        }

        if (isset($_POST["inPas"]) && isset($_POST["inSecPas"]) && (htmlspecialchars($_POST["inSecPas"])) == htmlspecialchars($_POST["inPas"])) {
            $password = password_hash(htmlspecialchars($_POST["inPas"]), PASSWORD_DEFAULT);
        }
        else {
            $errors[] = "Hibás jelszó!";
        }

        if (count($errors) == 0) {
            if (!$USERS->haveThisEmail($email)) {
                //Sikeres regisztráció
                $USERS->add($email,$password,$name,$taj, $address);
                header('Location: login.php');
            }
            else {
                $errors[] = "Ez az e-mail cím már regisztrálva van!";
            }

        }
    }
}

?>

<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Regisztráció -- DAX8UMBEAD</title>
</head>
<body>
<header>
    <nav>
        <a href="index.php">Vissza a főoldalra</a>
    </nav>
</header>

<main >
    <form method="POST" action="register.php" novalidate>
        <h1>Regisztráció</h1>
        <?php
            if (count($errors)) {
                echo "<ul class=errors>";
                for ($i = 0; $i < count($errors); $i++) {
                    echo "<li>".$errors[$i]."</li>";
                }
                echo "</ul>";
            }
        ?>

        <label for="inName">Teljes név</label>
        <input type="text" id="inName" name="inName" value="<?= ($_POST["inName"] ?? "")?>" placeholder="Teljes név" required autofocus>

        <label for="inTaj">TAJ szám</label>
        <input type="number" id="inTaj" name="inTaj"  value="<?= ($_POST["inTaj"] ?? "") ?>" placeholder="TAJ szám" required >

        <label for="inAddress">Értesítési cím</label>
        <input type="text" id="inAddress" name="inAddress" value="<?= ($_POST["inAddress"] ?? "")?>"  placeholder="Értesítési cím" required >

        <label for="inEmail">Email cím</label>
        <input type="email" id="inEmail" name="inEmail" value="<?= ($_POST["inEmail"] ?? "")?>" placeholder="Email cím" required >

        <label for="inPas">Jelszó</label>
        <input type="password" id="inPas" name="inPas"  placeholder="Jelszó" required>

        <label for="inSecPas">Jelszó megismétlése</label>
        <input type="password" id="inSecPas" name="inSecPas" placeholder="Jelszó megismétlése" required>


        <button type="submit" name="register">Regisztrálás</button>
        
    </form>
</main>


</body>

</html>