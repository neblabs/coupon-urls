<?php

namespace CouponURLS\Original\Deployment\Scripts;

use CouponURLS\Original\Deployment\Script;
use CouponURLS\Original\Environment\Env;

Class PHPDowngradeCompilerScript extends Script
{
    public function run()
    {
        (string) $copyDirectoryName = $this->data->get('copyDirectoryName');
        (string) $command = "/opt/local/bin/php82 -d error_reporting=\"E_ALL & ~E_DEPRECATED\" " . Env::directory() . "vendor/bin/rector process {$copyDirectoryName} 2>&1";;

        print "\nCompiling... {$copyDirectoryName}\n";
        print "\nrunning rector: $command\n";
        (string) $outPut = shell_exec($command);

        print $outPut;
    }
    
}