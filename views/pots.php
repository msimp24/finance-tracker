<?php
if (!$isLoggedIn) {
    header('Location: /finance-tracker/');
}
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
}

//get current balance
include('./globalFunctions.php');
$balance = getCurrentBalance($conn, $userId);

//create new pot variables
$errors = array('name' => '', 'target' => '');

$name = '';
$target = '';

//add money to pot variables

$moneyErrors = array('add' => '');
$addMoney = 0;
$potId = '';


if (isset($_POST['create-pot'])) {

    if ($_POST['name'] === '') {
        $errors['name'] = 'Name is a required field';
    } else {
        $name = $_POST['name'];
        $errors['name'] = '';
    }
    if ($_POST['target'] === '') {
        $errors['target'] = 'Target is a required field';
    } else {
        $target = $_POST['target'];
        $errors['target'] = '';
    }

    if (!array_filter($errors)) {

        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : null;

        if ($id) {
            $sql = "UPDATE savings_pots SET name = '$name', target = '$target' WHERE pots_id = '$id'";
        } else {
            $sql = "INSERT INTO savings_pots (name, target, user_id) values ('$name', '$target' ,'$userId');";
        }
    }

    if (mysqli_query($conn, $sql)) {
        header('Location: /finance-tracker/pots');
        exit;
    } else {
        echo 'query error:' . mysqli_error($conn);
    }
}

$sql = "SELECT * FROM savings_pots where user_id = '$userId'";
$result = mysqli_query($conn, $sql);
$pots = mysqli_fetch_all($result, MYSQLI_ASSOC);

function getPercentage($target, $totalSaved)
{
    $percentage = 0;

    if ($totalSaved === '0') {
        return $percentage;
    } else {
        $percentage = ($totalSaved / $target) * 100;
    }
    if (intval($percentage) > 100) {

        return 100;
    } else {
        return intval($percentage);
    }
}

//add money handler

//handle add money form

if (isset($_POST['add-money'])) {

    if ($_POST['add'] === '') {
        $moneyErrors['add'] = 'This is a required field';
    } else {
        $addMoney = $_POST['add'];

        if (!is_numeric($addMoney)) {
            $moneyErrors['add'] = 'Money must be a numerical value';
        } else {
            if ($addMoney > $balance) {
                $moneyErrors['add'] = "You don't have enough in your balance";
            }
        }
    }

    if (!array_filter($moneyErrors)) {
        $potId = $_POST['add_money_id'];

        $sql = "update savings_pots set total_saved = (total_saved + '$addMoney') where user_id = '$userId' AND pots_id = '$potId';";

        if (mysqli_query($conn, $sql)) {
            header('Location: /finance-tracker/pots');
            exit;
        } else {
            echo 'query error:' . mysqli_error($conn);
        }
    }
}


?>

<div class="layout">
    <?php include('./views/navbar.php') ?>
    <div class="container">
        <div class="modal" id="pots-modal">
            <form method="POST" class="login-form pots-form">
                <input type="hidden" name="id" id="potId">
                <i class="fa-solid fa-x" id="pots-close"></i>
                <p>
                    Create a pot to set aside savings.
                </p>
                <label for="name">Pot Name</label>
                <input type="text" name="name" class="login-input" id="pot-name" placeholder="e.g. Vacation">
                <p class="error-text" id="pot-name-error">
                    <?php echo $errors['name'] ?>
                </p>

                <label for="target">Target Amount</label>
                <input type="text" name="target" class="login-input" id="pot-target">
                <p class="error-text" id="pot-target-error">
                    <?php echo $errors['target'] ?>
                </p>
                <div class="center">
                    <input class="login-button button" type="submit" name="create-pot" value="Submit" id="submit">
                </div>
            </form>

        </div>

        <div class="modal" id="add-money-modal">
            <form method="POST" class="login-form add-money-form">
                <input type="hidden" name="add_money_id" id="add-money-id">
                <i class="fa-solid fa-x" id="add-money-close"></i>
                <label for="add" id="modal-pot-name"></label>
                <input type="text" class="login-input" step="1" name="add" id="add-money">
                <input type="hidden" id="curr-balance" value="<?php echo $balance; ?>">
                <p class="error-text" id="add-money-error">
                    <?php echo $moneyErrors['add'] ?>
                </p>
                <div class="add-money-container">
                    <h4 id="add-money-saved">
                    </h4>
                    <h4 id="add-money-target"></h4>

                </div>

                <div class="progress-border">
                    <div class="progress-bar" id="add-money-progress-bar"></div>
                </div>

                <div class="center">
                    <input class="login-button button" type="submit" name="add-money" value="Submit" id="submit">
                </div>
            </form>

        </div>

        <div class="modal" id="withdraw-money-modal">
            <form method="POST" class="login-form withdraw-money-form">
                <input type="hidden" name="withdraw_money_id" id="withdraw-money-id">
                <i class="fa-solid fa-x" id="withdraw-money-close"></i>

                <div class="progress-border">
                    <div class="progress-bar" id="withdraw-money-progress-bar"></div>
                </div>

                <div class="center">
                    <input class="login-button button" type="submit" name="withdraw-money" value="Submit" id="submit">
                </div>
            </form>

        </div>

        <div class="top-row">
            <h1>Pots</h1>
            <button id="pots-btn" class="button income-btn">Add New Pot</button>
        </div>
        <div class="pots-grid">
            <?php foreach ($pots as $pot): ?>
                <div class="pots-item" id="<?php echo $pot['pots_id'] ?>">
                    <div class="top">
                        <h1>
                            <?php echo htmlspecialchars($pot['name']) ?>
                        </h1>
                        <div class="tooltip-container" id="pots-tooltip">
                            <i class="fa-solid fa-ellipsis tooltip-trigger" data-id="<?php echo $pot['pots_id'] ?>"></i>
                            <div class="tooltip-content">
                                <div class="tooltip-btn edit-pot" data-id="<?php echo $pot['pots_id'] ?>">Edit</div>
                                <div class="tooltip-btn delete-pot" data-id="<?php echo $pot['pots_id'] ?>">Delete</div>
                            </div>
                        </div>
                    </div>
                    <div class="middle">
                        <h4>Total Saved:</h4>
                        <h2 id="total-saved">
                            $<?php echo htmlspecialchars($pot['total_saved']) ?>
                        </h2>
                    </div>
                    <div class="progress-border">
                        <div class="progress-bar" style="width: <?php echo getPercentage($pot['target'], $pot['total_saved']) ?>%;">

                        </div>
                    </div>
                    <p>Target: $<?php echo htmlspecialchars($pot['target']) ?></p>
                    <div class="bottom">
                        <button
                            class="pots-button button add-money"
                            data-pot-id="<?php echo $pot['pots_id'] ?>"
                            data-pot-name="<?php echo $pot['name'] ?>" data-target="<?php echo $pot['target']; ?>"
                            data-total-saved="<?php echo $pot['total_saved']; ?>">+ Add Money</button>
                        <button id="withdraw-money" class="pots-button button">Withdraw</button>
                    </div>
                </div>
            <?php endforeach ?>

        </div>


    </div>


</div>