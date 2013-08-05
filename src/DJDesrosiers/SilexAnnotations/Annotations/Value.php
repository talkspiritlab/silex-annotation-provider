<?php 

namespace DJDesrosiers\SilexAnnotations\Annotations;

use DJDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Value implements RouteAnnotation
{
	/** @var string */
	public $variable;

	/** @var mixed */
	public $default;
	
	public function process(\Silex\Route $route)
	{
		$route->value($this->variable, $this->default);
	}
}