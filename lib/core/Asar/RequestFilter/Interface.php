<?php

interface Asar_RequestFilter_Interface {
  function filterRequest(Asar_Request_Interface $request);
}