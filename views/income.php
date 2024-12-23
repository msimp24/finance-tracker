<?php
if (!$isLoggedIn) {
    header('Location: /finance-tracker/');
}

$errors = array('source' => '', 'amount' => '', 'date' => '', 'notes' => '');

$source = '';
$amount = '';
$date = '';
$notes = '';
$userId = '';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
}

if (isset($_POST['submit'])) {


    if ($_POST['source'] === '') {
        $errors['source'] = 'Source is a required field';
    } else {
        $source = $_POST['source'];
    }

    if ($_POST['amount'] === '') {
        $errors['amount'] = 'Amount is a required field';
    } else {
        $amount = $_POST['amount'];

        if (!is_numeric($amount)) {
            $errors['amount'] = 'Amount must be a numerical value';
        }
    }
    if ($_POST['date'] === '') {
        $errors['date'] = 'Date is a required field';
    } else {
        $date = $_POST['date'];
    }
    if ($_POST['notes'] === '') {
        $errors['notes'] = 'Notes is a required field';
    } else {
        $notes = $_POST['notes'];
    }


    if (!array_filter($errors)) {

        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : null;


        if ($id) {
            $sql = "UPDATE income SET source = '$source', amount = '$amount', income_date = '$date', notes = '$notes' WHERE income_id = '$id'";
        } else {
            $sql = "INSERT INTO income (source, user_id ,amount, income_date, notes) values ('$source', '$userId' ,'$amount', '$date', '$notes');";
        }
    }

    if (mysqli_query($conn, $sql)) {
        header('Location: /finance-tracker/income');
        exit;
    } else {
        echo 'query error:' . mysqli_error($conn);
    }
}



//retrieve all income transactions in the database based on the user's id

$incomeSql = "SELECT * FROM income where user_id = '$userId' order by income_date;";
$result = mysqli_query($conn, $incomeSql);
$incomes = mysqli_fetch_all($result, MYSQLI_ASSOC);

//sum all income data
$sumSql = "SELECT sum(amount) as sum from income where user_id = '$userId';";
$result = mysqli_query($conn, $sumSql);
$sum = mysqli_fetch_all($result, MYSQLI_ASSOC);


?>

<div class="layout">
    <?php include('./views/navbar.php') ?>
    <div class="container">
        <div class="modal" id="income-modal">
            <form method="POST" class="login-form income-form">
                <input type="hidden" name="id" id="incomeId">
                <i class="fa-solid fa-x" id="income-close"></i>
                <h1>Add Income</h1>
                <label for="source">Source</label>
                <input type="text" name="source" class="login-input" id="income-source">
                <p class="error-text" id="source-error">
                    <?php echo $errors['source'] ?>
                </p>
                <label for="amount">Amount</label>
                <input type="text" name="amount" class="login-input" id="income-amount">
                <p class="error-text" id="amount-error">
                    <?php echo $errors['amount'] ?>
                </p>
                <label for="date">Date</label>
                <input class="login-input" type="date" name="date" id="income-date">
                <p class="error-text" id="date-error">
                    <?php echo $errors['date'] ?>
                </p>
                <label for="notes">Notes</label>
                <input class="login-input" type="text" name="notes" id="income-notes">
                <p class="error-text" id="notes-error">
                    <?php echo $errors['notes'] ?>
                </p>
                <div class="center">
                    <input class="login-button button" type="submit" name="submit" value="Submit" id="submit">
                </div>
            </form>
        </div>

        <div class="top-row">
            <h1>Income</h1>
            <button id="income-btn" class="button income-btn">Add Income</button>
        </div>

        <div class="row">
            <table class="table">
                <tr>
                    <th>Source</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Notes</th>
                    <th></th>
                </tr>
                <?php foreach ($incomes as $income): ?>
                    <tr>
                        <th><?php echo $income['source'] ?></th>
                        <th>£<?php echo $income['amount'] ?></th>
                        <th><?php echo $income['income_date'] ?></th>
                        <th><?php echo $income['notes'] ?></th>
                        <th class="icon-container">
                            <i class="fa-regular fa-pen-to-square income-edit table-icon" id="<?php echo $income['income_id'] ?>"></i>
                            <i class="fa-solid fa-trash income-delete table-icon" id="<?php echo $income['income_id'] ?>"></i>
                        </th>
                    </tr>
                <?php endforeach ?>
            </table>

        </div>
        <?php if ($sum[0]['sum'] > 0): ?>
            <h3>Total Income: £<?php echo $sum[0]['sum'] ?></h3>
        <?php endif ?>
    </div>
</div>