<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Symfony\Component\DependencyInjection\Compiler;

use WPR\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
/**
 * Compiler Pass Configuration.
 *
 * This class has a default configuration embedded.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class PassConfig
{
    const TYPE_AFTER_REMOVING = 'afterRemoving';
    const TYPE_BEFORE_OPTIMIZATION = 'beforeOptimization';
    const TYPE_BEFORE_REMOVING = 'beforeRemoving';
    const TYPE_OPTIMIZE = 'optimization';
    const TYPE_REMOVE = 'removing';
    private $mergePass;
    private $afterRemovingPasses = [];
    private $beforeOptimizationPasses = [];
    private $beforeRemovingPasses = [];
    private $optimizationPasses;
    private $removingPasses;
    public function __construct()
    {
        $this->mergePass = new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass();
        $this->beforeOptimizationPasses = [100 => [new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveClassPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveInstanceofConditionalsPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\RegisterEnvVarProcessorsPass()], -1000 => [new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ExtensionCompilerPass()]];
        $this->optimizationPasses = [[new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\AutoAliasServicePass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ValidateEnvPlaceholdersPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveDecoratorStackPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveChildDefinitionsPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\RegisterServiceSubscribersPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveParameterPlaceHoldersPass(\false, \false), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveFactoryClassPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveNamedArgumentsPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\AutowireRequiredMethodsPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\AutowireRequiredPropertiesPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveBindingsPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\DecoratorServicePass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\CheckDefinitionValidityPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\AutowirePass(\false), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveTaggedIteratorArgumentPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveServiceSubscribersPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveReferencesToAliasesPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveInvalidReferencesPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\AnalyzeServiceReferencesPass(\true), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\CheckCircularReferencesPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\CheckReferenceValidityPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\CheckArgumentsValidityPass(\false)]];
        $this->beforeRemovingPasses = [-100 => [new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolvePrivatesPass()]];
        $this->removingPasses = [[new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\RemovePrivateAliasesPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ReplaceAliasByActualDefinitionPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\RemoveAbstractDefinitionsPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\RemoveUnusedDefinitionsPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\InlineServiceDefinitionsPass(new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\AnalyzeServiceReferencesPass()), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\AnalyzeServiceReferencesPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\DefinitionErrorExceptionPass()]];
        $this->afterRemovingPasses = [[new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\CheckExceptionOnInvalidReferenceBehaviorPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveHotPathPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveNoPreloadPass(), new \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\AliasDeprecatedPublicServicesPass()]];
    }
    /**
     * Returns all passes in order to be processed.
     *
     * @return CompilerPassInterface[]
     */
    public function getPasses()
    {
        return \array_merge([$this->mergePass], $this->getBeforeOptimizationPasses(), $this->getOptimizationPasses(), $this->getBeforeRemovingPasses(), $this->getRemovingPasses(), $this->getAfterRemovingPasses());
    }
    /**
     * Adds a pass.
     *
     * @throws InvalidArgumentException when a pass type doesn't exist
     */
    public function addPass(\WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface $pass, string $type = self::TYPE_BEFORE_OPTIMIZATION, int $priority = 0)
    {
        $property = $type . 'Passes';
        if (!isset($this->{$property})) {
            throw new \WPR\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid type "%s".', $type));
        }
        $passes =& $this->{$property};
        if (!isset($passes[$priority])) {
            $passes[$priority] = [];
        }
        $passes[$priority][] = $pass;
    }
    /**
     * Gets all passes for the AfterRemoving pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getAfterRemovingPasses()
    {
        return $this->sortPasses($this->afterRemovingPasses);
    }
    /**
     * Gets all passes for the BeforeOptimization pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getBeforeOptimizationPasses()
    {
        return $this->sortPasses($this->beforeOptimizationPasses);
    }
    /**
     * Gets all passes for the BeforeRemoving pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getBeforeRemovingPasses()
    {
        return $this->sortPasses($this->beforeRemovingPasses);
    }
    /**
     * Gets all passes for the Optimization pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getOptimizationPasses()
    {
        return $this->sortPasses($this->optimizationPasses);
    }
    /**
     * Gets all passes for the Removing pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getRemovingPasses()
    {
        return $this->sortPasses($this->removingPasses);
    }
    /**
     * Gets the Merge pass.
     *
     * @return CompilerPassInterface
     */
    public function getMergePass()
    {
        return $this->mergePass;
    }
    public function setMergePass(\WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface $pass)
    {
        $this->mergePass = $pass;
    }
    /**
     * Sets the AfterRemoving passes.
     *
     * @param CompilerPassInterface[] $passes
     */
    public function setAfterRemovingPasses(array $passes)
    {
        $this->afterRemovingPasses = [$passes];
    }
    /**
     * Sets the BeforeOptimization passes.
     *
     * @param CompilerPassInterface[] $passes
     */
    public function setBeforeOptimizationPasses(array $passes)
    {
        $this->beforeOptimizationPasses = [$passes];
    }
    /**
     * Sets the BeforeRemoving passes.
     *
     * @param CompilerPassInterface[] $passes
     */
    public function setBeforeRemovingPasses(array $passes)
    {
        $this->beforeRemovingPasses = [$passes];
    }
    /**
     * Sets the Optimization passes.
     *
     * @param CompilerPassInterface[] $passes
     */
    public function setOptimizationPasses(array $passes)
    {
        $this->optimizationPasses = [$passes];
    }
    /**
     * Sets the Removing passes.
     *
     * @param CompilerPassInterface[] $passes
     */
    public function setRemovingPasses(array $passes)
    {
        $this->removingPasses = [$passes];
    }
    /**
     * Sort passes by priority.
     *
     * @param array $passes CompilerPassInterface instances with their priority as key
     *
     * @return CompilerPassInterface[]
     */
    private function sortPasses(array $passes) : array
    {
        if (0 === \count($passes)) {
            return [];
        }
        \krsort($passes);
        // Flatten the array
        return \array_merge(...$passes);
    }
}
