<?php
session_start();
session_unset();    // Pehle saare variables khali karein
session_destroy();  // Phir session khatam karein

// Browser ko majboor karein ke wo purana page cache se na uthaye (Professional Practice)
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 

// YAHAN TABDEELI HAI:
// Pehle aap login.php par bhej rahe thay, ab index.php par jayenge.
// ../ ka matlab hai admin folder se bahar nikal kar main index file dhoondo.
header("Location:login.php");
exit();
?>