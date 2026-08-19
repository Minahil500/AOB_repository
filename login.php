<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "config/db.php";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $query = "SELECT * FROM users 
              WHERE username = ? 
              OR official_email = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Query Error: " . $conn->error);
    }
    $stmt->bind_param(
        "ss",
        $login,
        $login
    );
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Role save
            if(isset($user['role_id'])){
                $_SESSION['role_id'] = $user['role_id'];
            }

            header("Location: dashboard.php");
            exit();

        } else {

            $error = "Invalid password";

        }

    } else {

        $error = "User not found";

    }
}

?>
<!DOCTYPE html>
<html>
<head>

<title>AOB Login</title>

</head>

<body>

<h2>AOB Legal Repository Login</h2>
<?php

if($error != "") {

?>

<p style="color:red;">
    <?php echo $error; ?>
</p>

<?php

}

?>
<form method="POST">
<label>
Username or Official Email
</label>
<br>
<input 
type="text" 
name="login"
required>
<br><br>
<label>
Password
</label>
<br>
<input 
type="password"
name="password"
required>
<br><br>
<button type="submit">
Login
</button>
</form>
</body>
</html>