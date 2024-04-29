<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wallpaper";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch popular tags
$sql = "SELECT t.id, t.name, COUNT(wt.wallpaper_id) AS tag_count
FROM tags t
LEFT JOIN wallpaper_tags wt ON t.id = wt.tag_id
GROUP BY t.id, t.name
ORDER BY tag_count DESC
LIMIT 5;
"; // Limit to top 5 popular tags
$result = $conn->query($sql);

$popular_tags = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $popular_tags[] = $row;
    }
}

// Close connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($popular_tags);
?>
