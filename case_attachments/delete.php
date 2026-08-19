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
    die("Invalid attachment ID.");
}

$query = "
    SELECT *
    FROM case_attachments
    WHERE id = ?
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
    die("Attachment not found.");
}

$attachment =
    mysqli_fetch_assoc(
        $result
    );

$delete_query = "
    DELETE FROM case_attachments
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

    $file_path =
        "../"
        . $attachment['file_path'];

    if (
        file_exists(
            $file_path
        )
    ) {
        unlink(
            $file_path
        );
    }

    log_activity(
        $conn,
        "Case Attachments",
        "DELETE",
        "Attachment #" . $id,
        json_encode($attachment),
        null,
        "Case attachment deleted."
    );

    header(
        "Location: index.php"
    );

    exit();

} else {

    die(
        "Unable to delete attachment: "
        . mysqli_stmt_error(
            $delete_stmt
        )
    );

}