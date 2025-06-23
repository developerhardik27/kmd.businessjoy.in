<?php

namespace App\Http\Controllers\v4_2_0\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemMonitorController extends Controller
{
    public $version;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
        } else {
            $this->version = 'v4_2_0';
        }
    }

    /**
     * Display a listing of the resource.
     * slow page view
     */
    public function slowpages()
    {
        return view($this->version . '.admin.Developer.slowpages');
    }

    /**
     * Display a listing of the resource.
     * error log list view
     */
    public function errorlogs()
    {
        return view($this->version . '.admin.Developer.errorlogs');
    }

    /**
     * Display a listing of the resource.
     * cron job list view
     */
    public function cronjobs()
    {
        return view($this->version . '.admin.Developer.cronjobs');
    }


    /**
     * Summary of techdocs
     * technical docs list view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function techdocs()
    {
        $directory = public_path('docs');

        if (!is_dir($directory)) {
            return view($this->version . '.admin.Developer.techdocs', ['files' => collect()]);
        }

        $files = collect(scandir($directory))
            ->filter(function ($file) use ($directory) {
                return is_file($directory . DIRECTORY_SEPARATOR . $file);
            })
            ->map(function ($file) use ($directory) {
                return [
                    'name' => $file,
                    'updated_on' => date('d-M-Y H:i', filemtime($directory . DIRECTORY_SEPARATOR . $file)),
                    'download_url' => asset('docs/' . $file),
                ];
            })
            ->values();

        return view($this->version . '.admin.Developer.techdocs', ['files' => $files]);
    }

    /**
     * Summary of versiondocs
     * version docs list view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function versiondocs()
    {
        $versionFiles = config('app.version_files') ?? [];

        return view($this->version . '.admin.Developer.versiondocs', compact('versionFiles'));
    }


}
