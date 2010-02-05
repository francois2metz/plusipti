<?php
/* Copyright (c) 2009, Arnaud Berthomier
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the University of California, Berkeley nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHORS AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHORS AND CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

class TouptiException extends Exception {}

/**
 * Toupti: yet another micro-framework made out of PHP.
 * @package Toupti
 * @author  Arnaud Berthomier <oz@cyprio.net>
 */
class Toupti
{
    /**
     * Application views templates config.
     */
    public $template_path = 'template';

    /**
     * Current config
     */
    public $conf = null;

    /**
     * Parameters from _GET, _POST, and defined routes.
     */
    protected $params = array();

    /**
     * Request info
     */
    public $request = null;

    /**
     * The action we'll want to run...
     */
    public $action = null;

    /**
     * The methods chains to complete the action.
     */
    public $method = null;

    /**
     * Routing setup
     */
    private $route = null;

    /**
     * Internal template path
     */
    private $_template_path = null;

    private static $_instance = null;

    /**
     * Toupti constructor
     */
    private function __construct($conf)
    {

        // So we know where we are...
        /**
         * shouldn't we give this as an optionnal parameter ?
         */
        $this->app_root = dirname(__FILE__) .'/../..';

        $this->conf = $conf;

        $this->setup_request();

        // Read user routes, and set-up internal dispatcher
        $this->setup_route();
    }

    /**
     * get instance of toupti
     * @param $conf
     */
    public static function instance($conf = null)
    {
        if(is_null(self::$_instance))
            self::$_instance = new self($conf);
        return self::$_instance;
    }

    public function get_params()
    {
        return $this->params;
    }


    /**
     * Dispatch browser query to the appropriate action.
     * This our "main" entry point.
     * @return void
     */
    public function run()
    {
        // Find an action for the query, and set params accordingly.
        list($action, $method, $params) = $this->route->find_route();

        // Update ourself
        $this->action = $action;
        $this->method = $method;

        // Merge route params with POST/GET values
        $params = array_merge($params, $_POST, $_GET); // FIXME Possible CSRF attacks here
        $this->params = $params;

        // Dispatch the routed action !
        if (isset($action) && isset($method)) {
            $controller = ucfirst($action)."Controller";
            return $this->call_action($controller, $method, $params);
        } else {
            throw new TouptiException($_SERVER['REQUEST_URI'], 404);
        }

    }

    /**
     * Call a user action:
     *  - First, try to call any otherwised defined filter,
     *  - Then, call the user action.
     *  - Finally, call any post filter that is callable.
     *
     * @param  string   Name of the action to call
     * @param  array    Request parameters
     * @return mixed    User-defined action's return value
     */
    private function call_action($controller_name, $method_name, $params)
    {
        if($controller_name != 'Controller' && class_exists($controller_name, true))
        {
            $controller = new $controller_name();
            if(method_exists($controller, $method_name))
            {
                if($controller->isAuthorized($method_name))
                {
                    return $controller->$method_name();
                }
                else
                {
                    throw new TouptiException('access_not_allowed', 403);
                }
            } else {
                throw new TouptiException('Route error '. $method_name, 404);
            }
        }
        Logs::info('exit call_action without return');
    }

    /**
     * Redirect to another path, and stops un
     * @param  string    $path          Redirects to $path
     * @param  boolean   $we_are_done   Stops PHP if true (defaults to true)
     * @return void
     */
    public function redirect_to($path = '', $we_are_done = true)
    {
        header("Location: $path");
        if ( $we_are_done )
            exit(0);
    }

    private function setup_request()
    {
        // Parsing Request.
        $this->request = new RequestMapper();
    }

    private function setup_route()
    {
        $routes_file = $this->app_root . '/conf/routes.php';
        if (file_exists($routes_file))
        {
            include $routes_file;
        }
        else
        {
            throw new Exception('No route defined. Please create '. $routes_file);
        }
        $this->route = HighwayToHeaven::instance();
        $this->route->setRequest($this->request);
    }
}
