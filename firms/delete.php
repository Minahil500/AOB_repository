
<?php

session_start();

include "../config/db.php";
// CHECK FIRM ID

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: index.php");
    exit();

}

$firm_id = (int) $_GET['id'];
// GET FIRM

$query = "
    SELECT id, firm_code, firm_name
    FROM firms
    WHERE id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $firm_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$firm = mysqli_fetch_assoc($result);


if (!$firm) {

    die("Firm not found.");

}
// DELETE FIRM

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $delete_query = "
        DELETE FROM firms
        WHERE id = ?
    ";

    $delete_stmt = mysqli_prepare(
        $conn,
        $delete_query
    );

    mysqli_stmt_bind_param(
        $delete_stmt,
        "i",
        $firm_id
    );


    if (mysqli_stmt_execute($delete_stmt)) {

        header("Location: index.php");
        exit();

    } else {

        $error = "Unable to delete this firm. It may have related cases or documents.";

    }

}
// LAYOUT

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<main>
<h1>
Delete Firm
</h1>


<p>
Please confirm that you want to delete this firm.
</p>
<section>
<?php if ($error != "") { ?>

    <div class="form-error">

        <?php
        echo htmlspecialchars($error);
        ?>

    </div>

<?php } ?>


<h2>
Are you sure?
</h2>


<p>
You are about to delete the following firm:
</p>


<div class="detail-item">

    <strong>
        Firm Code:
    </strong>

    <?php
    echo htmlspecialchars($firm['firm_code']);
    ?>

</div>


<div class="detail-item">

    <strong>
        Firm Name:
    </strong>

    <?php
    echo htmlspecialchars($firm['firm_name']);
    ?>

</div>


<p>
<strong>Warning:</strong>
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
    onclick="return confirm('Are you sure you want to delete this firm?');"
>
Delete Firm
</button>


</div>


</form>


</section>


</main>


<?php

include "../includes/footer.php";

?>
