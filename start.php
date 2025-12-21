<?php
$command = $argv[1] ?? null;

if($command=='serve'){
    echo "Server running on http://localhost:8000\n";
    exec("php -S localhost:8000 -t public");
} else {
    echo "Command tidak dikenal. Gunakan: php start.php serve\n";
}
