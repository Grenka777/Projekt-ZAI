<?php 
session_start();
include_once('../includes/config.php');

// CSRF Token Generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Brute Force Protection
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['locked_time'] = 0;
}

// Check if the account is locked
$current_time = time();
if ($_SESSION['locked_time'] && ($current_time - $_SESSION['locked_time']) < 3600) {
    die('Your account is locked. Please try again after ' . (3600 - ($current_time - $_SESSION['locked_time'])) . ' seconds.');
}

// Login Code
if (isset($_POST['login'])) {
    // CSRF Token Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    // reCAPTCHA Validation
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $recaptcha_secret = '6LeMuOApAAAAAHTiW_71k8G23izGpX_HAWoWjB7b';
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        echo "<script>alert('reCAPTCHA verification failed, please try again.');</script>";
    } else {
        // Sanitize form data
        $adminusername = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
        $password = $_POST['password'];

        // Use prepared statements with PDO
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
        $stmt->execute(['username' => $adminusername]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['login'] = $adminusername;
            $_SESSION['adminid'] = $admin['id'];
            $_SESSION['login_attempts'] = 0;  // Reset login attempts
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] > 3) {
                $_SESSION['locked_time'] = time();  // Set lock time
                echo "<script>alert('Too many failed login attempts. Your account has been locked for an hour.');</script>";
                exit;
            } else {
                echo "<script>alert('Invalid username or password');</script>";
                
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Admin Login | Registration and Login System</title>
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script> <!-- reCAPTCHA v2 script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h2 align="center">Registration and Login System</h2>
                                    <hr />
                                    <h3 class="text-center font-weight-light my-4">Admin Login</h3>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" name="username" type="text" placeholder="Username" required/>
                                            <label for="inputEmail">Username</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" name="password" type="password" placeholder="Password" required />
                                            <label for="inputPassword">Password</label>
                                        </div>
                                        <div class="g-recaptcha" data-sitekey="6LeMuOApAAAAAK-vh4ZnBM2r8DopUApTQA50z8TM"></div> <!-- reCAPTCHA widget -->
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="password-recovery.php">Forgot Password?</a>
                                            <button class="btn btn-primary" name="login" type="submit">Login</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="../index.php">Back to Home Page</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <?php include('../includes/footer.php');?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>
