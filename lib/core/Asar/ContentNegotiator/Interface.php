<?php

interface Asar_ContentNegotiator_Interface {
  function negotiateFormat($accept_header, $available_formats);
}
