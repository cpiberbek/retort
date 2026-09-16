<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IssueComplainController extends Controller
{
    public function index()
    {
        return view('issue-complain.index');
    }
}