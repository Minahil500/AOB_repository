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


$id =
    (int)($_GET['id'] ?? 0);


if ($id <= 0) {

    die("Invalid document type ID.");

}
// GET TYPE

$query = "
    SELECT *
    FROM document_types
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


if (
    mysqli_num_rows($result) == 0
) {

    die("Document type not found.");

}


$type =
    mysqli_fetch_assoc(
        $result
    );

// DELETE

$delete_query = "
    DELETE FROM document_types
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
        "Document Types",
        "DELETE",
        "Document Type #" . $id,
        json_encode($type),
        null,
        "Document type deleted."
    );


    header(
        "Location: index.php"
    );

    exit();


} else {


    // Foreign-key related error
    if (
        mysqli_errno($conn) == 1451
    ) {

        die(
            "This document type cannot be deleted because it is already being used by another record."
        );

    }
    die(
        "Unable to delete document type: "
        . mysqli_stmt_error(
            $delete_stmt
        )
    );

}
mysqli_stmt_close(
    $delete_stmt
);

?>