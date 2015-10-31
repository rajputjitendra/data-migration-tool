<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Migration\Step\ConfigurablePrices;

use Migration\ResourceModel;
use Migration\Logger\Logger;
use Migration\App\ProgressBar;
use Migration\Reader\MapInterface;

/**
 * Class Integrity
 */
class Integrity extends \Migration\App\Step\AbstractIntegrity
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var ResourceModel\Source
     */
    protected $source;

    /**
     * @var ResourceModel\Destination
     */
    protected $destination;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var ProgressBar\LogLevelProcessor
     */
    protected $progress;

    /**
     * @param Helper $helper
     * @param Logger $logger
     * @param ProgressBar\LogLevelProcessor $progress
     * @param ResourceModel\Source $source
     * @param ResourceModel\Destination $destination
     */
    public function __construct(
        Helper $helper,
        Logger $logger,
        ProgressBar\LogLevelProcessor $progress,
        ResourceModel\Source $source,
        ResourceModel\Destination $destination
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->progress = $progress;
        $this->source = $source;
        $this->destination = $destination;
    }

    /**
     * {@inheritdoc}
     */
    public function perform()
    {
        $this->progress->start($this->getIterationsCount());
        $this->check($this->helper->getSourceFields(), MapInterface::TYPE_SOURCE);
        $this->check($this->helper->getDestinationFields(), MapInterface::TYPE_DEST);
        $this->progress->finish();
        return $this->checkForErrors();
    }

    /**
     * Get iterations count for step
     *
     * @return int
     */
    protected function getIterationsCount()
    {
        return count($this->helper->getSourceFields()) + count($this->helper->getDestinationFields());
    }

    /**
     * @param array $fieldsData
     * @param string $sourceType
     * @return void
     */
    protected function check($fieldsData, $sourceType)
    {
        $source = $this->getResourceModel($sourceType);
        foreach ($fieldsData as $field => $documentName) {
            $this->progress->advance();
            $document = $source->getDocument($documentName);
            $structure = array_keys($document->getStructure()->getFields());
            if (!in_array($field, $structure)) {
                $message = sprintf(
                    '%s table does not contain field: %s',
                    $documentName,
                    $field
                );
                $this->logger->error($message);
            }
        }
    }

    /**
     * @param string $sourceType
     * @return ResourceModel\Destination|ResourceModel\Source
     */
    protected function getResourceModel($sourceType)
    {
        return ($sourceType == MapInterface::TYPE_SOURCE) ? $this->source : $this->destination;
    }
}
