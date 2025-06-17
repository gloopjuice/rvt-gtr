# RVTGTR
uzraksti aprakstu +-

**Kas tika izmantots:**
Mājaslapai

    Laravel
    Node.js
    Vue.js
    MySQL

Priekš plugina 

 C++ ar JUCE framework


**lai mājaslapu palaistu:**
backenda setups
```
cd laravelBackend
cp .env.example .env
composer install
php artisan key:generate
php artisan passport:keys
php artisan passport:client --personal
php artisan storage:link
php artisan migrate --seed
```
env faila config:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rvtgtr
DB_USERNAME=root
DB_PASSWORD=
```
    frontend setup
    cd frontend        
    npm install

**palaišana**
    
    cd frontend    
    npm run dev
    
    cd laravelBackend    
    php artisan serve

>>>>>>> origin/main
