@echo off
cd /d C:\Users\Hannah\Documents\GitHub\VPA-Booking-System
php artisan forms:mark-late
echo %DATE% %TIME% - Late form check completed >> logs\scheduler.log