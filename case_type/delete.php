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
    die("Invalid case type ID.");
}

// GET TYPE BEFORE DELETE

$query = "
    SELECT
        id,
        type_name,
        description
    FROM case_types
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
    die("Case type not found.");
}

$type =
    mysqli_fetch_assoc($result);
// DELETE

$delete_query = "
    DELETE FROM case_types
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
        "Case Types",
        "DELETE",
        "Case Type #" . $id,
        json_encode($type),
        null,
        "Case type deleted."
    );


    header(
        "Location: index.php"
    );

    exit();


} else {

    die(
        "Unable to delete case type: " .
        mysqli_stmt_error(
            $delete_stmt
        )
    );
}