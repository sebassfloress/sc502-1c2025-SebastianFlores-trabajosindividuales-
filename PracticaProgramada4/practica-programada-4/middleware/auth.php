<?php
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}
?>