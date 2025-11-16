<?php
// positions.php - CRUD for Positions table
require_once 'config.php';

// Check if editing
$edit_mode = false;
$edit_position = null;

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit'];
    
    // Fetch the position's data
    $result = $conn->query("SELECT * FROM Positions WHERE posID=$edit_id");
    $edit_position = $result->fetch_assoc();
}

// Handle Add Position
if (isset($_POST['add'])) {
    $posName = $_POST['posName'];
    $numOfPositions = $_POST['numOfPositions'];
    $posStat = $_POST['posStat'];
    
    $conn->query("INSERT INTO Positions (posName, numOfPositions, posStat) VALUES ('$posName', '$numOfPositions', '$posStat')");
}

// Handle Edit Position
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $posName = $_POST['posName'];
    $numOfPositions = $_POST['numOfPositions'];
    $posStat = $_POST['posStat'];
    
    $conn->query("UPDATE Positions SET posName='$posName', numOfPositions='$numOfPositions', posStat='$posStat' WHERE posID=$id");
}

// Handle Deactivate Position
if (isset($_GET['deactivate'])) {
    $id = $_GET['deactivate'];
    $conn->query("UPDATE Positions SET posStat='closed' WHERE posID=$id");
}

// Fetch all positions
$positions = $conn->query("SELECT * FROM Positions");
?>

<?php if ($edit_mode && $edit_position): ?>
<!-- Edit Position Form -->
<h2>Edit Position</h2>
<form method="post">
    <input type="hidden" name="id" value="<?= $edit_position['posID'] ?>">
    Position Name: <input type="text" name="posName" value="<?= $edit_position['posName'] ?>" required><br>
    Number of Positions: <input type="number" name="numOfPositions" value="<?= $edit_position['numOfPositions'] ?>" required><br>
    Status: 
    <select name="posStat">
        <option value="open" <?= $edit_position['posStat'] == 'open' ? 'selected' : '' ?>>Open</option>
        <option value="closed" <?= $edit_position['posStat'] == 'closed' ? 'selected' : '' ?>>Closed</option>
    </select><br>
    <button type="submit" name="edit">Update Position</button>
    <a href="positions.php">Cancel</a>
</form>
<?php else: ?>
<!-- Add Position Form -->
<button><a href="index.php">Back</a></button>
<h2>Positions Management</h2>
<form method="post">
    <input type="hidden" name="id" value="">
    Position Name: <input type="text" name="posName" required><br>
    Number of Positions: <input type="number" name="numOfPositions" required><br>
    Status: 
    <select name="posStat">
        <option value="open">Open</option>
        <option value="closed">Closed</option>
    </select><br>
    <button type="submit" name="add">Add Position</button>
</form>
<?php endif; ?>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Position Name</th>
        <th>Number of Positions</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $positions->fetch_assoc()): ?>
    <tr>
        <td><?= $row['posID'] ?></td>
        <td><?= $row['posName'] ?></td>
        <td><?= $row['numOfPositions'] ?></td>
        <td><?= $row['posStat'] ?></td>
        <td>
            <a href="?edit=<?= $row['posID'] ?>">Edit</a>
            <?php if ($row['posStat'] == 'open'): ?>
                <a href="?deactivate=<?= $row['posID'] ?>">Deactivate</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>