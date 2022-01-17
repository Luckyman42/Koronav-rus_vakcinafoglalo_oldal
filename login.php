<?php
session_start();
require_once("users.php");
$USERS = new UserStorage("users.json");

$errors = [];

$url = "login.php";
if (isset($_GET["appid"])) {
    $url = "login.php?appid=".$_GET["appid"];
}

if (isset($_POST) && isset($_POST["loginBtn"])) {
    $errors = [];

    if (isset($_POST["inEmail"])) {
        $email = htmlspecialchars($_POST["inEmail"]);
    }
    else {
        $errors[] = "Az e-mail cím nem felismerhető!";
    }

    if (isset($_POST["inPas"])) {
        $password = htmlspecialchars($_POST["inPas"]);
    }
    else {
        $errors[] = "A jelszó nem felismerhető!";
    }

    if (count($errors) == 0) {
            if ($USERS->validPassword($email,$password)) {
            //Sikeres belépés
            $_SESSION["user"] = $USERS->findIdByEmail($email);
            if (isset($_GET["appid"])) {
                header("Location:oneAppoi.php?appid=".$_GET["appid"]);
            } else {
                header('Location: index.php');
            }
        }else{
            $errors[]="Hibás felhaszálónév vagy jelszó!";
        }
    }


}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEJELENTKEZÉS -- DAX8UM BEAD</title>
</head>
<body>
<header>
    <nav>
        <a href="index.php">Vissza a főoldalra</a>
    </nav>
</header>
    <form action="<?=$url?>" method="post" novalidate>
        <h1 >Bejelentkezés</h1>
        <?php
        if (count($errors)) {
            echo "<ul class=errors>";
            for ($i = 0; $i < count($errors); $i++) {
                echo "<li>".$errors[$i]."</li>";
            }
            echo "</ul>";
        }
        ?>

        <label for="inEmail">Email cím</label>
        <input type="email" id="inEmail" name="inEmail" placeholder="Email cím" required autofocus>

        <label for="inPas">Jelszó</label>
        <input type="password" id="inPas" name="inPas" placeholder="Jelszó" required>

        <button name="loginBtn" type="submit">Belépés</button>
    </form>
    </div>
    <br>
    <p>Regisztrációhoz kattintson ide: <a href="register.php">Regisztráció</a></p>
</body>
</html>