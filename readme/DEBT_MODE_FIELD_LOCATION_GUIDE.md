# How to Find and Use Debt Mode Fields

## 🎯 **Step-by-Step Instructions**

### **For Customer Creation:**

1. **Login Requirements:**
   - Log in as **Admin** or **General Supervisor** 
   - Other roles (Agent, Trainee, etc.) will NOT see these fields

2. **Navigate to Create Customer:**
   - Go to Customers menu → "إنشاء عميل جديد" (Create New Customer)
   - URL: `/customers/create`

3. **Fill Basic Information First:**
   - Name (الاسم)
   - Mobile Number (رقم الهاتف) 
   - Customer Code (كود العميل) - optional
   - Gender (الجنس)

4. **Find the Debt Settings Section:**
   - Scroll down in the form
   - Look for a **light orange/amber colored box** with the title **"إعدادات الدين"** (Debt Settings)
   - This section should be visible regardless of wallet status

5. **Configure Debt Mode:**
   - **Step 1**: Check the box **"السماح بالدين"** (Allow Debt)
   - **Step 2**: Once checked, the **"الحد الأقصى للدين"** (Max Debt Limit) field becomes enabled
   - **Step 3**: Enter a **negative number** (e.g. -5000) for the maximum debt allowed

### **For Customer Editing:**

1. **Navigate to Edit Customer:**
   - Go to Customers list → Click "تعديل" (Edit) on any customer
   - URL: `/customers/{id}/edit`

2. **Find the Debt Settings Section:**
   - Scroll down in the edit form
   - Look for the **amber/orange colored box** with **"إعدادات الدين"** (Debt Settings)

3. **Configure Debt Mode:**
   - Same as creation: Check "السماح بالدين" first, then set the debt limit

## 🔍 **Visual Guide**

### What to Look For:

```
┌─────────────────────────────────────────────┐
│  📋 إعدادات الدين (Debt Settings)           │
│  ┌─────────────────────────────────────────┐ │
│  │ ☑️ السماح بالدين (Allow Debt)           │ │
│  │    (يسمح للعميل بإرسال أموال...)      │ │
│  │                                         │ │
│  │ الحد الأقصى للدين (Max Debt Limit):    │ │
│  │ [    -5000    ] EGP                     │ │
│  │ مثال: -5000 يعني أن العميل...           │ │
│  └─────────────────────────────────────────┘ │
└─────────────────────────────────────────────┘
```

### Section Characteristics:
- **Background Color**: Light amber/orange (different from other sections)
- **Border**: Amber border
- **Position**: After basic customer info, before branch selection
- **Visibility**: Only for Admin and General Supervisor

## 🚨 **Troubleshooting**

### If you still can't see the debt fields:

#### 1. **Check Your User Role**
```php
// Run this in tinker to check your role:
php artisan tinker
>>> auth()->user()->roles->pluck('name')
// Should show: ["admin"] or ["general_supervisor"]
```

#### 2. **Clear All Caches**
Run these commands in terminal:
```bash
php artisan view:clear
php artisan config:clear  
php artisan cache:clear
```

#### 3. **Hard Refresh Browser**
- Press **Ctrl + F5** to hard refresh
- Or clear browser cache completely

#### 4. **Check Form Position**
The debt section appears in this order:
1. Basic customer info (name, mobile, etc.)
2. Wallet activation section  
3. **→ DEBT SETTINGS SECTION ← (Look here!)**
4. Branch selection
5. Submit buttons

## 💡 **Important Notes**

### Debt Limit Field Behavior:
- **Always visible** to Admin/Supervisor (no longer hidden)
- **Disabled state** when "Allow Debt" is unchecked
- **Enabled state** when "Allow Debt" is checked
- **Required validation** only when debt is allowed

### Required Values:
- **Allow Debt**: Checkbox (true/false)
- **Max Debt Limit**: Negative number (e.g., -1000, -5000, -10000)

### Example Configuration:
- ✅ Allow Debt: **Checked**
- ✅ Max Debt Limit: **-5000**
- **Result**: Customer can have up to 5000 EGP debt (balance can go down to -5000)

The fields are now **always visible** when you have the right permissions, making them much easier to find and configure!
