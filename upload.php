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

// Image upload and database insertion
$title = $_POST['title'];
$tags = $_POST['tags'];
$author = $_POST['author']; // Add author field

// Generate a random string for uniqueness
$randomString = bin2hex(random_bytes(5)); // Generate 10 characters (5 bytes)

// Remove special characters and spaces from title and tags
$titleSlug = preg_replace('/[^a-zA-Z0-9]+/', '-', $title);
$tagsSlug = preg_replace('/[^a-zA-Z0-9]+/', '-', $tags);

// Concatenate title, tags, and random string to create filename
$filename = $titleSlug . '-' . $tagsSlug . '-' . $randomString;

$targetDir = "images/"; // Change to your desired directory
$uploadedFiles = [];
foreach ($_FILES['images']['name'] as $key => $image) {
    $targetFile = $targetDir . $filename . '_' . $key . '.' . pathinfo($image, PATHINFO_EXTENSION);

    if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFile)) {
        $uploadedFiles[] = $targetFile;

        // Get image details
        $dimensions = getimagesize($targetFile);
        $size = filesize($targetFile) / (1024 * 1024); // Convert bytes to megabytes

        // Generate alternative text based on available data
        $altText = "Download " . $title . " wallpaper with tags: " . $tags . ". Dimensions: " . $dimensions[0] . "x" . $dimensions[1];

        // Insert into database
        $sql = "INSERT INTO Wallpapers (title, image_path, image_size, image_dimension, alt_text, author, download_count, view_count)
                VALUES ('$title', '$targetFile', '$size MB', '$dimensions[0]x$dimensions[1]', '$altText', '$author', 0, 0)";
        if ($conn->query($sql) === TRUE) {
            // Retrieve the ID of the inserted wallpaper
            $wallpaperId = $conn->insert_id;
            
            // Split tags into an array
            $tagArray = explode(',', $tags);
            foreach ($tagArray as $tag) {
                $tag = trim($tag);
                // Check if tag exists in Tags table
                $tagQuery = "SELECT id FROM tags WHERE name = '$tag'";
                $tagResult = $conn->query($tagQuery);
                if ($tagResult->num_rows == 0) {
                    // Tag doesn't exist, insert it into Tags table
                    $insertTagQuery = "INSERT INTO tags (name) VALUES ('$tag')";
                    if ($conn->query($insertTagQuery) === TRUE) {
                        $tagId = $conn->insert_id;
                    } else {
                        echo "Error inserting tag: " . $conn->error;
                    }
                } else {
                    // Tag exists, retrieve its ID
                    $tagId = $tagResult->fetch_assoc()['id'];
                }
                // Establish relationship between wallpaper and tag
                $insertRelationQuery = "INSERT INTO wallpaper_tags (wallpaper_id, tag_id) VALUES ($wallpaperId, $tagId)";
                if ($conn->query($insertRelationQuery) !== TRUE) {
                    echo "Error inserting relation: " . $conn->error;
                }
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error; // Output SQL error for debugging
        }
    } else {
        echo "Error moving uploaded file"; // Output error moving file for debugging
    }
}

$conn->close();

// Response to client
$response = ['message' => 'Wallpapers uploaded successfully'];
echo json_encode($response);
?>
