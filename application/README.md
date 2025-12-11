# BLD

1. composer update

2. php artisan migrate:fresh   ---- ONLY FOR FIRST TIME     

3. php artisan app:extract-modules-from-routes

4. php artisan db:seed    

5. php artisan db:seed --class=RolesSeeder


# Setup Google Cloud Vision API
# Creatre a project for Vision API
# Enable Google CLoud vision API services with a project
# Enable Google Billing account and connect it into project


composer require google/cloud-vision
composer require spatie/pdf-to-image
composer require phpoffice/phpword

# OR 

composer require google/cloud-vision
composer require phpoffice/phpword
composer require smalot/pdfparser


# Increase PHP.ini onfiguration 

upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300


# Install

brew install ghostscript
brew install imagemagick
pecl install imagick
