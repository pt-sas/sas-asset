<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Mail;
use Config\Services;

class Mail extends BaseController
{
    protected $email;

    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Mail($this->request);
        $this->entity = new \App\Entities\Brand();
        $this->email = Services::email();
    }

    public function index()
    {
        return $this->template->render('backend/configuration/mail/form_mail');
    }

    public function showAll()
    {
        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->findAll(1);

                $result = [
                    'header'   => $this->field->store($this->model->table, $list)
                ];

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function create()
    {
        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            try {
                $this->entity->fill($post);

                if (!$this->validation->run($post, 'mail')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $response = $this->save();
                    $response[0]['insert_id'] = $this->model->getInsertID();
                }
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);

            return json_encode($response);
        }
    }

    public function createTestEmail()
    {
        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            try {
                if (!$this->validation->run($post, 'mail')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $row = $this->model->first();

                    if ($row) {
                        $content = 'Aset EMail Test';

                        if ($row->getIsActive() === $this->access->active()) {
                            $email = $this->initializeEmail();

                            $email->setFrom($row->getSmtpUser(), $row->getSmtpUser());
                            $email->setTo($row->getRequestEmail());
                            $email->setSubject($content);
                            $email->setMessage($content);

                            if ($email->send()) {
                                $response = message('success', true, 'Process completed successfully');
                            } else {
                                $response = message('error', true, $email->printDebugger(['header']));
                            }
                        } else {
                            $response = message('error', true, 'Please Active data first');
                        }
                    } else {
                        $response = message('error', true, 'Please Insert data first');
                    }
                }
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function initializeEmail()
    {
        $row = $this->model->first();

        $config["protocol"] = $row->getProtocol();
        $config["SMTPHost"] = $row->getSmtpHost();
        $config["SMTPUser"] = $row->getSmtpUser();
        $config["SMTPPass"] = $row->getSmtpPassword();
        $config["SMTPPort"] = $row->getSmtpPort();
        $config["SMTPCrypto"] = $row->getSmtpCrypto();

        return $this->email->initialize($config);
    }
}
