<?php

session_start();

include "../config/db.php";

$firms_query = "
    SELECT id, firm_name
    FROM firms
    WHERE status = 'active'
    ORDER BY firm_name ASC
";

$firms_result = mysqli_query($conn, $firms_query);

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $case_number = trim($_POST['case_number'] ?? '');
    $firm_id = (int) ($_POST['firm_id'] ?? 0);
    $case_title = trim($_POST['case_title'] ?? '');
    $case_type = trim($_POST['case_type'] ?? '');
    $regulation_violated = trim($_POST['regulation_violated'] ?? '');
    $assigned_officer = trim($_POST['assigned_officer'] ?? '');
    $court_name = trim($_POST['court_name'] ?? '');
    $priority = trim($_POST['priority'] ?? 'Medium');
    $status = trim($_POST['status'] ?? 'Draft');
    $has_court_order = isset($_POST['has_court_order']) ? 1 : 0;
    $next_followup_date = !empty($_POST['next_followup_date'])
        ? $_POST['next_followup_date']
        : null;

    $created_by = $_SESSION['user_id'] ?? 1;

    if (
        $case_number == "" ||
        $firm_id <= 0 ||
        $case_title == "" ||
        $case_type == ""
    ) {

        $error = "Please fill all required fields.";

    } else {

        $check_query = "
            SELECT id
            FROM cases
            WHERE case_number = ?
            LIMIT 1
        ";

        $check_stmt = mysqli_prepare(
            $conn,
            $check_query
        );

        mysqli_stmt_bind_param(
            $check_stmt,
            "s",
            $case_number
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result(
            $check_stmt
        );

        if (mysqli_num_rows($check_result) > 0) {

            $error = "Case number already exists.";

        } else {

            $query = "
                INSERT INTO cases
                (
                    case_number,
                    firm_id,
                    case_title,
                    case_type,
                    regulation_violated,
                    assigned_officer,
                    court_name,
                    priority,
                    status,
                    has_court_order,
                    next_followup_date,
                    created_by
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt = mysqli_prepare($conn, $query);

            if (!$stmt) {

                die(
                    "Prepare failed: " .
                    mysqli_error($conn)
                );

            }

            mysqli_stmt_bind_param(
                $stmt,
                "sisssssssiis",
                $case_number,
                $firm_id,
                $case_title,
                $case_type,
                $regulation_violated,
                $assigned_officer,
                $court_name,
                $priority,
                $status,
                $has_court_order,
                $next_followup_date,
                $created_by
            );

            if (mysqli_stmt_execute($stmt)) {

                header("Location: index.php");
                exit();

            } else {

                $error = "Unable to create case: " . mysqli_error($conn);

            }

        }

    }

}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>Add Case</h1>

<p>
Register a new legal case.
</p>

<?php if ($error != "") { ?>

<div class="form-error">

<?php echo htmlspecialchars($error); ?>

</div>

<?php } ?>

<section>

<form method="POST">

<div class="form-group">

<label>
Case Number *
</label>

<input
    type="text"
    name="case_number"
    value="<?php echo htmlspecialchars($_POST['case_number'] ?? ''); ?>"
    required
>

</div>

<div class="form-group">

<label>
Firm *
</label>

<select name="firm_id" required>

<option value="">
Select Firm
</option>

<?php

if ($firms_result) {

    while ($firm = mysqli_fetch_assoc($firms_result)) {

?>

<option
    value="<?php echo $firm['id']; ?>"
    <?php
    echo (
        ($_POST['firm_id'] ?? '') == $firm['id']
    ) ? 'selected' : '';
    ?>
>
    <?php echo htmlspecialchars($firm['firm_name']); ?>
</option>

<?php

    }

}

?>

</select>

</div>

<div class="form-group">

<label>
Case Title *
</label>

<input
    type="text"
    name="case_title"
    value="<?php echo htmlspecialchars($_POST['case_title'] ?? ''); ?>"
    required
>

</div>

<div class="form-group">

<label>
Case Type *
</label>

<select name="case_type" required>

<option value="">
Select Case Type
</option>

<option value="Enforcement">Enforcement</option>
<option value="Legal">Legal</option>
<option value="Appeal">Appeal</option>
<option value="Disciplinary">Disciplinary</option>
<option value="Other">Other</option>

</select>

</div>

<div class="form-group">

<label>
Regulation Violated
</label>

<textarea
    name="regulation_violated"
    rows="3"
><?php echo htmlspecialchars($_POST['regulation_violated'] ?? ''); ?></textarea>

</div>

<div class="form-group">

<label>
Assigned Officer
</label>

<input
    type="text"
    name="assigned_officer"
    value="<?php echo htmlspecialchars($_POST['assigned_officer'] ?? ''); ?>"
>

</div>

<div class="form-group">

<label>
Court Name
</label>

<input
    type="text"
    name="court_name"
    value="<?php echo htmlspecialchars($_POST['court_name'] ?? ''); ?>"
>

</div>

<div class="form-group">

<label>
Priority
</label>

<select name="priority">

<option value="Low">Low</option>
<option value="Medium" selected>Medium</option>
<option value="High">High</option>
<option value="Critical">Critical</option>

</select>

</div>

<div class="form-group">

<label>
Status
</label>

<select name="status">

<option value="Draft">Draft</option>
<option value="Open">Open</option>
<option value="Under Review">Under Review</option>
<option value="Referred to Court">Referred to Court</option>
<option value="Closed">Closed</option>
<option value="Archived">Archived</option>

</select>

</div>

<div class="form-group">

<label>

<input
    type="checkbox"
    name="has_court_order"
    value="1"
>
Has Court Order

</label>

</div>

<div class="form-group">

<label>
Next Follow-up Date
</label>

<input
    type="date"
    name="next_followup_date"
    value="<?php echo htmlspecialchars($_POST['next_followup_date'] ?? ''); ?>"
>

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
Create Case
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>