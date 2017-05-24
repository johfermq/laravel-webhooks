<?php

namespace Jurihub\LaravelWebhooks\Http\Controllers\Webhooks;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Jurihub\LaravelWebhooks\Webhook;
use Symfony\Component\HttpFoundation\Response;

class SenderController extends Controller
{
    /**
     * Verify if we are in the testing environment.
     *
     * @return bool
     */
    protected function isInTestingEnvironment()
    {
        return env('APP_ENV') !== 'prod';
    }
    
    public function retry()
    {
        $webhook = Webhook
            ::where([
                ['is_working', '=', 0],
                ['is_closed', '=', 0],
                ['last_tried_at', '<', Carbon::now()->subMinutes($this->isInTestingEnvironment() ? 1 : 30)],
            ])
            ->whereNotNull('last_tried_at')
            ->orderBy('id', 'desc')
            ->first();
        $webhook->send();
    }
}
