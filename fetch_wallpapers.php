<?php
// fetch_wallpapers.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wallpaper";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch wallpapers along with their tags from the database
$sql = "SELECT
            w.id AS wallpaper_id,
            w.title AS title,
            w.image_path AS image_path,
            w.image_size AS image_size,
            w.image_dimension AS image_dimension,
            w.alt_text AS alt_text,
            w.author AS author,
            w.download_count AS download_count,
            w.view_count AS view_count,
            w.created_at AS created_at,
            GROUP_CONCAT(t.name SEPARATOR ', ') AS tags
        FROM
            Wallpapers w
        LEFT JOIN
            Wallpaper_Tags wt ON w.id = wt.wallpaper_id
        LEFT JOIN
            Tags t ON wt.tag_id = t.id
        GROUP BY
            w.id";
$result = $conn->query($sql);

$wallpapers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $wallpapers[] = $row;
    }
}

$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($wallpapers);
?>
