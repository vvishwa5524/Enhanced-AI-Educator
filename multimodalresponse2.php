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
  $pretext ="User Role: ".$usertype;
  $prompt = $text1;
  $note = "** generate the result acording to the user role or the according to the level og understanding of the user role ";
  $text = $pretext."the prompt is ".$prompt."  and remember ".$note;
  $imageContent = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
  $mimeType = $_FILES['image']['type'];

  // Validate image type (optional)
  if ($mimeType !== 'image/jpeg') {
    $output = "Only JPEG images are supported.";
  } else {
    $client = new Client('AIzaSyABOA6IE0Brn_VUnFX5m8lGCr4N-vF5R90'); // Replace with your actual API key

    $response = $client->geminiProVision()->generateContent(
      new TextPart($text),
      new ImagePart(
        MimeType::from($mimeType),
        $imageContent
      ),
    );

    $output = $response->text();
  }
  echo $output; // Send output back to AJAX request
  exit;
}
?>
