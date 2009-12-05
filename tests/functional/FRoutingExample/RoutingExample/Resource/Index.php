<?php
class RoutingExample_Resource_Index extends Asar_Resource {

  public function GET() {
    return get_class($this);
  }

}
