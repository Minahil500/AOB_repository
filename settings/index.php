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

$user_id = (int) $_SESSION['user_id'];

$organisation_name = "AOB Legal Repository";
$short_name = "AOB";
$official_email = "";
$contact_number = "";
$time_zone = "Asia/Karachi";
$default_department_id = null;
$registered_address = "";

$minimum_password_length = 12;
$password_expiry_days = 90;
$failed_attempts_before_lockout = 3;
$session_timeout_minutes = 20;
$default_role_id = null;
$require_two_factor_auth = 0;
$restrict_network_access = 0;

$maximum_file_size_mb = 20;
$allowed_file_types = "pdf";
$default_document_type_id = null;
$record_retention_years = 7;

$run_ocr_on_upload = 0;
$keep_version_history = 1;
$case_assignment_alerts = 1;
$followup_due_reminders = 1;
$daily_digest_email = 0;

$settings_query = "
    SELECT *
    FROM system_settings
    ORDER BY id ASC
    LIMIT 1
";

$settings_result = mysqli_query(
    $conn,
    $settings_query
);

if (!$settings_result) {

    die(
        "Settings Database Error: "
        . mysqli_error($conn)
    );

}

if (mysqli_num_rows($settings_result) > 0) {

    $settings = mysqli_fetch_assoc(
        $settings_result
    );

    $organisation_name =
        $settings['organisation_name'] ?? $organisation_name;

    $short_name =
        $settings['short_name'] ?? $short_name;

    $official_email =
        $settings['official_email'] ?? $official_email;

    $contact_number =
        $settings['contact_number'] ?? $contact_number;

    $time_zone =
        $settings['time_zone'] ?? $time_zone;

    $default_department_id =
        $settings['default_department_id'];

    $registered_address =
        $settings['registered_address'] ?? $registered_address;

    $minimum_password_length =
        (int) $settings['minimum_password_length'];

    $password_expiry_days =
        (int) $settings['password_expiry_days'];

    $failed_attempts_before_lockout =
        (int) $settings['failed_attempts_before_lockout'];

    $session_timeout_minutes =
        (int) $settings['session_timeout_minutes'];

    $default_role_id =
        $settings['default_role_id'];

    $require_two_factor_auth =
        (int) $settings['require_two_factor_auth'];

    $restrict_network_access =
        (int) $settings['restrict_network_access'];

    $maximum_file_size_mb =
        (int) $settings['maximum_file_size_mb'];

    $allowed_file_types =
        $settings['allowed_file_types'] ?? $allowed_file_types;

    $default_document_type_id =
        $settings['default_document_type_id'];

    $record_retention_years =
        (int) $settings['record_retention_years'];

    $run_ocr_on_upload =
        (int) $settings['run_ocr_on_upload'];

    $keep_version_history =
        (int) $settings['keep_version_history'];

    $case_assignment_alerts =
        (int) $settings['case_assignment_alerts'];

    $followup_due_reminders =
        (int) $settings['followup_due_reminders'];

    $daily_digest_email =
        (int) $settings['daily_digest_email'];
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $organisation_name =
        trim($_POST['organisation_name'] ?? '');

    $short_name =
        trim($_POST['short_name'] ?? '');

    $official_email =
        trim($_POST['official_email'] ?? '');

    $contact_number =
        trim($_POST['contact_number'] ?? '');

    $time_zone =
        trim(
            $_POST['time_zone']
            ?? 'Asia/Karachi'
        );

    $default_department_id =
        !empty(
            $_POST['default_department_id']
        )
        ? (int) $_POST['default_department_id']
        : null;

    $registered_address =
        trim(
            $_POST['registered_address']
            ?? ''
        );

    $minimum_password_length =
        (int) (
            $_POST['minimum_password_length']
            ?? 12
        );

    $password_expiry_days =
        (int) (
            $_POST['password_expiry_days']
            ?? 90
        );

    $failed_attempts_before_lockout =
        (int) (
            $_POST['failed_attempts_before_lockout']
            ?? 3
        );

    $session_timeout_minutes =
        (int) (
            $_POST['session_timeout_minutes']
            ?? 20
        );

    $default_role_id =
        !empty(
            $_POST['default_role_id']
        )
        ? (int) $_POST['default_role_id']
        : null;

    $require_two_factor_auth =
        isset(
            $_POST['require_two_factor_auth']
        )
        ? 1
        : 0;

    $restrict_network_access =
        isset(
            $_POST['restrict_network_access']
        )
        ? 1
        : 0;

    $maximum_file_size_mb =
        (int) (
            $_POST['maximum_file_size_mb']
            ?? 20
        );

    $allowed_file_types =
        trim(
            $_POST['allowed_file_types']
            ?? 'pdf'
        );

    $default_document_type_id =
        !empty(
            $_POST['default_document_type_id']
        )
        ? (int) $_POST['default_document_type_id']
        : null;

    $record_retention_years =
        (int) (
            $_POST['record_retention_years']
            ?? 7
        );

    $run_ocr_on_upload =
        isset(
            $_POST['run_ocr_on_upload']
        )
        ? 1
        : 0;

    $keep_version_history =
        isset(
            $_POST['keep_version_history']
        )
        ? 1
        : 0;

    $case_assignment_alerts =
        isset(
            $_POST['case_assignment_alerts']
        )
        ? 1
        : 0;

    $followup_due_reminders =
        isset(
            $_POST['followup_due_reminders']
        )
        ? 1
        : 0;

    $daily_digest_email =
        isset(
            $_POST['daily_digest_email']
        )
        ? 1
        : 0;

    if ($organisation_name === '') {

        $error =
            "Organisation name is required.";

    } elseif (
        $official_email !== '' &&
        !filter_var(
            $official_email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            "Please enter a valid official email.";

    } elseif (
        $minimum_password_length < 8
    ) {

        $error =
            "Minimum password length cannot be less than 8.";

    } elseif (
        $password_expiry_days < 0
    ) {

        $error =
            "Password expiry days cannot be negative.";

    } elseif (
        $failed_attempts_before_lockout < 1
    ) {

        $error =
            "Failed login attempts must be at least 1.";

    } elseif (
        $session_timeout_minutes < 1
    ) {

        $error =
            "Session timeout must be at least 1 minute.";

    } elseif (
        $maximum_file_size_mb < 1
    ) {

        $error =
            "Maximum file size must be at least 1 MB.";

    } elseif (
        $record_retention_years < 1
    ) {

        $error =
            "Record retention must be at least 1 year.";

    } else {

        $old_query = "
            SELECT *
            FROM system_settings
            ORDER BY id ASC
            LIMIT 1
        ";

        $old_result = mysqli_query(
            $conn,
            $old_query
        );

        $old_settings = null;

        if (
            $old_result &&
            mysqli_num_rows($old_result) > 0
        ) {

            $old_settings =
                mysqli_fetch_assoc(
                    $old_result
                );

        }

        $check_query = "
            SELECT id
            FROM system_settings
            ORDER BY id ASC
            LIMIT 1
        ";

        $check_result = mysqli_query(
            $conn,
            $check_query
        );

        if (!$check_result) {

            $error =
                "Unable to check settings: "
                . mysqli_error($conn);

        } else {

            if (
                mysqli_num_rows($check_result) == 0
            ) {

                $insert_query = "
                    INSERT INTO system_settings
                    (
                        organisation_name,
                        short_name,
                        official_email,
                        contact_number,
                        time_zone,
                        default_department_id,
                        registered_address,
                        minimum_password_length,
                        password_expiry_days,
                        failed_attempts_before_lockout,
                        session_timeout_minutes,
                        default_role_id,
                        require_two_factor_auth,
                        restrict_network_access,
                        maximum_file_size_mb,
                        allowed_file_types,
                        default_document_type_id,
                        record_retention_years,
                        run_ocr_on_upload,
                        keep_version_history,
                        case_assignment_alerts,
                        followup_due_reminders,
                        daily_digest_email
                    )
                    VALUES
                    (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )
                ";

                $stmt = mysqli_prepare(
                    $conn,
                    $insert_query
                );

                if (!$stmt) {

                    $error =
                        "Settings insert error: "
                        . mysqli_error($conn);

                } else {

                    mysqli_stmt_bind_param(
                        $stmt,
                        "sssssisisiiiiisisiiiiii",
                        $organisation_name,
                        $short_name,
                        $official_email,
                        $contact_number,
                        $time_zone,
                        $default_department_id,
                        $registered_address,
                        $minimum_password_length,
                        $password_expiry_days,
                        $failed_attempts_before_lockout,
                        $session_timeout_minutes,
                        $default_role_id,
                        $require_two_factor_auth,
                        $restrict_network_access,
                        $maximum_file_size_mb,
                        $allowed_file_types,
                        $default_document_type_id,
                        $record_retention_years,
                        $run_ocr_on_upload,
                        $keep_version_history,
                        $case_assignment_alerts,
                        $followup_due_reminders,
                        $daily_digest_email
                    );

                    if (
                        mysqli_stmt_execute($stmt)
                    ) {

                        log_activity(
                            $conn,
                            "System Settings",
                            "CREATE",
                            "System Settings",
                            null,
                            json_encode([
                                "organisation_name" =>
                                    $organisation_name,
                                "short_name" =>
                                    $short_name,
                                "official_email" =>
                                    $official_email,
                                "time_zone" =>
                                    $time_zone
                            ]),
                            "Initial system settings created."
                        );

                        $message =
                            "System settings saved successfully.";

                    } else {

                        $error =
                            "Unable to save settings: "
                            . mysqli_stmt_error($stmt);

                    }

                    mysqli_stmt_close($stmt);
                }

            } else {

                $existing =
                    mysqli_fetch_assoc(
                        $check_result
                    );

                $settings_id =
                    (int) $existing['id'];

                $update_query = "
                    UPDATE system_settings
                    SET
                        organisation_name = ?,
                        short_name = ?,
                        official_email = ?,
                        contact_number = ?,
                        time_zone = ?,
                        default_department_id = ?,
                        registered_address = ?,
                        minimum_password_length = ?,
                        password_expiry_days = ?,
                        failed_attempts_before_lockout = ?,
                        session_timeout_minutes = ?,
                        default_role_id = ?,
                        require_two_factor_auth = ?,
                        restrict_network_access = ?,
                        maximum_file_size_mb = ?,
                        allowed_file_types = ?,
                        default_document_type_id = ?,
                        record_retention_years = ?,
                        run_ocr_on_upload = ?,
                        keep_version_history = ?,
                        case_assignment_alerts = ?,
                        followup_due_reminders = ?,
                        daily_digest_email = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ";

                $stmt = mysqli_prepare(
                    $conn,
                    $update_query
                );

                if (!$stmt) {

                    $error =
                        "Settings update error: "
                        . mysqli_error($conn);

                } else {

                    mysqli_stmt_bind_param(
                        $stmt,
                        "sssssisisiiiiisisiiiiiii",
                        $organisation_name,
                        $short_name,
                        $official_email,
                        $contact_number,
                        $time_zone,
                        $default_department_id,
                        $registered_address,
                        $minimum_password_length,
                        $password_expiry_days,
                        $failed_attempts_before_lockout,
                        $session_timeout_minutes,
                        $default_role_id,
                        $require_two_factor_auth,
                        $restrict_network_access,
                        $maximum_file_size_mb,
                        $allowed_file_types,
                        $default_document_type_id,
                        $record_retention_years,
                        $run_ocr_on_upload,
                        $keep_version_history,
                        $case_assignment_alerts,
                        $followup_due_reminders,
                        $daily_digest_email,
                        $settings_id
                    );

                    if (
                        mysqli_stmt_execute($stmt)
                    ) {

                        log_activity(
                            $conn,
                            "System Settings",
                            "UPDATE",
                            "System Settings",
                            $old_settings
                                ? json_encode($old_settings)
                                : null,
                            json_encode([
                                "organisation_name" =>
                                    $organisation_name,
                                "short_name" =>
                                    $short_name,
                                "official_email" =>
                                    $official_email,
                                "contact_number" =>
                                    $contact_number,
                                "time_zone" =>
                                    $time_zone,
                                "default_department_id" =>
                                    $default_department_id,
                                "registered_address" =>
                                    $registered_address,
                                "minimum_password_length" =>
                                    $minimum_password_length,
                                "password_expiry_days" =>
                                    $password_expiry_days,
                                "failed_attempts_before_lockout" =>
                                    $failed_attempts_before_lockout,
                                "session_timeout_minutes" =>
                                    $session_timeout_minutes,
                                "default_role_id" =>
                                    $default_role_id,
                                "require_two_factor_auth" =>
                                    $require_two_factor_auth,
                                "restrict_network_access" =>
                                    $restrict_network_access,
                                "maximum_file_size_mb" =>
                                    $maximum_file_size_mb,
                                "allowed_file_types" =>
                                    $allowed_file_types,
                                "default_document_type_id" =>
                                    $default_document_type_id,
                                "record_retention_years" =>
                                    $record_retention_years,
                                "run_ocr_on_upload" =>
                                    $run_ocr_on_upload,
                                "keep_version_history" =>
                                    $keep_version_history,
                                "case_assignment_alerts" =>
                                    $case_assignment_alerts,
                                "followup_due_reminders" =>
                                    $followup_due_reminders,
                                "daily_digest_email" =>
                                    $daily_digest_email
                            ]),
                            "System settings updated."
                        );

                        $message =
                            "System settings updated successfully.";

                    } else {

                        $error =
                            "Unable to save settings: "
                            . mysqli_stmt_error($stmt);

                    }

                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<main>

<h1>
System Settings
</h1>

<p>
Configure organisation and security settings for the AOB Legal Repository.
</p>

<?php if ($message !== '') { ?>

<div style="
    background:#ecfdf5;
    border:1px solid #a7f3d0;
    color:#065f46;
    padding:15px;
    border-radius:8px;
    margin-bottom:20px;
">

<?php echo htmlspecialchars($message); ?>

</div>

<?php } ?>

<?php if ($error !== '') { ?>

<div style="
    background:#fef2f2;
    border:1px solid #fecaca;
    color:#991b1b;
    padding:15px;
    border-radius:8px;
    margin-bottom:20px;
">

<?php echo htmlspecialchars($error); ?>

</div>

<?php } ?>

<section>

<h2>
Organisation Settings
</h2>

<form method="POST">

<div class="form-group">

<label>
Organisation Name
</label>

<input
    type="text"
    name="organisation_name"
    value="<?php echo htmlspecialchars($organisation_name); ?>"
    required
>

</div>

<div class="form-group">

<label>
Short Name
</label>

<input
    type="text"
    name="short_name"
    value="<?php echo htmlspecialchars($short_name); ?>"
>

</div>

<div class="form-group">

<label>
Official Email
</label>

<input
    type="email"
    name="official_email"
    value="<?php echo htmlspecialchars($official_email); ?>"
>

</div>

<div class="form-group">

<label>
Contact Number
</label>

<input
    type="text"
    name="contact_number"
    value="<?php echo htmlspecialchars($contact_number); ?>"
>

</div>

<div class="form-group">

<label>
Time Zone
</label>

<select name="time_zone">

<option
    value="Asia/Karachi"
    <?php
    echo $time_zone === 'Asia/Karachi'
        ? 'selected'
        : '';
    ?>
>
Asia/Karachi (PKT)
</option>

<option
    value="UTC"
    <?php
    echo $time_zone === 'UTC'
        ? 'selected'
        : '';
    ?>
>
UTC
</option>

</select>

</div>

<div class="form-group">

<label>
Registered Address
</label>

<textarea
    name="registered_address"
    rows="4"
><?php echo htmlspecialchars($registered_address); ?></textarea>

</div>

<h2 style="margin-top:35px;">
Security Policy
</h2>

<div class="form-group">

<label>
Minimum Password Length
</label>

<input
    type="number"
    name="minimum_password_length"
    min="8"
    value="<?php echo (int)$minimum_password_length; ?>"
>

</div>

<div class="form-group">

<label>
Password Expiry (Days)
</label>

<input
    type="number"
    name="password_expiry_days"
    min="0"
    value="<?php echo (int)$password_expiry_days; ?>"
>

</div>

<div class="form-group">

<label>
Failed Login Attempts Before Lockout
</label>

<input
    type="number"
    name="failed_attempts_before_lockout"
    min="1"
    value="<?php echo (int)$failed_attempts_before_lockout; ?>"
>

</div>

<div class="form-group">

<label>
Session Timeout (Minutes)
</label>

<input
    type="number"
    name="session_timeout_minutes"
    min="1"
    value="<?php echo (int)$session_timeout_minutes; ?>"
>

</div>

<div class="form-group">

<label>
Maximum File Size (MB)
</label>

<input
    type="number"
    name="maximum_file_size_mb"
    min="1"
    value="<?php echo (int)$maximum_file_size_mb; ?>"
>

</div>

<div class="form-group">

<label>
Allowed File Types
</label>

<input
    type="text"
    name="allowed_file_types"
    value="<?php echo htmlspecialchars($allowed_file_types); ?>"
>

</div>

<div class="form-group">

<label>
Record Retention (Years)
</label>

<input
    type="number"
    name="record_retention_years"
    min="1"
    value="<?php echo (int)$record_retention_years; ?>"
>

</div>

<div style="margin-top:20px;">

<label>

<input
    type="checkbox"
    name="require_two_factor_auth"
    value="1"
    <?php
    echo $require_two_factor_auth
        ? 'checked'
        : '';
    ?>
>

Require Two-Factor Authentication

</label>

</div>

<div style="margin-top:15px;">

<label>

<input
    type="checkbox"
    name="restrict_network_access"
    value="1"
    <?php
    echo $restrict_network_access
        ? 'checked'
        : '';
    ?>
>

Restrict Access to AOB Network Ranges

</label>

</div>

<h2 style="margin-top:35px;">
Document & System Behaviour
</h2>

<div style="margin-top:15px;">

<label>

<input
    type="checkbox"
    name="run_ocr_on_upload"
    value="1"
    <?php
    echo $run_ocr_on_upload
        ? 'checked'
        : '';
    ?>
>

Run OCR Automatically on Upload

</label>

</div>

<div style="margin-top:15px;">

<label>

<input
    type="checkbox"
    name="keep_version_history"
    value="1"
    <?php
    echo $keep_version_history
        ? 'checked'
        : '';
    ?>
>

Keep Document Version History

</label>

</div>

<div style="margin-top:15px;">

<label>

<input
    type="checkbox"
    name="case_assignment_alerts"
    value="1"
    <?php
    echo $case_assignment_alerts
        ? 'checked'
        : '';
    ?>
>

Case Assignment Alerts

</label>

</div>

<div style="margin-top:15px;">

<label>

<input
    type="checkbox"
    name="followup_due_reminders"
    value="1"
    <?php
    echo $followup_due_reminders
        ? 'checked'
        : '';
    ?>
>

Follow-up Due Reminders

</label>

</div>

<div style="margin-top:15px;margin-bottom:25px;">

<label>

<input
    type="checkbox"
    name="daily_digest_email"
    value="1"
    <?php
    echo $daily_digest_email
        ? 'checked'
        : '';
    ?>
>

Daily Digest Email

</label>

</div>

<div style="
    display:flex;
    gap:10px;
    flex-wrap:wrap;
">

<button
    type="submit"
    class="btn-primary"
>
Save Settings
</button>

<a
    href="../dashboard.php"
    class="btn-secondary"
>
Back to Dashboard
</a>

</div>

</form>

</section>

</main>

<?php

include "../includes/footer.php";

?>