<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeC extends Controller
{
    public function git_test(Request $r)
    {
        // test branch

        return 'hello git';
    }
}
