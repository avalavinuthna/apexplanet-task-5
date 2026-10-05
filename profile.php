<?php

session_start();

require_once "db.php";


if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");

    exit;
}


$user_id = $_SESSION['user_id'];

$message = "";


/*
|--------------------------------------------------------------------------
| Update profile
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST['fullname']);


    if (empty($fullname)) {

        $message = "Full name is required.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE users
             SET fullname = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $fullname,
            $user_id
        );


        if (mysqli_stmt_execute($stmt)) {

            $_SESSION['fullname'] = $fullname;

            $message =
                "Profile updated successfully.";

        } else {

            $message =
                "Unable to update profile.";

        }

    }
}


/*
|--------------------------------------------------------------------------
| Get current profile
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        fullname,
        email,
        created_at,
        is_verified
     FROM users
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Profile - JobConnect
    </title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header class="navbar">

    <div class="logo">
        JobConnect
    </div>

    <nav>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="jobs.php">
            Jobs
        </a>

        <a href="my_applications.php">
            Applications
        </a>

        <a href="logout.php">
            Logout
        </a>

    </nav>

</header>


<div class="form-container">

    <h2>
        My Profile
    </h2>


    <?php if ($message != ""): ?>

        <p class="success-message">

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
                value="<?php
                echo htmlspecialchars(
                    $user['fullname']
                );
                ?>"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Email
            </label>

            <input
                type="email"
                value="<?php
                echo htmlspecialchars(
                    $user['email']
                );
                ?>"
                readonly
            >

        </div>


        <div class="form-group">

            <label>
                Email Verification
            </label>

            <input
                type="text"
                value="<?php
                echo $user['is_verified']
                    ? 'Verified'
                    : 'Not Verified';
                ?>"
                readonly
            >

        </div>


        <div class="form-group">

            <label>
                Account Created
            </label>

            <input
                type="text"
                value="<?php
                echo htmlspecialchars(
                    $user['created_at']
                );
                ?>"
                readonly
            >

        </div>


        <button
            type="submit"
            class="form-button"
        >
            Update Profile
        </button>

    </form>

</div>

</body>

</html>
