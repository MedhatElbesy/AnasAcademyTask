# Laravel E-commerce Project

This project is a Laravel-based e-commerce application that allows users to manage products, make orders, and process payments using Stripe.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Testing](#testing)


## Prerequisites

Before you begin, ensure you have met the following requirements:
- PHP >= 8.0
- Composer
- MySQL or SQLite database
- Node.js and npm (for frontend assets)

## Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/MedhatElbesy/AnasAcademyTask.git
   cd your-repository

2. **Install PHP dependencies:**
    - composer install

2. **Install JavaScript dependencies:**

    - npm install

3. **Copy the environment file:**

    
    - cp .env.example .env

4. **Generate the application key:**

    - php artisan key:generate


## Configuration

1. **Set up your database:**

    - DB_CONNECTION=mysql
    - DB_HOST=127.0.0.1
    - DB_PORT=3306
    - DB_DATABASE=your_database
    - DB_USERNAME=your_username
    - DB_PASSWORD=your_password


2. **Set up Stripe credentials:**

    - STRIPE_KEY=your_stripe_key
    - STRIPE_SECRET=your_stripe_secret
    - STRIPE_WEBHOOK_SECRET = your STRIPE_WEBHOOK_SECRET


3. **Migrate the database and seeder:**

    - php artisan migrate --seed



    ## Running the Application

    - php artisan serve
    - npm run dev


