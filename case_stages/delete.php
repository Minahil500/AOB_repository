
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "../config/db.php";
include "../includes/activity_logger.php";


// CHECK LOGIN

if (!isset($_SESSION['user_id'])) {

    header("Location: ../login.php");
    exit();

}

// CHECK ID

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {

    die("Invalid stage ID.");

}
$stage_id = (int) $_GET['id'];

// GET STAGE BEFORE DELETE
$query = "
    SELECT
        id,
        stage_name,
        description
    FROM case_stages
    WHERE id = $stage_id
    LIMIT 1
";
$result = mysqli_query(
    $conn,
    $query
);


if (!$result) {

    die(
        "Database Error: " .
        mysqli_error($conn)
    );

}


if (mysqli_num_rows($result) == 0) {

    die("Case stage not found.");

}


$stage = mysqli_fetch_assoc($result);

// DELETE STAGE

$delete_query = "
    DELETE FROM case_stages
    WHERE id = $stage_id
    LIMIT 1
";


$delete_result = mysqli_query(
    $conn,
    $delete_query
);


if (!$delete_result) {

    die(
        "Unable to delete case stage: " .
        mysqli_error($conn)
    );

}

// ACTIVITY LOG

log_activity(
    $conn,
    "Case Stages",
    "DELETE",
    "Stage #" . $stage_id,
    json_encode([
        "stage_name" =>
            $stage['stage_name'],
        "description" =>
            $stage['description']
    ]),
    null,
    "Case stage deleted"
);

// REDIRECT

header(
    "Location: index.php"
);

exit();

?>