<?php

require "config.php";

//$db->query("drop table Users");
//$db->query("drop table Quiz");
//$db->query("drop table Question");
//$db->query("drop table Answer");
$db->query("drop table Submission");
//$db->query("drop table Grade");
//$db->query("drop table Accessed");



//$db->query("create table Users (student_id CHAR(255) NOT NULL PRIMARY KEY, time_mult FLOAT(10,2) NOT NULL DEFAULT '1.0')") or die ($db->error);
//$db->query("create table Quiz (quiz_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, text CHAR(255) NOT NULL, open_date CHAR(255) NOT NULL, close_date CHAR(255) NOT NULL, time_limit INT NOT NULL, points FLOAT(10,2) NOT NULL DEFAULT '0.0')") or die ($db->error);
//$db->query("create table Question (quiz_id INT, question_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, text CHAR(255) NOT NULL, points INT NOT NULL, question_type CHAR(255) NOT NULL, base_points INT NOT NULL, FOREIGN KEY(quiz_id) REFERENCES Quiz(quiz_id))") or die ($db->error);
//$db->query("create table Answer (question_id INT, answer_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, text CHAR(255) NOT NULL, points INT NOT NULL, example_answer CHAR(255), FOREIGN KEY(quiz_id) REFERENCES Quiz(quiz_id), FOREIGN KEY(question_id) REFERENCES Question(question_id))") or die ($db->error);
$db->query("create table Submission (student_id CHAR(255) NOT NULL, quiz_id INT, question_id INT, answer_id INT, answer CHAR(255), comment CHAR(255) NOT NULL DEFAULT ' ', FOREIGN KEY(quiz_id) REFERENCES Quiz(quiz_id), FOREIGN KEY(question_id) REFERENCES Question(question_id), FOREIGN KEY(answer_id) REFERENCES Answer(answer_id))") or die ($db->error);
//$db->query("create table Grade (student_id CHAR(255) NOT NULL, quiz_id INT, question_id INT, grade CHAR(255) NOT NULL, FOREIGN KEY(question_id) REFERENCES Question(question_id))") or die ($db->error);
//$db->query("create table Accessed (quiz_id INT, student_id CHAR(255) NOT NULL, access_time CHAR(255) NOT NULL, deadline CHAR(255) NOT NULL, FOREIGN KEY(quiz_id) REFERENCES Quiz(quiz_id))") or die ($db->error);





