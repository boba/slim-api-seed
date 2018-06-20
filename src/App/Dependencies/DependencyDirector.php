<?php
/**
 * PHP version 7
 *
 * Director object to build dependency injection containers
 *
 * DependencyDirector and ContainerBuild implement the Builder design pattern
 *
 * @category Dependencies
 * @package  App\Dependencies
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace App\Dependencies;

use Psr\Container\ContainerInterface;

/**
 * Class DependencyDirector
 *
 * Take an existing container and construct the content.
 *
 * Implements the Director portion of the Builder design pattern
 *
 * @category Dependencies
 * @package  App\Dependencies
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class DependencyDirector
{
    /**
     * The container builder
     *
     * @var ContainerBuilder The container builder
     */
    private $_builder;

    /**
     * The DI Container
     *
     * @var ContainerInterface|mixed
     */
    private $_container;

    /**
     * DependencyDirector constructor.
     *
     * @param ContainerInterface $container DI container
     */
    public function __construct(ContainerInterface $container = null)
    {
        if ($container == null) {
            throw new \InvalidArgumentException("Invalid PSR Container");
        }

        $this->_container = $container;
    }

    /**
     * Set the container builder
     *
     * @param ContainerBuilder $builder Set container builder
     *
     * @return void
     */
    public function setBuilder(ContainerBuilder $builder)
    {
        $this->_builder = $builder;
    }

    /**
     * Get the container builder
     *
     * @return ContainerBuilder The container builder
     */
    protected function getBuilder()
    {
        if (!$this->_builder) {
            $this->_builder = new ContainerBuilder($this->_container);
        }
        return $this->_builder;
    }

    /**
     * Return the container
     *
     * @return ContainerInterface The DI container
     */
    public function getContainer()
    {
        return $this->_container;
    }

    /**
     * Use the builder(s) to populate the container
     *
     * @return ContainerInterface DI container
     */
    public function constructContainer()
    {
        $this->getBuilder()->buildIniDependencies();
        $this->getBuilder()->buildApplicationDependencies();
        $this->getBuilder()->buildLoggingDependencies();
        $this->getBuilder()->buildErrorHandlerDependencies();
        $this->getBuilder()->buildCORSDependencies();

        return $this->getContainer();
    }
}
