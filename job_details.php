<?php

session_start();

require_once "db.php";


/*
|--------------------------------------------------------------------------
| Get Job ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: jobs.php");

    exit;
}

$job_id = (int)$_GET['id'];


/*
|--------------------------------------------------------------------------
| Get job
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        title,
        company,
        location,
        category,
        salary,
        description,
        requirements,
        created_at
     FROM jobs
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $job_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$job = mysqli_fetch_assoc($result);


if (!$job) {

    die("Job not found.");

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($job['title']); ?>
        - JobConnect
    </title>

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

        <?php if (isset($_SESSION['user_id'])): ?>

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="logout.php">
                Logout
            </a>

        <?php else: ?>

            <a href="login.php">
                Login
            </a>

        <?php endif; ?>

    </nav>

</header>


<div class="form-container">

    <h2>
        <?php echo htmlspecialchars($job['title']); ?>
    </h2>


    <p>

        <strong>
            Company:
        </strong>

        <?php echo htmlspecialchars($job['company']); ?>

    </p>


    <p>

        <strong>
            Location:
        </strong>

        <?php echo htmlspecialchars($job['location']); ?>

    </p>


    <p>

        <strong>
            Category:
        </strong>

        <?php echo htmlspecialchars($job['category']); ?>

    </p>


    <?php if (!empty($job['salary'])): ?>

        <p>

            <strong>
                Salary:
            </strong>

            <?php echo htmlspecialchars($job['salary']); ?>

        </p>

    <?php endif; ?>


    <hr>


    <h3>
        Job Description
    </h3>

    <p>
        <?php
        echo nl2br(
            htmlspecialchars($job['description'])
        );
        ?>
    </p>


    <?php if (!empty($job['requirements'])): ?>

        <h3>
            Requirements
        </h3>

        <p>
            <?php
            echo nl2br(
                htmlspecialchars($job['requirements'])
            );
            ?>
        </p>

    <?php endif; ?>


    <?php if (isset($_SESSION['user_id'])): ?>

        <?php if ($_SESSION['role_id'] == 2): ?>

            <a
                href="apply_job.php?id=<?php echo $job['id']; ?>"
                class="btn"
            >
                Apply Now
            </a>

        <?php endif; ?>

    <?php else: ?>

        <a
            href="login.php"
            class="btn"
        >
            Login to Apply
        </a>

    <?php endif; ?>


</div>


<footer>

    <p>
        &copy; <?php echo date("Y"); ?> JobConnect.
    </p>

</footer>

</body>

</html>
