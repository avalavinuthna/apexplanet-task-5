<?php

session_start();

require_once "db.php";


/*
|--------------------------------------------------------------------------
| Login check
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Admin redirect
|--------------------------------------------------------------------------
*/

if (
    isset($_SESSION['role_id']) &&
    $_SESSION['role_id'] == 1
) {

    header("Location: admin/dashboard.php");

    exit;
}


$user_id = $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| Get application statistics
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        COUNT(*) AS total
     FROM applications
     WHERE user_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);

$total_applications = $row['total'];


/*
|--------------------------------------------------------------------------
| Get selected applications
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        COUNT(*) AS total
     FROM applications
     WHERE user_id = ?
     AND status = 'Selected'"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);

$selected = $row['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Dashboard - JobConnect</title>

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
            My Applications
        </a>

        <a href="profile.php">
            Profile
        </a>

        <a href="logout.php">
            Logout
        </a>

    </nav>

</header>


<div class="container">

    <h2>
        Welcome,
        <?php echo htmlspecialchars($_SESSION['fullname']); ?>
    </h2>


    <div class="cards">

        <div class="card">

            <h3>
                Total Applications
            </h3>

            <p>
                <?php echo $total_applications; ?>
            </p>

        </div>


        <div class="card">

            <h3>
                Selected
            </h3>

            <p>
                <?php echo $selected; ?>
            </p>

        </div>


        <div class="card">

            <h3>
                Find Jobs
            </h3>

            <p>
                Search and apply for new opportunities.
            </p>

            <a
                href="jobs.php"
                class="btn"
            >
                Browse Jobs
            </a>

        </div>

    </div>

</div>


<footer>

    <p>
        &copy; <?php echo date("Y"); ?> JobConnect.
    </p>

</footer>

</body>

</html>
