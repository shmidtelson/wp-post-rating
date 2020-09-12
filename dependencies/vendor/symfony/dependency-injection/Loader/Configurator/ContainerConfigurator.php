<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use WPR\Vendor\Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use WPR\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use WPR\Vendor\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use WPR\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use WPR\Vendor\Symfony\Component\DependencyInjection\Definition;
use WPR\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use WPR\Vendor\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use WPR\Vendor\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use WPR\Vendor\Symfony\Component\ExpressionLanguage\Expression;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ContainerConfigurator extends \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator
{
    const FACTORY = 'container';
    private $container;
    private $loader;
    private $instanceof;
    private $path;
    private $file;
    private $anonymousCount = 0;
    public function __construct(\WPR\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder $container, \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\PhpFileLoader $loader, array &$instanceof, string $path, string $file)
    {
        $this->container = $container;
        $this->loader = $loader;
        $this->instanceof =& $instanceof;
        $this->path = $path;
        $this->file = $file;
    }
    public final function extension(string $namespace, array $config)
    {
        if (!$this->container->hasExtension($namespace)) {
            $extensions = \array_filter(\array_map(function (\WPR\Vendor\Symfony\Component\DependencyInjection\Extension\ExtensionInterface $ext) {
                return $ext->getAlias();
            }, $this->container->getExtensions()));
            throw new \WPR\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('There is no extension able to load the configuration for "%s" (in "%s"). Looked for namespace "%s", found "%s".', $namespace, $this->file, $namespace, $extensions ? \implode('", "', $extensions) : 'none'));
        }
        $this->container->loadFromExtension($namespace, static::processValue($config));
    }
    public final function import(string $resource, string $type = null, $ignoreErrors = \false)
    {
        $this->loader->setCurrentDir(\dirname($this->path));
        $this->loader->import($resource, $type, $ignoreErrors, $this->file);
    }
    public final function parameters() : \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator
    {
        return new \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator($this->container);
    }
    public final function services() : \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator
    {
        return new \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator($this->container, $this->loader, $this->instanceof, $this->path, $this->anonymousCount);
    }
    /**
     * @return static
     */
    public final function withPath(string $path) : self
    {
        $clone = clone $this;
        $clone->path = $clone->file = $path;
        return $clone;
    }
}
/**
 * Creates a service reference.
 *
 * @deprecated since Symfony 5.1, use service() instead.
 */
function ref(string $id) : \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator
{
    trigger_deprecation('symfony/dependency-injection', '5.1', '"%s()" is deprecated, use "service()" instead.', __FUNCTION__);
    return new \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator($id);
}
/**
 * Creates a reference to a service.
 */
function service(string $serviceId) : \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator
{
    return new \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator($serviceId);
}
/**
 * Creates an inline service.
 *
 * @deprecated since Symfony 5.1, use inline_service() instead.
 */
function inline(string $class = null) : \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator
{
    trigger_deprecation('symfony/dependency-injection', '5.1', '"%s()" is deprecated, use "inline_service()" instead.', __FUNCTION__);
    return new \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator(new \WPR\Vendor\Symfony\Component\DependencyInjection\Definition($class));
}
/**
 * Creates an inline service.
 */
function inline_service(string $class = null) : \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator
{
    return new \WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator(new \WPR\Vendor\Symfony\Component\DependencyInjection\Definition($class));
}
/**
 * Creates a service locator.
 *
 * @param ReferenceConfigurator[] $values
 */
function service_locator(array $values) : \WPR\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument
{
    return new \WPR\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument(\WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator::processValue($values, \true));
}
/**
 * Creates a lazy iterator.
 *
 * @param ReferenceConfigurator[] $values
 */
function iterator(array $values) : \WPR\Vendor\Symfony\Component\DependencyInjection\Argument\IteratorArgument
{
    return new \WPR\Vendor\Symfony\Component\DependencyInjection\Argument\IteratorArgument(\WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator::processValue($values, \true));
}
/**
 * Creates a lazy iterator by tag name.
 */
function tagged_iterator(string $tag, string $indexAttribute = null, string $defaultIndexMethod = null, string $defaultPriorityMethod = null) : \WPR\Vendor\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument
{
    return new \WPR\Vendor\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument($tag, $indexAttribute, $defaultIndexMethod, \false, $defaultPriorityMethod);
}
/**
 * Creates a service locator by tag name.
 */
function tagged_locator(string $tag, string $indexAttribute = null, string $defaultIndexMethod = null) : \WPR\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument
{
    return new \WPR\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument(new \WPR\Vendor\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument($tag, $indexAttribute, $defaultIndexMethod, \true));
}
/**
 * Creates an expression.
 */
function expr(string $expression) : \WPR\Vendor\Symfony\Component\ExpressionLanguage\Expression
{
    return new \WPR\Vendor\Symfony\Component\ExpressionLanguage\Expression($expression);
}
