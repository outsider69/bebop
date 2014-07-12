<?php

namespace Ponticlaro\Bebop\Db\Query;

class ArgFactory {

    /**
     * Holds the class that manufacturables must extend
     */
    const ARG_CLASS = 'Ponticlaro\Bebop\Db\Query\Arg';

    /**
     * List of manufacturable classes
     * 
     * @var array
     */
    protected static $manufacturable = array(
        'author'       => 'Ponticlaro\Bebop\Db\Query\Presets\AuthorArg',
        'cat'          => 'Ponticlaro\Bebop\Db\Query\Presets\CatArg',
        'date'         => 'Ponticlaro\Bebop\Db\Query\Presets\DateArg',
        'ignoresticky' => 'Ponticlaro\Bebop\Db\Query\Presets\IgnoreStickyArg',
        'limit'        => 'Ponticlaro\Bebop\Db\Query\Presets\PostsPerPageArg',
        'maxresults'   => 'Ponticlaro\Bebop\Db\Query\Presets\PostsPerPageArg',
        'meta'         => 'Ponticlaro\Bebop\Db\Query\Presets\MetaArg',
        'metakey'      => 'Ponticlaro\Bebop\Db\Query\Presets\MetaKeyArg',
        'metavalue'    => 'Ponticlaro\Bebop\Db\Query\Presets\MetaValueArg',
        'mime'         => 'Ponticlaro\Bebop\Db\Query\Presets\MimeArg',
        'offset'       => 'Ponticlaro\Bebop\Db\Query\Presets\OffsetArg',
        'orderby'      => 'Ponticlaro\Bebop\Db\Query\Presets\OrderByArg',
        'orderbymeta'  => 'Ponticlaro\Bebop\Db\Query\Presets\OrderByMetaArg',
        'page'         => 'Ponticlaro\Bebop\Db\Query\Presets\ResultsPageArg',
        'paged'        => 'Ponticlaro\Bebop\Db\Query\Presets\ResultsPageArg',
        'parent'       => 'Ponticlaro\Bebop\Db\Query\Presets\ParentArg',
        'post'         => 'Ponticlaro\Bebop\Db\Query\Presets\PostArg',
        'postsperpage' => 'Ponticlaro\Bebop\Db\Query\Presets\PostsPerPageArg',
        'posttype'     => 'Ponticlaro\Bebop\Db\Query\Presets\TypeArg',
        'ppp'          => 'Ponticlaro\Bebop\Db\Query\Presets\PostsPerPageArg',
        'status'       => 'Ponticlaro\Bebop\Db\Query\Presets\StatusArg',
        'tag'          => 'Ponticlaro\Bebop\Db\Query\Presets\TagArg',
        'tax'          => 'Ponticlaro\Bebop\Db\Query\Presets\TaxArg',
        'taxonomy'     => 'Ponticlaro\Bebop\Db\Query\Presets\TaxArg',
    );

    /**
     * Making sure class cannot get instantiated
     */
    protected function __construct() {}

    /**
     * Making sure class cannot get instantiated
     */
    protected function __clone() {}

    /**
     * Adds a new manufacturable class
     * 
     * @param string $type  Object type ID
     * @param string $class Full namespace for a class
     */
    public static function set($type, $class)
    {
        self::$manufacturable[$type] = $class;
    }

    /**
     * Removes a new manufacturable class
     * 
     * @param string $type  Object type ID
     */
    public static function remove($type)
    {
        if (isset(self::$manufacturable[$type])) unset(self::$manufacturable[$type]);
    }

    /**
     * Checks if there is a manufacturable with target key
     * 
     * @param  string  $key Target key
     * @return boolean      True if key exists, false otherwise
     */
    public static function canManufacture($key)
    {
        return is_string($key) && isset(self::$manufacturable[$key]) ? true : false;
    }

    /**
     * Returns the id to manufacture another instance of the passed object, if any
     * 
     * @param  object $instance Arg instance
     * @return string           Arg ID 
     */
    public static function getInstanceId($instance)
    {
        if (is_object($instance)) {

            $class = get_class($instance);
            $id    = array_search($class, self::$manufacturable);

            return $id ?: null;
        }

        return null;
    }

    /**
     * Creates instance of target class
     * 
     * @param  string] $type Class ID
     * @param  array   $args Class arguments
     * @return object        Class instance
     */
    public static function create($type, array $args = array())
    {
        // Check if target is in the allowed list
        if (array_key_exists($type, self::$manufacturable)) {

            $class_name = self::$manufacturable[$type];

            return call_user_func(array(__CLASS__, "__createInstance"), $class_name, $args);
        }

        // Return null if target object is not manufacturable
        return null;
    }

    /**
     * Creates and instance of the target class
     * 
     * @param  string $class_name Target class
     * @param  array  $args       Arguments to pass to target class
     * @return mixed              Class instance or false
     */
    private static function __createInstance($class_name, array $args = array())
    {
        // Get an instance of the target class
        $obj = call_user_func_array(

            array(
                new \ReflectionClass($class_name), 
                'newInstance'
            ), 
            $args
        );
            
        // Return object
        return is_a($obj, self::ARG_CLASS) ? $obj : null;
    }
}