<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBXRjaVw_WUlADDWnEZENgl00YAgOBus9g&libraries=places&callback=initAutocomplete"
            async defer></script>
    <style>
        html, body {

            margin: 0;
            padding: 0;
        }
        #map {
            width: 50%;
            height: 50%;
        }
        .controls {
            margin-top: 10px;
            border: 1px solid transparent;
            border-radius: 2px 0 0 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            height: 32px;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        #pac-input {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            margin-left: 12px;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 300px;
        }

        #pac-input:focus {
            border-color: #4d90fe;
        }

        .pac-container {
            font-family: Roboto;
        }

        #type-selector {
            color: #fff;
            background-color: #4d90fe;
            padding: 5px 11px 0px 11px;
        }

        #type-selector label {
            font-family: Roboto;
            font-size: 13px;
            font-weight: 300;
        }
        #target {
            width: 345px;
        }
    </style>
</head>
<body>
<?php
    $loc_lat = "";
    $loc_lng = "";
?>

<!--
ex.
form action="successfulbooking.php" method="post">
    <input type="hidden" name="date" value="<?php// echo $date; ?>">
    <input type="submit" value="Submit Form">
</form>
-->
<!-- GOOGLE MAPS -->
<!-- input search bar, map div, and javascript
    below for embedded google maps -->
<script>
    function initAutocomplete() {
        var loc_lat = document.getElementById('loc_lat').value;
        var loc_lng = document.getElementById('loc_lng').value;

        var myLatlng = new google.maps.LatLng( loc_lat, loc_lng );

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Add Marker
        var marker1 = new google.maps.Marker({
            draggable: true,
            position: myLatlng,
            map: map
        });

        map.addListener(marker1, 'dragend', function (event) {
            displayPosition(this.getPosition());
        });

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        var markers = [];
        markers.push(marker1);
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // Clear out the old markers.
            markers.forEach(function(marker) {
                marker.setMap(null);
            });
            markers = [];

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                if (!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                var marker = new google.maps.Marker({
                    draggable: true,
                    map: map,
                    title: place.name,
                    position: place.geometry.location
                });

                // drag response
                google.maps.event.addListener(marker, 'dragend', function(event) {
                    displayPosition(this.getPosition());
                });

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
                markers.push(marker);
            });
            displayPosition(map.getCenter());
            map.fitBounds(bounds);
            map.setZoom(14);
        });
    }
    function displayPosition(pos) {
        document.getElementById('latitude').value = pos.lat();
        document.getElementById('longitude').value = pos.lng();
    }
</script>
<input type="hidden" id="loc_lat" name="lat" value="<?php echo $loc_lat?>">
<input type="hidden" id="loc_lng" name="lng" value="<?php echo $loc_lng?>" >
<input type="text" id="latitude" name="lat" placeholder="Latitude">
<input type="text" id="longitude" name="lng" placeholder="Longitude">
<input id="pac-input" class="controls" type="text" placeholder="Search Box">
<div id="map"></div>
</body>
</html>