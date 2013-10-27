<?php
session_start();
unset($_SESSION);
session_destroy();
session_unset();
header("location: ../manage/");
exit;