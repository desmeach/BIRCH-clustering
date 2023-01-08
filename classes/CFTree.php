<?php

class CFTree {
    private CFNode $root;
    public function __construct() {
        $this->root = new CFNode();
    }
    /**
     * @return CFNode
     */
    public function getRoot(): CFNode
    {
        return $this->root;
    }
    public function setRoot(CFNode $root) {
        $this->root = $root;
    }
    public function splitRoot(Point $point): CFNode {
        $newRoot = new CFNode();
        $newRootInputs = $newRoot->getInputs();
        $nodeInput = $newRootInputs[0];
        $nodeInput->addNodeChild($this->root);
        foreach ($this->root->getInputs() as $input) {
            $input->setParent($nodeInput);
        }
        $newRoot->setNewInputCF();
        $newNodeInput = $newRoot->addInput();
        $newNodeInput->addNodeChild(new CFNode());
        $newNodeInputChild = $newNodeInput->getChildren()[0];
        $newNodeInputChildInput = $newNodeInputChild->getInputs()[0];
        $newNodeInputChildInput->setParent($newNodeInput);
        $newNodeInputChildInput->addPointChild($point);
        $this->root = $newRoot;
        return $newRoot;
    }
}

class CFNode {
    private array $inputs;
    public function __construct() {
        $input = new CFInput();
        $input->setParentNode($this);
        $this->inputs = [$input];
    }
    /**
     * @return array
     */
    public function getInputs(): array
    {
        return $this->inputs;
    }
    public function addInput($parent = null): CFInput {
        $input = new CFInput();
        $input->setParentNode($this);
        $this->inputs[] = $input;
        if ($parent)
            $input->setParent($parent);
        return $input;
    }
    public function setNewInputCF() {
        $nodeInputs = $this->inputs[0]->getChildren()[0];
        $sum = [];
        foreach ($nodeInputs->getInputs() as $input) {
            $CF = $input->getCF();
            if (!empty($sum)) {
                $sum['N'] += $CF['N'];
                $sum['LS'] = $input->getSumCF($sum['LS'], $CF['LS']);
                $sum['SS'] = $input->getSumCF($sum['SS'], $CF['SS']);
            }
            else
                $sum = $CF;
        }
        $this->inputs[0]->setCF($sum);
    }
}

class CFInput {
    private array $CF;
    private array $children;
    private CFNode $parentNode;
    private ?CFInput $parent = null;
    public function __construct($node = null) {
        $this->CF = ['N' => 0, 'LS' => [], 'SS' => []];
        if ($node)
            $this->children = [$node];
        else
            $this->children = [];
    }
    public function addPointChild(Point $point) {
        $this->children[] = $point;
        $this->updateCF($point->getData());
        $parent = $this->getParent();
        while ($parent) {
            $parent->updateCF($point->getData());
            $parent = $parent->getParent();
        }
    }
    public function addNodeChild(CFNode $node) {
        if ($this->children[0] && get_class($this->children[0]) !== CFNode::class)
            $this->children = [];
        $this->children[] = $node;
    }
    public function getCentroid(): array {
        $N = $this->CF['N'];
        return array_map(fn($value) => $value / $N, $this->CF['LS']);
    }
    public function updateCF(array $point) {
        $this->CF['N'] += 1;
        if (empty($this->CF['LS']) && empty($this->CF['SS'])) {
            $this->CF['LS'] = $point;
            $this->CF['SS'] = array_map(fn($value): int => pow($value, 2), $point);
            return;
        }
        foreach ($point as $key => $value) {
            $this->CF['LS'][$key] += $value;
            $this->CF['SS'][$key] += pow($value, 2);
        }
    }

    /**
     * @return array
     */
    public function getCF(): array
    {
        return $this->CF;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array $CF
     */
    public function setCF(array $CF): void
    {
        $this->CF = $CF;
    }

    /**
     * @param array $oldVal
     * @param array $newVal
     * @return array
     */
    public function getSumCF(array $oldVal, array $newVal): array {
        foreach ($newVal as $key => $value) {
            $oldVal[$key] += $value;
        }
        return $oldVal;
    }

    /**
     * @param CFInput $parent
     */
    public function setParent(CFInput $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return CFInput|null
     */
    public function getParent(): ?CFInput
    {
        return $this->parent;
    }

    /**
     * @param CFNode $parentNode
     */
    public function setParentNode(CFNode $parentNode): void
    {
        $this->parentNode = $parentNode;
    }

    /**
     * @return CFNode
     */
    public function getParentNode(): CFNode
    {
        return $this->parentNode;
    }
}