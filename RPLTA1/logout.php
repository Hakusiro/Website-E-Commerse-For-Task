<?php
session_start();
session_unset();
session_destroy();
header("Location: dashboard.php?logout=1"); // ✅ Tambahkan parameter URL
exit;
