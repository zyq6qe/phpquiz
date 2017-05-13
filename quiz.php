<?php
require "config.php";
require "header.php";


unset($_SESSION['quiz']);
if (isset($_GET['quiz'])) {
    $_SESSION['quiz'] = $_GET['quiz'];
} else {
    header('Location: home.php');
}

$quiz = $_SESSION['quiz'];

$student_id = $_SESSION['user'];

//crude way to prevent sql injection
if (strpos($_SESSION['quiz'], '\'') !== false) {
    unset($_SESSION['quiz']);
    header('Location: home.php');
}

//alphabet
$alphabet = range('A', 'Z');


//Create an array to hold all the returned submissions
//$accArray = array();
//while ($row = $result->fetch_assoc()) {
//    $accArray[] = $row;
//}

//check quiz open date
$query = "SELECT open_date FROM Quiz WHERE quiz_id = " . $quiz ;
$open = $db->query($query)->fetch_assoc();
$open_date = new DateTime($open['open_date']);

//check quiz close date
$query = "SELECT close_date FROM Quiz WHERE quiz_id = " . $quiz;
$cd = $db->query($query)->fetch_assoc();
$close_date = new DateTime($cd['close_date']);

//get current DateTime
$now = new \DateTime();

if ($open_date > $now) {
    header('Location: home.php');
}

//Create a query to fetch all the questions
$query = "SELECT * FROM Question WHERE quiz_id = " . $_SESSION['quiz'];
$result = $db->query($query);

//Create an array to hold all the returned questions
$questionsArray = array();

//Add all the questions from the result to the questions array
while ($row = $result->fetch_assoc()) {
    $questionsArray[] = $row;
}



/**************************************************************************************************************************
 **************************************************************************************************************************
 *
 * FORMATTING QUIZ TAKING DISPLAY
 *
 **************************************************************************************************************************
 **************************************************************************************************************************/

//it is after the open time and before the close date
if ($close_date > $now) {
    /**************************************************************************************************************************
     **************************************************************************************************************************
     *
     * PING DB ONCE PAGE IS LOADED
     *
     **************************************************************************************************************************
     **************************************************************************************************************************/

    require "timelimit.php";


    ?>

<!--    <div id="test"></div>-->

    <div id="status"></div>
    <div class="progress" id="progress">
        <div id="progressbardanger" class="progress-bar progress-bar-danger" role="progressbar" style="width:0%">
        </div>
    </div>
    <form id="myquiz" action="home.php" method="post" class="form-group"><?php

        echo "<ol>";
        foreach ($questionsArray as $question) {
            ?> <h3><li> <?php echo $question['text']; ?> </h3>
            <?php
            //check if student has already has a submission for this question
            $query = "SELECT answer_id, answer, comment FROM Submission WHERE student_id = \"" . $_SESSION['user'] . "\" AND quiz_id = " . $_SESSION['quiz'] . " AND question_id = " . $question['question_id'];
            $result = $db->query($query);

            //Create an array to hold all the returned submissions
            $qSub = array();
            while ($row = $result->fetch_assoc()) {
                $qSub[] = $row['answer_id'];
                $qSub[] = $row['answer'];
                $qSub[] = $row['comment'];
            }


            if ($question['question_type'] == "FR") { //free response
                ?> <input class="target" type="text" name="<?php echo $question['question_id']; ?>" value="<?php if (count($qSub) > 0) { echo $qSub[1]; } ?>"><?php
            } else { //multiple choice
                $query = "SELECT * FROM Answer WHERE question_id = " . $question['question_id'];

                $result = $db->query($query);

                //Create an array to hold all the returned answers
                $answersArray = array();

                //Add all the questions from the result to the answers array
                while ($row = $result->fetch_assoc()) {
                    $answersArray[] = $row;
                }

                //listing MC answers
                echo "<ol type='A'>";
                foreach ($answersArray as $answer) {
                    if ($question['question_type'] == "MC") { ?>
                        <li><input
                                <?php if (in_array($answer['answer_id'], $qSub)) { ?>checked<?php } ?>
                                class="target" type="radio" name="<?php echo $question['question_id']; ?>"
                                  value="<?php echo $answer['answer_id']; ?>,<?php echo $answer['text']; ?>"/>
                    <?php
                    } else if ($question['question_type'] == "MMC") { ?>
                        <li><input
                                <?php if (in_array($answer['answer_id'], $qSub)) { ?>checked<?php } ?>
                                class="target" type="checkbox" name="<?php echo $question['question_id']; ?>[]"
                                   value="<?php echo $answer['answer_id']; ?>,<?php echo $answer['text']; ?>"/>
                    <?php
                    }
                    echo $answer['text']."<br/>";


                }
                echo "</ol>";

            } ?>
            <br/><br/>Comment:<br/>
            <textarea class="target form-control" rows="5" name="comment,<?php echo $question['question_id']; ?>"><?php if (count($qSub) > 0) { echo $qSub[2]; } ?></textarea>
        <?php }
        echo "</ol>";

        ?><br/><br/>
        <input type='submit' value='submit'/>
<!--        <input type='submit' value='submit' name="submit"/>-->
    </form>

    <script type="text/javascript">
        /**************************************************************************************************************************
         **************************************************************************************************************************
         *
         * FORMATTING TIMING DISPLAY AND TIME CONSTRICTION OF QUIZ TAKING
         *
         **************************************************************************************************************************
         **************************************************************************************************************************/

        var secs = <?php echo $my_time_limit ?>;
        var totalsecs = <?php echo $my_total_limit ?>;
        timer = setInterval(function () {
            var element = document.getElementById("status");
            element.innerHTML = secs + "</b> seconds</h2>";

            var element2 = document.getElementById("progressbardanger");
            element2.style.width = 100 - (secs/totalsecs * 100) + "%";

            if (secs < 1) {
                clearInterval(timer);
                var update = document.getElementById("progress");
                element.innerHTML = "Time limit has passed. Your current answers have been recorded. Any further changes will not be accepted.";

            }
            secs--;
        }, 1000);



        /**************************************************************************************************************************
         **************************************************************************************************************************
         *
         * PING DATABASE ON ANY FORM CHANGE
         *
         **************************************************************************************************************************
         **************************************************************************************************************************/
        $(document).ready(function() {
            $('.target').change(function(){
                console.log("hello");
                $.ajax({
                    type: 'POST',
                    url: 'submit.php',
                    data: $('#myquiz').serialize(),
                    success: function(data){
                        console.log("success!");
                        $('#test').html(data);
                    }
                });
            });
        });

    </script>
    <?php
} else {
    /**************************************************************************************************************************
     **************************************************************************************************************************
     *
     * SHOWING QUIZ FEEDBACK
     *
     **************************************************************************************************************************
     **************************************************************************************************************************/
    if ($close_date < $now) { //past close date, can show answers
        echo "<ol>";


        foreach ($questionsArray as $question) {
            //get grade for this question
            $query = "SELECT grade FROM Grade WHERE student_id = \"" . $_SESSION['user'] . "\" AND quiz_id = " . $_SESSION['quiz'] . " AND question_id = " . $question['question_id'];
            $result = $db->query($query)->fetch_assoc();

            //PRINT QUESTION
            echo "<h3><li>" . $question['text'] . " (" . $result['grade'] . "/" . $question['points'] . ")</h3>";

            //check if student has already has a submission for this question
            $query = "SELECT answer_id, answer, comment FROM Submission WHERE student_id = \"" . $_SESSION['user'] . "\" AND quiz_id = " . $_SESSION['quiz'] . " AND question_id = " . $question['question_id'];
            $result = $db->query($query);

            //Create an array to hold all the returned submissions
            $qSub = array();
            while ($row = $result->fetch_assoc()) {
                $qSub[] = $row['answer_id'];
                $qSub[] = $row['answer'];
                $qSub[] = $row['comment'];
            }

            //get all answers for this question
            $ans = array();
            $query = "SELECT * FROM Answer WHERE question_id = " . $question['question_id'];
            $result = $db->query($query);
            while ($row = $result->fetch_assoc()) {
                $ans[] = $row;
            }

            if ($question['question_type'] == "FR") { //free response
                //find the correct answer
                $maxPoints = 0;
                $correctAns = array();
                $query = "SELECT * FROM Answer WHERE question_id = " . $question['question_id'] . " AND points > 0";
                $result = $db->query($query);
                while ($row = $result->fetch_assoc()) {
                    $correctAns[] = $row;
                }



                $correctAnsText = "";
                foreach ($correctAns as $correct) {
                    if ($correct['points'] > $maxPoints) {
                        $maxPoints = $correct['points'];
                        $correctAnsText = $correct['example_answer'];
                    }
                }
                ?>
                    <input class="target" type="text" name="<?php echo $question['question_id']; ?>"
                          value="<?php if (count($qSub) > 0) { echo $qSub[1]; } ?>">

                <h4>Key: <?php echo $correctAnsText . "</h4>";

            } else {
                $query = "SELECT * FROM Answer WHERE question_id = " . $question['question_id'];

                $result = $db->query($query);

                //Create an array to hold all the returned answers
                $answersArray = array();

                //Add all the questions from the result to the answers array
                while ($row = $result->fetch_assoc()) {
                    $answersArray[] = $row;
                }

                echo "<ol type='A'>";
                foreach ($answersArray as $answer) {
                    if ($question['question_type'] == "MC") { ?>
                        <li>(<?php echo $answer['points']; ?>) <input
                            <?php if (in_array($answer['answer_id'], $qSub)) { ?>checked<?php } ?>
                            class="target" type="radio" name="<?php echo $question['question_id']; ?>"
                            value="<?php echo $answer['answer_id']; ?>,<?php echo $answer['text']; ?>"/>
                        <?php
                    } else if ($question['question_type'] == "MMC") { ?>
                        <li>(<?php echo $answer['points']; ?>) <input
                            <?php if (in_array($answer['answer_id'], $qSub)) { ?>checked<?php } ?>
                            class="target" type="checkbox" name="<?php echo $question['question_id']; ?>[]"
                            value="<?php echo $answer['answer_id']; ?>,<?php echo $answer['text']; ?>"/>
                        <?php
                    }
                    echo $answer['text'] . "<br/>";


                }
                echo "</ol>";

                //GET CORRECT ANSWERS
                if ($question['question_type'] == 'MC') {
                    //get correct answer
                    $maxPoints = 0;
                    $correctAnsAlph = 0;
                    $currentAnsAlph = 0;
                    foreach ($answersArray as $ans) {
                        if ($ans['points'] > $maxPoints) {
                            $maxPoints = $ans['points'];
                            $correctAnsAlph = $currentAnsAlph;
                        }
                        $currentAnsAlph++;
                    } ?>

                    <h4>Key: <?php echo $alphabet[$correctAnsAlph] . "</h4>";
                } else if ($question['question_type'] == 'MMC') {
                    //get correct answer
                    $maxPoints = 0;
                    $correctAnsAlph = array();
                    $currentAnsAlph = 0;
                    foreach ($answersArray as $ans) {
                        if ($ans['points'] > 0) {
                            $correctAnsAlph[] = $currentAnsAlph;
                        }
                        $currentAnsAlph++;
                    } ?>

                    <h4>Key: <?php
                    for ($i = 0; $i < count($correctAnsAlph) - 1; $i++) {
                        echo $alphabet[$correctAnsAlph[$i]] . ", ";
                    }
                    echo $alphabet[$correctAnsAlph[count($correctAnsAlph) - 1]] . "</h4>";
                }
            } ?>
            <br/>Comment:<br/>
            <textarea class="target form-control" rows="5"><?php if (count($qSub) > 0) { echo $qSub[2]; } ?></textarea>
        <?php }
    }
}