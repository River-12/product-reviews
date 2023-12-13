<?php

namespace Riverstone\ProductReviews\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    protected const XML_PATH_ENABLED = 'reviews/general/enable';
    protected const XML_PATH_PURCHASE = 'reviews/general/purchase';
    protected const XML_PATH_DEBUG = 'reviews/general/debug';
    protected const XML_PATH_CUSTOMERGROUP = 'reviews/general/customer_group_list';
    /**
     * phpcs:disable
     */
    protected $_logger;
    /**
     * @var ModuleListInterface
     */
    protected $_moduleList;
    /**
     * @var ResourceConnection
     */
    protected $_resource;
    /**
     * @var Order
     */
    protected $order;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectmanager;

    /**
     * @param Context $context
     * @param ModuleListInterface $moduleList
     * @param ResourceConnection $resource
     * @param Session $session
     * @param Order $order
     * @param CollectionFactory $collectionFactory
     * @param ObjectManagerInterface $objectmanager
     */
    public function __construct(Context                $context,
                                ModuleListInterface    $moduleList,
                                ResourceConnection     $resource,
                                Session                $session,
                                Order                  $order,
                                CollectionFactory      $collectionFactory,
                                ObjectManagerInterface $objectmanager)
    {
        $this->_logger = $context->getLogger();
        $this->_moduleList = $moduleList;
        $this->_resource = $resource;
        $this->order = $order;
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
        $this->_objectmanager = $objectmanager;
        parent::__construct($context);
    }
    /**
     * Module Enable/Disable
     *
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }
    /**
     * Ispurchased Enable/Disable
     *
     * @return mixed
     */
    public function isPurchased()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PURCHASE, ScopeInterface::SCOPE_STORE);
    }
    /**
     * Get Customer Groups
     *
     * @return mixed
     */
    public function isCustomerGroups()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMERGROUP, ScopeInterface::SCOPE_STORE);
    }
    /**
     * Get log status
     *
     * @param $message
     * @param $useSeparator
     * @return void
     */
    public function log($message, $useSeparator = false)
    {
        if ($this->getDebugStatus()) {
            if ($useSeparator) {
                $this->_logger->addDebug(str_repeat('=', 100));
            }
            $this->_logger->addDebug($message);
        }
    }
    /**
     * Get Debug Status
     *
     * @return mixed
     */
    public function getDebugStatus()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEBUG, ScopeInterface::SCOPE_STORE);
    }
    /**
     * Get Purchased Product Details
     *
     * @param $product_id
     * @return bool
     */
    public function checkPurchasedProduct($product_id)
    {
        $flag = false;
        $customerSession = $this->_objectmanager->create('Magento\Customer\Model\Session');
        $_dataObject = $customerSession->getCustomerData();
        if (is_object($_dataObject)) {
            $coreOrderModel = $this->_objectmanager->create('Magento\Sales\Model\Order')->getCollection()->addAttributeToFilter('customer_id', $_dataObject->getId());
            if (count($coreOrderModel) > 0) {
                foreach ($coreOrderModel as $order) {
                    $orderItems = $order->getAllItems();
                    foreach ($orderItems as $od) {
                        if ($product_id == $od->getProductId()) {
                            $flag = true;
                            break;
                        }
                    }
                }
            }
        }
        return $flag;
    }
    /**
     * Check Customer Login
     *
     * @return bool
     */
    public function checkLogin()
    {
        $flag = false;
        $customerSession = $this->_objectmanager->create('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            $flag = true;
        }
        return $flag;
    }
}
