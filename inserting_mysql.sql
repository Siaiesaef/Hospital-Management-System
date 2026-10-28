/* Insert Patients*/
INSERT INTO Patient
(pFirstName, pMiddleName, pLastName, pDOB, pGender, pAddress,
pCity, pPostcode, pEmail, pPhoneNo)
VALUES
("Anini", null, "Srinivasan", "2006-06-23", "Female", "173 Road Drive", "Sheffield", "S4 5EG", 
"aniniS173@exapmle.com", "07407327003"),
("Rachel", "Margret", "Kirtland", "2002-09-04", "Female", "234 Boston Street", "Leeds", "L13 6RH",
"rachaelkirtland@example.com", "07893748576"),
("Sia", "Amba", "Pugal", "2006-10-24", "Female", "267 Thirlwall Drive", "Newcastle", "NE10 8BD",
"siaamba@example.co.uk", "+44 7548736317"),
("Daniel", "James", "Thompson", "1995-08-17", "Male", "42 Maple Street", "Manchester", "M14 6XY",
"daniel.thompson@example.com", "07123456789"),
("Adam", null, "Khan", "2001-02-03", "Male", "18 Willow Close", "Birmingham", "B27 9QT",
"adam.khan01@example.com", "+44 7987654321");

/* Insert Staff Types*/
INSERT INTO StaffType
(stSpeciality) 
VALUES 
("Neurosurgeon"), 
("Cardiothoracic  Surgeon"),
("Pediatric Surgeon"),
("Orthopedic Surgeon"),
("Plastic  Surgeon");

/* Insert Staff*/
INSERT INTO Staff
(staffTypeID, sFirstName, sMiddleName, sLastName, sEmail, sType, sPhoneNo)
VALUES
(1, "Sheila", null, "Brown", "sheilab24@example.co.uk",
"Doctor", "07548757567"),

(null, "Dan", "Adams", "Andrew", "dan.a.andrew@example.com",
"Admin", "+44 7398902378"),

(2, "Anini", null, "Srinivasan", "aniniS173@example.com",
"Doctor", "07407327003"),

(null, "Ava", null, "David", "avadavid@example.com",
"Admin", "+44 7856344356"),

(5, "Paul", "Dave", "Marie", "pauldave@example.com",
"Doctor", "+44 7903478495");

/* Insert Login*/
INSERT INTO Login
(patientID, staffID, lUsername, lPassword)
VALUES
(1, null, "AS1.P", "Password1."),
(null, 3, "AS2.S", "Password2."),
(4, null, "DJT1.P", "passWord3!"),
(null, 5, "PDM1.S", "pAssword4,"),
(null, 1, "SB1.S", "paSsword5?");


/* Insert Prescriptions*/
INSERT INTO Prescription
(patientID, staffID, datePrescribed, dateCollected, 
medication, dosage, nextPickUp, repeatPrescription)
VALUES 
(3, 3, "2026-01-28", "2026-01-28", "Statins", 
"1 per day", "2026-02-28", "Yes"),

(2, 5, "2025-12-11", "2025-12-13", "PainKillers",
"One every 4 hours", null, null),

(5, 5, "2025-12-28", "2026-01-02", "Numbing Cream",
"2 hours before procedure", null, null),

(4, 1, "2025-11-01", "2025-11-02", "Anti-Seizure", 
"one every 12 hours", null, null),

(1, 1, "2026-02-04", "2026-02-05", "Antibiotics", 
"one every 8 hours", "2026-02-19", "Yes");


/* Insert Test Results*/
INSERT INTO TestResults
(patientID, staffID, testResults, dateTested)
VALUES
(1, 1, "Brain tumor", "2026-01-29"),
(1, 5, "O-", "2025-06-01"),
(2, 3, "Safe to operate", "2026-02-08"),
(3, 3, "AB+", "2026-01-20"),
(1, 1, "A+", "2026-01-30");


/* Insert Appointments*/
INSERT INTO Appointment
(patientID, staffID, testResultsID, aDate)
VALUES
(1, 1, 1, "2026-02-02"),
(1, 5, 2, "2025-06-10"),
(2, 3, 3, "2026-02-14"),
(3, 3, 4, "2026-01-25"),
(1, 1, 5, "2026-02-11");