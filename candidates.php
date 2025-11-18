<?php
include 'config.php';

// add
if (isset($_POST['add'])) {
    $candFName = $_POST['candFName'];
    $candMName = $_POST['candMName'];
    $candLName = $_POST['candLName'];
    $posID = $_POST['posID'];
    $candStat = $_POST['candStat'];
    $conn->query("INSERT INTO Candidates (candFName, candMName, candLName, posID, candStat) VALUES ('$candFName', '$candMName', '$candLName', '$posID', '$candStat')");
}

// edit
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $candFName = $_POST['candFName'];
    $candMName = $_POST['candMName'];
    $candLName = $_POST['candLName'];
    $posID = $_POST['posID'];
    $candStat = $_POST['candStat'];
    $conn->query("UPDATE Candidates SET candFName='$candFName', candMName='$candMName', candLName='$candLName', posID='$posID', candStat='$candStat' WHERE candID=$id");
}

$edit_mode = false;
$edit_candidate = null;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit'];
    $result = $conn->query("SELECT c.*, p.posName
                            FROM Candidates c
                            JOIN Positions p ON c.posID=p.posID
                            WHERE c.candID=$edit_id");
    $edit_candidate = $result->fetch_assoc();
}

if(isset($_GET['deactivate'])){
    $id = $_GET['deactivate'];
    $conn->query("UPDATE Candidates SET candStat='inactive' WHERE candID=$id");
}

$positions = $conn->query("SELECT * FROM Positions WHERE posStat='open'");

$candidates = $conn->query("SELECT c.*, p.posName
                            FROM Candidates c
                            JOIN Positions p ON c.posID=p.posID");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, inital-scale=1.0">
    <title>VOTING SYSTEM</title>
</head>
<body>
    <button><a href="index.php">Back</a></button>
    <h1>CANDIDATE MANAGEMENT</h1>

    <?php if ($edit_mode && $edit_candidate): ?>

        <!-- edit candidates -->
        <h3>EDIT CANDIDATE</h3>
        <form method="post">
            <input type="hidden" name="id" value="<?= $edit_candidate['candID'] ?>">

            FIRST NAME: 
            <input type="text" name="candFName" value="<?= $edit_candidate['candFName'] ?>" required><br>

            MIDDLE NAME: 
            <input type="text" name="candMName" value="<?= $edit_candidate['candMName'] ?>" required><br>

            LAST NAME: 
            <input type="text" name="candLName" value="<?= $edit_candidate['candLName'] ?>" required><br>

            POSITION:
            <select name="posID">
                <?php foreach ($positions as $p): ?>
                    <option value="<?= $p['posID'] ?>"><?= $p['posName'] ?></option>
                <?php endforeach; ?>
            </select><br>

            STATUS:
            <select name="candStat">
                <option value="active"  <?= $edit_candidate['candStat']=='active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $edit_candidate['candStat']=='inactive' ? 'selected' : '' ?>>Inactive</option>
            </select><br>

            <button type="submit" name="edit">Update</button>
            <a href="candidates.php">Cancel</a>
        </form>

    <?php else: ?>

        <!-- add candidates -->
        <h3>ADD CANDIDATE</h3>
        <form method="post">

            FIRST NAME: <input type="text" name="candFName" required><br>
            MIDDLE NAME: <input type="text" name="candMName" required><br>
            LAST NAME: <input type="text" name="candLName" required><br>

            POSITION:
            <select name="posID">
                <?php foreach ($positions as $p): ?>
                    <option value="<?= $p['posID'] ?>"><?= $p['posName'] ?></option>
                <?php endforeach; ?>
            </select><br>

            STATUS:
            <select name="candStat">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select><br>

            <button type="submit" name="add">Add</button>
        </form>

    <?php endif; ?>

    <br><br>

    <!-- table list -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>NAME</th>
            <th>POSITION</th>
            <th>STATUS</th>
            <th>ACTIONS</th>
        </tr>

        <?php foreach ($candidates as $row): ?>
            <tr>
                <td><?= $row['candID'] ?></td>
                <td><?= $row['candFName'] . " " . $row['candMName'] . " " . $row['candLName'] ?></td>
                <td><?= $row['posName'] ?></td>
                <td><?= $row['candStat'] ?></td>

                <td>
                    <a href="?edit=<?= $row['candID'] ?>">EDIT</a>
                    <?php if ($row['candStat'] == 'active'): ?>
                        <a href="?deactivate=<?= $row['candID'] ?>">DEACTIVATE</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>

</html>
