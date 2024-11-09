<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Services\BreadcrumbService;
use App\Jobs\CheckExpiredWorksheetsJob;
use Barryvdh\DomPDF\Facade\Pdf; // Import the PDF facade directly




abstract class Controller
{
    protected $pageData;
    protected $breadcrumbService;

    public function __construct(BreadcrumbService $breadcrumbService)
    {
        // $this->middleware('Client')->except('logout');
        $this->pageData = [
            'page_title' => 'What Public See',
            'page_url' => base_url('login-url'),
            'custom_date_format' => "ddd, DD MMM YYYY, h:mm:ss A",

        ];
        Session::put('page', $this->pageData);
        $this->breadcrumbService = $breadcrumbService;
        // Dispatch the job to check for expired worksheets
        // CheckExpiredWorksheetsJob::dispatch();
        // $this->runQueueWorkerv1();
        // $this->runQueueWorkerv2();
        // $this->dispatchJob();
    }


    ///////////////////////////// JOB DB CHECK VER.1 - NOT USED ////////////////////////////
    public function runQueueWorkerv1()
    {
        // NOTE:
        // Run this cmd, at terminal 1x times if using this way (background process)
        // php artisan queue:work --sleep=3 --tries=9999999999

        // This will run the queue worker command
        Artisan::call('queue:work', [
            '--sleep' => 3,
            '--tries' => 9999999999,
        ]);

        return response()->json(['message' => 'Queue worker started.']);
    }

    ///////////////////////////// JOB DB CHECK VER.2 - CURRENTLY USED ////////////////////////////
    public function runQueueWorkerv2()
    {
        $lockFile = storage_path('logs/queue_worker.lock');

        // Check if the lock file exists
        if (file_exists($lockFile)) {
            return response()->json(['message' => 'Queue worker is already running.'], 409);
        }

        // Create a lock file
        file_put_contents($lockFile, getmypid());

        // Check if a queue worker is already running
        $runningWorkers = shell_exec("ps aux | grep 'queue:work' | grep -v grep");

        if (!empty($runningWorkers)) {
            // If a worker is running, kill it
            preg_match_all('/\S+\s+(\d+)\s+/', $runningWorkers, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $pid) {
                    // Kill the process
                    shell_exec("kill -9 $pid");
                }
                Log::info('Existing queue worker(s) killed.');
            }
        }

        // Start a new queue worker in the background
        shell_exec("nohup php " . base_path('artisan') . " queue:work --sleep=3 --tries=3 > /dev/null 2>&1 &");

        // Remove the lock file after the worker has started
        unlink($lockFile);

        return response()->json(['message' => 'Queue worker started.']);
    }


    ///////////////////////////// JOB DB CHECK VER.3 - MANUAL USED ////////////////////////////
    public function dispatchJob()
    {
        try {
            CheckExpiredWorksheetsJob::dispatch();
            Log::info('Job dispatched successfully.');
            return response()->json(['message' => 'Job dispatched.']);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch job: ' . $e->getMessage());
            return response()->json(['message' => 'Job dispatched.']);
        }
    }

    public function getBreadcrumb($routeName)
    {
        return $this->breadcrumbService->generate($routeName);
    }


    public function getQuote()
    {
        $quote = trim(strip_tags(Inspiring::quote()));
        $quote = htmlspecialchars_decode($quote, ENT_QUOTES);
        $lastHyphenPos = strrpos($quote, '—');
        if ($lastHyphenPos !== false) {
            $text = trim(substr($quote, 0, $lastHyphenPos));
            $author = trim(substr(utf8_decode($quote), $lastHyphenPos - 2));
        } else {
            $text = $quote;
            $author = '';
        }
        return [
            'text' => $text,
            'author' => $author
        ];
        // Note (in the view):<div><p><strong>{{ $quote['text'] }}</strong><span style="color: gray;"> {{ '  —' . $quote['author'] }}</span></p></div>
    }



    ///////////////////////////// GEN PDF ////////////////////////////
    public function generatePDF($request, $relatedView)
    {
        $title = $request->input('print-task-title');

        // Check if title is provided
        if (is_null($title)) {
            return response()->json(['error' => 'Title is not provided'], 400);
        }

        $data = [
            'title'         => $title,
            'columns'       => json_decode($request->input('print-columns')),
            'print_length'  => $request->input('print-length')
        ];

        $pdf = PDF::loadView($relatedView, compact('data', 'title'));
        return $pdf->download($title . '_report.pdf');
    }






    ///////////////////////////// PAGE SETTER ////////////////////////////
    public function setPageSession($pageTitle, $pageUrl)
    {
        $pageData = Session::get('page');
        $pageData['page_title'] = $pageTitle;
        $pageData['page_url'] = $pageUrl;

        // Store the updated array back in the session
        Session::put('page', $pageData);
        return true;
    }


    public function setReturnView($viewurl, $loadDatasFromDB = [])
    {
        $pageData = Session::get('page');
        $mergedData = array_merge($loadDatasFromDB, ['pageData' => $pageData]);
        return view($viewurl, $mergedData);
    }
}
