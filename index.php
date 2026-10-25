<?php
session_start();
include_once("auth.php");
include_once("userstorage.php");
include_once("utils.php");
include_once("projectstorage.php");
include_once("votestorage.php");
$ps = new ProjectStorage();
$cs = new Storage(new JsonIO("categories.json"));
$us = new UserStorage(new JsonIO("users.json"));
$vs = new VoteStorage();
$a = new Auth($us);
$categories = $cs->findAll([]);
$user = $a->authenticated_user();
$selectedCategory = $_GET["category"] ?? -1;

$hasVoted = function ($p) use ($user, $vs): bool {
    return $vs->findOne(['user' => $user['id'], 'project' => $p['id']]) != NUll;
};

$hasMaxPerCategory = function ($c) use ($vs, $user): bool{
    return count($vs->findAll(['user' => $user['id'], 'category' => $c])) >= 3;
};

if (isset($_POST['vote_action']) && $a->is_authenticated()) {
    $projectId = $_POST['project_id'];
    $userId = $user['id'];
    $category = $_POST['category'];
    
    $existingVote = $vs->findOne(['user' => $userId, 'project' => $projectId]);
    if (!$existingVote && $_POST['vote_action'] === 'vote') {

        $vs->add([
            'user' => $userId,
            'project' => $projectId,
            'category' => (int)$category
        ]);
    } else if ($existingVote &&$_POST['vote_action'] === 'unvote')
            $vs->delete($existingVote['id']);
    header("Location: index.php" . (isset($_GET['category']) ? "?category=" . $_GET['category'] : ""));
    exit();
}

$hasExpired = function ($date) :bool{
    $approved = new DateTime($date);
    $now = date_create('now');
    $diff = $approved->diff($now);
    
    return $diff->days >= 14;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <div class="container">
            <div class="header-content">
                <h1>Budapest Project Voting</h1>
                <div class="nav-links">
                    <?php if ($a->authenticated_user() !== NULL): ?>
                        <span class="text-white">Welcome, <?= $user["username"] ?>!</span>
                        <a href="addproject.php" class="btn btn-success">Submit Project</a>
                        <a href="logout.php" class="btn btn-secondary">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary">Login</a>
                        <a href="signup.php" class="btn btn-success">Sign up</a>
                    <?php endif;  ?>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="card">

            <h2>Approved Projects</h2>

            <div class="filter-section mb-4">
                <form action="" method="get" class="row">
                    <div class="col-md-4">
                        <label for="op" class="form-label">Filter by Category</label>
                        <select id="op" name="category" class="form-select" onchange="this.form.submit()">
                            <option value="-1">All categories</option>
                            <?php foreach ($categories as $k => $v): ?>
                                <option <?= $selectedCategory == $k ? "selected" : "" ?> value="<?= $k ?>"><?= $v ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </form>
            </div>


            <?php foreach ($categories as $k => $c) : ?>
                <?php if ($selectedCategory == -1 || $selectedCategory == $k): ?>
                    <?php
                    $projects =  $ps->findAll(['status' => 2, 'category' => $k]);
                    if (count($projects) !== 0): ?>
                        <h3> <?= $c ?></h3>
                        <?php if ($a->is_authenticated()):?>
                            <h4>Remaining votes: <?= 3 - count($vs->findAll(['user' => $user['id'], 'category' => $k])) ?><h4>
                        <?php endif;?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th class="text-center">Votes</th>
                                    <?php if ($a->is_authenticated()): ?>
                                        <th class="text-center">Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($projects as $p): ?>
                                    <tr>
                                        <td><a href="project.php?id=<?= $p["id"] ?>" class="project-title"><?= $p["title"] ?></a></td>
                                        <td class="text-center"><?= count($vs->findAll(["project" => $p["id"]])) ?></td>
                                        
                                        <?php if ($a->is_authenticated()): ?>
                                            <td class="text-center">
                                            <form action="" method="post" style="display: inline;">
                                                <input type="hidden" name="project_id" value="<?= $p['id'] ?>">
                                                <?php if (!$hasExpired($p["approved"]) && $hasVoted($p)): ?>
                                                    <input type="hidden" name="vote_action" value="unvote">
                                                    <button type="submit" class="btn btn-danger btn-sm">Remove Vote</button>
                                                <?php elseif(!$hasExpired($p["approved"])): ?>
                                                    <input type="hidden" name="vote_action" value="vote">
                                                    <input type="hidden" name="category" value=<?= $k ?>>
                                                    <button type="submit" class="btn btn-success btn-sm" <?= $hasMaxPerCategory($k) ? 'disabled' : '' ?>>Vote</button>
                                                <?php else:?>
                                                    <button type="submit" class="btn btn-success btn-sm" disabled>Voting Closed</button>
                                                <?php endif; ?>
                                            </form>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>