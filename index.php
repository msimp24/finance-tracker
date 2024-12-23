<?php
include('./config/db_connect.php');

session_start();

$isLoggedIn = isset($_SESSION['user_id']);

if (file_exists(__DIR__ . $_SERVER['REQUEST_URI'])) {
    return false; // Let the server handle the request directly (file will be served)
}
//global variables for login form
$errors = array('loginEmail' => '', 'password' => '', 'response' => '');

$request = str_replace('/finance-tracker', '', $_SERVER['REQUEST_URI']);

$loginEmail = '';
$password = '';

//global varibles for registration
$registerErrors = array('email' => '', 'password' => '', 'confirmPassword' => '', 'firstName' => '', 'lastName' => '', 'response' => '', 'emailTaken' => '');

$firstName = '';
$lastName = '';
$registerEmail = '';
$registerPassword = '';
$confirmPassword = '';

//handle form submit for login and register

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //handle what form has been submitted
    $action = $_POST['action'] ?? null;

    //login form 
    if ($action === 'login') {
        if ($_POST['loginEmail'] === '') {
            $errors['loginEmail'] = 'Email is a required field';
        } else {
            $loginEmail = $_POST['loginEmail'];

            if (!filter_var($loginEmail, FILTER_VALIDATE_EMAIL)) {
                $errors['loginEmail'] = "Not a valid email";
            }
        }

        if ($_POST['password'] === '') {
            $errors['password'] = 'Password is a required field';
        }
        if (!array_filter($errors)) {
            $sql = "SELECT user_id, email, password, first_name, last_name FROM users WHERE '$loginEmail' = email";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) === 1) {
                $user = mysqli_fetch_assoc($result);


                if (password_verify($_POST['password'], $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['first_name'] = $user['first_name'];

                    header('Location: /finance-tracker/dashboard');
                    exit;
                } else {
                    $errors['response'] = 'Incorrect password';
                }
            } else {
                $errors['response'] = 'No account found with that email';
            }
        }
    }


    if ($action === 'logout') {
        session_unset();
        session_destroy();

        header('Location: /finance-tracker');
    }

    if ($action === 'register') {

        if ($_POST['registerEmail'] === '') {
            $registerErrors['email'] = 'Email is a required field';
        } else {
            $registerEmail = $_POST['registerEmail'];

            if (!filter_var($registerEmail, FILTER_VALIDATE_EMAIL)) {
                $registerErrors['email'] = "Not a valid email";
            }
        }
        if ($_POST['firstName'] === '') {
            $registerErrors['firstName'] = 'First name is a required field';
        } else {
            $firstName = $_POST['firstName'];

            if (strlen($firstName) < 2) {
                $registerErrors['firstName'] = 'First name must have a length larger than 2';
            }
        }
        if ($_POST['lastName'] === '') {
            $registerErrors['lastName'] = 'Last name is a required field';
        } else {
            $lastName = $_POST['lastName'];

            if (strlen($lastName) < 2) {
                $registerErrors['lastName'] = 'Last name must have a length larger than 2';
            }
        }

        if ($_POST['registerPassword'] === '') {
            $registerErrors['password'] = 'Password is a required field';
        } else {
            $registerPassword = $_POST['registerPassword'];

            if (!preg_match('/^[A-Z](?=.*\d)(?=.*[\W_]).{9,}$/', $registerPassword)) {
                $registerErrors['password'] = 'Password must start with a capital letter [A-Z], must require a special character !@#$%^&*() and a number';
            }
        }
        if (empty($_POST['confirmPassword'])) {
            $registerErrors['confirmPassword'] = 'Confirm password is a required field';
        } else {
            $confirmPassword = $_POST['confirmPassword'];

            if ($registerPassword !== $confirmPassword) {
                $registerErrors['confirmPassword'] = 'Passwords must match';
            }
        }

        if (!array_filter($registerErrors)) {
            $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
            $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
            $email = mysqli_real_escape_string($conn, $_POST['registerEmail']);

            $emailCheckQuery = "SELECT user_id from users WHERE email = '$email' LIMIT 1";
            $emailCheckResult = mysqli_query($conn, $emailCheckQuery);

            if (mysqli_num_rows($emailCheckResult) > 0) {
                $registerErrors['emailTaken'] = 'This email is already registered.';
            } else {
                // Hash the password (Do not escape it)
                $hashedPassword = password_hash($registerPassword, PASSWORD_DEFAULT);

                // Insert into the database
                $sql = "INSERT INTO users (first_name, last_name, email, password) 
            VALUES ('$firstName', '$lastName', '$email', '$hashedPassword')";
                $_SESSION['user_id'] = $user['user_id'];

                if (mysqli_query($conn, $sql)) {
                    header('Location: /finance-tracker/');
                    exit;
                } else {
                    echo 'Query error: ' . mysqli_error($conn);
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Tracker</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script defer src="./app.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <?php if ($request == '/dashboard'): ?>

        <?php include('./views/dashboard.php') ?>

    <?php elseif ($request === '/income'): ?>

        <?php include('./views/income.php') ?>

    <?php elseif ($request === '/expenses'): ?>

        <?php include('./views/expenses.php') ?>


    <?php elseif ($request === '/pots'): ?>

        <?php include('./views/pots.php') ?>


    <?php elseif ($request === '/transactions'): ?>

        <?php include('./views/transactions.php') ?>


    <?php elseif ($request === '/profile'): ?>

        <?php include('./views/profile.php') ?>


    <?php elseif ($request == '/'): ?>

        <?php if ($isLoggedIn): ?>
            <?php header('Location: /finance-tracker/income') ?>
        <?php endif ?>

        <div class="home-container">
            <div class="left-image">
            </div>

            <div class="right-side">
                <h1 class="home-title">Finance Tracker</h1>
                <form class="login-form" method="POST">
                    <h1>Login</h1>
                    <input type="hidden" name="action" value="login">

                    <label for="loginEmail">Email</label>
                    <input class="login-input" type="text" name="loginEmail" value="<?php echo htmlspecialchars($loginEmail) ?>">
                    <p class="error-text">
                        <?php if (strlen($errors['loginEmail']) > 1) {
                            echo $errors['loginEmail'];
                        }; ?>
                    </p>

                    <label for="password">Password</label>
                    <input class="login-input" type="password" name="password" value="<?php echo htmlspecialchars($password) ?>">
                    <p class="error-text">
                        <?php echo $errors['password'] ?>
                    </p>
                    <p class="error-text">
                        <?php echo $errors['response'] ?>
                    </p>

                    <div class="center">
                        <input class="button login-button" type="submit" value="Login">
                    </div>

                    <a class="new-user" href="/finance-tracker/register">New User? Register Here</a>
                </form>
            </div>
        </div>
    <?php elseif ($request === '/register'): ?>
        <?php if ($isLoggedIn): ?>
            <?php header('Location: /finance-tracker/dashboard') ?>
        <?php endif ?>

        <div class="home-container">
            <div class="left-image">
            </div>

            <div class="right-side">
                <h1 class="home-title">Finance Tracker</h1>
                <form class="login-form" method="POST">
                    <h1>Register</h1>
                    <input type="hidden" name="action" value="register">

                    <label for="firstName">First Name</label>
                    <input class="login-input" type="text" value="<?php echo $firstName ?>" name="firstName">
                    <p class="error-text">
                        <?php echo $registerErrors['firstName'] ?>
                    </p>

                    <label for="lastName">Last Name</label>
                    <input class="login-input" type="text" value="<?php echo $lastName ?>" name="lastName">
                    <p class="error-text">
                        <?php echo $registerErrors['lastName'] ?>
                    </p>


                    <label for="registerEmail">Email</label>
                    <input class="login-input" type="text" name="registerEmail" value="<?php echo htmlspecialchars($registerEmail) ?>">
                    <p class="error-text">
                        <?php echo $registerErrors['email'] ?>
                    </p>


                    <label for="registerPassword">Password</label>
                    <input class="login-input" type="password" name="registerPassword" value="<?php echo htmlspecialchars($registerPassword) ?>">
                    <p class="error-text">
                        <?php echo $registerErrors['password'] ?>
                    </p>

                    <label for="confirmPassword">Confirm Password</label>
                    <input class="login-input" type="password" name="confirmPassword" value="<?php echo htmlspecialchars($confirmPassword) ?>">
                    <p class="error-text">
                        <?php echo $registerErrors['confirmPassword'] ?>
                    </p>
                    <p class="error-text">
                        <?php if (strlen($registerErrors['emailTaken']) > 1) {
                            echo $registerErrors['emailTaken'];
                        }; ?>
                    </p>

                    <div class="center">
                        <input class="button login-button" type="submit" value="Submit">
                    </div>

                </form>
            </div>
        </div>

    <?php else: ?>
        <h1>404 Error, page not found</h1>
    <?php endif; ?>


</body>


</html>