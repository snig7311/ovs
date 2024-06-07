<?php
	include 'includes/session.php';
	include 'includes/slugify.php';
?>

<script type="text/javascript">
	function checkCamera() {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(stream) {
                console.log('Camera is enabled');
                // Send the result to the server using AJAX
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'camera_check.php');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('camera_enabled=true');
            })
            .catch(function(err) {
                console.log('Error accessing camera:', err);
                // Send the result to the server using AJAX
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'camera_check.php');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('camera_enabled=false');
            });
    }

    window.onload = function() {
        checkCamera();
    };

	
</script>

<?php 

	if(isset($_POST['vote'])){
		if(count($_POST) == 1){
			$_SESSION['error'][] = 'Please vote at least one candidate';
		}
		else{
			$_SESSION['post'] = $_POST;
			$sql = "SELECT * FROM positions";
			$query = $conn->query($sql);
			$error = false;
			$sql_array = array();
			while($row = $query->fetch_assoc()){
				$position = slugify($row['description']);
				$pos_id = $row['id'];
				if(isset($_POST[$position])){
					if($row['max_vote'] > 1){
						if(count($_POST[$position]) > $row['max_vote']){
							$error = true;
							$_SESSION['error'][] = 'You can only choose '.$row['max_vote'].' candidates for '.$row['description'];
						}
						else{
							foreach($_POST[$position] as $key => $values){
								$sql_array[] = "INSERT INTO votes (voters_id, candidate_id, position_id) VALUES ('".$voter['id']."', '$values', '$pos_id')";
							}
						}
						
					}
					else{
						$candidate = $_POST[$position];
						$sql_array[] = "INSERT INTO votes (voters_id, candidate_id, position_id) VALUES ('".$voter['id']."', '$candidate', '$pos_id')";
					}
				}
			}

			if(!$error){
				foreach($sql_array as $sql_row){
					$conn->query($sql_row);
				}

				unset($_SESSION['post']);
				$_SESSION['success'] = 'Ballot Submitted';
			}
		}
	}
	else{
		$_SESSION['error'][] = 'Select candidates to vote first';
	}

	header('location: home.php');
?>
