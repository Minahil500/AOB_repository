<?php
session_start();
include "../config/db.php";

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
        "Database Error: " .
        mysqli_error($conn)
    );
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $case_id = (int)($_POST['case_id'] ?? 0);
    $followup_date = $_POST['followup_date'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned_officer = trim($_POST['assigned_officer'] ?? '');
    $priority = trim($_POST['priority'] ?? 'Medium');
    $status = trim($_POST['status'] ?? 'Pending');
    $created_by = $_SESSION['user_id'] ?? 1;

    if (
        $case_id <= 0 ||
        $followup_date === "" ||
        $title === ""
    ) {
        $error = "Please fill all required fields.";
    } else {
        $query = "
            INSERT INTO case_followups
            (
                case_id,
                followup_date,
                title,
                description,
                assigned_officer,
                priority,
                status,
                created_by
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = mysqli_prepare(
            $conn,
            $query
        );

        if (!$stmt) {
            die(
                "Prepare failed: " .
                mysqli_error($conn)
            );
        }

        mysqli_stmt_bind_param(
            $stmt,
            "issssssi",
            $case_id,
            $followup_date,
            $title,
            $description,
            $assigned_officer,
            $priority,
            $status,
            $created_by
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php");
            exit();
        } else {
            $error =
                "Unable to create follow-up: " .
                mysqli_stmt_error($stmt);
        }
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<main>
<h1>Add Case Follow-up</h1>
<p>Create a new follow-up obligation for a legal case.</p>

<?php if ($error !== "") { ?>
<div class="form-error">
<?php echo htmlspecialchars($error); ?>
</div>
<?php } ?>

<section>
<form method="POST">

<div class="form-group">
<label>Case *</label>
<select name="case_id" required>
<option value="">Select Case</option>
<?php while ($case = mysqli_fetch_assoc($cases_result)) { ?>
<option
    value="<?php echo $case['id']; ?>"
    <?php
    echo (
        ($_POST['case_id'] ?? '') == $case['id']
    )
    ? 'selected'
    : '';
    ?>
>
<?php echo htmlspecialchars($case['case_number']); ?> - <?php echo htmlspecialchars($case['case_title']); ?>
</option>
<?php } ?>
</select>
</div>

<div class="form-group">
<label>Follow-up Date *</label>
<input
    type="date"
    name="followup_date"
    value="<?php echo htmlspecialchars($_POST['followup_date'] ?? ''); ?>"
    required
>
</div>

<div class="form-group">
<label>Title *</label>
<input
    type="text"
    name="title"
    value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
    placeholder="Enter follow-up title"
    required
>
</div>

<div class="form-group">
<label>Description</label>
<textarea
    name="description"
    rows="5"
    placeholder="Enter follow-up details"
><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
</div>

<div class="form-group">
<label>Assigned Officer</label>
<input
    type="text"
    name="assigned_officer"
    value="<?php echo htmlspecialchars($_POST['assigned_officer'] ?? ''); ?>"
    placeholder="Enter assigned officer"
>
</div>

<div class="form-group">
<label>Priority</label>
<select name="priority">
<option value="Low">Low</option>
<option
    value="Medium"
    <?php
    echo (
        ($_POST['priority'] ?? 'Medium') === 'Medium'
    )
    ? 'selected'
    : '';
    ?>
>
Medium
</option>
<option value="High">High</option>
<option value="Critical">Critical</option>
</select>
</div>

<div class="form-group">
<label>Status</label>
<select name="status">
<option
    value="Pending"
    <?php
    echo (
        ($_POST['status'] ?? 'Pending') === 'Pending'
    )
    ? 'selected'
    : '';
    ?>
>
Pending
</option>
<option value="In Progress">In Progress</option>
<option value="Completed">Completed</option>
<option value="Cancelled">Cancelled</option>
</select>
</div>

<div class="form-actions">
<a href="index.php" class="btn-secondary">Cancel</a>
<button type="submit" class="btn-primary">Create Follow-up</button>
</div>

</form>
</section>
</main>

<?php
include "../includes/footer.php";
?>