<?php

$password = 'yourpassword'; $hashedPassword = password_hash($password, PASSWORD_BCRYPT); echo $hashedPassword;


?>