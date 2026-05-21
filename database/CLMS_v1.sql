CREATE DATABASE AppSysComlabManagementSystem;
USE AppSysComlabManagementSystem;

-- =========================================
-- SYSTEM USERS
-- =========================================

CREATE TABLE system_users (
    system_user_id INT PRIMARY KEY AUTO_INCREMENT,

    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    suffix VARCHAR(5),

    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,

    role_type ENUM('admin','instructor') NOT NULL,

    reset_token VARCHAR(255) NULL,
    reset_token_expiration DATETIME NULL
);

CREATE TABLE system_user_privileges (
    privilege_id INT PRIMARY KEY AUTO_INCREMENT,

    system_user_id INT NOT NULL,

    can_manage_accounts BOOLEAN DEFAULT FALSE,
    can_manage_equipment BOOLEAN DEFAULT FALSE,
    can_manage_bookings BOOLEAN DEFAULT FALSE,
    can_manage_logs BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (system_user_id)
        REFERENCES system_users(system_user_id)
        ON DELETE CASCADE
);

-- =========================================
-- LABORATORIES
-- =========================================

CREATE TABLE laboratories (
    lab_id INT PRIMARY KEY AUTO_INCREMENT,

    lab_name VARCHAR(100) NOT NULL,
    location VARCHAR(150),
    capacity INT DEFAULT 0
);

-- =========================================
-- OFFICE SUPPLIES / INVENTORY
-- =========================================

CREATE TABLE office_supplies (
    supply_id INT PRIMARY KEY AUTO_INCREMENT,

    supply_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,

    unit VARCHAR(30),
    remarks VARCHAR(255)
);

-- =========================================
-- STUDENTS
-- =========================================

CREATE TABLE students (
    student_id VARCHAR(30) PRIMARY KEY,

    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    suffix VARCHAR(5)
);

-- =========================================
-- COURSES
-- =========================================

CREATE TABLE courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,

    course_code VARCHAR(20) UNIQUE NOT NULL,
    course_name VARCHAR(100) NOT NULL
);

-- =========================================
-- GUEST LOGS
-- =========================================

CREATE TABLE guest_logs (
    guest_log_id INT PRIMARY KEY AUTO_INCREMENT,

    guest_name VARCHAR(150) NOT NULL,
    organization VARCHAR(150),
    contact_number VARCHAR(30),
    purpose VARCHAR(255)
);

-- =========================================
-- EQUIPMENT / INVENTORY
-- =========================================

CREATE TABLE equipments (
    equipment_id INT PRIMARY KEY AUTO_INCREMENT,

    equipment_no VARCHAR(50) NOT NULL,
    serial_no VARCHAR(100) UNIQUE,

    equipment_name VARCHAR(150) NOT NULL,
    model_number VARCHAR(100),

    equipment_type ENUM(
        'computer_unit',
        'peripheral',
        'component',
        'miscellaneous'
    ) NOT NULL,

    quantity INT DEFAULT 1,

    lab_id INT NOT NULL,

    parent_equipment_id INT NULL,

    preventive_maintenance_done BOOLEAN DEFAULT FALSE,
    remarks VARCHAR(255),

    FOREIGN KEY (lab_id)
        REFERENCES laboratories(lab_id)
        ON DELETE RESTRICT,

    FOREIGN KEY (parent_equipment_id)
        REFERENCES equipments(equipment_id)
        ON DELETE SET NULL
);

-- =========================================
-- ACADEMIC TERMS
-- =========================================

CREATE TABLE academic_terms (
    academic_term_id INT PRIMARY KEY AUTO_INCREMENT,

    academic_year VARCHAR(20) NOT NULL,

    semester ENUM(
        '1st',
        '2nd',
        'summer'
    ) NOT NULL,

    start_date DATE NOT NULL,
    end_date DATE NOT NULL
);

-- =========================================
-- LAB UTILIZATION LOGS
-- =========================================

CREATE TABLE lab_utilization_logs (
    lab_utilization_log_id INT PRIMARY KEY AUTO_INCREMENT,

    student_id VARCHAR(30) NULL,
    guest_log_id INT NULL,

    purpose VARCHAR(255) NOT NULL,

    instructor_id INT NULL,

    lab_id INT NOT NULL,

    log_date DATE NOT NULL,

    time_in DATETIME NOT NULL,
    time_out DATETIME NULL,

    FOREIGN KEY (student_id)
        REFERENCES students(student_id)
        ON DELETE SET NULL,

    FOREIGN KEY (guest_log_id)
        REFERENCES guest_logs(guest_log_id)
        ON DELETE SET NULL,

    FOREIGN KEY (instructor_id)
        REFERENCES system_users(system_user_id)
        ON DELETE SET NULL,

    FOREIGN KEY (lab_id)
        REFERENCES laboratories(lab_id)
        ON DELETE CASCADE,

    CONSTRAINT chk_student_or_guest
    CHECK (
        (student_id IS NOT NULL AND guest_log_id IS NULL)
        OR
        (student_id IS NULL AND guest_log_id IS NOT NULL)
    )
);

-- =========================================
-- CLASS SCHEDULE
-- =========================================

CREATE TABLE class_schedule (
    class_schedule_id INT PRIMARY KEY AUTO_INCREMENT,

    course_id INT NOT NULL,
    instructor_id INT NOT NULL,
    lab_id INT NOT NULL,

    day_of_week ENUM(
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday'
    ) NOT NULL,

    time_start TIME NOT NULL,
    time_end TIME NOT NULL,

    term_id INT NOT NULL,

    FOREIGN KEY (course_id)
        REFERENCES courses(course_id)
        ON DELETE CASCADE,

    FOREIGN KEY (instructor_id)
        REFERENCES system_users(system_user_id)
        ON DELETE CASCADE,

    FOREIGN KEY (lab_id)
        REFERENCES laboratories(lab_id)
        ON DELETE CASCADE,

    FOREIGN KEY (term_id)
        REFERENCES academic_terms(academic_term_id)
        ON DELETE CASCADE
);

-- =========================================
-- STUDENT ENROLLMENT
-- =========================================

CREATE TABLE student_enrollment (
    enrollment_id INT PRIMARY KEY AUTO_INCREMENT,

    student_id VARCHAR(30) NOT NULL,
    class_schedule_id INT NOT NULL,

    FOREIGN KEY (student_id)
        REFERENCES students(student_id)
        ON DELETE CASCADE,

    FOREIGN KEY (class_schedule_id)
        REFERENCES class_schedule(class_schedule_id)
        ON DELETE CASCADE,

    UNIQUE(student_id, class_schedule_id)
);

-- =========================================
-- LAB BOOKINGS
-- =========================================

CREATE TABLE bookings (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,

    lab_id INT NOT NULL,

    requestor_name VARCHAR(150) NOT NULL,
    contact_number VARCHAR(50),

    purpose VARCHAR(255) NOT NULL,

    booking_date DATE NOT NULL,

    start_time TIME NOT NULL,
    end_time TIME NOT NULL,

    booking_status ENUM(
        'pending',
        'approved',
        'rejected',
        'completed'
    ) DEFAULT 'pending',

    approved_by INT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (lab_id)
        REFERENCES laboratories(lab_id)
        ON DELETE CASCADE,

    FOREIGN KEY (approved_by)
        REFERENCES system_users(system_user_id)
        ON DELETE SET NULL
);