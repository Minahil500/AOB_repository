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

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid attachment ID.");
}

$query = "
    SELECT *
    FROM case_attachments
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

if (mysqli_num_rows($result) == 0) {
    die("Attachment not found.");
}

$attachment =
    mysqli_fetch_assoc(
        $result
    );

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

    } elseif ($category === '') {

        $error =
            "Document category is required.";

    } else {

        $update_query = "
            UPDATE case_attachments
            SET
                case_id = ?,
                category = ?,
                document_date = NULLIF(?, ''),
                version = ?,
                description = ?
            WHERE id = ?
        ";

        $update_stmt =
            mysqli_prepare(
                $conn,
                $update_query
            );

        mysqli_stmt_bind_param(
            $update_stmt,
            "issssi",
            $case_id,
            $category,
            $document_date,
            $version,
            $description,
            $id
        );

        if (
            mysqli_stmt_execute(
                $update_stmt
            )
        ) {

            log_activity(
                $conn,
                "Case Attachments",
                "UPDATE",
                "Attachment #" . $id,
                json_encode($attachment),
                json_encode([
                    "case_id" =>
                        $case_id,
                    "category" =>
                        $category,
                    "document_date" =>
                        $document_date,
                    "version" =>
                        $version,
                    "description" =>
                        $description
                ]),
                "Case attachment information updated."
            );

            header(
                "Location: index.php"
            );

            exit();

        } else {

            $error =
                "Unable to update attachment: "
                . mysqli_stmt_error(
                    $update_stmt
                );
        }

        mysqli_stmt_close(
            $update_stmt
        );
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>
Edit Case Attachment
</h1>

<p>
Update attachment information.
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

<form method="POST">

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
    <?php

    echo
        (int)$case['id']
        ===
        (int)$attachment['case_id']
        ? 'selected'
        : '';

    ?>
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
Current File
</label>

<p>

<a
    href="../<?php
        echo htmlspecialchars(
            $attachment['file_path']
        );
    ?>"
    target="_blank"
>

<?php

echo htmlspecialchars(
    $attachment['file_name']
);

?>

</a>

</p>

<small>
File replacement can be handled separately if required.
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

<option
    value="Court Order"
    <?php

    echo
        $attachment['category']
        === 'Court Order'
        ? 'selected'
        : '';

    ?>
>
Court Order
</option>

<option
    value="Legal Notice"
    <?php

    echo
        $attachment['category']
        === 'Legal Notice'
        ? 'selected'
        : '';

    ?>
>
Legal Notice
</option>

<option
    value="Evidence"
    <?php

    echo
        $attachment['category']
        === 'Evidence'
        ? 'selected'
        : '';

    ?>
>
Evidence
</option>

<option
    value="Correspondence"
    <?php

    echo
        $attachment['category']
        === 'Correspondence'
        ? 'selected'
        : '';

    ?>
>
Correspondence
</option>

<option
    value="Audit Report"
    <?php

    echo
        $attachment['category']
        === 'Audit Report'
        ? 'selected'
        : '';

    ?>
>
Audit Report
</option>

<option
    value="Other"
    <?php

    echo
        $attachment['category']
        === 'Other'
        ? 'selected'
        : '';

    ?>
>
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
    value="<?php
        echo htmlspecialchars(
            $attachment['document_date']
            ?? ''
        );
    ?>"
>

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
            $attachment['version']
            ?? '1.0'
        );
    ?>"
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
    $attachment['description']
    ?? ''
);

?></textarea>

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
Update Attachment
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>