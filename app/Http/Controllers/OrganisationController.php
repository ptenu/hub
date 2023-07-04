<?php

namespace App\Http\Controllers;

use App\Extensions\Statistics;
use App\Models\Branch;
use App\Models\Contact;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganisationController extends Controller
{
    public function index()
    {
        // Member count
//        $memberships = Statistics::membershipStatuses();

        return view('organisation.index');
    }

    public function listBranches()
    {
        $branches = Branch::all();

        return view('organisation.branches', [
            'branches' => $branches
        ]);
    }

    public function getBranch(Request $request, Branch $branch) {
        return view('organisation.branch', [
            'branch' => $branch
        ]);
    }

    public function createBranch()
    {
        return view('organisation.create-branch');
    }

    public function saveNewBranch(Request $request)
    {
        $request->validate([
            "name" => "required",
            "postcodes" => "required"
        ]);

        // Create branch
        $branch = new Branch();
        $branch->name = $request->input('name');
        $branch->description = $request->input('description') ?? null;
        $branch->save();

        // Add postcodes
        $postcodes = explode("\n", $request->input('postcodes'));
        foreach ($postcodes as $pc) {
            DB::table('branch_postcodes')->insert([
                "branch_id" => $branch->id,
                "postcode_substr" => strtoupper($pc)
            ]);
        }

        return redirect()->route('org.branches');
    }
}
