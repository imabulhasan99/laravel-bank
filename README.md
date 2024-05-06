
## Laravel Bank Management System
<h2>Installation Guide</h2>
<ol>
        <li>Clone the repository from GitHub. <code>git clone https://github.com/imabulhasan99/laravel-bank.git</code></li>
        <li>Install Composer dependencies by running <code>composer install</code>.</li>
        <li>Copy .env from .env.example <code>cp .env.example .env</code>.</li>
        <li>Create a new database and configure the database connection in the <code>.env</code> file.</li>
        <li>Run migrations and seeders with <code>php artisan migrate</code>.</li>
        <li>Generate an application key with <code>php artisan key:generate</code>.</li>
        <li>Start the development server with <code>php artisan serve</code>.</li>
        <li>Access the application in your web browser.</li>
    </ol>
<h2>Routes</h2>
    <ul>
        <li>POST /users: Create a new user with the provided name and account type.</li>
        <li>POST /login: Login user with the email and password.</li>
        <li>GET / Show all the transactions and current balance. It is your main dashboard</li>
        <li>GET /deposit: Show all the deposited transactions.</li>
        <li>POST /deposit: Accept the user ID and amount, and update the user's balance by adding the deposited amount.</li>
        <li>GET /withdrawal Show all the withdrawal transactions.</li>
        <li>POST /withdrawal Accept the user ID and amount, and update the user's balance by deducting the withdrawn amount.</li>
    </ul>
    <h2>Features</h2>
    <ul>
        <li>User management: Create users and handle logins securely.</li>
        <li>Transaction handling: Deposit and withdraw funds with appropriate fees and conditions.</li>
        <li>Each Friday withdrawal is free of charge.</li>
        <li>The first 1K withdrawal per transaction is free, and the remaining amount will be charged.</li>
        <li>The first 5K withdrawal each month is free.</li>
        <li>Decrease the withdrawal fee to 0.015% for Business accounts after a total withdrawal of 50K.</li>
        <li>Account type differentiation: Individual and Business accounts have different withdrawal rates and conditions.</li>
        <li>Secure transactions: Use database transactions to ensure data integrity.</li>
        <li>Error handling: Handle insufficient balance and other errors gracefully.</li>
    </ul>

