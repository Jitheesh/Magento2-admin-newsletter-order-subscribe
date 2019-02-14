<?php
/**
 * @category    Jworks
 * @package     Jworks_CheckoutSubscribe
 * @author Jitheesh V O <jitheeshvo@gmail.com>
 * @copyright Copyright (c) 2017 Jworks Digital ()
 */

namespace Jworks\CheckoutSubscribe\Observer\Backend;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class SaveOrderAfterSubmitObserver implements ObserverInterface
{

    /** @var \Magento\Newsletter\Model\SubscriberFactory */
    protected $_subscriberFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $subscription;

    /**
     * @param \Magento\Newsletter\Model\SubscriberFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
    ) {
        $this->_subscriberFactory = $subscriberFactory;
        $this->request = $request;
    }

    /**
     * @param $customerId customer Id
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsSubscribed($customerId)
    {
        return $this->getSubscriptionObject($customerId)->isSubscribed();
    }

    /**
     * Retrieve the subscription object (i.e. the subscriber).
     * @param $customerId customer Id
     * @return \Magento\Newsletter\Model\Subscriber
     */
    public function getSubscriptionObject($customerId)
    {
        if ($this->subscription === null) {
            $this->subscription =
                $this->_subscriberFactory->create()->loadByCustomerId($customerId);
        }

        return $this->subscription;
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order Order */
        $order = $observer->getEvent()->getData('order');
        $isSubscribed = $this->request->getParam('newsletter:subscribe');
        $customerId = $order->getCustomerId();

        if ($isSubscribed !== null && $isSubscribed === 'on') {
            if ($customerId) {
                if (!$this->getIsSubscribed($customerId)) {
                    $this->_subscriberFactory->create()->subscribeCustomerById($customerId);
                }
            } else {
                $email = $order->getCustomerEmail();
                $this->_subscriberFactory->create()->subscribe($email);
            }
        }

        return $this;
    }
}
