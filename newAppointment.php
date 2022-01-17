<?php
session_start();
require_once("appointments.php");
$APPOINTMENTS = new AppointmentStorage("appointments.json");
$errors = [];

if (isset($_POST)) {
    if (isset($_POST["ok"])) {
        $newYear;
        $newMonth;
        $newDay;
        $newHour;
        $newMin;
        $newLimit;
        $errors = [];


        //Dátum
        if (isset($_POST["date"])) {
            $YMD = explode(".",htmlspecialchars($_POST["date"]));
            if (count($YMD) == 3) {
                $year = intval($YMD[0]);
                $month = intval($YMD[1]);
                $day = intval($YMD[2]);

                if ($year <= 0 || $month <= 0 || $day <= 0 || $month > 12 || $day > cal_days_in_month(CAL_GREGORIAN, $month, $year)) {
                    $errors[] = "A dátum minden részének helyesnek kell lennie";
                }
                else {
                    if (
                        $year < intval(date("Y")) ||  //korábbi év
                        ($year == intval(date("Y")) && $month < intval(date("m"))) || // ebben az évben korábbi hónap
                        ($year == intval(date("Y")) && $month == intval(date("m")) && $day < intval(date("d"))) ) {  //ebben az évben és hónapban de korábbi napon
                        
                        $errors[] = "Nem lehet időpontot a múltban rögzíteni!";
                    }

                    /*else if ($year == intval(date("Y"))) {
                        if ($month < intval(date("m"))) {
                            $errors[] = "Nem lehet időpontot a múltban rögzíteni (hónap)!";
                        }
                        else if ($month == intval(date("m"))) {
                            if ($day < intval(date("d"))) {
                                $errors[] = "Nem lehet időpontot a múltban rögzíteni (nap)!";
                            }
                        }

                    }*/
                }

                if (count($errors) == 0) {
                   $newYear = $year;
                   $newMonth = $month;
                   $newDay = $day;
                }
            }
            else {
                $errors[] = "Dátum formátum nem megfelelő!";
            }
        }
        else {
            $errors[] = "A beírt dátum nem értelmezhető!";
        }

        //Időpont
        if (isset($_POST["time"])) {
            $HM = explode(":",htmlspecialchars($_POST["time"]));
            if (count($HM) == 2) {
                $hour = intval($HM[0]);
                $min = intval($HM[1]);
                if ($hour < 0 || $min < 0 || $hour > 23 || $min > 59) {
                    $errors[] = "Az időpont minden tagjának helyesnek kell lennie!";
                }
                else {
                    if (count($errors) == 0) {

                        //Ha a mai dátum volt megadva akkor ne hírdessünk meg a múltra, 
                        //mivel az error-ok száma 0 így tudjuk hogy a dátum helyesen van megadva.
                        $isToday = $newYear == date("Y") && $newMonth == date("m") && $newDay == date("d");
                        if ( $isToday && $hour < intval(date("G")) || // mai nap
                             $isToday && $hour == intval(date("G")) && $min < intval(date("i"))
                            ) {
                                $errors[] = "Nem lehet időpontot a múltban rögzíteni!";
                          
                                /*
                            if ($hour < intval(date("G"))) {
                                $errors[] = "Nem lehet időpontot a múltban rögzíteni (óra)!";
                            }
                            else {
                                if ($min < intval(date("i"))) {
                                    $errors[] = "Nem lehet időpontot a múltban rögzíteni (perc)!";
                                }
                            }*/
                        }

                        if (count($errors) == 0) {
                            $newHour = $hour;
                            $newMin = $min;
                        }
                    }
                }



            }
            else {
                $errors[] = "Időpont formátum nem megfelelő!";
            }
        }
        else {
            $errors[] = "A beírt időpont nem értelmezhető!";
        }

        //Limit
        if (isset($_POST["limit"])) {
            if (is_numeric(htmlspecialchars($_POST["limit"]))) {
                $limit = intval(htmlspecialchars($_POST["limit"]));
                if ($limit <= 0) {
                    $errors[] = "A helyek számának pozitívnak kell lennie!";
                }else{
                    $newLimit = $limit;
                }
                
            }
            else {
                $errors[] = "A helyek számának számnak kell lennie!";
            }
        }
        else {
            $errors[] = "A beírt időpont nem értelmezhető!";
        }


        if (count($errors) == 0) {
            //Sikeres validálás
            $APPOINTMENTS->newAppoi($newYear,$newMonth,$newDay,$newHour,$newMin,$newLimit);
            header("Location: index.php");
        }
    }
}

?>

<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Időpont rögzítés --- DAX8UM BEAD</title>
</head>
<body>

<header>
    <nav>
        <a href="index.php">Vissza a főoldalra</a>
    </nav>
</header>
<main>
    <form method="POST" action="newAppointment.php" novalidate>
        <h1>Új időpont meghírdetése</h1>
        <?php
            if (count($errors)) {
                echo "<ul class=errors>";
                for ($i = 0; $i < count($errors); $i++) {
                    echo "<li>".$errors[$i]."</li>";
                }
                echo "</ul>";
            }
        ?>

            <label for="date">Dátum</label>
            <input type="text" id="date" name="date" placeholder="ÉÉÉÉ.HH.NN" value="<?=($_POST["date"]??"")?>" required>
       
            <label for="time">Időpont</label>
            <input type="text" id="time" name="time" placeholder="ÓÓ:PP" value="<?=($_POST["time"]??"")?>" required>
       
            <label for="limit">Helyek száma</label>
            <input type="number" id="limit" name="limit" placeholder="Helyek száma" value="<?=($_POST["limit"]??"")?>" required>
       
        <button type="submit" name="ok">Időpont rögzítése</button>
    </form>
</main>


</body>

</html>