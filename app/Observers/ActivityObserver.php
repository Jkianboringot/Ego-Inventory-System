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
        // Get real IP address, considering proxies/load balancers
        $ip = $this->getRealIpAddress();

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
                    // Real geolocation data - use the correct property names
                    $country = $locationData['country'] ?? 'Unknown';
                    $region = $locationData['state_name'] ?? $locationData['state'] ?? 'Unknown';
                    $city = $locationData['city'] ?? 'Unknown';
                } else {
                    // Fallback data - database not configured properly
                    $country = 'Unknown';
                    $region = 'Unknown';
                    $city = 'Unknown';
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
     * Get the real IP address from request headers
     */
    protected function getRealIpAddress()
    {
        $request = request();
        
        if (!$request) {
            return null;
        }

        // Check various headers that proxies/load balancers use
        $headers = [
            'HTTP_CF_CONNECTING_IP',    // Cloudflare
            'HTTP_X_REAL_IP',            // Nginx proxy
            'HTTP_X_FORWARDED_FOR',      // Standard proxy header
            'HTTP_CLIENT_IP',            // Some proxies
            'REMOTE_ADDR',               // Direct connection
        ];

        $fallbackIp = null;

        foreach ($headers as $header) {
            $ip = $request->server($header);
            
            if ($ip) {
                // HTTP_X_FORWARDED_FOR can contain multiple IPs (client, proxy1, proxy2)
                // We want the first one (the client's real IP)
                if (strpos($ip, ',') !== false) {
                    $ips = array_map('trim', explode(',', $ip));
                    $ip = $ips[0];
                }
                
                // Validate the IP address
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
                
                // If it's a valid IP but private/reserved, continue checking other headers
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    // Store it as fallback in case we don't find a public IP
                    $fallbackIp = $fallbackIp ?? $ip;
                }
            }
        }

        // Return fallback IP or Laravel's default method
        return $fallbackIp ?? $request->ip();
    }

    /**
     * Check if IP is a private/local IP address
     */
    protected function isPrivateIP($ip)
    {
        // Handle IPv6 addresses
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // Check for IPv6 localhost
            if (in_array($ip, ['::1', '::ffff:127.0.0.1'])) {
                return true;
            }
            // Check for IPv6 private ranges (fc00::/7, fe80::/10)
            if (strpos($ip, 'fc') === 0 || strpos($ip, 'fd') === 0 || strpos($ip, 'fe80') === 0) {
                return true;
            }
            return false;
        }

        // Handle IPv4 addresses
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