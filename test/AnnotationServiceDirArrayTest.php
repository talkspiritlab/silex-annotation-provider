<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2014, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\Test\SilexAnnotations;

use DDesrosiers\SilexAnnotations\AnnotationService;
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use DDesrosiers\Test\SilexAnnotations\Controller\TestControllerProvider;
use Doctrine\Common\Cache\ApcCache;
use Silex\Application;

class AnnotationServiceDirArrayTest extends AnnotationDirArrayTestBase
{
    public function testServiceControllerDirArray()
    {        
        $this->assertEndPointStatus(self::GET_METHOD, '/test2/test1', self::STATUS_OK);
    }

    public function testIsolationOfControllerModifiersDirArray()
    {        
        $this->assertEndPointStatus(self::GET_METHOD, '/before/test', self::STATUS_ERROR);
        $this->assertEndPointStatus(self::GET_METHOD, '/before2/test', self::STATUS_ERROR);
        $this->assertEndPointStatus(self::GET_METHOD, '/test2/test1', self::STATUS_OK);
        $this->assertEndPointStatus(self::GET_METHOD, '/test/test1', self::STATUS_OK);
    }

    public function testControllerProviderDirArray()
    {
        $this->app->register(new AnnotationServiceProvider());
        $this->app->mount('/cp', new TestControllerProvider());

        $this->assertEndPointStatus(self::GET_METHOD, '/cp/test', self::STATUS_OK);
    }

    public function cacheTestProvider()
    {
        return array(
            array('Array'),                                // string identifier
            array(new ApcCache()),                         // proper implementation of Cache
            array('Fake', 'RuntimeException'),             // invalid cache string
            array(new InvalidCache(), 'RuntimeException')  // class that does not implement Cache
        );
    }

    /**
     * @dataProvider cacheTestProvider
     */
    public function testCacheDirArray($cache, $exception=null)
    {
        $app = new Application();
        $app['annot.cache'] = $cache;
        try {
            $service = new AnnotationService($app);
            $this->assertInstanceOf("Doctrine\\Common\\Annotations\\CachedReader", $service->getReader());
        } catch (\Exception $e) {
            $this->assertEquals($exception, get_class($e));
        }
    }
}

class InvalidCache
{

}
