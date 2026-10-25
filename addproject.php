<?php
session_start();
include_once("projectstorage.php");
include_once("utils.php");
$ps = new ProjectStorage();
$cs = new Storage(new JsonIO("categories.json"));
$categories = $cs->findAll([]);

if (!isset($_SESSION["user"])) {
  redirect("login.php");
}
$owner = $_SESSION["user"]["id"];


function validate($post, &$data, &$errors): bool{

  $data = [
    'title' => trim($post['title'] ?? ''),
    'description' => trim($post['description'] ?? ''),
    'postal_code' => $post['postal_code'] ?? '',
    'image' => trim($post['image'] ?? '')
  ];

  if (strlen($data["title"]) < 10)
    $errors["title"] = "The title must be at least 10 characters long";

  if (strlen($data["description"]) < 150)
    $errors["description"] = "The project description must be at least 150 characters long";

  if ($data["postal_code"] < 1000 || $data["postal_code"] > 9999)
    $errors["postal_code"] = "The postal code must be 4 digits long";

  else if ($data["postal_code"] == 1007) {} 

  else if (!preg_match('/^1(0[1-9]|1[0-9]|2[0-3])[1-9]$/', $data["postal_code"]))
    $errors["postal_code"] = "Invalid Budapest postal code format";

  if (!empty($data['image']) && !filter_var($data['image'], FILTER_VALIDATE_URL))
    $errors['image'] = "Invalid URL format";

  if (empty($data['image']))
    $data['image'] = "https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg?w=360";

  return count($errors) === 0;
}

$errors = [];
$data = [];

if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    $data["owner"] = $owner;
    $data["status"] = 2; // TODO: for extra points, make it pending and not immediately approved
    $data["category"] = (int)$_POST["category"];
    $data["submitted"] = Date("Y-m-d") . ' ' . date("H:i");
    $data["approved"] = $data["submitted"]; //TODO
    $id = $ps->add($data);
    header("Location: project.php?id={$id}");
    exit();
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add your project</title>
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
    <h1>Send in your idea!</h1>

    <form action="" method="post">
      <label for="title">Title: </label>
      <input type="text" name="title" id="name" value="<?= $_POST['title'] ?? "" ?>">
      <?php if (isset($errors['title'])) : ?>
        <span class="error"><?= $errors['title'] ?></span>
      <?php endif; ?><br>


      <label for="description">Description: </label>
      <textarea name="description" id="description"><?= $_POST['description'] ?? "" ?></textarea>
      <?php if (isset($errors['description'])) : ?>
        <span class="error"><?= $errors['description'] ?></span>
      <?php endif; ?> <br>


      <label for="postal_code">Postal code: </label>
      <input type="number" name="postal_code" id="postal_code" value="<?= $_POST['postal_code'] ?? "" ?>">
      <?php if (isset($errors['postal_code'])) : ?>
        <span class="error"><?= $errors['postal_code'] ?></span>
      <?php endif; ?> <br>


      <label for="category">Category: </label>
      <select id="op" name="category">
        <?php foreach ($categories as $k => $v): ?>
          <option value="<?= $k ?>"><?= $v ?></option>
        <?php endforeach ?>
      </select> <br>

      <label for="image">Illustrative image: </label>
      <input type="url" name="image"> <br>

      <button>Submit project</button>
    </form>
  </div>
</body>

</html>