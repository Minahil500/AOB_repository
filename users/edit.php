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

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid user ID.");
}

$query = "
    SELECT *
    FROM users
    WHERE id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("User not found.");
}

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

$roles_result = mysqli_query(
    $conn,
    "
    SELECT id, role_name
    FROM roles
    ORDER BY role_name ASC
    "
);

if (!$roles_result) {
    die("Roles Error: " . mysqli_error($conn));
}

$departments_result = mysqli_query(
    $conn,
    "
    SELECT id, department_name
    FROM departments
    ORDER BY department_name ASC
    "
);

$officers_stmt = mysqli_prepare(
    $conn,
    "
    SELECT id, full_name, username
    FROM users
    WHERE status = 'active'
    AND id != ?
    ORDER BY full_name ASC
    "
);

mysqli_stmt_bind_param(
    $officers_stmt,
    "i",
    $id
);

mysqli_stmt_execute($officers_stmt);

$officers_result =
    mysqli_stmt_get_result(
        $officers_stmt
    );

$error = "";

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
        $_POST['status'] ?? 'active';

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
        $official_email === ''
    ) {

        $error =
            "Username and official email are required.";

    } elseif (
        !filter_var(
            $official_email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            "Please enter a valid official email.";

    } else {

        $check_stmt = mysqli_prepare(
            $conn,
            "
            SELECT id
            FROM users
            WHERE
                (username = ? OR official_email = ?)
                AND id != ?
            LIMIT 1
            "
        );

        mysqli_stmt_bind_param(
            $check_stmt,
            "ssi",
            $username,
            $official_email,
            $id
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

            if ($password !== '') {

                if (strlen($password) < 8) {

                    $error =
                        "Password must contain at least 8 characters.";

                } else {

                    $password_hash =
                        password_hash(
                            $password,
                            PASSWORD_DEFAULT
                        );

                    $update_query = "
                        UPDATE users
                        SET
                            user_code = ?,
                            full_name = ?,
                            username = ?,
                            official_email = ?,
                            mobile_number = ?,
                            designation = ?,
                            department_id = ?,
                            role_id = ?,
                            reporting_officer_id = ?,
                            password_hash = ?,
                            status = ?,
                            account_expiry_date = ?,
                            must_change_password = ?,
                            send_activation_email = ?,
                            two_factor_enabled = ?,
                            office_location = ?,
                            signature_block = ?,
                            last_password_changed_at = NOW()
                        WHERE id = ?
                    ";

                    $update_stmt =
                        mysqli_prepare(
                            $conn,
                            $update_query
                        );

                    mysqli_stmt_bind_param(
                        $update_stmt,
                        "ssssssiiisssiiissi",
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
                        $signature_block,
                        $id
                    );

                    if (
                        !mysqli_stmt_execute(
                            $update_stmt
                        )
                    ) {

                        $error =
                            "Unable to update user: "
                            . mysqli_stmt_error(
                                $update_stmt
                            );
                    }

                    mysqli_stmt_close(
                        $update_stmt
                    );
                }

            } else {

                $update_query = "
                    UPDATE users
                    SET
                        user_code = ?,
                        full_name = ?,
                        username = ?,
                        official_email = ?,
                        mobile_number = ?,
                        designation = ?,
                        department_id = ?,
                        role_id = ?,
                        reporting_officer_id = ?,
                        status = ?,
                        account_expiry_date = ?,
                        must_change_password = ?,
                        send_activation_email = ?,
                        two_factor_enabled = ?,
                        office_location = ?,
                        signature_block = ?
                    WHERE id = ?
                ";

                $update_stmt =
                    mysqli_prepare(
                        $conn,
                        $update_query
                    );

                mysqli_stmt_bind_param(
                    $update_stmt,
                    "ssssssiiissiiissi",
                    $user_code,
                    $full_name,
                    $username,
                    $official_email,
                    $mobile_number,
                    $designation,
                    $department_value,
                    $role_value,
                    $reporting_value,
                    $status,
                    $account_expiry_date,
                    $must_change_password,
                    $send_activation_email,
                    $two_factor_enabled,
                    $office_location,
                    $signature_block,
                    $id
                );

                if (
                    !mysqli_stmt_execute(
                        $update_stmt
                    )
                ) {

                    $error =
                        "Unable to update user: "
                        . mysqli_stmt_error(
                            $update_stmt
                        );
                }

                mysqli_stmt_close(
                    $update_stmt
                );
            }

            if ($error === '') {

                log_activity(
                    $conn,
                    "Users",
                    "UPDATE",
                    "User #" . $id,
                    json_encode($user),
                    json_encode([
                        "username" =>
                            $username,
                        "official_email" =>
                            $official_email,
                        "status" =>
                            $status,
                        "role_id" =>
                            $role_value
                    ]),
                    "User account updated."
                );

                header(
                    "Location: index.php"
                );

                exit();
            }
        }

        mysqli_stmt_close(
            $check_stmt
        );
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>Edit User</h1>

<p>
Update user account information and access settings.
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
    value="<?php echo htmlspecialchars($user['user_code'] ?? ''); ?>"
>
</div>

<div class="form-group">
<label>Full Name</label>

<input
    type="text"
    name="full_name"
    value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"
>
</div>

<div class="form-group">
<label>Username *</label>

<input
    type="text"
    name="username"
    required
    value="<?php echo htmlspecialchars($user['username']); ?>"
>
</div>

<div class="form-group">
<label>Official Email *</label>

<input
    type="email"
    name="official_email"
    required
    value="<?php echo htmlspecialchars($user['official_email']); ?>"
>
</div>

<div class="form-group">
<label>Mobile Number</label>

<input
    type="text"
    name="mobile_number"
    value="<?php echo htmlspecialchars($user['mobile_number'] ?? ''); ?>"
>
</div>

<div class="form-group">
<label>Designation</label>

<input
    type="text"
    name="designation"
    value="<?php echo htmlspecialchars($user['designation'] ?? ''); ?>"
>
</div>

<div class="form-group">

<label>Role</label>

<select name="role_id">

<option value="">
Select Role
</option>

<?php while (
    $role =
        mysqli_fetch_assoc(
            $roles_result
        )
) {

?>

<option
    value="<?php echo (int)$role['id']; ?>"
    <?php
        echo (
            (int)$user['role_id']
            === (int)$role['id']
        )
        ? 'selected'
        : '';
    ?>
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
    <?php
        echo (
            (int)$user['department_id']
            === (int)$department['id']
        )
        ? 'selected'
        : '';
    ?>
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
) {

?>

<option
    value="<?php echo (int)$officer['id']; ?>"
    <?php
        echo (
            (int)$user['reporting_officer_id']
            === (int)$officer['id']
        )
        ? 'selected'
        : '';
    ?>
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

<label>
New Password
</label>

<input
    type="password"
    name="password"
>

<small>
Leave blank to keep the current password.
</small>

</div>

<div class="form-group">

<label>Status</label>

<select name="status">

<?php

$statuses = [
    'active',
    'inactive',
    'locked',
    'pending'
];

foreach ($statuses as $status) {

?>

<option
    value="<?php echo $status; ?>"
    <?php
        echo (
            $user['status'] === $status
        )
        ? 'selected'
        : '';
    ?>
>

<?php echo ucfirst($status); ?>

</option>

<?php } ?>

</select>

</div>

<div class="form-group">

<label>Account Expiry Date</label>

<input
    type="date"
    name="account_expiry_date"
    value="<?php echo htmlspecialchars($user['account_expiry_date'] ?? ''); ?>"
>

</div>

<div class="form-group">

<label>

<input
    type="checkbox"
    name="must_change_password"
    <?php
        echo !empty(
            $user['must_change_password']
        )
        ? 'checked'
        : '';
    ?>
>

Must Change Password

</label>

</div>

<div class="form-group">

<label>

<input
    type="checkbox"
    name="send_activation_email"
    <?php
        echo !empty(
            $user['send_activation_email']
        )
        ? 'checked'
        : '';
    ?>
>

Send Activation Email

</label>

</div>

<div class="form-group">

<label>

<input
    type="checkbox"
    name="two_factor_enabled"
    <?php
        echo !empty(
            $user['two_factor_enabled']
        )
        ? 'checked'
        : '';
    ?>
>

Enable Two-Factor Authentication

</label>

</div>

<div class="form-group">

<label>Office Location</label>

<input
    type="text"
    name="office_location"
    value="<?php echo htmlspecialchars($user['office_location'] ?? ''); ?>"
>

</div>

<div class="form-group">

<label>Signature Block</label>

<textarea
    name="signature_block"
    rows="5"
><?php echo htmlspecialchars($user['signature_block'] ?? ''); ?></textarea>

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
Update User
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>