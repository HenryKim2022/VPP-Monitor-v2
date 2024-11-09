<?php

namespace App\Http\Controllers\UserPanels\Navigate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Jobs\CheckExpiredWorksheetsJob;


class UserPanelController extends Controller
{
    //
    public function index(Request $request)
    {
         $process = $this->setPageSession("Dashboard", "dashboard");
         if ($process) {
             $data = [
                'currentRouteName' => Route::currentRouteName(),
                'quote' => $this->getQuote(),
                'breadcrumbs' => $this->getBreadcrumb($request->route()->getName()),
             ];
             return $this->setReturnView('pages/userpanels/p_dashboard', $data);
         }
    }
}
