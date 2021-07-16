<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DashboardRepository;
use App\Helpers\ErrorMessage;
use App\Http\Requests\ScraperSettingsRequest;

class DashboardController extends Controller
{

    protected $dashboardService;

    public function __construct(DashboardRepository $dashboardService) {
        $this->dashboardService = $dashboardService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $scraperSettings = $this->dashboardService->getScraperSettings();
        return view('dashboard.index')->with(compact('scraperSettings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ScraperSettingsRequest $request, $id = null)
    {
        try {
            $this->dashboardService->updateScraperSettings($request, $id);

            return redirect()->route('dashboard.index')->with('success', 'Successfully updated');
        } catch (\Throwable $ex) {
            dd($ex->getMessage());
            return redirect()->route('dashboard.index')->with('error', ErrorMessage::UNKNOWN_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
