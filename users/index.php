<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "../config/db.php";

if (!isset($_SESSION['user_id'])) {

    header("Location: ../login.php");
    exit();

}

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
    ORDER BY id DESC
";

$result = mysqli_query(
    $conn,
    $query
);

if (!$result) {

    die(
        "Users Database Error: "
        . mysqli_error($conn)
    );

}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>
Users
</h1>

<p>
Manage administrative accounts and user access.
</p>

<div style="
    margin-bottom:20px;
">

<a
    href="create.php"
    class="btn-primary"
>
+ Add User
</a>

</div>

<section>

<h2>
User Accounts
</h2>

<div style="
    overflow-x:auto;
">

<table>

<thead>

<tr>

<th>
ID
</th>

<th>
Username
</th>

<th>
Email
</th>

<th>
Role
</th>

<th>
Status
</th>

<th>
Last Login
</th>

<th>
Created At
</th>

<th>
Actions
</th>

</tr>

</thead>

<tbody>

<?php

if (
    mysqli_num_rows($result) == 0
) {

?>

<tr>

<td
    colspan="8"
>

No users found.

</td>

</tr>

<?php

} else {

    while (
        $user =
            mysqli_fetch_assoc($result)
    ) {

?>

<tr>

<td>

<?php

echo (int)
    $user['id'];

?>

</td>

<td>

<strong>

<?php

echo htmlspecialchars(
    $user['username']
);

?>

</strong>

</td>

<td>

<?php

echo htmlspecialchars(
    $user['official_email']
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    ucfirst(
        $user['role_id']
    )
);

?>

</td>

<td>

<?php

$status =
    $user['status'];

if ($status === 'active') {

?>

<span style="
    background:#dcfce7;
    color:#166534;
    padding:5px 10px;
    border-radius:15px;
    font-size:13px;
">

Active

</span>

<?php

} elseif (
    $status === 'inactive'
) {

?>

<span style="
    background:#f3f4f6;
    color:#374151;
    padding:5px 10px;
    border-radius:15px;
    font-size:13px;
">

Inactive

</span>

<?php

} elseif (
    $status === 'locked'
) {

?>

<span style="
    background:#fee2e2;
    color:#991b1b;
    padding:5px 10px;
    border-radius:15px;
    font-size:13px;
">

Locked

</span>

<?php

} else {

echo htmlspecialchars(
    $status
);

}

?>

</td>

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

echo "Never";

}

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $user['created_at']
);

?>

</td>

<td>

<a
    href="edit.php?id=<?php
        echo (int)$user['id'];
    ?>"
    class="btn-secondary"
>
Edit
</a>

<a
    href="delete.php?id=<?php
        echo (int)$user['id'];
    ?>"
    class="btn-danger"
    onclick="
        return confirm(
            'Are you sure you want to delete this user?'
        );
    "
>
Delete
</a>

</td>

</tr>

<?php

    }

}

?>

</tbody>

</table>

</div>

</section>

</main>

<?php

include "../includes/footer.php";

?>