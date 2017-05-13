<?php
require "config.php";

//comp_id,quizname[#],

$user = $_SESSION['user'];

if (!in_array($user, $admins)) {
    header('Location: home.php');
}


// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=grade.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');




if (isset($_GET['quiz'])) {
    //DOWNLOAD A SPECIFIC QUIZ' GRADES
    $quiz_id = $_GET['quiz'];

    $query = "SELECT * FROM Quiz WHERE quiz_id = '$quiz_id' ORDER BY quiz_id";
    $result = $db->query($query)->fetch_assoc();

    // output the column headings
    $headings = ['comp_id'];
    $headings[] = $result['text'] . "[" . (int)$result['points'] . "]";
    fputcsv($output, $headings);

    //get all users
    $query = "SELECT student_id FROM Users";
    $result = $db->query($query);
    $users = array();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row['student_id'];
    }


    foreach ($users as $user) {
        //fput a new row
        $next = array($user);
        $query = "SELECT student_id, text, SUM(Grade) AS QuizGrade FROM Users NATURAL JOIN Quiz NATURAL JOIN Grade WHERE student_id = \"" . $user . "\" AND quiz_id = '$quiz_id' GROUP BY student_id, quiz_id ORDER BY quiz_id";
        $result = $db->query($query);
        while ($row = $result->fetch_assoc()) {
            $next[] = $row['QuizGrade'];
        }
        fputcsv($output, $next);
    }

} else {
    //DOWNLOAD ALL GRADES
    $headings = ['comp_id'];

    //find all available quizzes already closed
    $query = "SELECT * FROM Quiz";
    $result = $db->query($query);
    $quizArr = array();
    while ($row = $result->fetch_assoc()) {
        $quizArr[] = $row;
    }

    foreach ($quizArr as $quiz) {
        if (new DateTime() > new DateTime($quiz['close_date'])) {
            $headings[] = $quiz['text'] . "[" . (int)$quiz['points'] . "]";
        }
    }
    fputcsv($output, $headings);

    //get all users
    $query = "SELECT student_id FROM Users";
    $result = $db->query($query);
    $users = array();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row['student_id'];
    }


    foreach ($users as $user) {
        //fput a new row
        $next = array($user);
        $query = "SELECT student_id, text, SUM(Grade) AS QuizGrade FROM Users NATURAL JOIN Quiz NATURAL JOIN Grade WHERE student_id = \"" . $user . "\" GROUP BY student_id, quiz_id ORDER BY quiz_id";
        $result = $db->query($query);
        while ($row = $result->fetch_assoc()) {
            $next[] = $row['QuizGrade'];
        }
        fputcsv($output, $next);

    }
}