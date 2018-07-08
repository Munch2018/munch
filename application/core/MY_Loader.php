<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* CI 2.1.0
SKIN 폴더 위치 변경으로 _ci_view_paths 수정
*/
class MY_Loader extends CI_Loader {
    function __construct() {
        parent::__construct();
        $this->_ci_view_paths = array(SKIN_PATH => TRUE);
    }

    function view($view, $vars = array(), $return = FALSE) {
        return $this->_ci_load(array('_ci_view' => $view.'.html', '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
    }

    public function database($params = '', $return = FALSE, $active_record = NULL)
    {
        // Grab the super object
        $CI =& get_instance();

        // Do we even need to load the database class?
        if (class_exists('CI_DB') AND $return == FALSE AND $active_record == NULL AND isset($CI->db) AND is_object($CI->db))
        {
            return FALSE;
        }

        require_once(BASEPATH.'database/DB.php');

        if((empty($params) || $params === 'default') && $CI->config->item('default_database')){
            $params = $CI->config->item('default_database');
        }

        if($params !== '' && $params !== 'default' && is_string($params)){
            $CI->load->library('database/Db_conn');
            $params_decode = $CI->db_conn->get_info($params);
            if(!empty($params_decode['hostname']))
                $params = $params_decode;
        }
        $db = DB($params, $active_record);

        // Load extended DB driver
        $custom_db_driver = config_item('subclass_prefix').'DB_'.$db->dbdriver.'_driver';
        $custom_db_driver_file = APPPATH.'core/'.$custom_db_driver.'.php';

        if (file_exists($custom_db_driver_file))
        {
            require_once($custom_db_driver_file);

            $db = new $custom_db_driver(get_object_vars($db));
        }

        if(isset($params['autoinit']) && $params['autoinit'] !== TRUE)
            $db->initialize();
        if(isset($params['char_set']) && $params['char_set'] !== 'utf8')
            $db->query("SET CHARACTER SET utf8");

        // Return DB instance
        if ($return === TRUE)
        {
            return $db;
        }

        // Initialize the db variable. Needed to prevent reference errors with some configurations
        $CI->db = '';
        $CI->db =& $db;
    }

    // 16.04.30 SLAVE DB 분기 처리
    public function use_slavedb($params = array(), $return = FALSE, $active_record = NULL)
    {
        // Grab the super object
        $CI =& get_instance();
        $CI->load->library('database/Db_conn');

        // Do we even need to load the database class?
        if (class_exists('CI_DB') AND $return == FALSE AND $active_record == NULL AND isset($CI->db) AND is_object($CI->db))
        {
            return FALSE;
        }

        require_once(BASEPATH.'database/DB.php');

        // Is the config file in the environment folder?
        if ( ! defined('ENVIRONMENT') OR ! file_exists($file_path = APPPATH.'config/'.ENVIRONMENT.'/database.php'))
        {
            if ( ! file_exists($file_path = APPPATH.'config/database.php'))
            {
                show_error('The configuration file database.php does not exist.');
            }
        }

        include($file_path);
        $params = $db[$params];
        $key_count = count($params);

        if ( $key_count > 0 )
        {
            shuffle($params);
            $params_array = $params;
            $is_connect = FALSE;
            foreach ($params_array as $row)
            {
                $db_info = $row;
                if((empty($db_info) || $db_info === 'default') && $CI->config->item('default_database')){
                    $db_info = $CI->config->item('default_database');
                }
                if($db_info !== '' && $db_info !== 'default' && is_string($db_info)){
                    $db_info_decode = $CI->db_conn->get_info($db_info);
                    if(!empty($db_info_decode['hostname']))
                        $db_info = $db_info_decode;
                }

                $db_check = DB($db_info, $active_record);
                if ( $db_check->db_connect() == TRUE )
                {
                    $params = $db_info;
                    $is_connect = TRUE;
                    //echo "<!--{$db_check->hostname} connection success -->";
                    break;
                }
                else	//  디버깅용
                {
                    if ($db_check->db_debug)
                    {
                        //echo "<!-- {$db_check->hostname} connection error -->";
                    }
                }
            }
            // 연결된 DB가 아무것도 없을때.
            if ( $is_connect  == FALSE && $db_check->db_debug )
            {
                echo "<p> All Slave DB  can not connect!!";
                $db_check->display_error('db_unable_to_connect');
            }
        }
        else
        {
            show_error('SLave database key array not exists! Do setting in the database config file.');
        }

        $db = DB($params, $active_record);

        // Load extended DB driver
        $custom_db_driver = config_item('subclass_prefix').'DB_'.$db->dbdriver.'_driver';
        $custom_db_driver_file = APPPATH.'core/'.$custom_db_driver.'.php';

        if (file_exists($custom_db_driver_file))
        {
            require_once($custom_db_driver_file);

            $db = new $custom_db_driver(get_object_vars($db));
        }

        if(isset($params['autoinit']) && $params['autoinit'] !== TRUE)
            $db->initialize();
        if(isset($params['char_set']) && $params['char_set'] !== 'utf8')
            $db->query("SET CHARACTER SET utf8");

        // Return DB instance
        if ($return === TRUE)
        {
            return $db;
        }

        // Initialize the db variable. Needed to prevent reference errors with some configurations
        $CI->db = '';
        $CI->db =& $db;
    }

    protected function _ci_object_to_array($object)
    {
        return is_object($object) ? get_object_vars($object) : $object;
    }
}
?>