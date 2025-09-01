
<?php
@session_start();
@session_destroy();

echo '<script> window.location="page.php"</script>';

?>