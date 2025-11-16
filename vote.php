<?php
// vote.php - Voting System
require_once 'config.php';

session_start();

if (!isset($_SESSION['voterID'])) {
    // Login form
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
        $voterID = $_POST['voterID'];
        $voterPass = $_POST['voterPass'];
        
        $sql = "SELECT * FROM Voters WHERE voterID='$voterID' AND voterStat='active' AND voted='n'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($voterPass, $row['voterPass'])) {
                $_SESSION['voterID'] = $voterID;
                echo "<script>window.location.href='vote.php';</script>";
            }
        }
    }
} else {
    // Voting interface
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_vote'])) {
        $voterID = $_SESSION['voterID'];
        $votes = $_POST['votes'];
        
        // Insert votes with ignore duplicates
        foreach ($votes as $posID => $candID) {
            if ($candID != '') {
                $sql = "INSERT IGNORE INTO Votes (posID, voterID, candID) VALUES ('$posID', '$voterID', '$candID')";
                $conn->query($sql);
            }
        }
        
        // Mark voter as voted
        $sql = "UPDATE Voters SET voted='y' WHERE voterID='$voterID'";
        $conn->query($sql);
        
        echo "Vote submitted successfully!";
        echo "<script>window.location.href='results.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voting System</title>
</head>
<body>
    <?php if (!isset($_SESSION['voterID'])): ?>
        <button><a href="index.php">Back</a></button>
        <h2>Login to Vote</h2>
        <form method="post">
            <input type="text" name="voterID" placeholder="Voter ID" required><br>
            <input type="password" name="voterPass" placeholder="Password" required><br>
            <input type="submit" name="login" value="Login">
        </form>
    <?php else: ?>
        <h2>Voting Interface</h2>
        <p>Welcome, <?php echo $_SESSION['voterID']; ?>! Please select your candidates.</p>
        
        <form method="post">
            <?php
            $positions = $conn->query("SELECT * FROM Positions WHERE posStat='open'");
            while($posRow = $positions->fetch_assoc()) {
                echo "<h3>".$posRow["posName"]."</h3>";
                echo "<p>Available candidates:</p>";
                $candidates = $conn->query("SELECT * FROM Candidates WHERE posID=".$posRow["posID"]." AND candStat='active'");
                echo "<select name='votes[".$posRow["posID"]."]'>";
                echo "<option value=''>-- Select Candidate --</option>";
                while($candRow = $candidates->fetch_assoc()) {
                    echo "<option value='".$candRow["candID"]."'>".$candRow["candFName"]." ".$candRow["candMName"]." ".$candRow["candLName"]."</option>";
                }
                echo "</select><br><br>";
            }
            ?>
            <input type="submit" name="submit_vote" value="Submit Vote">
        </form>
    <?php endif; ?>
</body>
</html>