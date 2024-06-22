# Description

The technical challenge is described as follows:
Develop a simple Kanban board application using Laravel 10 and Livewire, featuring user authentication and basic project management capabilities.

## Application Link

http://206.81.14.66/

## How to test it locally

This project uses Laravel Sail for the local development environment. Please check the official documentation for running it locally (https://laravel.com/docs/11.x/sail).

## Database diagram
![image](https://github.com/CaioMatInt/kanban_board_challenge/assets/40992883/3774aa22-e585-41af-b604-9dc5345807db)

### Technologies used
![image description](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)![image description](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)![MySQL](https://img.shields.io/badge/mysql-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)![SQLite](https://img.shields.io/badge/sqlite-%2307405e.svg?style=for-the-badge&logo=sqlite&logoColor=white)![image](https://github.com/CaioMatInt/kanban_board_challenge/assets/40992883/abc8770a-cd58-44a8-9c9f-b3617888b061)![image](https://github.com/CaioMatInt/kanban_board_challenge/assets/40992883/3d69d44d-73ca-48a8-8611-df67e8e4fe5f)

## Tests 
<img src="https://github.com/CaioMatInt/payment_gateways_integration/assets/40992883/9be42c02-f192-4daf-809a-90a35aca2b77" width="130" height="70">

The tests are being developed with the [PEST framework](https://pestphp.com/) for Laravel. You can run them by executing "php artisan test" in the root folder. The tests can be found in the "app/tests" directory.

Note: The code is not 100% covered. There are only some tests for the Service classes and some tests for the Boards Livewire component. Since I had never used Livewire before this challenge, I didn't use TDD in this case, as I had to manually learn and understand how Livewire works.

I am developing an API with test coverage if you are interested in reviewing it: https://github.com/CaioMatInt/laramultipay_payment_gateways_integration

![image](https://github.com/CaioMatInt/kanban_board_challenge/assets/40992883/838202e1-7913-4c43-9086-3918875d2c3a)

## How to test it

Open http://206.81.14.66/ (or localhost if you're testing it locally) and click on login

![image](https://github.com/CaioMatInt/kanban_board_challenge/assets/40992883/6ad668b1-6237-437f-b7c2-ff36c8a982e6)

The form already has a default username and password filled in. Click on login.

![image](https://github.com/CaioMatInt/kanban_board_challenge/assets/40992883/42f266b3-a5fc-43cd-8153-cda993e92dfb)

After logging in, feel free to test all CRUD operations for boards, lists, and tasks.

Some prints:

![Animation](https://github.com/CaioMatInt/kanban_board_challenge/assets/40992883/9a753631-29b7-4260-ab6d-8a23998a5816)

![image](https://github.com/CaioMatInt/kanban_board_challenge/assets/40992883/365e8e36-a974-4939-b0a7-a0a806cccc04)

![image](https://github.com/CaioMatInt/kanban_board_challenge/assets/40992883/d270d84d-7616-499a-befa-82fe3d00d348)

![Animation2](https://github.com/CaioMatInt/kanban_board_challenge/assets/40992883/0a644ba3-d7de-4ba7-9e27-d221cd76bdcb)

![image](https://github.com/CaioMatInt/kanban_board_challenge/assets/40992883/70ab8f0d-868c-4b59-9c90-046316c646c6)

![image](https://github.com/CaioMatInt/kanban_board_challenge/assets/40992883/95cf7650-9058-4bcd-a422-a4ca9a7aecf9)





