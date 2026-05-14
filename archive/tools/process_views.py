import os
import re

directory = r"C:\Users\melin\OneDrive\Documents\APLIN_PROJECT\PROJECT-APLIN\app\Views"
skipped_files = [
    os.path.join(directory, "Auth", "login.php"),
    os.path.join(directory, "Auth", "register.php")
]

replacements = [
    (r"url\('auth/login'\)", "index.php?page=login&action=login"),
    (r"url\('auth/register'\)", "index.php?page=register&action=register"),
    (r"url\('auth/logout'\)", "index.php?page=login&action=logout"),
    (r"url\('admin'\)", "index.php?page=admin"),
    (r"url\('customer'\)", "index.php?page=customer"),
    (r"url\('customer/appointment'\)", "index.php?page=customer&action=appointment"),
    (r"url\('customer/book-appointment'\)", "index.php?page=customer&action=bookAppointment"),
    (r"url\('customer/order-menu'\)", "index.php?page=customer&action=orderMenu"),
    (r"url\('customer/create-order'\)", "index.php?page=customer&action=createOrder"),
    (r"url\('barista'\)", "index.php?page=barista"),
    (r"url\('barista/update-order'\)", "index.php?page=barista&action=updateOrderStatus"),
    (r"url\('barista/history'\)", "index.php?page=barista&action=orderHistory"),
    (r"url\('beautician'\)", "index.php?page=beautician"),
    (r"url\('beautician/today'\)", "index.php?page=beautician&action=todaySchedule"),
    (r"url\('beautician/upcoming'\)", "index.php?page=beautician&action=upcomingSchedule"),
    (r"url\('beautician/update-status'\)", "index.php?page=beautician&action=updateReservationStatus"),
    (r"url\('receptionist'\)", "index.php?page=receptionist"),
    (r"url\('receptionist/schedule'\)", "index.php?page=receptionist&action=scheduleBooking"),
    (r"url\('receptionist/check-in'\)", "index.php?page=receptionist&action=checkIn"),
    (r"url\('receptionist/reservations'\)", "index.php?page=receptionist&action=viewReservations"),
    (r"url\('receptionist/orders'\)", "index.php?page=receptionist&action=viewOrders"),
    (r"url\(''\)", "index.php"),
]

# Replacement for form_url
form_url_replacements = []
for pattern, replacement in replacements:
    form_url_replacements.append((pattern.replace("url(", "form_url("), replacement))

all_replacements = replacements + form_url_replacements

# Add logic for stripping <?php echo url(...) ?> or <?php echo form_url(...) ?>
# This is tricky because it might be nested or on one line.
# Pattern: <?php echo (url|form_url)\('(.*?)'\);? ?>  -> \2 (but with our mapped replacement)

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content
    total_changes = 0

    # Handle the helper function replacements first
    # is_authenticated() -> isset($_SESSION['user_id'])
    content, count = re.subn(r"is_authenticated\(\)", "isset($_SESSION['user_id'])", content)
    total_changes += count

    # current_user() -> $_SESSION['user'] (assuming session array access)
    content, count = re.subn(r"current_user\(\)", "$_SESSION['user']", content)
    total_changes += count

    # user_role() -> $_SESSION['role']
    content, count = re.subn(r"user_role\(\)", "$_SESSION['role']", content)
    total_changes += count

    # has_role(...) -> $_SESSION['role'] == ...
    # Simple version: has_role('admin') -> $_SESSION['role'] == 'admin'
    content, count = re.subn(r"has_role\((.*?)\)", r"$_SESSION['role'] == \1", content)
    total_changes += count
    
    # get_dashboard_route() -> role based logic
    # Replace with a ternary or a variable. Let's use a logic string.
    dashboard_logic = "(isset($_SESSION['role']) ? 'index.php?page=' . $_SESSION['role'] : 'index.php?page=login')"
    content, count = re.subn(r"get_dashboard_route\(\)", dashboard_logic, content)
    total_changes += count

    # Handle url/form_url replacements
    for pattern, replacement in all_replacements:
        # First handle the <?php echo url(...) ?> case
        # Try both with and without semicolon
        php_echo_pattern = r"<\?php\s+echo\s+" + pattern + r";?\s+\?>"
        content, count = re.subn(php_echo_pattern, replacement, content)
        total_changes += count

        # Then handle the bare url(...) calls (likely inside other PHP tags or strings)
        content, count = re.subn(pattern, replacement, content)
        total_changes += count

    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        return total_changes
    return 0

modified_files = []

for root, dirs, files in os.walk(directory):
    for file in files:
        if file.endswith(".php"):
            filepath = os.path.join(root, file)
            # Check if file is in skip list (normalize paths)
            is_skipped = False
            for skip in skipped_files:
                if os.path.abspath(filepath) == os.path.abspath(skip):
                    is_skipped = True
                    break
            
            if is_skipped:
                continue

            changes = process_file(filepath)
            if changes > 0:
                modified_files.append((filepath, changes))

if modified_files:
    print(f"{'File Path':<80} | {'Replacements':<12}")
    print("-" * 95)
    for path, count in modified_files:
        print(f"{path:<80} | {count:<12}")
else:
    print("No files modified.")
