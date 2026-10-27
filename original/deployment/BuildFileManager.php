<?php

namespace CouponURLS\Original\Deployment;

use AllowDynamicProperties;
use CouponURLS\Original\Collections\Collection;
use CouponURLS\Original\Deployment\SettingsReader;
use CouponURLS\Original\Validation\Validator;
use CouponURLS\Original\Validation\Validators;
use Exception;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Throwable;
use function CouponURLS\Original\Utilities\Collection\{a, _, o};
use function CouponURLS\Original\Utilities\Text\i;

#[AllowDynamicProperties]
Class BuildFileManager
{
    protected $currentDirectory;
    protected FileSystem $fileSystem;

    public function __construct(
        protected string $dir = '',
        protected OutputInterface $output
    )
    {
        $this->currentDirectory = getcwd();  
        $this->settingsReader = new SettingsReader; 
        $this->fileSystem =  new FileSystem;
    }

    public function getCurrentDirectory()
    {
        return $this->currentDirectory;   
    }
    
    public function createCopyDirectory()
    {
        (string) $copyDirectoryName = $this->generateCopyDirectoryName();

        print "Creating copy...\n\n";

        $this->fileSystem->mkdir(
            $copyDirectoryName
        );

        return $copyDirectoryName;
    }

    public function cloneDirectories($copyDirectoryName)
    {
        $finder = $this->createFinder()->files();           
        (object) $filesCopied = _();

        foreach ($this->settingsReader->settings->directoriesToExclude->asArray() as $directoryToExclude) {
            (string) $absoluteDirToExclude = "{$this->currentDirectory}/{$directoryToExclude}";

            if (!is_dir($absoluteDirToExclude)) {
                //throw new \Exception("Nonexistent file to exclude: $absoluteDirToExclude");
                
            }
            $finder->exclude($directoryToExclude);
        }

        $finder->ignoreDotFiles($this->settingsReader->settings->excludePrivateDirectories)
               ->notName([
                    '*.phar',
                    '*.dist',
                    '*.DS_Store',
                    '*.sublime-project',
                    '*.sublime-workspace',
                    '*.bak',
                    '*.dist',
                    '*.phar',
                    '*.gz',
                    '*.DS_Store',
                    '*.sh',
                    '*.exe',
                    '*.php_cs.dist'
               ])
               ->in($this->currentDirectory);

        Script::handle($this->settingsReader->settings->scripts->beforeClone, [
            'copyDirectoryName' => $copyDirectoryName
        ]);

        (object) $excluded = new Collection([]);
        (object) $progressBar = new ProgressBar(
            output: $this->output,
            max: count($finder)
        );

        $progressBar->setFormat('  %current%/%max% %bar% %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% ');
        $progressBar->setBarCharacter('<fg=green>∎</>');
        $progressBar->setEmptyBarCharacter('<fg=gray>∎</>');
        $progressBar->setProgressCharacter('<fg=green>∎</>');
        $progressBar->start();

        foreach ($finder as $fileToCopy) {

            if ($this->settingsReader->settings->filesToExclude->contain($fileToCopy->getRelativePathname())) {
                $excluded->push($fileToCopy->getRelativePathname());
                continue;
            }
            //print "\e[H\e[J". "Cloning {$fileToCopy->getRealPath()}...";
            $this->fileSystem->copy(
                $source = $fileToCopy->getRealPath(), 
                $target = "{$copyDirectoryName}/{$fileToCopy->getRelativePathname()}"
            );

            $filesCopied->push(o(
                source: $source,
                target: $target
            ));

            try {
                Script::handle(
                    $this->settingsReader->settings->scripts->afterCloningSingleFile,
                    data: a(target: $target)
                );

                if (($this->getProcessableFilesValidator($target)->isValid())) {
                    Script::handle(
                        $this->settingsReader->settings->scripts->afterCloningSingleProcessableFile,
                        data: a(target: $target)
                    );
                }
            } catch(Throwable $exception) {
                print "Error in script: \n";
                throw $exception;
                //throw new Exception("Error in script: {$exception->getMessage()}, with source file: {$source} and message: {$exception->getmessage()}");
            }

            $progressBar->advance();

        }

        $progressBar->finish();

        (string) $filesExcludedAfterCopy = $excluded->reduce(function($list, $current){
            return "{$list}\n{$current}";
        });

        print "\e[H\e[J"."\nExcluded: \n{$filesExcludedAfterCopy}\n\n";

        Script::handle($this->settingsReader->settings->scripts->afterClone, [
            'copyDirectoryName' => $copyDirectoryName,
            'filesCopied' => $filesCopied,
            'fileSystem' => $this->fileSystem,
            'output' => $this->output
        ]);
    }

    protected function getProcessableFilesValidator(string $targetFilePath) : Validator
    {
        return new Validators(
            ($this->settingsReader->settings->processableFiles)(i($targetFilePath))
        );
    }
    
    public function compressCopy($copyDirectoryName)
    {
        (object) $compressor = new Compressor(
            $copyDirectoryName, 
            $this->getFinalBuildDirectory(),
            $this->getCurrentFolder()
        );

        $compressor->compress();

        return $compressor;
    }

    public function deleteCopiesDirectory($copyDirectoryName)
    {
        print 'Cleaning up the copies directory...';

        print shell_exec("rm -rf $copyDirectoryName");
    }
    
    protected function createFinder()
    {
        return new Finder;   
    }
    
    protected function generateCopyDirectoryName()
    {
        return $this->getCopiesDirectory().'/'.$this->getCurrentFolder().time();   
    }
    
    protected function getCurrentFolder()
    {
        return (new Collection(explode(DIRECTORY_SEPARATOR, $this->currentDirectory)))->last();   
    }
    
    protected function getCopiesDirectory()
    {
        return $this->getTargetDirectory('copies');  
    }

    protected function getFinalBuildDirectory()
    {
        (string) $currentBuild = date('j-m-Y_H-i-s', time());

        return $this->getTargetDirectory("builds/{$currentBuild}");   
    }
    
    protected function getBuildsDirectory()
    {
        $this->getTargetDirectory('builds');
    }

    protected function getTargetDirectory($directoryName)
    {
        $basedir = rtrim($this->dir ?: $this->settingsReader->settings->repository, '/');

        (string) $copiesDirectory = "{$basedir}/{$directoryName}";

        if (!is_dir($copiesDirectory)) {
            $this->fileSystem->mkdir($copiesDirectory);
        }

        return $copiesDirectory;
    }}