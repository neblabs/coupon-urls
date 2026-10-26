<?php

namespace CouponURLs\Original\Environment;

use CouponURLs\Original\Characters\StringManager;
use function CouponURLs\Original\Utilities\Collection\_a;

Class Env
{
    private static $instance;
    private static $directory;
    private static $absoluteRootFilePath;
    private static $settings;
    private static $originalSettings;

    public static function set($absoluteFilePath)
    {
        // can't use plugin_dir_path as this object is also used in a non-wordpress command line environment
        if (function_exists('plugin_dir_path')) {
            static::$directory = plugin_dir_path($absoluteFilePath);
        } else {
            static::$directory = dirname($absoluteFilePath).'/';
        }

        static::$absoluteRootFilePath = $absoluteFilePath;
        static::$settings = json_decode(static::getEncoderFunction()(require static::appDirectory('settings').'default.php'));
        static::$originalSettings = clone static::$settings;
    }

    static public function getEncoderFunction() : callable
    {
        (string) $base = 'json_encode';

        return match(function_exists("wp_{$base}")) {
            true => "wp_{$base}",
            false => $base
        };
    }

    public static function reset() : void
    {
        static::$settings = static::$originalSettings;
    }

    public static function isWordPress()
    {
        return function_exists('add_action');
    }
    
    public static function settings()
    {
        return static::$settings;
    }

    public static function id()
    {
        return strtoupper(static::$settings->app->id);
    }

    public static function shortId()
    {
        return strtolower(static::$settings->app->shortId);
    }

    public static function idLowerCase()
    {
        return strtolower(static::id());   
    }

    public static function getWithPrefix($text)
    {
        return static::idLowerCase() . "_{$text}";   
    }

    public static function getwithShortPrefix($text)
    {
        return strtolower(static::shortId()) . "_{$text}";   
    }

    public static function for(Collection $environmentCallables): mixed
    {
        $callable = $environmentCallables->find(
            fn(mixed $value, string $environment) => $environment === static::settings()->environment
        );

        if ($callable) {
            return $callable;
        }

        if ($environmentCallables->hasKey('default')) {
            return $environmentCallables->get('default')();
        }
    }

    public static function absolutePluginFilePath()
    {
        return static::$directory . strtolower(static::$settings->app->pluginFileName). '.php';
    }

    public static function directory()
    {
        return static::$directory;
    }

    public static function getAppDirectory($registeredDirectory)
    {
        return static::appDirectory(static::$settings->directories->app->{$registeredDirectory});
    }

    public static function appDirectory($subDirectory = '')
    {
        $subDirectory = $subDirectory? "$subDirectory/" : '';
        return static::directory() . "app/{$subDirectory}";
    }

    public static function originalDirectory($subDirectory = '')
    {
        $subDirectory = $subDirectory? "$subDirectory/" : '';
        return static::directory() . "original/{$subDirectory}";
    }

    public static function testsDirectory($subDirectory = '')
    {
        $subDirectory = $subDirectory? "$subDirectory/" : '';
        return static::directory() . "tests/{$subDirectory}";
    }

    public static function directoryURI()
    {
        if (function_exists('plugin_dir_url')) {
            return plugin_dir_url(static::$absoluteRootFilePath);
        }
    }

    public static function uploadsDirectory()
    {
        return wp_upload_dir()['basedir'];
    }

    public static function getwithBaseNamespace(string $relativeNamespace) : string
    {
        (string) $baseNamespace = static::settings()->app->namespace;

        return "{$baseNamespace}\\{$relativeNamespace}";
    }

    public static function getNamespaceFromDirectory(string $path) : string
    {
        (string) $convertedPath = _a(StringManager::create($path)->trim('/')
                                                              ->explode('/')
)                                                              ->map(function(string $pathPart) : string {
                                                                    return ucfirst($pathPart);
                                                              })
                                                              ->implode('\\');

        return static::getwithBaseNamespace($convertedPath);
    }

    public static function textDomain()
    {
        return strtolower(static::settings()->app->textDomain);
    }

    public static function database()
    {
        // for development only, not used live
        return (object) [
            'name' => 'social'
        ];
    }

    public static function isTesting()
    {
        return defined('COUPONS_PLUS_TESTING');
    }

    public static function remoteDebug(...$args)
    {
        header('NEBLABS_REMOTE_HTTP_DEBUG: true');

        var_dump('NEBLABS REMOTE DEBUG');
        foreach ($args as $arg) {
            var_dump($arg);
        }
        var_dump('NEBLABS REMOTE DEBUG');

        exit('');
    }
}