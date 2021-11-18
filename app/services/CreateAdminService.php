<?php
namespace App\services;
use App\Models\Admin;

class CreateAdminService{
    public $firstname;
    public $lastname;
    public $email;
    public $pin;
    public $authid;
    public $admin;

    public function __construct($firstname,$lastname,$email,$pin)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->pin = $pin;
    }

    public function CreateAdmin()
    {
        $this->authid = "AUTH".date("Ymdhis");
        $insert_fields = [
            'authid' => $this->authid,
            'firstname'=>$this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'password' => bcrypt($this->pin),
            'pin' => $this->pin
        ];
        $field = $insert_fields;
        $this->admin = Admin::create($field);
        return $this;
    }
}
