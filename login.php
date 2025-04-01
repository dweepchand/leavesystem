<?php
// Start the session at the very beginning
session_start();

require('db.inc.php');
$msg = "";

// Initialize CAPTCHA variable
$captcha_code = '';

if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['captcha'])) {
    // Store the CAPTCHA input from the form
    $captcha_input = $_POST['captcha'];

    // Validate CAPTCHA input
    if ($captcha_input == $_SESSION['captcha_code']) {
        // CAPTCHA verification passed, proceed with login validation
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $password = mysqli_real_escape_string($con, $_POST['password']);

        $res = mysqli_query($con, "SELECT * FROM employee WHERE email='$email' AND password='$password'");
        $count = mysqli_num_rows($res);

        if ($count > 0) {
            $row = mysqli_fetch_assoc($res);
            $_SESSION['ROLE'] = $row['role'];
            $_SESSION['USER_ID'] = $row['id'];
            $_SESSION['USER_NAME'] = $row['name'];
            header('location:index.php');
            die();
        } else {
            $msg = "Please enter correct login details";
        }
    } else {
        // CAPTCHA verification failed
        $msg = "CAPTCHA verification failed. Please try again.";
    }
}

// Generate a new CAPTCHA code
$_SESSION['captcha_code'] = rand(1000, 9999);
?>
<!doctype html>
<html class="no-js" lang="">
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/normalize.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/pe-icon-7-filled.css">
    <link rel="stylesheet" href="assets/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
</head>
<body class="bg-dark">
    <div class="sufee-login d-flex align-items-center justify-content-center flex-column">
        <div class="container text-center">
            <img src="CVSC_logo.png" alt="College logo" width="250" height="180" class="img-fluid">
            <h1 style="color:blue; margin-top:10px;">College of Veterinary Science</h1>
            <h2 style="color:blue; margin-top:10px;">Online Hostel Leave System</h2>
            <div class="login-content">
                <div class="login-form mt-4">
                    <form method="post">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label>CAPTCHA: <?php echo $_SESSION['captcha_code']; ?></label>
                            <input type="text" name="captcha" class="form-control" placeholder="Enter the CAPTCHA" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Sign in</button>
                        <div class="result_msg"><?php echo $msg?></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/vendor/jquery-2.1.4.min.js" type="text/javascript"></script>
    <script src="assets/js/popper.min.js" type="text/javascript"></script>
    <script src="assets/js/plugins.js" type="text/javascript"></script>
    <script src="assets/js/main.js" type="text/javascript"></script>
</body>
</html>
