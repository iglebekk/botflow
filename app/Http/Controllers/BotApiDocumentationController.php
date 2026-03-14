<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BotApiDocumentationController extends Controller
{
    public function __invoke(): View
    {
        $content = Str::of(File::get(base_path('doc/bot-api.md')))
            ->replace('{{ base_url }}', url('/'))
            ->replace('{{ api_base_url }}', url('/api'))
            ->value();

        return view('docs.bot-api', [
            'content' => $content,
            'renderedContent' => Str::markdown($content),
        ]);
    }
}
