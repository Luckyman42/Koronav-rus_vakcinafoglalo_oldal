<?php
session_start();
require_once("users.php");
require_once("appointments.php");
$USERS = new UserStorage("users.json");
$APPOINTMENTS = new AppointmentStorage("appointments.json");

if(!isset($_SESSION['user']) ||  
   (isset($_SESSION['user']) && $USERS->findById($_SESSION['user'])["appointment"] == -1)){
    header("Location: index.php");

}else{    
    if (isset($_POST)) {
        if (isset($_POST["ok"])) {
            if (isset($_SESSION['user']) &&  $USERS->findById($_SESSION['user'])["appointment"] > -1) {
                $APPOINTMENTS->userRemove($_SESSION['user'],$USERS->findById($_SESSION['user'])["appointment"]);
                $USERS->removeAppointmentfromUser( $_SESSION['user']);
            }
            header("Location: index.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JELENTKEZÉS LEMONDÁSA -- DAX8UM BEAD</title>
</head>
<body>
<header>
    <nav>
        <a href="index.php">Vissza a főoldalra</a>
    </nav>
</header>
    <form action="nope.php" method="POST" novalidate>
        <h1>Időpont Lemondása</h1>
        <p>Biztos, hogy le szeretnéd mondani az időpontodat?</p>
        <button type="submit" name="ok">Jelentkezés lemondása</button>
    </form>
</body>
</html>