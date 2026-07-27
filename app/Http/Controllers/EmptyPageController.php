<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

final class EmptyPageController extends Controller
{
    public function __invoke(Request $request): View
    {
        $auth = (array) $request->session()->get('backoffice_auth', []);

        return view('empty', [
            'email' => (string) ($auth['email'] ?? ''),
        ]);
    }
}
