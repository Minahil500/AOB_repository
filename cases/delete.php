
<?php

session_start();

include "../config/db.php";


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: index.php");
    exit();

}

$case_id = (int) $_GET['id'];
// GET CASE

$query = "
    SELECT
        c.id,
        c.case_number,
        c.case_title,
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

// DELETE

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $delete_query = "
        DELETE FROM cases
        WHERE id = ?
    ";

    $delete_stmt = mysqli_prepare(
        $conn,
        $delete_query
    );

    mysqli_stmt_bind_param(
        $delete_stmt,
        "i",
        $case_id
    );


    if (mysqli_stmt_execute($delete_stmt)) {

        header("Location: index.php");
        exit();

    } else {

        $error = "Unable to delete this case. It may have related documents or follow-ups.";

    }

}
include "../includes/header.php";
include "../includes/sidebar.php";

?>


<main>

<h1>
Delete Case
</h1>

<p>
Please confirm that you want to delete this case.
</p>


<section>


<?php if ($error != "") { ?>

<div class="form-error">

<?php echo htmlspecialchars($error); ?>

</div>

<?php } ?>


<h2>
Are you sure?
</h2>


<p>
You are about to delete:
</p>


<div class="detail-item">

<strong>
Case Number:
</strong>

<?php
echo htmlspecialchars($case['case_number']);
?>

</div>


<div class="detail-item">

<strong>
Firm:
</strong>

<?php
echo htmlspecialchars(
    $case['firm_name'] ?? '-'
);
?>

</div>


<div class="detail-item">

<strong>
Case Title:
</strong>

<?php
echo htmlspecialchars($case['case_title']);
?>

</div>


<p>

<strong>
Warning:
</strong>

This action cannot be undone.

</p>


<form method="POST">


<div class="form-actions">


<a
    href="index.php"
    class="btn-secondary"
>
Cancel
</a>


<button
    type="submit"
    class="btn-danger"
    onclick="return confirm('Are you sure you want to delete this case?');"
>
Delete Case
</button>


</div>


</form>


</section>


</main>


<?php

include "../includes/footer.php";

?>
