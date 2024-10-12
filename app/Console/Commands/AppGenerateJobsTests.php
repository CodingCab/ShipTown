<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AppGenerateJobsTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-jobs-tests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates tests for all jobs';

    public function handle(): void
    {
        $jobFiles = $this->getJobFiles();

        collect($jobFiles)
            ->each(function ($jobFile) {
                $testFileName = $this->getTestFileName($jobFile);

                $jobFileName = $testFileName;
                $jobFileName = str_replace('/', '\\\\', $jobFileName);
                $jobFileName = Str::ucfirst($jobFileName);
                $jobFileName = Str::chopEnd($jobFileName, 'Test');
                $jobFileName = Str::start($jobFileName, 'App\\\\');


//                $testFileName = app()->basePath().'/tests/'.$testFileName.'.php';

                if (File::exists($testFileName)) {
                    return;
                }

                $command = 'app:make-test '.$testFileName.' --stub=test.job --testedClass='.$jobFileName;

                Artisan::call($command);

                $output = Artisan::output();

                $this->info($command);
                $this->info($output);
            });
    }

    private function getJobFiles(): array
    {
        $directory = new \RecursiveDirectoryIterator(app_path());
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, '/^.+\/Jobs\/.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);

        $jobFiles = [];
        foreach ($regex as $file) {
            $jobFiles[] = $file[0];
        }

        return $jobFiles;
    }

    private function getTestFileName($file): string
    {
        $directory = \File::dirname($file);
        $directory = str_replace(app()->basePath().'/app/', '', $directory);
        $directory = \Str::ucfirst($directory);

        $fileName = $directory.'/'.basename($file, '.php').'Test';

        return $fileName;
    }
}
