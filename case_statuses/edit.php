
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
    die("Invalid status ID.");
}

$status_id = (int)$_GET['id'];

$query = "
    SELECT
        id,
        status_name,
        description
    FROM case_statuses
    WHERE id = $status_id
    LIMIT 1
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die(
        "Database Error: " .
        mysqli_error($conn)
    );
}

if (mysqli_num_rows($result) == 0) {
    die("Case status not found.");
}

$status = mysqli_fetch_assoc($result);

$status_name =
    $status['status_name'];

$description =
    $status['description'] ?? '';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $status_name =
        trim($_POST['status_name'] ?? '');

    $description =
        trim($_POST['description'] ?? '');

    if ($status_name === '') {

        $error =
            "Status name is required.";

    } else {

        $old_values = [
            "status_name" =>
                $status['status_name'],
            "description" =>
                $status['description']
        ];

        $update_query = "
            UPDATE case_statuses
            SET
                status_name = ?,
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
                $status_name,
                $description,
                $status_id
            );

            if (mysqli_stmt_execute($stmt)) {

                log_activity(
                    $conn,
                    "Case Statuses",
                    "UPDATE",
                    "Status #" . $status_id,
                    json_encode($old_values),
                    json_encode([
                        "status_name" =>
                            $status_name,
                        "description" =>
                            $description
                    ]),
                    "Case status updated"
                );

                header(
                    "Location: view.php?id="
                    . $status_id
                );

                exit();

            } else {

                if (mysqli_errno($conn) == 1062) {

                    $error =
                        "This status name already exists. Please use a different status name.";

                } else {

                    $error =
                        "Unable to update case status: "
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

<h1>Edit Case Status</h1>

<p>
Update the selected case status.
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
    value="<?php
        echo htmlspecialchars(
            $status_name
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
    $description
);

?></textarea>

</div>

<div style="
    display:flex;
    gap:10px;
    flex-wrap:wrap;
">

<a
    href="view.php?id=<?php echo $status_id; ?>"
    class="btn-secondary"
>
    Cancel
</a>

<button
    type="submit"
    class="btn-primary"
>
    Update Status
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>