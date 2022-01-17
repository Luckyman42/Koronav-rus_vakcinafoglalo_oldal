<?php
session_start();
require_once("users.php");
require_once("appointments.php");
$USERS = new UserStorage("users.json");
$APPOINTMENTS = new AppointmentStorage("appointments.json");

//HA nem kéne itt lennünk ne legyünk itt
if(!isset($_GET["appid"]) || 
    (isset($_SESSION["user"]) && $USERS->findById($_SESSION["user"])["appointment"] >= 0)
){
    header("Location:index.php");
}

// HA nem vagyunk bejelentkezve először tegyük meg
if (!isset($_SESSION["user"])) {
    header("Location: login.php?appid=". $_GET["appid"]);
}

// egyszerü mezei felhasználók vagyunk
if (isset($_SESSION["user"]) && $_SESSION["user"] != 0) {
 
$errors = [];

$url = "oneAppoi.php?appid=" . $_GET["appid"];
if (isset($_POST)) {
    if (isset($_POST["ok"])) {
        if (!isset($_POST["check"])) {
            $errors[] = "Kérlek jelöld be, hogy elfogadod a feltételeket!";
        }

        if (count($errors) == 0) {
            //Időpont regisztrálás
            $USERS->addAppoinntmentToUser($_SESSION["user"],$_GET["appid"]);
            $APPOINTMENTS->userJoinAnAppoi($_SESSION["user"],$_GET["appid"]);
            header("Location: index.php");
        }
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
<?php if(isset($_SESSION["user"]) && $_SESSION["user"]==0):?>
<main>
    <h1>Időpont részletei</h1>
        <?php
        if (isset($_SESSION["user"]) && isset($_GET["appid"]) ) {
            $app = $APPOINTMENTS->getAppoiById($_GET["appid"]);
            echo "<p><b>Időpont: </b>";
            echo $app["year"] . '.' . sprintf("%02s", $app["month"]) . '.' . sprintf("%02s", $app["day"]) . ' ' . sprintf("%02s", $app["hour"]) . ':' . sprintf("%02s", $app["min"]) . '</p>';

            $users = $USERS->usersInTheSameAppoi($_GET["appid"]);
            foreach ($users as $user) {
                echo '<p><b>'.$user["fullname"].'</b>, Taj: '.$user["taj"].', (email: '.$user["email"].')</p>';
            }

            if (count($users) == 0) {
                echo "<p>Erre az időpontra nincsen jelentkező!</p>";
            }

        }
        ?>

</main>

<?php else: ?>

<main>
    <h1>Jelentkezés oltásra</h1>
    
    <?php
      if (count($errors)) {
        echo "<ul class=errors>";
        for ($i = 0; $i < count($errors); $i++) {
            echo "<li>".$errors[$i]."</li>";
        }
        echo "</ul>";
    }
    ?>

    <form action="<?= $url ?>" method="post" novalidate>
        <?php
        if (isset($_SESSION["user"]) && isset($_GET["appid"])) {
            $user = $USERS->findById($_SESSION["user"]);
            echo "<p><b>Teljes név: </b>" . $user["fullname"] . "</p>";
            echo "<p><b>Cím: </b>" . $user["address"] . "</p>";
            echo "<p><b>TAJ: </b>" . $user["taj"] . "</p>";
            echo "<p><b>Időpont: </b>";
            $app = $APPOINTMENTS->getAppoiById($_GET["appid"]);
            echo $app["year"] . '.' . sprintf("%02s", $app["month"]) . '.' . sprintf("%02s", $app["day"]) . ' ' . sprintf("%02s", $app["hour"]) . ':' . sprintf("%02s", $app["min"]) . '</p>';

        }
        ?>
        <div>
            <input type="checkbox" id="check" name="check">
            <label for="check">Elfogadom a jelentkezési feltételeket </label>
        </div>

        <button type="submit" name="ok">Jelentkezés megerősítése</button>
      
    </form>


</main>


<?php endif?>
</body>
</html>