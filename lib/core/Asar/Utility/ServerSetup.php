<?php
class Asar_Utility_ServerSetup {
    
    private static $servers;
    private $name, $root_path;
    
    public function __construct($server_name)
    {
        $this->name = $server_name;
        self::$servers[$server_name] = $this;
    }    
    
    public static function getServer($server)
    {
        return self::$servers[$server];
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setWebRoot($path)
    {
        $this->root_path = $path;
    }
    
    public function getWebRoot()
    {
        return $this->root_path;
    }
    
    public function setUp($app_name)
    {
        Asar_File::create($this->root_path . DIRECTORY_SEPARATOR . 'index.php')
            ->write(
                "<?php\nrequire_once 'Asar.php';\n" .
                "Asar::start('$app_name');"
            )->save();
        Asar_File::create($this->root_path . DIRECTORY_SEPARATOR . '.htaccess')
            ->write(
                "<IfModule mod_rewrite.c>\n" .
                "RewriteEngine On\n".
                "RewriteBase /\n".
                "RewriteCond %{REQUEST_FILENAME} !-f\n".
                "RewriteCond %{REQUEST_FILENAME} !-d\n".
                "RewriteRule . /index.php [L]\n".
                "</IfModule>\n"
            )->save();
    }
}
