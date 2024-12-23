<?php
if (!$isLoggedIn) {
    header('Location: /finance-tracker/');
}

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
}

include('./globalFunctions.php');

//sum all income data
$incomeSql = "SELECT sum(amount) as sum from income where user_id = '$userId';";
$result = mysqli_query($conn, $incomeSql);
$incomeSum = mysqli_fetch_all($result, MYSQLI_ASSOC);

$expenseSql = "SELECT sum(amount) as sum from expenses where user_id = '$userId';";
$result = mysqli_query($conn, $expenseSql);
$expenseSum = mysqli_fetch_all($result, MYSQLI_ASSOC);

$balance = getCurrentBalance($conn, $userId);

?>

<div class="layout">
    <?php include('./views/navbar.php') ?>

    <div class="container">
        <h1>Dashboard page</h1>

        <div class="row">
            <div class="col" id="dash-balance">Current balance: <?php echo intval($balance) ?></div>
            <div class="col">Income: <?php echo intval($incomeSum[0]['sum']) ?></div>
            <div class="col">Expenses: <?php echo intval($expenseSum[0]['sum']) ?></div>
        </div>

    </div>



</div>