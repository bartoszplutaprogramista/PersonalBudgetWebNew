<?php

    session_start();

    session_unset();

    $_SESSION['correctlyLogOut'] = 'Nastąpiło poprawne wylogowanie';

    header('Location: personal-budget');

?>