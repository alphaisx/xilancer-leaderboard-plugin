<?php

namespace Modules\Rank\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Modules\Rank\Entities\Ambassador;

class AmbassadorController extends Controller
{
    public function __construct() {}

    public function admin_index()
    {
        $ambassadors = Ambassador::with('user', 'user.user_complete_orders', 'user.freelancer_orders')->orderBy('created_at', 'desc')->paginate(20);
        return view('rank::admin.ambassador.manage-ambassador', compact('ambassadors'));
    }

    public function user_form()
    {
        $user = Auth::guard('web')->user();
        if ($user) {
            $ambassador = Ambassador::where('user_id', $user->id)->first();
            if ($ambassador && !$ambassador->is_ambassador) {
                // User has already submitted an application
                return redirect()->route($user->user_type == 1 ? 'client.dashboard' : 'freelancer.dashboard')->with(toastr_info('You have already submitted an application.'));
            }
        }

        return view('rank::frontend.ambassador.registration-form');
    }

    public function submit_form(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:ambassadors,email',
            'phone' => 'required|string|max:20',
            'school' => 'required|string|max:255',
            'level' => 'required|string|max:50',
            'course' => 'required|string|max:255',
            'reason' => 'required|string|max:2000',
        ], [
            'email.unique' => 'You have already submitted an application.',
            'reason.required' => 'Description field is required.',
        ]);

        try {
            //code...
            $school = $request->input('school');
            $universities = json_decode(file_get_contents(module_path('Rank', 'Services/Data/world_universities_and_domains.json')), true);
            $address = $fullAddress = null;

            foreach ($universities as $university) {
                if (strcasecmp($university['name'], $school) === 0) {
                    $address = $university;
                    break;
                }
            }

            if ($address) {
                $province = $address['state-province'] ? $address['state-province'] . ', ' : '';
                $country = $address['country'];
                $fullAddress = $province . $country;
            }

            Ambassador::firstOrCreate(
                [
                    'user_id' => Auth::user()->id,
                ],
                [
                    'fullname' => $request->input('fullname'),
                    'email' => $request->input('email'),
                    'address' => $fullAddress,
                    'phone' => $request->input('phone'),
                    'school' => $request->input('school'),
                    'level' => $request->input('level'),
                    'course' => $request->input('course'),
                    'reason' => $request->input('reason'),
                ]
            );
            return response()->json(['success' => true, 'message' => 'Your application has been submitted successfully. We will review it shortly.']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'An error occurred while submitting your application. Please try again later.'], 500);
        }
    }

    public function bulk_actions(Request $request)
    {
        $request->validate([
            'payloads' => 'required|array',
            'payloads.*.id' => 'required|integer|exists:ambassadors,id',
            'action' => 'required|string|in:approve,set_admin',
        ], [
            'payloads.*.id.exists' => __('The selected ambassador does not exist.'),
            'action.in' => __('Invalid action specified. Only "approve" and "set_admin" are allowed.'),
        ]);
        $action = $request->input('action');
        $payloads = $request->input('payloads');

        try {
            foreach ($payloads as $payload) {
                $ambassadorId = $payload['id'];

                $ambassador = Ambassador::findOrFail($ambassadorId);

                if ($action === 'approve') {
                    $ambassador->is_ambassador = true;
                    $ambassador->approved_at = now();
                    $ambassador->approved_by = Auth::user()->id;
                    $ambassador->save();
                } elseif ($action === 'set_admin') {
                    // Logic to set as community admin
                    // For example, you might want to update a field or assign a role
                } elseif ($action === 'delete') {
                    // $ambassador->delete();
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => __('Bulk action completed successfully.'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => __('An error occurred while processing bulk actions. :error', ['error' => $th->getMessage()]),
            ]);
        }
    }

    public function approve_as_ambassador(Request $request)
    {
        $request->validate([
            'ambassador_id' => 'required|integer|exists:ambassadors,id',
        ], [
            'ambassador_id.exists' => __('The selected ambassador does not exist.'),
        ]);

        try {
            $ambassador = Ambassador::findOrFail($request->input('ambassador_id'));
            // $ambassador->is_ambassador = true;
            // $ambassador->approved_at = now();
            // $ambassador->approved_by = Auth::user()->id;
            // $ambassador->save();

            return redirect()->back()->with(toastr_success('Ambassador approved successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with(toastr_error('An error occurred while approving the ambassador. Please try again later.'));
        }
    }

    public function delete_ambassador($id)
    {
        try {
            $ambassador = Ambassador::findOrFail($id);
            $ambassador->delete();

            return redirect()->back()->with(toastr_success('Ambassador record deleted successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with(toastr_error('An error occurred while deleting the ambassador record. Please try again later.'));
        }
    }
}
