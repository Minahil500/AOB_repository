<?php
session_start();
include "../config/db.php";

// GET FOLLOW-UPS
$query = "
    SELECT
        cf.*,
        c.case_number,
        c.case_title
    FROM case_followups cf
    LEFT JOIN cases c
        ON cf.case_id = c.id
    ORDER BY cf.followup_date ASC, cf.id DESC
";
$result = mysqli_query($conn, $query);
if (!$result) {
    die(
        "Database Error: " .
        mysqli_error($conn)
    );
}

// LAYOUT
include "../includes/header.php";
include "../includes/sidebar.php";
?>
<main>
<h1>
Case Follow-ups
</h1>
<p>
Manage upcoming, pending and completed case follow-up obligations.
</p>
<section>
<div class="page-header">
    <div>
        <h2>
        Follow-up Records
        </h2>
    </div>
    <div>
        <a
            href="create.php"
            class="btn-primary"
        >
            + Add Follow-up
        </a>
    </div>
</div>
<table
    border="1"
    width="100%"
>
<tr>
    <th>Case</th>
    <th>Follow-up Date</th>
    <th>Title</th>
    <th>Assigned Officer</th>
    <th>Priority</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
<?php
if (mysqli_num_rows($result) > 0) {
    while ($followup = mysqli_fetch_assoc($result)) {
?>
<tr>
<td>
<?php
echo htmlspecialchars(
    $followup['case_number'] ?? '-'
);
?>
<br>
<small>
<?php
echo htmlspecialchars(
    $followup['case_title'] ?? ''
);
?>
</small>
</td>
<td>
<?php
echo htmlspecialchars(
    $followup['followup_date']
);
?>
</td>
<td>
<?php
echo htmlspecialchars(
    $followup['title']
);
?>
</td>
<td>
<?php
echo htmlspecialchars(
    $followup['assigned_officer']
    ?? '-'
);
?>
</td>
<td>
<?php
echo htmlspecialchars(
    $followup['priority']
);
?>
</td>
<td>
<?php
echo htmlspecialchars(
    $followup['status']
);
?>
</td>
<td>
<a
    href="edit.php?id=<?php echo $followup['id']; ?>"
>
    Edit
</a>
&nbsp; | &nbsp;
<a
    href="delete.php?id=<?php echo $followup['id']; ?>"
    onclick="return confirm('Are you sure you want to delete this follow-up?');"
>
    Delete
</a>
</td>
</tr>
<?php
    }
} else {
?>
<tr>
<td
    colspan="7"
>
No follow-up records found.
</td>
</tr>
<?php
}
?>
</table>
</section>
</main>
<?php
include "../includes/footer.php";
?>