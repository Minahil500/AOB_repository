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

$user_id = (int) $_SESSION['user_id'];

$upload_directory =
    "../uploads/case_attachments/";

if (!is_dir($upload_directory)) {
    mkdir(
        $upload_directory,
        0777,
        true
    );
}

$error = "";

$cases_query = "
    SELECT
        id,
        case_number,
        case_title
    FROM cases
    ORDER BY id DESC
";

$cases_result =
    mysqli_query(
        $conn,
        $cases_query
    );

if (!$cases_result) {
    die(
        "Cases Database Error: " .
        mysqli_error($conn)
    );
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $case_id =
        (int)($_POST['case_id'] ?? 0);

    $category =
        trim(
            $_POST['category'] ?? ''
        );

    $document_date =
        trim(
            $_POST['document_date'] ?? ''
        );

    $version =
        trim(
            $_POST['version'] ?? '1.0'
        );

    $description =
        trim(
            $_POST['description'] ?? ''
        );

    if ($case_id <= 0) {

        $error =
            "Please select a case.";

    } elseif (
        $category === ''
    ) {

        $error =
            "Document category is required.";

    } elseif (
        !isset($_FILES['attachment']) ||
        $_FILES['attachment']['error']
        !== UPLOAD_ERR_OK
    ) {

        $error =
            "Please select a file.";

    } else {

        $file =
            $_FILES['attachment'];

        $original_name =
            $file['name'];

        $temporary_name =
            $file['tmp_name'];

        $file_size =
            (int)$file['size'];

        $extension =
            strtolower(
                pathinfo(
                    $original_name,
                    PATHINFO_EXTENSION
                )
            );

        $allowed_extensions = [
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'jpg',
            'jpeg',
            'png'
        ];

        if (
            !in_array(
                $extension,
                $allowed_extensions,
                true
            )
        ) {

            $error =
                "File type is not allowed.";

        } elseif (
            $file_size > 20 * 1024 * 1024
        ) {

            $error =
                "Maximum file size is 20 MB.";

        } else {

            $safe_name =
                preg_replace(
                    '/[^A-Za-z0-9._-]/',
                    '_',
                    $original_name
                );

            $unique_name =
                time()
                . "_"
                . uniqid()
                . "_"
                . $safe_name;

            $target_file =
                $upload_directory
                . $unique_name;

            if (
                move_uploaded_file(
                    $temporary_name,
                    $target_file
                )
            ) {

                $database_file_path =
                    "uploads/case_attachments/"
                    . $unique_name;

                $insert_query = "
                    INSERT INTO case_attachments
                    (
                        case_id,
                        file_name,
                        file_path,
                        category,
                        document_date,
                        version,
                        description,
                        uploaded_by
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        NULLIF(?, ''),
                        ?,
                        ?,
                        ?
                    )
                ";

                $stmt =
                    mysqli_prepare(
                        $conn,
                        $insert_query
                    );

                if (!$stmt) {

                    unlink($target_file);

                    $error =
                        "Database error: "
                        . mysqli_error($conn);

                } else {

                    mysqli_stmt_bind_param(
                        $stmt,
                        "issssssi",
                        $case_id,
                        $original_name,
                        $database_file_path,
                        $category,
                        $document_date,
                        $version,
                        $description,
                        $user_id
                    );

                    if (
                        mysqli_stmt_execute(
                            $stmt
                        )
                    ) {

                        $attachment_id =
                            mysqli_insert_id(
                                $conn
                            );

                        log_activity(
                            $conn,
                            "Case Attachments",
                            "CREATE",
                            "Attachment #" . $attachment_id,
                            null,
                            json_encode([
                                "case_id" =>
                                    $case_id,
                                "file_name" =>
                                    $original_name,
                                "category" =>
                                    $category,
                                "document_date" =>
                                    $document_date,
                                "version" =>
                                    $version
                            ]),
                            "Case attachment uploaded."
                        );

                        header(
                            "Location: index.php"
                        );

                        exit();

                    } else {

                        unlink($target_file);

                        $error =
                            "Unable to save attachment: "
                            . mysqli_stmt_error(
                                $stmt
                            );
                    }

                    mysqli_stmt_close(
                        $stmt
                    );
                }
            } else {

                $error =
                    "Unable to upload the file.";
            }
        }
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>
Upload Case Attachment
</h1>

<p>
Upload a document and attach it to a legal case.
</p>

<?php if ($error !== '') { ?>

<div style="
    background:#fef2f2;
    border:1px solid #fecaca;
    color:#991b1b;
    padding:15px;
    border-radius:8px;
    margin-bottom:20px;
">

<?php

echo htmlspecialchars(
    $error
);

?>

</div>

<?php } ?>

<section>

<form
    method="POST"
    enctype="multipart/form-data"
>

<div class="form-group">

<label>
Case *
</label>

<select
    name="case_id"
    required
>

<option value="">
-- Select Case --
</option>

<?php while (
    $case =
        mysqli_fetch_assoc(
            $cases_result
        )
) { ?>

<option
    value="<?php echo (int)$case['id']; ?>"
>

<?php

echo htmlspecialchars(
    $case['case_number']
    . " - "
    . $case['case_title']
);

?>

</option>

<?php } ?>

</select>

</div>

<div class="form-group">

<label>
File *
</label>

<input
    type="file"
    name="attachment"
    required
>

<small>
Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG.
Maximum size: 20 MB.
</small>

</div>

<div class="form-group">

<label>
Category *
</label>

<select
    name="category"
    required
>

<option value="">
-- Select Category --
</option>

<option value="Court Order">
Court Order
</option>

<option value="Legal Notice">
Legal Notice
</option>

<option value="Evidence">
Evidence
</option>

<option value="Correspondence">
Correspondence
</option>

<option value="Audit Report">
Audit Report
</option>

<option value="Other">
Other
</option>

</select>

</div>

<div class="form-group">

<label>
Document Date
</label>

<input
    type="date"
    name="document_date"
>

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
Description
</label>

<textarea
    name="description"
    rows="5"
></textarea>

</div>

<div
    style="
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    "
>

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
Upload Attachment
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>