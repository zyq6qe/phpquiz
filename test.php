<?php
//open time closing time
//deadline
//if time limit interval exceeds deadline
//can retake within timelimit period
//per question how many points did i get

//```can't see correct answer
//```can see correct answers only after deadline
//```print out point value of "correct answer" or just max correct answer
//```show answers they didn't answer
//```.* - 1pt, 0*11 - 5pt, 0x0*b - 3pt multiple matching regex


//MMC grading policy
//treat as false selecting the right one or not selecting the right one
//comments
//ordering with answers when displaying final--some sort of enumeration
//how to display your answer vs key answer

//sql escape mysqli real escape string


$subject = '0x0B';
$pattern = '/^0x0*b$/';
preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE, 0);
print_r($matches);