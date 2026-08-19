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

    die("Invalid document type ID.");

}

$query = "
    SELECT *
    FROM document_types
    WHERE id = ?
    LIMIT 1
";

$stmt = mysqli_prepare(
    $conn,
    $query
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    die("Document type not found.");

}

$type = mysqli_fetch_assoc($result);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $type_name = trim(
        $_POST['type_name'] ?? ''
    );

    $description = trim(
        $_POST['description'] ?? ''
    );

    if ($type_name === '') {

        $error =
            "Document type name is required.";

    } else {

        $check_query = "
            SELECT id
            FROM document_types
            WHERE type_name = ?
            AND id != ?
            LIMIT 1
        ";

        $check_stmt = mysqli_prepare(
            $conn,
            $check_query
        );

        mysqli_stmt_bind_param(
            $check_stmt,
            "si",
            $type_name,
            $id
        );

        mysqli_stmt_execute($check_stmt);

        $check_result =
            mysqli_stmt_get_result(
                $check_stmt
            );

        if (
            mysqli_num_rows(
                $check_result
            ) > 0
        ) {

            $error =
                "This document type already exists.";

        } else {

            $update_query = "
                UPDATE document_types
                SET
                    type_name = ?,
                    description = ?
                WHERE id = ?
            ";

            $update_stmt = mysqli_prepare(
                $conn,
                $update_query
            );

            mysqli_stmt_bind_param(
                $update_stmt,
                "ssi",
                $type_name,
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
                    "Document Types",
                    "UPDATE",
                    "Document Type #" . $id,
                    json_encode($type),
                    json_encode([
                        "type_name" =>
                            $type_name,
                        "description" =>
                            $description
                    ]),
                    "Document type updated."
                );

                header(
                    "Location: index.php"
                );

                exit();

            } else {

                $error =
                    "Unable to update document type: "
                    . mysqli_stmt_error(
                        $update_stmt
                    );
            }

            mysqli_stmt_close(
                $update_stmt
            );
        }

        mysqli_stmt_close(
            $check_stmt
        );
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>
Edit Document Type
</h1>

<p>
Update document type information.
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

<?php echo htmlspecialchars($error); ?>

</div>

<?php } ?>

<section>

<form method="POST">

<div class="form-group">

<label>
Document Type *
</label>

<input
    type="text"
    name="type_name"
    value="<?php
        echo htmlspecialchars(
            $type['type_name']
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
    $type['description'] ?? ''
);
?></textarea>

</div>

<div style="
    display:flex;
    gap:10px;
    flex-wrap:wrap;
">

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
Update Document Type
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>