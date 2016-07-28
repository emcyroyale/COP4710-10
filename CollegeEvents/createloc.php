<?php
	require_once ('check_session.php');
	require_once('config.php');
	require_once('connect.php');
?>
<html>
	<head>
	<meta charset="UTF-8">
	<meta name="description" content="Creates a new University by a Super Admin">
	<title>Create University</title>
	<link rel = "stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="styles.css">
	<link rel="stylesheet" type="text/css" href="maps.css">
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBXRjaVw_WUlADDWnEZENgl00YAgOBus9g&sensor=false&libraries=places" async defer></script>
</head>
<!-- PHP CODE -->
<?php
	//HTML tags for error messages
	$success = $name = $loc_name = $loc_lat = $loc_lng = "";
	$nameErr = $loc_nameErr = $loc_addressErr = $loc_latErr = $loc_lngErr =  "";
	$names = [];
	$missing_data = [];

	//Populates dropdown
	$query = "SELECT name
			  FROM location";
	$result = mysqli_query($database, $query);
	if(mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($names, $row['name']);
		}
		// Find current user's university location_id
		// And initialize map to these coordinates
		if ($_SESSION['user_type'] === 's') {
			$query_userloc = "SELECT latitude, longitude
					  FROM student, university, location
					  WHERE student.university=university.university 
					  AND student.student_id='$curUser'
					  AND university.location_id=location.location_id";
		} else if ($_SESSION['user_type'] === 'a') {
			$query_userloc = "SELECT latitude, longitude
					  FROM admin, university, location
					  WHERE admin.university=university.university
					  AND admin.admin_id='$curUser'
					  AND university.location_id=location.location_id";
		} else if ($_SESSION['user_type'] === 'sa') {
			$query_userloc = "SELECT latitude, longitude
					  FROM super_admin, university, location
					  WHERE super_admin.sadmin_id='$curUser'
					  AND super_admin.sadmin_id=university.created_by
					  AND university.location_id=location.location_id";
		}
		$result = mysqli_query($database, $query_userloc);

		$loc_lat = $loc_lat_default;
		$loc_lng = $loc_lng_default;
		if ($result && mysqli_num_rows($result) >= 1) {
			$row = mysqli_fetch_assoc($result);
			if (isset($row['latitude']) && isset($row['longitude'])) {
				$loc_lat = $row["latitude"];
				$loc_lng = $row["longitude"];
			}
		}
	}

	// Check for each required input data that has been POSTed through Request Method
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		if( isset($missing_data) ){
			unset($missing_data);
			$missing_data = [];
		}
		// If View Location was clicked
		if( isset($_POST["viewLoc"]) ) {
			//Reset submission and errors;
			$nameErr = $loc_nameErr = $loc_addressErr = $loc_latErr = $loc_lngErr =  "";
			if (empty($_POST["locSel"])) {
				$missing_data[] = "location";
				$loc_nameErr = $err . "Location is required" . $end;
			} else {
				$loc_name = trim_input($_POST["locSel"]);
				$loc_nameErr = "";
				if( !($loc_name === "optionNull") ) {
					$queryLatLng = "SELECT latitude, longitude
			  			  FROM location
			  			  WHERE location.name='$loc_name'";
					$result = mysqli_query($database, $queryLatLng);
					if (mysqli_num_rows($result) == 1) {
						$row = mysqli_fetch_assoc($result);
						$loc_lat = $row["latitude"];
						$loc_lng = $row["longitude"];
					}
				}
			}
		}

		// If Add New Location was clicked
		if( isset($_POST["submit"]) ) {
			$loc_nameErr = "";
			if (empty($_POST["name"])) {
				$missing_data[] = "name";
				$nameErr = $err . "Location name is required" . $end;
			} else {
				$name = trim_input($_POST["name"]);
				$nameErr = "";

				if (strlen($name) > 4 && strlen($name) < 20) {
					$missing_data[] = "name";
				}
			}

			if (empty($_POST["loc_lat"])) {
				$missing_data[] = "loc_address";
			} else {
				$loc_lat = $_POST['loc_lat'];
				$loc_lng = $_POST['loc_lng'];
			}
		}

		if (empty($missing_data)) {
			$queryLoc = "INSERT INTO location (name, latitude, longitude) VALUES (?, ?, ?)";
			$stmt = mysqli_prepare($database, $queryLoc);

			mysqli_stmt_bind_param($stmt, "sss", $name, $loc_lat, $loc_lng);
			mysqli_stmt_execute($stmt);
			$affected_rows = mysqli_stmt_affected_rows($stmt);
			if ($affected_rows > 0) {
				mysqli_stmt_close($stmt);
				mysqli_close($database);
				array_push($names, $name);
				$success = $suc . "Location has been added" . $end;
			} else {
				$success = $suc . "Location already exists" . $end;
				mysqli_stmt_close($stmt);
				mysqli_close($database);
			}
		}
	}
?>
<!-- Google Maps Javascript API -->
<script>
	function initialize() {
		var ini_coords = new google.maps.LatLng( <?php echo $loc_lat?>, <?php echo $loc_lng?>);
		var marker_pos = ini_coords;

		var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 15,
			center: ini_coords,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		// Create markers buffer and add initial marker
		var markers = [];
		addMarker(ini_coords);

		// Create the search box and link it to the UI element.
		var input = document.getElementById('pac-input');
		var searchBox = new google.maps.places.SearchBox(input);
		map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

		// Create the generate button and link it to the UI element.
		var genButtonDiv = document.createElement('pac-button');
		var genButton = new GenButton(genButtonDiv);
		genButtonDiv.index = 1;
		map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(genButtonDiv);

		// Geocoder to covnert Lat/Long from draggable pin to readable address
		var geocoder = new google.maps.Geocoder;
		var infowindow = new google.maps.InfoWindow;

		// Bias the SearchBox results towards current map's viewport.
		map.addListener('bounds_changed', function () {
			searchBox.setBounds(map.getBounds());
		});

		// Listen for the event fired when the user selects a prediction and retrieve
		// more details for that place.
		searchBox.addListener('places_changed', function () {
			var places = searchBox.getPlaces();

			if (places.length == 0) {
				return;
			}

			// For each place, get the icon, name and location.
			var bounds = new google.maps.LatLngBounds();
			places.forEach(function (place) {
				if (!place.geometry) {
					console.log("Returned place contains no geometry");
					return;
				}

				addMarker(place.geometry.location);

				if (place.geometry.viewport) {
					bounds.union(place.geometry.viewport);
				} else {
					bounds.extend(place.geometry.location);
				}
			});
			map.fitBounds(bounds);
			map.setZoom(14);
		});

		google.maps.event.addListener(map, 'click', function( event ){
			marker_pos = event.latLng;
			addMarker( marker_pos );
		});

		function addMarker(pos) {

			// Clear out the old markers.
			markers.forEach(function (marker) {
				marker.setMap(null);
			});
			markers = [];

			// Add Marker
			var marker = new google.maps.Marker({
				draggable: true,
				position: pos,
				map: map
			});

			google.maps.event.addListener(marker, 'dragend', function (event) {
				marker_pos = this.getPosition();
			});

			markers.push( marker );
			return marker;
		}

		function GenButton(controlDiv) {

			// Set CSS for the control border.
			var controlUI = document.createElement('div');
			controlUI.style.backgroundColor = '#fff';
			controlUI.style.border = '2px solid rgb(25,25,25)';
			controlUI.style.borderRadius = '5px';
			controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
			controlUI.style.cursor = 'pointer';
			controlUI.style.marginBottom = '20px';
			controlUI.style.textAlign = 'center';
			controlUI.title = "Click to add Pin's address to adress box below";
			controlDiv.appendChild(controlUI);

			// Set CSS for the control interior.
			var controlText = document.createElement('div');
			controlText.style.color = 'rgb(25,25,25)';
			controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
			controlText.style.fontSize = '16px';
			controlText.style.lineHeight = '38px';
			controlText.style.paddingLeft = '5px';
			controlText.style.paddingRight = '5px';
			controlText.innerHTML = 'Add Location';
			controlUI.appendChild(controlText);

			// Setup the click event listeners: simply set the map to Chicago.
			controlUI.addEventListener('click', function () {
				document.getElementById('loc_lat').value = marker_pos.lat();
				document.getElementById('loc_lng').value = marker_pos.lng();
				geocodeLatLng(geocoder, map, infowindow);
			});
		}

		function geocodeLatLng(geocoder, map, infowindow) {
			var latStr = marker_pos.lat();//document.getElementById('loc_lat').value;
			var lngStr = marker_pos.lng(); //document.getElementById('loc_lng').value;
			var latlng = {lat: parseFloat(latStr), lng: parseFloat(lngStr)};
			geocoder.geocode({'location': latlng}, function (results, status) {
				if (status === google.maps.GeocoderStatus.OK) {
					if (results[1]) {

						// Replace Marker with info window
						var marker = addMarker(marker_pos);

						var loc_address = results[0].formatted_address;
						infowindow.open(map, marker);
						infowindow.setContent(loc_address);
						document.getElementById('loc_address').value = loc_address;

					} else {
						window.alert('No results found');
					}
				} else {
					window.alert('Geocoder failed due to: ' + status);
				}
			});
		}
	}
</script>

<body onload="initialize()">
	<div class="flex-container">
		<header>
			<h1> CREATE LOCATION </h1>
	<span><b><?php echo "Welcome ". $_SESSION['username'] . "<br />";
			if($_SESSION['user_type']=='s'){ echo "Student Account";}
			elseif($_SESSION['user_type']=='a'){ echo "Admin Account";}
			elseif($_SESSION['user_type']=='sa'){ echo "Super Admin Account";}?></b></span><br />
			<a class="btn btn-xs btn-primary " href="logout.php" target="_self"> Log Out</a><br />
		</header>
	<nav class="nav">
		<ul>
			<?php
			if($_SESSION['user_type']== 's'){
				echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\">Dashboard</a></b></li>
							<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"joinRSO.php\" target=\"_self\"> Join RSO</a></b></li>
							<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createRSO.php\" target=\"_self\"> Create RSO</a><br /></b></li>";
			}
			elseif($_SESSION['user_type']== 'a'){
				echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li>
							<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createEvent.php\" target=\"_self\"> Create Event</a><br /></b></li>
							<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"joinRSO.php\" target=\"_self\"> Join RSO</a></b></li>
							<li><b> <a class = \"btn bt n-mg btn-primary btn-block\" href=\"createRSO.php\" target=\"_self\"> Create RSO</a><br /></b></li>";
			}
			elseif($_SESSION['user_type']== 'sa'){
				echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li>
							<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createuniversity.php\" target=\"_self\"> Create University</a></b></li>";
			}
			?>
		</ul>
	</nav>
	<article class="article">
		<!-- Location Selector -->
		<form class="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<?php echo $loc_nameErr ?>
			<select class="form-control" name="locSel">
				<option value="optionNull" selected="selected">Select Location:</option>
				<?php
				for($x = 0; $x <= count($names); $x++){
					echo "<option value=" . $names[$x] . ">" . $names[$x]  . "</option>";
				}
				?>
			<input class="btn btn-lg btn-primary btn-block" type="submit" name="viewLoc" value="View Location">
		</form>

		<!-- Embedded Google Maps -->
		<input id="pac-input" class="controls" type="text" placeholder="Search Box">
		<div id="map"></div>

		<!-- Add New Location -->
		<form class="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<!-- Location Name Text Box -->
			<?php echo $nameErr ?>
			<input class="form-control" type="text" id="name" title="Must be between 4 to 20 characters."
				   name="name" placeholder="New Location Name" required>

			<!-- Address Text Box -->
			<input class="form-control" type="text" id="loc_address" title="Please select 'Add Location' on google maps to generate address."
				   		name="loc_address" placeholder="Address" required disabled>
			<!-- Hidden input to post long/lat from google maps to PHP -> MySQL -->
			<input type="hidden" id="loc_lat" name="loc_lat">
			<input type="hidden" id="loc_lng" name="loc_lng">
			<!-- SUBMIT -->
			<input class = "btn btn-lg btn-primary btn-block" type="submit" name="submit" value="Add"><br />
			<?php echo $success; ?>
		</form>
	</article>
	<div>
</body>
</html>
