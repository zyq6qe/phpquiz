<?php

require "config.php";

unset($_SESSION['quiz']);

//Fetch all quizzes
$query = "SELECT * FROM Quiz";


$result = $db->query($query);

//hold all returned quizzes
$quizArray = array();

//Add all the questions from the result to the questions array
while ($row = $result->fetch_assoc()) {
    $quizArray[] = $row;
}

foreach ($quizArray as $quiz) {
?> <a href="quiz.php?quiz=<?php echo $quiz['quiz_id'] ?>"> <?php echo $quiz['text'] ?></a><br/>
    Grade: <?php
    //Fetch overall grade from Grade
    $query = "SELECT grade FROM Grade WHERE student_id = \"".$_SESSION['user']."\" AND quiz_id = ".$quiz['quiz_id'];
    $result = $db->query($query)->fetch_assoc();

    if (sizeof($result) == 0) {
        echo "Not taken <br/><br/>";
    } else {
        echo $result['grade']."/".$quiz['points']."<br/><br/>";
    }
}