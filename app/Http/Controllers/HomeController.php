<?php

namespace App\Http\Controllers;

use App\DoctorAppointment;
use App\DoctorService;
use Illuminate\Support\Facades\Auth;
use App\Service;
use App\Services\DoctorServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{

    public $service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DoctorServices $service)
    {
        $this->middleware('auth');

        $this->service = $service;
    }

    /**
     * Главная страница
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        return view('doc_appointment.index');
    }

    public function getServices()
    {

        return Cache::remember('services', config('cache.lifetime'), function () {
            return Service::select(['id', 'name'])->get();
        });
    }

    public function getDoctors($serviceId)
    {

        return Cache::remember('doctorsByService:'.$serviceId, config('cache.lifetime'), function () use($serviceId) {
            return Service::findOrFail($serviceId)->doctors()->with('services.service')->get();
        });
    }

    public function getSchedule(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'day' => 'required|integer',
            'doctorId' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response('Ошибка валидации', 402);
        }

        $doctorId = $request->get('doctorId');
        $day = $request->get('day') / 1000;

        return $this->service->getSlots($day, $doctorId);
    }

    public function postBlockSlot(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'doctor' => 'required|integer',
            'day' => 'required|date',
            'slot' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response('Ошибка валидации', 402);
        }

        $key = $request->get('doctor', '')
            . '_' . $request->get('day')
            . '_' . $request->get('slot');

        $this->service->blockSlot($key);

        return 'ok';
    }

    public function postUnBlockSlot(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'doctor' => 'required|integer',
            'day' => 'required|date',
            'slot' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response('Ошибка валидации', 402);
        }

        $key = $request->get('doctor', '')
            . '_' . $request->get('day')
            . '_' . $request->get('slot');

        $this->service->unblockSlot($key);

        return 'ok';
    }

    public function postRemoveSlot(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'doctor' => 'required|integer',
            'day' => 'required|date',
            'slot' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response('Ошибка валидации', 402);
        }

        $day = $request->get('day');
        $slot = $request->get('slot');
        $doctorId = $request->get('doctor');

        $appointment = DoctorAppointment::where('day', $day)
            ->where('doctor_id', $doctorId)
            ->where('slot', $slot)
            ->where('user_id', Auth::user()->id)
            ->first();

        Cache::forget('my-slots:'.Auth::user()->id);

        return !$appointment
            ? response('Ошибка удаления', 402)
            : (int) $appointment->delete();
    }

    public function postCreateSlot(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'doctor' => 'required|integer',
            'day' => 'required|date',
            'slot' => 'required|integer',
            'service' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response('Ошибка валидации', 402);
        }

        $blocked = Cache::has('blockedSlots') ? Cache::get('blockedSlots') : [];
        $day = $request->get('day');
        $slot = $request->get('slot');
        $doctorId = $request->get('doctor');
        $serviceId = $request->get('service');

        $isBlocked = isset($blocked[$doctorId.'_'.$day.'_'.$slot]) && $blocked[$doctorId.'_'.$day.'_'.$slot] != Auth::user()->id;

        if ($isBlocked) {
            return response('Данное время уже занято', 402);
        }

        $da = DoctorAppointment::create([
                'day' => $day,
                'doctor_id' => $doctorId,
                'slot' => $slot,
                'user_id' => Auth::user()->id,
                'service_id' => $serviceId
            ]);

        Cache::forget('my-slots:'.Auth::user()->id);

        return $da ? 'ok' : response('Ошибка', 402);
    }

    public function getMySlots()
    {

        $user = Auth::user();

        return $this->service->getMySlots($user);
    }
}
