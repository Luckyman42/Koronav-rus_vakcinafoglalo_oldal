<?php
session_start();
require_once("appointments.php");
require_once("users.php");
function CurrentMonth($currentMonth, $year) {
    $date = mktime(12, 0, 0, $currentMonth, 1, $year); // első nap
    $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $year); // napok száma
    
    $offset = date("w", $date);
    $offset = $offset == 0 ? 6 : $offset - 1;
    $row_number = 1;
    
    $calendar = new stdClass();
    $calendar->year = intval($year);
    $calendar->month = intval($currentMonth);
    $calendar->offset = $offset;
    $calendar->numberOfDays = $numberOfDays;
    $calendar->days = [];
    
    for ($day = 1; $day <= $numberOfDays; $day++) {
        if (($day + $offset - 1) % 7 == 0 && $day != 1) {
            $row_number++;
        }
        $calendar->days[] = [];
    }
    
    
    $APPOINTMENTS = new AppointmentStorage("appointments.json");
    
    foreach($APPOINTMENTS->dayInAMonthWhenExistAppoi($calendar->year, $calendar->month) as $day) { 
        $calendar->days[$day-1]= $APPOINTMENTS->allAppoiInTheSameDay($calendar->year, $calendar->month,$day);
    }

    $calendar->rows = $row_number;
    return $calendar;
}

function CurrentDate() {
    $curr = new stdClass();
    $curr->year = date("Y");
    $curr->month = date("m");
    $curr->day = date("j");
    return $curr;
}

function userData(){
    $USERS = new UserStorage("users.json");
    $data = new stdClass();
    if(isset($_SESSION["user"]) ){
        if($_SESSION["user"] == 0){
            $data->isAdmin = true;
            $data->haveAppoi = false;
        }else{
            $data->isAdmin = false;
            $data->haveAppoi = $USERS->findById($_SESSION["user"])["appointment"] >= 0;
        }
    }else{
        $data->isAdmin = false;
        $data->haveAppoi = false;
    }
    return $data;
}

if (isset($_GET)) {
    $response = json_decode(file_get_contents('php://input'), true);
    if (isset($_GET["fun"]) && $_GET["fun"] == "getCalendar") {
        echo json_encode(CurrentMonth($response["month"], $response["year"]));
    }
    else if (isset($_GET["fun"]) && $_GET["fun"] == "current") {
        echo json_encode(CurrentDate());
    }else if(isset($_GET["fun"]) && $_GET["fun"] == "getUserData"){
        echo json_encode(userData());
    }
}





