# Debt Mode Implementation

## Overview
Successfully implemented a comprehensive Debt Mode feature that allows customers to have negative balances within specified limits.

## Features Implemented

### 1. Database Schema
- **Migration**: `2025_09_02_143430_add_debt_mode_to_customers_table.php`
- **Fields Added**:
  - `allow_debt` (boolean, default false)
  - `max_debt_limit` (decimal, nullable, must be negative or zero)

### 2. Customer Model Enhancements
- **File**: `app/Models/Domain/Entities/Customer.php`
- **New Methods**:
  - `canSendAmount(float $amount)`: Checks if customer can send specified amount considering debt limits
  - `getAvailableSendingLimit()`: Returns maximum amount customer can send (balance + debt limit)
- **Updated Logic**:
  - Balance validation now considers debt limits for customers with `allow_debt = true`
  - Negative balances allowed within `max_debt_limit` constraint

### 3. Customer Creation & Editing
- **CreateCustomer Use Case**: Updated to accept `allowDebt` and `maxDebtLimit` parameters
- **UpdateCustomer Use Case**: Updated to accept `allowDebt` and `maxDebtLimit` parameters
- **Customer Create Form**: Added debt mode fields with validation
- **Customer Edit Form**: Added debt mode fields with validation

### 4. Transaction Logic Updates

#### Send Money Transactions
- **File**: `app/Livewire/Transactions/Send.php`
- **Enhancement**: Uses `Customer::canSendAmount()` method for balance validation
- **Logic**: Prevents sending if amount exceeds available limit (balance + debt limit)

#### Receive/Deposit Transactions  
- **File**: `app/Application/UseCases/CreateTransaction.php`
- **Enhancement**: Smart debt reduction logic for Deposit/Receive transactions
- **Logic**: 
  1. If customer has debt (negative balance), reduce debt first
  2. Any remaining amount goes to positive balance
  3. Full amount applied if no debt exists

### 5. UI Enhancements

#### Customer Listing
- **File**: `resources/views/livewire/customers/index.blade.php`
- **Enhancement**: Red highlighting for customers with negative balances
- **Visual Indicator**: Shows "(دين)" label and warning icon for negative balances

#### Customer Forms
- **Create Form**: Debt mode checkbox and debt limit field (admin/supervisor only)
- **Edit Form**: Debt mode checkbox and debt limit field (admin/supervisor only)
- **Validation**: Debt limit required when debt mode enabled, must be negative

## Business Rules

### Debt Limits
- Only customers with `allow_debt = true` can have negative balances
- `max_debt_limit` must be negative or zero (e.g., -1000 means customer can owe up to 1000 EGP)
- Customers cannot send money if it would exceed their debt limit

### Transaction Processing
- **Send Transactions**: Check available limit = current_balance + abs(max_debt_limit)
- **Receive Transactions**: Reduce debt first, then add to positive balance
- **Balance Display**: Red highlighting for negative balances with debt indicator

### Authorization
- Only Admin and General Supervisor can configure debt mode
- Debt mode settings visible/editable in customer create/edit forms
- Regular agents cannot modify debt settings

## Files Modified

### Models & Use Cases
- `app/Models/Domain/Entities/Customer.php`
- `app/Application/UseCases/CreateCustomer.php`
- `app/Application/UseCases/UpdateCustomer.php`
- `app/Application/UseCases/CreateTransaction.php`

### Livewire Components
- `app/Livewire/Customers/Create.php`
- `app/Livewire/Customers/Edit.php`
- `app/Livewire/Transactions/Send.php`

### Views
- `resources/views/livewire/customers/create.blade.php`
- `resources/views/livewire/customers/edit.blade.php`
- `resources/views/livewire/customers/index.blade.php` (red highlighting already existed)

### Database
- `database/migrations/2025_09_02_143430_add_debt_mode_to_customers_table.php`

## Testing Scenarios

### 1. Customer Creation with Debt Mode
1. Navigate to customer creation form
2. Enable "Allow Debt" checkbox
3. Set negative debt limit (e.g., -1000)
4. Create customer successfully

### 2. Send Money with Debt Limits
1. Create customer with debt mode (balance: 500, debt limit: -1000)
2. Try to send 1600 EGP (should fail - exceeds available limit of 1500)
3. Try to send 1400 EGP (should succeed - within available limit)
4. Customer balance becomes -900 EGP (within debt limit)

### 3. Receive Money with Debt Reduction
1. Customer has negative balance of -500 EGP
2. Receive 800 EGP
3. System reduces debt by 500, adds remaining 300 to positive balance
4. Final balance: +300 EGP

### 4. UI Visual Indicators
1. Customer with negative balance shows red highlighting in customer list
2. Balance displays as red with "(دين)" indicator
3. Debt mode fields only visible to admin/supervisor

## Status
✅ **FULLY IMPLEMENTED** - All debt mode features are complete and ready for production use.

### Components Ready
- ✅ Database schema with proper constraints
- ✅ Model logic with debt validation methods
- ✅ Customer creation and editing with debt mode support
- ✅ Transaction processing with debt-aware logic
- ✅ UI with visual debt indicators
- ✅ Authorization controls for debt mode access
