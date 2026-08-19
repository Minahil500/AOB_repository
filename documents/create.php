<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "../config/db.php";
require_once "../includes/activity_logger.php";

$types_query = "
    SELECT id, type_name
    FROM document_types
    ORDER BY type_name ASC
";

$types_result = mysqli_query(
    $conn,
    $types_query
);

if (!$types_result) {

    die(
        "Document Types Error: " .
        mysqli_error($conn)
    );

}

$firms_query = "
    SELECT id, firm_name
    FROM firms
    ORDER BY firm_name ASC
";

$firms_result = mysqli_query(
    $conn,
    $firms_query
);

if (!$firms_result) {

    die(
        "Firms Error: " .
        mysqli_error($conn)
    );

}

$cases_query = "
    SELECT
        id,
        case_number,
        case_title
    FROM cases
    ORDER BY id DESC
";

$cases_result = mysqli_query(
    $conn,
    $cases_query
);

if (!$cases_result) {

    die(
        "Cases Error: " .
        mysqli_error($conn)
    );

}

$courts_query = "
    SELECT
        id,
        court_name,
        city
    FROM courts
    ORDER BY court_name ASC
";

$courts_result = mysqli_query(
    $conn,
    $courts_query
);

if (!$courts_result) {

    die(
        "Courts Error: " .
        mysqli_error($conn)
    );

}

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
) {

    $document_number =
        trim(
            $_POST['document_number'] ?? ''
        );

    $document_name =
        trim(
            $_POST['document_name'] ?? ''
        );

    $document_type_id =
        (int)(
            $_POST['document_type_id'] ?? 0
        );

    $firm_id =
        (int)(
            $_POST['firm_id'] ?? 0
        );

    $case_id =
        (int)(
            $_POST['case_id'] ?? 0
        );

    $court_id =
        (int)(
            $_POST['court_id'] ?? 0
        );

    $version =
        trim(
            $_POST['version'] ?? '1.0'
        );

    $document_date =
        trim(
            $_POST['document_date'] ?? ''
        );

    $description =
        trim(
            $_POST['description'] ?? ''
        );

    $ocr_status = "Pending";

    if ($document_number === '') {

        die(
            "Document number is required."
        );

    }

    if ($document_name === '') {

        die(
            "Document name is required."
        );

    }

    if ($document_type_id <= 0) {

        die(
            "Please select a document type."
        );

    }

    if ($firm_id <= 0) {

        die(
            "Please select a firm."
        );

    }

    if ($case_id <= 0) {

        die(
            "Please select a case."
        );

    }

    if ($document_date === '') {

        die(
            "Please select document date."
        );

    }

    if (
        !isset(
            $_FILES['document_file']
        )
    ) {

        die(
            "Please select a PDF file."
        );

    }

    if (
        $_FILES['document_file']['error']
        !== UPLOAD_ERR_OK
    ) {

        die(
            "PDF upload failed. Error code: " .
            $_FILES['document_file']['error']
        );

    }

    $file_name =
        $_FILES['document_file']['name'];

    $tmp_name =
        $_FILES['document_file']['tmp_name'];

    $file_size =
        (int)$_FILES['document_file']['size'];

    $max_file_size =
        20 * 1024 * 1024;

    if ($file_size <= 0) {

        die(
            "Uploaded file is empty."
        );

    }

    if ($file_size > $max_file_size) {

        die(
            "PDF file is too large. Maximum allowed size is 20 MB."
        );

    }

    $extension =
        strtolower(
            pathinfo(
                $file_name,
                PATHINFO_EXTENSION
            )
        );

    if ($extension !== "pdf") {

        die(
            "Only PDF files are allowed."
        );

    }

    $upload_directory =
        dirname(__DIR__)
        . DIRECTORY_SEPARATOR
        . "uploads"
        . DIRECTORY_SEPARATOR
        . "documents";

    if (!is_dir($upload_directory)) {

        if (
            !mkdir(
                $upload_directory,
                0777,
                true
            )
        ) {

            die(
                "Unable to create upload directory."
            );

        }

    }

    $stored_file_name =
        time()
        . "_"
        . bin2hex(
            random_bytes(8)
        )
        . ".pdf";

    $full_file_path =
        $upload_directory
        . DIRECTORY_SEPARATOR
        . $stored_file_name;

    $database_file_path =
        "uploads/documents/"
        . $stored_file_name;

    if (
        !move_uploaded_file(
            $tmp_name,
            $full_file_path
        )
    ) {

        die(
            "Failed to upload the PDF file."
        );

    }

    $document_number =
        mysqli_real_escape_string(
            $conn,
            $document_number
        );

    $document_name =
        mysqli_real_escape_string(
            $conn,
            $document_name
        );

    $version =
        mysqli_real_escape_string(
            $conn,
            $version
        );

    $ocr_status =
        mysqli_real_escape_string(
            $conn,
            $ocr_status
        );

    $document_date =
        mysqli_real_escape_string(
            $conn,
            $document_date
        );

    $description =
        mysqli_real_escape_string(
            $conn,
            $description
        );

    $stored_file_name =
        mysqli_real_escape_string(
            $conn,
            $stored_file_name
        );

    $database_file_path =
        mysqli_real_escape_string(
            $conn,
            $database_file_path
        );

    $insert_query = "
        INSERT INTO legal_documents
        (
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
            file_size,
            document_date,
            uploaded_by,
            description
        )
        VALUES
        (
            '$document_number',
            '$document_name',
            $document_type_id,
            $firm_id,
            $case_id,
            $court_id,
            '$version',
            '$ocr_status',
            '$stored_file_name',
            '$database_file_path',
            $file_size,
            '$document_date',
            NULL,
            '$description'
        )
    ";

    $insert_result =
        mysqli_query(
            $conn,
            $insert_query
        );

    if (!$insert_result) {

        if (
            file_exists(
                $full_file_path
            )
        ) {

            unlink(
                $full_file_path
            );

        }

        die(
            "Database Insert Error: " .
            mysqli_error($conn)
        );

    }

    $new_document_id =
        mysqli_insert_id($conn);

    log_activity(
        $conn,
        "Legal Documents",
        "CREATE",
        "Document #" . $new_document_id,
        null,
        json_encode([
            "document_number" =>
                $document_number,
            "document_name" =>
                $document_name,
            "document_type_id" =>
                $document_type_id,
            "firm_id" =>
                $firm_id,
            "case_id" =>
                $case_id
        ]),
        "New legal document created"
    );

    header(
        "Location: view.php?id="
        . $new_document_id
    );

    exit();

}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>
Add Legal Document
</h1>

<p>
Upload and register a legal document.
</p>

<section>

<form
    method="POST"
    enctype="multipart/form-data"
>

<div class="form-group">

<label>
Document Number *
</label>

<input
    type="text"
    name="document_number"
    required
>

</div>

<div class="form-group">

<label>
Document Name *
</label>

<input
    type="text"
    name="document_name"
    required
>

</div>

<div class="form-group">

<label>
Document Type *
</label>

<select
    name="document_type_id"
    required
>

<option value="">
Select Document Type
</option>

<?php

while (
    $type = mysqli_fetch_assoc(
        $types_result
    )
) {

?>

<option
    value="<?php
        echo (int)$type['id'];
    ?>"
>

<?php

echo htmlspecialchars(
    $type['type_name']
);

?>

</option>

<?php

}

?>

</select>

</div>

<div class="form-group">

<label>
Firm *
</label>

<select
    name="firm_id"
    required
>

<option value="">
Select Firm
</option>

<?php

while (
    $firm = mysqli_fetch_assoc(
        $firms_result
    )
) {

?>

<option
    value="<?php
        echo (int)$firm['id'];
    ?>"
>

<?php

echo htmlspecialchars(
    $firm['firm_name']
);

?>

</option>

<?php

}

?>

</select>

</div>

<div class="form-group">

<label>
Case *
</label>

<select
    name="case_id"
    required
>

<option value="">
Select Case
</option>

<?php

while (
    $case = mysqli_fetch_assoc(
        $cases_result
    )
) {

?>

<option
    value="<?php
        echo (int)$case['id'];
    ?>"
>

<?php

echo htmlspecialchars(
    $case['case_number']
);

?>

 -

<?php

echo htmlspecialchars(
    $case['case_title']
);

?>

</option>

<?php

}

?>

</select>

</div>

<div class="form-group">

<label>
Court
</label>

<select
    name="court_id"
>

<option value="">
Select Court
</option>

<?php

while (
    $court = mysqli_fetch_assoc(
        $courts_result
    )
) {

?>

<option
    value="<?php
        echo (int)$court['id'];
    ?>"
>

<?php

echo htmlspecialchars(
    $court['court_name']
);

?>

 -

<?php

echo htmlspecialchars(
    $court['city']
);

?>

</option>

<?php

}

?>

</select>

</div>

<div class="form-group">

<label>
Version
</label>

<input
    type="text"
    name="version"
    value="1.0"
>

</div>

<div class="form-group">

<label>
Document Date *
</label>

<input
    type="date"
    name="document_date"
    required
>

</div>

<div class="form-group">

<label>
PDF File *
</label>

<input
    type="file"
    name="document_file"
    accept=".pdf,application/pdf"
    required
>

<p
    style="
        margin-top:6px;
        color:#6b7280;
        font-size:13px;
    "
>
Only PDF files are allowed.
Maximum size: 20 MB.
</p>

</div>

<div class="form-group">

<label>
Description
</label>

<textarea
    name="description"
    rows="5"
></textarea>

</div>

<div
    style="
        background:#f8fafc;
        border:1px solid #e5e7eb;
        border-radius:8px;
        padding:15px;
        margin-top:20px;
        margin-bottom:20px;
    "
>

<strong>
OCR Processing
</strong>

<p
    style="
        margin-bottom:0;
        color:#6b7280;
        font-size:13px;
    "
>
After uploading the PDF, the system can detect whether
it is searchable or scanned. Scanned PDFs can then be
processed using OCR.
</p>

</div>

<div class="form-actions">

<a
    href="index.php"
    class="btn-secondary"
>
Cancel
</a>

<button
    type="submit"
    class="btn-primary"
>
Save Document
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>