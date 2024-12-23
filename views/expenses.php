<?php
if (!$isLoggedIn) {
    header('Location: /finance-tracker/');
}


$source = '';
$amount = '';
$date = '';
$notes = '';
$category = '';
$expenseId = '';

$errors = array('source' => '', 'amount' => '', 'date' => '', 'notes' => '', 'category' => '');


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
    if (!isset($_POST['category']) || trim($_POST['category']) === '') {
        $errors['category'] = 'Category is a required field.';
    } else {
        $category = trim($_POST['category']);
    }




    if (!array_filter($errors)) {


        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : null;


        if ($id) {
            $sql = "UPDATE expenses SET source = '$source', amount = '$amount', expense_date = '$date', notes = '$notes', category='$category' WHERE expense_id = '$id'";
        } else {
            $sql = "INSERT INTO expenses (source, user_id ,amount, expense_date, notes, category) values ('$source', '$userId' ,'$amount', '$date', '$notes', '$category');";
        }
    }

    if (mysqli_query($conn, $sql)) {
        header('Location: /finance-tracker/expenses');
        exit;
    } else {
        echo 'query error:' . mysqli_error($conn);
    }
}

$expenseSql = "SELECT * from expenses where user_id = '$userId';";
$result = mysqli_query($conn, $expenseSql);
$expenses = mysqli_fetch_all($result, MYSQLI_ASSOC);

//sum all income data
$sumSql = "SELECT sum(amount) as sum from expenses where user_id = '$userId';";
$result = mysqli_query($conn, $sumSql);
$sum = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<div class="layout">
    <?php include('./views/navbar.php') ?>
    <div class="container">
        <div class="modal" id="expense-modal">
            <form method="POST" class="login-form expense-form">
                <input type="hidden" name="id" id="expenseId">
                <i class="fa-solid fa-x" id="expense-close"></i>
                <h1>Add Expense</h1>
                <label for="source">Source</label>
                <input type="text" name="source" class="login-input" id="expense-source">
                <p class="error-text" id="sourceExp-error">
                    <?php echo $errors['source'] ?>
                </p>

                <label for="amount">Amount</label>
                <input type="text" name="amount" class="login-input" id="expense-amount">
                <p class="error-text" id="amountExp-error">
                    <?php echo $errors['amount'] ?>
                </p>
                <label for="date">Date</label>
                <input class="login-input" type="date" name="date" id="expense-date">
                <p class="error-text" id="dateExp-error">
                    <?php echo $errors['date'] ?>
                </p>
                <label for="notes">Notes</label>
                <input class="login-input" type="text" name="notes" id="expense-notes">
                <p class="error-text" id="notesExp-error">
                    <?php echo $errors['notes'] ?>
                </p>
                <label for="category">Category</label>
                <select name="category" id="expense-category" class="input-select">
                    <option disabled selected value="">-- Choose a category --</option>
                    <option value="housing">Housing</option>
                    <option value="transportation">Transportation</option>
                    <option value="food">Food</option>
                    <option value="utilities">Utilities</option>
                    <option value="insurance">Insurance</option>
                    <option value="healthcare">Healthcare</option>
                    <option value="savings">Savings/Investments</option>
                    <option value="personal">Personal Spending</option>
                    <option value="entertainment">Rec & Entertainment</option>
                    <option value="other">Other</option>
                </select>
                <p class="error-text" id="categoryExp-error">
                    <?php echo $errors['category'] ?>
                </p>
                <div class="center">
                    <input class="login-button button" type="submit" name="submit" value="Submit" id="submit">
                </div>
            </form>
        </div>

        <div class="top-row">
            <h1>Expense</h1>
            <button id="expense-btn" class="button income-btn">Add Expense</button>
        </div>


        <div class="row">
            <table class="table">
                <tr>
                    <th>Source</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Notes</th>
                    <th>Category</th>
                    <th></th>
                </tr>
                <?php foreach ($expenses as $expense): ?>
                    <tr>
                        <th><?php echo $expense['source'] ?></th>
                        <th>£<?php echo $expense['amount'] ?></th>
                        <th><?php echo $expense['expense_date'] ?></th>
                        <th><?php echo $expense['notes'] ?></th>
                        <th><?php echo $expense['category'] ?></th>
                        <th class="icon-container">
                            <i class="fa-regular fa-pen-to-square expense-edit table-icon" id="<?php echo $expense['expense_id'] ?>"></i>
                            <i class="fa-solid fa-trash expense-delete table-icon" id="<?php echo $expense['expense_id'] ?>"></i>
                        </th>
                    </tr>
                <?php endforeach ?>
            </table>

        </div>
        <?php if ($sum[0]['sum'] > 0): ?>
            <h3>Total Expense: £<?php echo $sum[0]['sum'] ?></h3>
        <?php endif ?>
    </div>