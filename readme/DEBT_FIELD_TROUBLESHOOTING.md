# Troubleshooting: Max Debt Field Not Editable

## Recent Changes Made

### 1. Enhanced Reactivity
- Changed `wire:model="allow_debt"` to `wire:model.live="allow_debt"`
- Added `wire:key` attributes for better DOM tracking
- Added debug display to show current values

### 2. Fixed Field Interaction  
- Changed from `disabled` attribute to `readonly` 
- Updated styling to be conditional based on allow_debt state
- Added `updatedAllowDebt()` method to both Create and Edit components

### 3. Debug Information Added
- Blue debug text shows current `allow_debt` value (true/false)
- Blue debug text shows current `max_debt_limit` value

## How to Test the Fix

### Step 1: Clear All Caches
```bash
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

### Step 2: Test the Form
1. **Login as Admin or General Supervisor**
2. **Go to Create Customer** (`/customers/create`)
3. **Look for the amber section** "إعدادات الدين"
4. **Check the debug values**:
   - Should show: `Current allow_debt value: false`
   - Should show: `Current max_debt_limit value: null`

### Step 3: Test Checkbox Interaction
1. **Click the "السماح بالدين" checkbox**
2. **Watch the debug values change**:
   - Should show: `Current allow_debt value: true`
3. **The max debt field should become editable** (white background)
4. **Enter a negative value** like `-5000`
5. **Watch the debug value update**:
   - Should show: `Current max_debt_limit value: -5000`

## Troubleshooting Steps

### If Checkbox Doesn't Update:

#### Check Browser Console for JS Errors
1. Press `F12` → Console tab
2. Look for any JavaScript errors
3. Common issues:
   - Livewire scripts not loaded
   - CSRF token issues
   - Network connectivity problems

#### Check Livewire Connection
Add this to your blade template temporarily:
```html
<div>
    <p>Livewire Status: @if(app()->environment('local')) Connected @endif</p>
    <p>Current allow_debt: {{ $allow_debt ? 'YES' : 'NO' }}</p>
    <button wire:click="$toggle('allow_debt')" type="button">Toggle Debt Mode</button>
</div>
```

#### Check User Permissions
Make sure you're logged in as Admin or General Supervisor:
```bash
php artisan tinker
>>> auth()->user()->roles->pluck('name')
# Should return: ["admin"] or ["general_supervisor"]
```

### If Field Remains Non-Editable:

#### Try Force Refresh
- Press `Ctrl + F5` (hard refresh)
- Or clear browser cache completely

#### Check Network Tab
1. Press `F12` → Network tab
2. Click the checkbox
3. Look for Livewire AJAX requests
4. Should see requests to `/livewire/message/...`

#### Alternative Test Method
Try using the browser console:
```javascript
// Open browser console and try:
document.getElementById('allow_debt').checked = true;
document.getElementById('allow_debt').dispatchEvent(new Event('change'));
```

### Manual Override (Temporary)
If still not working, try removing the conditional entirely:
```html
<!-- Temporary test - remove readonly/disabled -->
<input type="number" wire:model.live="max_debt_limit" id="max_debt_limit" 
    step="0.01" max="0" placeholder="-5000"
    class="w-full px-4 py-3 bg-white/80 border border-slate-200 rounded-xl">
```

## Expected Behavior

### When Allow Debt = FALSE (unchecked):
- Checkbox: ⬜ (unchecked)
- Debug: `Current allow_debt value: false`
- Max Debt Field: Gray background, readonly
- Field Value: null or empty

### When Allow Debt = TRUE (checked):
- Checkbox: ☑️ (checked)  
- Debug: `Current allow_debt value: true`
- Max Debt Field: White background, editable
- Field Value: User can enter negative numbers

## Next Steps

1. **Test with debug information** to see if values are changing
2. **Check browser console** for JavaScript errors
3. **Verify user permissions** (Admin/General Supervisor required)
4. **Try manual toggle** using browser console if needed

The debug information should help us identify exactly where the issue is occurring!
