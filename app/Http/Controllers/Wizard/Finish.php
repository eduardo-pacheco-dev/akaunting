<?php

namespace App\Http\Controllers\Wizard;

use App\Abstracts\Http\Controller;
use App\Events\Wizard\WizardCompleted;

class Finish extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:read-admin-panel')->only('index', 'update');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->complete();

        return $this->response('wizard.finish.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function update()
    {
        $this->complete();

        return response()->json([]);
    }

    /**
     * Mark the wizard as completed and fire the event once.
     *
     * @return void
     */
    protected function complete()
    {
        $is_first = ! setting('wizard.completed', false);

        setting()->set('wizard.completed', 1);

        // Save all settings
        setting()->save();

        if (! $is_first) {
            return;
        }

        event(new WizardCompleted(company(), user()));
    }
}
