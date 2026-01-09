<?php

namespace App\Observers;

use App\Models\ActivityLog;

class ActivityObserver
{
    public function created($model)
    {
        $this->logActivity('created', $model);
    }

    public function updated($model)
    {
        $ignored = ['remember_token', 'updated_at'];

        $dirty = collect($model->getDirty())->except($ignored);

        if ($dirty->isEmpty()) {
            return;
        }

        $this->logActivity('updated', $model, [
            'old' => $model->getOriginal(),
            'new' => $model->getAttributes(),
        ]);
    }

    public function deleted($model)
    {
        $this->logActivity('deleted', $model);
    }

    protected function logActivity($action, $model, $changes = null)
    {
        $ip = request()?->ip();

        // Fallback if no HTTP request (artisan, job, seed, etc.)
        if (! $ip) {
            $ip = '127.0.0.1';
        }

        // Initialize default values
        $country = 'Unknown';
        $region = 'Unknown';
        $city = 'Unknown';

        // Only get geolocation for non-local IPs
        if ($ip !== '127.0.0.1' && $ip !== 'localhost' && !$this->isPrivateIP($ip)) {
            try {
                $location = geoip($ip);
                $locationData = $location->toArray();
                
                // Check if geoip returned default/fallback data
                $isDefault = $locationData['default'] ?? false;
                
                if (!$isDefault) {
                    // Real geolocation data
                    $country = $locationData['country'] ?? 'Philippines';
                    $region = $locationData['state_name'] ?? $locationData['state'] ?? 'Unknown';
                    $city = $locationData['city'] ?? 'Unknown';
                } else {
                    // Fallback data - database not configured properly
                    $country = 'Unknown (DB not configured)';
                    $region = 'Unknown';
                    $city = 'Unknown';
                    
                    \Log::warning('GeoIP returning default data. Run: php artisan geoip:update', [
                        'ip' => $ip,
                        'location_data' => $locationData
                    ]);
                }
            } catch (\Exception $e) {
                // Log error if geoip fails
                \Log::error('GeoIP Error: ' . $e->getMessage(), ['ip' => $ip]);
            }
        } else {
            // Set default values for local/private IPs
            $country = 'Local';
            $region = 'Local';
            $city = 'Local';
        }

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model'      => class_basename($model),
            'changes'    => $changes ? json_encode($changes) : null,
            'ip_address' => $ip,
            'country'    => $country,
            'region'     => $region,
            'city'       => $city,
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }

    /**
     * Check if IP is a private/local IP address
     */
    protected function isPrivateIP($ip)
    {
        $private_ranges = [
            '10.0.0.0|10.255.255.255',
            '172.16.0.0|172.31.255.255',
            '192.168.0.0|192.168.255.255',
            '127.0.0.0|127.255.255.255',
        ];

        $long_ip = ip2long($ip);
        if ($long_ip === false) {
            return true;
        }

        foreach ($private_ranges as $range) {
            list($start, $end) = explode('|', $range);
            if ($long_ip >= ip2long($start) && $long_ip <= ip2long($end)) {
                return true;
            }
        }

        return false;
    }
}