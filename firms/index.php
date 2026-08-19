<?php

session_start();

include "../config/db.php";

$query = "
    SELECT
        id,
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
        created_at
    FROM firms
    ORDER BY id DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error loading firms: " . mysqli_error($conn));
}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<div class="page-header">

    <div>

        <h1>
            Firms
        </h1>

        <p>
            Registered audit firms and their official information.
        </p>

    </div>

    <div>

        <a href="create.php" class="btn-primary">
            + Add Firm
        </a>

    </div>

</div>

<section>

<h2>
Registered Firms
</h2>

<div class="table-container">

<table>

<thead>

<tr>

<th>Firm Code</th>

<th>Firm Name</th>

<th>NTN</th>

<th>Official Email</th>

<th>City</th>

<th>Province</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if (mysqli_num_rows($result) > 0) {

    while ($firm = mysqli_fetch_assoc($result)) {

?>

<tr>

<td>

<?php

echo htmlspecialchars(
    $firm['firm_code']
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $firm['firm_name']
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $firm['ntn_number']
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $firm['official_email']
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $firm['city']
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $firm['province']
);

?>

</td>

<td>

<span class="status-badge">

<?php

echo htmlspecialchars(
    $firm['status']
);

?>

</span>

</td>

<td>

<a href="view.php?id=<?php echo $firm['id']; ?>">
View
</a>

&nbsp; | &nbsp;

<a href="edit.php?id=<?php echo $firm['id']; ?>">
Edit
</a>

<a href="delete.php?id=<?php echo $firm['id']; ?>">
    Delete
</a>

</td>

</tr>

<?php

    }

} else {

?>

<tr>

<td colspan="8">

No firms found.

</td>

</tr>

<?php

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