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

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stage_name = trim($_POST['stage_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($stage_name === '') {
        $error = "Stage name is required.";
    } else {
        $query = "
            INSERT INTO case_stages
            (
                stage_name,
                description
            )
            VALUES
            (
                ?,
                ?
            )
        ";

        $stmt = mysqli_prepare(
            $conn,
            $query
        );

        if (!$stmt) {
            $error =
                "Database Error: " .
                mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param(
                $stmt,
                "ss",
                $stage_name,
                $description
            );

            if (mysqli_stmt_execute($stmt)) {
                $new_stage_id =
                    mysqli_insert_id($conn);

                log_activity(
                    $conn,
                    "Case Stages",
                    "CREATE",
                    "Stage #" . $new_stage_id,
                    null,
                    json_encode([
                        "stage_name" => $stage_name,
                        "description" => $description
                    ]),
                    "New case stage created"
                );

                header(
                    "Location: view.php?id=" .
                    $new_stage_id
                );
                exit();
            } else {
                $error =
                    "Unable to create case stage: " .
                    mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        }
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<main>
<h1>
Add Case Stage
</h1>
<p>
Create a new stage for the case workflow.
</p>
<section>
<?php if ($error !== '') { ?>
<div
    style="
        background:#fef2f2;
        border:1px solid #fecaca;
        color:#991b1b;
        padding:15px;
        border-radius:8px;
        margin-bottom:20px;
    "
>
<?php
echo htmlspecialchars(
    $error
);
?>
</div>
<?php } ?>
<form method="POST">
<div class="form-group">
<label>
Stage Name *
</label>
<input
    type="text"
    name="stage_name"
    required
    maxlength="150"
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
    Save Stage
</button>
</div>
</form>
</section>
</main>
<?php
include "../includes/footer.php";
?>