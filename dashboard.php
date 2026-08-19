<?php

session_start();

include "config/db.php";

$query = "SELECT COUNT(*) AS total FROM firms";
$result = mysqli_query($conn, $query);
$total_firms = mysqli_fetch_assoc($result)['total'];

$query = "SELECT COUNT(*) AS total
          FROM cases
          WHERE status = 'Open'";
$result = mysqli_query($conn, $query);
$open_cases = mysqli_fetch_assoc($result)['total'];

$query = "SELECT COUNT(*) AS total
          FROM cases
          WHERE status = 'Under Review'";
$result = mysqli_query($conn, $query);
$under_review_cases = mysqli_fetch_assoc($result)['total'];

$query = "SELECT COUNT(*) AS total FROM cases";
$result = mysqli_query($conn, $query);
$total_cases = mysqli_fetch_assoc($result)['total'];

$query = "SELECT COUNT(*) AS total FROM users";
$result = mysqli_query($conn, $query);
$total_users = mysqli_fetch_assoc($result)['total'];

$query = "SELECT COUNT(*) AS total
          FROM users
          WHERE status = 'active'";
$result = mysqli_query($conn, $query);
$active_users = mysqli_fetch_assoc($result)['total'];

$query = "SELECT COUNT(*) AS total
          FROM legal_documents";
$result = mysqli_query($conn, $query);
$total_documents = mysqli_fetch_assoc($result)['total'];

$query = "
    SELECT COUNT(*) AS total
    FROM legal_documents ld
    INNER JOIN document_types dt
        ON ld.document_type_id = dt.id
    WHERE dt.type_name = 'Court Order'
";

$result = mysqli_query($conn, $query);
$court_orders = mysqli_fetch_assoc($result)['total'];

$activity_query = "
    SELECT
        al.created_at,
        al.user_id,
        al.module,
        al.action,
        al.record_reference,
        al.description,
        u.username
    FROM activity_logs al
    LEFT JOIN users u
        ON al.user_id = u.id
    ORDER BY al.created_at DESC
    LIMIT 6
";

$activity_result = mysqli_query($conn, $activity_query);

$cases_query = "
    SELECT
        id,
        case_number,
        firm_id,
        case_title,
        case_type,
        status,
        assigned_officer,
        updated_at
    FROM cases
    ORDER BY updated_at DESC
    LIMIT 5
";

$cases_result = mysqli_query($conn, $cases_query);

$followups_query = "
    SELECT
        cf.id,
        cf.case_id,
        cf.followup_date,
        cf.title,
        cf.assigned_officer,
        cf.priority,
        cf.status,
        c.case_number
    FROM case_followups cf
    LEFT JOIN cases c
        ON cf.case_id = c.id
    WHERE cf.status NOT IN ('Completed', 'Cancelled')
    ORDER BY cf.followup_date ASC
    LIMIT 5
";

$followups_result = mysqli_query($conn, $followups_query);

$status_query = "
    SELECT status, COUNT(*) AS total
    FROM cases
    GROUP BY status
";

$status_result = mysqli_query($conn, $status_query);

$case_status_counts = [];

if ($status_result) {

    while ($row = mysqli_fetch_assoc($status_result)) {

        $case_status_counts[$row['status']] = $row['total'];

    }

}

$query = "
    SELECT COUNT(*) AS total
    FROM case_followups
    WHERE status NOT IN ('Completed', 'Cancelled')
";

$result = mysqli_query($conn, $query);
$pending_followups = mysqli_fetch_assoc($result)['total'];

include "includes/header.php";
include "includes/sidebar.php";

?>

<main>

<h1>
Administrative Dashboard
</h1>

<p>
Consolidated view of firms, legal cases, orders and follow-up obligations across the Audit Oversight Board.
</p>

<div class="cards">

<div class="card">

<h3>
Total Firms
</h3>

<h2>
<?php echo $total_firms; ?>
</h2>

<p>
Registered firms
</p>

<a href="firms/index.php">
View details
</a>

</div>

<div class="card">

<h3>
Total Cases
</h3>

<h2>
<?php echo $total_cases; ?>
</h2>

<p>
Registered legal cases
</p>

<a href="cases/index.php">
View details
</a>

</div>

<div class="card">

<h3>
Open Cases
</h3>

<h2>
<?php echo $open_cases; ?>
</h2>

<p>
Currently open
</p>

<a href="cases/index.php">
View details
</a>

</div>

<div class="card">

<h3>
Cases Under Review
</h3>

<h2>
<?php echo $under_review_cases; ?>
</h2>

<p>
Currently under review
</p>

<a href="cases/index.php">
View details
</a>

</div>

<div class="card">

<h3>
Court Orders
</h3>

<h2>
<?php echo $court_orders; ?>
</h2>

<p>
Recorded court orders
</p>

<a href="documents/index.php">
View details
</a>

</div>

<div class="card">

<h3>
Pending Follow-ups
</h3>

<h2>
<?php echo $pending_followups; ?>
</h2>

<p>
Pending obligations
</p>

<a href="cases/index.php">
View details
</a>

</div>

<div class="card">

<h3>
Documents Uploaded
</h3>

<h2>
<?php echo $total_documents; ?>
</h2>

<p>
Uploaded documents
</p>

<a href="documents/index.php">
View details
</a>

</div>

<div class="card">

<h3>
Active Users
</h3>

<h2>
<?php echo $active_users; ?>
</h2>

<p>
Active administrative accounts
</p>

<a href="users/index.php">
View details
</a>

</div>

</div>

<section>

<h2>
Case Status Overview
</h2>

<p>
Distribution of all registered cases by current status
</p>

<ul>

<li>
Draft :
<?php
echo $case_status_counts['Draft'] ?? 0;
?>
</li>

<li>
Open :
<?php
echo $case_status_counts['Open'] ?? 0;
?>
</li>

<li>
Under Review :
<?php
echo $case_status_counts['Under Review'] ?? 0;
?>
</li>

<li>
Referred to Court :
<?php
echo $case_status_counts['Referred to Court'] ?? 0;
?>
</li>

<li>
Closed :
<?php
echo $case_status_counts['Closed'] ?? 0;
?>
</li>

<li>
Archived :
<?php
echo $case_status_counts['Archived'] ?? 0;
?>
</li>

</ul>

</section>

<section>

<h2>
Recent Activity
</h2>

<p>
Latest recorded system events
</p>

<table border="1" width="100%">

<tr>

<th>
Timestamp
</th>

<th>
User
</th>

<th>
Action
</th>

<th>
Module
</th>

<th>
Record
</th>

<th>
Description
</th>

</tr>

<?php

if (
    $activity_result &&
    mysqli_num_rows($activity_result) > 0
) {

    while (
        $activity = mysqli_fetch_assoc($activity_result)
    ) {

?>

<tr>

<td>
<?php
echo htmlspecialchars($activity['created_at']);
?>
</td>

<td>

<?php

if (!empty($activity['username'])) {

    echo htmlspecialchars(
        $activity['username']
    );

} else {

    echo "Unknown User";

}

?>

</td>

<td>
<?php
echo htmlspecialchars($activity['action']);
?>
</td>

<td>
<?php
echo htmlspecialchars($activity['module']);
?>
</td>

<td>

<?php

if (!empty($activity['record_reference'])) {

    echo htmlspecialchars(
        $activity['record_reference']
    );

} else {

    echo "-";

}

?>

</td>

<td>

<?php

if (!empty($activity['description'])) {

    echo htmlspecialchars(
        $activity['description']
    );

} else {

    echo "-";

}

?>

</td>

</tr>

<?php

    }

} else {

?>

<tr>

<td colspan="6">
No recent activity found.
</td>

</tr>

<?php

}

?>

</table>

</section>

<section>

<h2>
Recent Cases
</h2>

<p>
Latest registered or updated cases
</p>

<table border="1" width="100%">

<tr>

<th>
Case Number
</th>

<th>
Firm ID
</th>

<th>
Case Title
</th>

<th>
Case Type
</th>

<th>
Status
</th>

<th>
Assigned Officer
</th>

<th>
Last Updated
</th>

</tr>

<?php

if (
    $cases_result &&
    mysqli_num_rows($cases_result) > 0
) {

    while (
        $case = mysqli_fetch_assoc($cases_result)
    ) {

?>

<tr>

<td>
<?php
echo htmlspecialchars($case['case_number']);
?>
</td>

<td>
<?php
echo htmlspecialchars($case['firm_id']);
?>
</td>

<td>
<?php
echo htmlspecialchars($case['case_title']);
?>
</td>

<td>
<?php
echo htmlspecialchars($case['case_type']);
?>
</td>

<td>
<?php
echo htmlspecialchars($case['status']);
?>
</td>

<td>

<?php

if (!empty($case['assigned_officer'])) {

    echo htmlspecialchars(
        $case['assigned_officer']
    );

} else {

    echo "-";

}

?>

</td>

<td>
<?php
echo htmlspecialchars($case['updated_at']);
?>
</td>

</tr>

<?php

    }

} else {

?>

<tr>

<td colspan="7">
No recent cases found.
</td>

</tr>

<?php

}

?>

</table>

</section>

<section>

<h2>
Pending Follow-ups
</h2>

<p>
Upcoming and overdue follow-up obligations
</p>

<table border="1" width="100%">

<tr>

<th>
Case
</th>

<th>
Follow-up
</th>

<th>
Date
</th>

<th>
Assigned Officer
</th>

<th>
Priority
</th>

<th>
Status
</th>

</tr>

<?php

if (
    $followups_result &&
    mysqli_num_rows($followups_result) > 0
) {

    while (
        $followup = mysqli_fetch_assoc($followups_result)
    ) {

?>

<tr>

<td>

<?php

if (!empty($followup['case_number'])) {

    echo htmlspecialchars(
        $followup['case_number']
    );

} else {

    echo "-";

}

?>

</td>

<td>

<?php

if (!empty($followup['title'])) {

    echo htmlspecialchars(
        $followup['title']
    );

} else {

    echo "-";

}

?>

</td>

<td>

<?php

if (!empty($followup['followup_date'])) {

    echo htmlspecialchars(
        $followup['followup_date']
    );

} else {

    echo "-";

}

?>

</td>

<td>

<?php

if (!empty($followup['assigned_officer'])) {

    echo htmlspecialchars(
        $followup['assigned_officer']
    );

} else {

    echo "-";

}

?>

</td>

<td>

<?php

if (!empty($followup['priority'])) {

    echo htmlspecialchars(
        $followup['priority']
    );

} else {

    echo "-";

}

?>

</td>

<td>

<?php

if (!empty($followup['status'])) {

    echo htmlspecialchars(
        $followup['status']
    );

} else {

    echo "-";

}

?>

</td>

</tr>

<?php

    }

} else {

?>

<tr>

<td colspan="6">
No pending follow-ups found.
</td>

</tr>

<?php

}

?>

</table>

</section>

</main>

<?php

include "includes/footer.php";

?>