<?php

namespace Asar\Tests\Functional\StatusCodesExample\StatusCodesExample\Resource;

/**
 * @note due to classname limitations in PHP, we cant have classnames that
 * start with a number.
 */
class A405 extends \Asar\Resource {
  
  function GET() {
    return "Get request";
  }
  
  function POST() {
    return "Post request.";
  }
}

