<?php
session_start();
$id_per = $_GET['dato'];
$_SESSION['acad_periodo'] = $id_per;
echo "<script>
                window.location= window.history.back();
    			</script>";
?>