<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Migration\Handler;

use Migration\Resource\Record;

/**
 * Handler to transform field according to the map
 */
class Convert implements HandlerInterface
{
    /**
     * @var string
     */
    protected $map = [];

    /**
     * @param string $map
     * @throws \Exception
     */
    public function __construct($map)
    {
        $map = rtrim($map, ']');
        $map = ltrim($map, '[');
        $map = explode(';', $map);
        $resultMap =[];
        foreach ($map as $mapRecord) {
            $explodedRecord = explode(':', $mapRecord);
            if (count($explodedRecord) != 2) {
                throw new \Exception('Invalid map provided to convert handler');
            }
            list($key, $value) = $explodedRecord;
            $resultMap[$key] = $value;
        }
        $this->map = $resultMap;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Record $record, $fieldName)
    {
        $value = $record->getValue($fieldName);
        if (isset($this->map[$value])) {
            $value = $this->map[$value];
        }
        $record->setValue($fieldName, $value);
    }
}
