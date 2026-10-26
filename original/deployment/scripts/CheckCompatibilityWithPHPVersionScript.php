<?php

namespace CouponURLS\Original\Deployment\Scripts;

use CouponURLS\Original\Deployment\Script;
use CouponURLS\Original\Environment\Env;
use ParseError;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Finder\Finder;
use function CouponURLS\Original\Utilities\Text\i;

// this has moved to the ci, not here no more
Class CheckCompatibilityWithPHPVersionScript extends Script
{
    public function run()
    {
        /** @var string */
        (string) $copyDirectoryName = $this->data->get('copyDirectoryName');
        (object) $filesCopied = $this->data->get('filesCopied');

        (object) $progressBar = new ProgressBar(
            output: $this->data->get('output'),
            max: $filesCopied->count()
        );

        $progressBar->setFormat('  %current%/%max% %bar% %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% ');
        $progressBar->setBarCharacter('<fg=green>∎</>');
        $progressBar->setEmptyBarCharacter('<fg=gray>∎</>');
        $progressBar->setProgressCharacter('<fg=green>∎</>');

        print "\nVerifying compatibility...\n";

        $progressBar->start();

        foreach ($filesCopied->asArray() as $file) {
            if (i($file->target)->contains('/vendor/') || !i($file->target)->endsWith('.php')) {
                continue;
            }

            (string) $result = shell_exec("php72 -l {$file->target}");

            if (!i($result)->contains('No syntax errors detected')) {
                //throw new ParseError($result); 
                print $result;
            }
            $progressBar->advance();
        }

        $progressBar->finish();
    }
    
}