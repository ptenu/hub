<?php

namespace App\Http\Controllers;

use App\Models\PropertyInterest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PrinsFrank\Standards\Language\LanguageAlpha2;
use PrinsFrank\Standards\Language\LanguageName;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        return view('account.index', [
            "user" => $request->user()
        ]);
    }

    public function updateAddress(Request $request)
    {
        $postcode = $request->query("postcode", false);
        if (!$postcode) {
            return view('account.change-address');
        }

        $postcode = $request->input("postcode.0") . ' ' . $request->input('postcode.1');
        $postcode = strtoupper($postcode);

        $addresses = DB::connection('addresses')
            ->table('addresses_a')
            ->select('*')
            ->where('postcode', '=', $postcode)
            ->get();

        return view('account.change-address', [
            "addresses" => $addresses,
            "postcode" => [$request->input("postcode.0"), $request->input('postcode.1')]
        ]);
    }

    public function saveAddress(Request $request)
    {
        $request->validate([
            'uprn' => ['required']
        ]);

        $uprn = $request->input('uprn');
        $user = $request->user();

        $interest = PropertyInterest::query()->firstOrCreate([
            'contact_id' => $user->id,
            'uprn' => $uprn
        ]);

        $interest->deleted_at = null;
        $interest->save();

        return view('account.interest-details', ['interest' => $interest]);
    }

    public function saveInterest(Request $request)
    {
        $request->validate([
            'interest_id' => ['required'],
            'moved_in.day' => ['required'],
            'moved_in.month' => ['required'],
            'moved_in.year' => ['required'],
            'tenure' => ['required']
        ]);

        PropertyInterest::query()
            ->where('contact_id', $request->user()->id)
            ->where('id', '!=', $request->input('interest_id'))
            ->delete();

        $interest = PropertyInterest::query()
            ->where('id', $request->input('interest_id'))
            ->where('contact_id', $request->user()->id)
            ->firstOrFail();
        $interest->type = $request->input('tenure');
        $move_in_date = Carbon::create(
            $request->input('moved_in.year'),
            $request->input('moved_in.month'),
            $request->input('moved_in.day'),
        );

        $interest->created_at = $move_in_date;
        $interest->save();

        session()->flash('status', 'Your address has been updated');

        return redirect('/account');
    }

    public function detailsForm(Request $request)
    {
        return view('account.update-details', [
            'user' => $request->user(),
            'languages' => LanguageAlpha2::cases()
        ]);
    }

    public function saveDetails(Request $request)
    {
        $request->validate([
            'given_name' => 'required',
            'family_name' => 'required',
        ]);

        $contact = $request->user();

        // Update name if it doesn't match the existing one
        if ($contact->given_name != $request->input('given_name')) {
            $contact->given_name = ucwords($request->input('given_name'));
        }
        if ($contact->given_name != $request->input('given_name')) {
            $contact->given_name = ucwords($request->input('given_name'));
        }

        // Update date of birth, if provided
        $year = $request->input('date_of_birth.year');
        $month = $request->input('date_of_birth.month');
        $day = $request->input('date_of_birth.day');
        if ($year != '' && $month != '' && $day != '') {
            $newDate = Carbon::create($year, $month, $day);
            $contact->date_of_birth = $newDate;
        }

        // Update legal sex (or set to null)
        $sexKey = $request->input('legal_sex');
        $newSex = null;
        if (in_array($sexKey, ['M', 'F'])) {
            $newSex = $sexKey;
        }
        $contact->legal_sex = $newSex;

        // Update first language
        $newLanguageCode = $request->input('first_language');
        if ($newLanguageCode == '') {
            $newLanguageCode = null;
        }
        $contact->first_language = $newLanguageCode;

        // Save
        $contact->save();

        $request->session()->flash('status', 'Your details have been updated.');
        return redirect('/account');
    }

    public function viewTenancies(Request $request)
    {
        return view('account.tenancy', [
            'user' => $request->user()
        ]);
    }

    public function updateTenancy(Request $request)
    {

    }

    public function saveTenancy(Request $request)
    {

    }
}
