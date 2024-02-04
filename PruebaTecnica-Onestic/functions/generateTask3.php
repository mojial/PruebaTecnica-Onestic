<?php

// Almacenamos los datos de cada archivo en una variable

$customers = readCSV('customers.csv');
$products = readCSV('products.csv');
$orders = readCSV('orders.csv');

// Función para leer los csvs
function readCSV($filename)
{
    $handle = fopen($filename, "r");
    if ($handle === false) {
        die("Error al abrir el archivo CSV: $filename");
    }

    $headers = fgetcsv($handle);
    $csv = array();

    while (($row = fgetcsv($handle)) !== false) {
        $csv[] = array_combine($headers, $row);
    }

    fclose($handle);
    return $csv;
}

// Calcular el gasto total de cada cliente

$customerSpending = array();

foreach ($orders as $order) {
    $customerID = $order['customer'];
    $productIDs = explode(' ', $order['products']);

    // Inicializar el gasto total del cliente

    if (!isset($customerSpending[$customerID])) {
        $customerSpending[$customerID] = 0;
    }

    foreach ($productIDs as $productID) {
        foreach ($products as $product) {
            if ($product['id'] == $productID) {
                $customerSpending[$customerID] += floatval($product['cost']);
            }
        }
    }
}

// Crear un array vacio para almacenar los datos de customer_ranking

$customerRanking = array();

// Combinar la información del cliente con el gasto total
foreach ($customers as $customer) {
    $customerID = $customer['id'];
    $totalEuros = isset($customerSpending[$customerID]) ? $customerSpending[$customerID] : 0;

    $formattedTotalEuros = number_format($totalEuros, 2, '.', '');

    $customerRanking[] = array(
        'id' => $customerID,
        'firstname' => $customer['firstname'],
        'lastname' => $customer['lastname'],
        'total_euros' => $formattedTotalEuros,
    );
}

// Ordenar el array asociativo por total_euros en orden descendente

$totalEuros = array_column($customerRanking, 'total_euros');
array_multisort($totalEuros, SORT_DESC, $customerRanking);


// Escribir resultados en customer_ranking.csv

$fopen = fopen('tasksCsv/customer_ranking.csv', 'w');
fputcsv($fopen, array('id', 'firstname', 'lastname', 'total_euros'));

foreach ($customerRanking as $customer) {
    fputcsv($fopen, $customer);
}

fclose($fopen);

echo "customer_ranking.csv ha sido generado en la carpeta taskCsv.";
