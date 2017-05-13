<?php


require "config.php";
require "header.php";


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
            '<input type="text" name="input[]" value="0" required> <br/>' +
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
</script>

<h1> Create New Quiz </h1>
<form id="create" action="createsubmit.php" method="post">
    Title:
    <input type="text" name="input[]" required><br/>
    <input id="od" type="hidden" name="input[]" value="">
    <input id="cd" type="hidden" name="input[]" value="">
    <table class="table-condensed">
        <tr>
            <th></th>
            <th>MM</th>
            <th>/</th>
            <th>DD</th>
            <th>/</th>
            <th>YYYY</th>
            <th style="padding: 10px"></th>
            <th>HH</th>
            <th>:</th>
            <th>MM</th>
        </tr>
        <tr>
            <td>Open Date:</td>
            <td><input id="odMonth" type="text" maxlength="2" size="2" oninput="joinOD();" onpaste="joinOD();"></td>
            <td>/</td>
            <td><input id="odDate" type="text" maxlength="2" size="2" oninput="joinOD();" onpaste="joinOD();"></td>
            <td>/</td>
            <td><input id="odYear" type="text" maxlength="4" size="4"  oninput="joinOD();" onpaste="joinOD();"></td>
            <td></td>
            <td><input id="odHour" type="text" maxlength="2" size="2" oninput="joinOD();" onpaste="joinOD();"></td>
            <td>:</td>
            <td><input id="odMin" type="text" maxlength="2" size="2" oninput="joinOD();" onpaste="joinOD();"></td>
        </tr>
        <tr>
            <td>Close Date:</td>
            <td><input id="cdMonth" type="text" maxlength="2" size="2" oninput="joinCD();" onpaste="joinCD();"></td>
            <td>/</td>
            <td><input id="cdDate" type="text" maxlength="2" size="2" oninput="joinCD();" onpaste="joinCD();"></td>
            <td>/</td>
            <td><input id="cdYear" type="text" maxlength="4" size="4"  oninput="joinCD();" onpaste="joinCD();"></td>
            <td></td>
            <td><input id="cdHour" type="text" maxlength="2" size="2" oninput="joinCD();" onpaste="joinCD();"></td>
            <td>:</td>
            <td><input id="cdMin" type="text" maxlength="2" size="2" oninput="joinCD();" onpaste="joinCD();"></td>
        </tr>
    </table>
    <br/> Time limit (in minutes):
    <input type="text" name="input[]" required><br/>

    <!--        QUESTION ------------------>
    <div id="questionarea">
        <div id="original">
            <input type="hidden" name="input[]" value="QUESTION">
            <h3> Question </h3>
            Text:
            <textarea name="input[]" required></textarea>
            # of Points:
            <input type="text" name="input[]" required>
            Question type:
            <select name="input[]">
                <option value="MC">Multiple Choice</option>
                <option value="MMC">Multiple Multiple Choice</option>
                <option value="FR">Free Response</option>
            </select>
            Base point value:
            <input type="text" name="input[]" value="0" required> <br/>
            <a href="#" onclick="addA('original'); return false;">Add Answer</a>

            <!--        ANSWER ------------------>
            <div id="0">
                <input type="hidden" name="input[]" value="ANSWER">
                <h4> Answer </h4>
                <a href="#" onclick="remove(0); return false;">Remove Answer</a><br/>
                Text:
                <textarea name="input[]" required></textarea>
                # of Points:
                <input type="text" name="input[]" required>
                Example of Answer:
                <textarea name="input[]" required>null</textarea><br/>
            </div>
        </div>
    </div>
    <br/><br/>

    <a href="#" onclick="addQ(); return false;">Add Question</a>
    <br/><br/>
    <input type='submit' value='submit' name="submit"/>
</form>

<script>
    document.querySelector('form').onkeypress = checkEnter;
</script>

<script type="text/javascript">
    //format dates into YYYY-MM-DD HH:MM:SS eg. 2017-01-25 23:59:00
    function joinOD() {
        var year = document.getElementById('odYear').value;
        var month = document.getElementById('odMonth').value;
        var date = document.getElementById('odDate').value;
        var hour = document.getElementById('odHour').value;
        var min = document.getElementById('odMin').value;
        document.getElementById('od').value = year + "-" + month + "-" + date + " " + hour + ":" + min + ":00";
    }
    function joinCD() {
        var year = document.getElementById('cdYear').value;
        var month = document.getElementById('cdMonth').value;
        var date = document.getElementById('cdDate').value;
        var hour = document.getElementById('cdHour').value;
        var min = document.getElementById('cdMin').value;
        document.getElementById('cd').value = year + "-" + month + "-" + date + " " + hour + ":" + min + ":00";
    }
</script>