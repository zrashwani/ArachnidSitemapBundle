<?php 
namespace ArachnidSitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to write sitemap.xml to the web directory
 * 
 * @author Zeid Rashwani <zaid_r_86@hotmail.com>
 */
class GenerateSitemapCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('arachnid:sitemap:generate')
            ->setDescription('command to generate xml sitemap')            
            ->addArgument('base_url', null, InputArgument::REQUIRED, 'base url used to generate absolute urls, ex. http://www.example.com')
            ->addOption('links_depth',null, InputOption::VALUE_OPTIONAL, 'depth of links to traverse, default is 3', 3)
            ->addOption('sitemap_filename', null, InputOption::VALUE_OPTIONAL, 'name of sitemap file generated, by default sitemap.xml', 'sitemap.xml')
            ->addOption('frequency', null, InputOption::VALUE_OPTIONAL, 'frequency value to be written in the sitemap xml', 'daily')
            ->addOption('use_network', null, InputOption::VALUE_OPTIONAL, 'if set to true, crawler will use crawl base_url itself instead of "/" route pattern');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {       
        
        $depth = $input->getOption('links_depth'); 
        $frequency = $input->getOption('frequency');
        $base_url = $input->getArgument('base_url');

        if(filter_var($base_url,  FILTER_VALIDATE_URL) === false){
            $output->writeln('<error>invalid base url, please make sure its full url; ex. http://example.com</error>');
            return;
        }

        $valid_frequency_arr = array('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never');
        if(in_array($frequency, $valid_frequency_arr) === false){
            $output->writeln("<error>invalid frequency provided, allowed values: ".implode(',', $valid_frequency_arr)."</error>");
            return ;
        }
    
        $output->writeln("<info>begin crawling site</info>");        
        $start_point = ($input->getOption('use_network') !==true?'/':$base_url);

        $crawler = new \Arachnid\Crawler($start_point, $depth); 
        $crawler->traverse();

        $links = $crawler->getLinks();

        $output->writeln("<comment>start generating sitemap file</comment>");
        $dom_doc = $this->getSitemapDocument($output,$links, $frequency);
        $output->writeln("<comment>finished generating sitemap file</comment>");
        $output->writeln("");

        $web_dir = $this->getContainer()->get('kernel')->getRootDir().'/../web/'; 
        $target_file = $web_dir.$input->getOption('sitemap_filename');

        try{
            $dom_doc->save($target_file);
            $output->writeln("<info>sitemap file written to ".$target_file."</info>");
        }catch(Exception $ex){
            $output->writeln("<error>Error: ".$ex->getMessage()." on line ".$ex->getLine()."</error>");
        }
    }

    /**
     * generating DOMDocument object for sitemap.xml based on the links array based
     * @param OutputInterface $output
     * @param array $links array holds links information derived from Arachnide
     * @param string $frequency
     * @return \DOMDocument
     */
    protected function getSitemapDocument(OutputInterface $output, array $links, $frequency){

        $xmlDoc = new \DOMDocument("1.0", "UTF-8");
        $urlset = $xmlDoc->createElement('urlset');

        $xmlns = $xmlDoc->createAttribute('xmlns');
        $xmlns->value = "http://www.sitemaps.org/schemas/sitemap/0.9";

        $xmlns_xsi = $xmlDoc->createAttribute('xmlns:xsi');
        $xmlns_xsi->value = "http://www.w3.org/2001/XMLSchema-instance";

        $xsi_schemaLocation = $xmlDoc->createAttribute('xsi:schemaLocation');
        $xsi_schemaLocation->value = "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd";
        $urlset->appendChild($xmlns);
        $urlset->appendChild($xmlns_xsi);
        $urlset->appendChild($xsi_schemaLocation);

        $output->writeln('<info>Adding links to sitemap:</info>');

        foreach($links as $uri => $link){

            if(isset($link['absolute_url']) === false){
                continue;
            }
            if($link['external_link']===true){ //never add link pointing to other site to sitemap.xml
                continue;
            }

            $url = $xmlDoc->createElement('url');
            $loc = $xmlDoc->createElement('loc', $link['absolute_url']);

            $changefreq = $xmlDoc->createElement('changefreq', $frequency);
            $priority = $xmlDoc->createElement('priority', '1.00');
            $url->appendChild($loc);
            $url->appendChild($changefreq);
            $url->appendChild($priority);
            $urlset->appendChild($url);

            $output->writeln('<comment>'.$link['absolute_url'].'</comment>');
        }


        $xmlDoc->appendChild($urlset);
        
        return $xmlDoc;
    }
}
