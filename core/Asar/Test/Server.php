<?php

class Asar_Test_Server {
    public function setUp($options)
    {
        $test_data_path = Asar::constructRealPath(
            Asar::getFrameworkPath(), 'tests', 'data'
        );
        $test_server_path = Asar::constructPath(
            $test_data_path , 'test-server' 
        );
        
        if(is_link($test_server_path) || file_exists($test_server_path)) {
            unlink($test_server_path);
        }
        
        if (array_key_exists('fixture', $options)) {
            $server_dir = Asar::constructPath(
                $test_data_path, 'test-server-fixtures', $options['fixture']
            );
        } else {
            $server_dir = $options['path'];
        }
        symlink($server_dir, $test_server_path);
    }
}
