<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LMSStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Total Courses', Course::count())
                ->description('Published courses')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            Stat::make('Total Enrollments', Enrollment::count())
                ->description('Active enrollments')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),

            Stat::make('Total Categories', Category::count())
                ->description('Course categories')
                ->descriptionIcon('heroicon-m-folder')
                ->color('primary'),

            Stat::make('Students', User::where('role', 'student')->count())
                ->description('Registered students')
                ->descriptionIcon('heroicon-m-user')
                ->color('success'),

            Stat::make('Instructors', User::where('role', 'instructor')->count())
                ->description('Active instructors')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('info'),

            Stat::make('Active Enrollments', Enrollment::where('status', 'active')->count())
                ->description('Currently enrolled')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Completed Courses', Enrollment::where('status', 'completed')->count())
                ->description('Finished courses')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),
        ];
    }
}
