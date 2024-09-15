<?php

use App\Models\User;

if (!function_exists('getFacultyTitle')) {
    /**
     * Get the appropriate title for a faculty member based on their gender.
     *
     * @param  User  $faculty
     * @return string
     */
    function getFacultyTitle(User $faculty)
    {
        $gender = $faculty->gender;

        // Determine title based on gender
        if ($gender === 'male') {
            return 'Mr.';
        } elseif ($gender === 'female') {
            return 'Ms.';
        }

        return 'Mr./Ms.'; // Default if gender is not set or recognized
    }
}

if (!function_exists('formatTime')) {
    function formatTime($hour)
    {
        $period = $hour <= 12 ? 'AM' : 'PM';
        $formattedHour = $hour % 12;
        $formattedHour = $formattedHour == 0 ? 12 : $formattedHour;
        return sprintf('%d:00 %s', $formattedHour, $period);
    }
}