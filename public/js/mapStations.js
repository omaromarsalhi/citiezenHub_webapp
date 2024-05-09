let map; // Define map variable in the global scope

// Initialization function
function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 0, lng: 0 }, // Initialize with a default center
        zoom: 355,
        mapTypeControl: true,
    });

    // Call function to display all stations (assuming nomstationArray is defined somewhere)
    displayAllStations(map, nomstationArray);

    // Calculate the map bounds based on marker positions

    const bounds = new google.maps.LatLngBounds();
    nomstationArray.forEach(function (station) {
        const [lat, lng] = station.split(',').map(parseFloat);
        bounds.extend(new google.maps.LatLng(lat, lng));
    });

    // Fit the map to the calculated bounds
    map.fitBounds(bounds);

    // Set a maximum zoom level to prevent zooming in too close
    const maxZoom = 70;
    google.maps.event.addListenerOnce(map, 'bounds_changed', function () {
        if (this.getZoom() > maxZoom) {
            this.setZoom(maxZoom);
        }
    });

}


// Function to display all stations
function displayAllStations(map, stationArray) {
    stationArray.forEach(function (station) {
        // Split the address string into latitude and longitude
        const [lat, lng] = station.split(',').map(parseFloat);

        // Create a marker for each station
        const marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            title: station.nomstation,
        });

        // Add click listener to each marker
        marker.addListener('click', function () {
            // You can perform any action when a marker is clicked
            console.log('Clicked station:', station.nomstation);
        });
    });
}

// Function to display path between stations

function displayPathBetweenStations(map, startPoint, endPoint) {
    // Create a directions service object
    const directionsService = new google.maps.DirectionsService();

    // Define the directions request
    const directionsRequest = {
        origin: startPoint,
        destination: endPoint,
        travelMode: 'DRIVING', // Adjust travel mode as needed
    };

    // Request directions from the Directions API
    directionsService.route(directionsRequest, (response, status) => {
        if (status === 'OK') {
            const route = response.routes[0];
            const polyline = new google.maps.Polyline({
                path: route.overview_path,
                map: map,
                strokeColor: '#0000FF',
                strokeWeight: 2,
            });

            // Create a car marker
            const carMarker = new google.maps.Marker({
                position: startPoint,
                map: map,
            });

            // Animate the car along the route
            let step = 0;
            const numSteps = route.overview_path.length;
            const delay = 100; // 100 milliseconds interval

            function animateCar() {
                if (step >= numSteps) {
                    step = 0;
                }
                carMarker.setPosition(route.overview_path[step]);
                step++;
                setTimeout(animateCar, delay);
            }

            // Start the animation
            animateCar();
        } else {
            console.error('Directions request failed:', status);
        }
    });
}

// Add event listener to button for displaying path between stations
const displayPathButton = document.getElementById("mapBtn"); // Ensure you have a button with this ID in your HTML

displayPathButton.addEventListener("click", function () {
    // Get the start and end point coordinates
    const startPoint = { lat: 36.805551, lng: 10.179325 };
    const endPoint = { lat: 36.7956335, lng: 10.0893454 };
alert("displayed")
    // Call the displayPathBetweenStations function with the points
   // displayPathBetweenStations(map, startPoint, endPoint);
});
