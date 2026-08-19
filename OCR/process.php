<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$ghostscript_dir =
    'C:\\Program Files\\gs\\gs10.07.1\\bin';

$ghostscript_exe =
    $ghostscript_dir . '\\gswin64c.exe';

if (!file_exists($ghostscript_exe)) {

    die(
        "Ghostscript was not found.<br><br>" .
        "<strong>Expected path:</strong><br>" .
        htmlspecialchars($ghostscript_exe)
    );

}

$current_path =
    getenv('PATH');

putenv(
    'PATH=' .
    $ghostscript_dir .
    ';' .
    $current_path
);

include "../config/db.php";

require_once "../includes/permission.php";

require_permission(
    $conn,
    "Manage Documents"
);

require_once "../includes/activity_logger.php";

if (!extension_loaded('imagick')) {

    die(
        "Imagick extension is not installed or enabled."
    );

}

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {

    die(
        "Invalid document ID."
    );

}

$document_id =
    (int) $_GET['id'];

$query = "
    SELECT
    id,
    document_name,
    file_name,
    file_path,
    ocr_status,
    extracted_text,
    extracted_json
FROM legal_documents
    WHERE id = $document_id
    LIMIT 1
";

$result =
    mysqli_query(
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

    die(
        "Document not found."
    );

}

$document =
    mysqli_fetch_assoc($result);

$pdf_type = "Unknown";

if (
    !empty($document['extracted_text']) &&
    ($document['ocr_status'] ?? '') === 'Not Required'
) {

    $pdf_type = "Searchable PDF";

} elseif (
    ($document['ocr_status'] ?? '') === 'Completed'
) {

    $pdf_type = "Scanned PDF";

} elseif (
    ($document['ocr_status'] ?? '') === 'Pending'
) {

    $pdf_type = "Scanned PDF";

}

$file_path =
    trim(
        $document['file_path'] ?? ''
    );

if (
    $file_path === ''
) {

    die(
        "No PDF file path is attached to this document."
    );

}

$file_path =
    str_replace(
        '/',
        DIRECTORY_SEPARATOR,
        $file_path
    );

$file_path =
    preg_replace(
        '#^(\.\.[\\\\/])+?#',
        '',
        $file_path
    );

$file_path =
    ltrim(
        $file_path,
        "\\/"
    );

$full_pdf_path =
    dirname(__DIR__)
    . DIRECTORY_SEPARATOR
    . $file_path;

$real_pdf_path =
    realpath(
        $full_pdf_path
    );

if (
    $real_pdf_path === false ||
    !file_exists($real_pdf_path)
) {

    die(
        "PDF file not found.<br><br>" .
        "<strong>Path checked:</strong><br>" .
        htmlspecialchars(
            $full_pdf_path
        )
    );

}

$full_pdf_path =
    $real_pdf_path;

if (
    !is_readable($full_pdf_path)
) {

    die(
        "PDF file exists but PHP cannot read it.<br><br>" .
        htmlspecialchars(
            $full_pdf_path
        )
    );

}

$temp_directory =
    dirname(__DIR__)
    . DIRECTORY_SEPARATOR
    . "temp_images";

if (
    !is_dir($temp_directory)
) {

    if (
        !mkdir(
            $temp_directory,
            0777,
            true
        )
    ) {

        die(
            "Unable to create temp_images directory."
        );

    }

}

$document_temp_directory =
    $temp_directory
    . DIRECTORY_SEPARATOR
    . "document_"
    . $document_id;

if (
    !is_dir(
        $document_temp_directory
    )
) {

    if (
        !mkdir(
            $document_temp_directory,
            0777,
            true
        )
    ) {

        die(
            "Unable to create document temp directory."
        );

    }

}

$old_files =
    glob(
        $document_temp_directory
        . DIRECTORY_SEPARATOR
        . "*"
    );

if (
    !empty($old_files)
) {

    foreach (
        $old_files as $old_file
    ) {

        if (
            is_file($old_file)
        ) {

            @unlink(
                $old_file
            );

        }

    }

}

$tesseract =
    'C:\\Program Files\\Tesseract-OCR\\tesseract.exe';

if (
    !file_exists($tesseract)
) {

    mysqli_query(
        $conn,
        "
        UPDATE legal_documents
        SET ocr_status = 'Failed'
        WHERE id = $document_id
        "
    );

    die(
        "Tesseract.exe not found.<br><br>" .
        "<strong>Expected path:</strong><br>" .
        htmlspecialchars(
            $tesseract
        )
    );

}

$status_query = "
    UPDATE legal_documents
    SET ocr_status = 'Processing'
    WHERE id = $document_id
";

$status_result =
    mysqli_query(
        $conn,
        $status_query
    );

if (!$status_result) {

    die(
        "Unable to update OCR status: " .
        mysqli_error($conn)
    );

}

log_activity(
    $conn,
    "Legal Documents",
    "OCR",
    "Document ID: " . $document_id,
    $document['ocr_status'] ?? null,
    "Processing",
    "OCR processing started for legal document."
);

$output_prefix =
    $document_temp_directory
    . DIRECTORY_SEPARATOR
    . "page";

$images = [];

try {

    $imagick =
        new Imagick();

    $imagick->setResolution(
        200,
        200
    );

    $imagick->readImage(
        $full_pdf_path
    );

    $page_count =
        $imagick->getNumberImages();

    if (
        $page_count <= 0
    ) {

        throw new Exception(
            "No pages could be read from the PDF."
        );

    }

    $page_number = 1;

    foreach (
        $imagick as $page
    ) {

        $page->setImageFormat(
            "png"
        );

        $image_path =
            $output_prefix
            . "-"
            . $page_number
            . ".png";

        $write_result =
            $page->writeImage(
                $image_path
            );

        if (
            !$write_result ||
            !file_exists($image_path)
        ) {

            throw new Exception(
                "Failed to generate PNG image for page "
                . $page_number
            );

        }

        $images[] =
            $image_path;

        $page_number++;

    }

    $imagick->clear();

    $imagick->destroy();

} catch (
    Throwable $e
) {

    mysqli_query(
        $conn,
        "
        UPDATE legal_documents
        SET ocr_status = 'Failed'
        WHERE id = $document_id
        "
    );

    log_activity(
        $conn,
        "Legal Documents",
        "OCR",
        "Document ID: " . $document_id,
        "Processing",
        "Failed",
        "PDF to image conversion failed: "
        . $e->getMessage()
    );

    echo "<!DOCTYPE html>";

    echo "<html>";

    echo "<head>";

    echo "<meta charset='UTF-8'>";

    echo "<title>PDF Conversion Error</title>";

    echo "</head>";

    echo "<body>";

    echo "<div style='max-width:900px;margin:50px auto;padding:30px;'>";

    echo "<h2>PDF to image conversion failed.</h2>";

    echo "<p>";

    echo "<strong>Error:</strong>";

    echo "</p>";

    echo "<pre>";

    echo htmlspecialchars(
        $e->getMessage()
    );

    echo "</pre>";

    echo "<p>";

    echo "<strong>PDF Path:</strong>";

    echo "</p>";

    echo "<pre>";

    echo htmlspecialchars(
        $full_pdf_path
    );

    echo "</pre>";

    echo "<p>";

    echo "<strong>Ghostscript:</strong>";

    echo "</p>";

    echo "<pre>";

    echo htmlspecialchars(
        $ghostscript_exe
    );

    echo "</pre>";

    echo "<p>";

    echo "<strong>Temp Directory:</strong>";

    echo "</p>";

    echo "<pre>";

    echo htmlspecialchars(
        $document_temp_directory
    );

    echo "</pre>";

    echo "<br>";

    echo "<a href='../documents/view.php?id="
        . $document_id
        . "'>";

    echo "Back to Document";

    echo "</a>";

    echo "</div>";

    echo "</body>";

    echo "</html>";

    exit;

}

if (
    empty($images)
) {

    mysqli_query(
        $conn,
        "
        UPDATE legal_documents
        SET ocr_status = 'Failed'
        WHERE id = $document_id
        "
    );

    log_activity(
        $conn,
        "Legal Documents",
        "OCR",
        "Document ID: " . $document_id,
        "Processing",
        "Failed",
        "Imagick did not generate PNG images."
    );

    die(
        "PDF conversion completed but no PNG images were generated."
    );

}

natsort(
    $images
);

$all_text = '';

$ocr_errors = [];

$page_number = 1;

foreach (
    $images as $image
) {

    $ocr_output =
        $document_temp_directory
        . DIRECTORY_SEPARATOR
        . "ocr_page_"
        . $page_number;

    $ocr_command =
        '"' . $tesseract . '"'
        . ' "' . $image . '"'
        . ' "' . $ocr_output . '"'
        . ' -l eng'
        . ' --psm 3';

    $ocr_result = [];

    $ocr_return_code = 0;

    exec(
        $ocr_command . ' 2>&1',
        $ocr_result,
        $ocr_return_code
    );

    if (
        $ocr_return_code !== 0
    ) {

        $ocr_errors[] =
            "Page "
            . $page_number
            . ": "
            . implode(
                " ",
                $ocr_result
            );

        $page_number++;

        continue;

    }

    $text_file =
        $ocr_output
        . ".txt";

    if (
        file_exists($text_file)
    ) {

        $page_text =
            file_get_contents(
                $text_file
            );

        if (
            $page_text !== false
        ) {

            $page_text =
                trim(
                    $page_text
                );

            if (
                $page_text !== ''
            ) {

                $all_text .=
                    "\n\n"
                    . "===== PAGE "
                    . $page_number
                    . " =====\n\n"
                    . $page_text;

            }

        }

    }

    $page_number++;

}

$all_text =
    trim(
        $all_text
    );

if (
    $all_text === ''
) {

    mysqli_query(
        $conn,
        "
        UPDATE legal_documents
        SET ocr_status = 'Failed'
        WHERE id = $document_id
        "
    );

    log_activity(
        $conn,
        "Legal Documents",
        "OCR",
        "Document ID: " . $document_id,
        "Processing",
        "Failed",
        "OCR completed but no text was extracted."
    );

    echo "<!DOCTYPE html>";

    echo "<html>";

    echo "<head>";

    echo "<meta charset='UTF-8'>";

    echo "<title>OCR Error</title>";

    echo "</head>";

    echo "<body>";

    echo "<div style='max-width:900px;margin:50px auto;padding:30px;'>";

    echo "<h2>OCR completed but no text was extracted.</h2>";

    echo "<p>";

    echo "<strong>Pages converted:</strong> ";

    echo count($images);

    echo "</p>";

    if (
        !empty($ocr_errors)
    ) {

        echo "<h3>OCR Errors</h3>";

        echo "<pre>";

        echo htmlspecialchars(
            implode(
                "\n",
                $ocr_errors
            )
        );

        echo "</pre>";

    }

    echo "<p>";

    echo "<strong>PDF:</strong> ";

    echo htmlspecialchars(
        $document['file_name'] ?? ''
    );

    echo "</p>";

    echo "<a href='../documents/view.php?id="
        . $document_id
        . "'>";

    echo "Back to Document";

    echo "</a>";

    echo "</div>";

    echo "</body>";

    echo "</html>";

    exit;

}

$json_data = json_encode(
    [
        "document_id" => $document_id,
        "file_name" =>
            $document['file_name'] ?? '',
        "document_type" =>
            "Scanned PDF",
        "ocr_applied" =>
            true,
        "ocr_status" =>
            "Completed",
        "pages_processed" =>
            count($images),
        "extracted_text" =>
            $all_text
    ],
    JSON_UNESCAPED_UNICODE |
    JSON_PRETTY_PRINT
);

$safe_text =
    mysqli_real_escape_string(
        $conn,
        $all_text
    );

$safe_json =
    mysqli_real_escape_string(
        $conn,
        $json_data
    );

$update_query = "
    UPDATE legal_documents
    SET
        extracted_text = '$safe_text',
        extracted_json = '$safe_json',
        ocr_status = 'Completed'
    WHERE id = $document_id
";

$update_result =
    mysqli_query(
        $conn,
        $update_query
    );

if (!$update_result) {

    die(
        "Database Update Error: " .
        mysqli_error($conn)
    );

}

$update_result =
    mysqli_query(
        $conn,
        $update_query
    );

if (
    !$update_result
) {

    die(
        "Database Update Error: " .
        mysqli_error($conn)
    );

}

log_activity(
    $conn,
    "Legal Documents",
    "OCR",
    "Document ID: " . $document_id,
    "Processing",
    "Completed",
    "OCR processing completed successfully. "
    . count($images)
    . " page(s) processed."
);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>
OCR Processing Completed
</title>

<link
    rel="stylesheet"
    href="/aob_repository/assets/css/style.css"
>

</head>

<body>

<div
    style="
        max-width:1000px;
        margin:40px auto;
        padding:30px;
        background:#ffffff;
        border:1px solid #e5e7eb;
        border-radius:10px;
    "
>

<h1>
OCR Processing Completed
</h1>

<h2>

<?php

echo htmlspecialchars(
    $document['document_name'] ?? ''
);

?>

</h2>

<p>

<strong>
File:
</strong>

<?php

echo htmlspecialchars(
    $document['file_name'] ?? ''
);

?>

</p>

<p>

<strong>
OCR Status:
</strong>

<span>
Completed
</span>

</p>

<p>

<strong>
Pages Processed:
</strong>

<?php

echo count($images);

?>

</p>

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
        max-height:600px;
        overflow:auto;
        line-height:1.6;
    "
>

<?php

echo htmlspecialchars(
    $all_text
);

?>

</div>

<div
    style="
        margin-top:25px;
    "
>

<a
    href="../documents/view.php?id=<?php echo $document_id; ?>"
    class="btn-primary"
>
Back to Document
</a>

<a
    href="../documents/index.php"
    class="btn-secondary"
>
Documents
</a>

</div>

</div>

</body>

</html>