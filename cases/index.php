<?php

session_start();

include "../config/db.php";

$query = "
    SELECT
        c.*,
        f.firm_name
    FROM cases c
    LEFT JOIN firms f
        ON c.firm_id = f.id
    ORDER BY c.id DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database Error: " . mysqli_error($conn));
}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>Cases</h1>

<p>
Manage all legal cases registered in the AOB Legal Repository.
</p>

<section>

<div class="page-header">

    <div>
        <h2>Registered Cases</h2>
    </div>

    <div>
        <a href="create.php" class="btn-primary">
            + Add Case
        </a>
    </div>

</div>

<table border="1" width="100%">

<tr>

    <th>Case Number</th>
    <th>Firm</th>
    <th>Case Title</th>
    <th>Case Type</th>
    <th>Priority</th>
    <th>Status</th>
    <th>Next Follow-up</th>
    <th>Actions</th>

</tr>

<?php

if (mysqli_num_rows($result) > 0) {

    while ($case = mysqli_fetch_assoc($result)) {

?>

<tr>

<td>
    <?php echo htmlspecialchars($case['case_number']); ?>
</td>

<td>
    <?php
    echo htmlspecialchars(
        $case['firm_name'] ?? '-'
    );
    ?>
</td>

<td>
    <?php echo htmlspecialchars($case['case_title']); ?>
</td>

<td>
    <?php echo htmlspecialchars($case['case_type']); ?>
</td>

<td>
    <?php echo htmlspecialchars($case['priority']); ?>
</td>

<td>
    <?php echo htmlspecialchars($case['status']); ?>
</td>

<td>
    <?php
    echo htmlspecialchars(
        $case['next_followup_date'] ?? '-'
    );
    ?>
</td>

<td>

    <a href="view.php?id=<?php echo $case['id']; ?>">
        View
    </a>

    |

    <a href="edit.php?id=<?php echo $case['id']; ?>">
        Edit
    </a>

    |

    <a
        href="delete.php?id=<?php echo $case['id']; ?>"
        onclick="return confirm('Are you sure you want to delete this case?');"
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

<td colspan="8">
    No cases found.
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