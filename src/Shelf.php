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

    protected $methods = [
        'Illuminate\Database\Eloquent\Builder' => 'query',
        'Illuminate\Database\Eloquent\Collection' => 'collection',
        'Illuminate\Support\Collection' => 'collection',
        'Illuminate\Database\Eloquent\Model' => 'instance'
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
        if (!is_null($this->model)) {
            return $this->model;
        }

        return 'App\\Eloquent\\' . class_basename($this);
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
     * Get the type name of the query result class
     * @param  string
     * @return string
     */
    protected function getQueryResultType($class) {
        if (array_key_exists($class, $this->methods)) {
            return $this->methods[$class];
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
