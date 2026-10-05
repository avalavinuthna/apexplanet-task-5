<?php

session_start();

require_once "db.php";


$q = isset($_GET['q'])
    ? trim($_GET['q'])
    : '';

$location = isset($_GET['location'])
    ? trim($_GET['location'])
    : '';


/*
|--------------------------------------------------------------------------
| Search jobs
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        title,
        company,
        location,
        category,
        salary,
        description
    FROM jobs
    WHERE 1=1
";


$params = [];
$types = "";


if ($q !== '') {

    $sql .= "
        AND (
            title LIKE ?
            OR company LIKE ?
            OR category LIKE ?
        )
    ";

    $search_value = "%" . $q . "%";

    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;

    $types .= "sss";
}


if ($location !== '') {

    $sql .= "
        AND location LIKE ?
    ";

    $location_value =
        "%" . $location . "%";

    $params[] = $location_value;

    $types .= "s";
}


$sql .= "
    ORDER BY created_at DESC
";


$stmt = mysqli_prepare(
    $conn,
    $sql
);


if (!empty($params)) {

    mysqli_stmt_bind_param(
        $stmt,
        $types,
        ...$params
    );
}


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
        Search Results - JobConnect
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


<div class="job-search-container">

    <h2>
        Search Results
    </h2>


    <form
        action="search.php"
        method="GET"
        class="job-search-form"
    >

        <label>
            Search
        </label>

        <input
            type="text"
            name="q"
            value="<?php
            echo htmlspecialchars($q);
            ?>"
            placeholder="Job title, company or category"
        >


        <label>
            Location
        </label>

        <input
            type="text"
            name="location"
            value="<?php
            echo htmlspecialchars($location);
            ?>"
            placeholder="Location"
        >


        <button
            type="submit"
            class="form-button"
        >
            Search
        </button>

    </form>


    <div id="jobsContainer">


        <?php if (mysqli_num_rows($result) == 0): ?>

            <div class="no-jobs">

                <h3>
                    No Jobs Found
                </h3>

                <p>
                    Try a different search.
                </p>

            </div>


        <?php else: ?>


            <?php while ($job = mysqli_fetch_assoc($result)): ?>

                <div class="job-card">

                    <h3>
                        <?php
                        echo htmlspecialchars(
                            $job['title']
                        );
                        ?>
                    </h3>


                    <p>

                        <strong>
                            Company:
                        </strong>

                        <?php
                        echo htmlspecialchars(
                            $job['company']
                        );
                        ?>

                    </p>


                    <p>

                        <strong>
                            Location:
                        </strong>

                        <?php
                        echo htmlspecialchars(
                            $job['location']
                        );
                        ?>

                    </p>


                    <p>

                        <strong>
                            Category:
                        </strong>

                        <?php
                        echo htmlspecialchars(
                            $job['category']
                        );
                        ?>

                    </p>


                    <?php if (!empty($job['salary'])): ?>

                        <p>

                            <strong>
                                Salary:
                            </strong>

                            <?php
                            echo htmlspecialchars(
                                $job['salary']
                            );
                            ?>

                        </p>

                    <?php endif; ?>


                    <p>

                        <?php

                        $description =
                            $job['description'];

                        if (strlen($description) > 150) {

                            $description =
                                substr(
                                    $description,
                                    0,
                                    150
                                ) . "...";
                        }

                        echo htmlspecialchars(
                            $description
                        );

                        ?>

                    </p>


                    <a
                        href="job_details.php?id=<?php
                        echo $job['id'];
                        ?>"
                        class="btn"
                    >
                        View Job
                    </a>

                </div>

            <?php endwhile; ?>

        <?php endif; ?>

    </div>

</div>


<footer>

    <p>
        &copy; <?php echo date("Y"); ?> JobConnect.
    </p>

</footer>

</body>

</html>
