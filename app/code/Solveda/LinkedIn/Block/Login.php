<?php
namespace Solveda\LinkedIn\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Solveda\LinkedIn\Model\LinkedInData;

class Login extends Template
{
    protected $_linkedInData;
    protected $_customerSession;

    public function __construct(
        Template\Context $context,
        LinkedInData $linkedInData,
        Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_linkedInData = $linkedInData;
        $this->_customerSession = $customerSession;
    }

    public function getLinkedInLoginUrl()
    {
        return $this->getUrl('linkedin/account/login', ['_secure' => $this->getRequest()->isSecure()]);
    }

    public function isLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }
}
