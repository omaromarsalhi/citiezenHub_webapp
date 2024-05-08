let municipality = ''

function initMap() {
    const map = new google.maps.Map(document.getElementById("map"), {
        center: {lat: 34.28091343530563, lng: 9.391573951354815},
        zoom: 7,
        mapTypeControl: true,
    });

    const card = document.getElementById("pac-card");
    const input = document.getElementById("pac-input");
    const biasInputElement = document.getElementById("use-location-bias");

    const strictBoundsInputElement =
        document.getElementById("use-strict-bounds");
    const options = {
        fields: ["formatted_address", "geometry", "name"],
        strictBounds: false,
    };

    map.controls[google.maps.ControlPosition.TOP_LEFT].push(card);

    const autocomplete = new google.maps.places.Autocomplete(
        input,
        options
    );


    let markers = []; // Array to store created markers
    const infoWindow = new google.maps.InfoWindow();
    const geocoder = new google.maps.Geocoder();


    map.addListener('click', function (event) {
        const clickedLatLng = event.latLng;
        console.log('Clicked Lat:', clickedLatLng.lat(), 'Lng:', clickedLatLng.lng());


        const marker = new google.maps.Marker({
            position: clickedLatLng,
        });

        marker.setMap(map);
        markers.push(marker); // Add the marker to the array
        if (markers.length > 1) {
            markers[markers.length - 2].setMap(null); // Remove previous marker
        }


    });

    infoWindow.setOptions({
        maxWidth: 200, // Maximum width of the info window
        pixelOffset: new google.maps.Size(0, -30) // Adjust position relative to marker
    });


    autocomplete.bindTo("bounds", map);

    const infowindow = new google.maps.InfoWindow();
    const infowindowContent = document.getElementById("infowindow-content");

    infowindow.setContent(infowindowContent);

    const marker = new google.maps.Marker({
        map,
        anchorPoint: new google.maps.Point(0, -29),
    });

    autocomplete.addListener("place_changed", () => {
        infowindow.close();
        marker.setVisible(false);

        const place = autocomplete.getPlace();

        if (!place.geometry || !place.geometry.location) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert(
                "No details available for input: '" + place.name + "'"
            );
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }

        marker.setPosition(place.geometry.location);
        marker.setVisible(true);
        infowindowContent.children["place-name"].textContent = place.name;
        infowindowContent.children["place-address"].textContent =
            place.formatted_address;
        infowindow.open(map, marker);
    });


    /* // Define starting and ending point coordinates
     const startPoint = {lat: 36.55476265189162, lng: 9.9657544026299}; // Example coordinates (Tunis, Tunisia)
     const endPoint = {lat: 36.045651413849015, lng: 9.9547680745049};  // Example coordinates (Bardo, Tunisia)


     // Create a directions service object
     const directionsService = new google.maps.DirectionsService();
     // Define the directions request
     const directionsRequest = {
         origin: new google.maps.LatLng(startPoint.lat, startPoint.lng),
         destination: new google.maps.LatLng(endPoint.lat, endPoint.lng),
         travelMode: 'DRIVING', // Adjust travel mode as needed (e.g., DRIVING, BICYCLING, etc.)
     };

     // Request directions from the Directions API
     directionsService.route(directionsRequest, (response, status) => {
         if (status === 'OK') {
             const route = response.routes[0];
             const polyline = new google.maps.Polyline({
                 path: route.overview_path,
                 map: map,
                 strokeColor: '#0000FF', // Set a blue color for the line
                 strokeWeight: 2,
             });
         } else {
             console.error('Directions request failed:', status);
         }
     });

     const markerLocations = [
         {lat: 36.55476265189162, lng: 9.9657544026299, title: "Sydney"}, // Example marker (Sydney)
         {lat: 36.045651413849015, lng: 9.9547680745049, title: "Melbourne"}, // Example marker (Melbourne)
     ];

     // Add markers to the map
     for (const location of markerLocations) {
         const marker = new google.maps.Marker({
             position: location,
             map: map,
             title: location.title,
         });
         marker.setMap(map);
    */


// Define starting and ending point coordinates
    const startPoint = {lat: 36.55476265189162, lng: 9.9657544026299}; // Example coordinates (Tunis, Tunisia)
    const endPoint = {lat: 36.045651413849015, lng: 9.9547680745049};  // Example coordinates (Bardo, Tunisia)


// Create a directions service object
    const directionsService = new google.maps.DirectionsService();

// Define the directions request
    const directionsRequest = {
        origin: new google.maps.LatLng(startPoint.lat, startPoint.lng),
        destination: new google.maps.LatLng(endPoint.lat, endPoint.lng),
        travelMode: 'DRIVING',
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
            const delay = 100; // 1 second interval

            function animateCar() {
                if (step >= numSteps) {
                    step=0
                    // return;
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

window.initMap = initMap;


