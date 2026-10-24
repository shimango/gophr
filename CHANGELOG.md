# Changelog

### 2.1.0
- Added `generateLabel()` for the delivery label endpoint
- Added the new API fields: `pin_required`, `fallback_handover_method`, `safe_place_location` and `leave_safe_instructions` on dropoffs, `dangerous_goods` and `quantity` on parcels, `is_medical` and `min_required_age` on parcel responses, and `average_delivery_time` on quotes
- Response objects now ignore fields they do not know, so new API fields no longer make `getContentsObject()` return `null`
- Empty or invalid JSON bodies no longer throw, `getContentsArray()` returns an empty array instead
- Money amounts and quote prices can now be `null`, and `min_realistic_time` is a float to match the API
- Fixed `CreateParcelRequestDto` failing to load, the HTTP/2 check, the proxy URL, the `Host` header and case-insensitive response headers
- `booking_method` is now sent in `meta_data` as a `key`/`value` pair
- Rewrote the README with verified examples for every endpoint

### 2.0.4
- Get the base API URL used by the client

### 2.0.3
- Added booking method to the requests

### 2.0.2
- Added missing properties to Job Dto

### 2.0.1
- Bug fixes

### 2.0.0
- PHP upgrade

### 1.0.10
- Added support for local development using environment variables

### 1.0.9
- Fixed wrong Xdebug session key

### 1.0.8
- Fixed issues with test payloads date times

### 1.0.7
- Added method to progress delivery statuses
- Some code Refactoring

### 1.0.6
- Tested on PHP 8.0 and 8.1

### 1.0.5
- Updated the README file. Added badges.

### 1.0.4
- Updated the README file

### 1.0.3
- Updated the README file

### 1.0.2
- Create tests.yml for the Github test pipeline

### 1.0.1
- Corrected library description and removed test coverage data

## 1.0.0
- initial release
