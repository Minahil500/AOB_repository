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

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {
    die("Invalid stage ID.");
}

$stage_id = (int) $_GET['id'];

$query = "
    SELECT
        id,
        stage_name,
        description
    FROM case_stages
    WHERE id = $stage_id
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

if (mysqli_num_rows($result) == 0) {
    die("Case stage not found.");
}

$stage = mysqli_fetch_assoc($result);

$stage_name = $stage['stage_name'];
$description = $stage['description'] ?? '';
$error = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stage_name = trim($_POST['stage_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($stage_name === '') {
        $error = "Stage name is required.";
    } else {
        $old_values = [
            "stage_name" => $stage['stage_name'],
            "description" => $stage['description']
        ];

        $update_query = "
            UPDATE case_stages
            SET
                stage_name = ?,
                description = ?
            WHERE id = ?
        ";

        $stmt = mysqli_prepare(
            $conn,
            $update_query
        );

        if (!$stmt) {
            $error =
                "Database Error: " .
                mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $stage_name,
                $description,
                $stage_id
            );

            if (mysqli_stmt_execute($stmt)) {
                log_activity(
                    $conn,
                    "Case Stages",
                    "UPDATE",
                    "Stage #" . $stage_id,
                    json_encode($old_values),
                    json_encode([
                        "stage_name" => $stage_name,
                        "description" => $description
                    ]),
                    "Case stage updated"
                );

                header(
                    "Location: view.php?id=" .
                    $stage_id
                );
                exit();
            } else {
                if (mysqli_errno($conn) == 1062) {
                    $error =
                        "This stage name already exists. Please use a different name.";
                } else {
                    $error =
                        "Unable to update case stage: " .
                        mysqli_error($conn);
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
<h1>
Edit Case Stage
</h1>
<p>
Update the selected case stage.
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
    value="<?php
        echo htmlspecialchars(
            $stage_name
        );
    ?>"
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
><?php
echo htmlspecialchars(
    $description
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
    href="view.php?id=<?php echo $stage_id; ?>"
    class="btn-secondary"
>
    Cancel
</a>
<button
    type="submit"
    class="btn-primary"
>
    Update Stage
</button>
</div>
</form>
</section>
</main>
<?php
include "../includes/footer.php";
?>