<?php

namespace App\Http\Controllers;

use App\Mail\AlertMail;
use App\Mail\CheckMail;
use App\Mail\SaudiApplicationMail;
use App\Models\SaudiApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class SaudiApplicationController extends Controller
{
    public function index()
    {
        $applications = SaudiApplication::all();
        return response()->json($applications, 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'coName' => 'required|max:191',
                'coAddress' => 'required|max:255',
                'regNB' => 'required|max:30',
                'activity' => 'required|max:255',
                'empNB' => 'required|integer',
                'cPerson' => 'required|max:191',
                'cEmail' => 'required|email|max:191',
                'cPhone' => 'required|max:20',
                'remark' => 'nullable|max:500',
                'qualCertif' => 'required|max:255',
                'prodReg' => 'required|file|max:2048',
                'facCertif' => 'required|file|max:2048',
            ],
            [
                'coName.required' => 'The company name field is required.',
                'coName.max' => 'The company name may not be greater than 191 characters.',
                'coAddress.required' => 'The company address field is required.',
                'coAddress.max' => 'The company address may not be greater than 255 characters.',
                'regNB.required' => 'The registration number field is required.',
                'regNB.max' => 'The registration number may not be greater than 30 characters.',
                'activity.required' => 'The activity field is required.',
                'activity.max' => 'The activity may not be greater than 255 characters.',
                'empNB.required' => 'The number of employees field is required.',
                'empNB.integer' => 'The number of employees must be an integer.',
                'cPerson.required' => 'The contact person field is required.',
                'cPerson.max' => 'The contact person may not be greater than 191 characters.',
                'cEmail.required' => 'The contact email field is required.',
                'cEmail.email' => 'The contact email must be a valid email address.',
                'cEmail.max' => 'The contact email may not be greater than 191 characters.',
                'cPhone.required' => 'The contact phone field is required.',
                'cPhone.max' => 'The contact phone may not be greater than 20 characters.',
                'remark.max' => 'The remark may not be greater than 500 characters.',
                'qualCertif.required' => 'The quality certification file is required.',
                'qualCertif.max' => 'The quality certification may not be greater than 255 characters.',
                'prodReg.required' => 'The product registration file is required.',
                'prodReg.file' => 'The product registration must be a valid file.',
                'prodReg.max' => 'The product registration file may not be greater than 2048 kilobytes.',
                'facCertif.required' => 'The factory certification file is required.',
                'facCertif.file' => 'The factory certification must be a valid file.',
                'facCertif.max' => 'The factory certification file may not be greater than 2048 kilobytes.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag(),
            ], 422);
        } else {
            $application = new SaudiApplication;
            $application->coName = $request->input('coName');
            $application->coAddress = $request->input('coAddress');
            $application->regNB = $request->input('regNB');
            $application->activity = $request->input('activity');
            $application->empNB = $request->input('empNB');
            $application->cPerson = $request->input('cPerson');
            $application->cEmail = $request->input('cEmail');
            $application->cPhone = $request->input('cPhone');
            $application->remark = $request->input('remark');
            $application->qualCertif = $request->input('qualCertif');

            if ($request->hasFile('prodReg')) {
                $file = $request->file('prodReg');
                $filename = time() . '_prodReg.' . $file->getClientOriginalExtension();
                $file->move('upload/products/', $filename);
                $application->prodReg = 'upload/products/' . $filename;
            }

            if ($request->hasFile('facCertif')) {
                $file = $request->file('facCertif');
                $filename = time() . '_facCertif.' . $file->getClientOriginalExtension();
                $file->move('upload/factories/', $filename);
                $application->facCertif = 'upload/factories/' . $filename;
            }

            $application->save();

            $users = User::where("saudi", 1)->get();
            $fromAddress = env("MAIL_FROM_ADDRESS"); // Get default from address

            foreach ($users as $user) {
                try {
                    // Use primary SMTP settings
                    Mail::mailer('smtp')->to($user->email)->send(new SaudiApplicationMail($application, $fromAddress));
                } catch (\Exception $e) {
                    // Fallback to temporary mailer
                    $temporaryEmail = env("TEMPORARY_EMAIL");
                    Mail::mailer('temp_smtp')->to($user->email)->send(new SaudiApplicationMail($application, $temporaryEmail));

                    // Notify you that the temporary email was used
                    Mail::mailer('temp_smtp')->to(env("DEVELOPER_EMAIL"))
                        ->send(new AlertMail("Email sent to {$user->email} using temporary email.", $temporaryEmail));
                }
            }

            return response()->json([
                'message' => 'Application added successfully',
            ], 200);
        }
    }

    public function edit($id)
    {
        $application = SaudiApplication::find($id);
        if ($application) {
            return response()->json([
                'application' => $application
            ], 200);
        } else {
            return response()->json([
                'message' => 'Application not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'coName' => 'required|max:191',
                'coAddress' => 'required|max:255',
                'regNB' => 'required|max:30',
                'activity' => 'required|max:255',
                'empNB' => 'required|integer',
                'cPerson' => 'required|max:191',
                'cEmail' => 'required|email|max:191',
                'cPhone' => 'required|max:20',
                'remark' => 'nullable|max:500',
                'qualCertif' => 'required|max:255',
            ],
            [

                'coName.required' => 'The company name field is required.',
                'coName.max' => 'The company name may not be greater than 191 characters.',
                'coAddress.required' => 'The company address field is required.',
                'coAddress.max' => 'The company address may not be greater than 255 characters.',
                'regNB.required' => 'The registration number field is required.',
                'regNB.max' => 'The registration number may not be greater than 30 characters.',
                'activity.required' => 'The activity field is required.',
                'activity.max' => 'The activity may not be greater than 255 characters.',
                'empNB.required' => 'The number of employees field is required.',
                'empNB.integer' => 'The number of employees must be an integer.',
                'cPerson.required' => 'The contact person field is required.',
                'cPerson.max' => 'The contact person may not be greater than 191 characters.',
                'cEmail.required' => 'The contact email field is required.',
                'cEmail.email' => 'The contact email must be a valid email address.',
                'cEmail.max' => 'The contact email may not be greater than 191 characters.',
                'cPhone.required' => 'The contact phone field is required.',
                'cPhone.max' => 'The contact phone may not be greater than 20 characters.',
                'remark.max' => 'The remark may not be greater than 500 characters.',
                'qualCertif.required' => 'The quality certification file is required.',
                'qualCertif.max' => 'The quality certification may not be greater than 255 characters.',
                'prodReg.required' => 'The product registration file is required.',
                'prodReg.file' => 'The product registration must be a valid file.',
                'prodReg.max' => 'The product registration file may not be greater than 2048 kilobytes.',
                'facCertif.required' => 'The factory certification file is required.',
                'facCertif.file' => 'The factory certification must be a valid file.',
                'facCertif.max' => 'The factory certification file may not be greater than 2048 kilobytes.',

            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag(),
            ], 422);
        } else {
            $application = SaudiApplication::find($id);
            if ($application) {
                $application->coName = $request->input('coName');
                $application->coAddress = $request->input('coAddress');
                $application->regNB = $request->input('regNB');
                $application->activity = $request->input('activity');
                $application->empNB = $request->input('empNB');
                $application->cPerson = $request->input('cPerson');
                $application->cEmail = $request->input('cEmail');
                $application->cPhone = $request->input('cPhone');
                $application->remark = $request->input('remark');
                $application->qualCertif = $request->input('qualCertif');

                if ($request->hasFile('prodReg')) {
                    $path = $application->prodReg;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    $file = $request->file('prodReg');
                    $filename = time() . '_prodReg.' . $file->getClientOriginalExtension();
                    $file->move('upload/products/', $filename);
                    $application->prodReg = 'upload/products/' . $filename;
                }

                if ($request->hasFile('facCertif')) {
                    $path = $application->facCertif;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    $file = $request->file('facCertif');
                    $filename = time() . '_facCertif.' . $file->getClientOriginalExtension();
                    $file->move('upload/factories/', $filename);
                    $application->facCertif = 'upload/factories/' . $filename;
                }

                $application->save();

                return response()->json([
                    'message' => 'Application updated',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Application not found',
                ], 404);
            }
        }
    }

    public function destroy($id)
    {
        $application = SaudiApplication::find($id);

        if ($application) {
            $application->delete();
            return response()->json([
                'message' => 'Application deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Application not found!',
            ], 404);
        }
    }
}
