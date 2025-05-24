<?php
require_once 'db.php';

// Handle score submission
if ($_POST && isset($_POST['submit_score'])) {
    $judge_id = $_POST['judge_id'];
    $participant_id = $_POST['participant_id'];
    $points = $_POST['points'];
    
    // Check if judge already scored this participant
    $existing = $pdo->prepare("SELECT id FROM scores WHERE judge_id = ? AND participant_id = ?");
    $existing->execute([$judge_id, $participant_id]);
    
    if ($existing->fetch()) {
        // Update existing score
        $stmt = $pdo->prepare("UPDATE scores SET points = ? WHERE judge_id = ? AND participant_id = ?");
        $stmt->execute([$points, $judge_id, $participant_id]);
        $message = "Score updated successfully!";
    } else {
        // Insert new score
        $stmt = $pdo->prepare("INSERT INTO scores (judge_id, participant_id, points) VALUES (?, ?, ?)");
        $stmt->execute([$judge_id, $participant_id, $points]);
        $message = "Score submitted successfully!";
    }
}

// Get all judges and participants
$judges = $pdo->query("SELECT * FROM judges ORDER BY display_name")->fetchAll();
$participants = $pdo->query("SELECT * FROM participants ORDER BY name")->fetchAll();

// Get current scores for selected judge
$selected_judge_id = $_GET['judge_id'] ?? null;
$current_scores = [];
if ($selected_judge_id) {
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, s.points 
        FROM participants p 
        LEFT JOIN scores s ON p.id = s.participant_id AND s.judge_id = ?
        ORDER BY p.name
    ");
    $stmt->execute([$selected_judge_id]);
    $current_scores = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Judge Portal - Scoring App</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .judge-container { max-width: 900px; margin: 0 auto; text-align: left; }
        .judge-section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .judge-select { padding: 10px; font-size: 16px; margin: 10px 0; width: 100%; }
        .participant-card { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .score-input { width: 80px; padding: 5px; margin: 0 10px; }
        .current-score { color: #666; font-style: italic; }
        .success { color: green; font-weight: bold; margin: 10px 0; }
        .back-btn { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="judge-container">
        <a href="index.php" class="btn back-btn">← Back to Home</a>
        <h1>⚖️ Judge Portal</h1>
        
        <?php if (isset($message)): ?>
            <div class="success"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Judge Selection -->
        <div class="judge-section">
            <h2>Select Judge</h2>
            <form method="GET">
                <select name="judge_id" class="judge-select" onchange="this.form.submit()">
                    <option value="">-- Select a Judge --</option>
                    <?php foreach ($judges as $judge): ?>
                        <option value="<?php echo $judge['id']; ?>" 
                                <?php echo ($selected_judge_id == $judge['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($judge['display_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if ($selected_judge_id && $current_scores): ?>
        <!-- Scoring Section -->
        <div class="judge-section">
            <h2>Score Participants</h2>
            <?php foreach ($current_scores as $participant): ?>
                <div class="participant-card">
                    <h3><?php echo htmlspecialchars($participant['name']); ?></h3>
                    <?php if ($participant['points'] !== null): ?>
                        <div class="current-score">Current Score: <?php echo $participant['points']; ?> points</div>
                    <?php endif; ?>
                    
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="judge_id" value="<?php echo $selected_judge_id; ?>">
                        <input type="hidden" name="participant_id" value="<?php echo $participant['id']; ?>">
                        
                        <label>Points (0-100):</label>
                        <input type="number" name="points" class="score-input" 
                               min="0" max="100" 
                               value="<?php echo $participant['points'] ?? ''; ?>" required>
                        <button type="submit" name="submit_score" class="btn">
                            <?php echo ($participant['points'] !== null) ? 'Update Score' : 'Submit Score'; ?>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
