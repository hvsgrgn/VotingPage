<?php
session_start();
include_once("userstorage.php");
include_once("auth.php");
include_once("utils.php");

$us = new UserStorage();
$auth = new Auth($us);


//this function validates both passwords and the username, returning the array of errors with the potential errormessages to display
function validate($post, &$data, &$errors): bool
{
  $data = [
    'username' => trim($post['username'] ?? ''),
    'password' => $post['password'] ?? '',
    'password2' => $post['password2'] ?? '',
    'email' => trim($post['email'] ?? '')
  ];

  //Checking if username exists
  if ($data['username'] === '')
    $errors['username'] = 'A username is required';

  //use of strpos: find the first occurence of a string inside a string. return false if no match was found (we want to see this)
  else if (strpos($data["username"], ' ') !== false)
    $errors['username'] = "The username must not contain Whitespace";

  // Checking if password exists
  if ($data['password'] === '')
    $errors['password'] = 'A password is required';
  else {
    if (strlen($data['password']) < 8)
      $errors['password'] = 'The password must be at least 8 characters long';
    if (!preg_match('/[a-z]/', $data['password']))
      $errors['password'] = 'Password must contain a lowercase letter';
    if (!preg_match('/[A-Z]/', $data['password']))
      $errors['password'] = 'Password must contain an uppercase letter';
    if (!preg_match('/[0-9]/', $data['password']))
      $errors['password'] = 'Password must contain a digit';
    if ($data['password'] !== $data['password2'])
      $errors['password2'] = 'The passwords should match';
  }

  if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email format';
  }

  return count($errors) === 0;
}


$errors = [];
$data = [];

//if the sign up hasnt been attempted yet, this doesnt run. otherwise, we check if we already have the user to avoid double registration
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    if ($auth->user_exists($data['username'])) {
      $errors['global'] = "This username is already taken. Choose another one or sign in!";
    } else {
      $auth->register($data);
      redirect('login.php');
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign up</title>
  <style>
    input~span {
      color: red;
      font-size: smaller;
    }
  </style>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <header>
    <div class="container">
      <div class="header-content">
        <h1>Budapest Project Voting</h1>
        <div class="nav-links">
          <a href="index.php" class="btn btn-primary">Back to Home</a>
        </div>
      </div>
    </div>
  </header>



  <div class="container">
    <h1>Create a new profile!</h1>
    <?php if (isset($errors['global'])) : ?>
      <p><span class="error"><?= $errors['global'] ?></span></p>
    <?php endif; ?>
    <form action="" method="post">
      <div>
        <label for="username">Username: </label><br>
        <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>">
        <?php if (isset($errors['username'])) : ?>
          <span class="error"><?= $errors['username'] ?></span>
        <?php endif; ?>
      </div>
      <div>
        <label for="email">Email: </label>
        <input type="email" name="email" id="email" value="<?= $_POST['email'] ?? "" ?>">
        <?php if (isset($errors['email'])) : ?>
          <span class="error"><?= $errors['email'] ?></span>
        <?php endif; ?>
      </div>
      <div>
        <label for="password">Password: </label><br>
        <input type="password" name="password" id="password">
        <?php if (isset($errors['password'])) : ?>
          <span class="error"><?= $errors['password'] ?></span>
        <?php endif; ?>
      </div>
      <div>
        <label for="password2">Repeat your password: </label><br>
        <input type="password" name="password2" id="password2">
        <?php if (isset($errors['password2'])) : ?>
          <span class="error"><?= $errors['password2'] ?></span>
        <?php endif; ?>
      </div>

      <button>Sign up</button>
    </form>
  </div>
</body>

</html>