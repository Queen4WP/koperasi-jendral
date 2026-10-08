<?php

namespace App\Http\Controllers;

use App\Services\KoperasiService;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Display the public homepage.
     */
    public function index()
    {
        $minLoan = KoperasiService::MIN_LOAN_AMOUNT;
        $maxLoan = KoperasiService::MAX_LOAN_AMOUNT;
        $rates = KoperasiService::RATE_MATRIX;

        return view('public.home', compact('minLoan', 'maxLoan', 'rates'));
    }

    /**
     * Display the About KOPIN page.
     */
    public function about()
    {
        return view('public.about');
    }

    /**
     * Display the public savings products page.
     */
    public function savings()
    {
        return view('public.savings');
    }

    /**
     * Display the public loan products page.
     */
    public function loans()
    {
        $minLoan = KoperasiService::MIN_LOAN_AMOUNT;
        $maxLoan = KoperasiService::MAX_LOAN_AMOUNT;
        $rates = KoperasiService::RATE_MATRIX;

        return view('public.loans', compact('minLoan', 'maxLoan', 'rates'));
    }

    /**
     * Display the public workflow / how it works page.
     */
    public function howItWorks()
    {
        return view('public.how-it-works');
    }

    /**
     * Display the public contact page.
     */
    public function contact()
    {
        return view('public.contact');
    }
}
