<?php
include('Database.php');

if (isset($_POST['send'])) {
    $Name = $_POST['name'];
    $Email = $_POST['email'];
    $Phn_no = $_POST['phone'];
    $Msg = $_POST['message'];

    $stmt = mysqli_prepare($conn, "INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $Name, $Email, $Phn_no, $Msg);
    $data = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($data) {
        echo "<script>alert('Sent Successfully!');</script>";
    } else {
        echo "<script>alert('Something went wrong');</script>";
    }
}

?>