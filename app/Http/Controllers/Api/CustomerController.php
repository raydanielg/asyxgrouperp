<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CrmLead;
use App\Models\CrmDeal;
use App\Models\CrmContact;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return response()->json(CrmContact::with('lead', 'company')->paginate(20));
    }

    public function leads()
    {
        return response()->json(CrmLead::with('company', 'deals', 'assignedTo')->latest()->paginate(20));
    }

    public function deals()
    {
        return response()->json(CrmDeal::with('lead', 'company', 'contracts')->latest()->paginate(20));
    }
}
