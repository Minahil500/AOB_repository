
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "../config/db.php";
require_once "../includes/permission.php";
require_once "../includes/activity_logger.php";

require_permission(
    $conn,
    "Manage Documents"
);
// CHECK DOCUMENT ID

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    die("Invalid document ID.");

}

$document_id = (int) $_GET['id'];
// GET DOCUMENT FILE PATH
$query = "
    SELECT
        document_number,
        document_name,
        document_type_id,
        firm_id,
        case_id,
        court_id,
        version,
        ocr_status,
        file_name,
        file_path,
        document_date,
        description
    FROM legal_documents
    WHERE id = $document_id
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

    die("Document not found.");

}
$document = mysqli_fetch_assoc($result);
$previous_value = json_encode([
    "document_number"  => $document['document_number'],
    "document_name"    => $document['document_name'],
    "document_type_id" => $document['document_type_id'],
    "firm_id"          => $document['firm_id'],
    "case_id"          => $document['case_id'],
    "court_id"         => $document['court_id'],
    "version"          => $document['version'],
    "ocr_status"       => $document['ocr_status'],
    "file_name"        => $document['file_name'],
    "document_date"    => $document['document_date'],
    "description"      => $document['description']
]);
// DELETE DATABASE RECORD

$delete_query = "
    DELETE FROM legal_documents
    WHERE id = $document_id
";

$delete_result = mysqli_query(
    $conn,
    $delete_query
);
if (!$delete_result) {

    die(
        "Delete Error: " .
        mysqli_error($conn)
    );

}
// ACTIVITY LOG

log_activity(
    $conn,
    "Legal Documents",
    "DELETE",
    "Document ID: " . $document_id,
    $previous_value,
    null,
    "Legal document deleted."
);

// DELETE PHYSICAL PDF FILE

if (
    !empty($document['file_path'])
) {

    $file_path = $document['file_path'];

    if (
        file_exists($file_path)
    ) {

        unlink($file_path);

    }

}
// REDIRECT

header("Location: index.php");

exit();

?>
