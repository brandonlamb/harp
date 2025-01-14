<?php

namespace Harp\Harp\Repo;

use Harp\Harp\Repo;

/**
 * A dependancy injection container for Repo objects
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Container
{
    /**
     * Holds all the singleton repo instances.
     * Use the name of the class as array key.
     *
     * @var array
     */
    private static $repos;

    /**
     * @var array
     */
    private static $actualClasses;

    /**
     * @param  string $class
     * @return Repo
     */
    public static function get($class)
    {
        if (! self::has($class)) {
            if (self::hasActualClass($class)) {
                $actualClass = self::getActualClass($class);

                if (self::has($actualClass)) {
                    $repo = self::get($actualClass);
                } else {
                    $repo = $actualClass::newRepo($actualClass);
                }

                self::set($actualClass, $repo);
            } else {
                $repo = $class::newRepo($class);
            }

            self::set($class, $repo);
        }

        return self::$repos[$class];
    }

    /**
     * @param string $class
     * @param Repo   $repo
     */
    public static function set($class, Repo $repo)
    {
        self::$repos[$class] = $repo;
    }

    /**
     * @param string $class
     * @param string $alias
     */
    public static function setActualClass($class, $alias)
    {
        self::$actualClasses[$class] = $alias;
    }

    /**
     * Set multiple actual classes at once. [class => actual class]
     *
     * @param array $actual
     */
    public static function setActualClasses(array $actual)
    {
        foreach ($actual as $class => $actualClass) {
            self::setActualClass($class, $actualClass);
        }
    }

    /**
     * @param  string $class
     * @return string
     */
    public static function getActualClass($class)
    {
        return self::$actualClasses[$class];
    }

    /**
     * @param  string  $class
     * @return boolean
     */
    public static function hasActualClass($class)
    {
        return isset(self::$actualClasses[$class]);
    }

    /**
     * @param  string  $class
     * @return boolean
     */
    public static function has($class)
    {
        return isset(self::$repos[$class]);
    }

    public static function clear()
    {
        self::$repos = [];
        self::$actualClasses = [];
    }
}
