<?php
session_start();

// initializing variables'
$fname = "fname";
$lname = "lname";
$email    = "email";

$phone = "phone";
$password="password";



// connect to the database
$db = mysqli_connect('localhost','root','','invoice');

// REGISTER USER
if (isset($_POST['reg_user']))
 {
  // receive all input values from the form
  $fname= mysqli_real_escape_string($db, $_POST['fname']);
  $lname= mysqli_real_escape_string($db, $_POST['lname']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $phone = mysqli_real_escape_string($db, $_POST['phone']);
 
  $password= mysqli_real_escape_string($db, $_POST['password']);
  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($fname)) { array_push($errors, "First Name is required"); }
  if (empty($lname)) { array_push($errors, "Last Name is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($phone)) { array_push($errors, "Phone No. is required"); }
  
  if (empty($password)) { array_push($errors, "Password is required"); }
    

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM users WHERE email='$email' or phone='$phone' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) 
  { // if user exists
    if ($user['phone'] === $phone) {
      array_push($errors, "Phone no. already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) 
  {
    $password = md5($password);//encrypt the password before saving in the database

    $query = "INSERT INTO users (fname,lname,email,phone,password) 
          VALUES('$fname','$lname','$email','$phone','$password')";
    mysqli_query($db, $query);
    $_SESSION['fname'] = $fname;
    $_SESSION['success'] = "You are now logged in";
    header('location: invoice/index.html');
  }
  }
if (isset($_POST['login_user']))
 {
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($email)) {
    array_push($errors, "Email is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
    $password = md5($password);
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
  
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) == 1) {
      $_SESSION['email'] = $email;
      $_SESSION['success'] = "You are now logged in";
      header('location:  invoice/index.html');
    }else {
      array_push($errors, "Wrong username/password combination");
    }
  }
}
?>