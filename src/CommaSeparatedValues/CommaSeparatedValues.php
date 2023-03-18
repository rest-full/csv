<?php

namespace Restfull\CommaSeparatedValues;

use Restfull\Error\Exceptions;
use Restfull\Filesystem\File;

class CommaSeparatedValues extends File
{

    /**
     * @var string
     */
    private $delimiter = ',';

    /**
     * @throws Exceptions
     */
    public function __construct(string $file)
    {
        if (pathinfo($file)['extension'] !== 'csv') {
            throw new Exceptions("this {$file} file is't a csv to be able to extract or import.", 404);
        }
        parent::__construct($file, []);
    }

    public function delimiter(string $delimiter): CommaSeparatedValues
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * @param array $positions
     * @param int $count
     * @param string $mode
     * @return array
     * @throws Exceptions
     */
    public function reading(array $positions, int $count = 0, string $mode = 'r+'): array
    {
        $handle = $this->handle();
        if ($handle === null) {
            $this->create($mode);
            $handle = $this->handle();
        }
        $reading = [];
        $number = 0;
        $positions = $this->checkPositions($positions);
        if ($this->exists()) {
            while ($read = fgetcsv($handle, $count, $this->delimiter)) {
                if ($number >= $positions['initial']) {
                    $reading[] = $this->dataKeysAssociative($read, $positions['columns']);
                }
                $number++;
            }
            $this->close();
        }
        return $reading;
    }

    private function checkPositions(array $positions): array
    {
        if (!isset($positions['columns'], $positions['initial'])) {
            return ['initial' => 1, 'columns' => $positions];
        }
        return $positions;
    }

    /**
     * @param array $data
     * @param string $mode
     * @return bool
     * @throws Exceptions
     */
    public function writing(array $data, string $mode = 'w+'): bool
    {
        $handle = $this->handle();
        if ($handle === null) {
            $this->create($mode);
            $handle = $this->handle();
        }
        $this->checkData($data);
        for ($a = 0; $a < count($data); $a++) {
            if (count($data[$a]) > 0) {
                $data[] = implode(',', $this->dataKeysAssociative($data[$a], []));
            }
        }
        $success = fwrite($this->handle(), implode(PHP_EOL, $data)) !== false;
        $this->close();
        return $success;
    }

    /**
     * @param array $datas
     * @return void
     * @throws Exceptions
     */
    private function checkData(array $datas): void
    {
        if (in_array("array", array_map('gettype', $datas)) === false) {
            throw new Exceptions('this variable $data is not a multidimensional array', 404);
        }
    }

    /**
     * @param array $datas
     * @param array $positions
     * @return array
     */
    private function dataKeysAssociative(array $datas, array $positions = []): array
    {
        $keys = array_keys($datas);
        if (count($keys) > 0) {
            if (count($positions) === 0) {
                $keysAssociative = array_filter($keys, 'is_string');
            }
            $count = count($datas);
            $newDatas = [];
            for ($a = 0; $a < $count; $a++) {
                if (isset($keysAssociative)) {
                    $newDatas[] = in_array(
                        $a,
                        array_keys($keysAssociative)
                    ) !== false ? $keys[$a] . ': ' . $datas[$keys[$a]] : $datas[$keys[$a]];
                } else {
                    if (in_array($a, $positions) !== false) {
                        $data = stripos(
                            $datas[$a],
                            ': '
                        ) !== false ? [$keys[$a] => $datas[$a]] : [$datas[$a]];
                        $newDatas = count($newDatas) > 0 ? array_merge($newDatas, $data) : $data;
                    }
                }
            }
            return $newDatas;
        }

        return $datas;
    }
}