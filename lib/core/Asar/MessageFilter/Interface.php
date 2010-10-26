<?php

interface Asar_MessageFilter_Interface {
  function filterRequest(Asar_Request_Interface $request);
  function filterResponse(Asar_Response_Interface $response);
}