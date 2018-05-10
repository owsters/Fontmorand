<?php
// check for form submission - if it doesn't exist then send back to contact form  
if (!isset($_POST["save"]) || $_POST["save"] != "contact") {  
    header("Location: contact.php"); exit;  
}
/* Set e-mail recipient */
$myemail = "contact@fontmorand.fr";

/* Check all form inputs using check_input function */
$name = $_POST['name'];
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];
$headers = 'From:contact@fontmorand.fr';

// check that a name was entered  
if (empty ($name))  
    $error .= "You must enter your name.</br>";  
// check that an email address was entered  
if (empty ($email))   
    $error .= "You must enter your email address.</br>";  
// check for a valid email address  
if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email))  
    $error .= "You must enter a valid email address.</br>"; 
// check that a subject was entered
if (empty ($subject))  
    $error .= "You must enter a subject.</br>";
// check that a message was entered  
if (empty ($message))  
    $error .= "You must enter a message.</br>";  
          
// check if an error was found - if there was, send the user back to the form  
if (isset($error)) {  
    header("Location: contact.php?e=".urlencode($error)); exit;  
}  

/* Let's prepare the message for the e-mail */

$message = "Name: $name
Email: $email
Subject: $subject

Message:
$message

";

/* Send the message using mail() function */
$subject = "contact from fontmorand";
mail($myemail, $subject, $message, $headers);

// send the user back to the form  
header("Location: contact.php?s=".urlencode("Thank you. Your message has been sent successfully!")); exit;

?>