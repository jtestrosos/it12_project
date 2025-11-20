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
        $occupiedSlots = self::getOccupiedSlots($date);
        
        // Debug: Log what we're working with
        \Log::info("Date: {$date}");
        \Log::info("Occupied slots from DB: " . json_encode($occupiedSlots));
        \Log::info("All time slots: " . json_encode($timeSlots));
        
        $availableSlots = [];
        foreach ($timeSlots as $slot) {
            $isOccupied = in_array($slot['time'], $occupiedSlots);
            
            // Debug: Log each slot check
            \Log::info("Checking slot {$slot['time']} against occupied slots: " . ($isOccupied ? 'OCCUPIED' : 'AVAILABLE'));
            
            $availableSlots[] = [
                'time' => $slot['time'],
                'display' => $slot['display'],
                'available' => !$isOccupied,
                'occupied_count' => self::getSlotOccupancyCount($date, $slot['time'])
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
     * Get occupied time slots for a given date
     * 
     * @param string $date
     * @return array
     */
    public static function getOccupiedSlots($date)
    {
        $appointments = Appointment::whereDate('appointment_date', $date)
            ->whereIn('status', ['approved', 'completed'])
            ->get();
        
        // Debug: Log the raw appointment_time values
        $rawTimes = $appointments->pluck('appointment_time')->toArray();
        \Log::info("Raw appointment_time values: " . json_encode($rawTimes));
        
        $occupiedSlots = $appointments->pluck('appointment_time')
            ->map(function($time) {
                // Debug: Log each time value and its length
                \Log::info("Processing time: '{$time}' (length: " . strlen($time) . ")");
                
                // Handle different time formats
                if (strlen($time) > 5) {
                    // If it's a datetime, extract just the time part
                    return substr($time, 11, 5); // Extract HH:MM from datetime format
                }
                return $time;
            })
            ->toArray();
        
        \Log::info("Processed occupied slots: " . json_encode($occupiedSlots));
        return $occupiedSlots;
    }
    
    /**
     * Get the number of appointments for a specific time slot
     * 
     * @param string $date
     * @param string $time
     * @return int
     */
    public static function getSlotOccupancyCount($date, $time)
    {
        return Appointment::whereDate('appointment_date', $date)
            ->where('appointment_time', $time)
            ->whereIn('status', ['approved', 'completed'])
            ->count();
    }
    
    /**
     * Check if a time slot is available
     * 
     * @param string $date
     * @param string $time
     * @return bool
     */
    public static function isSlotAvailable($date, $time)
    {
        $occupiedCount = self::getSlotOccupancyCount($date, $time);
        return $occupiedCount === 0;
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
        $date = Carbon::create($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($year, $month, $day)->format('Y-m-d');
            $occupiedSlots = self::getOccupiedSlots($currentDate);
            $totalSlots = count(self::getAllTimeSlots());
            $availableSlots = $totalSlots - count($occupiedSlots);
            
            $calendar[] = [
                'date' => $currentDate,
                'day' => $day,
                'total_slots' => $totalSlots,
                'occupied_slots' => count($occupiedSlots),
                'available_slots' => $availableSlots,
                'is_fully_occupied' => $availableSlots === 0,
                'is_weekend' => $date->copy()->addDays($day - 1)->isWeekend(),
                'is_past' => Carbon::create($year, $month, $day)->isPast(),
            ];
        }
        
        return $calendar;
    }
}
