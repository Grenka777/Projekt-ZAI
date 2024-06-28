<?php 
session_start();
include_once('includes/config.php');

// CSRF token initialization at the start of the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Protection against brute force attacks
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['locked_time'] = 0;
}

// Helper function to sanitize input and protect against XSS
function sanitize($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Check if the account is locked
$current_time = time();
if ($_SESSION['locked_time'] && ($current_time - $_SESSION['locked_time']) < 3600) {
    die('Your account is locked. Please try again in ' . (3600 - ($current_time - $_SESSION['locked_time'])) . ' seconds.');
}

// Login code
if (isset($_POST['login'])) {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    // reCAPTCHA handling
    $recaptcha_response = $_POST['g-recaptcha-response'];
    if (!$recaptcha_response) {
        die('reCAPTCHA response is missing');
    }

    $recaptcha_secret = '6LcQjtkpAAAAAK8BAqhJ5HOzPDSzmUItMThWH97E';  // Secret key
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response);
    $responseData = json_decode($response);

    if (!$responseData->success || $responseData->score < 0.5) {
        echo "<script>alert('reCAPTCHA verification failed, please try again.');</script>";
    } else {
        // Input Sanitization
        $useremail = sanitize($_POST['uemail']);
        $password = sanitize($_POST['password']);


        // SQL Injection Protection
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $useremail]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['fname'];
            $_SESSION['login_attempts'] = 0;  // Reset login attempts on successful login
            $_SESSION['locked_time'] = 0;  // Reset lock time
            log_event($user['id'], 'Successful login'); // log event successful login
            header("Location: welcome.php");
            exit();
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['last_login_attempt'] = time();
            if ($_SESSION['login_attempts'] > 3) {
                $_SESSION['locked_time'] = time();  // Set lock time

                log_event($user ? $user['id'] : 0, 'Account locked due to too many failed login attempts'); // log event locked

                echo "<script>alert('Too many failed login attempts. Your account has been locked for one hour.');</script>";
            } else {
                log_event($user ? $user['id'] : 0, 'Failed login'); // log event failed login
                echo "<script>alert('Invalid email or password');</script>";
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
    <title>User Login | Registration and Login System</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LcQjtkpAAAAAK3CglxVJzvd1AvdoEhqUcBtXeHv"></script> <!-- Site key -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var emailInput = document.getElementById('uemail');
            emailInput.addEventListener('input', function() {
                this.value = this.value.replace(/[<>"']/g, '');
            });
        });
    </script>
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
                                    <h3 class="text-center font-weight-light my-4">User Login</h3>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" name="uemail" type="email" placeholder="name@example.com" required/>
                                            <label for="inputEmail">Email address</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" name="password" type="password" placeholder="Password" required />
                                            <label for="inputPassword">Password</label>
                                        </div>
                                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="password-recovery.php">Forgot password?</a>
                                            <button class="btn btn-primary" name="login" type="submit">Login</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="signup.php">Need an account? Sign up!</a></div>
                                    <div class="small"><a href="index.php">Back to home page</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
<?php include('includes/footer.php'); ?>
    </div>
    <script>
       grecaptcha.ready(function() {
            grecaptcha.execute('6LcQjtkpAAAAAK3CglxVJzvd1AvdoEhqUcBtXeHv', {action: 'login'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
