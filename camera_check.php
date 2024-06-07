<?php
// This part of the code should be in camera_check.php
if (isset($_POST['camera_enabled'])) {
    $_SESSION['camera_enabled'] = $_POST['camera_enabled'] === 'true';
}
?>