
let municipality=''

function initMap() {
    const map = new google.maps.Map(document.getElementById("map"), {
        center: {lat: 34.28091343530563, lng: 9.391573951354815},
        zoom: 6,
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
            icon: {
                url: 'images/user/markep.png',
            scaledSize: new google.maps.Size(32, 32)
        }
        });

        marker.setMap(map);
        markers.push(marker); // Add the marker to the array
        if (markers.length > 1) {
            markers[markers.length - 2].setMap(null); // Remove previous marker
        }



        geocoder.geocode({location: clickedLatLng}, function (results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    const placeName = results[0].formatted_address;


                    const content= `
                    <div >
                        <h4 style=" color: black !important;
                                    font-weight: 500;
                                    font-size: 15px !important;margin: 0 !important;">` + placeName + `</h4>
                            </div>
                        `;

                    infoWindow.setPosition(clickedLatLng);
                    infoWindow.setContent(content);
                    infoWindow.open(map);


                }

                getCityFromGeocodeResponse(results)

            }
        });

        setTimeout(function () {
            geocoder.geocode({ address: 'Municipalité de '+$('#municipality').val() }, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        const municipalityAddress = results[0].formatted_address;
                        $('#municipalityAddressNew').val(municipalityAddress)
                    }
                }
            });
        },600)


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

    // Sets a listener on a radio button to change the filter type on Places
    // Autocomplete.
    // function setupClickListener(id, types) {
    //     const radioButton = document.getElementById(id);
    //
    //     radioButton.addEventListener("click", () => {
    //         autocomplete.setTypes(types);
    //         input.value = "";
    //     });
    // }
    //
    // setupClickListener("changetype-all", []);
    // setupClickListener("changetype-address", ["address"]);
    // setupClickListener("changetype-establishment", ["establishment"]);
    // setupClickListener("changetype-geocode", ["geocode"]);
    // setupClickListener("changetype-cities", ["(cities)"]);
    // setupClickListener("changetype-regions", ["(regions)"]);

    biasInputElement.addEventListener("change", () => {
        if (biasInputElement.checked) {
            autocomplete.bindTo("bounds", map);
        } else {
            // User wants to turn off location bias, so three things need to happen:
            // 1. Unbind from map
            // 2. Reset the bounds to whole world
            // 3. Uncheck the strict bounds checkbox UI (which also disables strict bounds)
            autocomplete.unbind("bounds");
            autocomplete.setBounds({
                east: 180,
                west: -180,
                north: 90,
                south: -90,
            });
            strictBoundsInputElement.checked = biasInputElement.checked;
        }

        input.value = "";
    });
    strictBoundsInputElement.addEventListener("change", () => {
        autocomplete.setOptions({
            strictBounds: strictBoundsInputElement.checked,
        });
        if (strictBoundsInputElement.checked) {
            biasInputElement.checked = strictBoundsInputElement.checked;
            autocomplete.bindTo("bounds", map);
        }
        input.value = "";
    });




}

function getCityFromGeocodeResponse(response) {
    const addressComponents = response[0].address_components;
    let municipality;


    const placeName = response[0].formatted_address;
    $('#mapAddress').val(placeName)

    for (const component of addressComponents) {
        if (component.types.includes('administrative_area_level_1')) {
            municipality = component.long_name;

            console.log('state:', municipality);
            $('#state').val(municipality)
        }
        if (component.types.includes('administrative_area_level_2')) {
            municipality = component.long_name;

            console.log('Municipality:', municipality);
            $('#municipality').val(municipality)
        }
    }
}






window.initMap = initMap;