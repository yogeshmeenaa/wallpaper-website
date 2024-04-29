// Function to fetch popular tags
function fetchPopularTags() {
    fetch('fetch_popular_tags.php')
        .then(response => response.json())
        .then(data => {
            renderPopularTags(data);
        })
        .catch(error => console.error('Error fetching popular tags:', error));
}

// Function to render popular tags on the webpage
// Function to render popular tags on the webpage
function renderPopularTags(tags) {
    const popularTagsContainer = document.getElementById('popularTags');
    popularTagsContainer.innerHTML = ''; // Clear previous content

    tags.forEach(tag => {
        const tagLink = document.createElement('a');
        tagLink.href = `tag_details.php?id=${tag.id}`; // Redirect to tagdetails.php with tag ID as query parameter
        tagLink.textContent = tag.name + ` (${tag.tag_count})`; // Display tag name and count
        popularTagsContainer.appendChild(tagLink);
    });
}

// Call fetchPopularTags function when the page loads
window.addEventListener('load', fetchPopularTags);

