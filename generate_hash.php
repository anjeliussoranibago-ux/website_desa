<?php

$password_baru = 'password_baru_anda';

$hash = password_hash($password_baru, PASSWORD_DEFAULT);

echo "Password Baru Anda: " . htmlspecialchars($password_baru) . "<br><br>";
echo "Copy dan paste kode hash di bawah ini ke dalam file login.php:<br><br>";
echo "<strong>" . htmlspecialchars($hash) . "</strong>";