<?php 
session_start();
include_once('../includes/config.php');

if (strlen($_SESSION['adminid']) == 0) {
    header('location:logout.php');
    exit;
}

// Code for Updation 
if (isset($_POST['update'])) {
    $fname = htmlspecialchars($_POST['fname'], ENT_QUOTES, 'UTF-8');
    $lname = htmlspecialchars($_POST['lname'], ENT_QUOTES, 'UTF-8');
    $contact = htmlspecialchars($_POST['contact'], ENT_QUOTES, 'UTF-8');
    $userid = intval($_GET['uid']);

    $stmt = $pdo->prepare("UPDATE users SET fname = :fname, lname = :lname, contactno = :contact WHERE id = :id");
    $stmt->execute(['fname' => $fname, 'lname' => $lname, 'contact' => $contact, 'id' => $userid]);

    if ($stmt->rowCount()) {
        echo "<script>alert('Profile updated successfully');</script>";
        echo "<script type='text/javascript'> document.location = 'manage-users.php'; </script>";
    } else {
        echo "<script>alert('No changes were made.');</script>";
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
    <title>Edit Profile | Registration and Login System</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php include_once('includes/navbar.php'); ?>
    <div id="layoutSidenav">
        <?php include_once('includes/sidebar.php'); ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <?php 
                    $userid = intval($_GET['uid']);
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
                    $stmt->execute(['id' => $userid]);
                    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <h1 class="mt-4"><?php echo htmlspecialchars($result['fname']); ?>'s Profile</h1>
                    <div class="card mb-4">
                        <form method="post">
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>First Name</th>
                                        <td><input class="form-control" id="fname" name="fname" type="text" value="<?php echo htmlspecialchars($result['fname']); ?>" required /></td>
                                    </tr>
                                    <tr>
                                        <th>Last Name</th>
                                        <td><input class="form-control" id="lname" name="lname" type="text" value="<?php echo htmlspecialchars($result['lname']); ?>" required /></td>
                                    </tr>
                                    <tr>
                                        <th>Contact No.</th>
                                        <td colspan="3"><input class="form-control" id="contact" name="contact" type="text" value="<?php echo htmlspecialchars($result['contactno']); ?>" pattern="[0-9]{10}" title="10 numeric characters only" maxlength="10" required /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td colspan="3"><?php echo htmlspecialchars($result['email']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Reg. Date</th>
                                        <td colspan="3"><?php echo htmlspecialchars($result['posting_date']); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="text-align:center;"><button type="submit" class="btn btn-primary btn-block" name="update">Update</button></td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                    </div>
                    <?php } ?>
                </div>
            </main>
            <?php include('../includes/footer.php'); ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="../js/datatables-simple-demo.js"></script>
</body>
</html>

