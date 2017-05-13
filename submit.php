<?php

require "config.php";
require "header.php";


$user = $_SESSION['user'];
$quiz = $_SESSION['quiz'];
$submission = array();

$grade = 0;

$query = "";


$query = "SELECT * FROM Accessed WHERE student_id = '$user' AND quiz_id = '$quiz'";
$access = $db->query($query)->fetch_assoc();
$now = new \DateTime();
$deadline = new \DateTime($access['deadline']);


//IF IT'S NOT PAST THE DEADLINE PUSH TO DB
if ($deadline > $now) {
    //reset users submission and grade
    $query = "DELETE FROM Submission WHERE quiz_id = $quiz AND student_id ='$user'";
    $db->query($query);

    $query = "DELETE FROM Grade WHERE quiz_id = $quiz AND student_id ='$user'";
    $db->query($query);

    print_r($_POST);

//re-enter everything in form so far
    foreach ($_POST as $key => $value) {
        if($key == "submit") {
            continue;
        }

        if (strpos($key, 'comment') !== false) {
            continue;
        }

        //prevent sql injection
        if (strpos($key, '\'') !== false) {
            header('Location: home.php');
        }

        //get comment
        $comment = $_POST['comment,' . $key];
        if (empty($_POST['comment,' . $key])) {
            $comment = NULL;
        }


        $query = "SELECT base_points FROM Question WHERE question_id = ".$key;
        $result = $db->query($query)->fetch_assoc();
        $base_points = $result['base_points'];

        $max_points = 0;

        //get question type
        $query = "SELECT question_type FROM Question WHERE question_id = ".$key;
        $type = $db->query($query)->fetch_assoc();

        if($type['question_type'] == 'FR') { //free response

            $query = "INSERT INTO Submission VALUES('$user', '$quiz', '$key', NULL, '$value', '$comment')";
            $db->query($query); //insert into Submission

            //grade the response
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

            $max_points = $max_points + $base_points;

            $query = "INSERT INTO Grade VALUES('$user', '$quiz', '$key', '$max_points')";
            $db->query($query);

        } else { //multiple choice

            if ($type['question_type'] == 'MMC') {

                for ($i = 0; $i < count($value); $i++) {
                    $submission = explode(",", $value[$i]);
                    $a_id = $submission[0];
                    $text = $submission[1];
                    $query = "INSERT INTO Submission VALUES('$user', '$quiz', '$key', '$a_id', '$text', '$comment')";
                    $db->query($query); //insert into Submission

                    //grade the response
                    $query = "SELECT points FROM Answer WHERE answer_id = ".$a_id;
                    $result = $db->query($query)->fetch_assoc();
                    $max_points = $max_points + $result['points'];
                }
            } else {
                $submission = explode(",", $value);
                $a_id = $submission[0];
                $text = $submission[1];
                $query = "INSERT INTO Submission VALUES('$user', '$quiz', '$key', '$a_id', '$text', '$comment')";
                $db->query($query); //insert into Submission

                //grade the response
                $query = "SELECT points FROM Answer WHERE answer_id = ".$a_id;
                $result = $db->query($query)->fetch_assoc();
                $max_points = $result['points'];
            }

            $max_points = $max_points + $base_points;

            $query = "INSERT INTO Grade VALUES('$user', '$quiz', '$key', '$max_points')";
            $db->query($query);

        }
    }
}


//$query = "INSERT INTO Grade VALUES('$user', '$quiz', '$grade')";
//$db->query($query);

//header('Location: home.php');