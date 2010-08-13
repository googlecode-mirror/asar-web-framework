<?php
class StatusCodesExample_Resource_500 extends Asar_Resource {
  public function GET() {
    throw new Exception('Something is wrong.');
  }
}

