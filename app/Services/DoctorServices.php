<?php
namespace App\Services;


use App\DoctorAppointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DoctorServices
{

    public function getSlots($day, $doctorId)
    {
        $startHour = 9;

        $day = Carbon::createFromTimestamp($day)->addDay()->format('Y-m-d');

        $blocked = Cache::has('blockedSlots') ? Cache::get('blockedSlots') : [];

        $appointments = DoctorAppointment::where('day', $day)->where('doctor_id', $doctorId)->pluck('user_id', 'slot')->all();

        $startTime = Carbon::now()->setHour($startHour)->startOfHour();
        $user = Auth::check() ? Auth::user()->id : null;

        $fillingSlot = function ($k) use($doctorId, $day, $blocked, $appointments, &$startTime, $user) {

            $isMy = isset($appointments[$k]) && $user == $appointments[$k] ? : false;
            $isBlocked = isset($blocked[$doctorId.'_'.$day.'_'.$k]);

            return [
                'time' => $startTime->addMinutes($k ? 30 : 0)->format('H:i'),
                'busy' => !$isMy && (isset($appointments[$k]) || $isBlocked),
                'isMy' => $isMy
            ];
        };

        $slots = [
            $fillingSlot(0)
        ];

        for ($i = $startHour; $i < 17.5; $i += 0.5) {
            $slots[] = $fillingSlot(count($slots));
        }

        return ['slots' => $slots, 'day' => $day];
    }

    public function blockSlot($key)
    {
        $blocked = Cache::has('blockedSlots') ? Cache::get('blockedSlots') : [];

        if (count($blocked) && isset($blocked[$key])) {
            return true;
        }

        $blocked[$key] = Auth::user()->id;

        Cache::forever('blockedSlots', $blocked);

        return true;
    }

    public function unblockSlot($key)
    {
        $blocked = Cache::has('blockedSlots') ? Cache::get('blockedSlots') : [];

        if (count($blocked) && isset($blocked[$key]) && $blocked[$key] == Auth::user()->id) {
            unset($blocked[$key]);
        }

        Cache::forever('blockedSlots', $blocked);

        return true;
    }

    public function getMySlots($user)
    {
        return Cache::remember('my-slots:'.$user->id, config('cache.lifetime'), function () use($user) {

            //можно было бы через ОРМ, но решил так как демонстрация запросов
            $rows =
                \DB::table('doctor_appointments')
                    ->selectRaw('doctor_appointments.day, doctor_appointments.slot, doctor_services.price, services.name as service, doctors.name as doctor')
                    ->leftJoin('doctor_services', function ($join) {
                        $join->on('doctor_services.service_id', '=', 'doctor_appointments.service_id')
                            ->on('doctor_services.doctor_id', '=', 'doctor_appointments.doctor_id');
                    })
                    ->leftJoin('services', 'doctor_appointments.service_id', '=', 'services.id')
                    ->leftJoin('doctors', 'doctor_appointments.doctor_id', '=', 'doctors.id')
                    ->where('doctor_appointments.user_id', $user->id)
                    ->orderBy('doctor_appointments.day', 'desc')
                    ->orderBy('doctor_appointments.slot', 'desc')
                    ->get();

            foreach ($rows as $row) {
                $row->slot = Carbon::now()->setHour(9)->startOfHour()->addMinutes(30 * $row->slot)->format('H:i');
            }

            return $rows;
        });
    }
}