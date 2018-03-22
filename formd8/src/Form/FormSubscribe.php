<?php

namespace Drupal\formd8\form;

use \Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

class FormSubscribe extends FormBase {
    public function getFormId() {
        return 'subscribe_form';
    }

    /**
     *  Form constructor.
     * 
     * @param array $form
     *  An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *  The current state of the form.
     * 
     * @param array
     *  The form structure.
     */

    public function buildForm(array $form, FormStateInterface $form_state) {
        
        $form['first_name']=array(
            '#type' => 'textfield',
            '#title' => $this->t('First Name'),
            '#size' => 60,
            '#maxlength' => 128,
            '#ajax' => [
                'callback' => array($this, 'validateFirstNameAjax'),
                'event' => 'change',
                'progress' => array(
                  'type' => 'throbber',
                  'message' => t('Verifying name...'),
                ),
              ],
              '#suffix' => '<span class="first-name-valid-message"></span>'
        );

        $form['last_name']=array(
            '#type' => 'textfield',
            '#title' => $this->t('Last Name'),
            '#size' => 60,
            '#maxlength' => 128,
            '#ajax' => [
                'callback' => array($this, 'validateLastNameAjax'),
                'event' => 'change',
                'progress' => array(
                  'type' => 'throbber',
                  'message' => t('Verifying name...'),
                ),
              ],
              '#suffix' => '<span class="last-name-valid-message"></span>'
        );
        $form['image']=array(
            '#type' => 'managed_file',
            '#title' => $this->t('Image'),
            // '#upload_validators' => array(
            //     'file_validate_extensions' => array('gif png jpg jpeg'),
            //     'file_validate_size' => array(25600000),
            // ),
            '#upload_location' => 'public://images/',
            '#default_value' => '',
            '#description'   => t('Specify an image(s) to display.'),
            '#states'        => array(
            'visible'      => array(
                ':input[name="image_type"]' => array('value' => t('Upload New Image(s)')),
            ),
        ),
            '#required' => TRUE,
        );
        $form['color'] = [
            '#type' => 'color',
            '#title' => $this->t('Color'),
            '#default_value' => '#ffffff',
            '#description' => 'Pick a color by clicking on the color above',
            ];
        $form['gender'] = [
            '#type' => 'select',
            '#title' => $this->t('Gender'),
            '#options' => [
                'Male' => $this->t('Male'),
                'Female' => $this->t('Female'),
            ],
            '#ajax' => [
                'callback' => array($this, 'validateGenderAjax'),
                'event' => 'change',
                'progress' => array(
                  'type' => 'throbber',
                  'message' => t('Verifying gender...'),
                ),
              ],
              '#suffix' => '<span class="gender-valid-message"></span>'
        ];

        $form['birth_date']=array(
            '#type' => 'date',
            '#title' => $this->t('Birth Date'),
            '#default_value' => array('year'=>2020, 'month'=> 2, 'day'=>15),
            '#required' => FALSE,
            '#ajax' => [
                'callback' => array($this, 'validateDateAjax'),
                'event' => 'change',
                'progress' => array(
                  'type' => 'throbber',
                  'message' => t('Verifying date...'),
                ),
              ],
              '#suffix' => '<span class="date-valid-message"></span>'
        );

        $form['email']=array(
            '#type' => 'email',
            '#title' => $this->t('Email'),
            '#ajax' => [
                'callback' => array($this, 'validateEmailAjax'),
                'event' => 'change',
                'progress' => array(
                  'type' => 'throbber',
                  'message' => t('Verifying email...'),
                ),
              ],
              '#suffix' => '<span class="email-valid-message"></span>'
        );
        $form['submit']=array(
            '#type' => 'submit',
            '#value' => $this->t('save'),
            '#ajax' => [
                'wrapper' => 'my-form-wrapper-id',
            ],
        );
        $form['#prefix'] = '<div id="my-form-wrapper-id">';
        $form['#suffix'] = '</div>';

        return $form;
    }

    /**
     * Form validation handler.
     * 
     * @param array $form
     *  An associative array contaning the structure ofthe form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *  The current state of the form.
     */


    /**
    * Validates that the name field is correct.
    */
    protected function validateFirstName(array &$form, FormStateInterface $form_state) {
        if (is_numeric($form_state->getValue('first_name'))){
            return FALSE;
        }
            return TRUE;
    }
    /**
    * Validates that the last name field is correct.
    */
    protected function validateLastName(array &$form, FormStateInterface $form_state) {
        if (is_numeric($form_state->getValue('last_name'))){
            return FALSE;
        }
            return TRUE;
    }
    /**
    * Validates that the gender field is correct.
    */
    protected function validateGender(array &$form, FormStateInterface $form_state) {
        if ($gender == 'male' || $gender == 'female'){
            return FALSE;
        }
            return TRUE;
    }
    /**
    * Validates that the gender field is correct.
    */
    protected function validateDate(array &$form, FormStateInterface $form_state) {
        if(preg_match("/^(\d{2})-(\d{2})-(\d{4})$/", $birth_date, $matches)) {
            if(checkdate($matches[1], $matches[2], $matches[3])){
                return true;
            }
        }
    }
    /**
    * Validates that the email field is correct.
    */
    protected function validateEmail(array &$form, FormStateInterface $form_state) {
        if (substr($form_state->getValue('email'), -4) !== '.com') {
            return FALSE;
        }
            return TRUE;
    }
    /**
    * Ajax callback to validate the first name field.
    */
    public function validateFirstNameAjax(array &$form, FormStateInterface $form_state) {
        $valid = $this->validateFirstName($form, $form_state);
        $response = new AjaxResponse();
        if ($valid) {
            $css = ['border' => '1px solid green'];
            $message = $this->t('First Name is ok.');
        }
        else {
            $css = ['border' => '1px solid red'];
            $message = $this->t('First Name is not valid.');
        }
        $response->addCommand(new CssCommand('#edit-first-name', $css));
        $response->addCommand(new HtmlCommand('.first-name-valid-message', $message));
        return $response;
    }
    /**
    * Ajax callback to validate the last name field.
    */
    public function validateLastNameAjax(array &$form, FormStateInterface $form_state) {
        $valid = $this->validateLastName($form, $form_state);
        $response = new AjaxResponse();
        if ($valid) {
            $css = ['border' => '1px solid green'];
            $message = $this->t('Last Name is ok.');
        }
        else {
            $css = ['border' => '1px solid red'];
            $message = $this->t('Last Name is not valid.');
        }
        $response->addCommand(new CssCommand('#edit-last-name', $css));
        $response->addCommand(new HtmlCommuse('.last-name-valid-message', $message));
        return $response;
    }
    /**
    * Ajax callback to validate the gender field.
    */
    public function validateGenderAjax(array &$form, FormStateInterface $form_state) {
        $valid = $this->validateGender($form, $form_state);
        $response = new AjaxResponse();
        if ($valid) {
            $css = ['border' => '1px solid green', 'background-color' => '#faffbd'];
        }
        else {
            $css = ['border' => '1px solid red'];
        }
        $response->addCommand(new CssCommand('#edit-gender', $css));
        return $response;
    }
    /**
    * Ajax callback to validate the date field.
    */
    public function validateDateAjax(array &$form, FormStateInterface $form_state) {
        $valid = $this->validateDate($form, $form_state);
        $response = new AjaxResponse();
        if ($valid) {
            $css = ['border' => '1px solid green'];
        }
        else {
            $css = ['border' => '1px solid red'];
        }
        $response->addCommand(new CssCommand('#edit-date', $css));
        return $response;
    }
    /**
    * Ajax callback to validate the email field.
    */
    public function validateEmailAjax(array &$form, FormStateInterface $form_state) {
        $valid = $this->validateEmail($form, $form_state);
        $response = new AjaxResponse();
        if ($valid) {
            $css = ['border' => '1px solid green'];
            $message = $this->t('Email ok.');
        }
        else {
            $css = ['border' => '1px solid red'];
            $message = $this->t('Email not valid.');
        }
        $response->addCommand(new CssCommand('#edit-email', $css));
        $response->addCommand(new HtmlCommand('.email-valid-message', $message));
        return $response;
    }
    

    /**
     * Form submission handler.
     * 
     * @param array $form
     * An associative array contaning the structure ofthe form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     * The current state of the form.
     */



    public function submitForm(array &$form, FormStateInterface $form_state){
        drupal_set_message($this->t('Thank you for getting in touch!<br><br><br>'));

        // $file = File::create([
        //     'uid' => 1,
        //     'filename' => 'logo.png',
        //     'uri' => 'public://page/logo.png',
        //     'status' => 1,
        //    ]);
        // $file->save();

        /* Fetch the array of the file stored temporarily in database */ 
            $image = $form_state->getValue('image');

        /* Load the object of the file by it's fid */ 
           $file = File::load( $image[0] );
        
        /* Set the status flag permanent of the file object */
           $file->setPermanent();
        
        /* Save the file in database */
           $file->save();

        $node = Node::create([
            'type'        => 'article',
            'title'       => 'Ajax Test',
            'body'        => [
                             'format' => 'full_html',
                             'value' =>$this->t('Account informations!<br><br>
                                         Your First Name is: @first_name <br>
                                         Your Last Name is: @last_name <br>
                                         Image: @image <br>
                                         Color: @color <br>
                                         Your Gender is: @gender <br>
                                         Your birthdate is: @birth_date <br>
                                         Your email is: @email',
                                            array('@first_name' => $form_state->getValue('first_name'),
                                                  '@last_name' => $form_state->getValue('last_name'),
                                                  '@image' => $form_state->getValue('image'),
                                                  '@color' => $form_state->getValue('color'),
                                                  '@gender' => $form_state->getValue('gender'),
                                                  '@birth_date' => $form_state->getValue('birth_date'),
                                                  '@email' => $form_state->getValue('email')))
            ],
            
            'field_image' => [
                            'target_id' => $file->id(),
                              'alt' => "My 'alt'",
                              'title' => "My 'title'",
                             ],
            ]);
           
            $node->save();
               \Drupal::service('path.alias_storage')->save('/node/' . $node->id(), '/my-path', 'en');
       
    
    }
}