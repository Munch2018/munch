<?php

/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-26
 * Time: 오후 9:37
 */
class Pets extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model('Admin_pet_model', 'model');
        $this->load->service('common_code_service', '', true);

        $this->load->service('admin_service', '', true);
        $this->admin_service->checkAdmin();
    }

    public function index()
    {
        $data['pets'] = $this->model->getList();
        $this->load->view('admin/common/header.html');
        $this->load->view('admin/pets/list.html', $data);
        $this->load->view('admin/common/footer.html');
    }
}