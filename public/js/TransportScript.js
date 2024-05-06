
  // Get the modal
  var modal = document.getElementById('ratingModal');
  var rating = 0;
  var id;
  var stationId ;
  var userId=115;

  // Function to open the modal
  function openModal(station) {
    modal.style.display = 'block';
    stationId=station;
  }

  // Function to close the modal
  function closeModal() {
    modal.style.display = 'none';
  }

  // Function to set the rating
  function setRating(stars) {
    rating = stars;
    var starsElements = document.getElementsByClassName('star');
    for (var i = 0; i < starsElements.length; i++) {
      if (i < stars) {
        starsElements[i].classList.add('checked');
      } else {
        starsElements[i].classList.remove('checked');
      }
    }
  }

  // Function to submit the rating
 function submitRating(event) {
    var ratingValue = rating;

    // Check if rating value is valid
    if (!ratingValue || ratingValue < 1 || ratingValue > 5) {
        alert('Please select a valid rating.');
        return; // Exit the function early if rating is invalid
    }

    // Get rating value from the input field
    

    let formData = new FormData();
    formData.append('rating', ratingValue);
    formData.append('stationId', stationId);
    formData.append('userId', userId);
    console.log(formData);

    // Make AJAX request to Symfony controller endpoint
    $.ajax({
        url: '/rating/add',
        type: 'POST',
        data: formData,
        contentType: false, // Do not set content type
        processData: false, // Do not process data
        success: function(response) {
            // Rating submitted successfully
            alert('Rating submitted successfully!');
            closeModal(); // Close the modal or perform any other action
        },
        error: function(xhr, status, error) {
            // Error handling: Display an error message if the request fails
            alert('An error occurred while submitting the rating.');
        }
    });
}

 var currentPage = 1;
var rowsPerPage = 2; // Change this to the number of rows you want per page

function updateTable() {
    var tableBody = document.getElementById('transport-table-body');
    var rows = Array.from(tableBody.children);
    var totalPages = Math.ceil(rows.length / rowsPerPage);

    // Hide all rows
    rows.forEach(function(row) {
        row.style.display = 'none';
    });

    // Show only the rows for the current page
    var start = (currentPage - 1) * rowsPerPage;
    var end = start + rowsPerPage;
    rows.slice(start, end).forEach(function(row) {
        row.style.display = '';
    });

    // Update the current page number
    document.querySelector('.page-link.active').textContent = currentPage;
}

document.getElementById('prev-page').addEventListener('click', function(event) {
    event.preventDefault();
    if (currentPage > 1) {
        currentPage--;
        updateTable();
    }
});

document.getElementById('next-page').addEventListener('click', function(event) {
    event.preventDefault();
    var tableBody = document.getElementById('transport-table-body');
    var rows = Array.from(tableBody.children);
    var totalPages = Math.ceil(rows.length / rowsPerPage);

    if (currentPage < totalPages) {
        currentPage++;
        updateTable();
    }
});

// Initialize the table
updateTable();