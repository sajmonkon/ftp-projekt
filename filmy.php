<?php
session_start();
if (!isset($_SESSION['uzivatel_id'])) {
    header("Location: index.php");
    exit();
}

$host = 'localhost';
$db_name = 'konecnys_filmy';
$username = 'konecnys';
$password = 'Simon2006';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$stmt = $conn->prepare("SELECT * FROM filmy LIMIT 50");
$stmt->execute();
$filmy = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmová databáze</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .movie-table {
            max-height: 600px;
            overflow-y: auto;
            display: block;
        }
        .movie-table table {
            width: 100%;
        }
        .movie-table th, .movie-table td {
            text-align: left;
        }
        .movie-table td {
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Seznam filmů</h1>
        
        <!-- Scrollable table for films -->
        <div class="movie-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Název</th>
                        <th>Popis</th>
                        <th>Rok vydání</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filmy as $film): ?>
                    <tr>
                        <td><?= htmlspecialchars($film['nazev']) ?></td>
                        <td><?= htmlspecialchars($film['popis']) ?></td>
                        <td><?= htmlspecialchars($film['rok_vydani']) ?></td>
                        <td>
                            <form method="POST" action="playlists.php">
                                <input type="hidden" name="film_id" value="<?= $film['id'] ?>">
                                <button type="submit" class="btn btn-primary">Přidat do oblíbených</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Button to view favorite films -->
        <a href="playlists.php" class="btn btn-info mt-3">Zobrazit oblíbené filmy</a>
        
        <a href="odhlasit.php?action=logout" class="btn btn-danger mt-3">Odhlásit se</a>
    </div>
</body>
</html>
