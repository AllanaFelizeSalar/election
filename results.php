<?php
// result.php - election results
require_once 'config.php';

// calculate results
$results = array();
$positions = $conn->query("SELECT * FROM Positions WHERE posStat='open'");
while($posRow = $positions->fetch_assoc()) {
    $results[$posRow["posID"]] = array(
        "positon" => $posRow["posName"],
        "candidates" => array()
    );

    $candidates = $conn->query("SELECT * FROM Candidates WHERE posID=".$posRow["posID"]." AND candStat='active'");
    while($candRow = $candidates->fetch_assoc()) {
        $totalVotes = $conn->query("SELECT COUNT(*) as count FROM Votes WHERE candID=".$candRow["candID"])->fetch_assoc()["count"];
        $totalVoters = $conn->query("SELECT COUNT(*) as count FROM Voters WHERE voterStat='active' AND voted='y'")->fetch_assoc()["count"];
        
        $percentage = $totalVoters > 0 ? round(($totalVotes / $totalVoters) * 100, 2) : 0;
        
        $results[$posRow["posID"]]["candidates"][$candRow["candID"]] = array(
            "name" => $candRow["candFName"]." ".$candRow["candMName"]." ".$candRow["candLName"],
            "votes" => $totalVotes,
            "percentage" => $percentage
        );
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Election Results</title>
</head>
<body>
    <button><a href="index.php">Back</a></button>
    <h2>Election Results</h2>
    
    <?php foreach($results as $posID => $data): ?>
        <h3><?php echo $data["position"]; ?></h3>
        <table border="1">
            <tr>
                <th>Candidate</th>
                <th>Total Votes</th>
                <th>Voting %</th>
            </tr>
            <?php foreach($data["candidates"] as $candID => $candidate): ?>
                <tr>
                    <td><?= $candidate["name"] ?></td>
                    <td><?= $candidate["votes"] ?></td>
                    <td><?= $candidate["percentage"] ?>%</td>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
    <?php endforeach; ?>
</body>