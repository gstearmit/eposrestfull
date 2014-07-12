<?php
namespace Igirlxinhcom\Form;

use Zend\Form\Form;

class IgirlxinhcomForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('igirlxinhcom');

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'nameapp',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'nameapp',
            ),
        ));

        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Title',
            ),
        ));
        //link
        $this->add(array(
        		'name' => 'link',
        		'attributes' => array(
        				'type'  => 'text',
        		),
        		'options' => array(
        				'label' => 'link',
        		),
        ));
        
        $this->add(array(
        		'name' => 'image_thumbnail',
        		'attributes' => array(
        				'type'  => 'file',
        				'required' => 'required',
        		),
        		'options' => array(
        				'label' => 'image thumbnail',
        		),
        ));
        
        //content_detail
        $this->add(array(
        		'name' => 'content_detail',
        		'attributes' => array(
        				'type'  => 'textarea',
        				//'required' => 'required',
        		),
        		'options' => array(
        				'label' => 'Content detail',
        		),
        ));
        
        //content_detail_full
        $this->add(array(
        		'name' => 'content_detail_full',
        		'attributes' => array(
        				'type'  => 'textarea',
        				//'required' => 'required',
        		),
        		'options' => array(
        				'label' => 'Content detail full',
        		),
        ));
        
        

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));

    }
}
