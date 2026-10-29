<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    /**
     * Switch application language.
     */
    public function switch(Request $request, string $lang): RedirectResponse
    {
        $request->session()->put('locale', $lang);
        App::setLocale($lang);

        return redirect()->back();
    }
}
