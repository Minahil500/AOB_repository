<?php

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function get_current_role_id($conn)
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $user_id = (int) $_SESSION['user_id'];

    $query = "
        SELECT role_id
        FROM users
        WHERE id = $user_id
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) === 0) {
        return null;
    }

    $row = mysqli_fetch_assoc($result);

    return (int) $row['role_id'];
}

function has_permission($conn, $permission_name)
{
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $user_id = (int) $_SESSION['user_id'];

    $permission_name =
        mysqli_real_escape_string(
            $conn,
            $permission_name
        );

    $query = "
        SELECT
            rp.id
        FROM users u
        INNER JOIN role_permissions rp
            ON u.role_id = rp.role_id
        INNER JOIN permissions p
            ON rp.permission_id = p.id
        WHERE
            u.id = $user_id
            AND p.permission_name = '$permission_name'
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        return false;
    }

    return mysqli_num_rows($result) > 0;
}

function require_permission(
    $conn,
    $permission_name
)
{
    if (!has_permission(
        $conn,
        $permission_name
    )) {

        http_response_code(403);

        die(
            "
            <h1>Access Denied</h1>

            <p>
            You do not have permission
            to perform this action.
            </p>
            "
        );
    }
}