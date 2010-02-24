<?php
/**
 * Test Example1 Application Class
 * 
 * This is the Application definition for the application application
 * named 'Example1'. This test application is used only for integration
 * testing.
 */
class Example1_Application extends Asar_Application
{
    protected function setUp() {
        $this->setIndex('Index');
        $this->setMap('/what', 'What');
    }
}
