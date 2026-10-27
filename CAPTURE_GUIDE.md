# Browser Screenshot Guide (ERP Documentation)

## When browser doesn't respond to computer_use

### Problem
- `computer_use(action="capture")` returns empty or background view
- Browser is not visible/focused at all

### Solutions

#### Option 1: Verify Server is Running
```bash
curl -s http://127.0.0.1:8000/
# Look for HTML response, not connection refused
```

#### Option 2: Check URL Variations
Common Laragon URLs:
- `http://127.0.0.1:8000/`
- `http://localhost:8000/`
- `http://localhost/` (port 80)
- Laragon IP: `http://192.168.x.x:8000/`

#### Option 3: Use Code Analysis Instead
When screenshots unavailable, read Blade templates directly:
```bash
read_file("resources/views/login.blade.php")
read_file("resources/views/register.blade.php")
read_file("resources/views/index.blade.php")
```

## Screenshot Workflow (When Browser Available)

### Standard Flow
1. Open browser manually to target URL
2. `computer_use(action="capture", app="Chrome")` - capture browser window
3. Read numbered elements from capture response
4. Click elements by index for navigation
5. Document UI elements found

### Element Types to Look For

```
#1  AXTextField 'Email' - login email field
#2  AXTextField 'Password' - login password field  
#3  AXButton 'Masuk' - login submit button
#4  AXLink 'Buat Akun Baru' - register link
#5  AXButton 'Ingat saya' - remember me checkbox
```

### Common Form Fields in ERP Systems

| Field Type | HTML Input Type | Blade Template |
|------------|-----------------|----------------|
| Email | type="email" | name="email" |
| Password | type="password" | name="password" |
| Nama | type="text" | name="name" |
| Username | type="text" | name="username" |
| No Telepon | type="number" | name="no_telp" |
| Foto | type="file" | name="foto", accept="image/*" |

## Fallback: Documentation from Code

When UI capture fails, use this template:

```
## Authentication Flow

### Login Page (`login.blade.php`)
- Location: `resources/views/login.blade.php`
- Form action: `/login`
- Method: POST
- Fields:
  - Email (type="email", name="email", placeholder="nama@email.com")
  - Password (type="password", name="password", placeholder="••••••••")
  - Remember me (type="checkbox", name="remember")
- Features:
  - Eye toggle for password visibility (JavaScript function `togglePassword()`)
  - SweetAlert2 toast notifications for errors/success
```