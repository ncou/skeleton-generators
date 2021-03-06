<?php
declare(strict_types=1);
namespace Narrowspark\Skeleton\Generator;

final class MicroGenerator extends ConsoleGenerator
{
    /**
     * {@inheritdoc}
     */
    public function getSkeletonType(): string
    {
        return 'micro';
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return \array_merge(
            parent::getDependencies(),
            [
                'cakephp/chronos'          => '^1.0.4',
                'narrowspark/http-emitter' => '^0.7.0',
                'viserio/http-foundation'  => 'dev-master',
                'viserio/view'             => 'dev-master',
            ]
        );
    }

    /**
     * Returns all directories that should be generated.
     *
     * @return string[]
     */
    protected function getDirectories(): array
    {
        return \array_merge(
            parent::getDirectories(),
            [
                $this->folderPaths['public'],
                $this->folderPaths['tests'] . \DIRECTORY_SEPARATOR . 'Feature',
                $this->folderPaths['resources'],
                $this->folderPaths['views'],
                $this->folderPaths['app'] . \DIRECTORY_SEPARATOR . 'Http',
                $this->folderPaths['app'] . \DIRECTORY_SEPARATOR . 'Http' . \DIRECTORY_SEPARATOR . 'Controller',
                $this->folderPaths['app'] . \DIRECTORY_SEPARATOR . 'Http' . \DIRECTORY_SEPARATOR . 'Middleware',
                $this->folderPaths['app'] . \DIRECTORY_SEPARATOR . 'Http' . \DIRECTORY_SEPARATOR . 'Bootstrap',
            ]
        );
    }

    /**
     * Returns all files that should be generated.
     *
     * @return array
     */
    protected function getFiles(): array
    {
        $httpPath = $this->folderPaths['app'] . \DIRECTORY_SEPARATOR . 'Http' . \DIRECTORY_SEPARATOR;

        $files = \array_merge(
            parent::getFiles(),
            [
                $this->folderPaths['routes'] . \DIRECTORY_SEPARATOR . 'api.php'            => '<?php' . \PHP_EOL . 'declare(strict_types=1);' . \PHP_EOL . \PHP_EOL . '/** @var \Viserio\Component\Routing\Router $router */' . \PHP_EOL,
                $this->folderPaths['routes'] . \DIRECTORY_SEPARATOR . 'web.php'            => $this->getWebRoutes(),
                $httpPath . 'Kernel.php'                                                   => $this->getHttpKernelClass(),
                $httpPath . 'Controller' . \DIRECTORY_SEPARATOR . 'AbstractController.php' => $this->getControllerClass(),
                $httpPath . 'Bootstrap' . \DIRECTORY_SEPARATOR . 'LoadRoutes.php'          => $this->getLoadRoutesClass(),
                $this->folderPaths['public'] . \DIRECTORY_SEPARATOR . 'index.php'          => $this->getIndexFile(),
                $this->folderPaths['views'] . \DIRECTORY_SEPARATOR . 'welcome.php'         => $this->getWelcomeFile(),
            ]
        );

        if (! static::$isTest) {
            $files['phpunit.xml'] = $this->getPhpunitXmlContent();
        }

        return $files;
    }

    /**
     * Get the phpunit.xml content.
     *
     * @return string
     */
    protected function getPhpunitXmlContent(): string
    {
        $phpunitContent = (string) \file_get_contents($this->resourcePath . \DIRECTORY_SEPARATOR . 'phpunit.xml.stub');
        $feature        = "        <testsuite name=\"Feature\">\n            <directory suffix=\"Test.php\">./tests/Feature</directory>\n        </testsuite>\n";

        return $this->doInsertStringBeforePosition($phpunitContent, $feature, (int) \mb_strpos($phpunitContent, '</testsuites>'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getBootstrapContent(): string
    {
        return '<?php' . \PHP_EOL . 'declare(strict_types=1);' . \PHP_EOL . \PHP_EOL . 'return [' . \PHP_EOL . '    /** > app/bootstrap **/' . \PHP_EOL . '    \App\Console\Bootstrap\LoadConsoleCommand::class => [\'console\'],' . \PHP_EOL . '    \App\Http\Bootstrap\LoadRoutes::class => [\'http\'],' . \PHP_EOL . '    /** app/bootstrap < **/' . \PHP_EOL . '];' . \PHP_EOL;
    }

    /**
     * Insert string at specified position.
     *
     * @param string $string
     * @param string $insertStr
     * @param int    $position
     *
     * @return string
     */
    private function doInsertStringBeforePosition(string $string, string $insertStr, int $position): string
    {
        return \mb_substr($string, 0, $position) . $insertStr . \mb_substr($string, $position);
    }

    /**
     * Get the http kernel class.
     *
     * @return string
     */
    private function getHttpKernelClass(): string
    {
        return (string) \file_get_contents($this->resourcePath . \DIRECTORY_SEPARATOR . 'HttpKernel.php.stub');
    }

    /**
     * Get the controller class.
     *
     * @return string
     */
    private function getControllerClass(): string
    {
        return (string) \file_get_contents($this->resourcePath . \DIRECTORY_SEPARATOR . 'AbstractController.php.stub');
    }

    /**
     * Get the load routes bootstrap class.
     *
     * @return string
     */
    private function getLoadRoutesClass(): string
    {
        return (string) \file_get_contents($this->resourcePath . \DIRECTORY_SEPARATOR . 'LoadRoutes.php.stub');
    }

    /**
     * Get the web file content.
     *
     * @return string
     */
    private function getWebRoutes(): string
    {
        return (string) \file_get_contents($this->resourcePath . \DIRECTORY_SEPARATOR . 'web.php.stub');
    }

    /**
     * Get the index file content.
     *
     * @return string
     */
    private function getIndexFile(): string
    {
        return (string) \file_get_contents($this->resourcePath . \DIRECTORY_SEPARATOR . 'index.php.stub');
    }

    /**
     * Get the welcome file content.
     *
     * @return string
     */
    private function getWelcomeFile(): string
    {
        return (string) \file_get_contents($this->resourcePath . \DIRECTORY_SEPARATOR . 'welcome.php.stub');
    }
}
