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
    die("Invalid regulation ID.");
}
// GET REGULATION BEFORE DELETE

$query = "
    SELECT
        id,
        regulation_name,
        description
    FROM regulations
    WHERE id = ?
    LIMIT 1
";

$stmt = mysqli_prepare(
    $conn,
    $query
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("Regulation not found.");
}

$regulation =
    mysqli_fetch_assoc($result);
// DELETE

$delete_query = "
    DELETE FROM regulations
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
        "Regulations",
        "DELETE",
        "Regulation #" . $id,
        json_encode($regulation),
        null,
        "Regulation deleted."
    );


    header(
        "Location: index.php"
    );

    exit();

} else {

    die(
        "Unable to delete regulation: " .
        mysqli_stmt_error(
            $delete_stmt
        )
    );
}