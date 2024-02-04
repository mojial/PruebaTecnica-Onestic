<?php

// Almacenamos los datos de cada archivo en una variable

$customers = readCSV('customers.csv');
$products = readCSV('products.csv');
$orders = readCSV('orders.csv');

// Función para leer los csvs indicados anteriormente
function readCSV($filename)
{
    $handle = fopen($filename, "r");
    $headers = fgetcsv($handle);
    $csv = array();

    while (($row = fgetcsv($handle)) !== false) {
        $csv[] = array_combine($headers, $row);
    }

    fclose($handle);
    return $csv;
}

// Crear un array vacio para almacenar los datos de product_customers
$productCustomers = array();

// Recorrer los pedidos para crear los datos de product_customers
foreach ($orders as $order) {
    $customerID = $order['customer'];
    $productIDs = explode(' ', $order['products']);

    foreach ($productIDs as $productID) {
        $productCustomers[$productID][] = $customerID;
    }
}

// Escribir los datos de product_customers en product_customers.csv

$fp = fopen('tasksCsv/product_customers.csv', 'w');
fputcsv($fp, array('id', 'customer_ids'));

foreach ($productCustomers as $productID => $customerIDs) {
    $customerIDs = implode(' ', $customerIDs);
    fputcsv($fp, array('id' => $productID, 'customer_ids' => $customerIDs));
}

fclose($fp);

echo "product_customers.csv ha sido generado en la carpeta taskCsv.";
