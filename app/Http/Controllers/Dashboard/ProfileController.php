<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Languages;
use App\Models\Profile;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit()
    {
        // مفيش داعي اني ابعت ال id الان اليوزر اوريد عامل لوجين وبالتالي انا عارفه البيانات بتاعته
        $user = Auth::user();
        return view('dashboard.profile.edit', [
            'user' => $user,
            'countries' => Countries::getNames(),
            'locales' => Languages::getNames(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birthday' => ['nullable', 'date', 'before:today'],
            'gender' => ['in:male,female'],
            // 'country' => ['required', 'string', 'size:2'],
        ]);
        // $user = Auth::user();
        $user = $request->user();
        $user->profile->country='ar';

        $user->profile->fill( $request->all() )->save(); // fill=>اما اضافه او تعديل
        // $user->profile->update( $request->all() ); في حاله ضمان ان اليوزر ليه  بروفايل لان ممكن ميبقاش عنده بروفايل
        // $profile = $user->profile;
        // if ($profile->first_name) {
        //     $profile->update($request->all());
        // } else {
        //     $user->profile()->create($request->all()); مش لازم اعمل merge هنا بسبب وجود ال relation
        //     // is equal to

        //     $request->merge([
        //         'user_id'=>$user->id
        //     ]); لان الريكويست لا يتضمن user_id ف الفورم
        //     Profile::create($request->all());
        // }
        // ال relation دايما بتدي قيمه لل forignn key

        return redirect()->route('dashboard.profile.edit')
            ->with('success', 'Profile updated!');





    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
