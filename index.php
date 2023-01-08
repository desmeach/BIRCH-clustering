<?php
require_once 'classes/ClusterAnalyzer.php';
$clusterAnalyzer = new ClusterAnalyzer(2, 5, 1.5);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BIRCH Algorithm</title>
</head>
<body>
    <h3>
        BIRCH Algorithm
    </h3>
    <p>
        <?php
        $clusterAnalyzer->analyzeClusters();
        ?>
    </p>
</body>
</html>