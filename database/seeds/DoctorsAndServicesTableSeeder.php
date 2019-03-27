<?php

use Illuminate\Database\Seeder;
use App\Service;
use App\Doctor;

class DoctorsAndServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $doctors = [
            'Доктор 1',
            'Доктор 2',
            'Доктор 3',
            'Доктор 4',
            'Доктор 5',
            'Доктор 6',
            'Доктор 7',
            'Доктор 8',
        ];
        foreach ($doctors as $doc) {
            Doctor::create(['name' => $doc]);
        }

        $services = [
            'Узи почек и надпочечников',
            'Узи поджелудочной железы',
            'Узи лимфатических узлов',
            'Лечение зубов',
            'Проверка зрения',
            'Анализ крови',
            'Анализ мочи',
            'Прием (осмотр, консультация) врача-терапевта',
            'Клинико-психологическое консультирование',
            'Ультразвуковая допплерография',
            'Экстракортикальный остеосинтез бедренной кости',
            'Тромболиз',
            'Артроскопическая  аутопластика передней крестообразной связки',
            'Коронарное шунтирование в условиях искусственного кровобращения'
        ];

        foreach ($services as $service) {
            Service::create(['name' => $service]);
        }

        foreach (Doctor::all() as $doc) {
            foreach (Service::all()->random(5) as $service) {
                \App\DoctorService::create([
                    'service_id' => $service->id,
                    'doctor_id' => $doc->id,
                    'price' => rand(10000, 500000) / 100
                ]);
            }
        }
    }
}
