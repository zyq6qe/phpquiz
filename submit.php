<?php

require "config.php";

$user = $_SESSION['user'];
$quiz = $_SESSION['quiz'];
$submission = array();

$grade = 0;

if (isset($_POST['submit'])) {
    foreach ($_POST as $key => $value) {
        if($key == "submit") {
            break;
        }
        $submission = explode(",", $value);

        //prevent sql injection
        if (strpos($key, '\'') !== false) {
            header('Location: home.php');
        }

        //get question type
        $query = "SELECT question_type FROM Question WHERE question_id = ".$key;
        $type = $db->query($query)->fetch_assoc();

        if($type['question_type'] == 'FR') { //free response
            //get answer id
            $query = "SELECT answer_id FROM Answer WHERE question_id = ".$key;
            $result = $db->query($query)->fetch_assoc();
            $answer_id = $result['answer_id'];

            $query = "INSERT INTO Submission VALUES('$user', '$quiz', '$key', '$answer_id', '$value')";
            $db->query($query); //insert into Submission

            //grade the response
            $max_points = 0;
            //get and check regex
            $query = "SELECT * FROM Answer WHERE question_id = ".$key;
            $result = $db->query($query);

            //Create an array to hold all the returned fr answers
            $fr = array();
            while ($row = $result->fetch_assoc()) {
                $fr[] = $row;
            }

            foreach ($fr as $response) {
                preg_match($response['text'], $value, $matches, PREG_OFFSET_CAPTURE, 0);
                if (sizeof($matches) != 0) {
                    if ($max_points < $response['points']) {
                        $max_points = $response['points'];
                    }
                }
            }

            $grade = $grade + $max_points;

        } else { //multiple choice
            $query = "INSERT INTO Submission VALUES('$user', '$quiz', '$key', '$submission[0]', '$submission[1]')";
            $db->query($query); //insert into Submission

            //Grade the response
            $query = "SELECT points FROM Answer WHERE answer_id = ".$submission[0];
            $result = $db->query($query)->fetch_assoc();
            $grade = $grade + $result['points'];
        }
    }

    $query = "INSERT INTO Grade VALUES('$user', '$quiz', '$grade')";
    $db->query($query);
}

unset($_SESSION['quiz']);

header('Location: home.php');