<?php

namespace CouponURLS\Original\Deployment\Scripts;

use CouponURLS\Original\Characters\StringManager;
use CouponURLS\Original\Collections\Collection;
use CouponURLS\Original\Deployment\Script;
use Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Name;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

use function CouponURLS\Original\Utilities\Collection\{_, a};
use function CouponURLS\Original\Utilities\Text\i;

/**
 * IMPORTANT!
 * NAMED ARGUMENT SHOULD BE LEFT UNTOUCHED SO THAT RECTOR
 * CAN PROPERLY DOWNGRADE THEM, 
 *
 *For example: $this->globalFuncionWrapper->do_action(hook: 'init') 
 *should be compiled to: do_action(hook: 'init') 
 *then Rector will pick up that fuction and will transform the named arguments as needed
 *USING THE SOURCE OF THE FUNCTION!
 */
Class AddEarlyExitOnNonSourceFlesCompilerScript extends Script
{
    static protected array $filesToExclude = [
        // strings with the file names ENDING IN, like: 'utilities/collection/collection.php'
    ];

    static public bool $replaced = false;


    static public function withFilesToExclude(array $filesToExclude) : string
    {
        static::$filesToExclude = array_merge(static::$filesToExclude, $filesToExclude);

        return self::class;
    }

    public function run()
    {
        if (
            !i($this->data->target)->endsWith('.php') ||
            i($this->data->target)->matchesRegEx('/coupon-urls[A-Za-z0-9_-]*\/vendor/') ||
            _(static::$filesToExclude)->have(
                fn(string $fileEndingIn) => i($this->data->target)->endsWith($fileEndingIn)
            )
        ) {
            return;
        }


        $fileData = file_get_contents($this->data->target);<<<FILEDATA
            <?php
            function kug() {
                return new Class {
                    public function do() : void
                    {
                        return \$this->globalFunctionWrapper->do_action(hook: 'init');
                    }
                };
            }

            public function register(): void
            {
                foreach (\$this->subscribers as \$subscriber) {
                    (object) \$eventHandler = \$this->eventHandlerFactory->create(\$subscriber);
                    (object) \$handle = \$eventHandler->handle(...);

                    \$this->addHandler(\$handle, \$subscriber->priority());

                    \$this->globalFunctionWraper->add_action(
                        hook_name: \$this->name, 
                        callback: \$handle,
                        priority: \$subscriber->priority(),
                        accepted_args: \$this->numberOfAcceptedArgumentsForSubscriber(\$subscriber)
                    );
                }
            }

            public function unregister(): void
            {
                \$this->handlers->forEvery(
                    fn(array \$handlerAndPriority) => \$this->globalFunctionWraper->remove_action(
                        hook_name: \$this->name,
                        callback: \$handlerAndPriority['handler'],
                        priority: \$handlerAndPriority['priority']
                    )
                );

                \$this->handlers = _();
            } 

        FILEDATA;


        $file = i($fileData);
        $namespacePattern = '/namespace\s+\w+/i';
        $exitStatement = "if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly\n\n";

        // Run on non-namespaced files OR namespaced files that define functions
        if (!$file->matchesRegEx($namespacePattern) || $file->matchesRegEx('/^\s*function\s+\w+\s*\(/mi')) {

            if ($file->matchesRegEx('/namespace\s+[^;]+;/i')) {
                // Namespaced file: Insert right AFTER the namespace line (before `use` statements)
                $result = $file->replaceRegEx(
                    '/^(namespace\s+[^;]+;\s*)/mi',
                    fn(array $matches) => $matches[1] . "\n" . $exitStatement
                );
            } else {
                // Non-namespaced file: Insert right after <?php
                $result = $file->trim()->trimLeft('<?php')->prepend("<?php\n\n" . $exitStatement);
            }

            file_put_contents(
                filename: $this->data->target,
                data: (string) $result
            );
        }
    }
    
}