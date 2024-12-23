 <?php
    $conn = mysqli_connect(
        'localhost:3307',
        'admin',
        'Pass1234',
        'finance-tracker'
    );

    if (!$conn) {
        echo 'Connnection error: ' . mysqli_connect_error();
    }
    ?>
