<?php

namespace CouponURLs\Original\Autoloading;

use CouponURLs\Original\Environment\Env;
use CouponURLs\Original\Exceptions\AutoloaderException;
use Error;

Class Autoloader
{
    protected $fullyQualifiedClassName;
    protected $masterNamespace;

    public static function register()
    {
        spl_autoload_register(function($name){
            try {
                (object) $autoLoader = new self($name);

                $autoLoader->loadClass();
            } catch(Error $exception) {
                throw $exception;
                //throw new AutoloaderException("Error when trying to load class: {$name}. Error message: {$exception->getMessage()} in: {$exception->getFile()}:{$exception->getLine()}\n\n");
            }
        });
    }

    public function __construct($fullyQualifiedClassName)
    {
        (array) $namespaces = explode('\\', $fullyQualifiedClassName);
        (integer) $classNameindex = count($namespaces) - 1;
        (array) $lowerCaseNamespaces = array_map('lcfirst', $namespaces);
        (array) $capitalizedClass = ucfirst(
            $lowerCaseNamespaces[$classNameindex]
        );

        $lowerCaseNamespaces[$classNameindex] = $capitalizedClass;

        $this->fullyQualifiedClassName = implode('\\', $lowerCaseNamespaces);
        $this->masterNamespace = Env::settings()->app->namespace;
    }

    protected function loadClass()
    {
        if ($this->isOurClass()) {
            require_once $this->classFileName();
        }
    }

    protected function isOurClass()
    {
        (boolean) $firstNamespace = 0;

        return strpos(
                   strtolower($this->fullyQualifiedClassName), 
                   strtolower("{$this->masterNamespace}\\")
               ) === $firstNamespace;
    }

    protected function classFileName()
    {
        (string) $classNameWithNoMasterNamespace = substr(
            $this->fullyQualifiedClassName, 
            (strlen($this->masterNamespace) + 1)
        );

        return $this->findPath($classNameWithNoMasterNamespace);

    }

    protected function findPath($classNameWithNoMasterNamespace)
    {
        
        return Env::directory().strtolower(str_replace('\\', '/', $classNameWithNoMasterNamespace).'.php');
    }

    protected function transform($functionName)
    {
        return function ($pieces, $index, $piece) use ($functionName) {
            $pieces[$index] = $functionName($piece);

            return implode('/', $pieces);
        };
    }

}