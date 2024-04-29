

function deletePost(abonnementId) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cet abonnement ?")) {
        $.ajax({
            url: '/showAbonnement/' + abonnementId,
            type: 'DELETE',
            success: function(response) {
                $('#abonnements-list').load('/showAbonnement'); // Recharge la liste des abonnements
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
}
function countTime(debut,end){
    const debutTimestamp =debut ; // Replace with actual timestamp
    const endDate = end; // Replace with actual date object

// Calculate the difference in milliseconds
    const timeDifference = endDate.getTime() - debutTimestamp;

// Check if the end date has passed (negative difference)
    if (timeDifference <= 0) {
        console.error("End date has already passed. Countdown cannot be displayed.");
        return; // Exit the function if end date is in the past
    }

// Calculate days, hours, minutes, and seconds
    const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

// Update the countdown elements
    const countdownContainer = document.querySelector('.countdown');

    countdownContainer.dataset.date = endDate.toISOString().split('T')[0]; // Update data-date attribute

    const dayElement = countdownContainer.querySelector('.days .countdown-value');
    dayElement.textContent = days.toString().padStart(3, '0'); // Pad with leading zeros

    const hourElement = countdownContainer.querySelector('.hours .countdown-value');
    hourElement.textContent = hours.toString().padStart(2, '0');

    const minuteElement = countdownContainer.querySelector('.minutes .countdown-value');
    minuteElement.textContent = minutes.toString().padStart(2, '0');

    const secondElement = countdownContainer.querySelector('.seconds .countdown-value');
    secondElement.textContent = seconds.toString().padStart(2, '0');


}