# How to Access Debt Mode Fields

## Issue Resolution
The debt mode fields were only showing when the wallet was activated (`is_client = true`). This has been fixed to show the debt mode section for Admin and General Supervisor users regardless of wallet status.

## Access Instructions

### 1. User Role Requirements
To see and configure debt mode fields, you must be logged in as:
- **Admin** user
- **General Supervisor** user

Other roles (Agent, Trainee, Auditor, Branch Manager) will NOT see these fields.

### 2. Customer Creation Form
**Location**: `/customers/create`

**Steps to access debt mode fields:**
1. Log in as Admin or General Supervisor
2. Navigate to "Create Customer" page
3. Scroll down to find the **"وضع الدين" (Debt Mode)** section
4. The section will appear as an amber/orange highlighted box
5. Check "السماح بالدين" (Allow Debt) checkbox
6. Enter the maximum debt limit in the "الحد الأقصى للدين" field (must be negative, e.g., -5000)

### 3. Customer Edit Form  
**Location**: `/customers/{id}/edit`

**Steps to access debt mode fields:**
1. Log in as Admin or General Supervisor
2. Navigate to customer list and click "Edit" on any customer
3. Scroll down to find the **"وضع الدين" (Debt Mode)** section
4. The section will appear as an amber/orange highlighted box
5. Check "السماح بالدين" (Allow Debt) checkbox
6. Enter the maximum debt limit in the "الحد الأقصى للدين" field (must be negative)

## Visual Indicators

### Debt Mode Section Appearance
- **Background**: Light amber/orange color
- **Border**: Amber border
- **Checkbox**: Orange-colored when checked
- **Position**: Between wallet activation and branch selection

### Field Details
1. **Allow Debt Checkbox**: 
   - Label: "السماح بالدين"
   - Description: "(يسمح للعميل بإرسال أموال حتى لو كان الرصيد غير كافي)"

2. **Max Debt Limit Field** (appears when checkbox is checked):
   - Label: "الحد الأقصى للدين (رقم سالب)"
   - Type: Number input
   - Constraint: Must be zero or negative
   - Placeholder: "-5000"
   - Example help text: "مثال: -5000 يعني أن العميل يمكنه أن يصل دينه إلى 5000 جنيه"

## Troubleshooting

### If you can't see the debt mode fields:

1. **Check your user role:**
   - Go to user profile or ask admin to confirm your role
   - Only Admin and General Supervisor can see these fields

2. **Clear browser cache:**
   - Press Ctrl+F5 to hard refresh
   - Clear browser cache and cookies

3. **Check form position:**
   - The debt mode section appears after the wallet activation section
   - Scroll down to find it - it's not at the very top of the form

4. **Verify authorization:**
   - Make sure you're logged in with the correct account
   - Log out and log back in if necessary

## Form Validation

### Rules Applied:
- **allow_debt**: boolean (checkbox)
- **max_debt_limit**: 
  - Required when allow_debt is true
  - Must be numeric
  - Must be zero or negative (max="0")
  - Can have decimal places (step="0.01")

### Error Messages:
- If debt is allowed but no limit set: "The max debt limit field is required when allow debt is true."
- If limit is positive: "The max debt limit must not be greater than 0."

## Database Impact
When saved, these fields update the customers table:
- `allow_debt`: boolean column
- `max_debt_limit`: decimal column (nullable)
