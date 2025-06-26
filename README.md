# BloodConnect - Blood Donation Management System

#### Video Demo: https://youtu.be/oUyJoBnwU4A

#### Description:

BloodConnect is a comprehensive web-based blood donation management system designed to connect blood donors with those in need. The platform facilitates the entire blood donation process, from donor registration to appointment scheduling and communication between donors and requesters.

## Project Overview

BloodConnect addresses the critical challenge of blood shortages in healthcare settings by creating an efficient platform that connects potential donors with patients in need. The system is built with a focus on the Bangladesh context but can be adapted for use in any region.

## Key Features

### For Donors
- **Donor Registration**: Users can register as blood donors by providing their blood type, medical history, and other relevant information.
- **Availability Management**: Donors can toggle their availability status to indicate when they are ready to donate.
- **Location Sharing**: Donors can share their location to be matched with nearby donation requests.
- **Donation History**: Donors can view their complete donation history.
- **Appointment Management**: Schedule, track, and manage donation appointments.

### For Blood Requesters
- **Request Creation**: Users can create blood donation requests specifying blood type, quantity, urgency level, and contact information.
- **Hospital Selection**: Requests can be associated with specific hospitals from the system database.
- **Request Tracking**: Requesters can track the status of their requests.

### Matching System
- **Smart Matching Algorithm**: Automatically matches donation requests with compatible donors based on blood type, location proximity, and availability.
- **Scoring System**: Prioritizes matches based on various factors including urgency and location.

### Communication
- **Messaging System**: Built-in messaging between donors and requesters.
- **Notification System**: Comprehensive notifications via in-app alerts, email, and SMS (simulated).
- **Appointment Coordination**: Facilitates scheduling and confirmation of donation appointments.

### User Management
- **User Profiles**: Comprehensive user profiles for both donors and requesters.
- **Authentication System**: Secure login and registration system.
- **Role-Based Access**: Different interfaces and permissions for donors, requesters, and administrators.

### Administrative Features
- **Hospital Management**: Admin interface for managing hospital information.
- **Blood Inventory Tracking**: System for tracking available blood units.
- **User Management**: Tools for administrators to manage users.

## Technical Implementation

- **Backend**: PHP with PDO for database operations
- **Frontend**: HTML, CSS (Tailwind CSS), JavaScript
- **Database**: MySQL
- **Maps Integration**: Leaflet.js for location-based features
- **Responsive Design**: Mobile-friendly interface using Tailwind CSS

## Future Enhancements

- Mobile application development
- Advanced analytics for blood donation trends
- Integration with hospital management systems
- Blood transportation logistics
- Expanded reminder system for regular donations

## Installation and Setup

1. Clone the repository
2. Import the database schema from `database_schema.sql`
3. Configure database connection in `config/database.php`
4. Set up a web server with PHP support
5. Access the application through the web server

## Demo Data

The system includes a demo data generator (`setup/populate_demo_data.php`) that creates realistic test data with a Bangladesh context, including users, hospitals, donation requests, and more.