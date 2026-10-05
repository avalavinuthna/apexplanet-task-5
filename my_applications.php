<?php

session_start();

require_once "db.php";


if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");

    exit;
}


$user_id = $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| Get applications
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        applications.id,
        applications.resume,
        applications.cover_letter,
        applications.status,
        applications.applied_at,
        jobs.title,
        jobs.company,
        jobs.location
     FROM applications
     INNER JOIN jobs
        ON applications.job_id = jobs.id
     WHERE applications.user_id = ?
     ORDER BY applications.applied_at DESC"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        My Applications - JobConnect
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

        <a href="profile.php">
            Profile
        </a>

        <a href="logout.php">
            Logout
        </a>

    </nav>

</header>


<div class="table-container">

    <h2>
        My Applications
    </h2>


    <?php if (mysqli_num_rows($result) == 0): ?>

        <div class="no-jobs">

            <h3>
                No Applications Yet
            </h3>

            <p>
                You have not applied for any jobs.
            </p>

            <a
                href="jobs.php"
                class="btn"
            >
                Find Jobs
            </a>

        </div>


    <?php else: ?>


        <table>

            <thead>

                <tr>

                    <th>
                        Job
                    </th>

                    <th>
                        Company
                    </th>

                    <th>
                        Location
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Applied Date
                    </th>

                    <th>
                        Resume
                    </th>

                </tr>

            </thead>


            <tbody>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                <tr>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $row['title']
                        );
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $row['company']
                        );
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $row['location']
                        );
                        ?>
                    </td>


                    <td>
                        <strong>
                            <?php
                            echo htmlspecialchars(
                                $row['status']
                            );
                            ?>
                        </strong>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $row['applied_at']
                        );
                        ?>
                    </td>


                    <td>

                        <?php if (!empty($row['resume'])): ?>

                            <a
                                href="uploads/<?php echo urlencode($row['resume']); ?>"
                                target="_blank"
                            >
                                View Resume
                            </a>

                        <?php else: ?>

                            No Resume

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    <?php endif; ?>

</div>


<footer>

    <p>
        &copy; <?php echo date("Y"); ?> JobConnect.
    </p>

</footer>

</body>

</html>
