<?php
require_once __DIR__ . '/database.php';

$db = (new Database())->getConnection();

$db->exec("
CREATE TABLE IF NOT EXISTS users (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'parent',
    status TEXT NOT NULL DEFAULT 'active',
    created_at TEXT DEFAULT (datetime('now'))
);
CREATE TABLE IF NOT EXISTS parents (
    parent_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    full_name TEXT NOT NULL,
    phone TEXT,
    address TEXT,
    created_at TEXT DEFAULT (datetime('now')),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
CREATE TABLE IF NOT EXISTS hospitals (
    hospital_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    hospital_name TEXT NOT NULL,
    address TEXT,
    city TEXT,
    phone TEXT,
    email TEXT,
    pincode TEXT,
    registration_number TEXT,
    status TEXT DEFAULT 'active',
    created_at TEXT DEFAULT (datetime('now')),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
CREATE TABLE IF NOT EXISTS vaccines (
    vaccine_id INTEGER PRIMARY KEY AUTOINCREMENT,
    vaccine_name TEXT NOT NULL,
    description TEXT,
    doses_required INTEGER DEFAULT 1,
    age_group TEXT,
    status TEXT DEFAULT 'active',
    created_at TEXT DEFAULT (datetime('now'))
);
CREATE TABLE IF NOT EXISTS children (
    child_id INTEGER PRIMARY KEY AUTOINCREMENT,
    parent_id INTEGER NOT NULL,
    unique_child_id TEXT UNIQUE,
    full_name TEXT NOT NULL,
    date_of_birth TEXT NOT NULL,
    gender TEXT,
    birth_weight TEXT,
    blood_group TEXT,
    allergies TEXT,
    medical_conditions TEXT,
    created_at TEXT DEFAULT (datetime('now')),
    FOREIGN KEY (parent_id) REFERENCES parents(parent_id)
);
CREATE TABLE IF NOT EXISTS appointments (
    appointment_id INTEGER PRIMARY KEY AUTOINCREMENT,
    child_id INTEGER NOT NULL,
    hospital_id INTEGER NOT NULL,
    vaccine_id INTEGER NOT NULL,
    appointment_date TEXT NOT NULL,
    status TEXT DEFAULT 'pending',
    created_at TEXT DEFAULT (datetime('now')),
    FOREIGN KEY (child_id) REFERENCES children(child_id),
    FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id),
    FOREIGN KEY (vaccine_id) REFERENCES vaccines(vaccine_id)
);
CREATE TABLE IF NOT EXISTS vaccination_records (
    record_id INTEGER PRIMARY KEY AUTOINCREMENT,
    child_id INTEGER NOT NULL,
    vaccine_id INTEGER NOT NULL,
    hospital_id INTEGER NOT NULL,
    administration_date TEXT NOT NULL,
    created_at TEXT DEFAULT (datetime('now')),
    FOREIGN KEY (child_id) REFERENCES children(child_id),
    FOREIGN KEY (vaccine_id) REFERENCES vaccines(vaccine_id),
    FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id)
);
CREATE TABLE IF NOT EXISTS contact_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    subject TEXT,
    message TEXT NOT NULL,
    status TEXT DEFAULT 'pending',
    created_at TEXT DEFAULT (datetime('now'))
);
CREATE TABLE IF NOT EXISTS password_resets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    token TEXT NOT NULL,
    expires_at TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
CREATE TABLE IF NOT EXISTS system_logs (
    log_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    action TEXT,
    ip_address TEXT,
    created_at TEXT DEFAULT (datetime('now'))
);
");

// ---- ADMIN ----
if ($db->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn() == 0) {
    $pw = password_hash('admin123', PASSWORD_DEFAULT);
    $db->exec("INSERT INTO users (email,password,role,status) VALUES ('admin@vaxora.pk','$pw','admin','active')");
}

// ---- KARACHI HOSPITALS ----
if ($db->query("SELECT COUNT(*) FROM hospitals")->fetchColumn() == 0) {
    $hospitals = [
        ["Civil Hospital Karachi",           "Liaquatabad Road, Karachi",               "Karachi", "021-99215740", "info@civilhospital.pk"],
        ["Aga Khan University Hospital",     "Stadium Road, Karachi",                   "Karachi", "021-34861000", "info@aku.edu.pk"],
        ["Jinnah Postgraduate Medical Centre","Rafiqui Shaheed Road, Karachi",          "Karachi", "021-99201300", "info@jpmc.pk"],
        ["Indus Hospital Karachi",           "Korangi Industrial Area, Karachi",         "Karachi", "021-35112709", "info@indushospital.org.pk"],
        ["South City Hospital",              "Block 2 Clifton, Karachi",                 "Karachi", "021-35374000", "info@southcity.pk"],
        ["Liaquat National Hospital",        "Stadium Road, Karachi",                    "Karachi", "021-34412000", "info@lnh.com.pk"],
        ["Ziauddin Hospital",                "North Nazimabad, Karachi",                 "Karachi", "021-36648000", "info@ziauddin.edu.pk"],
        ["Patel Hospital",                   "Gulshan-e-Iqbal, Karachi",                "Karachi", "021-34829001", "info@patelhospital.com.pk"],
        ["National Medical Centre",          "Federal B Area, Karachi",                  "Karachi", "021-36952601", "info@nmc.pk"],
        ["Dow University Hospital",          "Baba-e-Urdu Road, Karachi",               "Karachi", "021-99215740", "info@duhs.edu.pk"],
    ];
    foreach ($hospitals as $h) {
        $pw = password_hash('hospital123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (email,password,role,status) VALUES (?,?,'hospital','active')");
        $stmt->execute([$h[4], $pw]);
        $uid = $db->lastInsertId();
        $db->prepare("INSERT INTO hospitals (user_id,hospital_name,address,city,phone,email,status) VALUES (?,?,?,?,?,?,'active')")
           ->execute([$uid, $h[0], $h[1], $h[2], $h[3], $h[4]]);
    }
}

// ---- VACCINES ----
if ($db->query("SELECT COUNT(*) FROM vaccines")->fetchColumn() == 0) {
    $vaccines = [
        ["BCG",              "Bacillus Calmette-Guérin vaccine — protects against tuberculosis.", 1, "At Birth"],
        ["Polio (OPV)",      "Oral Polio Vaccine — prevents poliomyelitis (paralysis).", 4, "0–5 years"],
        ["Hepatitis B",      "Protects against hepatitis B virus infection and liver disease.", 3, "At Birth – 6 months"],
        ["DPT",              "Combined vaccine for Diphtheria, Pertussis & Tetanus.", 3, "6 weeks – 18 months"],
        ["Hib",              "Haemophilus influenzae type b — prevents meningitis.", 3, "6 weeks – 15 months"],
        ["Pneumococcal (PCV)","Prevents pneumococcal pneumonia, meningitis and blood infections.", 3, "6 weeks – 12 months"],
        ["Rotavirus",        "Prevents severe diarrhoea caused by rotavirus infection.", 2, "6–24 weeks"],
        ["Measles",          "Single-antigen measles vaccine administered at 9 months.", 1, "9 months"],
        ["MMR",              "Combined Measles, Mumps and Rubella vaccine.", 2, "12–15 months"],
        ["Typhoid (TCV)",    "Typhoid Conjugate Vaccine — newer, longer-lasting protection.", 1, "9 months+"],
        ["Varicella",        "Chickenpox vaccine — prevents varicella infection.", 2, "12 months – 12 years"],
        ["Influenza",        "Seasonal flu vaccine — recommended annually.", 1, "6 months+"],
    ];
    foreach ($vaccines as $v) {
        $db->prepare("INSERT INTO vaccines (vaccine_name,description,doses_required,age_group,status) VALUES (?,?,?,?,'active')")->execute($v);
    }
}

// ---- DEMO PARENT ----
if ($db->query("SELECT COUNT(*) FROM users WHERE role='parent'")->fetchColumn() == 0) {
    $pw = password_hash('parent123', PASSWORD_DEFAULT);
    $db->exec("INSERT INTO users (email,password,role,status) VALUES ('parent@demo.com','$pw','parent','active')");
    $uid = $db->lastInsertId();
    $db->exec("INSERT INTO parents (user_id,full_name,phone,address) VALUES ($uid,'Ahmed Ali Khan','0300-1234567','Gulshan-e-Iqbal, Karachi')");
}

// ---- FAKE PARENTS & CHILDREN ----
if ($db->query("SELECT COUNT(*) FROM children")->fetchColumn() == 0) {
    $fakeParents = [
        ["Fatima Noor",      "0312-9876543", "DHA Phase 5, Karachi",       "fatima.noor@gmail.com"],
        ["Muhammad Usman",   "0333-1122334", "North Nazimabad, Karachi",   "usman.khan@gmail.com"],
        ["Sana Malik",       "0345-5566778", "Clifton Block 4, Karachi",   "sana.malik@gmail.com"],
        ["Zubair Ahmed",     "0321-9988776", "Gulshan Block 13, Karachi",  "zubair.ahmed@gmail.com"],
        ["Ayesha Farooq",    "0313-7788990", "Saddar, Karachi",            "ayesha.farooq@gmail.com"],
        ["Tariq Mehmood",    "0300-4455667", "PECHS, Karachi",             "tariq.m@gmail.com"],
        ["Rukhsana Begum",   "0335-3344556", "Federal B Area, Karachi",    "rukhsana.b@gmail.com"],
    ];
    $fakeChildren = [
        ["Zara Ali Khan",        "2022-03-15", "female", "3.1",  "B+",   1],
        ["Omar Noor",            "2021-07-22", "male",   "3.4",  "A+",   2],
        ["Haya Usman",           "2020-11-05", "female", "2.9",  "O+",   3],
        ["Ibrahim Usman",        "2023-01-18", "male",   "3.6",  "B-",   3],
        ["Maryam Malik",         "2019-06-30", "female", "3.2",  "AB+",  4],
        ["Yusuf Zubair",         "2022-09-14", "male",   "3.0",  "A-",   5],
        ["Aisha Farooq",         "2021-04-25", "female", "3.5",  "O-",   6],
        ["Hassan Tariq",         "2020-08-08", "male",   "2.8",  "B+",   7],
        ["Khadija Rukhsana",     "2023-05-12", "female", "3.3",  "A+",   8],
        ["Abdullah Ahmed",       "2018-12-01", "male",   "3.7",  "O+",   1],
    ];

    $pw = password_hash('parent123', PASSWORD_DEFAULT);
    $parentIds = [];
    // Get demo parent id first
    $demoParent = $db->query("SELECT parent_id FROM parents LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $parentIds[1] = $demoParent['parent_id'] ?? 1;

    foreach ($fakeParents as $i => $fp) {
        $db->prepare("INSERT INTO users (email,password,role,status) VALUES (?,?,'parent','active')")->execute([$fp[3], $pw]);
        $uid = $db->lastInsertId();
        $db->prepare("INSERT INTO parents (user_id,full_name,phone,address) VALUES (?,?,?,?)")->execute([$uid,$fp[0],$fp[1],$fp[2]]);
        $parentIds[$i+2] = $db->lastInsertId();
    }

    foreach ($fakeChildren as $c) {
        $pid = $parentIds[$c[5]] ?? $parentIds[1];
        $uid_code = 'VAX-' . strtoupper(substr(uniqid(), -6));
        $db->prepare("INSERT INTO children (parent_id,unique_child_id,full_name,date_of_birth,gender,birth_weight,blood_group) VALUES (?,?,?,?,?,?,?)")
           ->execute([$pid,$uid_code,$c[0],$c[1],$c[2],$c[3],$c[4]]);
    }

    // ---- FAKE APPOINTMENTS & VACCINATION RECORDS ----
    $children = $db->query("SELECT child_id FROM children")->fetchAll(PDO::FETCH_COLUMN);
    $hospitals = $db->query("SELECT hospital_id FROM hospitals LIMIT 10")->fetchAll(PDO::FETCH_COLUMN);
    $vaccines  = $db->query("SELECT vaccine_id FROM vaccines")->fetchAll(PDO::FETCH_COLUMN);

    // Completed vaccinations (historical)
    $completedData = [
        [$children[0]??1, $hospitals[0]??1, $vaccines[0]??1, "2022-03-20"],
        [$children[0]??1, $hospitals[0]??1, $vaccines[3]??4, "2022-05-15"],
        [$children[1]??2, $hospitals[1]??2, $vaccines[0]??1, "2021-07-25"],
        [$children[1]??2, $hospitals[1]??2, $vaccines[1]??2, "2022-01-10"],
        [$children[2]??3, $hospitals[2]??3, $vaccines[0]??1, "2020-11-10"],
        [$children[2]??3, $hospitals[3]??4, $vaccines[2]??3, "2021-02-20"],
        [$children[3]??4, $hospitals[4]??5, $vaccines[0]??1, "2023-01-22"],
        [$children[4]??5, $hospitals[5]??6, $vaccines[4]??5, "2019-07-05"],
        [$children[4]??5, $hospitals[5]??6, $vaccines[8]??9, "2020-07-01"],
        [$children[5]??6, $hospitals[6]??7, $vaccines[0]??1, "2022-09-18"],
        [$children[6]??7, $hospitals[7]??8, $vaccines[0]??1, "2021-04-29"],
        [$children[7]??8, $hospitals[8]??9, $vaccines[3]??4, "2021-02-15"],
        [$children[8]??9, $hospitals[9]??10, $vaccines[0]??1, "2023-05-16"],
        [$children[9]??10, $hospitals[0]??1, $vaccines[8]??9, "2020-01-15"],
        [$children[9]??10, $hospitals[1]??2, $vaccines[9]??10, "2021-03-20"],
    ];

    foreach ($completedData as $d) {
        // Create appointment as completed
        $db->prepare("INSERT INTO appointments (child_id,hospital_id,vaccine_id,appointment_date,status) VALUES (?,?,?,?,'completed')")
           ->execute([$d[0],$d[1],$d[2],$d[3]]);
        $aid = $db->lastInsertId();
        // Create vaccination record
        $db->prepare("INSERT INTO vaccination_records (child_id,vaccine_id,hospital_id,administration_date) VALUES (?,?,?,?)")
           ->execute([$d[0],$d[2],$d[1],$d[3]]);
    }

    // Pending appointments
    $pendingData = [
        [$children[0]??1, $hospitals[0]??1, $vaccines[6]??7, date('Y-m-d', strtotime('+3 days'))],
        [$children[1]??2, $hospitals[1]??2, $vaccines[7]??8, date('Y-m-d', strtotime('+5 days'))],
        [$children[2]??3, $hospitals[2]??3, $vaccines[4]??5, date('Y-m-d', strtotime('+7 days'))],
        [$children[5]??6, $hospitals[3]??4, $vaccines[5]??6, date('Y-m-d', strtotime('+10 days'))],
        [$children[8]??9, $hospitals[4]??5, $vaccines[3]??4, date('Y-m-d', strtotime('+2 days'))],
    ];
    foreach ($pendingData as $d) {
        $db->prepare("INSERT INTO appointments (child_id,hospital_id,vaccine_id,appointment_date,status) VALUES (?,?,?,?,'pending')")
           ->execute([$d[0],$d[1],$d[2],$d[3]]);
    }

    // Approved appointments
    $approvedData = [
        [$children[3]??4, $hospitals[5]??6, $vaccines[1]??2, date('Y-m-d', strtotime('+1 day'))],
        [$children[6]??7, $hospitals[6]??7, $vaccines[2]??3, date('Y-m-d', strtotime('+4 days'))],
    ];
    foreach ($approvedData as $d) {
        $db->prepare("INSERT INTO appointments (child_id,hospital_id,vaccine_id,appointment_date,status) VALUES (?,?,?,?,'approved')")
           ->execute([$d[0],$d[1],$d[2],$d[3]]);
    }
}

// ---- FAKE INQUIRIES ----
if ($db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn() == 0) {
    $msgs = [
        ["Fatima Noor", "fatima.noor@gmail.com", "Appointment Confirmation", "I booked an appointment for my daughter but haven't received any confirmation. Please help.", "pending"],
        ["Dr. Tariq", "tariq.doctor@gmail.com", "Hospital Partnership", "We are interested in joining Vaxora's hospital network. Please contact us.", "replied"],
        ["Sana Malik", "sana.malik@gmail.com", "Vaccine Record Download", "Can I download my child's vaccination certificate in PDF?", "pending"],
        ["Ahmed Ali", "ahmed.ali@gmail.com", "Login Issue", "I forgot my password and the reset link is not working.", "replied"],
        ["Rukhsana Begum", "rukhsana.b@gmail.com", "Child Profile", "I need to update my child's blood group in the system.", "pending"],
    ];
    foreach ($msgs as $m) {
        $db->prepare("INSERT INTO contact_messages (name,email,subject,message,status) VALUES (?,?,?,?,?)")->execute($m);
    }
}

echo "Database initialized successfully!";
