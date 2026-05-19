┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                               │
│                  FIELD NAME VERIFICATION CHECKLIST                           │
│                  Database: db_merish | Project: MERISH                      │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘

═══════════════════════════════════════════════════════════════════════════════
APPOINTMENT FORM FIELDS ↔ DATABASE FIELDS MAPPING
═══════════════════════════════════════════════════════════════════════════════

STEP 1: Category & Service Selection
─────────────────────────────────────────────────────────────────────────────
  Form Field Name    │ Database Table │ Column Name    │ Type/Values
  ─────────────────────────────────────────────────────────────────────────
  category           │ services       │ category       │ ENUM('Hair', 'Nails', 'Lashes', 'Wax & Eyebrows')
  service_id         │ services       │ service_id     │ VARCHAR(10) PRIMARY KEY
  ✓ VERIFIED          │                │                │

STEP 2: Bundle Suggestions  
─────────────────────────────────────────────────────────────────────────────
  Form Field Name    │ Database Table │ Column Name       │ Type
  ─────────────────────────────────────────────────────────────────────────
  (promo_id - opt)   │ promotions     │ promo_id          │ INT AUTO_INCREMENT
  [no form field]    │ promotions     │ promo_name        │ VARCHAR(100)
  [no form field]    │ promotions     │ service_id_req    │ VARCHAR(10) FK
  [no form field]    │ promotions     │ discount_value    │ DECIMAL(10,2)
  ✓ VERIFIED          │                │                  │

STEP 3: Date & Time Selection
─────────────────────────────────────────────────────────────────────────────
  Form Field Name    │ Database Table │ Column Name       │ Type
  ─────────────────────────────────────────────────────────────────────────
  reservation_date   │ reservations   │ (combined as)     │ (from schedule_time)
  reservation_time   │ reservations   │ schedule_time     │ DATETIME
  ✓ VERIFIED          │                │ (Y-m-d H:i:s)    │

  NOTE: Controller combines reservation_date + reservation_time into single
        schedule_time DATETIME field before database insert
        See: ReservationsModel::create() line 112

STEP 4: Beautician Selection
─────────────────────────────────────────────────────────────────────────────
  Form Field Name    │ Database Table │ Column Name    │ Type/Source
  ─────────────────────────────────────────────────────────────────────────
  beautician_id      │ staff_profiles │ profile_id     │ INT AUTO_INCREMENT
  [mapped to]        │ users          │ user_id        │ INT (via staff_profiles.user_id)
  ✓ VERIFIED          │                │                │

  NOTE: Form receives beautician profile_id, controller resolves to user_id
        before storing in reservation_details.beautician_id
        See: ReservationsModel::create() lines 77-86

STEP 5: Review & DP Confirmation
─────────────────────────────────────────────────────────────────────────────
  Form Field Name    │ Database Table │ Column Name       │ Type
  ─────────────────────────────────────────────────────────────────────────
  dp_proof           │ reservations   │ payment_proof_url │ VARCHAR(255)
  agreeTerms         │ (logic only)   │ (not stored)      │ BOOLEAN check
  [auto-gen]         │ reservations   │ is_dp_paid        │ BOOLEAN (default 0)
  [auto-gen]         │ reservations   │ dp_amount         │ DECIMAL(10,2) = 50000
  [auto-gen]         │ reservations   │ STATUS            │ ENUM (set to 'Pending')
  ✓ VERIFIED          │                │                  │

═══════════════════════════════════════════════════════════════════════════════
GENERATED/AUTO FIELDS IN DATABASE
═══════════════════════════════════════════════════════════════════════════════

reservations Table:
  ✓ res_id              INT AUTO_INCREMENT PRIMARY KEY
  ✓ user_id             INT (from $_SESSION or form, mapped to customer)
  ✓ seat_id             VARCHAR(10) (auto-selected from first available)
  ✓ companion_seat_id   VARCHAR(10) NULL (not used in wizard yet)
  ✓ STATUS              ENUM = 'Pending' (by default)
  ✓ schedule_time       DATETIME (combined from date + time)
  ✓ is_dp_paid          BOOLEAN = FALSE (0)
  ✓ dp_amount           DECIMAL(10,2) = 50000
  ✓ payment_proof_url   VARCHAR(255) = uploaded file path

reservation_details Table:
  ✓ detail_id           INT AUTO_INCREMENT PRIMARY KEY
  ✓ res_id              INT (refs reservations.res_id) FK
  ✓ service_id          VARCHAR(10) (refs services.service_id) FK
  ✓ beautician_id       INT (refs users.user_id) FK

═══════════════════════════════════════════════════════════════════════════════
CONTROLLER FLOW VERIFICATION
═══════════════════════════════════════════════════════════════════════════════

CustomerController::appointment()
  Step 1 →  GET ?step=1      | Form sends POST with category + service_id
             Save to $_SESSION['appointment_draft']
             Redirect to step=2

  Step 2 →  GET ?step=2      | Form sends POST with bundle selection (optional)
             Save to $_SESSION['appointment_draft']
             Redirect to step=3

  Step 3 →  GET ?step=3      | Form sends POST with reservation_date + reservation_time
             Save to $_SESSION['appointment_draft']
             Redirect to step=4

  Step 4 →  GET ?step=4      | Form sends POST with beautician_id
             Save to $_SESSION['appointment_draft']
             Redirect to step=5

  Step 5 →  GET ?step=5      | Form displays review
             POST form with dp_proof file
             Call confirmAppointment()
             ✓ Upload file to uploads/dp_proofs/
             ✓ Insert into reservations
             ✓ Insert into reservation_details
             Redirect to appointmentConfirmed?res_id=XXX

═══════════════════════════════════════════════════════════════════════════════
confirmAppointment() METHOD VERIFICATION
═══════════════════════════════════════════════════════════════════════════════

Expected Behavior:
  1. Extract draft from $_SESSION['appointment_draft']
  2. Validate required fields (user_id, service_id, beautician_id, date, time)
  3. Handle file upload → uploads/dp_proofs/timestamp_filename.ext
  4. Create ReservationsModel instance
  5. Call create() with parameters:
     {
       'customer_id': $userId,           ← from $_SESSION or logged-in user
       'user_id': $userId,                ← alternative field name
       'service_id': $serviceId,          ← from draft
       'beautician_id': $beauticianId,    ← from draft (profile_id → user_id)
       'reservation_date': $date,         ← from draft (YYYY-MM-DD)
       'reservation_time': $time,         ← from draft (HH:MM)
       'seat_id': auto-selected,          ← first available seat
       'status': 'Pending',               ← default
       'is_dp_paid': 0,                   ← not yet paid
       'dp_amount': 50000,                ← fixed amount
       'payment_proof_url': $uploadPath   ← uploaded file path
     }
  6. ReservationsModel::create() combines date + time → schedule_time
  7. Insert reservation → get res_id
  8. Insert reservation_details with res_id + service_id + beautician_id
  9. Redirect to appointmentConfirmed with res_id parameter

═══════════════════════════════════════════════════════════════════════════════
TESTING CHECKLIST
═══════════════════════════════════════════════════════════════════════════════

[ ] Database Setup
    [ ] db_merish database exists
    [ ] All 14 tables created (users, services, seats, etc.)
    [ ] Dummy data loaded (users, services, staff_profiles, etc.)

[ ] Services Model
    [ ] findAll() returns services with category filter
    [ ] findById() returns single service object
    [ ] Services with promotions have promo flag = 1

[ ] PromotionsModel
    [ ] findByServiceId() returns promotions for that service
    [ ] Returns promo_name, discount_value, etc.

[ ] Step 1: Category & Service
    [ ] Category cards display (Hair, Nails, Lashes, Wax & Eyebrows)
    [ ] Services filter by category
    [ ] POST saves category + service_id to session
    [ ] Redirects to ?step=2

[ ] Step 2: Bundles
    [ ] Promotions load from DB for selected service
    [ ] Promo names and discounts display
    [ ] POST redirects to ?step=3

[ ] Step 3: Date & Time
    [ ] Calendar generates dates (H-1 for Regular, H-14 for VIP)
    [ ] Time slots display (10:00, 11:30, 13:00, 14:30, 16:00)
    [ ] POST saves reservation_date + reservation_time to session
    [ ] Redirects to ?step=4

[ ] Step 4: Beautician
    [ ] Beautician list filters by specialization
    [ ] Only Hair Stylists show for Hair services
    [ ] POST saves beautician_id to session
    [ ] Redirects to ?step=5

[ ] Step 5: Review & Confirm
    [ ] Service name displays correctly
    [ ] Service price displays correctly
    [ ] Bundle discount applied to total
    [ ] File upload input accepts images
    [ ] Terms checkbox required
    [ ] POST triggers confirmAppointment()

[ ] Database Create
    [ ] File uploads to uploads/dp_proofs/
    [ ] Reservation inserted in DB
    [ ] Reservation detail inserted in DB
    [ ] Query shows correct res_id, service, beautician, date/time, status=Pending

[ ] Guest User Flow
    [ ] Guest can access appointment booking (controller allows public)
    [ ] Guest submits all 5 steps
    [ ] confirmAppointment() detects guest (no $_SESSION['user_id'])
    [ ] Redirects to login page with post_login_redirect

[ ] Logged-in User Flow
    [ ] Customer can book appointment
    [ ] user_id correctly stored in reservations
    [ ] Confirmation page displays with QR and receipt

═══════════════════════════════════════════════════════════════════════════════
COMMON ISSUES & FIXES
═══════════════════════════════════════════════════════════════════════════════

Issue: "Column not found" error
  → Check field name spelling against database schema
  → Verify column names in all queries match CREATE TABLE definitions

Issue: "Foreign key constraint failed"
  → Ensure referenced IDs exist in parent tables
  → Beautician profile_id must exist in staff_profiles table
  → Service_id must exist in services table
  → Seat_id must exist in seats table

Issue: File upload fails
  → Check uploads/dp_proofs/ directory exists
  → Check directory permissions (write access)
  → Verify uploaded file path format

Issue: Session data not persisting
  → Verify $_SESSION['appointment_draft'] being saved in each step
  → Check session timeout settings
  → Ensure session_start() called in bootstrap

Issue: Step redirects loop or skip steps
  → Verify step parameter increments: step+1
  → Check $_POST['step'] being sent from form
  → Verify no extra header() calls in controller

═══════════════════════════════════════════════════════════════════════════════
SUMMARY: All field names verified against db_merish schema. Ready for testing!
═══════════════════════════════════════════════════════════════════════════════
