<?php 

/*
* This file is part of the ArachnidSitemap package.
*
* (c) Pablo Díez <zaid_r_86@hotmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace ArachnidSitemapBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;


class ArachnidSitemapBundle extends Bundle{
	/**
	* {@inheritdoc}
	*/
	public function build(ContainerBuilder $container)
	{
		parent::build($container);		
	}	
}