<table class="table">
    <thead>
        <tr><th>Datum</th><th>Produkt</th><th>Preis</th></tr>
    </thead>
    <tbody>
<?php 
foreach ($transactions as $transaction) { 
    $state = "";
    
    switch ($transaction->getStatus()) {
        case "2":
            $state = "success";
            break;
        case "3":
            $state = "annulated";
            break;
    }
    
    $timestamp = $transaction->getTimestamp();
    $productName = $transaction->getProduct()->getName();
    $amount = $transaction->getAmount();
    
    echo '<tr class="' . $state . '"><td>' . $timestamp . '</td><td>' . $productName . '</td><td>' . $amount . '</td></tr>';
}
?>
	</tbody>
</table>