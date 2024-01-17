<?php
namespace Solveda\LinkedIn\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_LINKEDIN_SECTION = 'linkedin/api_credentials/';

    /**
     * Get LinkedIn API Key
     *
     * @param int|null $storeId
     * @return string
     */
    public function getLinkedInClientId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LINKEDIN_SECTION . 'api_key',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get LinkedIn API Secret
     *
     * @param int|null $storeId
     * @return string
     */
    public function getLinkedInClientSecret($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LINKEDIN_SECTION . 'api_secret',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get LinkedIn OAuth URL
     *
     * @param int|null $storeId
     * @return string
     */
    public function getLinkedInRedirectUri($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LINKEDIN_SECTION . 'oauth_url',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
