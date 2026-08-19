<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "../config/db.php";

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {

    die("Invalid document ID.");

}

$document_id = (int) $_GET['id'];

$document_query = "
    SELECT
        id,
        document_number,
        document_name,
        version
    FROM legal_documents
    WHERE id = $document_id
    LIMIT 1
";

$document_result =
    mysqli_query(
        $conn,
        $document_query
    );

if (!$document_result) {

    die(
        "Database Error: "
        . mysqli_error($conn)
    );

}

if (
    mysqli_num_rows(
        $document_result
    ) == 0
) {

    die("Document not found.");

}

$document =
    mysqli_fetch_assoc(
        $document_result
    );

$versions_query = "
    SELECT
        dv.id,
        dv.version,
        dv.version_number,
        dv.file_name,
        dv.file_path,
        dv.uploaded_by,
        dv.remarks,
        dv.created_at,

        u.username

    FROM document_versions dv

    LEFT JOIN users u
        ON dv.uploaded_by = u.id

    WHERE dv.document_id = $document_id

    ORDER BY dv.id DESC
";

$versions_result =
    mysqli_query(
        $conn,
        $versions_query
    );

if (!$versions_result) {

    die(
        "Version History Error: "
        . mysqli_error($conn)
    );

}

include "../includes/header.php";

include "../includes/sidebar.php";

?>

<main>

<h1>
Document Version History
</h1>

<p>
View all versions of this legal document.
</p>

<section>

<h2>

<?php

echo htmlspecialchars(
    $document['document_name']
    ?? ''
);

?>

</h2>

<p>

<strong>
Document Number:
</strong>

<?php

echo htmlspecialchars(
    $document['document_number']
    ?? ''
);

?>

</p>

<p>

<strong>
Current Version:
</strong>

<?php

echo htmlspecialchars(
    $document['version']
    ?? ''
);

?>

</p>

<div
    style="
        margin:20px 0;
    "
>

<a
    href="add_version.php?id=<?php echo $document_id; ?>"
    class="btn-primary"
>
+ Add New Version
</a>

<a
    href="view.php?id=<?php echo $document_id; ?>"
    class="btn-secondary"
>
Back to Document
</a>

</div>

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
Version
</th>

<th>
Version Number
</th>

<th>
File
</th>

<th>
Uploaded By
</th>

<th>
Remarks
</th>

<th>
Created At
</th>

</tr>

</thead>

<tbody>

<?php

if (
    mysqli_num_rows(
        $versions_result
    ) == 0
) {

?>

<tr>

<td
    colspan="7"
>

No version history found.

</td>

</tr>

<?php

} else {

while (
    $version =
        mysqli_fetch_assoc(
            $versions_result
        )
) {

?>

<tr>

<td>

<?php

echo (int)
    $version['id'];

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $version['version']
    ?? ''
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $version['version_number']
    ?? ''
);

?>

</td>

<td>

<?php

if (
    !empty(
        $version['file_path']
    )
) {

?>

<a
    href="../<?php
        echo htmlspecialchars(
            ltrim(
                $version['file_path'],
                "/"
            )
        );
    ?>"
    target="_blank"
    class="btn-secondary"
>
Open File
</a>

<?php

} else {

    echo htmlspecialchars(
        $version['file_name']
        ?? 'No file'
    );

}

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $version['username']
    ?? 'System'
);

?>

</td>

<td>

<?php

echo nl2br(
    htmlspecialchars(
        $version['remarks']
        ?? ''
    )
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $version['created_at']
    ?? ''
);

?>

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