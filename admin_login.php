
<?php
session_start();

require_once __DIR__ . "/includes/db.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: admin.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter your username and password.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "SELECT id, username, password_hash
                 FROM admins
                 WHERE username = :username
                 LIMIT 1"
            );

            $stmt->execute(["username" => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify(
                $password,
                $admin["password_hash"]
            )) {
                session_regenerate_id(true);

                $_SESSION["admin_id"] = $admin["id"];
                $_SESSION["admin_username"] = $admin["username"];

                header("Location: admin.php");
                exit;
            }

            $error = "Invalid username or password.";

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = "Login could not be completed. Check your database table.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Mini World Decor</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f7f0e7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-box {
            background: white;
            width: 340px;
            max-width: calc(100% - 64px);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px #00000018;
        }

        h2 {
            text-align: center;
            color: #3b2520;
        }

        label {
            display: block;
            margin-top: 16px;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            padding: 12px;
            margin-top: 7px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            margin-top: 22px;
            padding: 12px;
            background: #b88a44;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .error {
            color: #b00020;
            background: #fff0f0;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Admin Login</h2>

    <?php if ($error !== ""): ?>
        <p class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="admin_login.php">
        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            autocomplete="username"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            required
        >

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
