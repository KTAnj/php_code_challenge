# php_code_challenge

I went through the requirement, and completed the assignment.

<b>Framework: Laravel 7.29</b>

I’ve followed the PSR-2 style guide and used Visual Studio Code IDE with phppc extension to check coding standards.

In this assignment I’ve implemented a post API to save betting details as mentioned in the requirement. Then I’ve added route to /routes/api.php. The first part includes request validation as well. I wrote FormRequest to validate form request data and write a custom rule to validate selections.

In the second part , I followed database structure. According to the requirement I had to add some additional columns(which are highlighted) to some tables. I’ve added migrations for them.

<b>bet:</b>
- id
- <b>player_id</b> // this will used to validate player have previous action
- stake_amount
- <b>ended (boolean)</b> // this will help to check that be is ended
- created_at

<b>balance_transaction:</b>
- id 
- <b>bet_id</b>   // this will help to track the transaction with the bet
- player_id 
- amount 
- amount_before

Then I’ve implemented business logic for the requirement with BetController and store function.

## How to set up

First, you need to unzip the project folder or clone project from git.

I wrote a script file (run.sh) to run and set up the project.  I’ve configured three docker containers for mysql , nginx and backend. all project setup details are in the run.sh file.  Docker needs to install to run this process.
You can run the setup file

`./run.sh build `

After all services started, you can check http://localhost:9088

If you want to set up the project without docker, then you need to set up  the environment as you can run laravel [7.x version] project. after that open the terminal on the project folder
```
cd back-end
composer.phar install
```
create .env file by copy the .env.example file and update the database config details

next run 

`php artisan migrate --force`

after that you can easy deploy the server  by running

`php artisan serve`

start a development server at http://localhost:8000:


## Testing the API

I’ve created an api document in postman and I published it, click on the link below to check that document.
[Link to view API document](https://dark-crater-5703.postman.co/collections/8491210-7c9bb9bf-c573-49bb-aadd-bc06cde728cd/publish?workspace=2805aa49-52cc-4b8d-8036-6a1911927aec)
