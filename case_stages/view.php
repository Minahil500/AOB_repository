<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {
    die("Invalid stage ID.");
}

$stage_id = (int) $_GET['id'];

$query = "
    SELECT
        id,
        stage_name,
        description,
        created_at
    FROM case_stages
    WHERE id = $stage_id
    LIMIT 1
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

if (mysqli_num_rows($result) == 0) {
    die("Case stage not found.");
}

$stage = mysqli_fetch_assoc($result);

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<main>
<h1>
Case Stage Details
</h1>
<p>
View complete information about the selected case stage.
</p>
<section>
<h2>
<?php
echo htmlspecialchars(
    $stage['stage_name']
);
?>
</h2>
<table>
<tr>
<th>
ID
</th>
<td>
<?php
echo (int) $stage['id'];
?>
</td>
</tr>
<tr>
<th>
Stage Name
</th>
<td>
<?php
echo htmlspecialchars(
    $stage['stage_name']
);
?>
</td>
</tr>
<tr>
<th>
Description
</th>
<td>
<?php
if (!empty($stage['description'])) {
    echo nl2br(
        htmlspecialchars(
            $stage['description']
        )
    );
} else {
    echo "N/A";
}
?>
</td>
</tr>
<tr>
<th>
Created At
</th>
<td>
<?php
echo htmlspecialchars(
    $stage['created_at']
);
?>
</td>
</tr>
</table>
<div
    style="
        margin-top:25px;
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    "
>
<a
    href="edit.php?id=<?php echo $stage_id; ?>"
    class="btn-primary"
>
    Edit
</a>
<a
    href="delete.php?id=<?php echo $stage_id; ?>"
    class="btn-secondary"
    onclick="return confirm('Are you sure you want to delete this case stage?');"
>
    Delete
</a>
<a
    href="index.php"
    class="btn-secondary"
>
    Back to Case Stages
</a>
</div>
</section>
</main>
<?php
include "../includes/footer.php";
?>