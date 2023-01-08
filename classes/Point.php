<?php

class Point {
    private string $name;
    private array $data;
    public function __construct($elem, $paramsNum) {
        $this->name = $elem['name'];
        $this->data = $this->normalizeData($elem, $paramsNum);
    }
    private function normalizeData($elem, $paramsNum): array {
        $creditHistory = [
            'Нет' => 0,
            'Есть' => 1
        ];
        $maritalStatus = [
            'Холост' => 0,
            'Женат' => 1
        ];
        $education = [
            'Среднее' => 0,
            'Высшее' => 1,
            'Уч. степень' => 2
        ];
        $data = [];
        foreach ($elem as $key => $value) {
            if ($key == 'name')
                continue;
            switch ($key) {
                case 'credit_history':
                    $data[] = $creditHistory[$value];
                    break;
                case 'marital_status':
                    $data[] = $maritalStatus[$value];
                    break;
                case 'education':
                    $data[] = $education[$value];
                    break;
                case 'income':
                    $data[] = $value / 1000;
                    break;
                case 'age':
                    $data[] = $value / 100;
            }
            if (count($data) == $paramsNum)
                break;
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}