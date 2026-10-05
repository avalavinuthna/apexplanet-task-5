<?php

session_start();

require_once "db.php";

$message = "";
$message_type = "";


if (isset($_SESSION['success_message'])) {

    $message = $_SESSION['success_message'];

    $message_type = "success";

    unset($_SESSION['success_message']);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];


    if (empty($email) || empty($password)) {

        $message = "Email and password are required.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | Find user
        |--------------------------------------------------------------------------
        */

        $stmt = mysqli_prepare(
            $conn,
            "SELECT
                id,
                fullname,
                email,
                password,
                role_id,
                is_verified
             FROM users
             WHERE email = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $user = mysqli_fetch_assoc($result);


        if (!$user) {

            $message = "Invalid email or password.";

        } elseif (
            !password_verify(
                $password,
                $user['password']
            )
        ) {

            $message = "Invalid email or password.";

        } elseif ((int)$user['is_verified'] !== 1) {

            $_SESSION['verification_email'] = $email;

            $message =
                "Please verify your email before logging in.";

        } else {


            /*
            |--------------------------------------------------------------------------
            | Create session
            |--------------------------------------------------------------------------
            */

            $_SESSION['user_id'] = $user['id'];

            $_SESSION['fullname'] = $user['fullname'];

            $_SESSION['email'] = $user['email'];

            $_SESSION['role_id'] = $user['role_id'];


            /*
            |--------------------------------------------------------------------------
            | Redirect
            |--------------------------------------------------------------------------
            */

            if ((int)$user['role_id'] === 1) {

                header("Location: admin/dashboard.php");
                exit;

            } else {

                header("Location: dashboard.php");
                exit;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - JobConnect</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header class="navbar">

    <div class="logo">
        JobConnect
    </div>

    <nav>

        <a href="index.php">
            Home
        </a>

        <a href="jobs.php">
            Jobs
        </a>

        <a href="register.php">
            Register
        </a>

    </nav>

</header>


<div class="form-container">

    <h2>
        Login
    </h2>


    <?php if ($message != ""): ?>

        <p class="<?php echo $message_type == 'success'
            ? 'success-message'
            : 'error-message'; ?>">

            <?php echo htmlspecialchars($message); ?>

        </p>

    <?php endif; ?>


    <form method="POST">

        <div class="form-group">

            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Password
            </label>

            <input
                type="password"
                name="password"
                required
            >

        </div>


        <button
            type="submit"
            class="form-button"
        >
            Login
        </button>

    </form>


    <p>

        Don't have an account?

        <a href="register.php">
            Register
        </a>

    </p>

</div>

</body>

</html>
