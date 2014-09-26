<?php
namespace Zrashwani\ArachnidSitemapBundle\Tests;

use \Symfony\Bundle\FrameworkBundle\Console\Application;
use \Symfony\Component\Console\Tester\CommandTester;
use \Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Zrashwani\ArachnidSitemapBundle\Command\GenerateSitemapCommand ;

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
                                      ));
        $this->assertRegExp('/invalid base url.*/', $commandTester->getDisplay(), 'url format validation failed');
        

        $commandTester->execute(array('command' => $command->getName(),
                                      'base_url'=>'http://zrashwani.com/',
                                      '--links_depth'=>3,                                      
                                      '--frequency' => 'invalid_val'
                                      ));
        $this->assertRegExp('/invalid frequency .*/', $commandTester->getDisplay(), 'frequency value validation failed');
        

        $commandTester->execute(array('command' => $command->getName(),
                                      'base_url'=>'http://zrashwani.com/',
                                      '--links_depth'=>5,
                                      '--frequency' => 'daily',                                      
                                      ));
        $this->assertRegExp("/sitemap file written .*/", $commandTester->getDisplay(), 'sitemap not written due to some error');
        $this->assertRegExp("/(([0-9])*[1-9]+) links found.*/", $commandTester->getDisplay(), 'number of links found failed due to some error');
        

        $commandTester->execute(array('command' => $command->getName(),
                                      'base_url'=>'http://zrashwani.com/', //test default values
                                      ));
        $this->assertRegExp('/sitemap file written .*/', $commandTester->getDisplay(), 'sitemap not written due to some error');        
        $this->assertRegExp("/(([0-9])*[1-9]+) links found.*/", $commandTester->getDisplay(), 'number of links found failed due to some error');

        
    }
}
