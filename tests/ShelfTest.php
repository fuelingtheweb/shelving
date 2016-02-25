<?php

class ShelfTest extends PHPUnit_Framework_TestCase
{
    /**
     * Mock for abstract Shelf class
     */
    private $shelf;

    /**
     * Model class name to be used
     * for setting model property of $shelf
     * and mocking the model class
     */
    private $modelClass = 'SomeClass';

    /**
     * Set $shelf property to mock for the abstract Shelf class
     * Add model property
     * Mock the model class
     */
    public function setUp() {
        $this->shelf = $this->getMockForAbstractClass('Fuelingtheweb\Shelving\Shelf');
        $this->setProperty('model', $this->modelClass);
        $this->getMockBuilder($this->modelClass)->getMock();
    }

    /**
     * Set property of mocked Shelf
     */
    private function setProperty($name, $value) {
        $r = new ReflectionClass(get_class($this->shelf));
        $prop = $r->getProperty($name);
        $prop->setAccessible(true);
        $prop->setValue($this->shelf, $value);
        $prop->setAccessible(false);
    }

    /**
     * Invoke protected or private method on $shelf
     */
    private function invokeMethod($name, $args = []) {
        $r = new ReflectionClass(get_class($this->shelf));
        $method = $r->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($this->shelf, $args);
    }

    /**
     * Exception should be thrown in getModelClass
     * when the model property is null
     *
     * @expectedException Exception
     * @expectedExceptionMessage The model property must be a string name of your model class.
     */
    public function testExceptionWhenModelPropIsNull() {
        $this->setProperty('model', null);
        $this->invokeMethod('getModelClass');
    }

    /**
     * A new instance of the model class name should be returned
     */
    public function testGetModelReturnsNewClass() {
        $model = $this->invokeMethod('getModel');

        $this->assertInstanceOf($this->modelClass, $model);
    }
}
