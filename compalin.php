<?php
// Database Configuration - UPDATE THESE WITH YOUR CREDENTIALS
$host = 'localhost';
$dbname = 'complaint_system';
$username = 'root';  // e.g., 'root' for XAMPP
$password = '';      // e.g., empty for XAMPP

// Initialize variables
$show_success = false;
$new_complaint_id = '';
$error_message = '';
$complaints = [];
$modal_data = null;
$show_modal = false;
$modal_mode = 'view';  // 'view' or 'edit'

// PDO Connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error_message = "Connection failed: " . $e->getMessage();
}

// Enable error reporting (for testing - remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle Insert (Submit Complaint)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_complaint'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = trim($_POST['priority'] ?? 'medium');

    if (!empty($name) && !empty($email) && !empty($category) && !empty($subject) && !empty($description)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO complaints (name, email, phone, category, subject, description, priority, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$name, $email, $phone, $category, $subject, $description, $priority]);
            $new_complaint_id = $pdo->lastInsertId();
            $show_success = true;
        } catch (PDOException $e) {
            $error_message = "Error submitting complaint: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill all required fields.";
    }
}

// Handle Search/Read (Track Complaints)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_complaint'])) {
    $search_id = trim($_POST['complaint_id'] ?? '');
    if (!empty($search_id)) {
        $stmt = $pdo->prepare("SELECT * FROM complaints WHERE id = ? ORDER BY date_submitted DESC");
        $stmt->execute([$search_id]);
        $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->query("SELECT * FROM complaints ORDER BY date_submitted DESC LIMIT 50");
        $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} elseif (isset($_GET['search_id'])) {
    $search_id = trim($_GET['search_id']);
    $stmt = $pdo->prepare("SELECT * FROM complaints WHERE id = ? ORDER BY date_submitted DESC");
    $stmt->execute([$search_id]);
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->query("SELECT * FROM complaints ORDER BY date_submitted DESC LIMIT 10");
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle View/Edit Modal
if (isset($_GET['view_id']) || isset($_GET['edit_id'])) {
    $id = (int)($_GET['view_id'] ?? $_GET['edit_id']);
    $stmt = $pdo->prepare("SELECT * FROM complaints WHERE id = ?");
    $stmt->execute([$id]);
    $modal_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($modal_data) {
        $show_modal = true;
        $modal_mode = isset($_GET['edit_id']) ? 'edit' : 'view';
    }
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_complaint'])) {
    $id = (int)$_POST['id'];
    $status = trim($_POST['status'] ?? 'pending');
    $description = trim($_POST['description'] ?? '');

    try {
        $stmt = $pdo->prepare("UPDATE complaints SET status = ?, description = ? WHERE id = ?");
        $stmt->execute([$status, $description, $id]);
        header("Location: index.php?success=updated");
        exit;
    } catch (PDOException $e) {
        $error_message = "Error updating: " . $e->getMessage();
    }
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_complaint'])) {
    $id = (int)$_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM complaints WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: index.php?success=deleted");
        exit;
    } catch (PDOException $e) {
        $error_message = "Error deleting: " . $e->getMessage();
    }
}

// Show error if any
if (!empty($error_message)) {
    echo "<script>alert('" . htmlspecialchars($error_message) . "');</script>";
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
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <h1 class="text-2xl font-bold">Complaint Management System</h1>
                    </div>
                    <nav class="hidden md:flex space-x-6">
                        <a href="index.php" class="font-medium hover:text-blue-200 transition" id="home-link">Home</a>
                        <a href="#" class="font-medium hover:text-blue-200 transition" id="submit-link">Submit Complaint</a>
                        <a href="#" class="font-medium hover:text-blue-200 transition" id="track-link">Track Complaints</a>
                        <a href="#" class="font-medium hover:text-blue-200 transition">FAQ</a>
                        <a href="#" class="font-medium hover:text-blue-200 transition">Contact</a>
                    </nav>
                    <button class="md:hidden focus:outline-none" id="mobile-menu-button">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                <div class="md:hidden hidden mt-4 space-y-2" id="mobile-menu">
                    <a href="index.php" class="block py-2 hover:bg-blue-700 px-3 rounded" id="home-link-mobile">Home</a>
                    <a href="#" class="block py-2 hover:bg-blue-700 px-3 rounded" id="submit-link-mobile">Submit Complaint</a>
                    <a href="#" class="block py-2 hover:bg-blue-700 px-3 rounded" id="track-link-mobile">Track Complaints</a>
                    <a href="#" class="block py-2 hover:bg-blue-700 px-3 rounded">FAQ</a>
                    <a href="#" class="block py-2 hover:bg-blue-700 px-3 rounded">Contact</a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-4 py-8">
            <!-- Home Section -->
            <section id="home-section" class="space-y-8">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="md:flex">
                        <div class="md:w-1/2 p-8 flex flex-col justify-center">
                            <h2 class="text-3xl font-bold text-gray-800 mb-4">Welcome to our Complaint Management System</h2>
                            <p class="text-gray-600 mb-6">We're committed to addressing your concerns promptly and efficiently. Submit your complaint and track its progress through our easy-to-use system.</p>
                            <div class="flex space-x-4">
                                <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition" id="get-started-btn">
                                    Submit a Complaint
                                </button>
                                <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-6 rounded-lg transition" id="track-btn">
                                    Track Your Complaint
                                </button>
                            </div>
                        </div>
                        <div class="md:w-1/2 bg-gradient-to-br from-blue-500 to-indigo-600 p-8 text-white">
                            <div class="flex items-center mb-6">
                                <div class="rounded-full bg-white p-3 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg">Easy Submission</h3>
                                    <p class="text-blue-100">Submit your complaints in minutes</p>
                                </div>
                            </div>
                            <div class="flex items-center mb-6">
                                <div class="rounded-full bg-white p-3 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg">Real-time Tracking</h3>
                                    <p class="text-blue-100">Monitor the status of your complaint</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="rounded-full bg-white p-3 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg">Quick Resolution</h3>
                                    <p class="text-blue-100">Get timely solutions to your issues</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-blue-500">
                        <div class="text-blue-500 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Submit</h3>
                        <p class="text-gray-600">Fill out our simple complaint form with all relevant details about your issue.</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-indigo-500">
                        <div class="text-indigo-500 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Track</h3>
                        <p class="text-gray-600">Monitor the progress of your complaint with our real-time tracking system.</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-purple-500">
                        <div class="text-purple-500 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Resolve