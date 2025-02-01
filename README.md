<p align="center"><h1>Aplikasi SIMPIN</h1></p>

## Requirement Aplikasi

-   PHP > 8
-   Node > 18
-   NPM > 10
-   Composer Latest Version / 2.8.5

## Running Dev Aplikasi

1. Jalankan script terminal :
    ```
    npm install
    ```
    ```
    npm install npm-run-all --save-dev
    ```
    ```
    composer install
    ```
2. copy .env.example menjadi .env

3. buka file .env, lalu ubah point2 ini :

    ```
    APP_URL=http://localhost:8000
    ```

    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=simpin
    DB_USERNAME=root
    DB_PASSWORD=root
    ```

    Point nomor 2 untuk DB_DATABASE, DB_USERNAME dan DB_PASSWORD disesuaikan dengan settingan mySql local anda

4. Jalankan script terminal :

    ```
    php artisan key:generate
    ```

    ```
    php artisan migrate
    ```

    ```
    php artisan db:seed
    ```

    Note : Jika ingin refresh data (mereset ulang data, biasanya karena bikin tabel baru atau membuat seed baru), lakukan ini :

    ```
    php artisan migrate:fresh
    ```

    ```
    php artisan db:seed
    ```

5. Modifikasi file package.json :

    ```
    "scripts": {
        "dev": "npm-run-all --parallel serve artisan",
        "serve": "vite",
        "artisan": "php artisan serve"
    }
    ```

6. Jalankan Perintah :
    ```
    npm run dev
    ```
