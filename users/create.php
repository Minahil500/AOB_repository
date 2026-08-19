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

$roles_query = "
    SELECT id, role_name
    FROM roles
    ORDER BY role_name ASC
";

$roles_result = mysqli_query($conn, $roles_query);

if (!$roles_result) {
    die("Roles Error: " . mysqli_error($conn));
}

$departments_query = "
    SELECT id, department_name
    FROM departments
    ORDER BY department_name ASC
";

$departments_result = mysqli_query(
    $conn,
    $departments_query
);

$officers_query = "
    SELECT id, full_name, username
    FROM users
    WHERE status = 'active'
    ORDER BY full_name ASC
";

$officers_result = mysqli_query(
    $conn,
    $officers_query
);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_code =
        trim($_POST['user_code'] ?? '');

    $full_name =
        trim($_POST['full_name'] ?? '');

    $username =
        trim($_POST['username'] ?? '');

    $official_email =
        trim($_POST['official_email'] ?? '');

    $mobile_number =
        trim($_POST['mobile_number'] ?? '');

    $designation =
        trim($_POST['designation'] ?? '');

    $department_id =
        (int)($_POST['department_id'] ?? 0);

    $role_id =
        (int)($_POST['role_id'] ?? 0);

    $reporting_officer_id =
        (int)($_POST['reporting_officer_id'] ?? 0);

    $password =
        $_POST['password'] ?? '';

    $status =
        $_POST['status'] ?? 'pending';

    $account_expiry_date =
        !empty($_POST['account_expiry_date'])
        ? $_POST['account_expiry_date']
        : null;

    $must_change_password =
        isset($_POST['must_change_password'])
        ? 1
        : 0;

    $send_activation_email =
        isset($_POST['send_activation_email'])
        ? 1
        : 0;

    $two_factor_enabled =
        isset($_POST['two_factor_enabled'])
        ? 1
        : 0;

    $office_location =
        trim($_POST['office_location'] ?? '');

    $signature_block =
        trim($_POST['signature_block'] ?? '');

    if (
        $username === '' ||
        $official_email === '' ||
        $password === ''
    ) {

        $error =
            "Username, official email and password are required.";

    } elseif (
        !filter_var(
            $official_email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            "Please enter a valid official email.";

    } elseif (
        strlen($password) < 8
    ) {

        $error =
            "Password must contain at least 8 characters.";

    } else {

        $check_query = "
            SELECT id
            FROM users
            WHERE username = ?
               OR official_email = ?
            LIMIT 1
        ";

        $check_stmt =
            mysqli_prepare(
                $conn,
                $check_query
            );

        mysqli_stmt_bind_param(
            $check_stmt,
            "ss",
            $username,
            $official_email
        );

        mysqli_stmt_execute($check_stmt);

        $check_result =
            mysqli_stmt_get_result(
                $check_stmt
            );

        if (
            mysqli_num_rows(
                $check_result
            ) > 0
        ) {

            $error =
                "Username or official email already exists.";

        } else {

            $password_hash =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

            $department_value =
                $department_id > 0
                ? $department_id
                : null;

            $role_value =
                $role_id > 0
                ? $role_id
                : null;

            $reporting_value =
                $reporting_officer_id > 0
                ? $reporting_officer_id
                : null;

            $insert_query = "
                INSERT INTO users
                (
                    user_code,
                    full_name,
                    username,
                    official_email,
                    mobile_number,
                    designation,
                    department_id,
                    role_id,
                    reporting_officer_id,
                    password_hash,
                    status,
                    account_expiry_date,
                    must_change_password,
                    send_activation_email,
                    two_factor_enabled,
                    office_location,
                    signature_block
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )
            ";

            $stmt =
                mysqli_prepare(
                    $conn,
                    $insert_query
                );

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssiiisssiiiss",
                $user_code,
                $full_name,
                $username,
                $official_email,
                $mobile_number,
                $designation,
                $department_value,
                $role_value,
                $reporting_value,
                $password_hash,
                $status,
                $account_expiry_date,
                $must_change_password,
                $send_activation_email,
                $two_factor_enabled,
                $office_location,
                $signature_block
            );

            if (
                mysqli_stmt_execute($stmt)
            ) {

                $new_user_id =
                    mysqli_insert_id($conn);

                log_activity(
                    $conn,
                    "Users",
                    "CREATE",
                    "User #" . $new_user_id,
                    null,
                    json_encode([
                        "username" =>
                            $username,
                        "official_email" =>
                            $official_email,
                        "status" =>
                            $status
                    ]),
                    "New user account created."
                );

                header(
                    "Location: index.php"
                );

                exit();

            } else {

                $error =
                    "Unable to create user: "
                    . mysqli_stmt_error($stmt);
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

<h1>Create User</h1>

<p>
Create a new administrative or staff account.
</p>

<?php if ($error !== '') { ?>

<div style="
    background:#fef2f2;
    border:1px solid #fecaca;
    color:#991b1b;
    padding:15px;
    margin-bottom:20px;
    border-radius:8px;
">

<?php echo htmlspecialchars($error); ?>

</div>

<?php } ?>

<section>

<form method="POST">

<div class="form-group">

<label>User Code</label>

<input
    type="text"
    name="user_code"
    value="<?php echo htmlspecialchars($_POST['user_code'] ?? ''); ?>"
>

</div>

<div class="form-group">

<label>Full Name</label>

<input
    type="text"
    name="full_name"
    value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
>

</div>

<div class="form-group">

<label>Username *</label>

<input
    type="text"
    name="username"
    required
    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
>

</div>

<div class="form-group">

<label>Official Email *</label>

<input
    type="email"
    name="official_email"
    required
    value="<?php echo htmlspecialchars($_POST['official_email'] ?? ''); ?>"
>

</div>

<div class="form-group">

<label>Mobile Number</label>

<input
    type="text"
    name="mobile_number"
    value="<?php echo htmlspecialchars($_POST['mobile_number'] ?? ''); ?>"
>

</div>

<div class="form-group">

<label>Designation</label>

<input
    type="text"
    name="designation"
    value="<?php echo htmlspecialchars($_POST['designation'] ?? ''); ?>"
>

</div>

<div class="form-group">

<label>Role</label>

<select name="role_id">

<option value="">
Select Role
</option>

<?php while ($role = mysqli_fetch_assoc($roles_result)) { ?>

<option
    value="<?php echo (int)$role['id']; ?>"
>

<?php echo htmlspecialchars($role['role_name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="form-group">

<label>Department</label>

<select name="department_id">

<option value="">
Select Department
</option>

<?php

if ($departments_result) {

    while (
        $department =
            mysqli_fetch_assoc(
                $departments_result
            )
    ) {

?>

<option
    value="<?php echo (int)$department['id']; ?>"
>

<?php

echo htmlspecialchars(
    $department['department_name']
);

?>

</option>

<?php

    }

}

?>

</select>

</div>

<div class="form-group">

<label>Reporting Officer</label>

<select name="reporting_officer_id">

<option value="">
Select Reporting Officer
</option>

<?php while (
    $officer =
        mysqli_fetch_assoc(
            $officers_result
        )
) { ?>

<option
    value="<?php echo (int)$officer['id']; ?>"
>

<?php

echo htmlspecialchars(
    $officer['full_name']
    ?: $officer['username']
);

?>

</option>

<?php } ?>

</select>

</div>

<div class="form-group">

<label>Password *</label>

<input
    type="password"
    name="password"
    required
>

</div>

<div class="form-group">

<label>Status</label>

<select name="status">

<option value="active">Active</option>
<option value="inactive">Inactive</option>
<option value="locked">Locked</option>
<option value="pending" selected>Pending</option>

</select>

</div>

<div class="form-group">

<label>Account Expiry Date</label>

<input
    type="date"
    name="account_expiry_date"
>

</div>

<div class="form-group">

<label>

<input
    type="checkbox"
    name="must_change_password"
    checked
>

Must Change Password

</label>

</div>

<div class="form-group">

<label>

<input
    type="checkbox"
    name="send_activation_email"
>

Send Activation Email

</label>

</div>

<div class="form-group">

<label>

<input
    type="checkbox"
    name="two_factor_enabled"
>

Enable Two-Factor Authentication

</label>

</div>

<div class="form-group">

<label>Office Location</label>

<input
    type="text"
    name="office_location"
>

</div>

<div class="form-group">

<label>Signature Block</label>

<textarea
    name="signature_block"
    rows="5"
></textarea>

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
Create User
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>