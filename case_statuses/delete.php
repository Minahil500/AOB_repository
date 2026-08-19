
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

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {
    die("Invalid status ID.");
}

$status_id = (int)$_GET['id'];

// GET STATUS

$query = "
    SELECT
        id,
        status_name,
        description
    FROM case_statuses
    WHERE id = $status_id
    LIMIT 1
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die(
        "Database Error: " .
        mysqli_error($conn)
    );
}

if (mysqli_num_rows($result) == 0) {
    die("Case status not found.");
}

$status = mysqli_fetch_assoc($result);

// DELETE

$delete_query = "
    DELETE FROM case_statuses
    WHERE id = $status_id
    LIMIT 1
";

$delete_result = mysqli_query(
    $conn,
    $delete_query
);

if (!$delete_result) {

    die(
        "Unable to delete case status: " .
        mysqli_error($conn)
    );

}

// ACTIVITY LOG

log_activity(
    $conn,
    "Case Statuses",
    "DELETE",
    "Status #" . $status_id,
    json_encode([
        "status_name" =>
            $status['status_name'],
        "description" =>
            $status['description']
    ]),
    null,
    "Case status deleted"
);

// REDIRECT

header("Location: index.php");
exit();

?>