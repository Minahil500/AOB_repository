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

$user_id = (int)$_SESSION['user_id'];
$error = "";

$cases_query = "
    SELECT
        id,
        case_number,
        case_title
    FROM cases
    ORDER BY id DESC
";

$cases_result = mysqli_query($conn, $cases_query);

if (!$cases_result) {
    die("Cases Database Error: " . mysqli_error($conn));
}

$status_query = "
    SELECT
        id,
        status_name
    FROM case_statuses
    ORDER BY status_name ASC
";

$status_result = mysqli_query($conn, $status_query);

if (!$status_result) {
    die("Status Database Error: " . mysqli_error($conn));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $case_id = (int)($_POST['case_id'] ?? 0);
    $old_status_id = !empty($_POST['old_status_id']) ? (int)$_POST['old_status_id'] : null;
    $new_status_id = (int)($_POST['new_status_id'] ?? 0);
    $remarks = trim($_POST['remarks'] ?? '');

    if ($case_id <= 0) {
        $error = "Please select a case.";
    } elseif ($new_status_id <= 0) {
        $error = "Please select the new status.";
    } elseif ($old_status_id !== null && $old_status_id === $new_status_id) {
        $error = "Old status and new status cannot be the same.";
    } else {
        $insert_query = "
            INSERT INTO case_status_history
            (
                case_id,
                old_status_id,
                new_status_id,
                remarks,
                changed_by
            )
            VALUES (?, ?, ?, ?, ?)
        ";

        $stmt = mysqli_prepare($conn, $insert_query);

        if (!$stmt) {
            $error = "Database error: " . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param(
                $stmt,
                "iiisi",
                $case_id,
                $old_status_id,
                $new_status_id,
                $remarks,
                $user_id
            );

            if (mysqli_stmt_execute($stmt)) {
                $history_id = mysqli_insert_id($conn);

                log_activity(
                    $conn,
                    "Case Status History",
                    "CREATE",
                    "History #" . $history_id,
                    null,
                    json_encode([
                        "case_id" => $case_id,
                        "old_status_id" => $old_status_id,
                        "new_status_id" => $new_status_id,
                        "remarks" => $remarks,
                        "changed_by" => $user_id
                    ]),
                    "Case status history entry created."
                );

                header("Location: index.php");
                exit();
            } else {
                $error = "Unable to save status history: " . mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        }
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<main>

<h1>Add Case Status History</h1>

<p>Record a status change for a legal case.</p>

<?php if ($error !== '') { ?>

<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:15px;border-radius:8px;margin-bottom:20px;">
<?php echo htmlspecialchars($error); ?>
</div>

<?php } ?>

<section>

<form method="POST">

<div class="form-group">

<label>Case *</label>

<select name="case_id" required>

<option value="">-- Select Case --</option>

<?php while ($case = mysqli_fetch_assoc($cases_result)) { ?>

<option value="<?php echo (int)$case['id']; ?>">
<?php echo htmlspecialchars($case['case_number'] . " - " . $case['case_title']); ?>
</option>

<?php } ?>

</select>

</div>

<div class="form-group">

<label>Old Status</label>

<select name="old_status_id">

<option value="">-- None / Initial Status --</option>

<?php

mysqli_data_seek($status_result, 0);

while ($status = mysqli_fetch_assoc($status_result)) {

?>

<option value="<?php echo (int)$status['id']; ?>">
<?php echo htmlspecialchars($status['status_name']); ?>
</option>

<?php } ?>

</select>

</div>

<div class="form-group">

<label>New Status *</label>

<select name="new_status_id" required>

<option value="">-- Select New Status --</option>

<?php

mysqli_data_seek($status_result, 0);

while ($status = mysqli_fetch_assoc($status_result)) {

?>

<option value="<?php echo (int)$status['id']; ?>">
<?php echo htmlspecialchars($status['status_name']); ?>
</option>

<?php } ?>

</select>

</div>

<div class="form-group">

<label>Remarks</label>

<textarea
    name="remarks"
    rows="5"
    placeholder="Enter reason or remarks for this status change..."
></textarea>

</div>

<div style="display:flex;gap:10px;flex-wrap:wrap;">

<a href="index.php" class="btn-secondary">Cancel</a>

<button type="submit" class="btn-primary">
Save Status History
</button>

</div>

</form>

</section>

</main>

<?php
include "../includes/footer.php";
?>