<?php

namespace App\Service;

use App\Enum\ConstantsEnum;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class SmartAssetsHelperService.
 *
 * @category Service
 */
class SmartAssetsHelperService
{
    private KernelInterface $kernel;

    /**
     * @var string mailer URL base
     */
    private string $mub;

    /**
     * Methods.
     */
    public function __construct(KernelInterface $kernel, ContainerBagInterface $containerBag)
    {
        $this->kernel = $kernel;
        $this->mub = $containerBag->get('mailer_url_base');
    }

    /**
     * Determine if this PHP script is executed under a CLI context.
     *
     * @return bool
     */
    public function isDevelEnvironment(): bool
    {
        return ConstantsEnum::SYMFONY_DEV_ENVIRONMENT === $this->kernel->getEnvironment();
    }

    /**
     * Determine if this PHP script is executed under a CLI context.
     *
     * @return bool
     */
    public function isCliContext(): bool
    {
        return ConstantsEnum::PHP_SERVER_API_CLI_CONTEXT === php_sapi_name();
    }

    /**
     * Always return absolute URL path, even in CLI contexts.
     *
     * @param string $assetPath
     *
     * @return string
     */
    public function getAbsoluteAssetPathContextIndependent($assetPath): string
    {
        $package = new UrlPackage(ConstantsEnum::HTTP_PROTOCOL.$this->mub.'/', new EmptyVersionStrategy());

        return $package->getUrl($assetPath);
    }

    /**
     * If is CLI context returns absolute file path, otherwise returns absolute URL path.
     *
     * @param string $assetPath
     *
     * @return string
     */
    public function getAbsoluteAssetPathByContext($assetPath): string
    {
        $result = $this->getAbsoluteAssetPathContextIndependent($assetPath);

        if ($this->isCliContext()) {
            $result = $this->kernel->getProjectDir().DIRECTORY_SEPARATOR.'public'.$assetPath;
        }

        return $result;
    }

    /**
     * Always return relative URL path, even in CLI contexts.
     *
     * @param string $assetPath
     *
     * @return string
     */
    public function getRelativeAssetPathContextIndependent($assetPath): string
    {
        $package = new UrlPackage('/', new EmptyVersionStrategy());

        return $package->getUrl($assetPath);
    }

    /**
     * If is CLI context returns absolute file path, otherwise returns relative URL path.
     *
     * @param string $assetPath
     *
     * @return string
     */
    public function getRelativeAssetPathByContext($assetPath): string
    {
        $result = $this->getRelativeAssetPathContextIndependent($assetPath);

        if ($this->isCliContext()) {
            $result = $this->kernel->getProjectDir().DIRECTORY_SEPARATOR.'public'.$assetPath;
        }

        return $result;
    }

    /**
     * Returns absolute file path.
     *
     * @param string $assetPath
     *
     * @return string
     */
    public function getAbsoluteAssetFilePath($assetPath): string
    {
        return $this->kernel->getProjectDir().DIRECTORY_SEPARATOR.'public'.$assetPath;
    }
}
