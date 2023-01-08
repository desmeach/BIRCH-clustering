<?php
require_once 'CFTree.php';
require_once 'Database.php';
require_once 'Point.php';
class ClusterAnalyzer {
    private int $B;
    private float $threshold;
    private float $L;
    private array $clients;
    private array $points;
    public function __construct() {
        $db = new Database();
        $this->clients = $db->getClients();
    }
    public function setParams(int $B, int $L, float $threshold, int $clientsNum, int $paramsNum) {
        $this->B = $B;
        $this->L = $L;
        $this->threshold = $threshold;
        $this->points = $this->getPoints($clientsNum, $paramsNum);
    }
    public function getClients() {
        return $this->clients;
    }
    public function getPoints(int $clientsNum, int $paramsNum): array {
        $points = [];
        $counter = 0;
        foreach ($this->clients as $client) {
            if ($counter == $clientsNum)
                break;
            $points[] = new Point($client, $paramsNum);
            $counter++;
        }
        return $points;
    }
    public function analyzeClusters(): CFTree {
        $tree = new CFTree();
        $root = $tree->getRoot();
        foreach ($this->points as $point) {
            $pointData = $point->getData();
            $closestCluster = $this->getClosestCluster($root, $point);
            $closestClusterNode = $closestCluster['node'];
            $closestCluster = $closestCluster['cluster'];
            if ($this->isClusterRadiusUnderThreshold($closestCluster, $pointData)
                && count($closestCluster->getChildren()) < $this->L) {
                $closestCluster->addPointChild($point);
            }
            else {
                if (count($closestClusterNode->getInputs()) < $this->B) {
                    $input = $closestClusterNode->addInput($closestCluster->getParent());
                    $input->addPointChild($point);
                }
                else {
                    $root = $tree->splitRoot($point);
                }
            }
        }
        return $tree;
    }
    public function getClosestCluster(CFNode $node, Point $point): array {
        $inputs = $node->getInputs();
        $closestInput = $this->getClosestInput($inputs, $point->getData());
        while (gettype($closestInput->getChildren()[0]) === 'object'
            && get_class($closestInput->getChildren()[0]) === CFNode::class) {
            $closestInputNode = $closestInput->getChildren()[0];
            $inputs = $closestInputNode->getInputs();
            $closestInput = $this->getClosestInput($inputs, $point->getData());
        }
        $node = $closestInput->getParentNode();
        return [
            'node' => $node,
            'cluster' => $closestInput
        ];
    }
    public function getClosestInput(array $inputs, array $point): CFInput {
        $min = PHP_INT_MAX;
        $closestInput = null;
        foreach ($inputs as $input) {
            $centroid = $input->getCentroid();
            $vector = [];
            foreach ($centroid as $key => $value) {
                $vector[] = $value - $point[$key];
            }
            $squaredSum = 0;
            foreach ($vector as $value)
                $squaredSum += pow($value, 2);
            $length = abs(sqrt($squaredSum));
            if ($length < $min) {
                $min = $length;
                $closestInput = $input;
            }
        }
        return $closestInput;
    }
    private function getRadius(CFInput $input, array $point): array {
        $LS = $this->getLinearSum($input, $point);
        $SS = $this->getSquaredSum($input, $point);
        $N = $input->getCF()['N'] + 1;
        // Формула нахождения радиуса кластера sqrt(ss/n - (ls/n)/n)
        $SS = array_map(fn($value) => $value / $N, $SS);
        $LS = array_map(fn($value) => pow($value, 2) / $N, $LS);
        $LS = array_map(fn($value) => $value / $N, $LS);
        foreach ($SS as $key => $value) {
            $SS[$key] = $value - $LS[$key];
        }
        return array_map(fn($value) => sqrt($value), $SS);
    }
    private function isClusterRadiusUnderThreshold(CFInput $input, array $point): bool {
        $radius = $this->getRadius($input, $point);
        foreach ($radius as $value) {
            if ($value > $this->threshold)
                return false;
        }
        return true;
    }
    private function getLinearSum(CFInput $input, array $point): array {
        $LS = [];
        $inputLS = $input->getCF()['LS'];
        foreach ($inputLS as $key => $value) {
            $LS[] += $value + $point[$key];
        }
        return $LS;
    }
    private function getSquaredSum(CFInput $input, array $point): array {
        $SS = [];
        $inputLS = $input->getCF()['SS'];
        foreach ($inputLS as $key => $value) {
            $SS[] += $value + pow($point[$key], 2);
        }
        return $SS;
    }
    public function printClusters(CFTree $tree)
    {
        echo '<div class="container text-center">' . '<h4>Результат анализа:</h4>';
        $root = $tree->getRoot();
        $inputs = $root->getInputs();
        $clusterNum = 1;
        if (get_class($inputs[0]->getChildren()[0]) !== CFNode::class) {
            $this->printLeafNode($root, $clusterNum);
            return;
        }
        foreach ($inputs as $input) {
            $leaf = $this->getLeafNode($input);
            if ($leaf !== null) {
                $clusterNum = $this->printLeafNode($leaf, $clusterNum);
            }
        }
        echo '</div>';
    }
    private function getLeafNode(CFInput $input) {
        $children = $input->getChildren();
        foreach ($children as $node) {
            $inputs = $node->getInputs();
            foreach ($inputs as $_input) {
                if (get_class($_input->getChildren()[0]) === CFNode::class) {
                    $this->getLeafNode($_input);
                }
                else
                    return $node;
            }
        }
    }
    private function printLeafNode(CFNode $node, int $clusterNum) {
        foreach ($node->getInputs() as $input) {
            $this->printLeafInput($input, $clusterNum);
            $clusterNum++;
        }
        return $clusterNum;
    }
    private function printLeafInput(CFInput $input, int $clusterNum) {
        echo '<h5> Кластер №' . $clusterNum . '</h5>';
        foreach ($input->getChildren() as $child) {
            echo '<p>' . $child->getName() . '</p>';
        }
    }
}