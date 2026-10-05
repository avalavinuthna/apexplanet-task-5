<?php

session_start();

require_once "db.php";

$message = "";
$message_type = "";

$email = $_SESSION['verification_email'] ?? "";


if (empty($email)) {

    header("Location: register.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $otp = trim($_POST['otp']);


    if (empty($otp)) {

        $message = "Please enter the OTP.";
        $message_type = "error";

    } else {


        /*
        |--------------------------------------------------------------------------
        | Get user
        |--------------------------------------------------------------------------
        */

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, otp, otp_expiry
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

            $message = "User account not found.";

        } elseif ($user['otp'] !== $otp) {

            $message = "Invalid OTP.";

        } elseif (
            empty($user['otp_expiry']) ||
            strtotime($user['otp_expiry']) < time()
        ) {

            $message = "OTP has expired. Please register again.";

        } else {


            /*
            |--------------------------------------------------------------------------
            | Verify account
            |--------------------------------------------------------------------------
            */

            $update = mysqli_prepare(
                $conn,
                "UPDATE users
                 SET is_verified = 1,
                     otp = NULL,
                     otp_expiry = NULL
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update,
                "i",
                $user['id']
            );

            mysqli_stmt_execute($update);


            unset($_SESSION['verification_email']);

            $_SESSION['success_message'] =
                "Email verified successfully. You can now login.";

            header("Location: login.php");
            exit;
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

    <title>Verify OTP - JobConnect</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header class="navbar">

    <div class="logo">
        JobConnect
    </div>

</header>


<div class="form-container">

    <h2>
        Verify Email
    </h2>

    <p>
        Enter the 6-digit OTP sent to:
    </p>

    <p>
        <strong>
            <?php echo htmlspecialchars($email); ?>
        </strong>
    </p>


    <?php if ($message != ""): ?>

        <p class="error-message">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>


    <form method="POST">

        <div class="form-group">

            <label>
                Enter OTP
            </label>

            <input
                type="text"
                name="otp"
                maxlength="6"
                pattern="[0-9]{6}"
                required
            >

        </div>


        <button
            type="submit"
            class="form-button"
        >
            Verify OTP
        </button>

    </form>


    <p>

        <a href="register.php">
            Register Again
        </a>

    </p>

</div>

</body>

</html>
