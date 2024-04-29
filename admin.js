document.getElementById('uploadForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const fileInput = document.getElementById('fileInput');
    const titleInput = document.getElementById('titleInput');
    const tagsInput = document.getElementById('tagsInput');
    const authorInput = document.getElementById('authorInput'); // New input for author
    
    const formData = new FormData();
    formData.append('title', titleInput.value);
    formData.append('tags', tagsInput.value);
    formData.append('author', authorInput.value); // Append author to FormData
    
    for (let i = 0; i < fileInput.files.length; i++) {
        formData.append('images[]', fileInput.files[i]);
    }
    
    fetch('upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        // Optionally, clear the form fields
        titleInput.value = '';
        tagsInput.value = '';
        fileInput.value = '';
        authorInput.value = ''; // Clear author input field
    })
    .catch(error => console.error('Error:', error));
});
