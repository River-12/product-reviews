<?php

namespace Riverstone\ProductReviews\Model\ResourceModel\ReviewManagement;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @param EntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param AbstractDb $resource
     * @param null|Zend_Db_Adapter_Abstract $connection
     */
    public function __construct(
        EntityFactory           $entityFactory,
        LoggerInterface         $logger,
        FetchStrategyInterface  $fetchStrategy,
        ManagerInterface        $eventManager,
        StoreManagerInterface   $storeManager,
        AbstractDb               $resource = null,
        \Magento\Framework\DB\Adapter\AdapterInterface    $connection = null
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }
    /**
     * Load Model and Resource Model
     *
     * @return void
     * phpcs:disable
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Riverstone\ProductReviews\Model\ReviewManagement', 'Riverstone\ProductReviews\Model\ResourceModel\ReviewManagement');
    }
}
