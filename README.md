# Scoring System

A comprehensive scoring application built with the LAMP stack (Linux, Apache, MySQL, PHP) that allows administrators to manage judges and participants, enables judges to score participants, and displays real-time public scoreboards.

## Table of Contents

- [Features](#features)
- [Project Structure](#project-structure)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [Database Schema](#database-schema)
- [Design Choices](#design-choices)
- [Assumptions](#assumptions)
- [Future Enhancements](#future-enhancements)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## Features

- 🎯 **Admin Panel**: Add and manage judges and participants
- ⚖️ **Judge Portal**: Score participants with validation (0-100 points)
- 🏆 **Live Scoreboard**: Auto-refreshing public scoreboard with rankings
- 🔄 **Real-time Updates**: Scoreboard refreshes every 30 seconds
- 📱 **Responsive Design**: Clean, modern interface

## Project Structure

```
scoring-app/
├── index.php          # Home page with navigation
├── admin.php          # Admin panel for managing judges/participants
├── judge.php          # Judge portal for scoring
├── scoreboard.php     # Public scoreboard display
├── db.php             # Database connection configuration
├── schema.sql         # Database schema
├── sample_data.sql    # Sample data SQL file
├── sample_data.php    # Sample data generator (alternative)
├── style.css          # Main stylesheet
├── admin.css          # Admin-specific styles
└── README.md          # This file
```

## Prerequisites

Before you begin, ensure you have the following installed:

- Linux (Ubuntu/Debian recommended)
- Apache2 web server
- MySQL database server
- PHP 8.1+ with required extensions

## Installation

### Step 1: Install Dependencies

```bash
# Update system packages
sudo apt update

# Install Apache2
sudo apt install apache2

# Install MySQL
sudo apt install mysql-server

# Install PHP and required extensions
sudo apt install php8.1 php8.1-mysql php8.1-pdo php8.1-common php8.1-cli libapache2-mod-php8.1

# Start services
sudo systemctl start apache2
sudo systemctl start mysql
sudo systemctl enable apache2
sudo systemctl enable mysql
```

### Step 2: Clone the Repository

```bash
# Navigate to web root
cd /var/www/html

# Clone the repository
sudo git clone https://github.com/PowellHabwe/scoring-system.git scoring-app

# Set proper permissions
sudo chown -R www-data:www-data /var/www/html/scoring-app
sudo chmod -R 755 /var/www/html/scoring-app
```

### Step 3: Configure Database Connection

Edit the database configuration file:

```bash
sudo nano /var/www/html/scoring-app/db.php
```

Update the database credentials if needed:

```php
<?php
$host = 'localhost';
$db   = 'scoring_app';
$user = 'scoring_user';  // or 'root' if using root
$pass = 'scoring_pass';  // your database password
$charset = 'utf8mb4';
?>
```

## Database Setup

### Create Database and User

```bash
# Access MySQL as root
sudo mysql -u root -p
```

```sql
-- Create database and user
CREATE DATABASE scoring_app;
CREATE USER 'scoring_user'@'localhost' IDENTIFIED BY 'scoring_pass';
GRANT ALL PRIVILEGES ON scoring_app.* TO 'scoring_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Import Database Schema

```bash
# Import database schema
mysql -u scoring_user -p scoring_app < /var/www/html/scoring-app/schema.sql
```

### Load Sample Data

You have two options to prepopulate the database with sample data:

#### Option A: Using SQL File (Recommended)

```bash
# Import sample data directly from SQL file
mysql -u scoring_user -p scoring_app < /var/www/html/scoring-app/sample_data.sql
```

#### Option B: Using PHP Script

Visit the following URL in your browser:

```
http://localhost/scoring-app/sample_data.php
```

Both methods will add:
- 3 sample judges (John Doe, Jane Smith, Mike Wilson)
- 5 sample participants (Alice Johnson, Bob Brown, Charlie Davis, Diana Evans, Ethan Foster)

### Access the Application

Open your web browser and navigate to:

```
http://localhost/scoring-app/
```

## Usage

### Admin Panel (`/admin.php`)

- Add new judges with unique usernames and display names
- Add new participants to the scoring system
- View current judges and participants lists
- Real-time feedback on successful additions

### Judge Portal (`/judge.php`)

- Select your judge profile from the dropdown
- View all participants in the system
- Assign scores (0-100 points) to participants
- Update existing scores as needed
- Immediate confirmation of score submissions

### Public Scoreboard (`/scoreboard.php`)

- View real-time rankings of all participants
- See total accumulated points from all judges
- Automatic refresh every 30 seconds
- First place highlighted with gold background
- Number of scores received displayed for each participant

## Database Schema

The application uses three main tables with proper foreign key relationships:

### Judges Table

```sql
CREATE TABLE judges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL
);
```

### Participants Table

```sql
CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);
```

### Scores Table

```sql
CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judge_id INT,
    participant_id INT,
    points INT CHECK (points >= 0 AND points <= 100),
    FOREIGN KEY (judge_id) REFERENCES judges(id),
    FOREIGN KEY (participant_id) REFERENCES participants(id)
);
```

## Design Choices

### Database Design

- **Normalized Structure**: Three-table design eliminates data redundancy
- **Foreign Key Constraints**: Ensures data integrity between judges, participants, and scores
- **Flexible Scoring**: Allows multiple judges to score the same participant
- **Unique Constraints**: Prevents duplicate judge usernames

### PHP Implementation

- **PDO (PHP Data Objects)**: Used for secure database interactions with prepared statements
- **Separation of Concerns**: Database logic separated from presentation
- **Error Handling**: Basic error handling for database connections and operations
- **Input Validation**: Server-side validation for score ranges (0-100)

### Frontend Design

- **Progressive Enhancement**: Works without JavaScript, enhanced with auto-refresh
- **Responsive Layout**: Adapts to different screen sizes
- **Clean UI/UX**: Intuitive navigation and clear visual hierarchy
- **Real-time Updates**: Meta refresh for live scoreboard updates

### Security Considerations

- **Prepared Statements**: Prevents SQL injection attacks
- **Input Sanitization**: HTML escaping prevents XSS attacks
- **Data Validation**: Server-side validation of all inputs

## Assumptions

The following assumptions were made during development:

- **No Authentication Required**: As per project requirements, login functionality was not implemented
- **Single Event Scoring**: Application assumes one active scoring event at a time
- **Numeric Scoring Only**: Scores are limited to integers between 0-100
- **Local Development**: Optimized for local LAMP stack deployment
- **Sample Data Included**: Pre-populated data available for immediate testing
- **Auto-refresh Acceptable**: 30-second meta refresh is suitable for real-time updates

## Future Enhancements

Given more time, the following features would be valuable additions:

### Authentication & Security

- [ ] User Authentication: Login system for judges and admins
- [ ] Role-Based Access Control: Different permission levels
- [ ] Session Management: Secure session handling
- [ ] Password Hashing: Bcrypt for secure password storage

### Enhanced Functionality

- [ ] Real-time Updates: WebSocket implementation for instant scoreboard updates
- [ ] Score History: Track scoring timeline and changes
- [ ] Event Management: Multiple concurrent scoring events
- [ ] Advanced Analytics: Statistical analysis and reporting
- [ ] Export Features: CSV/PDF export of results

### User Experience

- [ ] AJAX Interactions: No-refresh form submissions
- [ ] Mobile Optimization: Dedicated mobile interface
- [ ] Notifications: Real-time scoring notifications
- [ ] Dark Mode: Theme switching capability
- [ ] Drag & Drop: Enhanced admin interface for bulk operations

### Performance & Scalability

- [ ] Caching Layer: Redis for improved performance
- [ ] Database Optimization: Indexing and query optimization
- [ ] Load Balancing: Multi-server deployment ready
- [ ] API Development: REST API for third-party integrations

## Troubleshooting

### Common Issues

#### Database Connection Failed

```bash
# Verify MySQL service is running
sudo systemctl status mysql

# Restart MySQL if needed
sudo systemctl restart mysql
```

- Check database credentials in `db.php`
- Ensure database `scoring_app` exists

#### Permission Denied

```bash
# Set proper file permissions
sudo chmod -R 755 /var/www/html/scoring-app
sudo chown -R www-data:www-data /var/www/html/scoring-app

# Check Apache status
sudo systemctl status apache2
```

#### PHP Extensions Missing

```bash
# Install required extensions
sudo apt install php8.1-mysql php8.1-pdo

# Restart Apache
sudo systemctl restart apache2
```

#### Page Not Found (404)

- Ensure Apache is running: `sudo systemctl start apache2`
- Check if mod_rewrite is enabled: `sudo a2enmod rewrite`
- Verify project is in `/var/www/html/scoring-app/`

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is open source and available under the [MIT License](LICENSE).

---

**Built with ❤️ using the LAMP Stack**

For issues or questions regarding this project, please [create an issue](https://github.com/PowellHabwe/scoring-system/issues) on GitHub.
