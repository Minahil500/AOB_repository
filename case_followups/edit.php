<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../config/db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid follow-up ID.");
}

$followup_id = (int) $_GET['id'];

$query = "
    SELECT *
    FROM case_followups
    WHERE id = $followup_id
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
    die("Follow-up not found.");
}

$followup = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $case_id = (int) ($_POST['case_id'] ?? 0);
    $followup_date = trim($_POST['followup_date'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned_officer = trim($_POST['assigned_officer'] ?? '');
    $priority = trim($_POST['priority'] ?? 'Medium');
    $status = trim($_POST['status'] ?? 'Pending');

    if ($case_id <= 0) {
        die("Please select a case.");
    }

    if ($followup_date === "") {
        die("Please select a follow-up date.");
    }

    if ($title === "") {
        die("Please enter a follow-up title.");
    }

    $followup_date = mysqli_real_escape_string($conn, $followup_date);
    $title = mysqli_real_escape_string($conn, $title);
    $description = mysqli_real_escape_string($conn, $description);
    $assigned_officer = mysqli_real_escape_string($conn, $assigned_officer);
    $priority = mysqli_real_escape_string($conn, $priority);
    $status = mysqli_real_escape_string($conn, $status);

    $update_query = "
        UPDATE case_followups
        SET
            case_id = $case_id,
            followup_date = '$followup_date',
            title = '$title',
            description = '$description',
            assigned_officer = '$assigned_officer',
            priority = '$priority',
            status = '$status'
        WHERE id = $followup_id
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

    header("Location: index.php");
    exit();
}

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
        "CASES ERROR: " .
        mysqli_error($conn)
    );
}

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<main>
<h1>Edit Case Follow-up</h1>
<p>Update the follow-up information.</p>

<section>
<form
    method="POST"
    action="edit.php?id=<?php echo $followup_id; ?>"
>

<div class="form-group">
<label>Case *</label>

<select
    name="case_id"
    required
>

<option value="">
Select Case
</option>

<?php while ($case = mysqli_fetch_assoc($cases_result)) { ?>

<option
    value="<?php echo $case['id']; ?>"
    <?php
    if ($case['id'] == $followup['case_id']) {
        echo "selected";
    }
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
    value="<?php echo htmlspecialchars($followup['followup_date'] ?? ''); ?>"
    required
>
</div>

<div class="form-group">
<label>Title *</label>

<input
    type="text"
    name="title"
    value="<?php echo htmlspecialchars($followup['title'] ?? ''); ?>"
    required
>
</div>

<div class="form-group">
<label>Description</label>

<textarea
    name="description"
    rows="5"
><?php echo htmlspecialchars($followup['description'] ?? ''); ?></textarea>
</div>

<div class="form-group">
<label>Assigned Officer</label>

<input
    type="text"
    name="assigned_officer"
    value="<?php echo htmlspecialchars($followup['assigned_officer'] ?? ''); ?>"
>
</div>

<div class="form-group">
<label>Priority</label>

<select name="priority">

<option
    value="Low"
    <?php
    if (($followup['priority'] ?? '') == 'Low') {
        echo "selected";
    }
    ?>
>
Low
</option>

<option
    value="Medium"
    <?php
    if (($followup['priority'] ?? '') == 'Medium') {
        echo "selected";
    }
    ?>
>
Medium
</option>

<option
    value="High"
    <?php
    if (($followup['priority'] ?? '') == 'High') {
        echo "selected";
    }
    ?>
>
High
</option>

<option
    value="Critical"
    <?php
    if (($followup['priority'] ?? '') == 'Critical') {
        echo "selected";
    }
    ?>
>
Critical
</option>

</select>
</div>

<div class="form-group">
<label>Status</label>

<select name="status">

<option
    value="Pending"
    <?php
    if (($followup['status'] ?? '') == 'Pending') {
        echo "selected";
    }
    ?>
>
Pending
</option>

<option
    value="In Progress"
    <?php
    if (($followup['status'] ?? '') == 'In Progress') {
        echo "selected";
    }
    ?>
>
In Progress
</option>

<option
    value="Completed"
    <?php
    if (($followup['status'] ?? '') == 'Completed') {
        echo "selected";
    }
    ?>
>
Completed
</option>

<option
    value="Cancelled"
    <?php
    if (($followup['status'] ?? '') == 'Cancelled') {
        echo "selected";
    }
    ?>
>
Cancelled
</option>

</select>
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
Save Changes
</button>

</div>

</form>
</section>
</main>

<?php
include "../includes/footer.php";
?>