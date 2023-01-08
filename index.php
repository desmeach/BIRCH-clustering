<?php
require_once 'classes/ClusterAnalyzer.php';
$clusterAnalyzer = new ClusterAnalyzer();
$clients = $clusterAnalyzer->getClients();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <title>BIRCH Algorithm</title>
    <script>
        function showVal(newVal) {
            document.getElementById("range-label").innerHTML = 'Порог (threshold): ' + newVal;
        }
    </script>
</head>
<body>
    <div class="container mt-2">
        <h3 class="text-center">
            BIRCH Algorithm
        </h3>
        <h4>Тестовый набор данных</h4>
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Фамилия</th>
                <th scope="col">Возраст</th>
                <th scope="col">Заработок</th>
                <th scope="col">Кредитная история</th>
                <th scope="col">Семейное положение</th>
                <th scope="col">Образование</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($clients as $client):?>
                <tr>
                    <th scope="row"><?=$client['id']?></th>
                    <td><?=$client['name']?></td>
                    <td><?=$client['age']?></td>
                    <td><?=$client['income']?></td>
                    <td><?=$client['credit_history']?></td>
                    <td><?=$client['marital_status']?>
                    <td><?=$client['education']?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        <h4>Настройки работы алгоритма</h4>
        <form method="post" action="">
            <div class="row justify-content-md-center">
                <div class="col">
                    <label for="threshold" class="form-label" id="range-label">Порог (threshold):
                        <?php if ($_POST['threshold']):
                            echo $_POST['threshold'];
                        endif;?> </label>
                    <input type="range" class="form-range" min="1" max="15" step="1" id="threshold"
                           name="threshold"
                           value="<?php
                           if ($_POST['threshold']):
                               echo $_POST['threshold'];
                           endif;?>"
                           oninput="showVal(this.value)">
                </div>
            </div>
            <div class="row justify-content-md-center">
                <div class="col">
                    <label for="clients-num" class="form-label">Количество записей</label>
                    <select class="form-select" id="clients-num" name="clients-num" aria-label="Default select example">
                        <option <?php if ($_POST['clients-num'] == 1): ?> selected <?php endif;?> value="1">1</option>
                        <option <?php if ($_POST['clients-num'] == 2): ?> selected <?php endif;?> value="2">2</option>
                        <option <?php if ($_POST['clients-num'] == 3): ?> selected <?php endif;?> value="3">3</option>
                        <option <?php if ($_POST['clients-num'] == 4): ?> selected <?php endif;?> value="4">4</option>
                        <option <?php if ($_POST['clients-num'] == 5): ?> selected <?php endif;?> value="5">5</option>
                    </select>
                </div>
                <div class="col">
                    <label for="params-num" class="form-label">Количество параметров</label>
                    <select class="form-select" id="params-num" name="params-num" aria-label="Default select example">
                        <option <?php if ($_POST['params-num'] == 1): ?> selected <?php endif;?> value="1">1</option>
                        <option <?php if ($_POST['params-num'] == 2): ?> selected <?php endif;?> value="2">2</option>
                        <option <?php if ($_POST['params-num'] == 3): ?> selected <?php endif;?> value="3">3</option>
                        <option <?php if ($_POST['params-num'] == 4): ?> selected <?php endif;?> value="4">4</option>
                        <option <?php if ($_POST['params-num'] == 5): ?> selected <?php endif;?> value="5">5</option>
                    </select>
                </div>
            </div>
            <div class="row justify-content-md-center">
                <div class="col">
                    <label for="L" class="form-label">L</label>
                    <input type="text" class="form-control" id="L" name="L"
                           value="<?php
                           if ($_POST['L']):
                               echo $_POST['L'];
                           endif;?>"
                           placeholder="Количество элементов в кластере" aria-label="Количество элементов в кластере">
                </div>
                <div class="col">
                    <label for="B" class="form-label">B</label>
                    <input type="text" class="form-control" id="B" name="B"
                           value="<?php
                           if ($_POST['B']):
                               echo $_POST['B'];
                           endif;?>"
                           placeholder="Степень ветвления CF-дерева" aria-label="Количество элементов в кластере">
                </div>
            </div>
            <button class="btn btn-primary mt-2 text-center" type="submit">Сгенерировать кластеры</button>
        </form>
        <div>
            <?php
            if (!empty($_POST)) {
                $B = $_POST['B'];
                $L = $_POST['L'];
                $threshold = $_POST['threshold'];
                $clientsNum = $_POST['clients-num'];
                $clusterAnalyzer->setParams($B, $L, $threshold, $clientsNum, 5);
                $CFtree = $clusterAnalyzer->analyzeClusters();
                $clusterAnalyzer->printClusters($CFtree);
            }
            ?>
        </div>
    </div>
</body>
</html>