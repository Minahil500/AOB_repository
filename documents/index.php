<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "../config/db.php";
require_once "../includes/permission.php";

require_permission(
    $conn,
    "View Documents"
);

$document_number = trim($_GET['document_number'] ?? '');
$document_name = trim($_GET['document_name'] ?? '');

$document_type_id = (int) ($_GET['document_type_id'] ?? 0);
$firm_id = (int) ($_GET['firm_id'] ?? 0);
$case_id = (int) ($_GET['case_id'] ?? 0);

$ocr_status = trim($_GET['ocr_status'] ?? '');
$document_date = trim($_GET['document_date'] ?? '');

$types_query = "
    SELECT
        id,
        type_name
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
    SELECT
        id,
        firm_name
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

$query = "
    SELECT
        ld.id,
        ld.document_number,
        ld.document_name,
        ld.version,
        ld.ocr_status,
        ld.document_date,
        ld.file_name,
        ld.file_size,
        ld.created_at,

        dt.type_name AS document_type,

        f.firm_name,

        c.case_number,
        c.case_title

    FROM legal_documents ld

    LEFT JOIN document_types dt
        ON ld.document_type_id = dt.id

    LEFT JOIN firms f
        ON ld.firm_id = f.id

    LEFT JOIN cases c
        ON ld.case_id = c.id

    WHERE 1 = 1
";

if ($document_number !== '') {

    $safe_document_number =
        mysqli_real_escape_string(
            $conn,
            $document_number
        );

    $query .= "
        AND ld.document_number LIKE
        '%$safe_document_number%'
    ";

}

if ($document_name !== '') {

    $safe_document_name =
        mysqli_real_escape_string(
            $conn,
            $document_name
        );

    $query .= "
        AND ld.document_name LIKE
        '%$safe_document_name%'
    ";

}

if ($document_type_id > 0) {

    $query .= "
        AND ld.document_type_id =
        $document_type_id
    ";

}

if ($firm_id > 0) {

    $query .= "
        AND ld.firm_id =
        $firm_id
    ";

}

if ($case_id > 0) {

    $query .= "
        AND ld.case_id =
        $case_id
    ";

}

if ($ocr_status !== '') {

    $safe_ocr_status =
        mysqli_real_escape_string(
            $conn,
            $ocr_status
        );

    $query .= "
        AND ld.ocr_status =
        '$safe_ocr_status'
    ";

}

if ($document_date !== '') {

    $safe_document_date =
        mysqli_real_escape_string(
            $conn,
            $document_date
        );

    $query .= "
        AND ld.document_date =
        '$safe_document_date'
    ";

}

$query .= "
    ORDER BY ld.id DESC
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

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>
Legal Documents
</h1>

<p>
Manage legal documents, versions and OCR information.
</p>

<div style="margin-bottom:20px;">

<a
    href="create.php"
    class="btn-primary"
>
    + Add Legal Document
</a>

</div>

<section
    style="
        margin-bottom:25px;
        padding:20px;
        border:1px solid #e5e7eb;
        border-radius:8px;
        background:#f8fafc;
    "
>

<h2>
Search & Filters
</h2>

<form
    method="GET"
>

<div
    style="
        display:grid;
        grid-template-columns:
        repeat(auto-fit,minmax(200px,1fr));
        gap:15px;
    "
>

<div>

<label>
Document Number
</label>

<input
    type="text"
    name="document_number"
    value="<?php
        echo htmlspecialchars(
            $document_number
        );
    ?>"
    placeholder="Search document number"
>

</div>

<div>

<label>
Document Name
</label>

<input
    type="text"
    name="document_name"
    value="<?php
        echo htmlspecialchars(
            $document_name
        );
    ?>"
    placeholder="Search document name"
>

</div>

<div>

<label>
Document Type
</label>

<select
    name="document_type_id"
>

<option value="">
All Document Types
</option>

<?php

while (
    $type =
        mysqli_fetch_assoc(
            $types_result
        )
) {

?>

<option
    value="<?php
        echo (int) $type['id'];
    ?>"
    <?php

    if (
        $document_type_id ==
        $type['id']
    ) {

        echo "selected";

    }

    ?>
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

<div>

<label>
Firm
</label>

<select
    name="firm_id"
>

<option value="">
All Firms
</option>

<?php

while (
    $firm =
        mysqli_fetch_assoc(
            $firms_result
        )
) {

?>

<option
    value="<?php
        echo (int) $firm['id'];
    ?>"
    <?php

    if (
        $firm_id ==
        $firm['id']
    ) {

        echo "selected";

    }

    ?>
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

</div>

<div
    style="
        display:grid;
        grid-template-columns:
        repeat(auto-fit,minmax(200px,1fr));
        gap:15px;
        margin-top:15px;
    "
>

<div>

<label>
Case
</label>

<select
    name="case_id"
>

<option value="">
All Cases
</option>

<?php

while (
    $case =
        mysqli_fetch_assoc(
            $cases_result
        )
) {

?>

<option
    value="<?php
        echo (int) $case['id'];
    ?>"
    <?php

    if (
        $case_id ==
        $case['id']
    ) {

        echo "selected";

    }

    ?>
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

<div>

<label>
OCR Status
</label>

<select
    name="ocr_status"
>

<option value="">
All OCR Statuses
</option>

<option
    value="Pending"
    <?php
    echo $ocr_status === 'Pending'
        ? 'selected'
        : '';
    ?>
>
Pending
</option>

<option
    value="Not Required"
    <?php
    echo $ocr_status === 'Not Required'
        ? 'selected'
        : '';
    ?>
>
Not Required
</option>

<option
    value="Processing"
    <?php
    echo $ocr_status === 'Processing'
        ? 'selected'
        : '';
    ?>
>
Processing
</option>

<option
    value="Completed"
    <?php
    echo $ocr_status === 'Completed'
        ? 'selected'
        : '';
    ?>
>
Completed
</option>

<option
    value="Failed"
    <?php
    echo $ocr_status === 'Failed'
        ? 'selected'
        : '';
    ?>
>
Failed
</option>

</select>

</div>

<div>

<label>
Document Date
</label>

<input
    type="date"
    name="document_date"
    value="<?php
        echo htmlspecialchars(
            $document_date
        );
    ?>"
>

</div>

</div>

<div
    style="
        margin-top:20px;
    "
>

<button
    type="submit"
    class="btn-primary"
>
    Search
</button>

<a
    href="index.php"
    class="btn-secondary"
>
    Reset
</a>

</div>

</form>

</section>

<section>

<?php

if (
    mysqli_num_rows($result) == 0
) {

?>

<p>
No legal documents found matching your filters.
</p>

<?php

} else {

?>

<div
    style="
        margin-bottom:10px;
    "
>

<strong>

<?php

echo mysqli_num_rows($result);

?>

document(s) found.

</strong>

</div>

<div
    style="
        overflow-x:auto;
    "
>

<table>

<thead>

<tr>

<th>
ID
</th>

<th>
Document Number
</th>

<th>
Document Name
</th>

<th>
Document Type
</th>

<th>
Firm
</th>

<th>
Case
</th>

<th>
Version
</th>

<th>
OCR Status
</th>

<th>
Document Date
</th>

<th>
File
</th>

<th>
Actions
</th>

</tr>

</thead>

<tbody>

<?php

while (
    $row =
        mysqli_fetch_assoc($result)
) {

?>

<tr>

<td>

<?php

echo (int) $row['id'];

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $row['document_number']
    ?? ''
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $row['document_name']
    ?? ''
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $row['document_type']
    ?? 'N/A'
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $row['firm_name']
    ?? 'N/A'
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $row['case_number']
    ?? 'N/A'
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $row['version']
    ?? ''
);

?>

</td>

<td>

<?php

$current_ocr_status =
    $row['ocr_status']
    ?? 'Pending';

echo htmlspecialchars(
    $current_ocr_status
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $row['document_date']
    ?? ''
);

?>

</td>

<td>

<?php

if (
    !empty(
        $row['file_name']
    )
) {

    echo htmlspecialchars(
        $row['file_name']
    );

} else {

    echo "No file";

}

?>

</td>

<td
    style="
        white-space:nowrap;
    "
>

<a
    href="view.php?id=<?php
        echo (int) $row['id'];
    ?>"
    class="btn-secondary"
>
    View
</a>

<a
    href="edit.php?id=<?php
        echo (int) $row['id'];
    ?>"
    class="btn-primary"
>
    Edit
</a>

<a
    href="delete.php?id=<?php
        echo (int) $row['id'];
    ?>"
    class="btn-danger"
    onclick="
        return confirm(
            'Are you sure you want to delete this document?'
        );
    "
>
    Delete
</a>

<?php

if (
    !empty(
        $row['file_name']
    )
) {

?>

<a
    href="../ocr/detect.php?id=<?php
        echo (int) $row['id'];
    ?>"
    class="btn-secondary"
>
    Detect
</a>

<?php

}

?>

<?php

if (
    !empty(
        $row['file_name']
    )
    &&
    (
        $current_ocr_status === 'Pending'
        ||
        $current_ocr_status === 'Failed'
    )
) {

?>

<a
    href="../ocr/process.php?id=<?php
        echo (int) $row['id'];
    ?>"
    class="btn-primary"
>
    Run OCR
</a>

<?php

}

?>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<?php

}

?>

</section>

</main>

<?php

include "../includes/footer.php";

?>