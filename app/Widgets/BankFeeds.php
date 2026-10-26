<?php

namespace App\Widgets;

use App\Abstracts\Widget;
use App\Traits\Cloud;
use App\Traits\Modules;

class BankFeeds extends Widget
{
    use Cloud, Modules;

    public $default_name = 'widgets.bank_feeds';

    public function show()
    {
        if (! $this->moduleIsEnabled('bank-feeds')) {
            return '';
        }

        $module = $this->getModulesByWidget('bank-feeds');

        if (empty($module)) {
            return '';
        }

        return $this->view('widgets.bank_feeds', [
            'module'            => $module,
        ]);
    }
}
