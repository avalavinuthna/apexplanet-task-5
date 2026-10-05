<?php

session_start();

require_once "db.php";

if (isset($_SESSION['user_id'])) {

    if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
        header("Location: admin/dashboard.php");
        exit;
    }

    header("Location: dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>JobConnect - Find Your Dream Job</title>

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

        <a href="register.php">Register</a>

    </nav>

</header>


<section class="hero">

    <div class="hero-content">

        <h1>
            Find Your Dream Job
        </h1>

        <p>
            Search thousands of jobs and take the next step
            in your career.
        </p>


        <form
            action="search.php"
            method="GET"
            class="search-box"
        >

            <input
                type="text"
                name="q"
                placeholder="Search job title, company or category"
                required
            >

            <input
                type="text"
                name="location"
                placeholder="Location"
            >

            <button type="submit">
                Search Jobs
            </button>

        </form>

    </div>

</section>


<section class="container">

    <h2>
        Why Choose JobConnect?
    </h2>


    <div class="cards">

        <div class="card">

            <h3>
                Find Jobs
            </h3>

            <p>
                Search jobs by title, category and location.
            </p>

        </div>


        <div class="card">

            <h3>
                Easy Applications
            </h3>

            <p>
                Apply for jobs and track your applications.
            </p>

        </div>


        <div class="card">

            <h3>
                Career Opportunities
            </h3>

            <p>
                Discover new opportunities and grow your career.
            </p>

        </div>

    </div>

</section>


<footer>

    <p>
        &copy; <?php echo date("Y"); ?> JobConnect.
        All Rights Reserved.
    </p>

</footer>

</body>

</html>
