
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

    $status_name =
        trim($_POST['status_name'] ?? '');

    $description =
        trim($_POST['description'] ?? '');

    if ($status_name === '') {

        $error = "Status name is required.";

    } else {

        $query = "
            INSERT INTO case_statuses
            (
                status_name,
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
                $status_name,
                $description
            );

            if (mysqli_stmt_execute($stmt)) {

                $new_status_id =
                    mysqli_insert_id($conn);

                log_activity(
                    $conn,
                    "Case Statuses",
                    "CREATE",
                    "Status #" . $new_status_id,
                    null,
                    json_encode([
                        "status_name" =>
                            $status_name,
                        "description" =>
                            $description
                    ]),
                    "New case status created"
                );

                header(
                    "Location: view.php?id="
                    . $new_status_id
                );

                exit();

            } else {

                if (mysqli_errno($conn) == 1062) {

                    $error =
                        "This status name already exists. Please use a different status name.";

                } else {

                    $error =
                        "Unable to create case status: "
                        . mysqli_error($conn);

                }

            }

            mysqli_stmt_close($stmt);
        }
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>Add Case Status</h1>

<p>
Create a new status for the case workflow.
</p>

<section>

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

<form method="POST">

<div class="form-group">

<label>
Status Name *
</label>

<input
    type="text"
    name="status_name"
    maxlength="150"
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
></textarea>

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
    Save Status
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>