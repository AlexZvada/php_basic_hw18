<?php
const APP_DIR = __DIR__ . '/';
require_once APP_DIR . 'database/config.php';
require_once APP_DIR . 'database/Connector.php';
require_once APP_DIR . 'interfaces/SqlQueryBuilder.php';
require_once APP_DIR . 'database/SqlBuilder.php';
require_once APP_DIR . 'database/Model/Model.php';
require_once APP_DIR . 'database/Model/Appointment.php';
require_once APP_DIR . 'database/Model/Bed.php';
require_once APP_DIR . 'database/Model/Bath.php';
require_once APP_DIR . 'database/Model/Floor.php';
require_once APP_DIR . 'database/Model/Type.php';
require_once APP_DIR . 'database/Model/Realestate.php';

try {
    //create tables
    Bed::up();
    Bath::up();
    Floor::up();
    Appointment::up();
    Type::up();
    Realestate::up();

    //delete tables
    Realestate::down();
    Bed::down();
    Bath::down();
    Floor::down();
    Appointment::down();
    Type::down();

    //insert data in to tables, create method accept array of params
    for ($i = 1; $i <=8; $i++){
        Bed::create(["value"=>$i]);
    }
    Bed::create(["value"=>0]);

    for ($i = 1; $i <=18; $i++){
       Floor::create(["value"=>$i]);
    }
    Floor::create(["value"=>0]);

    for ($i = 1; $i <=6; $i++){
        Bath::create(["value"=>$i]);
    }
    Bath::create(["value"=>0]);
    $appointments = ['for rent', 'for sale', 'commercial'];
    foreach ($appointments as $appointment) {
        Appointment::create(['value' => $appointment]);
    }
    $types = ['flat', 'house', 'room', 'villa', 'townhouse'];
    foreach ($types as $type) {
        Type::create(['value' => $type]);
    }

    Realestate::create(
        [
            "address"=>'ADDRESS str.',
            "city" => 'London',
            "country" => 'England',
            "price" => 350000,
            "beds_id" => 2,
            "bath_id" => 4,
            "floor_id" => 5,
            "type_id" => 2,
            "appointment_id" => 1,
            "description" => 'some new description',
            ]
    );

    //get data from tables
    $property = Realestate::where('id', 10, ['price', 'address', "id"], ">");
    foreach ($property as $realty){
        echo "<pre>";
        print_r($realty);
        echo "<pre>";
    }

    $all = Realestate::all(['price, address, id']);
    foreach ($all as $realty){
        echo "<pre>";
        print_r($realty);
        echo "<pre>";
    }

   //update data, update method accept array of params
    Realestate::update([
        'price'=>3200,
        'address'=>'some new address',
        'id'=>7,
    ]);


    //delete data
    Realestate::delete(6);
} catch (PDOException|Exception $exception) {
    echo $exception->getMessage();
}