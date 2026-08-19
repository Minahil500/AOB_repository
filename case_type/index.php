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
        id,
        type_name,
        description,
        created_at
    FROM case_types
    ORDER BY id DESC
";

$result = mysqli_query($conn, $query);

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

<h1>Case Types</h1>

<p>Manage available case types.</p>

<div style="margin-bottom:20px;">

<a
    href="create.php"
    class="btn-primary"
>
+ Add Case Type
</a>

</div>

<section>

<?php if (mysqli_num_rows($result) == 0) { ?>

<p>
No case types found.
</p>

<?php } else { ?>

<div style="overflow-x:auto;">

<table>

<thead>

<tr>

<th>ID</th>
<th>Type Name</th>
<th>Description</th>
<th>Created At</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>

<tr>

<td>
<?php echo (int)$row['id']; ?>
</td>

<td>
<?php
echo htmlspecialchars(
    $row['type_name']
);
?>
</td>

<td>
<?php
echo htmlspecialchars(
    $row['description'] ?? ''
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
    onclick="return confirm('Are you sure you want to delete this case type?');"
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