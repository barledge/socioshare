<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class ClientForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text', ['rules' => 'required|min:2'])
            ->add('email', 'text', ['rules' => 'required|email|unique:users', 'attr' => ['autocomplete' => 'off']])
            ->add('password', 'password', ['rules' => 'required|min:6', 'attr' => ['autocomplete' => 'off']])
            ->add('facebook_app_key', 'text', ['help_block' => ['text' => 'Enter this to use your own brand name']])
            ->add('facebook_app_secret', 'text', ['help_block' => ['text' => 'Must be used in combination with the APP KEY']])
            ->add('submit', 'submit', ['label' => 'Save client','attr' => ['class' => 'btn btn-block btn-primary']]);
    }
}
