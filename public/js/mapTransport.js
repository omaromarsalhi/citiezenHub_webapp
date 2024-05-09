

function initMap(departLatitude, departLongitude, arriveLatitude, arriveLongitude) {
    const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: (departLatitude + arriveLatitude) / 2, lng: (departLongitude + arriveLongitude) / 2 },
        zoom: 7,
        mapTypeControl: true,
    });

    const card = document.getElementById("pac-card");
    const input = document.getElementById("pac-input");
    const biasInputElement = document.getElementById("use-location-bias");

    const strictBoundsInputElement = document.getElementById("use-strict-bounds");
    const options = {
        fields: ["formatted_address", "geometry", "name"],
        strictBounds: false,
    };

    map.controls[google.maps.ControlPosition.TOP_LEFT].push(card);

    const autocomplete = new google.maps.places.Autocomplete(input, options);

    let markers = []; // Array to store created markers
    const infoWindow = new google.maps.InfoWindow();
    const geocoder = new google.maps.Geocoder();

    map.addListener('click', function(event) {
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
            window.alert("No details available for input: '" + place.name + "'");
            return;
        }

        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }

        marker.setPosition(place.geometry.location);
        marker.setVisible(true);
        infowindowContent.children["place-name"].textContent = place.name;
        infowindowContent.children["place-address"].textContent = place.formatted_address;
        infowindow.open(map, marker);
    });

    const startPoint = { lat: departLatitude, lng: departLongitude }; // Departure coordinates
    const endPoint = { lat: arriveLatitude, lng: arriveLongitude };  // Arrival coordinates

    const directionsService = new google.maps.DirectionsService();
    const directionsRequest = {
        origin: new google.maps.LatLng(startPoint.lat, startPoint.lng),
        destination: new google.maps.LatLng(endPoint.lat, endPoint.lng),
        travelMode: 'DRIVING',
    };

    directionsService.route(directionsRequest, (response, status) => {
        if (status === 'OK') {
            const route = response.routes[0];
            const polyline = new google.maps.Polyline({
                path: route.overview_path,
                map: map,
                strokeColor: '#0000FF',
                strokeWeight: 2,
            });

            const carMarker = new google.maps.Marker({
                position: startPoint,
                map: map,
            });

            let step = 0;
            const numSteps = route.overview_path.length;
            const delay = 100;

            function animateCar() {
                if (step >= numSteps) {
                    step = 0;
                }
                carMarker.setPosition(route.overview_path[step]);
                step++;
                setTimeout(animateCar, delay);
            }

            animateCar();
        } else {
            console.error('Directions request failed:', status);
        }
    });
}


window.initMap = initMap;

/*
function getStationCoordinates(departId, arriveId) {
    $.ajax({
        url: '/get-station-coordinates', // Replace with your actual route path
        method: 'GET',
        dataType: 'json',
        data: {
            departId: departId,
            arriveId: arriveId
        },
        success: function(response) {
            // Process the response (no need to split addresses)
            const departStation = response.depart;
            const arriveStation = response.arrive;

            // Access station coordinates directly
            const departLatitude = departStation.latitude;
            const departLongitude = departStation.longitude;
            const arriveLatitude = arriveStation.latitude;
            const arriveLongitude = arriveStation.longitude;

            // Call initMap with the coordinates
            initMap(departLatitude, departLongitude, arriveLatitude, arriveLongitude);

            // You can further manipulate the response data here based on your needs
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error getting station coordinates:", textStatus, errorThrown);
            // Handle errors appropriately (e.g., display an error message to the user)
        }
    });
}*/
function getStationCoordinates(departId, arriveId) {
    console.log(departId)
    $.ajax({
        url: '/get-station-coordinates', // Replace with your actual route path
        method: 'POST',
        data: {
            departId: departId,
            arriveId: arriveId
        },
        dataType: 'json',
        success: function(response) {
            console.log("aziz zabour")
            // Process the response (no need to split addresses)
                        const departStation = response.data.depart;
                        const arriveStation = response.data.arrive;


                        // Access station coordinates directly
                        const departLatitude = departStation.latitude;
                        const departLongitude = departStation.longitude;
                        const arriveLatitude = arriveStation.latitude;
                        const arriveLongitude = arriveStation.longitude;

                        // Call initMap with the coordinates
                        initMap(departLatitude, departLongitude, arriveLatitude, arriveLongitude);

            // You can further manipulate the response data here based on your needs
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error getting station coordinates:", textStatus, errorThrown);
            // Handle errors appropriately (e.g., display an error message to the user)
        }
    });
}






