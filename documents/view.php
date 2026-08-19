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

$query = "
    SELECT
        ld.*,

        dt.type_name AS document_type,

        f.firm_name,

        c.case_number,
        c.case_title,

        co.court_name,
        co.city AS court_city

    FROM legal_documents ld

    LEFT JOIN document_types dt
        ON ld.document_type_id = dt.id

    LEFT JOIN firms f
        ON ld.firm_id = f.id

    LEFT JOIN cases c
        ON ld.case_id = c.id

    LEFT JOIN courts co
        ON ld.court_id = co.id

    WHERE ld.id = $document_id

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

$document = mysqli_fetch_assoc(
    $result
);

$ocr_status =
    $document['ocr_status']
    ?? 'Pending';

$pdf_type = "Unknown";

if (
    $ocr_status === 'Not Required'
) {

    $pdf_type = "Searchable PDF";

} elseif (
    $ocr_status === 'Pending' ||
    $ocr_status === 'Processing' ||
    $ocr_status === 'Completed' ||
    $ocr_status === 'Failed'
) {

    $pdf_type = "Scanned PDF";

}

$extracted_text =
    trim(
        $document['extracted_text']
        ?? ''
    );

$extracted_json =
    trim(
        $document['extracted_json']
        ?? ''
    );

include "../includes/header.php";

include "../includes/sidebar.php";

?>

<main>

<h1>
Document Details
</h1>

<p>
Complete information about the selected legal document.
</p>

<section>

<h2>

<?php

echo htmlspecialchars(
    $document['document_name']
    ?? ''
);

?>

</h2>

<table>

<tr>

<th>
Document Number
</th>

<td>

<?php

echo htmlspecialchars(
    $document['document_number']
    ?? ''
);

?>

</td>

</tr>

<tr>

<th>
Document Type
</th>

<td>

<?php

echo htmlspecialchars(
    $document['document_type']
    ?? 'N/A'
);

?>

</td>

</tr>

<tr>

<th>
PDF Type
</th>

<td>

<strong>

<?php

echo htmlspecialchars(
    $pdf_type
);

?>

</strong>

</td>

</tr>

<tr>

<th>
OCR Status
</th>

<td>

<strong>

<?php

echo htmlspecialchars(
    $ocr_status
);

?>

</strong>

</td>

</tr>

<tr>

<th>
Firm
</th>

<td>

<?php

echo htmlspecialchars(
    $document['firm_name']
    ?? 'N/A'
);

?>

</td>

</tr>

<tr>

<th>
Case
</th>

<td>

<?php

if (
    !empty(
        $document['case_number']
    )
) {

    echo htmlspecialchars(
        $document['case_number']
    );

    if (
        !empty(
            $document['case_title']
        )
    ) {

        echo " - ";

        echo htmlspecialchars(
            $document['case_title']
        );

    }

} else {

    echo "N/A";

}

?>

</td>

</tr>

<tr>

<th>
Court
</th>

<td>

<?php

if (
    !empty(
        $document['court_name']
    )
) {

    echo htmlspecialchars(
        $document['court_name']
    );

    if (
        !empty(
            $document['court_city']
        )
    ) {

        echo " - ";

        echo htmlspecialchars(
            $document['court_city']
        );

    }

} else {

    echo "N/A";

}

?>

</td>

</tr>

<tr>

<th>
Version
</th>

<td>

<?php

echo htmlspecialchars(
    $document['version']
    ?? ''
);

?>

</td>

</tr>

<tr>

<th>
Document Date
</th>

<td>

<?php

echo htmlspecialchars(
    $document['document_date']
    ?? ''
);

?>

</td>

</tr>

<tr>

<th>
File Name
</th>

<td>

<?php

echo htmlspecialchars(
    $document['file_name']
    ?? ''
);

?>

</td>

</tr>

<tr>

<th>
File Size
</th>

<td>

<?php

if (
    !empty(
        $document['file_size']
    )
) {

    echo number_format(
        $document['file_size'] / 1024,
        2
    );

    echo " KB";

} else {

    echo "N/A";

}

?>

</td>

</tr>

<tr>

<th>
Description
</th>

<td>

<?php

echo nl2br(
    htmlspecialchars(
        $document['description']
        ?? ''
    )
);

?>

</td>

</tr>

<tr>

<th>
Created At
</th>

<td>

<?php

echo htmlspecialchars(
    $document['created_at']
    ?? ''
);

?>

</td>

</tr>

<tr>

<th>
Updated At
</th>

<td>

<?php

echo htmlspecialchars(
    $document['updated_at']
    ?? ''
);

?>

</td>

</tr>

</table>

<div
    style="
        margin-top:25px;
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    "
>

<?php

if (
    !empty(
        $document['file_path']
    )
) {

?>

<a
    href="<?php

        echo htmlspecialchars(
            $document['file_path']
        );

    ?>"
    target="_blank"
    class="btn-primary"
>
    Open PDF
</a>

<?php

}

?>

<a
    href="edit.php?id=<?php echo $document_id; ?>"
    class="btn-primary"
>
    Edit
</a>

<a
    href="../ocr/detect.php?id=<?php echo $document_id; ?>"
    class="btn-secondary"
>
    Detect PDF
</a>

<a
    href="versions.php?id=<?php echo $document_id; ?>"
    class="btn-primary"
>
    Version History
</a>

<a
    href="tags.php?id=<?php echo $document_id; ?>"
    class="btn-primary"
>
    Manage Tags
</a>

<?php

if (
    !empty(
        $document['file_name']
    ) &&
    (
        $ocr_status === 'Pending' ||
        $ocr_status === 'Failed'
    )
) {

?>

<a
    href="../ocr/process.php?id=<?php echo $document_id; ?>"
    class="btn-primary"
>
    Run OCR
</a>

<?php

}

?>

<a
    href="index.php"
    class="btn-secondary"
>
    Back to Documents
</a>

</div>

</section>

<section>

<h2>
Extracted Text
</h2>

<p>
Text extracted directly from a searchable PDF or through OCR from a scanned PDF.
</p>

<?php

if (
    $extracted_text !== ''
) {

?>

<div
    style="
        background:#f8fafc;
        border:1px solid #e5e7eb;
        border-radius:8px;
        padding:20px;
        max-height:650px;
        overflow:auto;
        white-space:pre-wrap;
        line-height:1.7;
        font-size:14px;
        color:#374151;
    "
>

<?php

echo htmlspecialchars(
    $extracted_text
);

?>

</div>

<?php

} else {

?>

<div
    style="
        background:#fff7ed;
        border:1px solid #fed7aa;
        border-radius:8px;
        padding:20px;
    "
>

<strong>
No extracted text available.
</strong>

<p style="margin-bottom:0;">

This document has not been processed yet.

</p>

<?php

if (
    !empty(
        $document['file_name']
    )
) {

?>

<div style="margin-top:15px;">

<a
    href="../ocr/detect.php?id=<?php echo $document_id; ?>"
    class="btn-secondary"
>
    Detect PDF
</a>

<?php

if (
    $ocr_status === 'Pending' ||
    $ocr_status === 'Failed'
) {

?>

<a
    href="../ocr/process.php?id=<?php echo $document_id; ?>"
    class="btn-primary"
>
    Run OCR
</a>

<?php

}

?>

</div>

<?php

}

?>

</div>

<?php

}

?>

</section>

<section>

<h2>
Extracted JSON
</h2>

<p>
Structured JSON generated from the PDF extraction/OCR process.
</p>

<?php

if (
    $extracted_json !== ''
) {

$decoded_json =
    json_decode(
        $extracted_json,
        true
    );

?>

<div
    style="
        background:#111827;
        border:1px solid #374151;
        border-radius:8px;
        padding:20px;
        max-height:650px;
        overflow:auto;
    "
>

<pre
    style="
        margin:0;
        color:#f9fafb;
        white-space:pre-wrap;
        word-break:break-word;
        line-height:1.6;
        font-size:14px;
    "
><?php

if (
    json_last_error() === JSON_ERROR_NONE
) {

    echo htmlspecialchars(
        json_encode(
            $decoded_json,
            JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE
        )
    );

} else {

    echo htmlspecialchars(
        $extracted_json
    );

}

?></pre>

</div>

<?php

} else {

?>

<div
    style="
        background:#f8fafc;
        border:1px solid #e5e7eb;
        border-radius:8px;
        padding:20px;
    "
>

<strong>
No JSON data available.
</strong>

<p style="margin-bottom:0;">

JSON will be generated when the document is processed.

</p>

</div>

<?php

}

?>

</section>

</main>

<?php

include "../includes/footer.php";

?>