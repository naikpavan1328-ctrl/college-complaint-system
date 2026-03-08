<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors',1);

$host="localhost"; $user="root"; $pass=""; $dbname="complaint_system";
$conn=new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

// Create admins table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(50) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Default admin (plain text password)
$res=$conn->query("SELECT id FROM admins LIMIT 1");
if($res && $res->num_rows==0){
    $default_email="admin@example.com";
    $default_password="admin123"; // plain text
    $stmt=$conn->prepare("INSERT INTO admins (email,password) VALUES (?,?)");
    $stmt->bind_param('ss',$default_email,$default_password);
    $stmt->execute(); $stmt->close();
}

// Login handling
$errors=[];
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['login_submit'])){
    $email=trim($_POST['email']);
    $password=trim($_POST['password']);
    if(!$email || !$password) $errors[]="Email and password required.";
    else{
        $stmt=$conn->prepare("SELECT id,password FROM admins WHERE email=?");
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $res=$stmt->get_result();
        if($res && $res->num_rows==1){
            $admin=$res->fetch_assoc();
            // Plain text comparison
            if($password === $admin['password']){
                $_SESSION['admin_id']=$admin['id'];
                $_SESSION['admin_email']=$email;
                header("Location: admin-panel.php");
                exit();
            } else $errors[]="Incorrect password.";
        } else $errors[]="Admin not found.";
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f4f6fb;font-family:Arial,sans-serif;}
.card{border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.1);}
</style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center" style="height:100vh;">
<div class="card p-4" style="max-width:400px;width:100%;">
<h3 class="text-center mb-3">Admin Login</h3>
<?php if(!empty($errors)) echo '<div class="alert alert-danger">'.implode('<br>',$errors).'</div>'; ?>
<form method="post" autocomplete="off">
<div class="mb-3"><input type="email" name="email" class="form-control form-control-lg" placeholder="admin@example.com" required></div>
<div class="mb-3">
<div class="input-group">
<input type="password" name="password" id="loginPassword" class="form-control form-control-lg" placeholder="Password" required>
<span class="input-group-text" style="background:#f1f3f5;border:none;">
<input type="checkbox" id="showPwd"><small class="ms-2">Show</small>
</span>
</div>
</div>
<button type="submit" name="login_submit" class="btn btn-primary w-100 btn-lg">Login</button>
<div class="mt-3 text-muted text-center small">Default: admin@example.com / admin123</div>
</form>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
const pwd=document.getElementById('loginPassword');
const cb=document.getElementById('showPwd');
if(cb && pwd) cb.addEventListener('change',()=>{pwd.type=cb.checked?'text':'password';});
});
</script>
</body>
</html>
