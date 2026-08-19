<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../vendor/autoload.php";

include "../config/db.php";

require_once "../includes/permission.php";

require_permission(
    $conn,
    "Manage Documents"
);

require_once "../includes/activity_logger.php";

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {

    die("Invalid document ID.");

}

$document_id = (int) $_GET['id'];

$query = "
    SELECT
        id,
        document_name,
        file_name,
        file_path,
        ocr_status,
        extracted_text
    FROM legal_documents
    WHERE id = $document_id
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

if (
    mysqli_num_rows($result) == 0
) {

    die("Document not found.");

}

$document =
    mysqli_fetch_assoc($result);

$file_path =
    $document['file_path'];

if (
    empty($file_path)
) {

    die(
        "No PDF file is attached to this document."
    );

}

$file_path =
    str_replace(
        "\\",
        "/",
        $file_path
    );

$file_path =
    preg_replace(
        '#^(\.\./)+#',
        '',
        $file_path
    );

$file_path =
    ltrim(
        $file_path,
        "/"
    );

$full_path =
    dirname(__DIR__)
    . DIRECTORY_SEPARATOR
    . str_replace(
        "/",
        DIRECTORY_SEPARATOR,
        $file_path
    );

if (
    !file_exists($full_path)
) {

    die(
        "PDF file not found.<br><br>"
        . "Path checked:<br>"
        . htmlspecialchars(
            $full_path
        )
    );

}

if (
    !is_readable($full_path)
) {

    die(
        "PDF file exists but PHP cannot read it.<br><br>"
        . htmlspecialchars(
            $full_path
        )
    );

}

try {

    $parser =
        new \Smalot\PdfParser\Parser();

    $pdf =
        $parser->parseFile(
            $full_path
        );

    $text =
        trim(
            $pdf->getText()
        );

} catch (
    Throwable $e
) {

    die(
        "PDF Parser Error:<br><br>"
        . htmlspecialchars(
            $e->getMessage()
        )
    );

}

$text =
    preg_replace(
        '/[ \t]+/',
        ' ',
        $text
    );

$text =
    trim(
        $text
    );

if (
    strlen($text) >= 20
) {

    $pdf_type =
        "Searchable PDF";

    $ocr_status =
        "Not Required";

} else {

    $pdf_type =
        "Scanned PDF";

    $ocr_status =
        "Pending";

}

$safe_text =
    mysqli_real_escape_string(
        $conn,
        $text
    );

$safe_status =
    mysqli_real_escape_string(
        $conn,
        $ocr_status
    );

$update_query = "
    UPDATE legal_documents
    SET
        ocr_status = '$safe_status',
        extracted_text = '$safe_text'
    WHERE id = $document_id
";

$update_result =
    mysqli_query(
        $conn,
        $update_query
    );

if (!$update_result) {

    die(
        "Update Error: " .
        mysqli_error($conn)
    );

}

$previous_value = json_encode([
    "ocr_status" => $document['ocr_status']
]);

$new_value = json_encode([
    "pdf_type" => $pdf_type,
    "ocr_status" => $ocr_status,
    "extracted_text_length" => strlen($text)
]);

log_activity(
    $conn,
    "Legal Documents",
    "DETECT",
    "Document ID: " . $document_id,
    $previous_value,
    $new_value,
    "PDF type detected as " . $pdf_type . "."
);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>
PDF Detection Result
</title>

<link
    rel="stylesheet"
    href="/aob_repository/assets/css/style.css"
>

</head>

<body>

<div
    style="
        max-width:900px;
        margin:50px auto;
        padding:30px;
        background:#ffffff;
        border:1px solid #e5e7eb;
        border-radius:10px;
    "
>

<h1>
PDF Detection Result
</h1>

<h2>

<?php

echo htmlspecialchars(
    $document['document_name']
    ?? ''
);

?>

</h2>

<p>

<strong>
File:
</strong>

<?php

echo htmlspecialchars(
    $document['file_name']
    ?? ''
);

?>

</p>

<p>

<strong>
PDF Type:
</strong>

<?php

echo htmlspecialchars(
    $pdf_type
);

?>

</p>

<p>

<strong>
OCR Status:
</strong>

<?php

echo htmlspecialchars(
    $ocr_status
);

?>

</p>

<?php

if (
    $pdf_type ===
    "Searchable PDF"
) {

?>

<h3>
Extracted Text
</h3>

<div
    style="
        background:#f8fafc;
        padding:20px;
        border:1px solid #e5e7eb;
        border-radius:6px;
        white-space:pre-wrap;
        max-height:500px;
        overflow:auto;
        line-height:1.6;
    "
>

<?php

echo htmlspecialchars(
    $text
);

?>

</div>

<div
    style="
        margin-top:20px;
        padding:15px;
        background:#ecfdf5;
        border:1px solid #a7f3d0;
        border-radius:6px;
    "
>

<strong>
OCR is not required.
</strong>

<br>

This is a searchable PDF, so its text was extracted directly
using the PDF parser.

</div>

<?php

} else {

?>

<div
    style="
        margin-top:20px;
        padding:15px;
        background:#fff7ed;
        border:1px solid #fed7aa;
        border-radius:6px;
    "
>

<strong>
This PDF appears to be scanned.
</strong>

<br><br>

OCR processing will be required.

</div>

<?php

}

?>

<div
    style="
        margin-top:25px;
    "
>

<a
    href="../documents/view.php?id=<?php echo $document_id; ?>"
    class="btn-secondary"
>
Back to Document
</a>

<?php

if (
    $pdf_type ===
    "Scanned PDF"
) {

?>

<a
    href="process.php?id=<?php echo $document_id; ?>"
    class="btn-primary"
>
Run OCR
</a>

<?php

}

?>

<?php

if (
    $pdf_type ===
    "Searchable PDF"
) {

?>

<a
    href="../documents/view.php?id=<?php echo $document_id; ?>"
    class="btn-primary"
>
View Extracted Text
</a>

<?php

}

?>

</div>

</div>

</body>

</html>