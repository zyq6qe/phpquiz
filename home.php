<?php

require "config.php";
require "header.php";


unset($_SESSION['quiz']);

?>

<h1>Quizzes</h1>

<?php

if ($_SESSION['user'] == "admin") { ?>
    <a href="create.php" class="btn btn-primary">Create New Quiz</a>
    <a href="download.php" class="btn btn-primary">Download All Grades</a><br/><br/>
    <?php
}

//Fetch all quizzes
$query = "SELECT * FROM Quiz";


$result = $db->query($query);

//hold all returned quizzes
$quizArray = array();

//Add all the quizzes to the quiz array
while ($row = $result->fetch_assoc()) {
    $quizArray[] = $row;
}
?>

<table class="table table-bordered">
    <tr>
        <th>Quiz</th>
        <th>Open Date</th>
        <th>Close Date</th>
        <th> Grade </th>
        <?php if ($_SESSION['user'] == "admin") { ?>
            <th> Action </th>
        <?php } ?>
    </tr>

<?php foreach ($quizArray as $quiz) { ?>


    <tr>
        <td>
            <a href="quiz.php?quiz=<?php echo $quiz['quiz_id'] ?>"> <?php echo $quiz['text'] ?></a>
        </td>
        <td><?php echo $quiz['open_date'] ?></td>
        <td><?php echo $quiz['close_date'] ?></td>
        <td>
            <?php

            //Fetch overall grades from Grade
            $query = "SELECT SUM(Grade) AS QuizGrade FROM Users NATURAL JOIN Quiz NATURAL JOIN Grade WHERE student_id = '" . $_SESSION['user'] . "' AND quiz_id = " . $quiz['quiz_id'] . " GROUP BY student_id, quiz_id";
            $result = $db->query($query)->fetch_assoc();

            if (sizeof($result) == 0) {
                echo "Not taken";
            } else if (new DateTime() > new DateTime($quiz['close_date'])) {
                echo $result['QuizGrade'] . "/" . $quiz['points'];
            } else {
                echo "Grades shown after close date";
            }
            ?>
        </td>
        </td>

        <?php
        if ($_SESSION['user'] == "admin") { ?>
        <td>
            &emsp;<a href="edit.php?quiz=<?php echo $quiz['quiz_id'] ?>" class="btn btn-danger">Edit</a>
             <a href="download.php?quiz=<?php echo $quiz['quiz_id'] ?>" class="btn btn-primary">Download Grade</a>

        </td>
        <?php } ?>
    </tr>
<?php } ?>

</table>
