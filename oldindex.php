<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "complaint_system";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$new_complaint_id = "";

if(isset($_POST['submit_complaint'])) {
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
  <link href="compalin.css" rel="stylesheet">
</head>
<body>
  <div class="min-h-screen flex flex-col">
    
    <header class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg">
      <div class="container mx-auto px-4 py-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Complaint Management System</h1>
        <nav class="space-x-6">
          <a href="#" id="home-link" class="hover:text-blue-200">Home</a>
          <a href="#" id="submit-link" class="hover:text-blue-200">Submit Complaint</a>
          <a href="demo-ad.html"class="hover:text-blue-200">admin </a>
          <a href="logout.php" class="hover:text-blue-200">logout</a>
        </nav>
      </div>
    </header>

   
    <main class="flex-grow container mx-auto px-4 py-8">

      <section id="home-section">
        <div class="bg-white p-8 rounded-xl shadow-md">
          <h2 class="text-3xl font-bold text-gray-800 mb-4">Welcome</h2>
          <p class="text-gray-600">Submit your complaint and track it easily.</p>
          <button id="get-started-btn" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg">Submit Complaint</button>
        </div>
      </section>

     
      <section id="submit-section" class="hidden mt-8">
        <div class="bg-white p-8 rounded-xl shadow-md">
          <h2 class="text-2xl font-bold mb-6">Submit a New Complaint</h2>
          <form id="complaint-form" method="post" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm">Full Name</label>
                <input type="text" name="name" required class="w-full border px-4 py-2 rounded">
              </div>
              <div>
                <label class="block text-sm">Email</label>
                <input type="email" name="email" required class="w-full border px-4 py-2 rounded">
              </div>
              <div>
                <label class="block text-sm">Phone</label>
                <input type="tel" name="phone" class="w-full border px-4 py-2 rounded">
              </div>
              <div>
                <label class="block text-sm">Category</label>
                <select name="category" required class="w-full border px-4 py-2 rounded">
                  <option value="">Select</option>
                  <option value="product">Product</option>
                  <option value="service">Service</option>
                  <option value="billing">Billing</option>
                  <option value="delivery">Delivery</option>
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
      </section>

    

    </main>

    <footer class="bg-gray-800 text-white py-6 text-center">
      <p>&copy; 2023 Complaint Management System</p>
    </footer>
  </div>

  <script src="Complain.js"></script>
</body>
</html>
