<?php
/**
 * @category    Jworks
 * @package     Jworks_CheckoutSubscribe
 * @author Jitheesh V O <jitheeshvo@gmail.com>
 * @copyright Copyright (c) 2017 Jworks Digital ()
 */

namespace Jworks\CheckoutSubscribe\Block\Adminhtml\Order\Create\Newsletter;

/**
 * Class Form
 * @package Jworks\CheckoutSubscribe\Block\Adminhtml\Order\Create\Newsletter
 */
class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\Newsletter\Form
{
    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $subscription;

    /**
     * Session quote
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $sessionQuote;
    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sessionQuote = $sessionQuote;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsSubscribed()
    {
        return $this->getSubscriptionObject()->isSubscribed();
    }

    /**
     * Retrieve quote session object
     * @return \Magento\Backend\Model\Session\Quote
     */
    protected function _getSession()
    {
        return $this->sessionQuote;
    }

    /**
     * Retrieve quote model object
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * Retrieve customer identifier
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_getSession()->getCustomerId();
    }

    /**
     * Retrieve the subscription object (i.e. the subscriber).
     * @return \Magento\Newsletter\Model\Subscriber
     */
    public function getSubscriptionObject()
    {
        if ($this->subscription === null) {
            $this->subscription =
                $this->_createSubscriber()->loadByCustomerId($this->getCustomerId());
        }

        return $this->subscription;
    }

    /**
     * Create an instance of a subscriber.
     * @return \Magento\Newsletter\Model\Subscriber
     */
    protected function _createSubscriber()
    {
        return $this->subscriberFactory->create();
    }
}
