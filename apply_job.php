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
| Only Job Seekers can apply
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['role_id']) ||
    $_SESSION['role_id'] != 2
) {

    die("Only Job Seekers can apply for jobs.");

}


/*
|--------------------------------------------------------------------------
| Get job ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: jobs.php");

    exit;
}

$job_id = (int)$_GET['id'];

$user_id = (int)$_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| Get job
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
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


$message = "";


/*
|--------------------------------------------------------------------------
| Check previous application
|--------------------------------------------------------------------------
*/

$check = mysqli_prepare(
    $conn,
    "SELECT id
     FROM applications
     WHERE job_id = ?
     AND user_id = ?"
);

mysqli_stmt_bind_param(
    $check,
    "ii",
    $job_id,
    $user_id
);

mysqli_stmt_execute($check);

mysqli_stmt_store_result($check);


if (mysqli_stmt_num_rows($check) > 0) {

    $message =
        "You have already applied for this job.";

}


/*
|--------------------------------------------------------------------------
| Submit application
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    mysqli_stmt_num_rows($check) == 0
) {

    $cover_letter = trim(
        $_POST['cover_letter']
    );


    /*
    |--------------------------------------------------------------------------
    | Resume upload
    |--------------------------------------------------------------------------
    */

    $resume_name = "";


    if (
        isset($_FILES['resume']) &&
        $_FILES['resume']['error'] == UPLOAD_ERR_OK
    ) {

        $file_name = $_FILES['resume']['name'];

        $file_tmp = $_FILES['resume']['tmp_name'];

        $file_size = $_FILES['resume']['size'];

        $file_ext = strtolower(
            pathinfo(
                $file_name,
                PATHINFO_EXTENSION
            )
        );


        $allowed_extensions = [
            "pdf",
            "doc",
            "docx"
        ];


        if (!in_array(
            $file_ext,
            $allowed_extensions
        )) {

            $message =
                "Only PDF, DOC and DOCX files are allowed.";

        } elseif ($file_size > 5 * 1024 * 1024) {

            $message =
                "Resume size must be less than 5 MB.";

        } else {


            /*
            |--------------------------------------------------------------------------
            | Create uploads directory
            |--------------------------------------------------------------------------
            */

            $upload_directory = "uploads/";


            if (!is_dir($upload_directory)) {

                mkdir(
                    $upload_directory,
                    0777,
                    true
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Unique filename
            |--------------------------------------------------------------------------
            */

            $resume_name =
                time() .
                "_" .
                uniqid() .
                "." .
                $file_ext;


            $destination =
                $upload_directory .
                $resume_name;


            if (!move_uploaded_file(
                $file_tmp,
                $destination
            )) {

                $message =
                    "Failed to upload resume.";

            }

        }

    } else {

        $message =
            "Please upload your resume.";

    }


    /*
    |--------------------------------------------------------------------------
    | Insert application
    |--------------------------------------------------------------------------
    */

    if ($message == "") {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO applications
            (
                job_id,
                user_id,
                resume,
                cover_letter,
                status
            )
            VALUES (?, ?, ?, ?, 'Applied')"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iiss",
            $job_id,
            $user_id,
            $resume_name,
            $cover_letter
        );


        if (mysqli_stmt_execute($stmt)) {

            header(
                "Location: my_applications.php"
            );

            exit;

        } else {

            $message =
                "Application failed: " .
                mysqli_error($conn);

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

    <title>
        Apply - JobConnect
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
            My Applications
        </a>

        <a href="logout.php">
            Logout
        </a>

    </nav>

</header>


<div class="form-container">

    <h2>
        Apply for Job
    </h2>


    <h3>
        <?php echo htmlspecialchars($job['title']); ?>
    </h3>


    <p>

        <strong>
            Company:
        </strong>

        <?php echo htmlspecialchars($job['company']); ?>

    </p>


    <?php if ($message != ""): ?>

        <p class="error-message">

            <?php echo htmlspecialchars($message); ?>

        </p>

    <?php endif; ?>


    <?php if (
        strpos(
            $message,
            "already applied"
        ) === false
    ): ?>

        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <div class="form-group">

                <label>
                    Resume
                </label>

                <input
                    type="file"
                    name="resume"
                    accept=".pdf,.doc,.docx"
                    required
                >

                <small>
                    Maximum 5 MB.
                </small>

            </div>


            <div class="form-group">

                <label>
                    Cover Letter
                </label>

                <textarea
                    name="cover_letter"
                    placeholder="Write your cover letter"
                    required
                ></textarea>

            </div>


            <button
                type="submit"
                class="form-button"
            >
                Submit Application
            </button>

        </form>

    <?php endif; ?>

</div>

</body>

</html>
