<?php
session_start();
include_once("projectstorage.php");
include_once("auth.php");
include_once("userstorage.php");
include_once("utils.php");

if (!isset($_GET["id"]))
    redirect("index.php");


$cs = new Storage(new JsonIO("categories.json"));
$us = new UserStorage();
$id = $_GET["id"];
$ps = new ProjectStorage();
$project = $ps->findById($id);
if (!$project)
    redirect("index.php");

$owner = $us->findById($project["owner"]);
if (!$owner)
    die("Error: creator not found");

$canEdit = isset($_SESSION["user"]) && ($project["owner"] === $_SESSION["user"]["id"] || in_array("admin", $_SESSION["user"]["roles"]));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $project["title"] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <div class="container">
            <div class="header-content">
                <h1>Budapest Project Voting</h1>
                <div class="nav-links">
                    <a href="index.php" class="btn btn-primary">Back to Projects</a>
                </div>
            </div>
        </div>
    </header>


    <div class="container">
        <h1>View the details of <i><?= $project["title"] ?></i></h1>
        <table>
            <?php if ($project["image"] != NULL): ?>
                <tr>
                    <td colspan="2"><img src="<?= $project["image"] ?>"></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td>Description:</td>
                <td><?= $project["description"] ?></td>
            </tr>
            <tr>
                <td>Category:</td>
                <td><?= $cs->findById($project["category"]) ?></td>
            </tr>
            <tr>
                <td>Postal code:</td>
                <td><?= $project["postal_code"] ?></td>
            </tr>
            <tr>
                <td>Creator:</td>
                <td><?= $owner["username"] ?></td>
            </tr>
            <tr>
                <td>Submission date:</td>
                <td><?= $project["submitted"] ?></td>
            </tr>
            <?php if ($project["approved"] != NULL): ?>
                <tr>
                    <td>Approval date:</td>
                    <td><?= $project["approved"] ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>