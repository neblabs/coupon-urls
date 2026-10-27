<?php

use CouponURLs\Original\Characters\StringManager;
use CouponURLs\Original\Deployment\Scripts\AddEarlyExitOnNonSourceFlesCompilerScript;
use CouponURLs\Original\Deployment\Scripts\ArryToArrayCompilerScript;
use CouponURLs\Original\Deployment\Scripts\CheckCompatibilityWithPHPVersionScript;
use CouponURLs\Original\Deployment\Scripts\ComposerClassMapReloaderScript;
use CouponURLs\Original\Deployment\Scripts\CustomToWordPressTranslationFunctionCompilerScript;
use CouponURLs\Original\Deployment\Scripts\DefinedInAForeignNamespaceAlerterCompilerScript;
use CouponURLs\Original\Deployment\Scripts\EscapeAllExceptionCallsCompilerScript;
use CouponURLs\Original\Deployment\Scripts\NamedArgumentCollectionCallsToAssociativeArrayCallsCompilerScript;
use CouponURLs\Original\Deployment\Scripts\PHPDowngradeCompilerScript;
use CouponURLs\Original\Deployment\Scripts\PHPFormatCompilerScript;
use CouponURLs\Original\Deployment\Scripts\ProductionEnvironmentSetter;
use CouponURLs\Original\Deployment\Scripts\RemoveVarDumpsCompilerScript;
use CouponURLs\Original\Deployment\Scripts\TransformGlobalFunctionWrapperIntoDirectFunctionCallCompilerScript;
use CouponURLs\Original\Deployment\Scripts\UpdatePluginOnPHP72SiteScript;
use CouponURLs\Original\Deployment\Scripts\_ToNewCollectionCompilerScript;
use CouponURLs\Original\Deployment\Scripts\AddEarlyExitOnNonSourceFilesTransformer;
use CouponURLs\Original\Deployment\Scripts\ArryToArrayTransformer;
use CouponURLs\Original\Deployment\Scripts\DevelopmentServiceExceptionHandlerFactoryRemoverScript;
use CouponURLS\Original\Deployment\Transformers\WithExcludedFiles;

use function CouponURLs\Original\Utilities\Collection\{a, _};

return a(
    directoriesToExclude: [
        'app/build',
        'app/commands',
        //'app/helpers',
        'app/creators',
        'app/tasks',
        'app/credentials',
        'app/presentation/components/dashboarddevelopmentscripts',
        'app/scripts/dashboard/.git',
        //'app/scripts/dashboard/build/static/css',
        'app/scripts/dashboard/public',
        'app/scripts/dashboard/node_modules',

        //'app/tests',

        'original/commands',
        'app/tasks',
        'original/creators',
        
        'original/deployment',
        'original/tests',
        'original/renderers',

        'bin',

        'tests',

        'vendor/bin',
        'vendor/antecedent',
        'vendor/brain',
        'vendor/brianium',
        //to make it regenerate the autoloader
        //'vendor/composer',
        'vendor/doctrine',
        'vendor/dealerdirect',
        'vendor/php-stubs',
        'vendor/phpcsstandards',
        'vendor/rector',
        'vendor/squizlabs',
        'vendor/symplify',
        'vendor/wp-coding-standards',
        'vendor/evenement',
        'vendor/fidry',
        'vendor/filp',
        'vendor/hamcrest',
        'vendor/illuminate',
        'vendor/jean85',
        'vendor/league',
        'vendor/mockery',
        'vendor/monolog',
        'vendor/myclabs',
        'vendor/nunomaduro',
        'vendor/nesbot',
        'vendor/nikic',
        'vendor/phpdocumentor',
        'vendor/phpunit',
        'vendor/psr',
        'vendor/pestphp',
        'vendor/sebastian',
        'vendor/phpunit',
        'vendor/phar-io',
        'vendor/phpstan',
        'vendor/rafark',
        'vendor/react',
        'vendor/seld',
        'vendor/spatie',

        //'vendor/symfony/polyfill-intl-grapheme',
        //'vendor/symfony/polyfill-intl-idn',
        //'vendor/symfony/polyfill-intl-normalizer',
        //'vendor/symfony/polyfill-mbstring',
        //'vendor/symfony/polyfill-php72',
        //'vendor/symfony/polyfill-php80',
        //'vendor/symfony/polyfill-php83',
        'vendor/symfony/process',
        'vendor/symfony/service-contracts',
        'vendor/symfony/string',
        'vendor/symfony/translation',
        'vendor/symfony/translation-contracts',
        'vendor/symfony/var-dumper',
        'vendor/symfony/deprecation-contracts',
        //'vendor/symfony/http-foundation',
        'vendor/symfony/mime',

        'vendor/symfony/console',
        'vendor/symfony/filesystem',
        'vendor/symfony/finder',
        'vendor/symfony/polyfill-ctype',
        'vendor/ta-tikoma',
        'vendor/theseer',
        //'vendor/voku',
        'vendor/webmozart',
        'node_modules'

    ],
    filesToExclude: [
        'app/Dashboard/DashboardDataCopy.php',

        'app/scripts/dashboard/build/index.html',
        'app/scripts/dashboard/.gitignore',
        'app/scripts/dashboard/craco.config.js',
        'app/scripts/dashboard/postcss.config.js',
        'app/scripts/dashboard/README.md',
        'app/scripts/dashboard/tailwind.config.js',
        'app/scripts/dashboard/yarn.lock',
        'app/scripts/dashboard/package.json',
        'app/scripts/dashboard/package-lock.json',
        'app/scripts/dashboard/gettext.pot',

        'app/components/DashboardDevelopmentScripts.php',
        'app/views/dashboardDevelopmentScripts.php',

        'international/main-source.pot',
        'international/scripts-source.pot',
        'international/scripts-source-original.pot',

        'original/data/drivers/DatabaseDriver.php',
        'original/data/drivers/PDODatabaseDriver.php',
        'original/data/drivers/sql/PDODatabaseDriver.php',
        'original/data/model/Gateway.php',

        'original/utilities/text/translation.php',

        //'vendor/autoload.php',

        '.blog',
        '.todo',
        '.examples',
        '.marketing',
        '.notes-to-rw',
        '.usability-tests',
        '.build.php',
        '.phpcs.xml.dist',
        'composer.lock',
        'no-original-unit-tests.xml',
        'phpunit.integration.xml',
        //'composer.json', this is removed in the afterclone scripts
        'rector.php',
        'commands-bootstrap.php',
        'ecs.php',
        'edp',
        'phpunit.xml',
    ],
    processableFiles: fn(StringManager $targetFile) =>([
        //new PHPFileValidator($targetFile),
        //new NOTVendorFileValidator($targetFile)
    ]),
    scripts: a(
        singleFile: a(
            new AddEarlyExitOnNonSourceFilesTransformer,
            new WithExcludedFiles(new ArryToArrayTransformer, _(
                'utilities/collection/collection.php',
                'index.php'
            )),   
        ),
        beforeClone: [
            //WebpackProductionBuilderScript::class,
            //no longer needed.ScriptsTranslationPOTSourceLocalizer::class,
            //TranslationFilesMerger::class,
        ],
        afterCloningSingleFile: [
            #start - this has to be before AddEarlyExitOnNonSourceFlesCompilerScript otherwise it'll exit 
            ProductionEnvironmentSetter::class,
            DefinedInAForeignNamespaceAlerterCompilerScript::class,
            #end - this has to be before AddEarlyExitOnNonSourceFlesCompilerScript otherwise it'll exit
            EscapeAllExceptionCallsCompilerScript::class,
            CustomToWordPressTranslationFunctionCompilerScript::class,
            ArryToArrayCompilerScript::withFilesToExclude([
                'utilities/collection/collection.php',
                'index.php'
            ]),
            _ToNewCollectionCompilerScript::withFilesToExclude([
                'utilities/collection/collection.php',
                'index.php'
            ]),
            NamedArgumentCollectionCallsToAssociativeArrayCallsCompilerScript::class,
            TransformGlobalFunctionWrapperIntoDirectFunctionCallCompilerScript::class,
            AddEarlyExitOnNonSourceFlesCompilerScript::class,
            RemoveVarDumpsCompilerScript::class,
            /*
            ArryToArrayCompilerScript::withFilesToExclude([
                'utilities/collection/collection.php',
                'index.php'
            ]),
            _ToNewCollectionCompilerScript::withFilesToExclude([
                'utilities/collection/collection.php',
                'index.php'
            ]),
            NamedArgumentCollectionCallsToAssociativeArrayCallsCompilerScript::class,
            TransformGlobalFunctionWrapperIntoDirectFunctionCallCompilerScript::class,*/
        ],
        afterClone: [
            //DevelopmentServiceExceptionHandlerFactoryRemoverScript::class,
            PHPDowngradeCompilerScript::class,
            //PHPFormatCompilerScript::class,
            //CheckCompatibilityWithPHPVersionScript::class,
            ComposerClassMapReloaderScript::class,
            //UpdatePluginOnPHP72SiteScript::class
            /*
            PHPDowngradeCompilerScript::class,
            //PHPFormatCompilerScript::class,
            CheckCompatibilityWithPHPVersionScript::class,
            ProductionEnvironmentSetter::class,
            ComposerClassMapReloaderScript::class,
            UpdatePluginOnPHP72SiteScript::class*/
        ]
    )
);
