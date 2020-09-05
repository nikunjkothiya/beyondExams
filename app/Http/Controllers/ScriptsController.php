<?php

namespace App\Http\Controllers;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Log;

class ScriptsController extends Controller
{
    public function clear_sheet(){
        $file = storage_path() . '/scripts/cs.py';
        $process = new Process(['python', $file]);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        Log::debug($process->getOutput());
    }
}
