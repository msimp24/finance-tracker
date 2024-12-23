<?php

include('./config/db_connect.php');

function getCurrentBalance($conn, $userId)
{

    $incomeSql = "SELECT sum(amount) as sum from income where user_id = '$userId';";
    $result = mysqli_query($conn, $incomeSql);
    $incomeSum = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $expenseSql = "SELECT sum(amount) as sum from expenses where user_id = '$userId';";
    $result = mysqli_query($conn, $expenseSql);
    $expenseSum = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $incomeSum[0]['sum'] - $expenseSum[0]['sum'];
}
