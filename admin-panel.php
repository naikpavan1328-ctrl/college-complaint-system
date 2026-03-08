<?php
session_start();
if(!isset($_SESSION['admin_id'])){header("Location: admin-login.php"); exit();}
$host="localhost"; $user="root"; $pass=""; $dbname="complaint_system";
$conn=new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

// Logout
if(isset($_GET['logout'])){session_destroy(); header("Location: admin-login.php"); exit();}

// Resolve complaint
if(isset($_GET['resolve_id'])){
    $id=intval($_GET['resolve_id']);
    $stmt=$conn->prepare("UPDATE complaints SET status='Resolved' WHERE id=?");
    $stmt->bind_param('i',$id); $stmt->execute(); $stmt->close();
    header("Location: admin-panel.php"); exit();
}

// Set complaint to Pending
if(isset($_GET['pending_id'])){
    $id=intval($_GET['pending_id']);
    $stmt=$conn->prepare("UPDATE complaints SET status='Pending' WHERE id=?");
    $stmt->bind_param('i',$id); $stmt->execute(); $stmt->close();
    header("Location: admin-panel.php"); exit();
}

// View complaint details
$view_complaint=null; 
if(isset($_GET['view_id'])){
    $vid=intval($_GET['view_id']);
    // Removed is_read column update as it caused the error, let's assume it doesn't exist for now.
    // If you add an `is_read` column to your database, uncomment the next 3 lines:
    /*
    $stmt=$conn->prepare("UPDATE complaints SET is_read=1 WHERE id=?");
    $stmt->bind_param('i',$vid); $stmt->execute(); $stmt->close();
    */

    $stmt=$conn->prepare("SELECT * FROM complaints WHERE id=?");
    $stmt->bind_param('i',$vid); $stmt->execute();
    $res=$stmt->get_result();
    if($res && $res->num_rows>0) $view_complaint=$res->fetch_assoc();
    $stmt->close();
}

// Reply
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['reply_submit'])){
    $id=intval($_POST['complaint_id']);
    $reply=trim($_POST['reply_text']);
    $stmt=$conn->prepare("UPDATE complaints SET reply=?,status='Replied' WHERE id=?");
    $stmt->bind_param('si',$reply,$id); $stmt->execute(); $stmt->close();
    header("Location: admin-panel.php?view_id=$id"); exit();
}

// Delete complaint (Added functionality)
if(isset($_GET['delete_id'])){
    $id=intval($_GET['delete_id']);
    $stmt=$conn->prepare("DELETE FROM complaints WHERE id=?");
    $stmt->bind_param('i',$id); $stmt->execute(); $stmt->close();
    header("Location: admin-panel.php"); exit();
}


// --- Data Fetching and Filtering ---
$filter = isset($_GET['filter']) ? strtolower($_GET['filter']) : 'all';
$sql = "SELECT * FROM complaints";
if($filter != 'all'){
    $sql .= " WHERE LOWER(status) = '".$conn->real_escape_string($filter)."'";
}
$sql .= " ORDER BY date_submitted DESC";

$complaints=[]; 
$res=$conn->query($sql);
if($res) while($row=$res->fetch_assoc()) $complaints[]=$row;

// Fetch ALL complaints for accurate stats
$all_complaints_res = $conn->query("SELECT * FROM complaints");
$all_complaints = [];
if($all_complaints_res) while($row=$all_complaints_res->fetch_assoc()) $all_complaints[]=$row;


// Stats
$total = count($all_complaints);
$resolved = count(array_filter($all_complaints, fn($c) => strtolower($c['status']) == 'resolved'));
$pending = count(array_filter($all_complaints, fn($c) => strtolower($c['status']) == 'pending'));
$replied = count(array_filter($all_complaints, fn($c) => strtolower($c['status']) == 'replied'));
$new_today = count(array_filter($all_complaints, fn($c) => date('Y-m-d', strtotime($c['date_submitted'])) == date('Y-m-d')));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Panel | Complaint System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
/* --- Google-like Professional Styling --- */
:root {
    --primary-color: #1a73e8; /* Google Blue */
    --secondary-color: #fbbc05; /* Google Yellow */
    --success-color: #34a853; /* Google Green */
    --danger-color: #ea4335; /* Google Red */
    --bg-light: #f8f9fa;
    --card-shadow: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
}

body {
    background: var(--bg-light);
    font-family: 'Roboto', sans-serif;
    padding-top: 60px; /* Space for fixed header */
}

/* Header/Navbar */
.main-header {
    background: #fff;
    color: #3c4043; /* Dark Grey Text */
    padding: 0.5rem 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1030;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.main-header h2 {
    font-size: 1.5rem;
    font-weight: 500;
    color: var(--primary-color);
    margin-bottom: 0;
}

/* Sidebar */
.sidebar {
    width: 250px;
    background: #ffffff;
    padding-top: 1rem;
    position: fixed;
    top: 60px;
    height: calc(100vh - 60px);
    box-shadow: 2px 0 5px rgba(0,0,0,0.05);
}
.sidebar a {
    display: flex;
    align-items: center;
    color: #3c4043;
    padding: 12px 18px;
    text-decoration: none;
    font-size: 1rem;
    border-left: 5px solid transparent;
    transition: all 0.3s ease;
}
.sidebar a i {
    margin-right: 10px;
    width: 20px;
}
.sidebar a:hover {
    background: #e8f0fe;
    color: var(--primary-color);
}
.sidebar a.active {
    background: #e8f0fe;
    color: var(--primary-color);
    font-weight: 500;
    border-left-color: var(--primary-color);
}

/* Main Content */
main {
    margin-left: 250px; /* Space for sidebar */
    padding: 2rem;
}
@media (max-width: 992px) {
    .sidebar { width: 100%; height: auto; position: static; box-shadow: none; border-bottom: 1px solid #eee; }
    main { margin-left: 0; padding-top: 1rem; }
    .main-header { padding: 0.5rem 1rem; }
}

/* Stats Cards */
.card-stats .card {
    border: none;
    border-radius: 8px;
    box-shadow: var(--card-shadow);
    transition: transform 0.3s ease;
}
.card-stats .card:hover {
    transform: translateY(-3px);
}
.card-stats h4 {
    font-size: 2rem;
    font-weight: 500;
    margin-top: 0.5rem;
}
.stat-icon {
    font-size: 2.5rem;
    color: #fff;
    width: 60px;
    height: 60px;
    line-height: 60px;
    border-radius: 50%;
    margin-bottom: 10px;
}

/* Table and Detail Panel */
.complaint-table, .detail-panel {
    background: #fff;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: var(--card-shadow);
}
.table thead th {
    font-weight: 500;
    color: #5f6368;
}

/* Status Badges */
.status-badge {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    padding: 5px 10px;
    border-radius: 12px;
}
.status-pending { background-color: #fbbc05; color: #3c4043; } /* Yellow */
.status-replied { background-color: #4285f4; color: #fff; } /* Blue */
.status-resolved { background-color: #34a853; color: #fff; } /* Green */

/* Unread Row (Optional - only if 'is_read' column is added) */
.unread td {
    font-weight: 600;
    background-color: #f5f5f5 !important;
}

/* Action Buttons */
.btn-action {
    padding: 4px 8px;
    font-size: 0.85rem;
    margin-right: 5px;
    border-radius: 4px;
}
.btn-view { background: var(--primary-color); color: #fff; }
.btn-resolve { background: var(--success-color); color: #fff; }
.btn-pending { background: var(--secondary-color); color: #3c4043; }
.btn-delete { background: var(--danger-color); color: #fff; }
</style>
</head>
<body>

<header class="main-header d-flex justify-content-between align-items-center mb-3">
    <h2><i class="fas fa-tools me-2"></i>Admin Dashboard</h2>

    <div>
        <a href="admin-panel.php?logout=1" class="btn btn-outline-danger btn-sm me-2">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>

        <a href="index.php" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-home"></i> Home Page
        </a>
    </div>
</header>


<aside class="sidebar">
<nav>
<a href="admin-panel.php?filter=all" class="<?=($filter=='all'?'active':'')?>"><i class="fas fa-list-alt"></i> All Complaints</a>
<a href="admin-panel.php?filter=pending" class="<?=($filter=='pending'?'active':'')?>"><i class="fas fa-clock"></i> Pending (<?= $pending ?>)</a>
<a href="admin-panel.php?filter=replied" class="<?=($filter=='replied'?'active':'')?>"><i class="fas fa-reply"></i> Replied (<?= $replied ?>)</a>
<a href="admin-panel.php?filter=resolved" class="<?=($filter=='resolved'?'active':'')?>"><i class="fas fa-check-circle"></i> Resolved (<?= $resolved ?>)</a>
</nav>
</aside>

<main>
<div class="row mb-5 card-stats">
    <div class="col-md-12 col-lg-2 mb-3">
        <div class="card p-3 text-center bg-white">
            <div class="stat-icon mx-auto bg-primary"><i class="fas fa-layer-group"></i></div>
            <p class="mb-0 text-muted">Total</p>
            <h4 class="text-primary"><?= $total ?></h4>
        </div>
    </div>
    <div class="col-md-6 col-lg-2 mb-3">
        <div class="card p-3 text-center bg-white">
            <div class="stat-icon mx-auto" style="background-color: var(--danger-color);"><i class="fas fa-star"></i></div>
            <p class="mb-0 text-muted">New Today</p>
            <h4 style="color: var(--danger-color);"><?= $new_today ?></h4>
        </div>
    </div>
    <div class="col-md-6 col-lg-2 mb-3">
        <div class="card p-3 text-center bg-white">
            <div class="stat-icon mx-auto" style="background-color: #fbbc05;"><i class="fas fa-hourglass-half"></i></div>
            <p class="mb-0 text-muted">Pending</p>
            <h4 style="color: #fbbc05;"><?= $pending ?></h4>
        </div>
    </div>
    <div class="col-md-6 col-lg-2 mb-3">
        <div class="card p-3 text-center bg-white">
            <div class="stat-icon mx-auto" style="background-color: #4285f4;"><i class="fas fa-comments"></i></div>
            <p class="mb-0 text-muted">Replied</p>
            <h4 style="color: #4285f4;"><?= $replied ?></h4>
        </div>
    </div>
    <div class="col-md-6 col-lg-2 mb-3">
        <div class="card p-3 text-center bg-white">
            <div class="stat-icon mx-auto" style="background-color: var(--success-color);"><i class="fas fa-handshake"></i></div>
            <p class="mb-0 text-muted">Resolved</p>
            <h4 style="color: var(--success-color);"><?= $resolved ?></h4>
        </div>
    </div>
</div>

<div class="row">
<div class="col-lg-7">
    <div class="complaint-table">
        <h3><i class="fas fa-inbox me-2"></i><?= ucfirst($filter) ?> Complaints</h3>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
            <thead><tr>
                <th>ID</th><th>Name</th><th>Category</th><th>Priority</th><th>Date</th><th>Status</th><th>Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach($complaints as $c): ?>
            <tr class="">
            <td><?= "CMP-".$c['id'] ?></td>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($c['category']) ?></td>
            <td><span class="badge bg-secondary"><?= ucfirst($c['priority']??'medium') ?></span></td>
            <td><?= date('Y-m-d',strtotime($c['date_submitted'])) ?></td>
            <td><span class="status-badge status-<?=strtolower($c['status'])?>"><?= ucfirst($c['status']) ?></span></td>
            <td class="text-nowrap">
                <a class="btn-action btn-view" href="admin-panel.php?view_id=<?= $c['id'] ?>"><i class="fas fa-eye"></i> View</a>
                
                <?php if(strtolower($c['status'])=='pending'): ?>
                    <a class="btn-action btn-resolve" href="admin-panel.php?resolve_id=<?= $c['id'] ?>" onclick="return confirm('Mark as Resolved?')"><i class="fas fa-check"></i> Resolve</a>
                <?php elseif(strtolower($c['status'])=='resolved' || strtolower($c['status'])=='replied'): ?>
                    <a class="btn-action btn-pending" href="admin-panel.php?pending_id=<?= $c['id'] ?>" onclick="return confirm('Mark as Pending?')"><i class="fas fa-redo"></i> Pending</a>
                <?php endif; ?>
                
                <a class="btn-action btn-delete" href="admin-panel.php?delete_id=<?= $c['id'] ?>" onclick="return confirm('Are you sure you want to DELETE this complaint permanently?')"><i class="fas fa-trash"></i></a>
            </td>
            </tr>
            <?php endforeach; if(empty($complaints)) echo '<tr><td colspan="7" class="text-center p-4">No complaints found for this filter.</td></tr>'; ?>
            </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-lg-5 mt-4 mt-lg-0">
    <div class="detail-panel">
    <?php if($view_complaint): ?>
    
        <h5 class="mb-3 text-primary"><i class="fas fa-info-circle me-2"></i>Complaint Details (<?= "CMP-".$view_complaint['id'] ?>)</h5>
        
        <div class="card p-3 mb-3 border-0 bg-light">
            <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($view_complaint['name']) ?></p>
            <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($view_complaint['email']) ?></p>
            <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($view_complaint['phone']) ?></p>
            <p class="mb-1"><strong>Category:</strong> <?= htmlspecialchars($view_complaint['category']) ?></p>
            <p class="mb-1"><strong>Priority:</strong> <span class="badge bg-danger"><?= ucfirst(htmlspecialchars($view_complaint['priority']??'medium')) ?></span></p>
            <p class="mb-1"><strong>Date:</strong> <?= date('Y-m-d H:i',strtotime($view_complaint['date_submitted'])) ?></p>
            <p class="mb-0"><strong>Current Status:</strong> <span class="status-badge status-<?=strtolower($view_complaint['status'])?>"><?= ucfirst(htmlspecialchars($view_complaint['status'])) ?></span></p>
        </div>

        <h6 class="mt-4">Subject & Message:</h6>
        <p class="mb-1"><strong>Subject:</strong> <?= htmlspecialchars($view_complaint['subject']) ?></p>
        <div class="p-3 border rounded mb-3 bg-white">
            <?= nl2br(htmlspecialchars($view_complaint['description'])) ?>
        </div>
        
        <h6 class="mt-4">Admin Reply:</h6>
        <div class="p-3 border rounded mb-3 bg-white">
            <?= $view_complaint['reply'] ? nl2br(htmlspecialchars($view_complaint['reply'])) : '<em>No reply yet</em>' ?>
        </div>

        <?php if(strtolower($view_complaint['status'])!='resolved'): ?>
        <h6 class="mt-4">Send Reply</h6>
        <form method="post">
            <input type="hidden" name="complaint_id" value="<?= intval($view_complaint['id']) ?>">
            <div class="mb-2"><textarea name="reply_text" class="form-control" rows="4" placeholder="Type your reply here..." required><?= strtolower($view_complaint['status'])=='replied' ? htmlspecialchars($view_complaint['reply']) : '' ?></textarea></div>
            <button type="submit" name="reply_submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Reply / Update</button>
            <?php if(strtolower($view_complaint['status'])=='replied'): ?><small class="text-muted d-block mt-2">Replying again will update the existing reply and keep the 'Replied' status.</small><?php endif; ?>
        </form>
        <?php else: ?><div class="alert alert-success mt-4"><i class="fas fa-check-circle"></i> This complaint is marked as **Resolved**.</div><?php endif; ?>

    <?php else: ?><p class="text-center p-5 text-muted">Select a complaint from the list to view its details and reply.</p><?php endif; ?>
    </div>
</div>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>