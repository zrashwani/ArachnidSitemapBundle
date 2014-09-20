<?php
namespace ArachnidSitemapBundle\Tests;

use \Symfony\Bundle\FrameworkBundle\Console\Application;
use \Symfony\Component\Console\Tester\CommandTester;
use \Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use ArachnidSitemapBundle\Command\GenerateSitemapCommand ;

class ArachnidSitemapCommandTest extends WebTestCase
{
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    public function testActivation()
    {
        $command     = new GenerateSitemapCommand();
        $application = new Application(static::$kernel);

        $command->setApplication($application);
        $commandTester = new CommandTester($command);


        $commandTester->execute(array('command' => $command->getName(),
                                      'base_url'=>'zrashwani.com',
                                      '--links_depth'=>2,
                                      '--use_network'=>true
                                      ));
        $this->assertRegExp('/invalid base url.*/', $commandTester->getDisplay(), 'url format validation failed');


        $commandTester->execute(array('command' => $command->getName(),
                                      'base_url'=>'http://zrashwani.com/',
                                      '--links_depth'=>3,
                                      '--use_network'=>true,
                                      '--frequency' => 'invalid_val'
                                      ));
        $this->assertRegExp('/invalid frequency .*/', $commandTester->getDisplay(), 'frequency value validation failed');


        $commandTester->execute(array('command' => $command->getName(),
                                      'base_url'=>'http://newstest.wewebit.com/',
                                      '--links_depth'=>3,
                                      '--use_network'=>true
                                      ));
        $this->assertRegExp('/sitemap file written .*/', $commandTester->getDisplay(), 'sitemap not written due to some error');

        echo $commandTester->getDisplay();
    }
}
