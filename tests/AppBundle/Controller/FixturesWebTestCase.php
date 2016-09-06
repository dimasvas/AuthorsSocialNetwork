<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

#http://stackoverflow.com/questions/14752930/best-way-to-create-a-test-database-and-load-fixtures-on-symfony-2-webtestcase?rq=1
class FixturesWebTestCase extends WebTestCase
{
    protected static $application;

    protected function setUp()
    {
        self::runCommand('doctrine:schema:drop --env=test --force');
        self::runCommand('doctrine:schema:create --env=test --no-interaction');
        self::runCommand('doctrine:fixtures:load --env=test --no-interaction --fixtures=tests/DataFixtures/ORM');
    }

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }
    
    public function testNoWarning()
    {
        $this->assertTrue(true);
    }
}
