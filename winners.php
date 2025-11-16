<?php
// winners.php - election winners
require_once 'config.php';

//calculate winners
$winners = array();
$positions = $conn->query("SELECT * FROM Positions WHERE posStat='open'");
while($posRow = $positions->fetch_assoc()) {
    $candidates = $conn->query("SELECT * FROM Candidates WHERE posID=".$posRow["posID"]." AND candStat='active'");
    $maxVotes = 0;
    $winnerCandID = null;

    while($candRow = $candidates->fetch_assoc()) {
        $totalVotes = $conn->query("SELECT COUNT(*) as count FROM Votes WHERE candID".$candRow["candID"])->fetch_assoc()["count"];

        if ($totalVotes > $maxVotes) {
            $maxVotes = $totalVotes;
            $winnerCandID = $candRow["candID"];
        }
    }

    if ($winnerCandID) {
        $winnerName = $conn->query("SELECT candFName, candMName, candLName FROM Candidates WHERE candID=$winnerCandID")->fetch_assoc();
        $winner[] = array(
            "position" => $posRow["posName"],
            "winner" => $winnerName["candFName"]." ".$winnerName["candMName"]." ".$winnerName["candLName"],
            "votes" => $macVotes
        );
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Election Winners</title>
</head>
<body>
    <button><a href="index.php">Back</a></button>
    <h2>Election Winners</h2>

    <table border="1">
        <tr>
            <th>Elective Position</th>
            <th>Winner</th>
            <th>Total Votes</th>
        </tr>
        <?php foreach($winners as $winner): ?>
            <tr>
                <td><?= $winner["position"] ?></td>
                <td><?= $winner["winner"] ?></td>
                <td><?= $winner["votes"] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>