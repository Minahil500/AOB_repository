<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "../config/db.php";

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {

    die("Invalid document ID.");

}

$document_id = (int) $_GET['id'];

$document_query = "
    SELECT
        id,
        document_number,
        document_name,
        version
    FROM legal_documents
    WHERE id = $document_id
    LIMIT 1
";

$document_result =
    mysqli_query(
        $conn,
        $document_query
    );

if (!$document_result) {

    die(
        "Database Error: "
        . mysqli_error($conn)
    );

}

if (
    mysqli_num_rows(
        $document_result
    ) == 0
) {

    die("Document not found.");

}

$document =
    mysqli_fetch_assoc(
        $document_result
    );

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
) {

    $version =
        trim(
            $_POST['version']
            ?? ''
        );

    $version_number =
        trim(
            $_POST['version_number']
            ?? ''
        );

    $remarks =
        trim(
            $_POST['remarks']
            ?? ''
        );

    if (
        $version === ''
    ) {

        die(
            "Version is required."
        );

    }

    if (
        $version_number === ''
    ) {

        die(
            "Version number is required."
        );

    }

    if (
        !isset(
            $_FILES['version_file']
        )
    ) {

        die(
            "Please select a PDF file."
        );

    }

    if (
        $_FILES['version_file']['error']
        !== UPLOAD_ERR_OK
    ) {

        die(
            "File upload failed. Error code: "
            . $_FILES['version_file']['error']
        );

    }

    $original_file_name =
        $_FILES['version_file']['name'];

    $tmp_name =
        $_FILES['version_file']['tmp_name'];

    $file_size =
        (int) $_FILES['version_file']['size'];

    $extension =
        strtolower(
            pathinfo(
                $original_file_name,
                PATHINFO_EXTENSION
            )
        );

    if (
        $extension !== 'pdf'
    ) {

        die(
            "Only PDF files are allowed."
        );

    }

    $max_file_size =
        20 * 1024 * 1024;

    if (
        $file_size <= 0
    ) {

        die(
            "Uploaded file is empty."
        );

    }

    if (
        $file_size > $max_file_size
    ) {

        die(
            "PDF file is too large. Maximum allowed size is 20 MB."
        );

    }

    $upload_directory =
        dirname(__DIR__)
        . DIRECTORY_SEPARATOR
        . "uploads"
        . DIRECTORY_SEPARATOR
        . "documents"
        . DIRECTORY_SEPARATOR
        . "versions";

    if (
        !is_dir(
            $upload_directory
        )
    ) {

        if (
            !mkdir(
                $upload_directory,
                0777,
                true
            )
        ) {

            die(
                "Unable to create version upload directory."
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
        "uploads/documents/versions/"
        . $stored_file_name;

    if (
        !move_uploaded_file(
            $tmp_name,
            $full_file_path
        )
    ) {

        die(
            "Failed to upload the version PDF."
        );

    }

    $version =
        mysqli_real_escape_string(
            $conn,
            $version
        );

    $version_number =
        mysqli_real_escape_string(
            $conn,
            $version_number
        );

    $database_file_path =
        mysqli_real_escape_string(
            $conn,
            $database_file_path
        );

    $original_file_name =
        mysqli_real_escape_string(
            $conn,
            $original_file_name
        );

    $remarks =
        mysqli_real_escape_string(
            $conn,
            $remarks
        );

    $uploaded_by = "NULL";

    if (
        isset($_SESSION['user_id']) &&
        is_numeric($_SESSION['user_id'])
    ) {

        $uploaded_by =
            (int) $_SESSION['user_id'];

    }

    $insert_query = "
        INSERT INTO document_versions
        (
            document_id,
            version,
            version_number,
            file_name,
            file_path,
            uploaded_by,
            remarks
        )
        VALUES
        (
            $document_id,
            '$version',
            '$version_number',
            '$original_file_name',
            '$database_file_path',
            $uploaded_by,
            '$remarks'
        )
    ";

    $insert_result =
        mysqli_query(
            $conn,
            $insert_query
        );

    if (
        !$insert_result
    ) {

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
            "Database Insert Error: "
            . mysqli_error($conn)
        );

    }

    $update_document_query = "
        UPDATE legal_documents
        SET
            version = '$version'
        WHERE id = $document_id
    ";

    $update_result =
        mysqli_query(
            $conn,
            $update_document_query
        );

    if (
        !$update_result
    ) {

        die(
            "Version saved, but current document version could not be updated.<br><br>"
            . mysqli_error($conn)
        );

    }

    header(
        "Location: versions.php?id="
        . $document_id
    );

    exit();

}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>
Add New Document Version
</h1>

<p>
Upload a new version of this legal document.
</p>

<section>

<h2>

<?php

echo htmlspecialchars(
    $document['document_name']
);

?>

</h2>

<p>

<strong>
Document Number:
</strong>

<?php

echo htmlspecialchars(
    $document['document_number']
);

?>

</p>

<p>

<strong>
Current Version:
</strong>

<?php

echo htmlspecialchars(
    $document['version']
);

?>

</p>

<form
    method="POST"
    enctype="multipart/form-data"
>

<div class="form-group">

<label>
Version *
</label>

<input
    type="text"
    name="version"
    placeholder="Example: 1.1"
    required
>

</div>

<div class="form-group">

<label>
Version Number *
</label>

<input
    type="text"
    name="version_number"
    placeholder="Example: 2"
    required
>

</div>

<div class="form-group">

<label>
PDF File *
</label>

<input
    type="file"
    name="version_file"
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
Only PDF files are allowed. Maximum size: 20 MB.
</p>

</div>

<div class="form-group">

<label>
Remarks
</label>

<textarea
    name="remarks"
    rows="5"
    placeholder="Enter notes about this version..."
></textarea>

</div>

<div class="form-actions">

<a
    href="versions.php?id=<?php echo $document_id; ?>"
    class="btn-secondary"
>
Cancel
</a>

<button
    type="submit"
    class="btn-primary"
>
Save Version
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>