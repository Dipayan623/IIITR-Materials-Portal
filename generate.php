<?php

$password = "Hello123";

$hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

echo "<h3>Password:</h3>";
echo $password;

echo "<br><br>";

echo "<h3>Hash:</h3>";
echo $hash;

?>