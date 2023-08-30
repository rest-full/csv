<?php

namespace Restfull\CommaSeparatedValues;

use Restfull\Error\Exceptions;
use Restfull\Filesystem\File;

/**
 *
 */
class CommaSeparatedValues extends File
{

    /**
     * @var string
     */
    private $delimiter = ',';

    /**
     * @var array
     */
    private $reading = [];

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

    /**
     * @param string $delimiter
     * @return $this
     */
    public function delimiter(string $delimiter): CommaSeparatedValues
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * @param array $positions
     * @param int $length
     * @param string $mode
     * @return array
     * @throws Exceptions
     */
    public function reading(array $positions, string $mode = 'r+'): array
    {
        $handle = $this->handle();
        if ($handle === null) {
            $this->create($mode);
            $handle = $this->handle();
        }
        if (!isset($positions['columns'], $positions['initial'], $positions['associative'])) {
            $positions = ['initial' => 1, 'columns' => $positions, 'associative' => true];
        }
        if ($this->exists()) {
            while ($read = fgetcsv($handle, 0, $this->delimiter)) {
                if ($number >= $positions['initial']) {
                    $datas = $this->datasPositions($read, array_values($positions['columns']));
                    $this->alignDatas(
                        $datas,
                        $positions['columns'],
                        ['associative' => $positions['associative'], 'comparation' => $positions['comparation']]
                    );
                }
            }
            $this->close();
        }
        return $this->reading;
    }

    /**
     * @param array $datas
     * @param array $positions
     * @return array
     */
    private function datasPositions(array $datas, array $positions): array
    {
        $keys = array_keys($datas);
        if (count($keys) > 0) {
            $count = count($datas);
            $newDatas = [];
            for ($a = 0; $a < $count; $a++) {
                if (in_array($a, $positions) !== false) {
                    $newDatas = count($newDatas) > 0 ? array_merge($newDatas, [$datas[$a]]) : [$datas[$a]];
                }
            }
            return $newDatas;
        }
        return $datas;
    }

    private function alignDatas(array $datas, array $positions, array $other): CommaSeparatedValues
    {
        if ($other['associative']) {
            foreach (array_keys($positions) as $position => $key) {
                $data = $datas[$position];
                if (isset($other['comparation'][$key])) {
                    $data = $other['comparation'][$key][$data];
                }
                $this->reading[$key][] = $data;
            }
        } else {
            $this->reading[] = $datas;
        }
        return $this;
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
                $data[] = implode($this->delimiter, $data[$a]);
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
}