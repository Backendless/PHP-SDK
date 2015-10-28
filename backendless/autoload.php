<?php
require __DIR__.'/src/BackendlessAutoloader.php';

backendless\BackendlessAutoloader::register();
backendless\BackendlessAutoloader::addNamespace('backendless', __DIR__ . '/src' );
