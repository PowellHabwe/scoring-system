<?php
require_once 'db.php';

// Get participants with their total scores
$stmt = $pdo->query("
    SELECT 
        p.id,
        p.name,
        COALESCE(SUM(s.points), 0) as total_points,
        COUNT(s.id) as total_scores
    FROM participants p
    LEFT JOIN scores s ON p.id = s.participant_id
    GROUP BY p.id, p.name
    ORDER BY total_points DESC, p.name ASC
");
$leaderboard = $stmt->fetchAll();

// Get individual scores for detailed view
$detailed_scores = $pdo->query("
    SELECT 
        p.name as participant_name,
        j.display_name as judge_name,
        s.points
    FROM scores s
    JOIN participants p ON s.participant_id = p.id
    JOIN judges j ON s.judge_id = j.id
    ORDER BY p.name, j.display_name
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Scoreboard - Scoring App</title>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="30"> <!-- Auto refresh every 30 seconds -->
    <style>
        .scoreboard-container { max-width: 1000px; margin: 0 auto; }
        .scoreboard-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .scoreboard-table th, .scoreboard-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .scoreboard-table th { background: #007bff; color: white; font-weight: bold; }
        .rank-1 { background: #fff3cd; border-left: 5px solid #ffc107; }
        .rank-2 { background: #f8f9fa; border-left: 5px solid #6c757d; }
        .rank-3 { background: #fff; border-left: 5px solid #fd7e14; }
        .total-points { font-size: 1.2em; font-weight: bold; color: #007bff; }
        .refresh-info { text-align: center; color: #666; font-style: italic; margin: 10px 0; }
        .back-btn { margin-bottom: 20px; }
        .detailed-scores { margin-top: 40px; }
        .detailed-table { font-size: 0.9em; }
        .toggle-btn { margin: 20px 0; }
    </style>
    <script>
        function toggleDetailed() {
            var detailed = document.getElementById('detailed-scores');
            var btn = document.getElementById('toggle-btn');
            if (detailed.style.display === 'none') {
                detailed.style.display = 'block';
                btn.textContent = 'Hide Detailed Scores';
            } else {
                detailed.style.display = 'none';
                btn.textContent = 'Show Detailed Scores';
            }
        }
    </script>
</head>
<body>
    <div class="scoreboard-container">
        <a href="index.php" class="btn back-btn">← Back to Home</a>
        <h1>🏆 Live Scoreboard</h1>
        <div class="refresh-info">Page automatically refreshes every 30 seconds | Last updated: <?php echo date('Y-m-d H:i:s'); ?></div>
        
        <!-- Main Leaderboard -->
        <table class="scoreboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Participant</th>
                    <th>Total Points</th>
                    <th>Scores Received</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $rank = 1;
                $prev_points = null;
                $display_rank = 1;
                
                foreach ($leaderboard as $index => $participant): 
                    // Handle tied scores
                    if ($prev_points !== null && $participant['total_points'] < $prev_points) {
                        $display_rank = $rank;
                    }
                    
                    $row_class = '';
                    if ($display_rank == 1) $row_class = 'rank-1';
                    elseif ($display_rank == 2) $row_class = 'rank-2';
                    elseif ($display_rank == 3) $row_class = 'rank-3';
                ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td>
                            <?php if ($display_rank <= 3): ?>
                                <?php echo ['🥇', '🥈', '🥉'][$display_rank - 1]; ?>
                            <?php endif; ?>
                            #<?php echo $display_rank; ?>
                        </td>
                        <td><?php echo htmlspecialchars($participant['name']); ?></td>
                        <td class="total-points"><?php echo $participant['total_points']; ?></td>
                        <td><?php echo $participant['total_scores']; ?> judge(s)</td>
                    </tr>
                <?php 
                    $prev_points = $participant['total_points'];
                    $rank++;
                endforeach; 
                ?>
            </tbody>
        </table>

        <!-- Toggle Button for Detailed Scores -->
        <button id="toggle-btn" class="btn toggle-btn" onclick="toggleDetailed()">Show Detailed Scores</button>

        <!-- Detailed Scores Section -->
        <div id="detailed-scores" class="detailed-scores" style="display: none;">
            <h2>Detailed Scores by Judge</h2>
            <table class="scoreboard-table detailed-table">
                <thead>
                    <tr>
                        <th>Participant</th>
                        <th>Judge</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detailed_scores as $score): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($score['participant_name']); ?></td>
                            <td><?php echo htmlspecialchars($score['judge_name']); ?></td>
                            <td><?php echo $score['points']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
