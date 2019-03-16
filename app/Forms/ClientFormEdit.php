<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class ClientFormEdit extends Form
{
    public function buildForm()
    {
      $this
          ->add('name', 'text', ['attr' => ['disabled' => 'disabled']])
          ->add('email', 'text', ['attr' => ['disabled' => 'disabled', 'autocomplete' => 'off']])
          ->add('password', 'password', ['rules' => 'nullable|min:6', 'help_block' => ['text' => 'Only enter if you want to change the password'], 'attr' => ['autocomplete' => 'off']])
          ->add('facebook_app_key', 'text', ['help_block' => ['text' => 'Enter this to use your own brand name']])
          ->add('facebook_app_secret', 'text', ['help_block' => ['text' => 'Must be used in combination with the APP KEY']])
          ->add('submit', 'submit', ['label' => 'Save client','attr' => ['class' => 'btn btn-block btn-primary']]);
    }
}
