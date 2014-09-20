# ArachnidSitemapBundle
Symfony2 bundle that help you generate sitemap.xml via traversing internal site urls dynamically.

This bundle uses [Arachnid Web Crawler](https://github.com/codeguy/arachnid) to extract site urls and use them to build sitemap.xml file.

## How to Install
You can install this bundle via [composer](http://getcomposer.org). Add the following to the "require" section of composer.json:

   "zrashwani/arachnid-sitemap-bundle": "dev-master"

then run `composer update`

## Adding the bundle to your kernel
To enable the sitemap bundle, add it to your kernel registerBundles() method:

    use Symfony\Foundation\Kernel;

    class MyKernel extends Kernel {
        // ...
        public function registerBundles() {
            return array(
                // ...
                new Zrashwani\ArachnidSitemapBundle(),
                // ...
            );
        }
    }


## Running sitemap generation command
The sitemap generation is implemented as symfony2 command that can be called as following:

php app/console arachnid:sitemap:generate http://your-base-url.com/


Optional parameters can be used to add simple customization on sitemap.xml contents and crawler behaviour
* use `--links_depth` to determine to which links level the crawler will operate, default: 3
* use `--sitemap_filename` to determine name of sitemap file to write, default: sitemap.xml
* use `--frequency` to specify default <changefreq> for all links
* use `--use_network` to let the crawler operate of the same base url, instead of "/" route pattern; this option may slow down the process of generation because crawler treats site as remote resource not local one.

make sure your `sitemap_filename` is writable to your web server so the command can place sitemap contents correctly.

## How to Contribute

1. Fork this repository
2. Create a new branch for each feature or improvement
3. Send a pull request from each feature branch

It is very important to separate new features or improvements into separate feature branches,
and to send a pull request for each branch. This allows me to review and pull in new features
or improvements individually.

All pull requests must adhere to the [PSR-2 standard][psr2].

