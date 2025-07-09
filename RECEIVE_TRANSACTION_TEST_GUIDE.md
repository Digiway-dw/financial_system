# Receive Transaction - Quick Test Guide

## Testing the Receive Transaction Feature

### Prerequisites
1. Server running: `php artisan serve`
2. Navigate to: `http://127.0.0.1:8000/transactions/receive`
3. Ensure you're logged in as an agent with a branch

### Test Scenarios

#### 1. Basic Receive Transaction
```
Client Mobile: 01123456789
Client Name: Ahmed Hassan
Sender Mobile: 01987654321
Amount: 1000
Expected Commission: 10 EGP (floor(1000/500) * 5)
Expected Safe Deduction: 990 EGP (1000 - 10)
```

#### 2. Receive with Discount
```
Client Mobile: 01234567890
Client Name: Fatma Ali
Sender Mobile: 01876543210
Amount: 1500
Discount: 5
Discount Notes: Regular customer discount
Expected Commission: 10 EGP (15 - 5 discount)
Expected Safe Deduction: 1490 EGP (1500 - 10)
```

#### 3. Commission Calculation Tests
- Amount 500 → Commission 5 EGP
- Amount 999 → Commission 5 EGP
- Amount 1000 → Commission 10 EGP
- Amount 1499 → Commission 10 EGP
- Amount 1500 → Commission 15 EGP

#### 4. Client Auto-fill Test
1. Type partial mobile number (e.g., "011")
2. Select from suggestions dropdown
3. Verify auto-fill of name, code, gender

#### 5. Validation Tests
- Try submitting empty form → Should show required field errors
- Enter amount not multiple of 5 → Should show validation error
- Add discount without notes → Should require discount notes
- Select line with insufficient safe balance → Should show warning

### Expected Behavior

#### Balance Changes
- **Line Balance**: Increases by full amount
- **Safe Balance**: Decreases by (amount - commission)
- **Commission**: Logged as agent earnings

#### UI Features
- Real-time commission calculation
- Client search and auto-complete
- Safe balance validation
- Transaction summary display
- Success/error messages

#### Business Logic
- Creates new customers automatically
- Updates existing customer info
- Validates safe has sufficient balance
- Logs complete transaction record
- Redirects to transactions index on success

### Troubleshooting

If you encounter issues:

1. **Page not loading**: Check if route is registered and component exists
2. **Database errors**: Ensure migrations are run and tables exist
3. **Authentication**: Make sure you're logged in as an agent
4. **Balance validation**: Check if safe has sufficient balance
5. **Commission calculation**: Verify amount is > 0 and multiple of 5

## ✅ Issue Fixed: Database Constraint

**Previous Error**: `CONSTRAINT chk_transaction_type failed`

**Resolution**: Updated database constraint to include 'Receive' transaction type and enhanced CreateTransaction use case.

**Status**: ✅ All Receive transactions now work correctly

---

### Testing the Full Flow

1. **Open** `/transactions/receive`
2. **Enter** client mobile number (try existing or new)
3. **Fill** required fields
4. **Enter** amount and watch commission calculate
5. **Select** available line
6. **Review** transaction summary
7. **Submit** and verify success message
8. **Check** transaction was created in database
9. **Verify** line and safe balances updated correctly

The implementation should handle all edge cases gracefully and provide clear feedback at each step.
