<?php
session_start();
require_once("users.php");
require_once("appointments.php");
$USERS = new UserStorage("users.json");
$APPOINTMENTS = new AppointmentStorage("appointments.json");


?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAX8UM BEAD</title>
    <link rel="stylesheet" href="style.css">
    <style>
    
        .full{
            color:red;
        }
        
        .notfull{
            color:green;
        }
    </style>
</head>
<body>
<header>
<nav>

    <?php
        if(isset($_SESSION["user"])){
            echo '<a href="logout.php">Kijelentkezés</a>';
        }else{
            echo '<a href="register.php">Regisztráció</a>';
            echo '<a href="login.php">Bejelentkezés</a>';
        }
    ?>
</nav>
    <h1>NemKoViD - Mondj nemet a koronavírusra</h1>
    <p>Itt a a koronavírus elleni oltásra lehet időpontot foglalni.</p>
</header>
<main>

<?php

if (isset($_SESSION["user"]) && $_SESSION["user"]==0) {
        echo '<h3>Admin lehetőségek:</h3>';
        echo '<ul>';
        echo '<li><a href="newAppointment.php">Új időpont meghirdetése</a></li>';
        echo '</ul>';
    }
    else if ((!isset($_SESSION["user"])) || (isset($_SESSION["user"]) && $USERS->findById($_SESSION["user"])["appointment"] == -1)) {
        echo '<h3>Jelentkezés oltásra</h3>';
        echo '<p>Lentebb, láthatja az oltásokat:</p>';
    }
    else if (isset($_SESSION["user"]) &&  $USERS->findById($_SESSION["user"])["appointment"] >= 0) {
        echo '<h2>Az ön által lefoglalt időpont részletei:</h2>';
        $app = $APPOINTMENTS->getAppoiById($USERS->findById($_SESSION["user"])["appointment"]);
        echo '<h3>'.$app["year"].'.'.sprintf("%02s", $app["month"]).'.'.sprintf("%02s", $app["day"]).' '.sprintf("%02s", $app["hour"]).':'.sprintf("%02s", $app["min"]).'</h3>';
        echo '<a href="nope.php" >Időpont lemondása</a>';

    }
    else {
        echo '<h2>Hiba!</h2>';

    }
?>


<table>
    <tr>
        <td><button id="prev" type="button"><- Előző</button></td>
        <td id=calHeader>Január</td>
        <td><button id="next" type="button">Következő -></button></td>
    </tr>
</table>
<table id="calendar">
<tr>
            <th>Hétfő</th>
            <th>Kedd</th>
            <th>Szerda</th>
            <th>Csütörtök</th>
            <th>Péntek</th>
            <th>Szombat</th>
            <th>Vasárnap</th>
      </tr>
        <tr id="loading">
            <td colspan="7">Betöltés alatt...</td>
        </tr>
</table>
</main>
</body>
<script src="index.js"></script>
</html>