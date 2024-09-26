<?php
namespace Payum\Core\Bridge\PlainPhp\Security;

use League\Uri\Components\HierarchicalPath;
use League\Uri\Components\Path;
use League\Uri\Http as HttpUri;
use League\Uri\UriModifier;
use Payum\Core\Registry\StorageRegistryInterface;
use Payum\Core\Security\AbstractTokenFactory;
use Payum\Core\Storage\StorageInterface;

class TokenFactory extends AbstractTokenFactory
{
    /**
     * @var HttpUri
     */
    protected $baseUrl;

    /**
     * @param StorageInterface         $tokenStorage
     * @param StorageRegistryInterface $storageRegistry
     * @param string                   $baseUrl
     */
    public function __construct(StorageInterface $tokenStorage, StorageRegistryInterface $storageRegistry, $baseUrl = null)
    {
        parent::__construct($tokenStorage, $storageRegistry);

        $this->baseUrl = $baseUrl ? HttpUri::createFromString($baseUrl) : HttpUri::createFromServer($_SERVER);
    }

    /**
     * {@inheritDoc}
     */
    protected function generateUrl($path, array $parameters = [])
    {
        $hierarchicalPath = HierarchicalPath::createFromUri($this->baseUrl);
        if ('php' === pathinfo($hierarchicalPath->getBasename(), PATHINFO_EXTENSION)) {
            $pathComponent = method_exists(Path::class, 'new') ? Path::new($path) : new Path($path);
            $pathComponent = $pathComponent->withoutLeadingSlash();

            $newPath = UriModifier::replaceBasename($this->baseUrl, $pathComponent)->getPath();
        } else {
            $newPath = UriModifier::appendSegment($this->baseUrl, $path)->getPath();
        }

        $uri = $this->baseUrl->withPath($newPath);
        $uri = $this->addQueryToUri($uri, $parameters);

        return (string) $uri;
    }
}
