// Function to fetch wallpapers from the server
function fetchWallpapers() {
    fetch('fetch_wallpapers.php')
        .then(response => response.json())
        .then(data => {
            // Call function to render wallpapers on the webpage
            renderWallpapers(data);
            
            // Initialize Masonry after rendering wallpapers
            var grid = document.querySelector('#wallpapersContainer');
            var masonry = new Masonry(grid, {
                itemSelector: '.wallpaper',
                columnWidth: '.wallpaper',
                fitWidth: true
            });
        })
        .catch(error => console.error('Error fetching wallpapers:', error));
}

// Function to render wallpapers on the webpage
function renderWallpapers(data) {
    const wallpapersContainer = document.getElementById('wallpapersContainer');
    wallpapersContainer.innerHTML = ''; // Clear previous content

    // Loop through each wallpaper data
    data.forEach(wallpaper => {
        // Create anchor element for each wallpaper
        const wallpaperLink = document.createElement('a');
        wallpaperLink.href = `image_details.html?id=${wallpaper.wallpaper_id}`; // Pass wallpaper ID as query parameter
        wallpaperLink.classList.add('wallpaper-link');

        // Create div to hold wallpaper details
        const wallpaperDiv = document.createElement('div');
        wallpaperDiv.classList.add('wallpaper');

        // Create image element
        const img = document.createElement('img');
        img.src = wallpaper.image_path;
        img.alt = wallpaper.alt_text;

        // Create tags element
        const tags = document.createElement('p');
        tags.textContent = 'Tags: ' + wallpaper.tags;

        // Append elements to wallpaper div
        wallpaperDiv.appendChild(img);
        wallpaperDiv.appendChild(tags);

        // Append wallpaper div to anchor element
        wallpaperLink.appendChild(wallpaperDiv);

        // Append anchor element to wallpapers container
        wallpapersContainer.appendChild(wallpaperLink);
    });
}

// Call fetchWallpapers function when the page loads
window.addEventListener('load', fetchWallpapers);
