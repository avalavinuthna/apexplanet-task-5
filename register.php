<?php

session_start();

require_once "db.php";

$message = "";
$message_type = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (
        empty($fullname) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $message = "All fields are required.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    } elseif (strlen($password) < 6) {

        $message = "Password must contain at least 6 characters.";
        $message_type = "error";

    } else {


        /*
        |--------------------------------------------------------------------------
        | Check existing email
        |--------------------------------------------------------------------------
        */

        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param(
            $check,
            "s",
            $email
        );

        mysqli_stmt_execute($check);

        mysqli_stmt_store_result($check);


        if (mysqli_stmt_num_rows($check) > 0) {

            $message = "Email address already registered.";
            $message_type = "error";

        } else {


            /*
            |--------------------------------------------------------------------------
            | Generate OTP
            |--------------------------------------------------------------------------
            */

            $otp = strval(random_int(100000, 999999));

            $otp_expiry = date(
                "Y-m-d H:i:s",
                strtotime("+10 minutes")
            );


            /*
            |--------------------------------------------------------------------------
            | Hash password
            |--------------------------------------------------------------------------
            */

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            /*
            |--------------------------------------------------------------------------
            | Insert user
            |--------------------------------------------------------------------------
            */

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users
                (
                    fullname,
                    email,
                    password,
                    role_id,
                    otp,
                    otp_expiry,
                    is_verified
                )
                VALUES (?, ?, ?, 2, ?, ?, 0)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $fullname,
                $email,
                $hashed_password,
                $otp,
                $otp_expiry
            );


            if (mysqli_stmt_execute($stmt)) {


                /*
                |--------------------------------------------------------------------------
                | Store email in session
                |--------------------------------------------------------------------------
                */

                $_SESSION['verification_email'] = $email;


                /*
                |--------------------------------------------------------------------------
                | Send OTP Email
                |--------------------------------------------------------------------------
                */

                $subject = "JobConnect Email Verification";

                $body =
                    "Hello " . $fullname . ",\n\n" .
                    "Your JobConnect verification OTP is: " . $otp . "\n\n" .
                    "This OTP is valid for 10 minutes.\n\n" .
                    "Thank you,\n" .
                    "JobConnect Team";


                $headers =
                    "From: noreply@jobconnect.com\r\n" .
                    "Reply-To: noreply@jobconnect.com\r\n";


                @mail(
                    $email,
                    $subject,
                    $body,
                    $headers
                );


                /*
                |--------------------------------------------------------------------------
                | Redirect to OTP verification
                |--------------------------------------------------------------------------
                */

                header("Location: verify_otp.php");
                exit;

            } else {

                $message =
                    "Registration failed: " .
                    mysqli_error($conn);

                $message_type = "error";
            }

        }

        mysqli_stmt_close($check);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Register - JobConnect</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header class="navbar">

    <div class="logo">
        JobConnect
    </div>

    <nav>

        <a href="index.php">Home</a>

        <a href="jobs.php">Jobs</a>

        <a href="login.php">Login</a>

    </nav>

</header>


<div class="form-container">

    <h2>
        Create Account
    </h2>


    <?php if ($message != ""): ?>

        <p class="error-message">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>


    <form method="POST">


        <div class="form-group">

            <label>
                Full Name
            </label>

            <input
                type="text"
                name="fullname"
                required
            >

        </div>


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


        <div class="form-group">

            <label>
                Confirm Password
            </label>

            <input
                type="password"
                name="confirm_password"
                required
            >

        </div>


        <button
            type="submit"
            class="form-button"
        >
            Register
        </button>

    </form>


    <p>
        Already have an account?

        <a href="login.php">
            Login
        </a>

    </p>

</div>


<footer>

    <p>
        &copy; <?php echo date("Y"); ?> JobConnect.
    </p>

</footer>

</body>

</html>
