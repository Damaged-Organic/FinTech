<?php
// AppBundle/Controller/Traits/ClassOperationsTrait.php
namespace AppBundle\Controller\Utility\Traits;

use ReflectionClass;

trait ClassOperationsTrait
{
    public function getObjectClassName($object)
    {
        if( !is_object($object) )
            return FALSE;

        return $objectClassName = (new ReflectionClass($object))->getShortName();
    }

    public function getObjectInstanceFromString($string, $namespace)
    {
        if( !is_string($string) )
            return FALSE;

        $string = ucfirst($string);

        $class = "{$namespace}\\{$string}";

        return new $class;
    }

    public function getObjectClassNameLower($object)
    {
        return mb_strtolower(
            $this->getObjectClassName($object), 'UTF-8'
        );
    }

    public function compareObjectClassNameToString($object, $string)
    {
        $objectClassName = $this->getObjectClassName($object);

        return (strtolower($objectClassName) === strtolower($string));
    }

    public function compareObjectToStringInstance($object, $string, $namespace)
    {
        $class = $this->getObjectInstanceFromString($string, $namespace);

        return ($class instanceof $object);
    }
}
