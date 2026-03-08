<?php
session_start();

$host = "localhost";
$user = "root";      
$pass = "";          
$dbname = "complaint_system";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $dob = $_POST['dob'];
    $password = $_POST['pass'];
    $confirm = $_POST['confirm'];
    $mobile = $_POST['mobile'];
    $pin = $_POST['pin'];
    $address = trim($_POST['address']);

    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/", $password)) {
        $errors[] = "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }
    if (!preg_match("/^\d{10}$/", $mobile)) {
        $errors[] = "Mobile number must be exactly 10 digits.";
    }
    if (!preg_match("/^\d{6}$/", $pin)) {
        $errors[] = "Pin code must be exactly 6 digits.";
    }

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $errors[] = "Email already exists. Please use another.";
    }
    $check->close();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (email, dob, password, mobile, pin, address) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $email, $dob, $hashed_password, $mobile, $pin, $address);

        if ($stmt->execute()) {
            $success = "Registration successful! Redirecting to login...";
            header("refresh:2;url=login.php");
        } else {
            $errors[] = "Error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>User Registration - Complaint Management System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="custom-card">
  <h3>User Registration</h3>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success">
      <?= $success ?>
    </div>
  <?php endif; ?>

  <form onsubmit="return validateForm()" method="POST" action="">
    <div class="mb-3">
      <input type="email" name="email" id="email" class="form-control" placeholder="Email Address" required />
    </div>
    <div class="mb-3">
      <input type="date" name="dob" id="dob" class="form-control" required />
    </div>
    <div class="mb-3">
      <input type="password" name="pass" id="pass" class="form-control" placeholder="Password" required />
      <input type="checkbox" onclick="togglePassword('pass')"> Show Password
    </div>
    <div class="mb-3">
      <input type="password" name="confirm" id="confirm" class="form-control" placeholder="Confirm Password" required />
      <input type="checkbox" onclick="togglePassword('confirm')"> Show Password
    </div>
    <div class="mb-3">
      <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile Number" maxlength="10" oninput="allowOnlyNumbers(this)" required />
    </div>
    <div class="mb-3">
      <input type="text" name="pin" id="pin" class="form-control" placeholder="Pin Code" maxlength="6" oninput="allowOnlyNumbers(this)" required />
    </div>
    <div class="mb-3">
      <textarea name="address" id="address" class="form-control" rows="2" placeholder="Address" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary w-100">Register</button>
    <div class="mt-3 text-center">
      Already have an account? <a href="login.php" class="text-info">Login here</a>
    </div>
  </form>
</div>

<script>
function validateForm() {
  const pass = document.getElementById('pass').value;
  const confirm = document.getElementById('confirm').value;
  const mobile = document.getElementById('mobile').value;
  const pin = document.getElementById('pin').value;

  const passPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;

  if (!passPattern.test(pass)) {
    alert("Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.");
    return false;
  }

  if (pass !== confirm) {
    alert("Passwords do not match.");
    return false;
  }

  if (!/^\d{10}$/.test(mobile)) {
    alert("Mobile number must be exactly 10 digits.");
    return false;
  }

  if (!/^\d{6}$/.test(pin)) {
    alert("Pin code must be exactly 6 digits.");
    return false;
  }

  return true;
}

function allowOnlyNumbers(input) {
  input.value = input.value.replace(/[^0-9]/g, '');
}

function togglePassword(id) {
  const input = document.getElementById(id);
  input.type = input.type === "password" ? "text" : "password";
}
</script>
</body>
</html>
