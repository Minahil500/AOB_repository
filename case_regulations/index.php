<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$query = "
    SELECT
        cr.id,
        cr.case_id,
        cr.regulation_id,
        cr.created_at,
        c.case_number,
        c.case_title,
        r.regulation_name
    FROM case_regulations cr
    LEFT JOIN cases c
        ON cr.case_id = c.id
    LEFT JOIN regulations r
        ON cr.regulation_id = r.id
    ORDER BY cr.id DESC
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

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<main>
<h1>
Case Regulations
</h1>
<p>
View and manage regulations linked to cases.
</p>
<div style="margin-bottom:20px;">
<a
    href="create.php"
    class="btn-primary"
>
+ Link Regulation
</a>
</div>
<section>
<?php if (mysqli_num_rows($result) == 0) { ?>
<p>
No case regulations found.
</p>
<?php } else { ?>
<div style="overflow-x:auto;">
<table>
<thead>
<tr>
<th>
ID
</th>
<th>
Case Number
</th>
<th>
Case Title
</th>
<th>
Regulation
</th>
<th>
Created At
</th>
<th>
Actions
</th>
</tr>
</thead>
<tbody>
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
<td>
<?php
echo (int)$row['id'];
?>
</td>
<td>
<?php
echo htmlspecialchars(
    $row['case_number']
    ?? 'N/A'
);
?>
</td>
<td>
<?php
echo htmlspecialchars(
    $row['case_title']
    ?? 'N/A'
);
?>
</td>
<td>
<?php
echo htmlspecialchars(
    $row['regulation_name']
    ?? 'N/A'
);
?>
</td>
<td>
<?php
echo htmlspecialchars(
    $row['created_at']
);
?>
</td>
<td>
<a
    href="edit.php?id=<?php echo (int)$row['id']; ?>"
    class="btn-secondary"
>
Edit
</a>
<a
    href="delete.php?id=<?php echo (int)$row['id']; ?>"
    class="btn-danger"
    onclick="return confirm('Are you sure you want to remove this regulation from the case?');"
>
Delete
</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
<?php } ?>
</section>
</main>
<?php
include "../includes/footer.php";
?>