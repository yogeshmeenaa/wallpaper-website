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

// Get tag ID from query parameter
// Get tag ID from query parameter
$tag_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch wallpapers associated with the tag
$sql = "SELECT w.id, w.image_path, w.alt_text
        FROM wallpapers w
        INNER JOIN wallpaper_tags wt ON w.id = wt.wallpaper_id
        WHERE wt.tag_id = ?
        ORDER BY w.id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tag_id);
$stmt->execute();
$result = $stmt->get_result();

$wallpapers = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $wallpapers[] = $row;
    }
}


// Close connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tag Details</title>
  <link rel="stylesheet" href="mainstyles.css">
</head>
<body>
<main>
        <nav class="navbar">
            <div class="logo">
                <a href="/" class="stename-1"><span class="site-name">Wall</span>fyre</a>
            </div>
            <ul class="menu">
                <li><a href="index.html">Home</a></li>
                <li><a href="category.html">Categories</a></li>
            </ul>
            <form class="search-form">
                <input type="text" placeholder="Search...">
                <button type="submit">Search</button>
            </form>
        </nav>
    
        <div class="tag-details">
        <h1>Wallpapers Associated with this Tag</h1>
        <div class="wallpapers-container">
            <?php foreach ($wallpapers as $wallpaper): ?>
                <div class="wallpaper">
                    <img src="<?php echo $wallpaper['image_path']; ?>" alt="<?php echo $wallpaper['alt_text']; ?>">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    </main>
   
</body>
</html>
