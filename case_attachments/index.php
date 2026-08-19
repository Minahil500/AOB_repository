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
        ca.id,
        ca.case_id,
        ca.file_name,
        ca.file_path,
        ca.category,
        ca.document_date,
        ca.version,
        ca.description,
        ca.uploaded_by,
        ca.uploaded_at,
        c.case_number,
        c.case_title,
        u.username AS uploader
    FROM case_attachments ca
    LEFT JOIN cases c
        ON ca.case_id = c.id
    LEFT JOIN users u
        ON ca.uploaded_by = u.id
    ORDER BY ca.id DESC
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

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<main>
<h1>Case Attachments</h1>
<p>View and manage documents attached to legal cases.</p>
<div style="margin-bottom:20px;">
<a href="create.php" class="btn-primary">+ Upload Attachment</a>
</div>
<section>
<?php if (mysqli_num_rows($result) == 0) { ?>
<p>No case attachments found.</p>
<?php } else { ?>
<div style="overflow-x:auto;">
<table>
<thead>
<tr>
<th>ID</th>
<th>Case</th>
<th>File</th>
<th>Category</th>
<th>Document Date</th>
<th>Version</th>
<th>Uploaded By</th>
<th>Uploaded At</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
<td><?php echo (int)$row['id']; ?></td>
<td>
<strong><?php echo htmlspecialchars($row['case_number'] ?? 'N/A'); ?></strong>
<br>
<small><?php echo htmlspecialchars($row['case_title'] ?? ''); ?></small>
</td>
<td>
<a href="../<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank">
<?php echo htmlspecialchars($row['file_name']); ?>
</a>
</td>
<td><?php echo htmlspecialchars($row['category']); ?></td>
<td><?php echo htmlspecialchars($row['document_date'] ?? 'N/A'); ?></td>
<td><?php echo htmlspecialchars($row['version'] ?? '1.0'); ?></td>
<td><?php echo htmlspecialchars($row['uploader'] ?? 'System'); ?></td>
<td><?php echo htmlspecialchars($row['uploaded_at']); ?></td>
<td>
<a href="edit.php?id=<?php echo (int)$row['id']; ?>" class="btn-secondary">Edit</a>
<a href="delete.php?id=<?php echo (int)$row['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this attachment?');">Delete</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
<?php } ?>
</section>
</main>
<?php
include "../includes/footer.php";
?>