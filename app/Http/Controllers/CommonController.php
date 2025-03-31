<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\DocSession;
use App\Models\Doctor;
use App\Models\Clinic;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
class CommonController extends Controller
{
    // Register
    public function register(Request $request)
    { 
        try {
      
        $request->validate([
            'name'     => 'required|string|max:50',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6|',
        ]);

       
            User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'role'     => 'patient',
                'password' => Hash::make($request->password),
            ]);

            return response()->json(['success' => 'You registered!']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'name'  => $user->name,
            'role'=> $user->role,
            'token' => $token
        ]);
    }

    // Get clinics for a doctor
    public function clinicsForDoctor(int $doctor_id)
    {
        try {
            $result = [];
            $sessions = DocSession::where('doctor_id', $doctor_id)->with('clinic')->get();

            foreach ($sessions as $session) {
                if ($session->clinic) {
                    $result[] = [
                        'name'  => $session->clinic->name,
                        'image' => $session->clinic->image,
                    ];
                }
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('clinicsForDoctor error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    // Get doctors for a clinic
    public function doctorsForClinic(int $clinic_id)
    {
        try {
            $result = [];
            $sessions = DocSession::where('clinic_id', $clinic_id)->with('doctor.user')->get();

            foreach ($sessions as $session) {
                if ($session->doctor && $session->doctor->user) {
                    $result[] = [
                        'name'  => $session->doctor->user->name,
                        'image' => $session->doctor->image,
                    ];
                }
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('doctorsForClinic error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    // Get sessions for a clinic
    public function sessionsForClinic(int $clinic_id)
    {
        try {
            $result = [];
            $sessions = DocSession::where('clinic_id', $clinic_id)->with('doctor.user')->get();

            foreach ($sessions as $session) {
                if ($session->doctor && $session->doctor->user) {
                    $result[] = [
                        'name'   => $session->doctor->user->name,
                        'image'  => $session->doctor->image,
                        'status' => $session->doctor->availability,
                    ];
                }
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('sessionsForClinic error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function allDoctors()
    {
     try{   
      return response()->json(Doctor::all());  
     }
     catch(Exception $e)
     {
        Log::error('sessionsForClinic error: ' . $e->getMessage());
        return response()->json(['error' => 'Something went wrong!'], 500);
     }
    }
    public function allClinics()
    {
      try{  
        return response()->json(Clinic::select('id', 'name', 'image')->get());
    }
      catch(Exception $e)
      {
        Log::error('sessionsForClinic error: ' . $e->getMessage());
        return response()->json(['error' => 'Something went wrong!'], 500);
      }
    }
    public function getNearstSessionForDoc()
    {
        try {
            $now = Carbon::now()->toDateString(); // Convert to YYYY-MM-DD to avoid time mismatches
        
            $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        
            if (!$doctor) {
                return response()->json(['error' => 'Doctor not found'], 404);
            }
        
            $session = DocSession::where('doctor_id', $doctor->id)
                ->where('availability', '!=', 'finished')
                ->whereDate('date', '>=', $now) // Ensures only future or today (without time issues)
                ->orderBy('date', 'asc')
                ->first();
        
            if ($session) {
                return response()->json([
                    'id'     => $session->id,
                    'date'   => $session->date,
                    'clinic' => $session->clinic->name ?? 'N/A', // Safe check for clinic
                    'status' => $session->availability,
                ]);
            } else {
                return response()->json(['message' => 'No upcoming sessions found.']);
            }
        } catch (\Exception $e) {
            \Log::error('Session Fetch Error: ' . $e->getMessage()); // Log error instead of dd()
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function categoriesOfClinic(int $clinic_id)
    {
        try {
            $result = [];
            $sessions = DocSession::where('clinic_id', $clinic_id)->with('doctor.category')->get();

            foreach ($sessions as $session) {
                if ($session->doctor && $session->doctor->category) {
                    $result[] = [
                        'name'   => $session->doctor->category->name,
                    ];
                }
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('sessionsForClinic error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }  
    }

    public function appointments($session_id)
{
    try {
        $appointments = DB::table('appointments')
            ->join('users', 'appointments.patient_id', '=', 'users.id')
            ->select(
                'appointments.id as id',
                'users.name as name',
                'appointments.token as token',
                'appointments.status as status'
            )->where('session_id',$session_id)
            ->get();

        return response()->json($appointments, 200);

    } catch (Exception $e) {
        // Log the errordd()
        Log::error('sessionsForClinic error: ' . $e->getMessage());

        // Return JSON response with error message
        return response()->json(['error' => 'Something went wrong!'], 500);
    }
}

     public function setSessionAvailability(Request $request,int $session_id)
     {
      try{  
       $request->validate(['availability'=>Rule::in(['ongoing','finished'])]);  
       DocSession::where('id',$session_id)->update(['availability'=>$request->availability]);
       return response()->json(['success' => 'Done!']);
    }
      catch(Exception $e)
      {
        Log::error('sessionsForClinic error: ' . $e->getMessage());
        return response()->json(['error' => 'Something went wrong!'], 500);
      } 
    }

    public function newAppointment(Request $request)
    {
        try{
          $request->validate(['session_id'=>'exists:doc_sessions,id']);
          $token=$this->generate4DigitToken(auth()->user()->id,$request->session_id);
          Appointment::create([
              'session_id'=>$request->session_id,
              'patient_id'=>auth()->user()->id,
              'token'=>$token,
              'status'=>'pending'
            ]);
          return response()->json(['token'=>$token]);  
            
        }
        catch(Exception $e)
        {
            Log::error('sessionsForClinic error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    function generate4DigitToken($userId, $sessionId)
    {
        $base = $userId . $sessionId;
        $hash = crc32($base); // Creates a numeric hash
        $code = abs($hash) % 10000; // Limit to 4 digits
        return str_pad($code, 4, '0', STR_PAD_LEFT); // Pad if needed
    }

    function startAppointment(int $appointment_id)
    {
     try{   
     Appointment::find($appointment_id)->update(['status'=>'checking']);
     }   catch(Exception $e)
     {
         Log::error('sessionsForClinic error: ' . $e->getMessage());
         return response()->json(['error' => 'Something went wrong!'], 500);
     }

    }
    function endAppointment(int $appointment_id)
    {
     try{   
     Appointment::find($appointment_id)->update(['status'=>'checked']);
     }   catch(Exception $e)
     {
         Log::error('sessionsForClinic error: ' . $e->getMessage());
         return response()->json(['error' => 'Something went wrong!'], 500);
     }

    }
}

