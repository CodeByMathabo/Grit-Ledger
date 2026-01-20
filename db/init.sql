-- Create the accounts table
CREATE TABLE IF NOT EXISTS accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_number VARCHAR(20) NOT NULL UNIQUE,
    account_holder_name VARCHAR(100) NOT NULL,
    balance DECIMAL(15, 2) NOT NULL DEFAULT 0.00
    );

-- Seed initial data so the app is ready to test immediately
INSERT INTO accounts (account_number, account_holder_name, balance) VALUES
('ACC-1001', 'Alice Manager', 5000.00),
('ACC-1002', 'Bob Recruiter', 1000.00);