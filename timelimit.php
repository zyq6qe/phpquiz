<?php
/******************************
 * Used to calculate how much time a student has left to complete the quiz
 ******************************/

$my_time_limit = 0;


function getTotalSecs(DateInterval $int){
    return ($int->d * 24 * 60 * 60) + ($int->h * 60 * 60) + ($int->i * 60) + $int->s;
}

$query = "SELECT * FROM Users WHERE student_id = '$student_id'";
$multiplier = $db->query($query)->fetch_assoc();
$mult = $multiplier['time_mult'];

$query = "SELECT * FROM Accessed WHERE student_id = '$student_id' AND quiz_id = '$quiz'";
$access = $db->query($query)->fetch_assoc();

//print_r($access);

//get quiz timelimit
$query = "SELECT time_limit FROM Quiz WHERE quiz_id = " . $quiz;
$time_limit = $db->query($query)->fetch_assoc();

$my_time_limit = (double) $time_limit['time_limit'] * (double) $mult;

$time_interval = date_interval_create_from_date_string($my_time_limit . "minutes");


//if user_id--quiz_id combo does not exist, add this first time access to quiz
//check if student has already access this quiz


if ($access == '') {
    $now = new \DateTime();
    $access = $now->format('Y-m-d H:i:s');
    $deadline = new DateTime($access);
    $deadline->add($time_interval);
    if ($deadline > $close_date) {
        //if access + timelimit > close_date, new date_limit = close_date
        $deadline = $close_date;
    }
    $dline = $deadline->format('Y-m-d H:i:s');
    $query = "INSERT INTO Accessed VALUES('$quiz', '$student_id', '$access', '$dline')";
    $db->query($query); //insert into Submission

    $deadline = $dline;
} else {
    $deadline = $access['deadline'];
}

$deadline_time = new DateTime($deadline);


if ($now > $deadline_time) {
    //if now - access > timelimit, cannot access quiz
    $my_time_limit = 0;
//    header('Location: home.php');
} else {
    //timelimit = deadline - now
    $my_time_limit = getTotalSecs($deadline_time->diff($now));
}


$my_total_limit = getTotalSecs($time_interval);
