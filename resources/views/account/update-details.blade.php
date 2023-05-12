@extends('layouts.default')

@section('pageTitle', 'Update your details')

@section('content')
    <ptu-page-header headline="Update your details" topic="Back to account" topic-href="/account">
        Use this page to update your basic information.
    </ptu-page-header>

    @if(count($errors) > 0)
        <ptu-section>
            <article class="card prose">
                <header>There was a problem:</header>
                <ul role="list">
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </article>
        </ptu-section>
    @endif

    <ptu-section>
        <ptu-form action="/account/update-details" method="POST">
            {{ csrf_field() }}
            <ptu-form-row label="Full name">
                <label>
                    Given name
                    <input type="text" name="given_name" value="{{ $user->given_name }}">
                </label>
                <label>
                    Family name
                    <input type="text" name="family_name" value="{{ $user->family_name }}">
                </label>
            </ptu-form-row>

            <ptu-form-row label="Date of birth" inline>
                <label>
                    Day
                    <input name="date_of_birth[day]"
                           maxlength="2"
                           min="1"
                           max="31"
                           size="4"
                           @if(!is_null($user->date_of_birth))
                           value="{{ $user->date_of_birth->day }}"
                           @endif
                    >
                </label>
                <label>
                    Month
                    <input name="date_of_birth[month]"
                           maxlength="2"
                           min="1"
                           max="12"
                           size="4"
                           @if(!is_null($user->date_of_birth))
                           value="{{ $user->date_of_birth->month }}"
                           @endif
                    >
                </label>
                <label>
                    Year
                    <input name="date_of_birth[year]"
                           maxlength="4"
                           min="1920"
                           max="2020"
                           size="7"
                           @if(!is_null($user->date_of_birth))
                           value="{{ $user->date_of_birth->year }}"
                           @endif
                    >
                </label>
            </ptu-form-row>

            <ptu-form-row label="Legal sex" help-text="Why do we ask for sex and not gender?">
                <label class="option">
                    <input type="radio"
                           name="legal_sex"
                           value="M"
                           @if($user->legal_sex == "M")
                               checked
                           @endif
                    >
                    Male
                </label>
                <label class="option">
                    <input type="radio"
                           name="legal_sex"
                           value="F"
                           @if($user->legal_sex == "F")
                               checked
                           @endif
                    >
                    Female
                </label>
                <label class="option">
                    <input type="radio"
                           name="legal_sex"
                           value=""
                           @if($user->legal_sex != "M" && $user->legal_sex != "F")
                               checked
                           @endif
                    >
                    No answer
                </label>

                <p slot="help">
                    This information is optional and you should only provide it if there is a need.
                </p>
                <p slot="help">
                    When calculating whether a property is overcrowded, the sex of the occupants needs
                    to be taken into account as this is how the law is written. This is the only
                    reason we would ask for your sex.
                </p>
            </ptu-form-row>
            <ptu-form-row label="First language" for="language_select">
                <select id="language_select" name="first_language">
                    <option value=""></option>
                    @foreach($languages as $lang)
                        <option value="{{ $lang->value }}"
                                @if($lang->value == $user->first_language)
                                    selected
                                @endif
                        >
                            {{$lang->toLanguageName()}}
                        </option>
                    @endforeach
                </select>
            </ptu-form-row>
        </ptu-form>

    </ptu-section>
@endsection
