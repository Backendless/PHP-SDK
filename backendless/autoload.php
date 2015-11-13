<?php
require __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'BackendlessAutoloader.php';

backendless\BackendlessAutoloader::register();
backendless\BackendlessAutoloader::addNamespace('backendless', __DIR__ . DIRECTORY_SEPARATOR .'src' );
