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
    $type_name = trim($_POST['type_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($type_name === '') {
        $error = "Case type name is required.";
    } else {
        $check_query = "
            SELECT id
            FROM case_types
            WHERE type_name = ?
            LIMIT 1
        ";

        $check_stmt = mysqli_prepare($conn, $check_query);

        if (!$check_stmt) {
            $error = "Database Error: " . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($check_stmt, "s", $type_name);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);

            if (mysqli_num_rows($check_result) > 0) {
                $error = "This case type already exists.";
            } else {
                $insert_query = "
                    INSERT INTO case_types
                    (
                        type_name,
                        description
                    )
                    VALUES
                    (
                        ?,
                        ?
                    )
                ";

                $stmt = mysqli_prepare($conn, $insert_query);

                if (!$stmt) {
                    $error = "Database Error: " . mysqli_error($conn);
                } else {
                    mysqli_stmt_bind_param(
                        $stmt,
                        "ss",
                        $type_name,
                        $description
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        $new_id = mysqli_insert_id($conn);

                        log_activity(
                            $conn,
                            "Case Types",
                            "CREATE",
                            "Case Type #" . $new_id,
                            null,
                            json_encode([
                                "type_name" => $type_name,
                                "description" => $description
                            ]),
                            "New case type created."
                        );

                        header("Location: index.php");
                        exit();
                    } else {
                        $error = "Unable to create case type: " . mysqli_stmt_error($stmt);
                    }

                    mysqli_stmt_close($stmt);
                }
            }

            mysqli_stmt_close($check_stmt);
        }
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<main>

<h1>Add Case Type</h1>

<p>Create a new case type.</p>

<?php if ($error !== '') { ?>

<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:15px;border-radius:8px;margin-bottom:20px;">
<?php echo htmlspecialchars($error); ?>
</div>

<?php } ?>

<section>

<form method="POST">

<div class="form-group">

<label>Case Type Name *</label>

<input
    type="text"
    name="type_name"
    required
>

</div>

<div class="form-group">

<label>Description</label>

<textarea
    name="description"
    rows="5"
></textarea>

</div>

<div class="form-actions">

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
Save Case Type
</button>

</div>

</form>

</section>

</main>

<?php
include "../includes/footer.php";
?>