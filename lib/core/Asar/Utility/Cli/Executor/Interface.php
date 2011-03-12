<?php

interface Asar_Utility_Cli_Executor_Interface {
  function registerTasks(
    Asar_Utility_CLI_Interface $tasklist, $namespace = null
  );
  function execute(Asar_Utility_Cli_Command $command);
  function getRegisteredTasks();
}
