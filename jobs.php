<?php

session_start();

require_once "db.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Jobs - JobConnect</title>

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

            <a href="register.php">
                Register
            </a>

        <?php endif; ?>

    </nav>

</header>


<div class="job-search-container">

    <h2>
        Find Jobs
    </h2>


    <div class="job-search-form">


        <label>
            Search Job
        </label>

        <input
            type="text"
            id="searchInput"
            placeholder="Search job title, company or category"
        >


        <label>
            Location
        </label>

        <input
            type="text"
            id="locationInput"
            placeholder="Enter location"
        >


        <label>
            Category
        </label>

        <select id="categoryInput">

            <option value="">
                All Categories
            </option>

            <option value="IT">
                IT
            </option>

            <option value="Software Development">
                Software Development
            </option>

            <option value="Web Development">
                Web Development
            </option>

            <option value="Marketing">
                Marketing
            </option>

            <option value="Finance">
                Finance
            </option>

            <option value="HR">
                HR
            </option>

        </select>

    </div>


    <div id="loadingMessage">
        Loading jobs...
    </div>


    <div id="jobsContainer"></div>

</div>


<footer>

    <p>
        &copy; <?php echo date("Y"); ?> JobConnect.
        All Rights Reserved.
    </p>

</footer>


<script src="js/script.js"></script>

</body>

</html>
