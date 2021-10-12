--
-- File generated with SQLiteStudio v3.3.3 on Tue Oct 12 14:58:36 2021
--
-- Text encoding used: UTF-8
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: categories
CREATE TABLE categories (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR);

-- Table: company
CREATE TABLE company (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR, address_line1 VARCHAR, address_line2 VARCHAR, town VARCHAR, postcode VARCHAR, telephone VARCHAR, website VARCHAR, email VARCHAR, currencysymbol VARCHAR (1, 1) DEFAULT £, lastbackup DATETIME, appversion VARCHAR (10));

-- Table: customers
CREATE TABLE customers (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR, creationdate DATETIME DEFAULT (CURRENT_TIMESTAMP), notes TEXT, company_number INTEGER, vat_number INTEGER, invoice_terms INTEGER DEFAULT (0), hold INTEGER DEFAULT (0), website VARCHAR);

-- Table: customers_addresses
CREATE TABLE customers_addresses (id INTEGER PRIMARY KEY AUTOINCREMENT, customer INTEGER, line1 VARCHAR, line2 VARCHAR, town VARCHAR, postcode VARCHAR);

-- Table: customers_contacts
CREATE TABLE customers_contacts (id INTEGER PRIMARY KEY AUTOINCREMENT, customer INTEGER, name VARCHAR, email VARCHAR, telephone VARCHAR);

-- Table: jobs
CREATE TABLE jobs (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR, customer INTEGER, address INTEGER DEFAULT (0), contact INTEGER DEFAULT (0), startdate DATETIME, enddate DATETIME, jobType VARCHAR DEFAULT quote, quoteAgreed INTEGER DEFAULT (0), lost INTEGER DEFAULT (0), complete INTEGER DEFAULT (0), invoiced INTEGER DEFAULT (0));

-- Table: jobs_cat
CREATE TABLE jobs_cat (id INTEGER PRIMARY KEY AUTOINCREMENT, job INTEGER, cat VARCHAR);

-- Table: jobs_lines
CREATE TABLE jobs_lines (id INTEGER PRIMARY KEY AUTOINCREMENT, job INTEGER, linetype VARCHAR DEFAULT hire, stockEntry INTEGER, stockEffect INTEGER DEFAULT (- 1), price DOUBLE DEFAULT (0.0), cat INTEGER DEFAULT (0), qty INTEGER DEFAULT (1), itemName VARCHAR, parent INTEGER DEFAULT (0), kit INTEGER, cost DOUBLE DEFAULT (0), notes TEXT, dispatch INTEGER DEFAULT (0), dispatch_date DATETIME, return INTEGER DEFAULT (0), return_date DATETIME);

-- Table: kit
CREATE TABLE kit (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR, purchasevalue DOUBLE, sloc INTEGER, price DOUBLE, height DOUBLE, width DOUBLE, length DOUBLE, weight DOUBLE, notes TEXT, active INTEGER (1, 1) DEFAULT (1), toplevel INTEGER (1, 1) DEFAULT (1), cat INTEGER DEFAULT (0));

-- Table: kit_accessories
CREATE TABLE kit_accessories (id INTEGER PRIMARY KEY AUTOINCREMENT, accessory INTEGER, type VARCHAR, price DOUBLE DEFAULT (0.0), kit INTEGER, qty INTEGER DEFAULT (1));

-- Table: kit_stock
CREATE TABLE kit_stock (id INTEGER PRIMARY KEY AUTOINCREMENT, kit INTEGER, stock_count INTEGER, serialized INTEGER DEFAULT (0), serialnumber VARCHAR, purchasedate DATETIME);

-- Table: licence
CREATE TABLE licence (id INTEGER PRIMARY KEY AUTOINCREMENT, licencekey TEXT NOT NULL, licenceto VARCHAR, purchasedate DATETIME, lastactivation DATETIME, nextactivation DATETIME);

-- Table: remote
CREATE TABLE remote (id INTEGER PRIMARY KEY AUTOINCREMENT, host VARCHAR, username VARCHAR, password VARCHAR, dbname VARCHAR);

-- Table: sloc
CREATE TABLE sloc (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR, address_line1 VARCHAR, address_line2 VARCHAR, town VARCHAR, postcode VARCHAR);

-- Table: venues
CREATE TABLE venues (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR, address_line1 VARCHAR, address_line2 VARCHAR, town VARCHAR, postcode VARCHAR, telephone VARCHAR, website VARCHAR, email VARCHAR, notes TEXT);

COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
