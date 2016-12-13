<?php
require "config.php";

unset($_SESSION['quiz']);
if (isset($_GET['quiz'])) {
    $_SESSION['quiz'] = $_GET['quiz'];
} else {
    header('Location: home.php');
}

//prevent sql injection
if (strpos($_SESSION['quiz'], '\'') !== false) {
    unset($_SESSION['quiz']);
    header('Location: home.php');
}

//check if student has already submitted this quiz
$query = "SELECT * FROM Submission WHERE student_id = \"" . $_SESSION['user'] . "\" AND quiz_id = " . $_SESSION['quiz'];
$result = $db->query($query);

//Create an array to hold all the returned questions
$subArray = array();

//Add all the questions from the result to the questions array
while ($row = $result->fetch_assoc()) {
    $subArray[] = $row;
}

//no submissions on this quiz yet
if (sizeof($subArray) == 0) {
    //Create a query to fetch all the questions
    $query = "SELECT * FROM Question WHERE quiz_id = " . $_SESSION['quiz'];


    $result = $db->query($query);

    //Create an array to hold all the returned questions
    $questionsArray = array();

    //Add all the questions from the result to the questions array
    while ($row = $result->fetch_assoc()) {
        $questionsArray[] = $row;
    }
    ?>
    <div id="status"></div>
    <form id="myquiz" action="submit.php" method="post"><?php

        foreach ($questionsArray as $question) {
            ?> <h3> <?php echo $question['text']; ?> </h3>
            <?php

            if ($question['question_type'] == "FR") { //free response
                ?> <input type="text" name="<?php echo $question['question_id']; ?>"><?php
            } else { //multiple choice
                $query = "SELECT * FROM Answer WHERE question_id = " . $question['question_id'] . " ORDER BY RAND()";

                $result = $db->query($query);

                //Create an array to hold all the returned questions
                $answersArray = array();

                //Add all the questions from the result to the questions array
                while ($row = $result->fetch_assoc()) {
                    $answersArray[] = $row;
                }
                foreach ($answersArray as $answer) {
                    if ($question['question_type'] == "MC") {
                        ?> <input type="radio" name="<?php echo $question['question_id']; ?>"
                                  value="<?php echo $answer['answer_id']; ?>,<?php echo $answer['text']; ?>"/>
                        <?php echo $answer['text']; ?><br/> <?php

                    }
                }
            }
        }

        ?><br/><br/>
        <input type='submit' value='submit' name="submit"/>
    </form>
    <script type="text/javascript">
        secs = 60*5;
        timer = setInterval(function () {
            var element = document.getElementById("status");
            element.innerHTML = "<h2>You have <b>" + secs + "</b> seconds</h2>";
            if (secs < 1) {
                clearInterval(timer);
                document.getElementById('myquiz').submit();
            }
            secs--;
        }, 1000)
    </script>
    <?php
} else { //student already submitted, show submission/answers but only after deadline
    $query = "SELECT deadline FROM Quiz WHERE quiz_id = " . $_SESSION['quiz'];
    $deadline = $db->query($query)->fetch_assoc();
    $the_date = \DateTime::createFromFormat('m/d/y', $deadline['deadline']);
    $now = new \DateTime();
    if ($the_date < $now) { //past deadline, can show answers
        foreach ($subArray as $sub) {
            //get question
            $query = "SELECT text FROM Question WHERE question_id = " . $sub['question_id'];
            $result = $db->query($query)->fetch_assoc();
            ?> <h3> <?php echo $result['text']; ?> </h3> <?php

            echo "You answered: " . $sub['answer'] . "<br/>";

            //get correct answer
            $correctAns = array();
            $query = "SELECT * FROM Answer WHERE question_id = " . $sub['question_id'] . " AND points <> 0";
            $result = $db->query($query);
            while ($row = $result->fetch_assoc()) {
                $correctAns[] = $row;
            }

            //find the answer that yield's the max points aka the correct answer
            $maxPoints = 0;
            foreach ($correctAns as $correct) {
                if ($correct['points'] > $maxPoints) {
                    $maxPoints = $correct['points'];
                }
            }

            //print correct answer
            foreach ($correctAns as $correct) {
                if ($correct['points'] == $maxPoints) {
                    echo "Correct answer: " . $correct['text'] . "<br/>";
                }
            }

            //other answers
            $ans = array();
            $query = "SELECT * FROM Answer WHERE question_id = " . $sub['question_id'] . " AND answer_id <> " . $sub['answer_id'] . " AND points <> " . $maxPoints;
            $result = $db->query($query);
            while ($row = $result->fetch_assoc()) {
                $ans[] = $row;
            }

            //print other answers
            foreach ($ans as $answer) {
                echo "Other answer: " . $answer['text'] . "<br/>";
            }
        }
    }

}


?>