# Simple PHP Router

## Usage

```
$http = new Http();
$router = new Router($http);

$rounter->route('/', function() {
    echo 'HELLO WORLD';
});
```

## Simple PHP template render
```
$templatePath = 'app\view';
$view = new View($templatePath);

$http = new Http();
$router = new Router($http);

$rounter->route('/', function() use ($view){
    $view->render('foo.php', ['name' => 'Hello']);
});
```
