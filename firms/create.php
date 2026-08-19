<?php

session_start();

include "../config/db.php";

$error = "";
$success = "";

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

        $error = "Please enter a valid official email address.";

    } else {

        $check_query = "
            SELECT id
            FROM firms
            WHERE firm_code = ?
            LIMIT 1
        ";

        $check_stmt = mysqli_prepare($conn, $check_query);

        mysqli_stmt_bind_param(
            $check_stmt,
            "s",
            $firm_code
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {

            $error = "A firm with this Firm Code already exists.";

        } else {

            $created_by = $_SESSION['user_id'] ?? null;

            $insert_query = "
                INSERT INTO firms
                (
                    firm_code,
                    ntn_number,
                    firm_name,
                    official_email,
                    landline,
                    principal_contact_person,
                    aob_representative,
                    city,
                    province,
                    status,
                    created_by
                )
                VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt = mysqli_prepare(
                $conn,
                $insert_query
            );

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
                $created_by
            );

            if (mysqli_stmt_execute($stmt)) {

                header("Location: index.php");
                exit();

            } else {

                $error = "Unable to create firm. Please try again.";

            }

        }

    }

}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<div class="page-header">

    <div>

        <h1>
            Add Firm
        </h1>

        <p>
            Register a new audit firm in the AOB Legal Repository.
        </p>

    </div>

</div>

<?php if ($error != "") { ?>

    <div class="form-error">
        <?php echo htmlspecialchars($error); ?>
    </div>

<?php } ?>

<section>

<form method="POST" action="">

<div class="form-group">

    <label for="firm_code">
        Firm Code <span>*</span>
    </label>

    <input
        type="text"
        id="firm_code"
        name="firm_code"
        value="<?php echo htmlspecialchars($_POST['firm_code'] ?? ''); ?>"
        placeholder="Example: FRM-0004"
        required
    >

</div>

<div class="form-group">

    <label for="ntn_number">
        NTN Number
    </label>

    <input
        type="text"
        id="ntn_number"
        name="ntn_number"
        value="<?php echo htmlspecialchars($_POST['ntn_number'] ?? ''); ?>"
        placeholder="Enter NTN number"
    >

</div>

<div class="form-group">

    <label for="firm_name">
        Firm Name <span>*</span>
    </label>

    <input
        type="text"
        id="firm_name"
        name="firm_name"
        value="<?php echo htmlspecialchars($_POST['firm_name'] ?? ''); ?>"
        placeholder="Enter registered firm name"
        required
    >

</div>

<div class="form-group">

    <label for="official_email">
        Official Email <span>*</span>
    </label>

    <input
        type="email"
        id="official_email"
        name="official_email"
        value="<?php echo htmlspecialchars($_POST['official_email'] ?? ''); ?>"
        placeholder="firm@example.com"
        required
    >

</div>

<div class="form-group">

    <label for="landline">
        Landline
    </label>

    <input
        type="text"
        id="landline"
        name="landline"
        value="<?php echo htmlspecialchars($_POST['landline'] ?? ''); ?>"
        placeholder="Enter landline number"
    >

</div>

<div class="form-group">

    <label for="principal_contact_person">
        Principal Contact Person <span>*</span>
    </label>

    <input
        type="text"
        id="principal_contact_person"
        name="principal_contact_person"
        value="<?php echo htmlspecialchars($_POST['principal_contact_person'] ?? ''); ?>"
        placeholder="Enter principal contact person"
        required
    >

</div>

<div class="form-group">

    <label for="aob_representative">
        AOB Representative <span>*</span>
    </label>

    <input
        type="text"
        id="aob_representative"
        name="aob_representative"
        value="<?php echo htmlspecialchars($_POST['aob_representative'] ?? ''); ?>"
        placeholder="Enter AOB representative"
        required
    >

</div>

<div class="form-group">

    <label for="city">
        City <span>*</span>
    </label>

    <input
        type="text"
        id="city"
        name="city"
        value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
        placeholder="Enter city"
        required
    >

</div>

<div class="form-group">

    <label for="province">
        Province <span>*</span>
    </label>

    <select
        id="province"
        name="province"
        required
    >

        <option value="">
            Select Province
        </option>

        <option value="Punjab"
            <?php echo (($_POST['province'] ?? '') == 'Punjab') ? 'selected' : ''; ?>>
            Punjab
        </option>

        <option value="Sindh"
            <?php echo (($_POST['province'] ?? '') == 'Sindh') ? 'selected' : ''; ?>>
            Sindh
        </option>

        <option value="Khyber Pakhtunkhwa"
            <?php echo (($_POST['province'] ?? '') == 'Khyber Pakhtunkhwa') ? 'selected' : ''; ?>>
            Khyber Pakhtunkhwa
        </option>

        <option value="Balochistan"
            <?php echo (($_POST['province'] ?? '') == 'Balochistan') ? 'selected' : ''; ?>>
            Balochistan
        </option>

        <option value="Islamabad Capital Territory"
            <?php echo (($_POST['province'] ?? '') == 'Islamabad Capital Territory') ? 'selected' : ''; ?>>
            Islamabad Capital Territory
        </option>

        <option value="Gilgit-Baltistan"
            <?php echo (($_POST['province'] ?? '') == 'Gilgit-Baltistan') ? 'selected' : ''; ?>>
            Gilgit-Baltistan
        </option>

        <option value="Azad Jammu and Kashmir"
            <?php echo (($_POST['province'] ?? '') == 'Azad Jammu and Kashmir') ? 'selected' : ''; ?>>
            Azad Jammu and Kashmir
        </option>

    </select>

</div>

<div class="form-group">

    <label for="status">
        Status <span>*</span>
    </label>

    <select
        id="status"
        name="status"
        required
    >

        <option value="active"
            <?php echo (($_POST['status'] ?? 'active') == 'active') ? 'selected' : ''; ?>>
            Active
        </option>

        <option value="inactive"
            <?php echo (($_POST['status'] ?? '') == 'inactive') ? 'selected' : ''; ?>>
            Inactive
        </option>

    </select>

</div>

<div class="form-actions">

    <a href="index.php" class="btn-secondary">
        Cancel
    </a>

    <button
        type="submit"
        class="btn-primary"
    >
        Save Firm
    </button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>