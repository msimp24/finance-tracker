<?php
if (!$isLoggedIn) {
    header('Location: /finance-tracker/');
}

$name = "";
if (isset($_SESSION['user_id'])) {
    $name = $_SESSION['first_name'];
}

?>

<div class="layout">
    <?php include('./views/navbar.php') ?>
    <div class="container">
        <h1>Hello <?php echo $name ?></h1>

        <form method="POST">
            <input type="hidden" name="action" value="logout">
            <input type="submit" value="Logout" class="button login-button">
        </form>
    </div>
</div>