<?php
class StatusCodesExample_Resource_500 extends \Asar\Resource {
  public function GET() {
    throw new Exception('Something is wrong.');
  }
}

