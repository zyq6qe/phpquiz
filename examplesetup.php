<?php

require "config.php";

$db->query("drop table Quiz");
$db->query("drop table Question");
$db->query("drop table Answer");
$db->query("drop table Submission");
$db->query("drop table Grade");


$db->query("create table Quiz (quiz_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, text CHAR(255) NOT NULL, deadline CHAR(255) NOT NULL, time_limit INT NOT NULL, points INT NOT NULL)") or die ($db->error);
$db->query("create table Question (quiz_id INT, question_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, text CHAR(255) NOT NULL, points INT NOT NULL, question_type CHAR(255) NOT NULL, FOREIGN KEY(quiz_id) REFERENCES Quiz(quiz_id))") or die ($db->error);
$db->query("create table Answer (question_id INT, answer_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, text CHAR(255) NOT NULL, points INT NOT NULL, FOREIGN KEY(question_id) REFERENCES Question(question_id))") or die ($db->error);
$db->query("create table Submission (student_id CHAR(255) NOT NULL, quiz_id INT, question_id INT, answer_id INT, answer CHAR(255) NOT NULL, FOREIGN KEY(quiz_id) REFERENCES Quiz(quiz_id), FOREIGN KEY(question_id) REFERENCES Question(question_id), FOREIGN KEY(answer_id) REFERENCES Answer(answer_id))") or die ($db->error);
$db->query("create table Grade (student_id CHAR(255) NOT NULL, quiz_id INT, grade CHAR(255) NOT NULL, FOREIGN KEY(quiz_id) REFERENCES Quiz(quiz_id))") or die ($db->error);


$db->query("INSERT INTO Quiz VALUES(NULL, 'quiz1', '12/05/16', 5, 3)");
$db->query("INSERT INTO Quiz VALUES(NULL, 'quiz2', '12/10/16', 5, 5)");
$db->query("INSERT INTO Quiz VALUES(NULL, 'quiz3', '12/05/16', 5, 5)");

$db->query("INSERT INTO Question VALUES(1, NULL, 'q1', 1, 'MC')");
$db->query("INSERT INTO Question VALUES(1, NULL, 'q2', 1, 'MC')");
$db->query("INSERT INTO Question VALUES(1, NULL, 'q3', 1, 'MC')");

$db->query("INSERT INTO Question VALUES(2, NULL, 'ques1', 1, 'MC')");
$db->query("INSERT INTO Question VALUES(2, NULL, 'ques2', 1, 'MC')");
$db->query("INSERT INTO Question VALUES(2, NULL, 'ques3', 3, 'MC')");

$db->query("INSERT INTO Question VALUES(3, NULL, 'FR', 5, 'FR')");


$db->query("INSERT INTO Answer VALUES(1, NULL, 'T', 1)");
$db->query("INSERT INTO Answer VALUES(1, NULL, 'F', 0)");

$db->query("INSERT INTO Answer VALUES(2, NULL, 'a', 0)");
$db->query("INSERT INTO Answer VALUES(2, NULL, 'b', 1)");
$db->query("INSERT INTO Answer VALUES(2, NULL, 'c', 0)");

$db->query("INSERT INTO Answer VALUES(3, NULL, 'a', 0)");
$db->query("INSERT INTO Answer VALUES(3, NULL, 'b', 0)");
$db->query("INSERT INTO Answer VALUES(3, NULL, 'c', 1)");

$db->query("INSERT INTO Answer VALUES(4, NULL, 'T', 0)");
$db->query("INSERT INTO Answer VALUES(4, NULL, 'F', 1)");

$db->query("INSERT INTO Answer VALUES(5, NULL, 'yes', 0)");
$db->query("INSERT INTO Answer VALUES(5, NULL, 'no', 1)");

$db->query("INSERT INTO Answer VALUES(6, NULL, 'a', 3)");
$db->query("INSERT INTO Answer VALUES(6, NULL, 'b', 3)");

$db->query("INSERT INTO Answer VALUES(7, NULL, '/^0*11$/', 5)");
$db->query("INSERT INTO Answer VALUES(7, NULL, '/^0x0*b$/', 3)");
$db->query("INSERT INTO Answer VALUES(7, NULL, '/^0x0*B$/', 3)");
$db->query("INSERT INTO Answer VALUES(7, NULL, '/^.*$/', 1)");
