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

// Calcular el costo total de cada orden

$orderPrices = array();

foreach ($orders as $order) {
    $totalCost = 0;
    $productIds = explode(' ', $order['products']);

    foreach ($productIds as $productId) {
        foreach ($products as $product) {
            if ($product['id'] == $productId) {
                $totalCost = $totalCost + $product['cost'];
            }
        }
    }

    $orderPrices[] = array('id' => $order['id'], 'euros' => $totalCost);
}

// Almacenar los resultados en el nuevo fichero order_prices.csv

$fp = fopen('tasksCsv/order_prices.csv', 'w');
fputcsv($fp, array('id', 'euros'));

foreach ($orderPrices as $orderPrice) {
    // Redondear el coste total a dos decimales
    $roundEuros = number_format($orderPrice['euros'], 2, '.', '');
    fputcsv($fp, array('id' => $orderPrice['id'], 'euros' => $roundEuros));
}

fclose($fp);

echo "order_prices.csv ha sido generado en la carpeta taskCsv.";
