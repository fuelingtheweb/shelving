<?php namespace Fuelingtheweb\Shelving;

abstract class Shelf {

    // Model class name
    protected $model;

    // Query that can be chained onto and then executed
    protected $query;

    // Individual model instance
    protected $instance;

    // Collection of model instances
    protected $items;

    // Classmap for query results
    protected $classmap;

    // Potential traits to use with model
    protected $traits = [
        'Owner'
    ];

    /**
     * Get the individual instance
     * @return mixed
     */
    public function getInstance() {
        return $this->instance;
    }

    /**
     * Get the collection of items
     * @return mixed
     */
    public function getCollection() {
        return $this->items;
    }

    /**
     * Call all method and return collection of items
     */
    public function getAll() {
        return $this->all()->getCollection();
    }

    /**
     * Save a new model and return the instance.
     *
     * @param  array  $attributes
     * @return static
     */
    public function create(array $attributes = []) {
        $instance = $this->getModel()->newInstance($attributes);
        $instance = $this->triggerTraitsOnInsert($instance);
        $instance->save();
        $this->instance = $instance;

        return $this;
    }

    /**
     * Trigger traits on insert
     * @param  $instance
     * @return $instance
     */
    protected function triggerTraitsOnInsert($instance) {
        foreach ($this->traits as $trait) {
            $method = 'add' . $trait . 'OnInsert';
            if (!method_exists($this, $method)) continue;

            $instance = $this->$method($instance);
        }

        return $instance;
    }

    /**
     * Continue chaining the existing query or start a new query
     * @return mixed
     */
    protected function query() {
        if (is_null($this->query)) {
            $this->newQuery();
        }

        return $this->query;
    }

    /**
     * Start a fresh query
     * @return $this
     */
    protected function newQuery() {
        $this->query = $this->getModel();

        return $this;
    }

    /**
     * Get the class name for the model
     * @return string
     */
    protected function getModelClass() {
        if (is_null($this->model)) {
            throw new \Exception('The model property must be a string name of your model class.');
        }

        return $this->model;
    }

    /**
     * Return a newly instantiated model class
     * @return string
     */
    protected function getModel() {
        $class = $this->getModelClass();

        return new $class;
    }

    /**
     * Get classmap property or config
     */
    public function getClassmap() {
        if (isset($this->classmap) && is_array($this->classmap)) {
            return $this->classmap;
        }

        if (function_exists('config') && is_array(config('shelving.classmap'))) {
            return $this->classmap = config('shelving.classmap');
        }

        $config = require(__DIR__ . '/../config/shelving.php');
        return $this->classmap = $config['classmap'];
    }

    /**
     * Get the type name of the query result class
     * @param  string
     * @return string
     */
    protected function getQueryResultType($class) {
        $classmap = $this->getClassmap();

        if (array_key_exists($class, $classmap)) {
            return $classmap[$class];
        }

        return 'value';
    }

    /**
     * Get the class name of the query result
     * @param  mixed
     * @return string
     */
    protected function getQueryResultClassName($object) {
        if ($class = get_parent_class($object)) {
            return $class;
        }

        return get_class($object);
    }

    /**
     * Retrieve attributes on the model instance
     * @param  string $key
     * @return mixed
     */
    public function __get($key) {
        if (is_null($this->instance)) return null;

        return $this->instance->getAttribute($key);
    }

    public function __call($method, $parameters) {
        $this->query = call_user_func_array([$this->query(), $method], $parameters);
        $class = $this->getQueryResultClassName($this->query);

        switch ($this->getQueryResultType($class)) {
            case 'query':
                break;
            case 'collection':
                $this->items = $this->query;
                $this->newQuery();
                break;
            case 'instance':
                $this->instance = $this->query;
                $this->newQuery();
                break;
            default:
                return $this->query;
        }

        return $this;
    }

    public static function __callStatic($method, $parameters) {
        $instance = new static;

        return call_user_func_array([$instance, $method], $parameters);
    }
}
