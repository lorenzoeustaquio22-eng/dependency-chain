<?php 

class Database
{
    public function connection()
    {
        return "Database connection";
    }
}

class UserRepository
{
    public $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }
}

class UserService
{
    public $userrepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userrepository = $userRepository;
    }
}

class UserController
{
    public $userservice;

    public function __construct(UserService $userservice)
    {
        $this->userservice = $userservice;
    }

    public function index()
    {
        return $this->userservice->userrepository->database->connection();
    }
}

class Container
{
    public function make($class)
    {
        if($class === UserController::class) {
            return new UserController(
                new UserService(
                    new UserRepository(
                        new Database()
                    )
                )
            );
        }
        return $class;
    }
}


// $db = new Database();
// $userrepository = new UserRepository($db);
// $userservice = new UserService($userrepository);
// $controller = new UserController($userservice);

$container = new Container();
$controller = $container->make(UserController::class);
echo $controller->index();
