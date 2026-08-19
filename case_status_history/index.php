<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "../config/db.php";

// CHECK LOGIN

if (!isset($_SESSION['user_id'])) {

    header("Location: ../login.php");
    exit();

}

// GET STATUS HISTORY

$query = "
    SELECT
        csh.id,
        csh.case_id,
        csh.old_status_id,
        csh.new_status_id,
        csh.remarks,
        csh.changed_by,
        csh.changed_at,

        c.case_number,
        c.case_title,

        old_s.status_name AS old_status,
        new_s.status_name AS new_status,

        u.username AS changed_by_user

    FROM case_status_history csh

    LEFT JOIN cases c
        ON csh.case_id = c.id

    LEFT JOIN case_statuses old_s
        ON csh.old_status_id = old_s.id

    LEFT JOIN case_statuses new_s
        ON csh.new_status_id = new_s.id

    LEFT JOIN users u
        ON csh.changed_by = u.id

    ORDER BY csh.id DESC
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
Case Status History
</h1>


<p>
View the status change history of legal cases.
</p>


<section>


<?php if (mysqli_num_rows($result) == 0) { ?>

<p>
No status history found.
</p>

<?php } else { ?>


<div style="overflow-x:auto;">

<table>

<thead>

<tr>

<th>ID</th>

<th>Case</th>

<th>Old Status</th>

<th>New Status</th>

<th>Remarks</th>

<th>Changed By</th>

<th>Changed At</th>

</tr>

</thead>


<tbody>


<?php while (
    $row =
        mysqli_fetch_assoc(
            $result
        )
) { ?>


<tr>


<td>

<?php

echo (int)$row['id'];

?>

</td>


<td>

<strong>

<?php

echo htmlspecialchars(
    $row['case_number']
    ?? 'N/A'
);

?>

</strong>

<br>

<small>

<?php

echo htmlspecialchars(
    $row['case_title']
    ?? ''
);

?>

</small>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['old_status']
    ?? 'N/A'
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['new_status']
    ?? 'N/A'
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['remarks']
    ?? ''
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['changed_by_user']
    ?? 'System'
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['changed_at']
    ?? ''
);

?>

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