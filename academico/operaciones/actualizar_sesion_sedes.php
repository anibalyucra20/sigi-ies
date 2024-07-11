<?php
session_start();
$id_sede = $_GET['dato'];
$_SESSION['acad_sede'] = $id_sede;
echo "<script>
                window.location= window.history.back();
    			</script>";
?>