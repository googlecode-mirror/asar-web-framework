<?php
class WebSetupExample_Application extends Asar_Application {

    protected function initialize() {
        $this->setMap('/', 'Index');
    }

}
