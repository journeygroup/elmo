<?php

namespace Journey;

class Elmo
{

    // The directory containing routes
    private static $routeDirectory = 'Routes';

    // The directory containing routes
    private static $layoutsDirectory = 'Layouts';

    // The route being accessed
    private $route;

    // The active layout
    private $layout;

    // The final rendered output
    public $output;
    

    /**
     * Initialize elmo
     */
    public function __construct($route = false)
    {
        chdir(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR);
        $route = ($route) ? str_replace(".php", "", $route):preg_replace(["/(index\.php)$/", "/\/$/"], "", $_GET['q']);

        if (!$route) {
            $route = 'index';
        }

        if (($file = file_exists(self::$routeDirectory . DIRECTORY_SEPARATOR . $route . '.php')) ||
            ($dir = is_dir(self::$routeDirectory . DIRECTORY_SEPARATOR . $route))) {
 
            $this->route =  self::$routeDirectory . DIRECTORY_SEPARATOR . $route . (($file) ? '.php':'/index.php');
        }
    
        // Unable to locate the rotue
        if (!file_exists($this->route)) {
            header("HTTP/1.0 404 Not Found");
            throw new \Exception('Unable to locate the route: ' . $this->route);
        }
    }


    /**
     * Set a given layout
     * @param string $layout  the name of the layout to set
     */
    public function setLayout($layout = 'master')
    {
        if (file_exists(self::$layoutsDirectory . DIRECTORY_SEPARATOR . $layout . '.php')) {
            $this->layout = self::$layoutsDirectory . DIRECTORY_SEPARATOR . $layout . '.php';
            // If we've already created output, re-render
            return $this;
        } else {
            throw new \Exception('Unable to load the layout: ' . $this->layoutsDirectory . '/' . $layout . '.php');
        }
    }


    /**
     * Render the page to string
     * @return string   html output
     */
    public function render()
    {
        if (!$this->layout) {
            $this->setLayout();
        }
        $initialLayout = $this->layout;

        ob_start();
        include $this->layout;
        $this->output = ob_get_clean();

        return ($initialLayout != $this->layout) ? $this->render():$this;
    }


    /**
     * Print the output to screen
     * @return none
     */
    public function out()
    {
        echo $this->output;
        return $this;
    }



    /**
     * Save this particular elmo instance to disk
     * @param string $location where the elmo output should be saved
     * @return none
     */
    public function save($location)
    {
        if (!is_dir(dirname($location))) {
            // Make necessary directories, recursively if necessary.
            mkdir(dirname($location), 0777, true);
        }
        file_put_contents($location, $this->output);
        return $this;
    }



    /**
     * Bundle the entire project as static files
     * @return none
     */
    public static function bundle()
    {
        global $argv;

        $directory = (isset($argv[1])) ? $argv[1]:'static-bundle';
        echo "Elmo is bundling static files to: " . $directory . "\n\n";


        // Finally take all our files and render them out
        $routes = static::seek(static::$routeDirectory . DIRECTORY_SEPARATOR . '*.php');
        $routes = preg_replace("/^" . static::$routeDirectory . "\\" . DIRECTORY_SEPARATOR . "/", "", $routes);

        // if we actually have output, create the directory
        if (count($routes)) {
            exec("cp -r Public " . $directory);
            unlink($directory . '/index.php');
            unlink($directory . '/.htaccess');
        }

        foreach ($routes as $route) {
            if (preg_match("/index\.php$/", $route)) {
                $location = $directory . DIRECTORY_SEPARATOR . $route;
            } else {
                $location = $directory . DIRECTORY_SEPARATOR . substr($route, 0, -4) . DIRECTORY_SEPARATOR . "index.php";
            }

            // Render the output and save it
            $location = str_replace(".php", ".html", $location);
            $elmo = new Elmo($route);
            $elmo->render()->save($location);

            // Let the CLI user know whats going on
            echo $route . " -> " . $location . "\n";
        }
    }



    /**
     * Find all instances of a given pattern (glob). Thanks to Mike on:
     * http://php.net/manual/en/function.glob.php#106595
     * @param   string $pattern pattern to search for
     * @param   int    $flag    standard flags accepted by glob
     * @return  array  $files
     */
    public static function seek($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, static::seek($dir . DIRECTORY_SEPARATOR . basename($pattern), $flags));
        }
        return $files;
    }
}
