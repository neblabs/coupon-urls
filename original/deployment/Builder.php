<?php

namespace CouponURLs\Original\Deployment;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[\AllowDynamicProperties]
Class Builder
{
    protected $currentDirectory;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->settingsReader = new SettingsReader;
        $this->buildFileManager = new BuildFileManager($input->getOption('target'), $output);
        $this->currentDirectory = $this->buildFileManager->getCurrentDirectory();
    }

    public function build()
    {
        $this->input->getOption('silent') && ob_start();
        (string) $copyDirectoryName = $this->buildFileManager->createCopyDirectory();

        $this->buildFileManager->cloneDirectories($copyDirectoryName);

        if (!$this->input->getOption('compress')) {
            $path = $copyDirectoryName;
        } else {
            $path = $this->buildFileManager->compressCopy($copyDirectoryName, $this->input->getOption('silent'));
        }

        $this->runScripts();

        if ($this->input->getOption('silent') && ob_end_clean()) {
            echo $path;
        }
    }

    public function runScripts()
    {
        Script::handle($this->settingsReader->settings->scripts->afterCompression);
    }

}
