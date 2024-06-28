<?php
session_start();
include_once('../includes/config.php');

if (strlen($_SESSION['adminid']) == 0) {
    header('location:logout.php');
    exit;
}

// CSRF Token Generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Helper function to sanitize input and protect against XSS
function sanitize($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Code for updating password
if (isset($_POST['update'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    $oldpassword = $_POST['currentpassword'];
    $newpassword = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);
    $adminid = $_SESSION['adminid'];

    $stmt = $pdo->prepare("SELECT password FROM admin WHERE id = :id");
    $stmt->execute(['id' => $adminid]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($oldpassword, $admin['password'])) {
        $stmt = $pdo->prepare("UPDATE admin SET password = :newpassword WHERE id = :id");
        $stmt->execute(['newpassword' => $newpassword, 'id' => $adminid]);
        
        if ($stmt->rowCount()) {
            echo "<script>alert('Password Changed Successfully !!');</script>";
            echo "<script type='text/javascript'> document.location = 'change-password.php'; </script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again');</script>";
        }
    } else {
        echo "<script>alert('Old Password does not match !!');</script>";
        echo "<script type='text/javascript'> document.location = 'change-password.php'; </script>";
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
    <title>Change Password | Registration and Login System</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script language="javascript" type="text/javascript">
    function valid() {
        if (document.changepassword.newpassword.value != document.changepassword.confirmpassword.value) {
            alert("Password and Confirm Password Field do not match !!");
            document.changepassword.confirmpassword.focus();
            return false;
        }
        return true;
    }
    </script>
</head>
<body class="sb-nav-fixed">
    <?php include_once('includes/navbar.php'); ?>
    <div id="layoutSidenav">
        <?php include_once('includes/sidebar.php'); ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Change Password</h1>
                    <div class="card mb-4">
                        <form method="post" name="changepassword" onSubmit="return valid();">
                            <input type="hidden" name="csrf_token" value="<?php echo sanitize($_SESSION['csrf_token']); ?>">
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Current Password</th>
                                        <td><input class="form-control" id="currentpassword" name="currentpassword" type="password" value="" required /></td>
                                    </tr>
                                    <tr>
                                        <th>New Password</th>
                                        <td><input class="form-control" id="newpassword" name="newpassword" type="password" value="" required /></td>
                                    </tr>
                                    <tr>
                                        <th>Confirm Password</th>
                                        <td colspan="3"><input class="form-control" id="confirmpassword" name="confirmpassword" type="password" required /></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="text-align:center;"><button type="submit" class="btn btn-primary btn-block" name="update">Change</button></td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
            <?php include('../includes/footer.php'); ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="../js/datatables-simple-demo.js"></script>
</body>
</html>

