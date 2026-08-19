<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../config/db.php";
include "../includes/activity_logger.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$error = "";

$cases_query = "
    SELECT id, case_number, case_title
    FROM cases
    ORDER BY id DESC
";

$cases_result = mysqli_query(
    $conn,
    $cases_query
);

if (!$cases_result) {
    die(
        "Cases Database Error: " .
        mysqli_error($conn)
    );
}

$regulations_query = "
    SELECT id, regulation_name
    FROM regulations
    ORDER BY regulation_name ASC
";

$regulations_result = mysqli_query(
    $conn,
    $regulations_query
);

if (!$regulations_result) {
    die(
        "Regulations Database Error: " .
        mysqli_error($conn)
    );
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $case_id = (int)($_POST['case_id'] ?? 0);
    $regulation_id = (int)($_POST['regulation_id'] ?? 0);

    if ($case_id <= 0) {
        $error = "Please select a case.";
    } elseif ($regulation_id <= 0) {
        $error = "Please select a regulation.";
    } else {
        $check_query = "
            SELECT id
            FROM case_regulations
            WHERE case_id = ?
            AND regulation_id = ?
            LIMIT 1
        ";

        $check_stmt = mysqli_prepare(
            $conn,
            $check_query
        );

        mysqli_stmt_bind_param(
            $check_stmt,
            "ii",
            $case_id,
            $regulation_id
        );

        mysqli_stmt_execute(
            $check_stmt
        );

        $check_result = mysqli_stmt_get_result(
            $check_stmt
        );

        if (mysqli_num_rows($check_result) > 0) {
            $error = "This regulation is already linked to this case.";
        } else {
            $insert_query = "
                INSERT INTO case_regulations
                (
                    case_id,
                    regulation_id
                )
                VALUES
                (
                    ?,
                    ?
                )
            ";

            $stmt = mysqli_prepare(
                $conn,
                $insert_query
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ii",
                $case_id,
                $regulation_id
            );

            if (mysqli_stmt_execute($stmt)) {
                $new_id = mysqli_insert_id($conn);

                log_activity(
                    $conn,
                    "Case Regulations",
                    "CREATE",
                    "Case Regulation #" . $new_id,
                    null,
                    json_encode([
                        "case_id" => $case_id,
                        "regulation_id" => $regulation_id
                    ]),
                    "Regulation linked to case."
                );

                header("Location: index.php");
                exit();
            } else {
                $error = "Unable to link regulation: " . mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check_stmt);
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<main>
<h1>
Link Regulation to Case
</h1>
<p>
Assign an existing regulation to a case.
</p>

<?php if ($error !== '') { ?>
<div style="
    background:#fef2f2;
    border:1px solid #fecaca;
    color:#991b1b;
    padding:15px;
    border-radius:8px;
    margin-bottom:20px;
">
<?php echo htmlspecialchars($error); ?>
</div>
<?php } ?>

<section>
<form method="POST">
<div class="form-group">
<label>
Case *
</label>
<select
    name="case_id"
    required
>
<option value="">
-- Select Case --
</option>
<?php while ($case = mysqli_fetch_assoc($cases_result)) { ?>
<option
    value="<?php echo (int)$case['id']; ?>"
>
<?php
echo htmlspecialchars(
    $case['case_number'] .
    " - " .
    $case['case_title']
);
?>
</option>
<?php } ?>
</select>
</div>

<div class="form-group">
<label>
Regulation *
</label>
<select
    name="regulation_id"
    required
>
<option value="">
-- Select Regulation --
</option>
<?php while ($regulation = mysqli_fetch_assoc($regulations_result)) { ?>
<option
    value="<?php echo (int)$regulation['id']; ?>"
>
<?php
echo htmlspecialchars(
    $regulation['regulation_name']
);
?>
</option>
<?php } ?>
</select>
</div>

<div class="form-actions">
<a
    href="index.php"
    class="btn-secondary"
>
Cancel
</a>
<button
    type="submit"
    class="btn-primary"
>
Link Regulation
</button>
</div>
</form>
</section>
</main>

<?php
include "../includes/footer.php";
?>