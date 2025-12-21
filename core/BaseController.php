<?php
class BaseController {
    protected function view($file, $data = []) {
        extract($data);
        require __DIR__."/../app/Views/$file.php";
    }
}
?>
