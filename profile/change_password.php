<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "../config/db.php";

if (!isset($_SESSION['user_id'])) {

    header("Location: ../login.php");
    exit();

}

$user_id = (int) $_SESSION['user_id'];

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $current_password =
        $_POST['current_password'] ?? '';

    $new_password =
        $_POST['new_password'] ?? '';

    $confirm_password =
        $_POST['confirm_password'] ?? '';

    if (
        $current_password === '' ||
        $new_password === '' ||
        $confirm_password === ''
    ) {

        $error =
            "All password fields are required.";

    }

    elseif (
        strlen($new_password) < 12
    ) {

        $error =
            "New password must be at least 12 characters long.";

    }

    elseif (
        $new_password !== $confirm_password
    ) {

        $error =
            "New password and confirm password do not match.";

    }

    elseif (
        $current_password === $new_password
    ) {

        $error =
            "New password must be different from the current password.";

    }

    else {

        $query = "
            SELECT password_hash
            FROM users
            WHERE id = ?
            LIMIT 1
        ";

        $stmt =
            $conn->prepare($query);

        if (!$stmt) {

            $error =
                "Database error: "
                . $conn->error;

        }

        else {

            $stmt->bind_param(
                "i",
                $user_id
            );

            $stmt->execute();

            $result =
                $stmt->get_result();

            if (
                $result->num_rows !== 1
            ) {

                $error =
                    "User account not found.";

            }

            else {

                $user =
                    $result->fetch_assoc();

                if (
                    !password_verify(
                        $current_password,
                        $user['password_hash']
                    )
                ) {

                    $error =
                        "Current password is incorrect.";

                }

                else {

                    $new_password_hash =
                        password_hash(
                            $new_password,
                            PASSWORD_DEFAULT
                        );

                    $update_query = "
                        UPDATE users
                        SET
                            password_hash = ?,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE id = ?
                    ";

                    $update_stmt =
                        $conn->prepare(
                            $update_query
                        );

                    if (!$update_stmt) {

                        $error =
                            "Database error: "
                            . $conn->error;

                    }

                    else {

                        $update_stmt->bind_param(
                            "si",
                            $new_password_hash,
                            $user_id
                        );

                        if (
                            $update_stmt->execute()
                        ) {

                            $message =
                                "Password changed successfully.";

                        }

                        else {

                            $error =
                                "Failed to update password: "
                                . $update_stmt->error;

                        }

                        $update_stmt->close();

                    }

                }

            }

            $stmt->close();

        }

    }

}

include "../includes/header.php";

include "../includes/sidebar.php";

?>

<main>

<h1>
Change Password
</h1>

<p>
Update your account password securely.
</p>

<section>

<?php if ($message !== '') { ?>

<div
    style="
        background:#ecfdf5;
        border:1px solid #a7f3d0;
        color:#065f46;
        padding:15px;
        border-radius:8px;
        margin-bottom:20px;
    "
>

<?php

echo htmlspecialchars(
    $message
);

?>

</div>

<?php } ?>

<?php if ($error !== '') { ?>

<div
    style="
        background:#fef2f2;
        border:1px solid #fecaca;
        color:#991b1b;
        padding:15px;
        border-radius:8px;
        margin-bottom:20px;
    "
>

<?php

echo htmlspecialchars(
    $error
);

?>

</div>

<?php } ?>

<form
    method="POST"
>

<div class="form-group">

<label>
Current Password
</label>

<input
    type="password"
    name="current_password"
    required
>

</div>

<div class="form-group">

<label>
New Password
</label>

<input
    type="password"
    name="new_password"
    minlength="12"
    required
>

<p
    style="
        color:#6b7280;
        font-size:13px;
    "
>
Minimum 12 characters.
</p>

</div>

<div class="form-group">

<label>
Confirm New Password
</label>

<input
    type="password"
    name="confirm_password"
    minlength="12"
    required
>

</div>

<div
    style="
        margin-top:20px;
        display:flex;
        gap:10px;
    "
>

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
    Change Password
</button>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>