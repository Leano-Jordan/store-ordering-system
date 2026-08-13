<?php

require_once __DIR__.'/../db.php';
require_once __DIR__.'/../helpers.php';

//======================GET PRODUCTS
function getProducts(mysqli $conn, int $limit, int $offset)
{
    $sql = "SELECT * FROM products 
    ORDER BY status = 'Inactive', 
    category ASC, 
    CASE WHEN stock = 0 THEN 2 
    WHEN stock<= 10 THEN 1 
    ELSE 0 END, 
    stock DESC, name ASC LIMIT ? OFFSET ?";

    return executeQuery(
        $conn,
        $sql,
        'ii',
        [$limit, $offset]
    );
}

//======================GET PRODUCTS COUNT

function getProductCount(mysqli $conn): int
{
    $sql = 'SELECT COUNT(*) AS total FROM products';

    $result = executeQuery($conn, $sql);

    return (int) $result->fetch_assoc()['total'];
}

//======================SEARCH PRODUCTS
function searchProducts(mysqli $conn, string $search, int $limit, int $offset)
{
    $search = '%'.$search.'%';

    $sql = "SELECT * FROM products 
    WHERE status = 'Active' AND 
    (name LIKE ? OR category LIKE ?)
    ORDER BY status = 'Inactive', 
    category ASC, 
    CASE 
        WHEN stock = 0 THEN 2 
        WHEN stock <= 10 THEN 1 
        ELSE 0 END, 
    stock DESC, 
    name ASC LIMIT ? OFFSET ?";

    return executeQuery(
        $conn,
        $sql,
        'ssii',
        [$search, $search, $limit, $offset]
    );
}

//======================COUNT SEARCH PRODUCTS
function countSearchProducts(mysqli $conn, string $search)
{
    $search = '%'.$search.'%';

    $sql = "SELECT COUNT(*) AS total FROM products WHERE status = 'Active' AND (name LIKE ? OR category LIKE ?)";

    return executeQuery($conn, $sql, 'ss', [$search, $search]);
}
