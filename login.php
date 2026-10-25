<?php
session_start();
include('auth.php');
include_once("userstorage.php");
include('utils.php');

function validate($post, &$data, &$errors): bool
{
  $data = [
    'username' => trim($post['username'] ?? ''),
    'password' => $post['password'] ?? '',
  ];

  if ($data['username'] === '') {
    $errors['username'] = 'Pleaser enter your username';
  }

  if ($data['password'] === '') {
    $errors['password'] = 'Please enter your password';
  }

  return count($errors) === 0;
}

$user_storage = new UserStorage();
$auth = new Auth($user_storage);


$data = [];
$errors = [];
//If the sign-in hasnt been attempted yet, this wont run
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    //attempting authentication via the auth class
    $auth_user = $auth->authenticate($data['username'], $data['password']);
    if (!$auth_user) {
      $errors['global'] = "Invalid username or password";
    } else {
      $auth->login($auth_user);
      if (isset($_GET["redirect_url"])) {
        redirect($_GET["redirect_url"]);
      } else {
        redirect('index.php');
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
  <title>Sign in</title>
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
    <h1>Sign in</h1>
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
        <label for="password">Password: </label><br>
        <input type="password" name="password" id="password">
        <?php if (isset($errors['password'])) : ?>
          <span class="error"><?= $errors['password'] ?></span>
        <?php endif; ?>
      </div>
      <div>
        <button type="submit">Login</button>
      </div>
    </form>
    <p>
      You dont have an account yet?
      <a href="signup.php">Sign up!</a>
    </p>
  </div>
</body>

</html>