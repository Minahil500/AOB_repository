<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../config/db.php";
include "../includes/activity_logger.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();

}
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {

    die("Invalid case regulation ID.");

}
// GET RECORD BEFORE DELETE
$query = "
    SELECT
        cr.id,
        cr.case_id,
        cr.regulation_id,
        c.case_number,
        c.case_title,
        r.regulation_name
    FROM case_regulations cr
    LEFT JOIN cases c
        ON cr.case_id = c.id
    LEFT JOIN regulations r
        ON cr.regulation_id = r.id
    WHERE cr.id = ?
    LIMIT 1
";
$stmt =
    mysqli_prepare(
        $conn,
        $query
    );


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


mysqli_stmt_execute(
    $stmt
);


$result =
    mysqli_stmt_get_result(
        $stmt
    );


if (mysqli_num_rows($result) == 0) {

    die(
        "Case regulation record not found."
    );

}


$record =
    mysqli_fetch_assoc(
        $result
    );
// DELETE
$delete_query = "
    DELETE FROM case_regulations
    WHERE id = ?
";
$delete_stmt =
    mysqli_prepare(
        $conn,
        $delete_query
    );
mysqli_stmt_bind_param(
    $delete_stmt,
    "i",
    $id
);
if (
    mysqli_stmt_execute(
        $delete_stmt
    )
) {
    // ACTIVITY LOG
    log_activity(
        $conn,
        "Case Regulations",
        "DELETE",
        "Case Regulation #" . $id,
        json_encode($record),
        null,
        "Regulation removed from case."
    );
    header(
        "Location: index.php"
    );
    exit();
} else {
    die(
        "Unable to delete case regulation: " .
        mysqli_stmt_error(
            $delete_stmt
        )
    );
}