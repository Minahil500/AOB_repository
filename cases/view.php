
<?php

session_start();

include "../config/db.php";


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: index.php");
    exit();

}

$case_id = (int) $_GET['id'];


$query = "
    SELECT
        c.*,
        f.firm_name
    FROM cases c
    LEFT JOIN firms f
        ON c.firm_id = f.id
    WHERE c.id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $case_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$case = mysqli_fetch_assoc($result);


if (!$case) {

    die("Case not found.");

}


include "../includes/header.php";
include "../includes/sidebar.php";

?>


<main>

<h1>Case Details</h1>

<p>
Complete information for this legal case.
</p>


<section>

<h2>
<?php echo htmlspecialchars($case['case_number']); ?>
</h2>


<div class="details-grid">


<div class="detail-item">

<span class="detail-label">
Case Number
</span>

<span class="detail-value">
<?php echo htmlspecialchars($case['case_number']); ?>
</span>

</div>


<div class="detail-item">

<span class="detail-label">
Firm
</span>

<span class="detail-value">
<?php
echo htmlspecialchars(
    $case['firm_name'] ?? '-'
);
?>
</span>

</div>


<div class="detail-item">

<span class="detail-label">
Case Title
</span>

<span class="detail-value">
<?php echo htmlspecialchars($case['case_title']); ?>
</span>

</div>


<div class="detail-item">

<span class="detail-label">
Case Type
</span>

<span class="detail-value">
<?php echo htmlspecialchars($case['case_type']); ?>
</span>

</div>


<div class="detail-item">

<span class="detail-label">
Regulation Violated
</span>

<span class="detail-value">
<?php
echo nl2br(
    htmlspecialchars(
        $case['regulation_violated'] ?? '-'
    )
);
?>
</span>

</div>


<div class="detail-item">

<span class="detail-label">
Assigned Officer
</span>

<span class="detail-value">
<?php
echo htmlspecialchars(
    $case['assigned_officer'] ?? '-'
);
?>
</span>

</div>


<div class="detail-item">

<span class="detail-label">
Court Name
</span>

<span class="detail-value">
<?php
echo htmlspecialchars(
    $case['court_name'] ?? '-'
);
?>
</span>

</div>


<div class="detail-item">

<span class="detail-label">
Priority
</span>

<span class="detail-value">
<?php echo htmlspecialchars($case['priority']); ?>
</span>

</div>


<div class="detail-item">

<span class="detail-label">
Status
</span>

<span class="detail-value">
<?php echo htmlspecialchars($case['status']); ?>
</span>

</div>


<div class="detail-item">

<span class="detail-label">
Court Order
</span>

<span class="detail-value">

<?php

echo $case['has_court_order']
    ? 'Yes'
    : 'No';

?>

</span>

</div>


<div class="detail-item">

<span class="detail-label">
Next Follow-up
</span>

<span class="detail-value">
<?php
echo htmlspecialchars(
    $case['next_followup_date'] ?? '-'
);
?>
</span>

</div>


<div class="detail-item">

<span class="detail-label">
Created At
</span>

<span class="detail-value">
<?php echo htmlspecialchars($case['created_at']); ?>
</span>

</div>


<div class="detail-item">

<span class="detail-label">
Updated At
</span>

<span class="detail-value">
<?php echo htmlspecialchars($case['updated_at']); ?>
</span>

</div>


</div>


</section>


<div class="form-actions">

<a
    href="index.php"
    class="btn-secondary"
>
Back to Cases
</a>


<a
    href="edit.php?id=<?php echo $case['id']; ?>"
    class="btn-primary"
>
Edit Case
</a>

</div>


</main>


<?php

include "../includes/footer.php";

?>
