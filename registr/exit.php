<?php
session_start();

unset($_SESSION['valid_user']);
unset($_SESSION['first_name']);
unset($_SESSION['last_name']);

header('Location: /');

