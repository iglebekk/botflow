<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;

class BotApiDocumentationController extends Controller
{
    public function __invoke(): View
    {
        return view('docs.bot-api', [
            'content' => File::get(base_path('doc/bot-api.md')),
        ]);
    }
}
