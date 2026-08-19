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

$user_id = (int)($_GET['user_id'] ?? 0);
$module = trim($_GET['module'] ?? '');
$action = trim($_GET['action'] ?? '');
$date_from = trim($_GET['date_from'] ?? '');
$date_to = trim($_GET['date_to'] ?? '');

$users_query = "
    SELECT id, username
    FROM users
    ORDER BY username ASC
";

$users_result = mysqli_query(
    $conn,
    $users_query
);

$modules_query = "
    SELECT DISTINCT module
    FROM activity_logs
    WHERE module IS NOT NULL
    AND module != ''
    ORDER BY module ASC
";

$modules_result = mysqli_query(
    $conn,
    $modules_query
);

$query = "
    SELECT
        al.id,
        al.user_id,
        al.role_id,
        al.module,
        al.action,
        al.record_reference,
        al.previous_value,
        al.new_value,
        al.ip_address,
        al.description,
        al.created_at,
        u.username,
        r.role_name
    FROM activity_logs al
    LEFT JOIN users u
        ON al.user_id = u.id
    LEFT JOIN roles r
        ON al.role_id = r.id
    WHERE 1=1
";

if ($user_id > 0) {
    $query .= " AND al.user_id = " . $user_id;
}

if ($module != '') {
    $safe_module = mysqli_real_escape_string(
        $conn,
        $module
    );

    $query .= " AND al.module = '$safe_module'";
}

if ($action != '') {
    $safe_action = mysqli_real_escape_string(
        $conn,
        $action
    );

    $query .= " AND al.action LIKE '%$safe_action%'";
}

if ($date_from != '') {
    $safe_date_from = mysqli_real_escape_string(
        $conn,
        $date_from
    );

    $query .= " AND DATE(al.created_at) >= '$safe_date_from'";
}

if ($date_to != '') {
    $safe_date_to = mysqli_real_escape_string(
        $conn,
        $date_to
    );

    $query .= " AND DATE(al.created_at) <= '$safe_date_to'";
}

$query .= "
    ORDER BY al.id DESC
";

$result = mysqli_query(
    $conn,
    $query
);

if (!$result) {
    die(
        "Database Error: "
        . mysqli_error($conn)
    );
}

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<main>

<h1>
Activity Logs
</h1>

<p>
View system activity and audit history.
</p>

<section>

<h2>
Filter Activity Logs
</h2>

<form
    method="GET"
    style="
        display:flex;
        gap:15px;
        flex-wrap:wrap;
        align-items:flex-end;
    "
>

<div class="form-group">

<label>
User
</label>

<select name="user_id">

<option value="">
All Users
</option>

<?php
if ($users_result) {
    while (
        $user = mysqli_fetch_assoc(
            $users_result
        )
    ) {
?>

<option
    value="<?php echo (int)$user['id']; ?>"
    <?php
    echo (
        $user_id == (int)$user['id']
    )
        ? 'selected'
        : '';
    ?>
>

<?php
echo htmlspecialchars(
    $user['username']
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

<label>
Module
</label>

<select name="module">

<option value="">
All Modules
</option>

<?php
if ($modules_result) {
    while (
        $module_row = mysqli_fetch_assoc(
            $modules_result
        )
    ) {

        $module_name = $module_row['module'];
?>

<option
    value="<?php
        echo htmlspecialchars(
            $module_name
        );
    ?>"
    <?php
    echo (
        $module == $module_name
    )
        ? 'selected'
        : '';
    ?>
>

<?php
echo htmlspecialchars(
    $module_name
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

<label>
Action
</label>

<input
    type="text"
    name="action"
    value="<?php
        echo htmlspecialchars(
            $action
        );
    ?>"
    placeholder="CREATE / UPDATE / OCR"
>

</div>

<div class="form-group">

<label>
Date From
</label>

<input
    type="date"
    name="date_from"
    value="<?php
        echo htmlspecialchars(
            $date_from
        );
    ?>"
>

</div>

<div class="form-group">

<label>
Date To
</label>

<input
    type="date"
    name="date_to"
    value="<?php
        echo htmlspecialchars(
            $date_to
        );
    ?>"
>

</div>

<button
    type="submit"
    class="btn-primary"
>
Apply Filters
</button>

<a
    href="index.php"
    class="btn-secondary"
>
Reset
</a>

</form>

</section>

<section>

<h2>
System Activity
</h2>

<?php
if (
    mysqli_num_rows($result) == 0
) {
?>

<p>
No activity logs found for the selected filters.
</p>

<?php
} else {
?>

<div
    style="
        overflow-x:auto;
    "
>

<table>

<thead>

<tr>

<th>
ID
</th>

<th>
User
</th>

<th>
Role
</th>

<th>
Module
</th>

<th>
Action
</th>

<th>
Record
</th>

<th>
Description
</th>

<th>
IP Address
</th>

<th>
Date
</th>

</tr>

</thead>

<tbody>

<?php
while (
    $row = mysqli_fetch_assoc(
        $result
    )
) {
?>

<tr>

<td>

<?php
echo (int)$row['id'];
?>

</td>

<td>

<?php
echo htmlspecialchars(
    $row['username']
    ?? 'System'
);
?>

</td>

<td>

<?php
echo htmlspecialchars(
    $row['role_name']
    ?? 'N/A'
);
?>

</td>

<td>

<?php
echo htmlspecialchars(
    $row['module']
    ?? ''
);
?>

</td>

<td>

<strong>

<?php
echo htmlspecialchars(
    $row['action']
    ?? ''
);
?>

</strong>

</td>

<td>

<?php
echo htmlspecialchars(
    $row['record_reference']
    ?? 'N/A'
);
?>

</td>

<td>

<?php
echo nl2br(
    htmlspecialchars(
        $row['description']
        ?? ''
    )
);
?>

</td>

<td>

<?php
echo htmlspecialchars(
    $row['ip_address']
    ?? 'N/A'
);
?>

</td>

<td>

<?php
echo htmlspecialchars(
    $row['created_at']
    ?? ''
);
?>

</td>

</tr>

<?php
}
?>

</tbody>

</table>

</div>

<?php
}
?>

</section>

</main>

<?php
include "../includes/footer.php";
?>