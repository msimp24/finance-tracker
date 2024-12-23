<?php
session_start();

include('./config/db_connect.php');



// api calls for income page

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? null;


    if ($action === 'edit-income') {

        $idToEdit = intval($_POST['id']);
        $sql = "SELECT * FROM income WHERE income_id = '$idToEdit'";


        $result = mysqli_query($conn, $sql);
        $income = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if ($income) {
            echo json_encode($income);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Income not found']);
        }
        mysqli_close($conn);
        exit;
    }

    if ($action === 'delete-income') {


        $idToDelete = intval($_POST['id']);

        $sql = "DELETE FROM income where income_id = '$idToDelete'";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Income was successfully deleted.']);
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Income was not removed.']);
        }

        // Close the connection
        mysqli_close($conn);

        exit;
    }

    //api calls for expense page

    if ($action === 'edit-expense') {

        $idToEdit = intval($_POST['id']);

        if (!$idToEdit) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
            exit;
        }
        $sql = "SELECT * FROM expenses WHERE expense_id = '$idToEdit'";

        $result = mysqli_query($conn, $sql);
        $expense = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if (!empty($expense)) {
            echo json_encode($expense);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Expense not found']);
        }
        mysqli_close($conn);
        exit;
    }

    if ($action === 'delete-expense') {


        $idToDelete = intval($_POST['id']);

        $sql = "DELETE FROM expenses where expense_id = '$idToDelete'";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Expense was successfully deleted.']);
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Expense was not removed.']);
        }

        // Close the connection
        mysqli_close($conn);

        exit;
    }


    //api calls for the pots page

    //deletes users pots
    if ($action === 'delete-pot') {
        $idToDelete = intval($_POST['id']);

        $sql = "DELETE FROM savings_pots where pots_id = '$idToDelete'";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Expense was successfully deleted.']);
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Expense was not removed.']);
        }

        // Close the connection
        mysqli_close($conn);

        exit;
    }

    //edits users pots
    if ($action === 'edit-pot') {

        $idToEdit = intval($_POST['id']);

        if (!$idToEdit) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
            exit;
        }
        $sql = "SELECT * FROM savings_pots WHERE pots_id = '$idToEdit'";

        $result = mysqli_query($conn, $sql);
        $pot = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if (!empty($pot)) {
            echo json_encode($pot);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Pot not found']);
        }
        mysqli_close($conn);
        exit;
    }

    // withdraw from pot


}
