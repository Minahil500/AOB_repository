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
        document_name
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

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
) {

    $tag_id =
        (int) (
            $_POST['tag_id']
            ?? 0
        );

    if (
        $tag_id <= 0
    ) {

        die("Please select a tag.");

    }

    $check_query = "
        SELECT id
        FROM document_tag_mapping
        WHERE document_id = $document_id
        AND tag_id = $tag_id
        LIMIT 1
    ";

    $check_result =
        mysqli_query(
            $conn,
            $check_query
        );

    if (!$check_result) {

        die(
            "Database Error: "
            . mysqli_error($conn)
        );

    }

    if (
        mysqli_num_rows(
            $check_result
        ) > 0
    ) {

        header(
            "Location: tags.php?id="
            . $document_id
            . "&message=exists"
        );

        exit();

    }

    $insert_query = "
        INSERT INTO document_tag_mapping
        (
            document_id,
            tag_id
        )
        VALUES
        (
            $document_id,
            $tag_id
        )
    ";

    $insert_result =
        mysqli_query(
            $conn,
            $insert_query
        );

    if (!$insert_result) {

        die(
            "Tag Insert Error: "
            . mysqli_error($conn)
        );

    }

    header(
        "Location: tags.php?id="
        . $document_id
        . "&message=added"
    );

    exit();

}

if (
    isset($_GET['remove']) &&
    is_numeric($_GET['remove'])
) {

    $mapping_id =
        (int) $_GET['remove'];

    $delete_query = "
        DELETE FROM document_tag_mapping
        WHERE id = $mapping_id
        AND document_id = $document_id
        LIMIT 1
    ";

    $delete_result =
        mysqli_query(
            $conn,
            $delete_query
        );

    if (!$delete_result) {

        die(
            "Delete Tag Error: "
            . mysqli_error($conn)
        );

    }

    header(
        "Location: tags.php?id="
        . $document_id
        . "&message=removed"
    );

    exit();

}

$tags_query = "
    SELECT
        id,
        tag_name
    FROM document_tags
    ORDER BY tag_name ASC
";

$tags_result =
    mysqli_query(
        $conn,
        $tags_query
    );

if (!$tags_result) {

    die(
        "Tags Error: "
        . mysqli_error($conn)
    );

}

$assigned_query = "
    SELECT
        dtm.id AS mapping_id,
        dt.id AS tag_id,
        dt.tag_name
    FROM document_tag_mapping dtm

    INNER JOIN document_tags dt
        ON dtm.tag_id = dt.id

    WHERE dtm.document_id = $document_id

    ORDER BY dt.tag_name ASC
";

$assigned_result =
    mysqli_query(
        $conn,
        $assigned_query
    );

if (!$assigned_result) {

    die(
        "Assigned Tags Error: "
        . mysqli_error($conn)
    );

}

include "../includes/header.php";

include "../includes/sidebar.php";

?>

<main>

<h1>
Document Tags
</h1>

<p>
Manage tags assigned to this legal document.
</p>

<section>

<h2>

<?php

echo htmlspecialchars(
    $document['document_name']
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
);

?>

</p>

<?php

if (
    isset($_GET['message'])
) {

    if (
        $_GET['message'] === 'added'
    ) {

?>

<p
    style="
        padding:10px;
        background:#dcfce7;
        border:1px solid #86efac;
    "
>
Tag added successfully.
</p>

<?php

    }

    if (
        $_GET['message'] === 'removed'
    ) {

?>

<p
    style="
        padding:10px;
        background:#dcfce7;
        border:1px solid #86efac;
    "
>
Tag removed successfully.
</p>

<?php

    }

    if (
        $_GET['message'] === 'exists'
    ) {

?>

<p
    style="
        padding:10px;
        background:#fef3c7;
        border:1px solid #fcd34d;
    "
>
This tag is already assigned to this document.
</p>

<?php

    }

}

?>

<h3>
Add Tag
</h3>

<form
    method="POST"
>

<div class="form-group">

<label>
Select Tag
</label>

<select
    name="tag_id"
    required
>

<option value="">
Select Tag
</option>

<?php

while (
    $tag =
        mysqli_fetch_assoc(
            $tags_result
        )
) {

?>

<option
    value="<?php
        echo (int) $tag['id'];
    ?>"
>

<?php

echo htmlspecialchars(
    $tag['tag_name']
);

?>

</option>

<?php

}

?>

</select>

</div>

<button
    type="submit"
    class="btn-primary"
>
Add Tag
</button>

</form>

<h3
    style="
        margin-top:30px;
    "
>
Assigned Tags
</h3>

<?php

if (
    mysqli_num_rows(
        $assigned_result
    ) == 0
) {

?>

<p>
No tags assigned to this document.
</p>

<?php

} else {

?>

<table>

<thead>

<tr>

<th>
Tag
</th>

<th>
Action
</th>

</tr>

</thead>

<tbody>

<?php

while (
    $assigned =
        mysqli_fetch_assoc(
            $assigned_result
        )
) {

?>

<tr>

<td>

<?php

echo htmlspecialchars(
    $assigned['tag_name']
);

?>

</td>

<td>

<a
    href="tags.php?id=<?php echo $document_id; ?>&remove=<?php echo (int) $assigned['mapping_id']; ?>"
    class="btn-danger"
    onclick="
        return confirm(
            'Remove this tag from the document?'
        );
    "
>
Remove
</a>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

<?php

}

?>

<div
    style="
        margin-top:25px;
    "
>

<a
    href="view.php?id=<?php echo $document_id; ?>"
    class="btn-secondary"
>
Back to Document
</a>

</div>

</section>

</main>

<?php

include "../includes/footer.php";

?>