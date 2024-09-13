<?php
session_start();
include("connections.php");
include("functions.php");
$user_data = check_login($con);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Registration Logic
  if (isset($_POST['user_name']) && isset($_POST['password']) && isset($_POST['user-type'])) {
    $username = $_POST['user_name'];
    $password = $_POST['password'];
    $user_type = $_POST['user-type'];

    if (!empty($username) && !empty($password)) {
      $user_id = random_num(20);
      $query = "insert into userss (user_id,user_name,password,user_type) values ('$user_id','$username','$password','$user_type')"; // Use password hashing
      mysqli_query($con, $query);
      echo "Registration successful!"; // Or redirect to a confirmation page
    } else {
      echo "Please enter some valid details";
    }
  } else {
    // Login Logic (assuming username and password fields exist in the form)
    if (($_POST['user_name1']) && isset($_POST['password1'])) {
      $username = $_POST['user_name1'];
      $password = $_POST['password1'];

      if (!empty($username) && !empty($password)) {
        $query = "select * from userss where user_name='$username' limit 1";
        $result = mysqli_query($con, $query);
        if($result){
          if($result && mysqli_num_rows($result)>0){
               $user_data=mysqli_fetch_assoc($result);
               if($user_data['password'] === $password){
                
                $_SESSION['user_name']  = $user_data['user_name'];
                $_SESSION['user_type'] = $user_data['user_type'];

                header("Location: page.php");
                die;
               }
          }
        }
      } else {
        echo "Please enter some valid details";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="project--title"><h1>AI Enhanced Educator</h1></div>
    <div class="wrapper">
      
        <div class="title-text">
          <div class="title login">Login Form</div>
          <div class="title signup">Signup Form</div>
        </div>
        <div class="form-container">
          <div class="slide-controls">
            <input type="radio" name="slide" id="login" checked>
            <input type="radio" name="slide" id="signup">
            <label for="login" class="slide login">Login</label>
            <label for="signup" class="slide signup">Signup</label>
            <div class="slider-tab"></div>
          </div>
          <div class="form-inner">
            <form action="#" method="post" class="login">
              <div class="field">
                <input type="text" placeholder="Email Address" required name="user_name1">
              </div>
              <div class="field">
                <input type="password" placeholder="Password" required name="password1">
              </div>
              <div class="pass-link"><a href="#">Forgot password?</a></div>
              <div class="field btn">
                <div class="btn-layer"></div>
                <input type="submit" value="Login">
              </div>
              <div class="signup-link">Not a member? <a href="">Signup now</a></div>
            </form>
            <form  class="signup" method="POST">
              <div class="field">
                <input type="text" placeholder="Email Address" required name="user_name">
              </div>
              <div class="field">
                <input type="password" placeholder="Password" required name="password">
              </div>
              <div class="field">
                <input type="password" placeholder="Confirm password" required>
              </div>
              <div class="field">
                <select name="user-type" id="user-type">
                    <option value="student">Student</option>
                    <option value="Parents/Guardians">Parents/Guardians</option>
                    <option value="Teachers/Educators">Teachers/Educators</option>
                    <option value="Administrators/School Staff">Administrators/School Staff</option>
                    <option value="Researchers/Developers">Researchers/Developers</option>
                    <option value="General Public">General Public</option>
                  </select>
              </div>
              <div class="field btn">
                <div class="btn-layer"></div>
                <input type="submit" value="Signup">
              </div>
            </form>
          </div>
        </div>
      </div>
<script src="main.js"></script>
</body>
</html>