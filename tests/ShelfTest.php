<?php

class ShelfTest extends PHPUnit_Framework_TestCase
{
    /**
     * Mock for abstract Shelf class
     */
    private $shelf;

    /**
     * Set $shelf property to mock for the abstract Shelf class
     */
    public function setUp() {
        $this->shelf = $this->getMockForAbstractClass('Fuelingtheweb\Shelving\Shelf');
    }

    /**
     * Exception should be thrown in getModelClass
     * when the model property is null
     *
     * @expectedException Exception
     * @expectedExceptionMessage The model property must be a string name of your model class.
     */
    public function testExceptionWhenModelPropIsNull() {
        $this->shelf->getModelClass();
    }
}
