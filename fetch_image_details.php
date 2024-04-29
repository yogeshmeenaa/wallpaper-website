<?php
// fetch_image_details.php

// Validate and sanitize input parameter
$wallpaperId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($wallpaperId <= 0) {
    // Invalid or missing wallpaper ID
    http_response_code(400); // Bad request
    echo json_encode(['error' => 'Invalid or missing wallpaper ID']);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wallpaper";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500); // Internal server error
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Fetch image details and increment download count
fetchImageDetails($conn, $wallpaperId);

// Function to fetch image details based on the wallpaper ID and increment download count
function fetchImageDetails($conn, $wallpaperId)
{
    // Fetch image details based on the provided wallpaper ID
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
            WHERE
                w.id = ?
            GROUP BY
                w.id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $wallpaperId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Increment download count in the database
        $updateSql = "UPDATE Wallpapers SET download_count = download_count + 1 WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("i", $wallpaperId);
        $updateStmt->execute();
        $updateStmt->close();

        // Construct JSON response
        $response = [
            'wallpaper_id' => $row['wallpaper_id'],
            'title' => $row['title'],
            'image_path' => $row['image_path'],
            'image_size' => $row['image_size'],
            'image_dimension' => $row['image_dimension'],
            'alt_text' => $row['alt_text'],
            'author' => $row['author'],
            'download_count' => $row['download_count'] + 1, // Incremented download count
            'view_count' => $row['view_count'],
            'created_at' => $row['created_at'],
            'tags' => $row['tags']
        ];

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        // Return error message if image is not found
        http_response_code(404); // Not found
        echo json_encode(['error' => 'Image not found']);
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
