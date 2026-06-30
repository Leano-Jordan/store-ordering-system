<?php

$conn = new mysqli(
    "localhost",
    "root",
    "mysql",
    "store-ordering"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
