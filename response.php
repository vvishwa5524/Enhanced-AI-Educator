<?php
session_start();
$usertype=$_SESSION['user_type'];
require "vendor/autoload.php";

use GeminiAPI\Client;
use GeminiAPI\Enums\MimeType;
use GeminiAPI\Resources\Parts\ImagePart;
use GeminiAPI\Resources\Parts\TextPart;

if (!empty($_POST)) { // Check for POST data (from AJAX)
  $text1 = $_POST['text'];
  $pretext ="User Role:  ".$usertype;
  $prompt = $text1;
  $note = "** generate the result acording to the user role or the according to the level og understanding of the user role ";
  $text = $pretext."the prompt is ".$prompt."  and remember ".$note;
    $client = new Client('AIzaSyABOA6IE0Brn_VUnFX5m8lGCr4N-vF5R90'); 
   
    $response = $client->geminiPro()->generateContent(
      new TextPart($text)
      
    );

    $output = $response->text();
    $output = str_replace('*', '', $output);
  echo $output; // Send output back to AJAX request
  exit;
}
else{
    $output = "not generated";
    echo $output;
    exit;
}
?>