<?php

namespace App\Helpers;

use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentHelper
{
    /**
     * Get all available time slots for a given date
     * 
     * @param string $date
     * @return array
     */
    public static function getAvailableSlots($date)
    {
        $timeSlots = self::getAllTimeSlots();
        $selectedDate = Carbon::parse($date);
        $now = Carbon::now();
        $isToday = $selectedDate->isToday();

        // Optimize: Use DB aggregation instead of fetching all records
        $counts = Appointment::whereDate('appointment_date', $date)
            ->whereIn('status', ['approved', 'completed'])
            ->selectRaw('appointment_time, count(*) as total')
            ->groupBy('appointment_time')
            ->pluck('total', 'appointment_time');

        // Map counts to normalized time strings (H:i)
        $normalizedCounts = [];
        foreach ($counts as $time => $count) {
            $normalizedTime = substr($time, 0, 5); // Convert 08:00:00 to 08:00
            $normalizedCounts[$normalizedTime] = $count;
        }

        $availableSlots = [];
        foreach ($timeSlots as $slot) {
            $count = $normalizedCounts[$slot['time']] ?? 0;
            
            // Check if the time slot has passed (only for today)
            $isPast = false;
            if ($isToday) {
                $slotTime = Carbon::parse($date . ' ' . $slot['time']);
                $isPast = $slotTime->isPast();
            }

            $availableSlots[] = [
                'time' => $slot['time'],
                'display' => $slot['display'],
                'available' => $count === 0 && !$isPast, // Not available if occupied or time has passed
                'occupied_count' => $count,
                'is_past' => $isPast
            ];
        }

        return $availableSlots;
    }

    /**
     * Get all predefined time slots (15 slots per day)
     * 
     * @return array
     */
    public static function getAllTimeSlots()
    {
        return [
            ['time' => '08:00', 'display' => '8:00 AM'],
            ['time' => '08:30', 'display' => '8:30 AM'],
            ['time' => '09:00', 'display' => '9:00 AM'],
            ['time' => '09:30', 'display' => '9:30 AM'],
            ['time' => '10:00', 'display' => '10:00 AM'],
            ['time' => '10:30', 'display' => '10:30 AM'],
            ['time' => '11:00', 'display' => '11:00 AM'],
            ['time' => '11:30', 'display' => '11:30 AM'],
            ['time' => '13:00', 'display' => '1:00 PM'],
            ['time' => '13:30', 'display' => '1:30 PM'],
            ['time' => '14:00', 'display' => '2:00 PM'],
            ['time' => '14:30', 'display' => '2:30 PM'],
            ['time' => '15:00', 'display' => '3:00 PM'],
            ['time' => '15:30', 'display' => '3:30 PM'],
            ['time' => '16:00', 'display' => '4:00 PM'],
        ];
    }

    /**
     * Get calendar data for a month
     * 
     * @param int $year
     * @param int $month
     * @return array
     */
    public static function getCalendarData($year, $month)
    {
        $calendar = [];
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        // Optimize: Use DB aggregation to count slots per day
        $dailyCounts = Appointment::whereBetween('appointment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status', ['approved', 'completed'])
            ->selectRaw('appointment_date, count(*) as total')
            ->groupBy('appointment_date')
            ->pluck('total', 'appointment_date');

        $daysInMonth = $startDate->daysInMonth;
        $totalSlotsPerDay = count(self::getAllTimeSlots());

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($year, $month, $day);
            $dateString = $currentDate->format('Y-m-d');

            $occupiedCount = $dailyCounts[$dateString] ?? 0;
            $availableSlots = $totalSlotsPerDay - $occupiedCount;

            $calendar[] = [
                'date' => $dateString,
                'day' => $day,
                'total_slots' => $totalSlotsPerDay,
                'occupied_slots' => $occupiedCount,
                'available_slots' => $availableSlots,
                'is_fully_occupied' => $availableSlots <= 0,
                'is_weekend' => $currentDate->isWeekend(),
                'is_past' => $currentDate->isPast() && !$currentDate->isToday(),
            ];
        }

        return $calendar;
    }
}
