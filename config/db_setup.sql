-- Table 1: Products
CREATE TABLE products (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci,
    description TEXT NULL COLLATE utf8mb4_general_ci,
    category VARCHAR(100) NULL COLLATE utf8mb4_general_ci,
    sku VARCHAR(100) NULL COLLATE utf8mb4_general_ci,
    purchase_price DECIMAL(10,2) NULL,
    unit_price DECIMAL(10,2) NULL,
    selling_price DECIMAL(10,2) NULL,
    supplier VARCHAR(255) NULL COLLATE utf8mb4_general_ci,
    date_added TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Active', 'Inactive') NULL DEFAULT 'Active' COLLATE utf8mb4_general_ci,
    PRIMARY KEY (id)
);

-- Table 2: Sales/Transactions
CREATE TABLE sales (
    id INT(11) NOT NULL AUTO_INCREMENT,
    transaction_id VARCHAR(50) NOT NULL COLLATE utf8mb4_general_ci,
    product_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    sale_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    is_cancelled TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
);

-- Table 3: Stock
CREATE TABLE stock (
    stock_id INT(11) NOT NULL AUTO_INCREMENT,
    product_id INT(11) NOT NULL,
    quantity INT(11) NULL DEFAULT 0,
    last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    purchase_price DECIMAL(10,2) NULL,
    selling_price DECIMAL(10,2) NULL,
    PRIMARY KEY (stock_id)
);

-- Table 4: Users
CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    company_id VARCHAR(50) NOT NULL COLLATE utf8mb4_general_ci,
    username VARCHAR(50) NOT NULL COLLATE utf8mb4_general_ci,
    email VARCHAR(100) NULL COLLATE utf8mb4_general_ci,
    password VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci,
    role ENUM('admin', 'manager', 'user') NULL DEFAULT 'user' COLLATE utf8mb4_general_ci,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- Add Foreign Key Constraints
ALTER TABLE sales 
ADD CONSTRAINT fk_sales_product 
FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE sales 
ADD CONSTRAINT fk_sales_user 
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE stock 
ADD CONSTRAINT fk_stock_product 
FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- Add Indexes for better performance
CREATE INDEX idx_products_sku ON products(sku);
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_sales_transaction_id ON sales(transaction_id);
CREATE INDEX idx_sales_sale_date ON sales(sale_date);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_company_id ON users(company_id);
CREATE INDEX idx_stock_product_id ON stock(product_id);
