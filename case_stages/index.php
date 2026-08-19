
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
// GET CASE STAGES

$query = "
    SELECT
        id,
        stage_name,
        description,
        created_at
    FROM case_stages
    ORDER BY id DESC
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

// HEADER + SIDEBAR

include "../includes/header.php";
include "../includes/sidebar.php";

?>


<main>

<h1>
Case Stages
</h1>

<p>
Manage case stages used in the legal case workflow.
</p>


<section>


<div
    style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:20px;
        flex-wrap:wrap;
        gap:10px;
    "
>

<h2>
Stages
</h2>


<a
    href="create.php"
    class="btn-primary"
>
    Add Stage
</a>

</div>


<?php

if (mysqli_num_rows($result) == 0) {

?>

<p>
No case stages found.
</p>

<?php

} else {

?>


<div style="overflow-x:auto;">

<table>

<thead>

<tr>

<th>
ID
</th>

<th>
Stage Name
</th>

<th>
Description
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


<?php

while (
    $row = mysqli_fetch_assoc($result)
) {

?>


<tr>


<td>

<?php

echo (int) $row['id'];

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['stage_name']
);

?>

</td>


<td>

<?php

echo nl2br(
    htmlspecialchars(
        $row['description'] ?? ''
    )
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
    href="view.php?id=<?php echo (int) $row['id']; ?>"
    class="btn-primary"
>
    View
</a>


<a
    href="edit.php?id=<?php echo (int) $row['id']; ?>"
    class="btn-primary"
>
    Edit
</a>


<a
    href="delete.php?id=<?php echo (int) $row['id']; ?>"
    class="btn-secondary"
    onclick="return confirm('Are you sure you want to delete this case stage?');"
>
    Delete
</a>

</td>


</tr>


<?php

}

?>


</tbody>

</table>

</div>


<?php

}

?>


</section>


</main>


<?php

include "../includes/footer.php";

?>