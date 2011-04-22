<?php

namespace Asar\Tests\Functional\StatusCodesExample\StatusCodesExample\Resource;

class A500 extends \Asar\Resource {
  public function GET() {
    throw new \Exception('Something is wrong.');
  }
}

