<?php 

class Database
{
    public function connect()
    {
        return "Database connected";
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
    public $userrepo;

    public function __construct(UserRepository $userrepo)
    {
        $this->userrepo = $userrepo;
    }
}

class UserController
{
    public $userservice;

    public function __construct(UserService $userService)
    {
        $this->userservice = $userService;
    }

    public function index()
    {
        return $this->userservice->userrepo->database->connect();
    }
}

class Container
{
    public function make($class)
    {
        // 1. Get information about the class we want to build
        $reflector = new ReflectionClass($class);

        // 2. Check if the class even has a constructor method
        $constructor = $reflector->getConstructor();

        // If there is no constructor, we can just instantiate it directly!
        if (is_null($constructor)) {
            return new $class();
        }

        // 3. Get the parameters (dependencies) required by the constructor
        $parameters = $constructor->getParameters();
        $dependencies = [];

        // 4. Loop through each parameter and recursively build it using this container
        foreach ($parameters as $parameter) {
            // Get the type-hinted class name (e.g., "UserRepository")
            $type = $parameter->getType();
            
            if ($type && !$type->isBuiltin()) {
                // Recursively call make() to build the dependency
                $dependencies[] = $this->make($type->getName());
            } else {
                throw new Exception("Cannot resolve a primitive dependency: " . $parameter->getName());
            }
        }

        // 5. Create the class instance, passing in all the resolved dependencies
        return $reflector->newInstanceArgs($dependencies);
    }
}

// object
$db = new Database();
$userrepository = new UserRepository($db);
$user = new UserService($userrepository);
// $controller = new UserController($user);
// echo $controller->index();
$container = new Container();
$controller = $container->make(UserController::class);
echo $controller->index();