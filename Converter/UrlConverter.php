<?php

namespace Pintushi\Bundle\GridBundle\Converter;

use Oro\Bundle\ActionBundle\Helper\DefaultOperationRequestHelper;
use Symfony\Component\Routing\RouterInterface;

/**
 * Converting ajax grid URL to the page URL where grid originally located.
 * Based on URL parameter "originalRoute".
 * Warning: can give incorrect result in a case when parameter "originalRoute" missed in URL!
 */
class UrlConverter
{
    /** @var RouterInterface */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $gridName
     * @param string $url
     *
     * @return string
     */
    public function convertGridUrlToPageUrl(string $gridName, string $url)
    {
        $urlParameters = [];

        $parts = explode('?', $url);
        $baseUrl = $parts[0];
        $urlParametersStr = $parts[1] ?? '';

        parse_str($urlParametersStr, $urlParameters);
        if (empty($urlParameters[$gridName][DefaultOperationRequestHelper::ORIGINAL_ROUTE_URL_PARAMETER_KEY])) {
            return $url;
        }

        $originalRoute = $urlParameters[$gridName][DefaultOperationRequestHelper::ORIGINAL_ROUTE_URL_PARAMETER_KEY];
        $originalRouteUrl = $this->router->generate($originalRoute);
        if ($originalRouteUrl === $baseUrl) {
            return $url;
        }

        $urlParams = [
            $gridName => $urlParameters[$gridName]
        ];
        return $originalRouteUrl . '?' . \http_build_query($urlParams);
    }
}
