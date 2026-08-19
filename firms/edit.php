<?php

session_start();

include "../config/db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    die("Invalid firm ID.");

}

$firm_id = (int) $_GET['id'];

$query = "SELECT * FROM firms WHERE id = $firm_id LIMIT 1";

$result = mysqli_query($conn, $query);

if (!$result) {

    die("Database error: " . mysqli_error($conn));

}

$firm = mysqli_fetch_assoc($result);

if (!$firm) {

    die("Firm not found.");

}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firm_code = trim($_POST['firm_code'] ?? '');
    $ntn_number = trim($_POST['ntn_number'] ?? '');
    $firm_name = trim($_POST['firm_name'] ?? '');
    $official_email = trim($_POST['official_email'] ?? '');
    $landline = trim($_POST['landline'] ?? '');
    $principal_contact_person = trim($_POST['principal_contact_person'] ?? '');
    $aob_representative = trim($_POST['aob_representative'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if (
        $firm_code == "" ||
        $firm_name == "" ||
        $official_email == "" ||
        $principal_contact_person == "" ||
        $aob_representative == "" ||
        $city == "" ||
        $province == ""
    ) {

        $error = "Please fill all required fields.";

    } elseif (!filter_var($official_email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        $update_query = "
            UPDATE firms
            SET
                firm_code = ?,
                ntn_number = ?,
                firm_name = ?,
                official_email = ?,
                landline = ?,
                principal_contact_person = ?,
                aob_representative = ?,
                city = ?,
                province = ?,
                status = ?
            WHERE id = ?
        ";

        $stmt = mysqli_prepare(
            $conn,
            $update_query
        );

        if (!$stmt) {

            die(
                "Prepare failed: " .
                mysqli_error($conn)
            );

        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssssi",
            $firm_code,
            $ntn_number,
            $firm_name,
            $official_email,
            $landline,
            $principal_contact_person,
            $aob_representative,
            $city,
            $province,
            $status,
            $firm_id
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: index.php");
            exit();

        } else {

            $error = "Update failed: " . mysqli_error($conn);

        }

    }

}

include "../includes/header.php";

include "../includes/sidebar.php";

?>

<main>

<h1>
Edit Firm
</h1>

<p>
Update the registered firm's information.
</p>

<?php if ($error != "") { ?>

    <div class="form-error">

        <?php
        echo htmlspecialchars($error);
        ?>

    </div>

<?php } ?>

<section>

<form method="POST">

<div class="form-group">

<label>
Firm Code *
</label>

<input
    type="text"
    name="firm_code"
    value="<?php
        echo htmlspecialchars(
            $_POST['firm_code'] ?? $firm['firm_code']
        );
    ?>"
    required
>

</div>

<div class="form-group">

<label>
NTN Number
</label>

<input
    type="text"
    name="ntn_number"
    value="<?php
        echo htmlspecialchars(
            $_POST['ntn_number'] ?? $firm['ntn_number']
        );
    ?>"
>

</div>

<div class="form-group">

<label>
Firm Name *
</label>

<input
    type="text"
    name="firm_name"
    value="<?php
        echo htmlspecialchars(
            $_POST['firm_name'] ?? $firm['firm_name']
        );
    ?>"
    required
>

</div>

<div class="form-group">

<label>
Official Email *
</label>

<input
    type="email"
    name="official_email"
    value="<?php
        echo htmlspecialchars(
            $_POST['official_email'] ?? $firm['official_email']
        );
    ?>"
    required
>

</div>

<div class="form-group">

<label>
Landline
</label>

<input
    type="text"
    name="landline"
    value="<?php
        echo htmlspecialchars(
            $_POST['landline'] ?? $firm['landline']
        );
    ?>"
>

</div>

<div class="form-group">

<label>
Principal Contact Person *
</label>

<input
    type="text"
    name="principal_contact_person"
    value="<?php
        echo htmlspecialchars(
            $_POST['principal_contact_person']
            ?? $firm['principal_contact_person']
        );
    ?>"
    required
>

</div>

<div class="form-group">

<label>
AOB Representative *
</label>

<input
    type="text"
    name="aob_representative"
    value="<?php
        echo htmlspecialchars(
            $_POST['aob_representative']
            ?? $firm['aob_representative']
        );
    ?>"
    required
>

</div>

<div class="form-group">

<label>
City *
</label>

<input
    type="text"
    name="city"
    value="<?php
        echo htmlspecialchars(
            $_POST['city'] ?? $firm['city']
        );
    ?>"
    required
>

</div>

<div class="form-group">

<label>
Province *
</label>

<select
    name="province"
    required
>

<option value="">
Select Province
</option>

<option value="Punjab"
<?php
if (
    ($_POST['province'] ?? $firm['province'])
    == "Punjab"
) {
    echo "selected";
}
?>
>
Punjab
</option>

<option value="Sindh"
<?php
if (
    ($_POST['province'] ?? $firm['province'])
    == "Sindh"
) {
    echo "selected";
}
?>
>
Sindh
</option>

<option value="Khyber Pakhtunkhwa"
<?php
if (
    ($_POST['province'] ?? $firm['province'])
    == "Khyber Pakhtunkhwa"
) {
    echo "selected";
}
?>
>
Khyber Pakhtunkhwa
</option>

<option value="Balochistan"
<?php
if (
    ($_POST['province'] ?? $firm['province'])
    == "Balochistan"
) {
    echo "selected";
}
?>
>
Balochistan
</option>

<option value="Islamabad Capital Territory"
<?php
if (
    ($_POST['province'] ?? $firm['province'])
    == "Islamabad Capital Territory"
) {
    echo "selected";
}
?>
>
Islamabad Capital Territory
</option>

<option value="Gilgit-Baltistan"
<?php
if (
    ($_POST['province'] ?? $firm['province'])
    == "Gilgit-Baltistan"
) {
    echo "selected";
}
?>
>
Gilgit-Baltistan
</option>

<option value="Azad Jammu and Kashmir"
<?php
if (
    ($_POST['province'] ?? $firm['province'])
    == "Azad Jammu and Kashmir"
) {
    echo "selected";
}
?>
>
Azad Jammu and Kashmir
</option>

</select>

</div>

<div class="form-group">

<label>
Status *
</label>

<select
    name="status"
    required
>

<option value="active"
<?php
if (
    ($_POST['status'] ?? $firm['status'])
    == "active"
) {
    echo "selected";
}
?>
>
Active
</option>

<option value="inactive"
<?php
if (
    ($_POST['status'] ?? $firm['status'])
    == "inactive"
) {
    echo "selected";
}
?>
>
Inactive
</option>

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
Save Changes
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>