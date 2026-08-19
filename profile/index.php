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

$query = "
    SELECT
        id,
        username,
        official_email,
        role_id,
        status,
        last_login_at,
        created_at,
        updated_at
    FROM users
    WHERE id = $user_id
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

    die("User profile not found.");

}

$user = mysqli_fetch_assoc($result);

$role_name = "N/A";

if (!empty($user['role_id'])) {

    $role_id = (int) $user['role_id'];

    $role_query = "
        SELECT role_name
        FROM roles
        WHERE id = $role_id
        LIMIT 1
    ";

    $role_result = mysqli_query(
        $conn,
        $role_query
    );

    if (
        $role_result &&
        mysqli_num_rows($role_result) > 0
    ) {

        $role = mysqli_fetch_assoc(
            $role_result
        );

        $role_name =
            $role['role_name'];

    }

}

include "../includes/header.php";

include "../includes/sidebar.php";

?>

<main>

<h1>
My Profile
</h1>

<p>
View your account information and profile details.
</p>

<section>

<h2>
Profile Information
</h2>

<table>

<tr>

<th>
Username
</th>

<td>

<?php

echo htmlspecialchars(
    $user['username'] ?? ''
);

?>

</td>

</tr>

<tr>

<th>
Official Email
</th>

<td>

<?php

echo htmlspecialchars(
    $user['official_email'] ?? 'N/A'
);

?>

</td>

</tr>

<tr>

<th>
Role
</th>

<td>

<?php

echo htmlspecialchars(
    $role_name
);

?>

</td>

</tr>

<tr>

<th>
Account Status
</th>

<td>

<?php

echo htmlspecialchars(
    $user['status'] ?? 'N/A'
);

?>

</td>

</tr>

<tr>

<th>
Last Login
</th>

<td>

<?php

if (
    !empty(
        $user['last_login_at']
    )
) {

    echo htmlspecialchars(
        $user['last_login_at']
    );

} else {

    echo "N/A";

}

?>

</td>

</tr>

<tr>

<th>
Account Created
</th>

<td>

<?php

echo htmlspecialchars(
    $user['created_at'] ?? 'N/A'
);

?>

</td>

</tr>

<tr>

<th>
Last Updated
</th>

<td>

<?php

echo htmlspecialchars(
    $user['updated_at'] ?? 'N/A'
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
    href="change_password.php"
    class="btn-primary"
>
    Change Password
</a>

<a
    href="../dashboard.php"
    class="btn-secondary"
>
    Back to Dashboard
</a>

</div>

</section>

</main>

<?php

include "../includes/footer.php";

?>