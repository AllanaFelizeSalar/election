<?php
require_once 'config.php';

$results = [];
$totalVoters = $conn->query("SELECT COUNT(*) AS c FROM Voters WHERE voterStat='active' AND voted='y'")->fetch_assoc()['c'];

$pos = $conn->query("SELECT posID, posName FROM Positions WHERE posStat='open'");
while ($p = $pos->fetch_assoc()) {

    $results[$p['posID']] = [
        "position"   => $p['posName'],
        "candidates" => []
    ];

    $cands = $conn->query("SELECT * FROM Candidates WHERE posID={$p['posID']} AND candStat='active'");
    while ($c = $cands->fetch_assoc()) {

        $votes = $conn->query("SELECT COUNT(*) AS c FROM Votes WHERE candID={$c['candID']}")->fetch_assoc()['c'];
        $percent = $totalVoters ? round($votes / $totalVoters * 100, 2) : 0;

        $name = trim("{$c['candFName']} {$c['candMName']} {$c['candLName']}");

        $results[$p['posID']]["candidates"][$c['candID']] = [
            "name"       => $name,
            "votes"      => $votes,
            "percentage" => $percent
        ];
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
