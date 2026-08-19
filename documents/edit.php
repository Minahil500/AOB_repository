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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    die("Invalid document ID.");

}

$document_id = (int) $_GET['id'];

$query = "
    SELECT *
    FROM legal_documents
    WHERE id = $document_id
    LIMIT 1
";

$result = mysqli_query($conn, $query);

if (!$result) {

    die(
        "SELECT ERROR: " .
        mysqli_error($conn)
    );

}

if (mysqli_num_rows($result) == 0) {

    die("Document not found.");

}

$document = mysqli_fetch_assoc($result);

$previous_value = json_encode([
    "document_number" => $document['document_number'],
    "document_name" => $document['document_name'],
    "document_type_id" => $document['document_type_id'],
    "firm_id" => $document['firm_id'],
    "case_id" => $document['case_id'],
    "court_id" => $document['court_id'],
    "version" => $document['version'],
    "ocr_status" => $document['ocr_status'],
    "document_date" => $document['document_date'],
    "description" => $document['description']
]);

$types_result = mysqli_query(
    $conn,
    "
    SELECT id, type_name
    FROM document_types
    ORDER BY type_name ASC
    "
);

if (!$types_result) {

    die(
        "Document Types Error: " .
        mysqli_error($conn)
    );

}

$firms_result = mysqli_query(
    $conn,
    "
    SELECT id, firm_name
    FROM firms
    ORDER BY firm_name ASC
    "
);

if (!$firms_result) {

    die(
        "Firms Error: " .
        mysqli_error($conn)
    );

}

$cases_result = mysqli_query(
    $conn,
    "
    SELECT id, case_number, case_title
    FROM cases
    ORDER BY id DESC
    "
);

if (!$cases_result) {

    die(
        "Cases Error: " .
        mysqli_error($conn)
    );

}

$courts_result = mysqli_query(
    $conn,
    "
    SELECT id, court_name, city
    FROM courts
    ORDER BY court_name ASC
    "
);

if (!$courts_result) {

    die(
        "Courts Error: " .
        mysqli_error($conn)
    );

}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $document_number =
        trim(
            $_POST['document_number'] ?? ''
        );

    $document_name =
        trim(
            $_POST['document_name'] ?? ''
        );

    $document_type_id =
        (int) (
            $_POST['document_type_id']
            ?? 0
        );

    $firm_id =
        (int) (
            $_POST['firm_id']
            ?? 0
        );

    $case_id =
        (int) (
            $_POST['case_id']
            ?? 0
        );

    $court_id =
        (int) (
            $_POST['court_id']
            ?? 0
        );

    $version =
        trim(
            $_POST['version'] ?? ''
        );

    $ocr_status =
        trim(
            $_POST['ocr_status'] ?? ''
        );

    $document_date =
        trim(
            $_POST['document_date'] ?? ''
        );

    $description =
        trim(
            $_POST['description'] ?? ''
        );

    if ($document_number === '') {

        die("Document number is required.");

    }

    if ($document_name === '') {

        die("Document name is required.");

    }

    if ($document_type_id <= 0) {

        die("Please select a document type.");

    }

    if ($firm_id <= 0) {

        die("Please select a firm.");

    }

    if ($case_id <= 0) {

        die("Please select a case.");

    }

    if ($document_date === '') {

        die("Please select document date.");

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

    $update_query = "
        UPDATE legal_documents
        SET
            document_number = '$document_number',
            document_name = '$document_name',
            document_type_id = $document_type_id,
            firm_id = $firm_id,
            case_id = $case_id,
            court_id = NULLIF($court_id, 0),
            version = '$version',
            ocr_status = '$ocr_status',
            document_date = '$document_date',
            description = '$description'
        WHERE id = $document_id
    ";

    $update_result = mysqli_query(
        $conn,
        $update_query
    );

    if (!$update_result) {

        die(
            "UPDATE ERROR: " .
            mysqli_error($conn)
        );

    }

    $new_value = json_encode([
        "document_number" => $document_number,
        "document_name" => $document_name,
        "document_type_id" => $document_type_id,
        "firm_id" => $firm_id,
        "case_id" => $case_id,
        "court_id" => $court_id,
        "version" => $version,
        "ocr_status" => $ocr_status,
        "document_date" => $document_date,
        "description" => $description
    ]);

    log_activity(
        $conn,
        "Legal Documents",
        "UPDATE",
        "Document ID: " . $document_id,
        $previous_value,
        $new_value,
        "Legal document updated."
    );

    header(
        "Location: view.php?id="
        . $document_id
    );

    exit();

}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>
Edit Legal Document
</h1>

<p>
Update the legal document information.
</p>

<section>

<form
    method="POST"
    action="edit.php?id=<?php echo $document_id; ?>"
>

<div class="form-group">

<label>
Document Number *
</label>

<input
    type="text"
    name="document_number"
    value="<?php
        echo htmlspecialchars(
            $document['document_number']
            ?? ''
        );
    ?>"
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
    value="<?php
        echo htmlspecialchars(
            $document['document_name']
            ?? ''
        );
    ?>"
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
        echo $type['id'];
    ?>"
    <?php

    if (
        $type['id']
        == $document['document_type_id']
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
        echo $firm['id'];
    ?>"
    <?php

    if (
        $firm['id']
        == $document['firm_id']
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
        echo $case['id'];
    ?>"
    <?php

    if (
        $case['id']
        == $document['case_id']
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
        echo $court['id'];
    ?>"
    <?php

    if (
        $court['id']
        == $document['court_id']
    ) {

        echo "selected";

    }

    ?>
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
    value="<?php
        echo htmlspecialchars(
            $document['version']
            ?? '1.0'
        );
    ?>"
>

</div>

<div class="form-group">

<label>
OCR Status
</label>

<select
    name="ocr_status"
>

<option
    value="Pending"
    <?php

    if (
        ($document['ocr_status'] ?? '')
        == 'Pending'
    ) {

        echo "selected";

    }

    ?>
>
Pending
</option>

<option
    value="Not Required"
    <?php

    if (
        ($document['ocr_status'] ?? '')
        == 'Not Required'
    ) {

        echo "selected";

    }

    ?>
>
Not Required
</option>

<option
    value="Processing"
    <?php

    if (
        ($document['ocr_status'] ?? '')
        == 'Processing'
    ) {

        echo "selected";

    }

    ?>
>
Processing
</option>

<option
    value="Completed"
    <?php

    if (
        ($document['ocr_status'] ?? '')
        == 'Completed'
    ) {

        echo "selected";

    }

    ?>
>
Completed
</option>

<option
    value="Failed"
    <?php

    if (
        ($document['ocr_status'] ?? '')
        == 'Failed'
    ) {

        echo "selected";

    }

    ?>
>
Failed
</option>

</select>

</div>

<div class="form-group">

<label>
Document Date *
</label>

<input
    type="date"
    name="document_date"
    value="<?php
        echo htmlspecialchars(
            $document['document_date']
            ?? ''
        );
    ?>"
    required
>

</div>

<div class="form-group">

<label>
Description
</label>

<textarea
    name="description"
    rows="5"
><?php

echo htmlspecialchars(
    $document['description']
    ?? ''
);

?></textarea>

</div>

<div class="form-group">

<label>
Current File
</label>

<p>

<?php

if (
    !empty(
        $document['file_name']
    )
) {

    echo htmlspecialchars(
        $document['file_name']
    );

} else {

    echo "No file attached.";

}

?>

</p>

</div>

<div class="form-actions">

<a
    href="view.php?id=<?php echo $document_id; ?>"
    class="btn-secondary"
>
Cancel
</a>

<button
    type="submit"
    class="btn-primary"
>
Save Changes
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>