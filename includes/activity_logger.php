<?php

function log_activity(
    $conn,
    $module,
    $action,
    $record_reference = null,
    $previous_value = null,
    $new_value = null,
    $description = null
) {

    $user_id = $_SESSION['user_id'] ?? null;
    $role_id = $_SESSION['role_id'] ?? null;

    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;


    $query = "
        INSERT INTO activity_logs
        (
            user_id,
            role_id,
            module,
            action,
            record_reference,
            previous_value,
            new_value,
            ip_address,
            description
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";


    $stmt = mysqli_prepare(
        $conn,
        $query
    );


    if (!$stmt) {

        die(
            "Activity Log Prepare Error: "
            . mysqli_error($conn)
        );

    }

    $user_id_param = $user_id !== null
        ? (int)$user_id
        : null;

    $role_id_param = $role_id !== null
        ? (int)$role_id
        : null;


    $module_param =
        (string)$module;

    $action_param =
        (string)$action;

    $record_reference_param =
        $record_reference !== null
        ? (string)$record_reference
        : null;

    $previous_value_param =
        $previous_value !== null
        ? (string)$previous_value
        : null;

    $new_value_param =
        $new_value !== null
        ? (string)$new_value
        : null;

    $ip_address_param =
        $ip_address !== null
        ? (string)$ip_address
        : null;

    $description_param =
        $description !== null
        ? (string)$description
        : null;


    mysqli_stmt_bind_param(
        $stmt,
        "iisssssss",
        $user_id_param,
        $role_id_param,
        $module_param,
        $action_param,
        $record_reference_param,
        $previous_value_param,
        $new_value_param,
        $ip_address_param,
        $description_param
    );


    if (!mysqli_stmt_execute($stmt)) {

        die(
            "Activity Log Insert Error: "
            . mysqli_stmt_error($stmt)
        );

    }


    mysqli_stmt_close($stmt);
}