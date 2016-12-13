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





