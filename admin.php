<?php
require_once 'db.php';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_judge'])) {
        $username = $_POST['username'];
        $display_name = $_POST['display_name'];
        
        $stmt = $pdo->prepare("INSERT INTO judges (username, display_name) VALUES (?, ?)");
        $stmt->execute([$username, $display_name]);
        $success_msg = "Judge added successfully!";
    }
    
    if (isset($_POST['add_participant'])) {
        $name = $_POST['participant_name'];
        
        $stmt = $pdo->prepare("INSERT INTO participants (name) VALUES (?)");
        $stmt->execute([$name]);
        $success_msg = "Participant added successfully!";
    }
}

// Get all judges and participants
$judges = $pdo->query("SELECT * FROM judges ORDER BY display_name")->fetchAll();
$participants = $pdo->query("SELECT * FROM participants ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Scoring App</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container { max-width: 800px; margin: 0 auto; text-align: left; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .admin-section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: green; font-weight: bold; margin: 10px 0; }
        .list-item { padding: 10px; border-bottom: 1px solid #eee; }
        .back-btn { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="admin-container">
        <a href="index.php" class="btn back-btn">← Back to Home</a>
        <h1>🔧 Admin Panel</h1>
        
        <?php if (isset($success_msg)): ?>
            <div class="success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <!-- Add Judge Section -->
        <div class="admin-section">
            <h2>Add New Judge</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Display Name:</label>
                    <input type="text" name="display_name" required>
                </div>
                <button type="submit" name="add_judge" class="btn">Add Judge</button>
            </form>
        </div>

        <!-- Add Participant Section -->
        <div class="admin-section">
            <h2>Add New Participant</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Participant Name:</label>
                    <input type="text" name="participant_name" required>
                </div>
                <button type="submit" name="add_participant" class="btn">Add Participant</button>
            </form>
        </div>

        <!-- Current Judges -->
        <div class="admin-section">
            <h2>Current Judges (<?php echo count($judges); ?>)</h2>
            <?php foreach ($judges as $judge): ?>
                <div class="list-item">
                    <strong><?php echo htmlspecialchars($judge['display_name']); ?></strong> 
                    (<?php echo htmlspecialchars($judge['username']); ?>)
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Current Participants -->
        <div class="admin-section">
            <h2>Current Participants (<?php echo count($participants); ?>)</h2>
            <?php foreach ($participants as $participant): ?>
                <div class="list-item">
                    <?php echo htmlspecialchars($participant['name']); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
