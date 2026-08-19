<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../config/db.php";
// CHECK ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid follow-up ID.");
}
$followup_id = (int) $_GET['id'];
// CHECK FOLLOW-UP EXISTS
$check_query = "
    SELECT id
    FROM case_followups
    WHERE id = $followup_id
    LIMIT 1
";
$check_result = mysqli_query(
    $conn,
    $check_query
);
if (!$check_result) {
    die(
        "Database Error: " .
        mysqli_error($conn)
    );
}
if (mysqli_num_rows($check_result) == 0) {
    die("Follow-up not found.");

}
// DELETE
$delete_query = "
    DELETE FROM case_followups
    WHERE id = $followup_id
";
$delete_result = mysqli_query(
    $conn,
    $delete_query
);
// CHECK DELETE
if (!$delete_result) {
    die(
        "Delete Error: " .
        mysqli_error($conn)
    );

}
// REDIRECT
header("Location: index.php");
exit();
?>
