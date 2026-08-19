
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
    die("Invalid user ID.");
}
// PREVENT SELF DELETE

$current_user_id =
    (int)($_SESSION['user_id'] ?? 0);

if ($id === $current_user_id) {

    die(
        "You cannot delete your own account."
    );
}
// GET USER

$query = "
    SELECT
        id,
        user_code,
        full_name,
        username,
        official_email,
        status
    FROM users
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
    mysqli_stmt_get_result(
        $stmt
    );

if (
    mysqli_num_rows($result) == 0
) {

    die("User not found.");

}

$user =
    mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);
// DELETE

$delete_stmt = mysqli_prepare(
    $conn,
    "
    DELETE FROM users
    WHERE id = ?
    "
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

    log_activity(
        $conn,
        "Users",
        "DELETE",
        "User #" . $id,
        json_encode($user),
        null,
        "User account deleted."
    );

    header(
        "Location: index.php"
    );

    exit();

}
// FOREIGN KEY ERROR

if (
    mysqli_errno($conn) == 1451
) {

    die(
        "This user cannot be deleted because the account is referenced by other records. Set the user status to inactive instead."
    );

}
die(
    "Unable to delete user: "
    . mysqli_error($conn)
);

?>