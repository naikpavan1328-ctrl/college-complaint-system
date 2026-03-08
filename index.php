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

$new_complaint_id = "";

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

if(isset($_POST['submit_complaint'])) {
    // Server-side: require login
    if (!$isLoggedIn) {
        header("Location: login.php");
        exit();
    }

    // Server-side: email must match session
    if (!isset($_SESSION['email']) || $_POST['email'] !== $_SESSION['email']) {
        echo "<script>alert('You can only submit a complaint using your registered (logged-in) email.');</script>";
        exit();
    }

    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $category = $conn->real_escape_string($_POST['category']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $description = $conn->real_escape_string($_POST['description']);
    $priority = $conn->real_escape_string($_POST['priority']);

    $sql = "INSERT INTO complaints (name, email, phone, category, subject, description, priority, status, date_submitted)
            VALUES ('$name', '$email', '$phone', '$category', '$subject', '$description', '$priority', 'pending', NOW())";

    if($conn->query($sql) === TRUE){
        $new_complaint_id = "CMP-" . $conn->insert_id;
        echo "<script>alert('Complaint submitted successfully! Your Complaint ID is $new_complaint_id');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complaint Management System</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-[Poppins]">
<div class="min-h-screen flex flex-col">

  <header class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg">
    <div class="container mx-auto px-4 py-6 flex justify-between items-center">
      <h1 class="text-2xl font-bold">Complaint Management System</h1>
      <nav class="space-x-6">
        <a href="#" id="home-link" class="hover:text-blue-200">Home</a>
        <a href="#" id="submit-link" class="hover:text-blue-200">Complaint here!</a>
        <a href="admin-login.php" class="hover:text-blue-200">Admin</a>
        <a href="logout.php" class="hover:text-blue-200">Logout</a>
      </nav>
    </div>
  </header>

  <main class="flex-grow container mx-auto px-4 py-8">
    <section id="home-section">
      <div class="bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Welcome</h2>
        <p class="text-gray-600">Submit your complaint and track it easily.</p>
        <button id="get-started-btn" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg">Complaint here!</button>
      </div>
    </section>

    <section id="submit-section" class="hidden mt-8">
      <?php if ($isLoggedIn): ?>
      <div class="bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6">Submit a New Complaint</h2>
        <form id="complaint-form" method="post" onsubmit="return validateForm()" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm">Full Name</label>
              <input type="text" name="name" required class="w-full border px-4 py-2 rounded">
            </div>
            <div>
              <label class="block text-sm">Email</label>
              <input type="email" name="email" id="email" required class="w-full border px-4 py-2 rounded" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
            </div>
            <div>
              <label class="block text-sm">Phone</label>
              <input type="tel" name="phone" id="phone" required maxlength="10" pattern="[0-9]{10}" class="w-full border px-4 py-2 rounded">
            </div>
            <div>
              <label class="block text-sm">Category</label>
              <select name="category" required class="w-full border px-4 py-2 rounded">
                <option value="">Select</option>
                <option value="product">Academic Issues</option>
                <option value="service">Events</option>
                <option value="billing">Infrasture & Facilities</option>
                <option value="delivery">Canteen</option>
                <option value="staff">Staff</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-sm">Subject</label>
            <input type="text" name="subject" required class="w-full border px-4 py-2 rounded">
          </div>
          <div>
            <label class="block text-sm">Description</label>
            <textarea name="description" rows="5" required class="w-full border px-4 py-2 rounded"></textarea>
          </div>
          <div>
            <label class="block text-sm">Priority</label>
            <div class="flex gap-4">
              <label><input type="radio" name="priority" value="low"> Low</label>
              <label><input type="radio" name="priority" value="medium" checked> Medium</label>
              <label><input type="radio" name="priority" value="high"> High</label>
            </div>
          </div>
          <button type="submit" name="submit_complaint" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Submit Complaint</button>
        </form>
      </div>
      <?php else: ?>
      <div class="bg-white p-8 rounded-xl shadow-md text-center">
        <h2 class="text-xl font-semibold mb-2">Please log in to submit a complaint</h2>
        <p class="text-gray-600 mb-4">You must be logged in with your registered email to submit a complaint.</p>
        <a href="login.php" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg">Go to Login</a>
      </div>
      <?php endif; ?>
    </section>
  </main>

  <footer class="bg-gray-800 text-white py-6 text-center">
    <p>&copy; 2023 Complaint Management System</p>
  </footer>
</div>

<script>
document.getElementById("get-started-btn").addEventListener("click", function() {
  <?php if (!$isLoggedIn): ?>
    window.location.href = "login.php"; 
  <?php else: ?>
    document.getElementById("home-section").classList.add("hidden");
    document.getElementById("submit-section").classList.remove("hidden");
  <?php endif; ?>
});

document.getElementById("submit-link").addEventListener("click", function(e) {
  e.preventDefault();
  <?php if (!$isLoggedIn): ?>
    window.location.href = "login.php"; 
  <?php else: ?>
    document.getElementById("home-section").classList.add("hidden");
    document.getElementById("submit-section").classList.remove("hidden");
  <?php endif; ?>
});

document.getElementById("home-link").addEventListener("click", function(e) {
  e.preventDefault();
  document.getElementById("submit-section").classList.add("hidden");
  document.getElementById("home-section").classList.remove("hidden");
});

// Modern email validation + phone check
function validateForm() {
  let email = document.getElementById("email").value.trim();
  let phone = document.getElementById("phone").value.trim();

  let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  if (!emailPattern.test(email)) {
    alert("Please enter a valid email address.");
    return false;
  }

  if (!/^\d{10}$/.test(phone)) {
    alert("Phone number must be exactly 10 digits.");
    return false;
  }

  return true;
}
</script>
</body>
</html>
