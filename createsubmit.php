<?php


require "config.php";
require "header.php";


$user = $_SESSION['user'];

if (!in_array($user, $admins)) {
    header('Location: home.php');
}

//quiz always has 4 fields
//quiz_title
//quiz_open_date
//quiz_close_date
//quiz_time_limit

//question has 4 fields
//text
//points
//question_type
//base_points

//answer has 2 fields
//text
//points



if (isset($_POST['submit'])) {
    $input = $_POST['input'];


    //first four fields:
    $title = $input[0];
    $open_date = $input[1];
    $close_date = $input[2];
    $time_limit = $input[3];

    //insert quiz
    $query = "INSERT INTO Quiz VALUES('null', '$title', '$open_date', '$close_date', '$time_limit', 'null')";
    $db->query($query); //insert new quiz into database

    //fetch new quiz_id
    $query = "SELECT MAX(quiz_id) FROM Quiz";
    $result = $db->query($query)->fetch_assoc();
    /******* NEW QUIZ ID *******/
    $quiz_id = $result['MAX(quiz_id)'];

    //shift off the first four entries in input arr
    $input = array_slice($input, 4);


    //parsing rest of input
    $question_flag = 1;
    $curr_question = null; //need to keep track of current question for answers to belong to a question
    $curr_answers = array();


    while (count($input) != 0) {
        if ($input[0] == 'QUESTION') {
            $question_flag = 1;
            $input = array_slice($input, 1);

        } else if ($input[0] == 'ANSWER') {
            $question_flag = 0;
            $input = array_slice($input, 1);
        } else if ($question_flag) {
            if (!empty($curr_answers)) {
                //previous questions answers are done
                //push previous questions' answers into the database
                print_r($curr_answers);
                shuffle($curr_answers);
                foreach ($curr_answers as $ans) {
                    $text = $ans[0];
                    $points = $ans[1];
                    $ex = $ans[2];
                    $query = "INSERT INTO Answer VALUES('$curr_question', 'null', '$text', '$points', '$ex')";
                    print_r($query);
                    echo '<br/>';
                    $db->query($query); //insert into Answer
                }
            }

            //reset answers array for this question
            $curr_answers = array();

            //the following 4 entries were marked to belong to a question
            $text = $input[0];
            $points = $input[1];
            $type = $input[2];
            $base_points = $input[3];
            $query = "INSERT INTO Question VALUES('$quiz_id', 'null', '$text', '$points', '$type', '$base_points')";
            print_r($query);
            echo '<br/>';
            $db->query($query); //insert into Question

            //fetch new question_id
            $query = "SELECT MAX(question_id) FROM Question";
            $result = $db->query($query)->fetch_assoc();
            $curr_question = $result['MAX(question_id)'];

            //shift off these 4 entries
            $input = array_slice($input, 4);
        } else if (!$question_flag) {
            //the following 3 entries were marked to belong to an answer
            $curr_answers[] = array($input[0], $input[1], $input[2]);

            //shift off these 3 entries
            $input = array_slice($input, 3);
        }
    }

    //last set of answers
    if (!empty($curr_answers)) {
        //previous questions answers are done
        //push previous questions' answers into the database
        print_r($curr_answers);
        shuffle($curr_answers);
        foreach ($curr_answers as $ans) {
            $text = $ans[0];
            $points = $ans[1];
            $ex = $ans[2];
            $query = "INSERT INTO Answer VALUES('$curr_question', 'null', '$text', '$points', '$ex')";
            print_r($query);
            echo '<br/>';
            $db->query($query); //insert into Answer
        }
    }

    //end: update quiz points attribute
    $max_points = 0;
    //Create a query to fetch all the questions
    $query = "SELECT * FROM Question WHERE quiz_id = " . $quiz_id;
    $result = $db->query($query);

    //Create an array to hold all the returned questions
    $questionsArray = array();

    //Add all the questions from the result to the questions array
    while ($row = $result->fetch_assoc()) {
        $questionsArray[] = $row;
    }

    foreach($questionsArray as $question) {
        $max_points = $max_points + $question['points'];
    }

    $query = "UPDATE Quiz SET points = '$max_points' WHERE quiz_id = " . $quiz_id;
    print_r($query);
    $db->query($query); //insert into Answer


}

header("Location: home.php");
