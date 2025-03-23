Welcome to StreamPlus Registration System!

We've built a modern, user-friendly registration system that makes it easy for users to sign up for our service. The system is designed to be secure, intuitive, and hassle-free.

What Makes Our Registration System Special?

Our registration process is broken down into three simple steps:

1. Personal Information
   - Full name
   - Email address (with duplicate checking)
   - Phone number with country code
   - Subscription type selection (Free or Premium)

2. Address Details
   - Street address
   - Optional apartment/suite information
   - City
   - Postal code
   - State/Province
   - Country selection

3. Payment Information (for Premium subscriptions only)
   - Secure credit card input
   - Expiration date
   - CVV

Technical Details

The system is built with:
- PHP 8.1+
- Symfony 6.4
- Bootstrap 5 for beautiful styling
- Modern JavaScript for smooth interactions

Getting Started

1. Install Symfony CLI tool:
   curl -sS https://get.symfony.com/cli/installer | bash

2. Install Symfony SSL certificate (for HTTPS support):
   symfony server:ca:install

3. Clone the repository:
   git clone <your-repository-url>
   cd <project-directory>

4. Install PHP dependencies using Composer:
   composer install

5. Copy environment configuration file:
   cp .env .env.local

6. Update .env.local with your local database configuration:
   DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"

7. Generate Symfony application secret keys:
   php bin/console secrets:generate-keys

8. Create the database:
   php bin/console doctrine:database:create

9. Run database migrations:
   php bin/console doctrine:migrations:migrate

10. Install frontend dependencies:
    npm install

11. Compile frontend assets:
    npm run dev

12. Install Symfony UX packages:
    composer require symfony/ux-turbo symfony/stimulus-bundle
    npm install @symfony/stimulus

13. Clear cache:
    php bin/console cache:clear

14. Validate doctrine mappings:
    php bin/console doctrine:schema:validate

15. Start the development server:
    symfony server:start
    Or alternatively:
    php -S 127.0.0.1:8000 -t public