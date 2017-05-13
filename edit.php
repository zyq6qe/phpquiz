<?php

require "config.php";
require "header.php";


unset($_SESSION['quiz']);
if (isset($_GET['quiz'])) {
    $_SESSION['quiz'] = $_GET['quiz'];
}

$user = $_SESSION['user'];

if (!in_array($user, $admins)) {
    header('Location: home.php');
}

?>

<script type="text/javascript">
    function checkEnter(e){
        e = e || event;
        var txtArea = /textarea/i.test((e.target || e.srcElement).tagName);
        return txtArea || (e.keyCode || e.which || e.charCode || 0) !== 13;
    }

    var i = 1;
    function addQ(){
        var div = document.createElement('div');
        div.setAttribute('id', '' + i);
        div.innerHTML =
            '<input type="hidden" name="input[]" value="QUESTION">' +
            '<h3> Question </h3>' +
            '<a href="#" onclick="remove(' + i + '); return false;">Remove Question</a><br/>' +
            'Text:' +
            '<textarea name="input[]" required></textarea>' +
            '# of Points:' +
            '<input type="text" name="input[]" required>' +
            'Question type:' +
            '<select name="input[]">' +
            '<option value="MC">Multiple Choice</option>' +
            '<option value="MMC">Multiple Multiple Choice</option>' +
            '<option value="FR">Free Response</option>' +
            '</select>' +
            'Base point value:' +
            '<input type="text" name="input[]" required> <br/>' +
            '<a href="#" onclick="addA(' + i + '); return false;">Add Answer</a>';
        document.getElementById('questionarea').appendChild(div);
        i++;
    }

    function addA(qid){
        var div = document.createElement('div');
        div.setAttribute('id', '' + i);
        div.innerHTML =
            '<input type="hidden" name="input[]" value="ANSWER">' +
            '<h4> Answer </h4>' +
            '<a href="#" onclick="remove(' + i + '); return false;">Remove Answer</a><br/>' +
            'Text:' +
            '<textarea name="input[]" required></textarea>' +
            '# of Points:' +
            '<input type="text" name="input[]" required>' +
            'Example of Answer:' +
            '<textarea name="input[]" required>null</textarea><br/>';
        document.getElementById(qid).appendChild(div);
        i++;
    }

    function remove(i) {
        var element = document.getElementById(i);
        element.outerHTML = "";
    }

    function removeOld(i) {
//    <input type="hidden" name="input[]" value="OLDQUESTION">
        var div = document.createElement('div');

        var input = i.split(',');
        if (input[0] == "OLDQ") {
            div.innerHTML =
                '<input type="hidden" name="deleteQ[]" value="' + input[1] + '">';
        } else if (input[0] == "OLDA") {
            div.innerHTML =
                '<input type="hidden" name="deleteA[]" value="' + input[1] + '">';
        }
        document.getElementById("deleted").appendChild(div);
        var element = document.getElementById(i);
        element.outerHTML = "";
    }
</script>

<h1> Edit This Quiz </h1>

<?php
//Create a query to fetch info of this quiz
$query = "SELECT * FROM Quiz WHERE quiz_id = " . $_SESSION['quiz'];
$result = $db->query($query)->fetch_assoc();

//compare now to open date
$open_date = new DateTime($result['open_date']);
$now = new \DateTime();

if ($now > $open_date) {
    echo "<h3>It is currently past the open date</h3>";
}
?>

<form id="edit" action="editsubmit.php" method="post">
    <div id="deleted">
<!--        WILL HOLD ALL HIDDEN RECORDS OF DELETED Q/A-->
    </div>
    Title:
    <input type="text" name="input[]" value="<?php echo $result['text']?>"><br/>
    Open Date (MM/DD/YY):
    <input type="text" name="input[]" value="<?php echo $result['open_date']?>"><br/>
    Close Date (MM/DD/YY):
    <input type="text" name="input[]" value="<?php echo $result['close_date']?>"><br/>
    Time limit (in minutes):
    <input type="text" name="input[]" value="<?php echo $result['time_limit']?>"><br/>

<?php

//Create a query to fetch all the questions
$query = "SELECT * FROM Question WHERE quiz_id = " . $_SESSION['quiz'];
$result = $db->query($query);

//Create an array to hold all the returned questions
$questionsArray = array();

//Add all the questions from the result to the questions array
while ($row = $result->fetch_assoc()) {
    $questionsArray[] = $row;
}

//populate form with each question--iterate through
foreach ($questionsArray as $question) {
    ?>
    <div id="<?php echo "OLDQ," . $question['question_id']?>">
        <input type="hidden" name="input[]" value="OLDQUESTION">
        <h3>Question</h3>
        <?php if ($open_date > $now) {?>
        <a href="#" onclick="removeOld('<?php echo "OLDQ," . $question['question_id']?>'); return false;">Remove Question</a><br/>
        <?php } ?>
        <input type="hidden" name="input[]" value="<?php echo $question['question_id']?>">
        Question:
        <textarea name="input[]" required><?php echo $question['text']?></textarea>
        # of Points:
        <input type="text" name="input[]" value="<?php echo $question['points']?>">
        Base point value:
        <input type="text" name="input[]" value="<?php echo $question['base_points']?>">

        <h4>Answers:</h4>
        <?php
        //Create a query to fetch all the answers to this question
        $query = "SELECT * FROM Answer WHERE question_id = " . $question['question_id'];
        $result = $db->query($query);

        //Create an array to hold all the returned questions
        $answersArray = array();

        //Add all the questions from the result to the questions array
        while ($row = $result->fetch_assoc()) {
            $answersArray[] = $row;
        }

        //populate form with each answer--iterate through
        foreach ($answersArray as $answer) { ?>
            <br/>
            <div id="<?php echo "OLDA," . $answer['answer_id']?>">
                <input type="hidden" name="input[]" value="OLDANSWER">
                <input type="hidden" name="input[]" value="<?php echo $answer['answer_id']?>">
                Text:
                <textarea name="input[]" required><?php echo $answer['text']?></textarea>
                # of Points:
                <input type="text" name="input[]" value="<?php echo $answer['points']?>"> <br/>

                <?php if ($open_date > $now) {?>
                <a href="#" onclick="removeOld('<?php echo "OLDA," . $answer['answer_id']?>'); return false;">Remove Answer</a><br/>
                <?php } ?>
            </div>

            <?php
        } ?>
    </div>
    <?php
} ?>
    <!--       OLD QUESTIONS ------------------>

    <?php if ($open_date > $now) {?>
    <!--        QUESTION ------------------>
    <div id="questionarea">

    </div>
    <br/><br/>

    <a href="#" onclick="addQ(); return false;">Add Question</a>
    <br/><br/>
    <?php }?>

    <input type='submit' value='submit' name="submit"/> <br/><br/>
</form>

<script>
    document.querySelector('form').onkeypress = checkEnter;
</script>