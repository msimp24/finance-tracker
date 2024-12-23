<?php
if (!$isLoggedIn) {
    header('Location: /finance-tracker/');
}

?>

<div class="layout">
    <?php include('./views/navbar.php') ?>
    <div class="container">
        <h1>Transactions page</h1>


    </div>
</div>