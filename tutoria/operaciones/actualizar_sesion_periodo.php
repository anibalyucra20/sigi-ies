<?php
session_start();
$id_per = $_GET['dato'];
$_SESSION['tutoria_periodo'] = $id_per;
echo "<script>
                window.location= window.history.back();
    			</script>";
?>