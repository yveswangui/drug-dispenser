-- Create patient table
CREATE TABLE IF NOT EXISTS patient (
	patientId INT AUTO_INCREMENT PRIMARY KEY,
	firstName VARCHAR(50) NOT NULL,
	lastName VARCHAR(50) NOT NULL,
	gender VARCHAR(10) NOT NULL,
	location VARCHAR(255) NOT NULL,
	emailAddress VARCHAR(255) UNIQUE NOT NULL,
	phoneNumber VARCHAR(20) UNIQUE NOT NULL,
	SSN VARCHAR(10) NOT NULL,
	passwordHash VARCHAR(255) NOT NULL,
	dateOfBirth DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create doctor table
CREATE TABLE IF NOT EXISTS doctor (
	doctorId INT AUTO_INCREMENT PRIMARY KEY,
	firstName VARCHAR(50) NOT NULL,
	lastName VARCHAR(50) NOT NULL,
	gender VARCHAR(10) NOT NULL,
	phoneNumber VARCHAR(20) NOT NULL,
	hospital VARCHAR(255) NOT NULL,
	emailAddress VARCHAR(255) UNIQUE NOT NULL,
	specialization VARCHAR(100) NOT NULL,
	passwordHash VARCHAR(255) NOT NULL,
	SSN VARCHAR(10) NOT NULL
);

-- Create doctor_patient_assignment table
CREATE TABLE IF NOT EXISTS doctor_patient_assignment (
	doctorPatientAssignmentId INT AUTO_INCREMENT PRIMARY KEY,
	doctorId INT NOT NULL,
	patientId INT NOT NULL,
	primaryAssignment BOOLEAN NOT NULL DEFAULT FALSE,
	FOREIGN KEY (doctorId) REFERENCES doctor(doctorId),
	FOREIGN KEY (patientId) REFERENCES patient(patientId)
);

-- Create pharmacy table
CREATE TABLE IF NOT EXISTS pharmacy (
	pharmacyId INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(127) NOT NULL,
	location VARCHAR(255) NOT NULL,
	phoneNumber VARCHAR(20) UNIQUE NOT NULL,
	emailAddress VARCHAR(100) UNIQUE NOT NULL,
	operator VARCHAR(100) NOT NULL,
	passwordHash VARCHAR(255) NOT NULL
);

-- Create pharmaceutical table
CREATE TABLE IF NOT EXISTS pharmaceutical (
	pharmaceuticalId INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(127) NOT NULL,
	location VARCHAR(255) NOT NULL,
	phoneNumber VARCHAR(20) UNIQUE NOT NULL,
	emailAddress VARCHAR(255) UNIQUE NOT NULL,
	operator VARCHAR(100) NOT NULL,
	passwordHash VARCHAR(255) NOT NULL
);

-- Create contract table
CREATE TABLE IF NOT EXISTS contract (
	contractId INT AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(100) NOT NULL,
	startDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	endDate DATETIME NOT NULL,
	pharmacyId INT NOT NULL,
	pharmaceuticalId INT NOT NULL,
	FOREIGN KEY (pharmacyId) REFERENCES pharmacy(pharmacyId),
	FOREIGN KEY (pharmaceuticalId) REFERENCES pharmaceutical(pharmaceuticalId)
);

-- Create drug table
CREATE TABLE IF NOT EXISTS drug (
	drugId INT AUTO_INCREMENT PRIMARY KEY,
	tradename VARCHAR(100) NOT NULL,
	scientificName VARCHAR(255) NOT NULL,
	formula VARCHAR(100) NOT NULL,
	form VARCHAR(50) NOT NULL,
	contractId INT NOT NULL,
	expirationDate DATETIME NOT NULL,
	manufacturingDate DATETIME NOT NULL,
	FOREIGN KEY (contractId) REFERENCES contract(contractId)
);

-- Create prescription table
CREATE TABLE IF NOT EXISTS prescription (
	prescriptionId INT AUTO_INCREMENT PRIMARY KEY,
	doctorPatientAssignmentId INT NOT NULL,
	drugId INT NOT NULL,
	dosage VARCHAR(100) NOT NULL,
	quantity INT NOT NULL,
	endDate DATETIME NOT NULL,
	startDate DATETIME NOT NULL,
	FOREIGN KEY (doctorPatientAssignmentId) REFERENCES doctor_patient_assignment(doctorPatientAssignmentId),
	FOREIGN KEY (drugId) REFERENCES drug(drugId)
);

-- Create payment table
CREATE TABLE IF NOT EXISTS payment (
	paymentId INT AUTO_INCREMENT PRIMARY KEY,
	paymentDate DATETIME NOT NULL,
	amount DECIMAL(10, 2) NOT NULL,
	method VARCHAR(50) NOT NULL,
	description VARCHAR(100) NOT NULL,
	prescriptionId INT NOT NULL,
	FOREIGN KEY (prescriptionId) REFERENCES prescription(prescriptionId)
);


INSERT INTO patient (firstName, lastName, gender, location, emailAddress, phoneNumber, SSN, passwordHash, dateOfBirth)
VALUES
('James', 'Mwangi', 'Male', 'Nairobi', 'jamesmwangi@example.com', '0712345678', '1234567890', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '1985-05-10'),
('Mary', 'Wanjiru', 'Female', 'Nakuru', 'marywanjiru@example.com', '0723456789', '0987654321', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '1990-01-01'),
('John', 'Kamau', 'Male', 'Mombasa', 'johnkamau@example.com', '0734567890', '1111111111', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '1995-07-15'),
('Grace', 'Njeri', 'Female', 'Eldoret', 'gracenjeri@example.com', '0745678901', '2222222222', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '1980-12-20'),
('Daniel', 'Muthoni', 'Male', 'Kisumu', 'danielmuthoni@example.com', '0756789012', '3333333333', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '1988-08-08'),
('Sarah', 'Kimani', 'Female', 'Nyeri', 'sarahkimani@example.com', '0767890123', '4444444444', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '1992-04-25'),
('David', 'Ochieng', 'Male', 'Nanyuki', 'davidochieng@example.com', '0778901234', '5555555555', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '1987-09-12'),
('Alice', 'Maina', 'Female', 'Meru', 'alicemaina@example.com', '0789012345', '6666666666', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '1998-03-07'),
('Joseph', 'Wambui', 'Male', 'Kitale', 'josephwambui@example.com', '0790123456', '7777777777', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '1993-11-15'),
('Hannah', 'Omondi', 'Female', 'Kisii', 'hannahomondi@example.com', '0701234567', '8888888888', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '1991-06-30');


INSERT INTO doctor (firstName, lastName, gender, phoneNumber, hospital, emailAddress, specialization, passwordHash, SSN)
VALUES
('Dr. James', 'Mwangi', 'Male', '0712345678', 'Kenyatta National Hospital', 'jamesmwangi@example.com', 'Cardiology', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '1234567890'),
('Dr. Mary', 'Wanjiru', 'Female', '0723456789', 'Aga Khan Hospital', 'marywanjiru@example.com', 'Pediatrics', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '0987654321'),
('Dr. John', 'Kamau', 'Male', '0734567890', 'Nairobi Hospital', 'johnkamau@example.com', 'Orthopedics', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '1111111111'),
('Dr. Grace', 'Njeri', 'Female', '0745678901', 'Mater Hospital', 'gracenjeri@example.com', 'Internal Medicine', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '2222222222'),
('Dr. Daniel', 'Muthoni', 'Male', '0756789012', 'Kisumu County Referral Hospital', 'danielmuthoni@example.com', 'General Surgery', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '3333333333'),
('Dr. Sarah', 'Kimani', 'Female', '0767890123', 'Coast Provincial General Hospital', 'sarahkimani@example.com', 'Obstetrics and Gynecology', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '4444444444'),
('Dr. David', 'Ochieng', 'Male', '0778901234', 'Moi Teaching and Referral Hospital', 'davidochieng@example.com', 'Neurology', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '5555555555'),
('Dr. Alice', 'Maina', 'Female', '0789012345', 'Nairobi West Hospital', 'alicemaina@example.com', 'Dermatology', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '6666666666'),
('Dr. Joseph', 'Wambui', 'Male', '0790123456', 'Nyeri County Referral Hospital', 'josephwambui@example.com', 'Ophthalmology', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '7777777777'),
('Dr. Hannah', 'Omondi', 'Female', '0701234567', 'Kisii Teaching and Referral Hospital', 'hannahomondi@example.com', 'Psychiatry', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK', '8888888888');

INSERT INTO doctor_patient_assignment (doctorId, patientId, primaryAssignment)
VALUES
(1, 1, true),
(2, 2, true),
(3, 3, true),
(4, 4, true),
(5, 5, true),
(6, 6, true),
(7, 7, true),
(8, 8, true),
(9, 9, true),
(10, 10, true);

INSERT INTO pharmaceutical (name, location, phoneNumber, emailAddress, operator, passwordHash)
VALUES
('PharmaKenya', 'Nairobi', '1234567890', 'info@pharmakenya.com', 'John Maina', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK'),
('MediHealth', 'Mombasa', '9876543210', 'info@medihealth.co.ke', 'Grace Mwangi', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK'),
('HealthPlus', 'Nakuru', '2345678901', 'info@healthpluspharma.com', 'James Gichuru', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK'),
('CureMart', 'Kisumu', '8765432109', 'info@curemart.co.ke', 'Mary Wanjiku', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK'),
('PharmaLife', 'Eldoret', '3456789012', 'info@pharmalife.co.ke', 'David Kimani', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK');

INSERT INTO pharmacy (name, location, phoneNumber, emailAddress, operator, passwordHash)
VALUES
('HealthMart Pharmacy', 'Nairobi', '1234567890', 'info@healthmartpharmacy.co.ke', 'Josephine Njeri', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK'),
('MediCare Pharmacy', 'Mombasa', '9876543210', 'info@medicarepharmacy.com', 'Michael Kamau', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK'),
('PharmaPlus', 'Nakuru', '2345678901', 'info@pharmaplus.co.ke', 'Grace Nyambura', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK'),
('MedLife Pharmacy', 'Kisumu', '8765432109', 'info@medlifepharmacy.co.ke', 'Daniel Owino', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK'),
('CurePharm', 'Eldoret', '3456789012', 'info@curepharm.co.ke', 'Lucy Wambui', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK');

-- Insert 3 contracts for each pharmaceutical with start dates between June 18 and June 20
INSERT INTO contract (title, startDate, endDate, pharmacyId, pharmaceuticalId)
VALUES
('Contract 1', '2023-06-18', '2025-07-18', 1, 1),
('Contract 2', '2023-06-19', '2025-07-19', 2, 1),
('Contract 3', '2023-06-20', '2026-07-20', 3, 1),
('Contract 1', '2023-06-18', '2024-07-18', 4, 2),
('Contract 2', '2023-06-19', '2024-07-19', 5, 2),
('Contract 3', '2023-06-20', '2024-07-20', 1, 2),
('Contract 1', '2023-06-18', '2025-07-18', 2, 3),
('Contract 2', '2023-06-19', '2027-07-19', 3, 3),
('Contract 3', '2023-06-20', '2025-07-20', 4, 3),
('Contract 1', '2023-06-18', '2024-07-18', 5, 4),
('Contract 2', '2023-06-19', '2025-07-19', 1, 4),
('Contract 3', '2023-06-20', '2025-07-20', 2, 4),
('Contract 1', '2023-06-18', '2025-07-18', 3, 5),
('Contract 2', '2023-06-19', '2024-07-19', 4, 5),
('Contract 3', '2023-06-20', '2026-07-20', 5, 5);

-- Insert 45 drug records (3 drug records for each of the 15 contracts)
INSERT INTO drug (tradename, scientificName, formula, form, contractId, expirationDate, manufacturingDate)
VALUES
-- Contract 1
('Ibuprofen', '2-(4-isobutylphenyl)propanoic acid', 'C13H18O2', 'Tablet', 1, '2023-07-18', '2023-06-18'),
('Paracetamol', 'N-(4-hydroxyphenyl)acetamide', 'C8H9NO2', 'Tablet', 1, '2023-07-19', '2023-06-19'),
('Amoxicillin', '4-hydroxyphenylacetamide', 'C16H19N3O5S', 'Capsule', 1, '2023-07-20', '2023-06-20'),

-- Contract 2
('Omeprazole', '5-methoxy-2-[(4-methoxy-3,5-dimethylpyridin-2-yl)methylsulfinyl]-1H-benzimidazole', 'C17H19N3O3S', 'Capsule', 2, '2023-07-18', '2023-06-18'),
('Metformin', 'N,N-dimethylimidodicarbonimidic diamide', 'C4H11N5', 'Tablet', 2, '2023-07-19', '2023-06-19'),
('Atorvastatin', 'calcium salt', 'C33H34FN2O5', 'Tablet', 2, '2023-07-20', '2023-06-20'),

-- Contract 3
('Ciprofloxacin', '1-cyclopropyl-6-fluoro-4-oxo-7-(piperazin-1-yl)-1,4-dihydroquinoline-3-carboxylic acid', 'C17H18FN3O3', 'Tablet', 3, '2023-07-18', '2023-06-18'),
('Aspirin', 'acetylsalicylic acid', 'C9H8O4', 'Tablet', 3, '2023-07-19', '2023-06-19'),
('Losartan', '2-butyl-4-chloro-1-[p-(o-1H-tetrazol-5-ylphenyl)benzyl]imidazole-5-methanol', 'C22H23ClN6O', 'Tablet', 3, '2023-07-20', '2023-06-20'),
('Ibuprofen', '2-(4-isobutylphenyl)propanoic acid', 'C13H18O2', 'Tablet', 9, '2023-07-18', '2023-06-18'),
('Paracetamol', 'N-(4-hydroxyphenyl)acetamide', 'C8H9NO2', 'Tablet', 9, '2023-07-19', '2023-06-19'),
('Amoxicillin', '4-hydroxyphenylacetamide', 'C16H19N3O5S', 'Capsule', 9, '2023-07-20', '2023-06-20'),

-- Contract 2
('Omeprazole', '5-methoxy-2-[(4-methoxy-3,5-dimethylpyridin-2-yl)methylsulfinyl]-1H-benzimidazole', 'C17H19N3O3S', 'Capsule', 12, '2023-07-18', '2023-06-18'),
('Metformin', 'N,N-dimethylimidodicarbonimidic diamide', 'C4H11N5', 'Tablet', 12, '2023-07-19', '2023-06-19'),
('Atorvastatin', 'calcium salt', 'C33H34FN2O5', 'Tablet', 12, '2023-07-20', '2023-06-20'),

-- Contract 3
('Ciprofloxacin', '1-cyclopropyl-6-fluoro-4-oxo-7-(piperazin-1-yl)-1,4-dihydroquinoline-3-carboxylic acid', 'C17H18FN3O3', 'Tablet', 13, '2023-07-18', '2023-06-18'),
('Aspirin', 'acetylsalicylic acid', 'C9H8O4', 'Tablet', 13, '2023-07-19', '2023-06-19'),
('Losartan', '2-butyl-4-chloro-1-[p-(o-1H-tetrazol-5-ylphenyl)benzyl]imidazole-5-methanol', 'C22H23ClN6O', 'Tablet', 13, '2023-07-20', '2023-06-20'),

-- Contract 4
('Amlodipine', '3-ethyl 5-methyl (4RS)-2-[(2-aminoethoxy)methyl]-4-(2-chlorophenyl)-6-methyl-1,4-dihydropyridine-3,5-dicarboxylate', 'C20H25ClN2O5', 'Tablet', 14, '2023-07-18', '2023-06-18'),
('Lisinopril', '1-[N2-(1-carboxy-3-phenylpropyl)-L-lysyl]-L-proline', 'C21H31N3O5', 'Tablet', 14, '2023-07-19', '2023-06-19'),
('Simvastatin', '[(1S,3R,7S,8S,8aR)-8-[2-[(2R,4R)-4-hydroxy-6-oxotetrahydro-2H-pyran-2-yl]ethyl]-3,7-dimethyl-1,2,3,7,8,8a-hexahydronaphthalen-1-yl] 2,2-dimethylbutanoate', 'C25H38O5', 'Tablet', 14, '2023-07-20', '2023-06-20'),

-- Contract 5
('Metronidazole', '2-(2-methyl-5-nitro-1H-imidazol-1-yl)ethanol', 'C6H9N3O3', 'Tablet', 15, '2023-07-18', '2023-06-18'),
('Warfarin', '4-hydroxy-3-(3-oxo-1-phenylbutyl)-2H-chromen-2-one', 'C19H16O4', 'Tablet', 15, '2023-07-19', '2023-06-19'),
('Ceftriaxone', '2-[(2-amino-1,3-thiazol-4-yl)-methoxyimino]-2-[2-(2-amino-1,3-thiazol-4-yl)-2-(methoxyimino)acetyl]amino-1,3-thiazol-4-yl]-2-(methoxyimino)acetyl]amino-1,3-thiazol-4-yl]-2-(methoxyimino)acetyl]amino-1,3-thiazol-4-yl]acetic acid', 'C18H18N8O7S3', 'Injection', 15, '2023-07-20', '2023-06-20'),

-- Contract 6
('Azithromycin', '9-deoxo-9a-aza-9a-methyl-9a-homoerythromycin A', 'C38H72N2O12', 'Tablet', 6, '2023-07-18', '2023-06-18'),
('Hydrochlorothiazide', '6-chloro-3,4-dihydro-2H-1,2,4-benzothiadiazine-7-sulfonamide 1,1-dioxide', 'C7H8ClN3O4S2', 'Tablet', 6, '2023-07-19', '2023-06-19'),
('Metronidazole', '2-(2-methyl-5-nitro-1H-imidazol-1-yl)ethanol', 'C6H9N3O3', 'Tablet', 6, '2023-07-20', '2023-06-20'),

-- Contract 7
('Cefuroxime', '1-acetyl-4-[(4R,5S)-5-[(2,4-dioxothiazolidin-3-yl)acetyl]sulfanylmethyl]-2-methyl-1,3-diazetidine-4-carboxylic acid', 'C16H16N4O8S', 'Tablet', 7, '2023-07-18', '2023-06-18'),
('Furosemide', '4-chloro-2-furan-2-yl-2-pyrimidin-2-yl-1,3-diazabicyclo[3.1.0]hex-3-en-2-one', 'C12H11ClN2O5S', 'Tablet', 7, '2023-07-19', '2023-06-19'),
('Paracetamol', 'N-(4-hydroxyphenyl)acetamide', 'C8H9NO2', 'Tablet', 7, '2023-07-20', '2023-06-20');
